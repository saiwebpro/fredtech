<?php

namespace MirazMac\YouFetch;

use \ArrayAccess;

/**
* StreamElement
*
* Holds information about a YouTube Video Stream
*
* @author MirazMac <mirazmac@gmail.com>
* @version 0.1 Early Access
* @package MirazMac\YouFetch
*/
class StreamElement implements ArrayAccess
{
    /**
     * Parsed Stream
     *
     * @var array
     */
    protected $stream = [];

    /**
     * Constructor
     *
     * @param array $stream Parsed stream
     */
    public function __construct(array $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Returns if the stream is audio only or not
     *
     * @return boolean boolean
     */
    public function isAudioOnly()
    {
        return empty($this['video']['codec']) ? true : false;;
    }

    /**
     * Returns if the stream is video only or not
     *
     * @return boolean
     */
    public function isVideoOnly()
    {
        return empty($this['audio']['codec']) ? true : false;
    }

    /**
     * Returns if the stream contains both audio and video
     *
     * @return boolean
     */
    public function hasBoth()
    {
        return !empty($this['audio']['codec']) && !empty($this['video']['codec']);
    }

    /**
     * Alias of self::hasBoth()
     *
     * @return boolean
     */
    public function hasAudioAndVideo()
    {
        return $this->hasBoth();
    }

    /**
     * Returns if the stream's video is 3D or not
     *
     * @return boolean
     */
    public function is3D()
    {
        return $this['video']['3d'] === true;
    }

    /**
     * Returns the height of the video ( if present , N/A otherwise )
     *
     * @return string
     */
    public function getHeight()
    {
        if (!$this['video']['height']) {
            return 'N/A';
        }

        return $this['video']['height'];
    }

    /**
     * Returns the width of the video ( if present , N/A otherwise )
     *
     * @return string
     */
    public function getWidth()
    {
        if (!$this['video']['width']) {
            return 'N/A';
        }

        return $this['video']['width'];
    }

    /**
     * Returns the resolution of the video ( if present )
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->getWidth() . ' x ' . $this->getHeight();
    }

    /**
     * Returns the audio bitrate of the video ( if present , N/A otherwise )
     *
     * @return string
     */
    public function getAudioBitrate()
    {
        if (!$this['audio']['bitrate']) {
            return 'N/A';
        }

        return $this->formatBitrate($this['audio']['bitrate']);
    }

    /**
     * Returns the actual formatted file size of the media
     *
     * @return string
     */
    public function getSize()
    {
        return $this->formatBytes($this['size']);
    }

    /**
     * Returns the link to the stream aka download link
     *
     * @return boolean
     */
    public function getLink()
    {
        return $this['link'];
    }

    /**
     * Returns the file extension for the stream
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->stream['extension'];
    }

    /**
     * Formats audio birate
     *
     * @param  string $bytes
     * @access protected
     * @return string
     */
    protected function formatBitrate($bytes)
    {
        $kb = (int)$this->formatBytes($bytes);
        return $kb . 'KBPS';
    }

    /**
     * Formats bytes to human readable file size
     *
     * @param  integer $bytes File size in bytes
     * @access protected
     * @return string
     */
    protected function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes === 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->stream);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->stream[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->stream[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->stream[$offset]);
        }
    }
}
