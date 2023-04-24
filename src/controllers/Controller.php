<?php

namespace spark\controllers;

/**
 * Parent Controller
 *
 * All controllers should extend this class
 *
 */
class Controller
{
    /**
     * Construct the Controller
     *
     * All Controllers must call this in their constructor exactly once!
     */
    public function __construct()
    {
        /**
         * @event Fires before Controller is initialized
         */
        do_action('controller.init_before');

        $app = app();

        if ((int) get_option('enable_registration', 0)) {
            $app->config('site.registration_enabled', true);
        }



        // Prevent caching at first
        // This header can be overwritten for dynamic cache-able content later
        $app->response->headers->set('Cache-Control', 'private,max-age=0');
        // Varies from time to time
        $app->response->headers->set('Vary', 'Accept-Encoding,User-Agent');

        $app->user->setupUser();

        // Set basic template values
        $title = get_option('site_name') . ' | ' . get_option('site_tagline');
        $description = get_option('site_description');
        $name = get_option('site_name');
        $image = site_uri('assets/img/og-image.png');
        $url = get_current_route_uri();

        $fbAppID = get_option('facebook_app_id');

        if (is_dashboard()) {
            $locale = get_site_locale();
        } else {
            $locale = get_cookie_locale() ? get_cookie_locale() : 'en_US';
        }

        view_data(
            [
                'title'                  => $title,
                'title_append_site_name' => true,
                'meta.description'       => $description,
                'meta.name'              => $name,
                'meta.image'             => $image,
                'meta.url'               => $url,
                'meta.type'              => 'website',
                'meta.locale'            => $locale,
                'meta.noindex'           => false,
                'meta.nocache'           => false,
                'meta.fb_app_id'         => $fbAppID,
                'meta.theme_color'       => "#e2043e",
            ]
        );
        
        // Load active theme functions
        require_once current_theme_path('skin.php');

        // Load frontend tasks file
        if (is_frontend()) {
            require trailingslashit(__DIR__) . 'frontend_task.php';
        } else {
            require trailingslashit(__DIR__) . 'backend_task.php';
        }


        /**
         * @event Fires after Controller is initialized
         */
        do_action('controller.init_after');
    }
}
