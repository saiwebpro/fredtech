<?php

namespace spark\controllers\Dashboard;

use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Extension;
use Upload\Validation\Size;
use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Auth\Auth;
use spark\drivers\Http\UpdateChecker;
use spark\drivers\I18n\Locale;
use spark\drivers\Mail\Mailer;
use spark\drivers\Nav\MenuStorage;
use spark\drivers\Nav\Pagination;
use spark\drivers\Nav\SidebarNav;
use spark\models\ContentModel;
use spark\models\OptionModel;
use spark\models\RoleModel;
use spark\models\TokenModel;
use spark\models\UserModel;

/**
* DashboardController
*
* @package spark
*/
class DashboardController extends Controller
{
    public function __construct()
    {
        registry_store('is_frontend', false);

        registry_store('is_dashboard', true);

        parent::__construct();

        
        $locale = dashboard_locale_path('en_US');

        load_textdomain($locale, Locale::DEFAULT_TEXTDOMAIN);

        /**
         * @event Fires before DashboardController is initialized
         */
        do_action('dashboard.controller_init_before');

        // Dashboard pages ain't meant to be cached
        // static resources on the other hand will be cached
        slim_no_cache_headers();

        // Please don't index
        view_set('meta.noindex', true);

        // Load Dashboard Menus and assets partialy for easier version upgrades
        require(trailingslashit(__DIR__) . 'inc/dashboard_menus.php');
        require(trailingslashit(__DIR__) . 'inc/dashboard_assets.php');

        // add dashboard to crumb array
        breadcrumb_add('dashboard', __('Dashboard'), url_for('dashboard'));

        $nonLoggedRoutes = [
            'dashboard.account.register', 'dashboard.account.register_post',
            'dashboard.account.signin',  'dashboard.account.signin_post',
            'dashboard.account.forgotpass', 'dashboard.account.forgotpass_post',
            'dashboard.account.resetpass',  'dashboard.account.resetpass_post',
            'dashboard.ajax.email_check', 'dashboard.ajax.username_check',
            'dashboard.account.verify_action',
        ];


        /**
         * @filter Filters non logged route names for dashboard
         *
         * @var array
         */
        $nonLoggedRoutes = apply_filters('dashboard.non_logged_routes', $nonLoggedRoutes);

        $nonVerifiedRoutes = [
            'dashboard.account.activation', 'dashboard.account.verify_action',
            'dashboard.account.activation_post', 'dashboard.account.logout'
        ];

        /**
         * @filter Filters non verified route names for dashboard
         *
         * @var array
         */
        $nonVerifiedRoutes = apply_filters('dashboard.non_verified_routes', $nonVerifiedRoutes);


        $redirectTo = '?redirect_to=' . rawurlencode(get_current_route_uri(true));
        $nonLoggedRedirectURI = url_for('dashboard.account.signin') . $redirectTo;
        $nonVerifiedRedirectURI = url_for('dashboard.account.activation') . $redirectTo;

        // Non logged user's can go and suck it
        if (!is_logged()) {
            // unless you're at one of these routes
            if (!in_array(get_current_route_name(), $nonLoggedRoutes)) {
                flash('account-warning', __("Please log in to access the page"));
                return redirect($nonLoggedRedirectURI);
            }
        } else {
            // Even if you're logged but don't have the permission to view dashboard
            // you can go and suck it too
            if (!current_user_can('access_dashboard')) {
                sp_not_permitted();
            }

            // If you're not verified and not on the allowed routes you'll be redirected to the verification page
            if (!(bool) current_user_field('is_verified') &&
                config('auth.force_email_verification', false) &&
                !in_array(get_current_route_name(), $nonVerifiedRoutes)
            ) {
                return redirect($nonVerifiedRedirectURI);
            }
        }


        $updates = [];

        if (config('site.check_for_updates') && is_admin()) {
            $update = new UpdateChecker;
            $updates = $update->check();
        }

        view_set('updates', $updates);

        /**
         * @event Fires after DashboardController has been initialized
         */
        do_action('dashboard.controller_init_after');
    }

    /**
     * Dashboard index
     *
     * @return
     */
    public function dashboard()
    {
        $userModel = new UserModel;
        $contentModel = new ContentModel;
        $roleModel = new RoleModel;
        $usersCount = $userModel->countRows();

        $filters = [];
        $filters['where'][] = ['content_type', '=', ContentModel::TYPE_PAGE];

        $pagesCount = $contentModel->countRows(null, $filters);

        $filters = [];

        $filters['where'][] = ['content_type', '=', ContentModel::TYPE_ATTACHMENT];

        $galleryCount = $contentModel->countRows(null, $filters);

        $rolesCount = $roleModel->countRows();

        sp_enqueue_script('parsley', 2, ['jquery']);


        $data = [
            'dashboard__active' => 'active',
            'users_count'       => $usersCount,
            'roles_count'       => $rolesCount,
            'pages_count'       => $pagesCount,
            'gallery_count'     => $galleryCount,
        ];

        return view('admin::dashboard.php', $data);
    }

    /**
     * Dashboard credits
     *
     * @return
     */
    public function credits()
    {
        $phpLibraries = [];

        $phpLibraries[] = [
            'name'    => 'slim microframework 2.0',
            'url'     => 'https://docs.slimframework.com/',
            'desc'    => 'Slim is a PHP micro framework that helps you quickly write simple yet powerful web applications and APIs.',
            'license' => 'The MIT License',
        ];

        $phpLibraries[] = [
            'name'    => 'Slim-PDO',
            'url'     => 'https://github.com/FaaPz/Slim-PDO',
            'desc'    => 'PDO database library for Slim Framework',
            'license' => 'The MIT License',
        ];

        $phpLibraries[] = [
            'name'    => 'symfony/polyfill-mbstring',
            'url'     => 'https://github.com/symfony/polyfill-mbstring',
            'desc'    => 'This component provides a partial, native PHP implementation for the Mbstring extension',
            'license' => 'The MIT License',
        ];

        $phpLibraries[] = [
            'name'    => 'symfony/polyfill-php55',
            'url'     => 'https://github.com/symfony/polyfill-php55',
            'desc'    => 'This component provides functions unavailable in releases prior to PHP 5.5',
            'license' => 'The MIT License',
        ];

        $phpLibraries[] = [
            'name'    => 'symfony/polyfill-php56',
            'url'     => 'https://github.com/symfony/polyfill-php56',
            'desc'    => 'This component provides functions unavailable in releases prior to PHP 5.6',
            'license' => 'The MIT License',
        ];

        $phpLibraries[] = [
            'name'    => 'phpmailer/phpmailer',
            'url'     => 'https://github.com/PHPMailer/PHPMailer',
            'desc'    => 'A full-featured email creation and transfer class for PHP',
            'license' => 'GNU LGPL',
        ];

        $phpLibraries[] = [
            'name'    => 'phpmyadmin/motranslator',
            'url'     => 'https://github.com/phpmyadmin/motranslator',
            'desc'    => 'Translation API for PHP using Gettext MO files',
            'license' => 'GNU LGPL',
        ];

        $phpLibraries[] = [
            'name'    => 'rmccue/requests',
            'url'     => 'https://github.com/rmccue/Requests',
            'desc'    => 'Requests for PHP is a humble HTTP request library',
            'license' => 'Custom',
        ];

        $phpLibraries[] = [
            'name'    => 'vlucas/valitron',
            'url'     => 'https://github.com/vlucas/valitron',
            'desc'    => 'Easy Validation That Doesn\'t Suck',
            'license' => 'The BSD 3-Clause License',
        ];

        $phpLibraries[] = [
            'name'    => 'codeguy/upload',
            'url'     => 'https://github.com/brandonsavage/Upload',
            'desc'    => 'This component simplifies file validation and uploading',
            'license' => 'The MIT License',
        ];

        $phpLibraries[] = [
            'name'    => 'pclzip/pclzip',
            'url'     => 'https://github.com/ivanlanin/pclzip',
            'desc'    => 'A PHP library that offers compression and extraction functions for Zip formatted archives',
            'license' => 'GNU LGPL'
        ];

        $phpLibraries[] = [
            'name'    => 'tedivm/stash',
            'url'     => 'https://github.com/tedious/stash',
            'desc'    => 'A PHP Caching Library',
            'license' => 'The BSD License'
        ];

        $phpLibraries[] = [
            'name'    => 'monolog/monolog',
            'url'     => 'https://github.com/Seldaek/monolog',
            'desc'    => 'Sends your logs to files, sockets, inboxes, databases and various web services',
            'license' => 'The MIT License'
        ];

        $phpLibraries[] = [
            'name'    => 'ircmaxell/password-compat',
            'url'     => 'https://github.com/ircmaxell/password_compat',
            'desc'    => 'A compatibility library for the proposed simplified password hashing algorithm: https://wiki.php.net/rfc/password_hash"',
            'license' => 'The MIT License'
        ];

        // JavaScript libraries

        $jsLibraries = [];

        $jsLibraries[] = [
            'name'    => 'jQuery',
            'url'     => 'https://jquery.com/',
            'desc'    => 'Famous ol\' jQuery',
            'license' => 'The MIT License'
        ];

        $jsLibraries[] = [
            'name'    => 'Trumbowyg',
            'url'     => 'https://alex-d.github.io/Trumbowyg/',
            'desc'    => 'A lightweight WYSIWYG editor',
            'license' => 'The MIT License'
        ];

        $jsLibraries[] = [
            'name'    => 'jQuery Countdown',
            'url'     => 'https://rendro.github.io/countdown/',
            'desc'    => 'Simple, lightweight and easy to use jQuery countdown plugin',
            'license' => 'The MIT License'
        ];

        $jsLibraries[] = [
            'name'    => 'jQuery Dialog.js',
            'url'     => 'https://www.jqueryscript.net/other/iOS-Style-Confirm-Alert-Dialog-Plugin-For-jQuery-Dialog-js.html',
            'desc'    => 'iOS Style Confirm / Alert Dialog Plugin For jQuery',
            'license' => 'The MIT License'
        ];

        $jsLibraries[] = [
            'name'    => 'jQuery Form Toggle',
            'url'     => 'http://madebykiwi.com/dev_center/jquery_form_toggle',
            'desc'    => 'A jQuery plugin that makes it easy to show and hide elements based on the state of a checkbox, radio button, or select menu',
            'license' => 'The MIT License'
        ];

        $jsLibraries[] = [
            'name'    => 'Parsley',
            'url'     => 'http://parsleyjs.org/',
            'desc'    => 'The ultimate JavaScript form validation library',
            'license' => 'The MIT License'
        ];

        $jsLibraries[] = [
            'name'    => 'Dropzone',
            'url'     => 'https://www.dropzonejs.com/',
            'desc'    => 'DropzoneJS is an open source library that provides drag’n’drop file uploads with image previews.',
            'license' => 'The MIT License'
        ];

        // others

        $others = [];

        $others[] = [
            'name'    => 'Bootstrap 4',
            'url'     => 'https://getbootstrap.com/',
            'desc'    => 'Bootstrap is an open source toolkit for developing with HTML, CSS, and JS.',
            'license' => 'The MIT License'
        ];

        $others[] = [
            'name'    => 'Tabler UI Kit',
            'url'     => 'https://github.com/tabler/tabler',
            'desc'    => 'Tabler is free and open-source HTML Dashboard UI Kit built on Bootstrap 4',
            'license' => 'The MIT License'
        ];

        $others[] = [
            'name'    => 'Google ReCaptcha',
            'url'     => 'https://www.google.com/recaptcha/intro/v3.html',
            'desc'    => 'The new way to stop bots',
            'license' => 'N/A'
        ];

        $others[] = [
            'name'    => 'Polyfill.io',
            'url'     => 'https://www.google.com/recaptcha/intro/v3.html',
            'desc'    => 'Upgrade the web. Automatically.',
            'license' => 'N/A'
        ];

        $data = [
            'credits__active' => 'active',
            'php_libraries'   => $phpLibraries,
            'js_libraries'    => $jsLibraries,
            'others'          => $others,
        ];

        return view('admin::credits.php', $data);
    }
}
