<?php

/*
Theme Name: Oishy
Theme URI: http://github.com/MirazMac
Description: The brand new shining default theme. Simple, calm and works like a charm. This theme should be cloned if you wanna create a new theme.
Author: Miraz Mac
Version: 0.2
Author URI: https://mirazmac.com
*/

// Theme Stylesheet
sp_register_style(
    'theme-styles',
    current_theme_uri('assets/css/styles.css'),
    ['abspath' => current_theme_path('assets/css/styles.css')]
);

// Google Webfont
sp_register_style(
    'theme-webfont',
    '//fonts.googleapis.com/css?family=DM+Sans:400,500&display=swap'
);

// Theme Bootstrap Bundle
sp_register_script(
    'theme-bootstrap-js-bundle',
    current_theme_uri('assets/js/bootstrap.bundle.min.js'),
    ['abspath' => current_theme_path('assets/js/bootstrap.bundle.min.js')]
);

// Theme JS
sp_register_script(
    'theme-js',
    current_theme_uri('assets/js/theme.js'),
    ['abspath' => current_theme_path('assets/js/theme.js')]
);


// jQuery autocomplete
sp_register_script(
    'jquery-autocomplete',
    current_theme_uri('assets/js/jquery.auto-complete.min.js')
);

// jQuery sticky sidebar
sp_register_script(
    'jquery-sticky-sidebar',
    current_theme_uri('assets/js/jquery.sticky-sidebar.min.js')
);

// Superplaceholder
sp_register_script(
    'jquery-unveil',
    current_theme_uri('assets/js/jquery.unveil.min.js')
);


// Frontend tasks
if (is_frontend()) {
    // Enqueue assets
    sp_enqueue_style('theme-styles');
    sp_enqueue_style('theme-webfont');
    sp_enqueue_script('jquery', 2);
    sp_enqueue_script('theme-bootstrap-js-bundle', 2, ['jquery']);
    sp_enqueue_script('theme-js', 2, ['jquery']);
    sp_enqueue_script('jquery-autocomplete', 2, ['jquery']);
    sp_enqueue_script('jquery-sticky-sidebar', 2, ['jquery']);
    sp_enqueue_script('jquery-unveil', 2, ['jquery']);
}

// Load locale
$locale = get_cookie_locale() ? get_cookie_locale() : get_option('site_language', 'en_US');

load_theme_locale($locale);

// Register the theme options
register_theme_options(
    'theme-options.php',
    function () use ($app) {
        if (is_demo()) {
            flash('settings-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return false;
        }

        $ignore = [];

        $layouts = [
            'single' => ['single', 'single-with-sidebar']
        ];

        $data = [
            'site_language' => $app->request->post('site_language'),
            'default_theme_custom_css' => $app->request->post('default_theme_custom_css'),
            'single_page_layout' => $app->request->post('single_page_layout'),
        ];

        if (!in_array($data['single_page_layout'], $layouts['single'])) {
            unset($data['single_page_layout']);
        }

        // Make sure the language exists
        $locales = get_theme_locales();
        if (!isset($locales[$data['site_language']])) {
            unset($data['site_language']);
        }

        foreach ($data as $key => $value) {
            // Strip values
            if (!in_array($key, $ignore)) {
                $value = sp_strip_tags($value);
            }

            set_option($key, $value);
        }

        flash('settings-success', __('options-updated-successfully', _T));
    },
    __('theme-options', _T)
);


$single_layout = get_option('single_page_layout', 'single');

if (has_template("{$single_layout}.php")) {
    add_filter('site.read.template.name', function ($template) use ($app, $single_layout) {
        // Don't change the template if current request is 404
        // for example when post is not found
        if ($app->response->isNotFound()) {
            return $template;
        }

        return "{$single_layout}.php";
    });
}
