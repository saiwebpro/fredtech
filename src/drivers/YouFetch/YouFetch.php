<?php

namespace MirazMac\YouFetch;

use \ArrayAccess;
use MirazMac\YouFetch\Exceptions\YouTubeException;
use \Requests_Session;
use \Requests_Cookie_Jar;

/**
* YouTube Video Downloader Library
*
* YouFetch parses YouTube video ID/URL and provides download links for different types of media.
* Including video only, audio only and both streams.
*
* @author MirazMac <mirazmac@gmail.com>
* @version 0.1 Early Access
* @package MirazMac\YouFetch
*/
class YouFetch
{
    /**
     * RegEx pattern to match player assets URI
     *
     * @var string
     */
    const PLAYER_PATTERN = '/"assets":.+?"js":\s*("[^"]+")/';

    /**
     * RegEx pattern to match adaptive formats from webpage
     *
     * @var string
     */
    const ADAPTIVE_FMTS_PATTERN = '/\"adaptive\_fmts\"\:\s*\"([^\"]+)/i';

    /**
     * RegEx pattern to match URL Encoded streams from webpage
     *
     * @var string
     */
    const URL_ENCODED_FMTS_PATTERN = '/\"url\_encoded\_fmt\_stream\_map\"\:\s*\"([^\"]+)/i';

    /**
     * RegEx pattern to match stream format from the codec
     *
     * @var string
     */
    const TYPE_PATTERN = '/^([a-z0-9\-\_\/]+)(\;\s*codecs\="(?P<codecs>[^"]+)")?/i';

    /**
     * Endpoint to the webpage
     *
     * @var string
     */
    const WEB_PAGE_URI = 'https://www.youtube.com/watch?v=%s&gl=US&hl=en&persist_gl=1&persist_hl=1&app=desktop&persist_app=1&has_verified=1&bpctr=9999999999';

    /**
     * Endpoint to the embed page
     *
     * @var string
     */
    const EMBED_URI = 'https://youtube.com/embed/%s';

    /**
     * Current Video ID
     *
     * @var string
     */
    protected $videoID;
    /**
     * Player URI of the current video
     *
     * @var string
     */
    protected $playerUri;
    /**
     * Player ID of the current video
     *
     * @var string
     */
    protected $playerID;

    /**
     * Player data
     *
     * @var array
     */
    protected $playerData;

    /**
     * Final parsed streams
     *
     * @var array
     */
    protected $parsedItems = [];

    /**
     * Internal Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Parsed Video Information
     *
     * @var array
     */
    protected $videoInfo = [];

    /**
     * Constructor
     *
     * @param string $videoID YouTube Video ID or URL
     * @param array  $options Options
     */
    public function __construct($videoID, array $options = [])
    {
        // If this is an URL extract the video ID from it
        if (filter_var($videoID, FILTER_VALIDATE_URL)) {
            $videoID = $this->extractVideoID($videoID);
        }

        $this->videoID = trim($videoID);

        $defaultOptions = [
            // Should we also parse the video webpage for the streams? Best to set this as FALSE
            'parseWebPageForStreams' => false,
            // If the media is playing on the browser instead of downloading, you can enable Redirector
            'enableRedirector' => false,
            // Text that will be appended after filename
            'appendAfterTitle' => '',
            // Text that will be perpended before filename
            'prependBeforeTitle' => ''
        ];
        $this->options = array_merge($defaultOptions, $options);

        // Start parsing the video
        $this->bootParser();
    }

    /**
     * Fetch all streams
     *
     * @return array
     */
    public function fetchAll()
    {
        return $this->parsedItems;
    }

    /**
     * Fetch Audio Only streams
     *
     * @return array
     */
    public function fetchAudioOnly()
    {
        $streams = $this->fetchAll();
        foreach ($streams as $itag => $stream) {
            if (!$stream->isAudioOnly()) {
                unset($streams[$itag]);
            }
        }
        return $streams;
    }

    /**
     * Fetch Video Only streams
     *
     * @return array
     */
    public function fetchVideoOnly()
    {
        $streams = $this->fetchAll();
        foreach ($streams as $itag => $stream) {
            if (!$stream->isVideoOnly()) {
                unset($streams[$itag]);
            }
        }
        return $streams;
    }

    /**
     * Fetch streams that contain both audio and video
     *
     * @return array
     */
    public function fetchFullVideos()
    {
        $streams = $this->fetchAll();
        foreach ($streams as $itag => $stream) {
            if (!$stream->hasBoth()) {
                unset($streams[$itag]);
            }
        }
        return $streams;
    }

    /**
     * Fetch information about the video
     *
     * @return object
     */
    public function fetchVideoInfo()
    {
        return $this->videoInfo;
    }

    /**
     * Boot the internal media parser
     *
     * @access protected
     * @return true
     */
    protected function bootParser()
    {
        // Fetch the player data first
        $this->fetchPlayerData();

        // Holds media links as array in future
        $mediaLinks = [];
        $rawMediaLinks = null;
        $videoDetails = $this->fetchMediaFromEndPoint();


        if (isset($videoDetails['adaptive_fmts'])) {
            $rawMediaLinks .= $videoDetails['adaptive_fmts'];
        }

        if (isset($videoDetails['url_encoded_fmt_stream_map'])) {
            $rawMediaLinks .= ",{$videoDetails['url_encoded_fmt_stream_map']}";
        }

        $playerData = json_decode($videoDetails['player_response'], true);


        if (!empty($playerData['streamingData']['formats'])) {
            foreach ($playerData['streamingData']['formats'] as $newFormat) {
                $data = $this->formatNewLinks($newFormat);

                if ($data) {
                    $rawMediaLinks .= ",{$data}";
                }
            }
        }

        if (!empty($playerData['streamingData']['adaptiveFormats'])) {
            foreach ($playerData['streamingData']['adaptiveFormats'] as $newFormat) {
                $data = $this->formatNewLinks($newFormat);

                if ($data) {
                    $rawMediaLinks .= ",{$data}";
                }
            }
        }

        // If we don't have any streams OR Force web page parsing is enabled
        // We'd try to grab the stream links from webpage
        if (empty($rawMediaLinks) || $this->options['parseWebPageForStreams']) {
            $rawMediaLinks .= "," . $this->fetchStreamsFromWebPage();
        }

        if (!is_string($rawMediaLinks) || mb_strpos($rawMediaLinks, ',') === false) {
            throw new YouTubeException("No media streams found for the video.");
        }

        // URL friendly file name
        $downloadAs = $this->sanitizeFileName(
            "{$this->options['prependBeforeTitle']} {$this->videoInfo->getTitle()} {$this->options['appendAfterTitle']}"
        );

        // Load itags
        $iTags = Itags::load();
        // Load default media format
        $defaultMedia = Itags::getDefaultMedia();
        // Final output init
        $finalOutput = [];

        // Arraify(!) the data
        foreach (explode(',', $rawMediaLinks) as $link) {
            parse_str($link, $link);

            // Must have an itag
            if (!isset($link['itag'])) {
                continue;
            }

            // uses itag as the key to prevent proccessing the same stream again
            // because some videos have the stream both in the get_video_info and player_response object
            $mediaLinks[$link['itag']] = $link;
        }

        // Lets loop watson!
        foreach ($mediaLinks as $mlink) {
            // You have an itag thats actually represnts a live video? OR
            // You don't have the stream URL!
            // hehehe, fuck off then -_-
            if ($mlink['itag'] === '_rtmp' || !isset($mlink['url'])) {
                continue;
            }

            // Adjust URL Queries
            parse_str(parse_url($mlink['url'], PHP_URL_QUERY), $mediaUrlQuery);
            // Update with URL Friendly file name we created earlier
            $mediaUrlQuery['title'] = $downloadAs;
            // Phew, nobody wants to die young
            $mediaUrlQuery['keepalive'] = 'yes';
            // Useless, probably, still trying is worth it :|
            $mediaUrlQuery['ratebypass'] = 'yes';

            // Append signature if exists
            if (isset($mlink['sig'])) {
                $mediaUrlQuery['signature'] = $mlink['sig'];
            }

            // Dynamic signature parameter, sometimes it's signature and other times it's just 'sig'
            $signatureParameter = isset($mlink['sp']) ? $mlink['sp'] : 'signature';

            // Decipher the ciphered signature if present
            if (isset($mlink['s'])) {
                $signature = new Signature($mlink['s'], $this->playerUri);
                $mediaUrlQuery[$signatureParameter] = $signature->decrypt();
            }

            // Update media link with adjusted parameters
            if (is_array($ex = explode('?', $mlink['url']))) {
                $mlink['url'] = "{$ex[0]}?" . http_build_query($mediaUrlQuery);
            }

            // Without any signature parameter, the link is pretty much useless
            if (mb_strpos($mlink['url'], "&{$signatureParameter}=") === false) {
                //continue;
            }

            // If redirector is enabled replace the stream URL with redirector
            if ($this->options['enableRedirector']) {
                $mlink['url'] = preg_replace(
                    '/^(.*)\.googlevideo\.com/',
                    'https://redirector.googlevideo.com',
                    $mlink['url']
                );
            }

            $mediaLink = $defaultMedia;
            // Update itag value
            $mediaLink['itag'] = intval($mlink['itag']);

            // Update media link
            $mediaLink['link'] = $mlink['url'];

             // Detect media link size
            if (isset($mlink['clen'])) {
                $mediaLink['size'] = $this->getNumber($mlink['clen']);
            }

            // Get details of media link with itag
            // Video Info init
            $iTagV = [];

            // Check for ITag Data
            if (isset($iTags[$mlink['itag']])) {
                $iTagInf = $iTags[$mlink['itag']];
                // Update media extension
                $mediaLink['extension'] = $iTagInf['extension'];
                // Update iTag video details
                if (isset($iTagInf['video'])) {
                    $iTagV = $iTagInf['video'];
                }
                // Check for is DASH media
                if (isset($iTagInf['dash']) && in_array($iTagInf['dash'], ['video', 'audio'])) {
                    // Detect media type
                    $mediaLink['type'] = $iTagInf['dash'];
                    // DASH media
                    $mediaLink['dash'] = true;

                    // Process Dash media
                    switch ($iTagInf['dash']) {
                        case 'video':
                            // Audio stream is not availabe
                            $mediaLink['audio'] = false;
                            break;
                        case 'audio':
                            // Video stream is not availabe
                            $mediaLink['video'] = false;
                            // Audio bitrate & quality
                            $bitrate = null;
                            if (isset($mlink['bitrate'])) {
                                $bitrate = $this->getNumber($mlink['bitrate']);
                            } elseif (isset($iTagInf['audio']) && isset($iTagInf['audio']['bitrate'])) {
                                $bitrate = $iTagInf['audio']['bitrate'];
                            }

                            // Update bitrate
                            if ($bitrate) {
                                $mediaLink['audio']['bitrate'] = $this->getNumber($bitrate);
                            }
                            // Audio frequency
                            if (isset($iTagInf['audio']) && isset($iTagInf['audio']['frequency'])) {
                                // Frequency
                                $mediaLink['audio']['frequency'] = $this->getNumber($iTagInf['audio']['frequency']);
                            }
                            break;
                    }
                    // Wooh! Done with processing dash medias
                } else {
                    // So, not a dash media right?
                    $mediaLink['type'] = 'video';
                }
            } else {
            }
            /** Done with checking itags **/

            // Now lets Update media video stream details
            if ($mediaLink['video'] !== false) {
                // Check for is 3D video
                if (isset($iTagV['3d']) && $iTagV['3d']) {
                    $mediaLink['video']['3d'] = true;
                }
                // Width x Height
                if (isset($mlink['size'])) {
                    list($width, $height) = explode('x', $mlink['size']);
                    $mediaLink['video']['width'] = intval($width);
                    $mediaLink['video']['height'] = intval($height);
                } elseif (isset($iTagV['height'])) {
                    // Get dimensions from iTag info
                    $mediaLink['video']['height'] = $iTagV['height'];
                    // Get width of video
                    if (isset($iTagV['width'])) {
                        $mediaLink['video']['width'] = $iTagV['width'];
                    } else {
                        $mediaLink['video']['width'] = ceil(($iTagV['height'] / 9) * 16);
                    }

                    // Video bitrate
                    $vBitrate = null;
                    if (isset($mlink['bitrate'])) {
                        $vBitrate = $this->getNumber($mlink['bitrate']);
                    } elseif (isset($iTagV['bitrate'])) {
                        $vBitrate = $iTagV['bitrate'];
                    }

                    if ($vBitrate) {
                        $mediaLink['video']['bitrate'] = $this->getNumber($vBitrate);
                    }

                    // Video FrameRate
                    $framerate = null;
                    if (isset($mlink['fps'])) {
                        $framerate = $this->getNumber($mlink['fps']);
                    } elseif (isset($iTagV['framerate'])) {
                        $framerate = $iTagV['framerate'];
                    }

                    $mediaLink['video']['framerate'] = $framerate;
                }
            }
            /** Done with processing video stream details **/

            // Time to deal with media extension and codecs
            if (isset($mlink['type']) && is_string($mlink['type']) &&
                preg_match(static::TYPE_PATTERN, $mlink['type'], $matches)) {
                // Check media type
                if ($mediaLink['type'] === null) {
                    $mediaLink['type'] = mb_stripos($matches[1], 'audio') !== false ? 'audio' : 'video';
                }
                // Check media file extension
                if ($mediaLink['extension'] === null) {
                    if (mb_stripos($matches[1], 'mp4')) {
                        $mediaLink['extension'] = $mediaLink['type'] == 'video' ? 'mp4' : 'm4a';
                    } elseif (mb_stripos($matches[1], 'webm')) {
                        $mediaLink['extension'] = 'webm';
                    } elseif (mb_stripos($matches[1], 'flv')) {
                        $mediaLink['extension'] = 'flv';
                    } elseif (mb_stripos($matches[1], '3gp')) {
                        $mediaLink['extension'] = '3gp';
                    }
                }

                // Update codec details
                if (isset($matches['codecs'])) {
                    $codecs = explode(',', $matches['codecs']);
                    // Check for is DASH media
                    if (!$mediaLink['dash'] && count($codecs) == 1) {
                        $mediaLink['dash'] = true;
                    }
                    // Media stream codecs
                    if ($mediaLink['type'] == 'video') {
                        // Update video codec
                        if (is_array($mediaLink['video']) && isset($codecs[0])) {
                            $vCodec = explode('.', trim($codecs[0]));
                            $mediaLink['video']['codec'] = $vCodec[0];
                        }
                        // Update audio codec
                        if (is_array($mediaLink['audio']) && isset($codecs[1])) {
                            $aCodec = explode('.', trim($codecs[1]));
                            $mediaLink['audio']['codec'] = $aCodec[0];
                        }
                    } else {
                        // Update audio codec
                        if (is_array($mediaLink['audio']) && isset($codecs[0])) {
                            $vCodec = explode('.', trim($codecs[0]));
                            $mediaLink['audio']['codec'] = $vCodec[0];
                        }
                    }
                }
            }

            /** Done dealing with media codecs **/

            // Fuck! Finally!
            $finalOutput[$mediaLink['itag']] = $mediaLink;
        }


        // Oops! Not yet? You kiddin' me? -_-
        foreach ($finalOutput as $itag => $data) {
            // Remove media block if extension not available
            if ($data['extension'] === null) {
                unset($finalOutput[$itag]);
                continue;
            }

            // Add sizes if not added previously
            if (!$data['size']) {
                $mediaSize = 0;
                $mediaSizeObj = Http::getSession()->head($data['link']);
                if (isset($mediaSizeObj->headers['content-length'])) {
                    $mediaSize = $mediaSizeObj->headers['content-length'];
                }
                $finalOutput[$itag]['size'] = $mediaSize;
            }
        }

        foreach ($finalOutput as $itag => $data) {
            $finalOutput[$itag] = new StreamElement($data);
        }


        $this->parsedItems = $finalOutput;

        // We dig for this?
        return true;
    }

    protected function formatNewLinks(array $stream)
    {
        if (!isset($stream['itag'])) {
            return false;
        }

        // Simulate a YouTube stream response
        $newData = [
            'itag' => $stream['itag'],
            'url' => isset($stream['url']) ? $stream['url'] : null,
        ];

        if (isset($stream['cipher'])) {
            parse_str($stream['cipher'], $data);

            if (isset($data['url'])) {
                $url = $data['url'];

                unset($data['url']);

                foreach ($data as $key => $value) {
                    $newData[$key] = $value;
                }

                $newData['url'] = $this->updateParameter($url, $data);
            }
        }

        // List of changed parameters as FORMER => NEWER
        $transform = [
            'clen'          => 'contentLength',
            'quality_label' => 'qualityLabel',
            'bitrate'       => 'bitrate',
            'type'          => 'mimeType',
        ];

        foreach ($transform as $former => $new) {
            if (isset($stream[$new])) {
                $newData[$former] = $stream[$new];
            }
        }


        $query = http_build_query($newData);

        return $query;
    }

    /**
     * Updates or adds query parameters to a URL
     *
     * @param  string $url
     * @param  array  $mod
     * @return string
     */
    protected function updateParameter($url, array $mod)
    {
        $url_array = parse_url($url);

        if (!empty($url_array['query'])) {
            parse_str($url_array['query'], $query_array);
            foreach ($mod as $key => $value) {
                    $query_array[$key] = $value;
            }
        } else {
            $query_array = $mod;
        }

        $return = "{$url_array['scheme']}://";
        // make sure the trailing slash is present only once
        $return .= rtrim($url_array['host'], '/') . '/';

        $return .= ltrim($url_array['path'], '/') . '?' . http_build_query($query_array);

        return $return;
    }

    /**
     * Fetch media from endpoint
     *
     * @return array
     */
    protected function fetchMediaFromEndPoint()
    {
        // We assume it's an Unknown error, So negative minded!
        $videoDetails = 'Unknown';

        // Different variations for the youtube getinfo page
        $variations = ['embedded', 'detailpage', 'vevo', ''];

        // Loop through each of 'em untill we find our soulmate <3
        foreach ($variations as $elKey) {
            $query = [
                'hl'        => 'en',
                'gl'        => 'US',
                'ps'        => 'default',
                'eurl'      => "https://youtube.googleapis.com/v/{$this->videoID}",
                'asv'       => '3',
                'video_id'  => $this->videoID
            ];

            if (!empty($elKey)) {
                $query['el'] = $elKey;
            }

            $query = http_build_query($query);
            $videoData = Http::getSession()->get("https://www.youtube.com/get_video_info?&{$query}")->body;

            if (!is_string($videoData)) {
                continue;
            }
            parse_str($videoData, $videoDetails);
            // Found match
            if (isset($videoDetails['player_response'])) {
                break;
            }
        }

        // I think its not working anymore, we need to break up :|
        if (isset($videoDetails['player_response'])) {
            $playerResponse = json_decode($videoDetails['player_response'], true);
            if (!isset($playerResponse['videoDetails']['title'])) {
                throw new YouTubeException("Failed to fetch YouTube video details. Reason: Invalid player response!");
            }
        } else {
            throw new YouTubeException("Failed to fetch YouTube video details. Reason: No player response object found!");
        }

        $defaultDetails = Itags::getDefaultInfo();

        foreach ($defaultDetails as $key => $value) {
            if (isset($playerResponse['videoDetails'][$key])) {
                $defaultDetails[$key] = $playerResponse['videoDetails'][$key];
            }
        }

        $this->videoInfo = new VideoElement($defaultDetails);

        return $videoDetails;
    }

    /**
     * Fetch player data from embed page
     *
     * @access protected
     * @return true
     */
    protected function fetchPlayerData()
    {
        $endPointUri = sprintf(static::EMBED_URI, $this->videoID);
        $response = Http::getSession()->get($endPointUri);

        // Here we go with the source code
        $source = $response->body;

        // Experimental
        // Not yet meant to do anything
        $config = $this->getBetween($source, 't.setConfig({\'PLAYER_CONFIG\': ', '});writeEmbed();');
        $config = json_decode($config, true);

        if ($config) {
            $this->playerData = $config;
        }

        // Extract the player information
        preg_match(static::PLAYER_PATTERN, $source, $matches);
        if (empty($matches[1]) || !is_string($playerPath = json_decode($matches[1]))) {
            throw new YouTubeException("Failed to find player information for the video.");
        }


        preg_match('%player(_|-)(.*?)/%', $playerPath, $matches);

        if (empty($matches[2]) || !is_string($matches[2])) {
            throw new YouTubeException("Failed to find player ID from the embed page!");
        }

        // Player ID
        $playerID  = $matches[2];

        // Player URI
        $playerUri = null;
        $protocol  = mb_substr($playerPath, 0, 2);

        if ($protocol === '//') {
            // If the path is double slashed then we just need to append the protocol name
            $playerUri .= "https:{$playerPath}";
        } else {
            // Otherwise it must be a native path
            $playerUri .= "https://youtube.com{$playerPath}";
        }

        $this->playerUri = $playerUri;
        $this->playerID  = $playerID;
        return true;
    }

    /**
     * Fetches streams from web page
     *
     * @access protected
     * @return true
     */
    protected function fetchStreamsFromWebPage()
    {
        // Get data from the video web page
        $webPageUri = sprintf(static::WEB_PAGE_URI, $this->videoID);
        $webPageRequest = Http::getSession()->get($webPageUri);

        // Sorry, no luck
        if (!$webPageRequest->success) {
            return false;
        }

        // Webpage content
        $webPage = $webPageRequest->body;
        // Raw media links, currently empty
        $rawMediaLinks = null;

        // Find adaptive video formats
        if (preg_match(static::ADAPTIVE_FMTS_PATTERN, $webPage, $matches) &&
            is_string($jsonOut = json_decode("\"{$matches[1]}\""))) {
            $rawMediaLinks .= $jsonOut;
        }

        // Find URL encoded formats
        if (preg_match(static::URL_ENCODED_FMTS_PATTERN, $webPage, $matches) &&
            mb_strpos($matches[1], 's=') === false &&
            is_string($jsonOut = json_decode("\"{$matches[1]}\""))) {
            $rawMediaLinks .= ',' . $jsonOut;
        }

        // Oops! No streams found, I don't feel so good!
        if (!is_string($rawMediaLinks) || mb_strpos($rawMediaLinks, ',') === false) {
            // No need to throw an exception here
            return null;
        }

        return $rawMediaLinks;
    }

    /**
     * Format numbers from a string
     *
     * @param  string $data The string
     * @return integer
     */
    protected function getNumber($data)
    {
        return floatval(number_format(floatval($data), 0, '.', ''));
    }

    /**
     * Get content between two identifiers
     *
     * @param  string $content
     * @param  string $start
     * @param  string $end
     * @return mixed
     */
    protected function getBetween($content, $start, $end)
    {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);
            return $r[0];
        }

        return '';
    }

    /**
     * Sanitizes string to file system safe file name
     *
     * @param  string $fileName The file name
     * @return string
     */
    protected function sanitizeFileName($fileName)
    {
        $specialChars = "\x00\x21\x22\x24\x25\x2a\x2f\x3a\x3c\x3e\x3f\x5c\x7c";
        $fileName = str_replace(str_split($specialChars), '_', $fileName);
        return $fileName;
    }

    /**
     * Extract video ID from YouTube video URL
     *
     * @access protected
     * @param  string $url The URL
     * @return string|boolean Extracted ID or FALSE if not found
     */
    protected function extractVideoID($url)
    {
        // RegEx source: https://gist.github.com/ghalusa/6c7f3a00fd2383e5ef33
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        if (isset($match[1])) {
            return $match[1];
        }

        return false;
    }
}
