<?php

namespace MirazMac\YouFetch;

use MirazMac\YouFetch\Exceptions\ItagsException;
use \ArrayAccess;

/**
* Itags Loader
*
* Loads iTags information from the file itags.json and parses it further for
* better information about the video streams
*
* @author MirazMac <mirazmac@gmail.com>
* @version 0.1 Early Access
* @package MirazMac\YouFetch
*/
class Itags
{
    /**
     * Stores the iTag information
     *
     * @var array
     */
    protected static $itagStorage = [];

    /**
     * Prevent constructing this class
     */
    private function __construct()
    {
    }

    /**
     * Prevent cloning this class
     */
    private function __clone()
    {
    }

    /**
     * Prevent waking up this class
     */
    private function __wakeup()
    {
    }

    /**
     * Get a static instance of the Itag Class
     *
     * @return object
     */
    public static function load()
    {
        if (!self::$itagStorage) {
            $iTagsPath = __DIR__ . '/' . 'itags.json';
            if (!is_file($iTagsPath)) {
                throw new ItagsException("Failed to read iTags from {$iTagsPath}");
            }
            $itags = json_decode(file_get_contents($iTagsPath), true);
            if (!$itags) {
                throw new ItagsException("The iTags file is possibly corrputed!");
            }
            self::$itagStorage = $itags;
        }

        return self::$itagStorage;
    }

    /**
     * Returns default media format, @see MirazMac\YouFetch\StreamElement
     *
     * @return array
     */
    public static function getDefaultMedia()
    {
        return
        [
            'extension' => null,
            'type' => null,
            'size' => null,
            'itag' => null,
            'link' => null,
            'dash' => false,
            'video' => [
                '3d' => false,
                'codec' => null,
                'width' => null,
                'height' => null,
                'bitrate' => null,
                'framerate' => null
            ],
            'audio' => [
                'codec' => null,
                'bitrate' => null,
                'frequency' => null
            ]
        ];
    }

    /**
     * Returns default video information format, @see MirazMac\YouFetch\VideoElement
     *
     * @return array
     */
    public static function getDefaultInfo()
    {
        return [
            'title' => null,
            'author' => null,
            'channelId' => null,
            'videoId' => null,
            'shortDescription' => null,
            'thumbnail_url' => null,
            'lengthSeconds' => 0,
            'viewCount' => 0,
            'keywords' => null,
            'averageRating' => 0,
        ];
    }
}
