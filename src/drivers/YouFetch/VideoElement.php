<?php

namespace MirazMac\YouFetch;

use \ArrayAccess;

/**
* VideoElement
*
* Holds information about the video
*
* @author MirazMac <mirazmac@gmail.com>
* @version 0.1 Early Access
* @package MirazMac\YouFetch
*/
class VideoElement implements ArrayAccess
{
    /**
     * Video information
     *
     * @var array
     */
    protected $videoInfo = [];

    /**
     * Constructor
     *
     * @param array $videoInfo Parsed video information from YouFetch
     */
    public function __construct(array $videoInfo)
    {
        $this->videoInfo = $videoInfo;
    }

    /**
     * Returns the video title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this['title'];
    }

    /**
     * Returns the video author name aka channel name
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this['author'];
    }

    /**
     * Alias of self::getAuthor();
     *
     * @return string
     */
    public function getChannelName()
    {
        return $this->getAuthor();
    }

    /**
     * Returns the channel ID where the video was uploaded to
     *
     * @return string
     */
    public function getChannelID()
    {
        return $this['channelId'];
    }

    /**
     * Returns the video ID
     *
     * @return string
     */
    public function getID()
    {
        return $this['videoId'];
    }

    /**
     * Returns the URL to the video thumbnail
     *
     * @var string  $size Preferred thumbnail size ( default, mqdefault, hqdefault, maxresdefault )
     *
     * @return string
     */
    public function getThumbnail($size = 'mqdefault')
    {
        $size = filter_var($size, FILTER_SANITIZE_STRING);
        $id = $this->getID();
        return "https://i.ytimg.com/vi/{$id}/{$size}.jpg";
    }

    /**
     * Get video length in seconds
     *
     * @return string
     */
    public function getLength()
    {
        return $this['lengthSeconds'];
    }

    /**
     * Alias of self::getLength()
     *
     * @return string
     */
    public function getDuration()
    {
        return $this->getLength();
    }

    /**
     * Get the number of views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this['viewCount'];
    }

    /**
     * Get average rating of the video
     *
     * @return string
     */
    public function getRating()
    {
        return $this['averageRating'];
    }

    /**
     * Get video keywords ( if has any )
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this['keywords'];
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->videoInfo);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->videoInfo[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->videoInfo[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->videoInfo[$offset]);
        }
    }
}
