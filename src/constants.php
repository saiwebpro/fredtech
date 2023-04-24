<?php

/**
 * Toggle developer mode
 *
 * @var boolean
 */

define('DEV_MODE', false);

/**
 * Current App version
 *
 * @var integer|float
 */
define('APP_VERSION', "1.0.5");

/**
 * Core constant to check if we're inside the app
 *
 * @var booleam
 */
define('SPARKIN', true);

/**
 * Toggle demo mode
 *
 * @var boolean
 */
define('DEMO_MODE', false);

/**
 * Path to the app directory
 *
 * @var string
 */
define('SRCPATH', str_replace('\\', '/', __DIR__) . '/');

/**
 * Path to the base directory
 *
 * @var string
 */
define('BASEPATH', dirname(SRCPATH) . '/');

/**
 * Name of the site directory
 *
 * @var string
 */
define('SITE_DIR', 'site');

/**
 * Name of the theme directory
 *
 * @var string
 */
define('THEME_DIR', SITE_DIR . '/themes');

/**
 * Name of the frontend locale textdomain
 *
 * @var string
 */
define('_T', 'theme');

/**
 * Name of the plugin directory
 *
 * @var string
 */
define('PLUGIN_DIR', SITE_DIR . '/plugins');

/**
 * Name of the uploads directory
 *
 * @var string
 */
define('UPLOADS_DIR', SITE_DIR . '/uploads');


/**
 * Base Controller namespace
 *
 * @var string
 */
define('CONTROLLER_NAMESPACE', '\\spark\\controllers\\');


/**
 * Current App Name
 *
 * @var string
 */
define('APP_NAME', 'InBefore');

/**
 * Determines whether the JSON API is enabled or not
 */
define('JSON_API_ENABLED', true);


// Dynamicly determines if current request asks for JSON or not
if (isset($_GET['json']) && (int) $_GET['json'] === 1) {
    define('JSON_REQUEST', true);
} else {
    define('JSON_REQUEST', false);
}


// Path to cache directory (must be writeable)
define('THUMB_CACHE', SRCPATH . 'var/cache/thumbnails/');
define('THUMB_CACHE_AGE', 86400);         // Duration of cached files in seconds
define('THUMB_BROWSER_CACHE', true);          // Browser cache true or false
define('SHARPEN_MIN', 12);            // Minimum sharpen value
define('SHARPEN_MAX', 28);            // Maximum sharpen value
define('ADJUST_ORIENTATION', true);          // Auto adjust orientation for JPEG true or false
define('JPEG_QUALITY', 100);           // Quality of generated JPEGs (0 - 100; 100 being best)


define('SIMPLEPIE_CACHE_PATH', SRCPATH . '/var/cache/simplepie');
