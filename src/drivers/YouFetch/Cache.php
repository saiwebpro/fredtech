<?php

namespace MirazMac\YouFetch;

/**
* A Dead Simple File Cache Class
*
* Its purpose is to store the decoded signature.
*
* @author MirazMac <mirazmac@gmail.com>
* @version 0.1 Early Access
* @package MirazMac\YouFetch
*/
class Cache
{
    /**
     * Extension of the cache file
     */
    const FILE_EXTENSION = '.cache';

    /**
     * Default lifetime of the cached files
     */
    const DEFAULT_LIFETIME = 86400;

    /**
     * Path to the cache directory
     *
     * @var string
     */
    protected static $cacheDir = '/tmp/';

    /**
     * Set the cache storage path
     *
     * @param string $cacheDir Path to the directory, will be created if not exists already
     */
    public static function setStoragePath($cacheDir)
    {
        self::$cacheDir = rtrim($cacheDir, '/\\') . '/';

        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir);
        }
    }

    /**
     * Save a entry to the cache pool
     *
     * @param  string $id   Unique cache identifier
     * @param  mixed $data  Serialize-able cache content
     * @return boolean
     */
    public static function save($id, $data)
    {
        $cachePath = self::getFileName($id);
        return file_put_contents($cachePath, serialize($data), LOCK_EX);
    }

    /**
     * Retrieve value from cache pool
     *
     * @param  string $id   Unique cache identifier
     * @param  integer $olderThan Time in seconds before the cache becomes invalid
     * @return mixed
     */
    public static function get($id, $olderThan = null)
    {
        $cachePath = self::getFileName($id);

        if (!is_file($cachePath)) {
            return false;
        }

        if ($olderThan === null) {
            $olderThan = static::DEFAULT_LIFETIME;
        }

        if ($olderThan === false) {
            return unserialize(file_get_contents($cachePath));
        }

        if (time() - filemtime($cachePath) >= $olderThan) {
            @unlink($cachePath);
            return false;
        }

        return unserialize(file_get_contents($cachePath));
    }

    public static function delete($id)
    {
        return @unlink(self::getFileName($id));
    }

    /**
     * Generates the cache file path
     *
     * @access protected
     * @param  string $id   Unique cache identifier
     * @return string
     */
    public static function getFileName($id)
    {
        return self::$cacheDir . trim($id) . self::FILE_EXTENSION;
    }
}
