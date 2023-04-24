<?php

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Slim\PDO\Database;
use Valitron\Validator;
use spark\drivers\Asset\AssetManager;
use spark\drivers\Auth\CurrentUser;
use spark\drivers\I18n\Locale;
use spark\drivers\Nav\BreadCrumbs;
use spark\drivers\Plugin\EventDispatcher;
use spark\drivers\Plugin\PluginManager;
use spark\drivers\Views\ThemeManager;
use spark\helpers\MonologWriter;
use spark\helpers\Registry;
use spark\helpers\Session;
use spark\models\Model;
use spark\models\OptionModel;

\Slim\Route::setDefaultConditions([
    'id' => '\d++'
]);

// Valitron i18n
Validator::langDir(srcpath('drivers/I18n'));
Validator::lang('valitron');

// Set up monologger
$formatter = new LineFormatter;
$formatter->includeStacktraces();
$fileHandler = new StreamHandler(
    srcpath('var/logs/' . date('d-m-Y') . '.log')
);
$fileHandler->setFormatter($formatter);
$app->getLog()->setWriter(new MonologWriter([
    'handlers' => [$fileHandler],
]));

// Add options as a singelton
$app->container->singleton('composer', function () use ($composer) {
    return $composer;
});

// Add options as a singelton
$app->container->singleton('asset', function () use ($app) {
    return new AssetManager;
});

// Add theme manager as a singelton
$app->container->singleton('event', function () use ($app) {
    return new EventDispatcher;
});

$app->container->singleton('registry', function () use ($app) {
    return new Registry;
});

// Add options as a singelton
$app->container->singleton('locale', function () use ($app) {
    return new Locale;
});

// Add options as a singelton
$app->container->singleton('breadcrumbs', function () use ($app) {
    return new BreadCrumbs;
});

// Connect to database
if ($app->config('db')) {
    $app->container->singleton('db', function () use ($app) {
        $dsn = "mysql:host={$app->config('db')['dbhost']};port={$app->config('db')['dbport']};dbname={$app->config('db')['dbname']};charset=utf8mb4";
        try {
            $db = new Database(
                $dsn,
                $app->config('db')['username'],
                $app->config('db')['password']
            );
        } catch (\Exception $e) {
            no_cache_headers();
            // Log the error
            $app->getLog()->critical($e);
            $error = "Please set <strong>DEV_MODE</strong> to <em>TRUE</em> or check the logs at <em>src/var/logs</em> for more detailed error report";

            if (is_dev()) {
                $error = "<p>{$e->getMessage()}";
            }

            fatal_server_error('Failed to connect to the database', $error);
        }

        return $db;
    });

    Model::setPrefix($app->config('db')['prefix']);
}

// Add options as a singelton
$app->container->singleton('options', function () use ($app) {
    return new OptionModel;
});

// Add theme manager as a singelton
$app->container->singleton('theme', function () use ($app) {
    return new ThemeManager;
});

// Add theme manager as a singelton
$app->container->singleton('plugins', function () use ($app) {
    return new PluginManager;
});

// Add theme manager as a singelton
$app->container->singleton('session', function () use ($app) {
    return new Session;
});

// Add theme manager as a singelton
$app->container->singleton('user', function () use ($app) {
    return new CurrentUser;
});

// Add cache pool as a singelton
$app->container->singleton('cache', function () use ($app) {
    if (extension_loaded('memcache')) {
        $driver = new \Stash\Driver\Memcache([]);
    } elseif (class_exists('\APCUIterator')) {
        $driver = new \Stash\Driver\Apc([]);
    } else {
        // File system cache is default for us
        $driver = new \Stash\Driver\FileSystem([
            'path' => srcpath('var/cache/pool')
        ]);
    }
    $pool = new \Stash\Pool($driver);

    return $pool;
});

load_functions(['i18n.php', 'pluggable.php']);

// Start the theme engine
$app->theme->initEngine();

require srcpath('functions/globals.php');

// Load required functions
load_functions(['system.php', 'theme.php', 'formatting.php', 'validation.php', 'oishy.php']);

// Load theme locales
//$app->locale->initThemeLocales();

// Good ol' jQuery <3
sp_register_script('jquery', site_uri('assets/js/jquery-3.3.1.min.js'));

// Polyfills
sp_register_script('polyfill-io', '//cdn.polyfill.io/v2/polyfill.min.js');

// Google recaptcha
sp_register_script('google-recaptcha', '//www.google.com/recaptcha/api.js');

// Parsley form validator
sp_register_script('parsley', site_uri('assets/js/parsley.min.js'));
