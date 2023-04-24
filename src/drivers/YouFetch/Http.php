<?php

namespace MirazMac\YouFetch;

use \Requests_Session;
use \Requests_Cookie_Jar;

/**
* Requests Http Wrapper
*
* @author MirazMac <mirazmac@gmail.com>
* @version 0.1 Early Access
* @package MirazMac\YouFetch
*/
class Http
{
    /**
     * Holds the Requests session to use accross the library
     *
     * @var object
     */
    protected static $session;

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
     * Returns a static session of the Requests class
     *
     * @return object
     */
    public static function getSession()
    {
        if (!static::$session) {
            // Eww, I hate PSR-0
            static::$session = new Requests_Session;
            // Maybe YouTube would bless us, if we use chrome? idk xD
            static::$session->useragent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36';
            static::$session->headers['Accept'] =
            'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
            static::$session->headers['Accept-Language'] = 'en-US,en;q=0.5';
            static::$session->headers['Accept-Charset'] = '*';
            static::$session->headers['DNT'] = '1';
            // Obvious -_-
            static::$session->headers['Referer'] = 'https://youtube.com';
            // Not works, still will try
            if (isset($_SERVER['REMOTE_ADDR'])) {
                static::$session->headers['X-Forwarded-For'] = $_SERVER['REMOTE_ADDR'];
            }
            static::$session->options['timeout'] = 100;
            static::$session->options['connect_timeout'] = 100;
            // This will make us look like more less of an asshole to YouTube
            static::$session->options['cookies'] = new Requests_Cookie_Jar;
        }

        return self::$session;
    }
}
