<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\models\AttemptModel;
use spark\models\TokenModel;

/**
* Controller for Settings Page
*
* @package spark
*/
class DashboardSettingsController extends DashboardController
{
    protected $regions = [
      'AR' => 'Argentina',
      'AU' => 'Australia',
      'AT' => 'Austria',
      'BE' => 'Belgium',
      'BR' => 'Brazil',
      'CA' => 'Canada',
      'CL' => 'Chile',
      'CO' => 'Colombia',
      'CZ' => 'Czechia',
      'DK' => 'Denmark',
      'EG' => 'Egypt',
      'FI' => 'Finland',
      'FR' => 'France',
      'DE' => 'Germany',
      'GR' => 'Greece',
      'HK' => 'Hong Kong',
      'HU' => 'Hungary',
      'IN' => 'India',
      'ID' => 'Indonesia',
      'IE' => 'Ireland',
      'IL' => 'Israel',
      'IT' => 'Italy',
      'JP' => 'Japan',
      'KE' => 'Kenya',
      'MY' => 'Malaysia',
      'MX' => 'Mexico',
      'NL' => 'Netherlands',
      'NZ' => 'New Zealand',
      'NG' => 'Nigeria',
      'NO' => 'Norway',
      'PH' => 'Philippines',
      'PL' => 'Poland',
      'PT' => 'Portugal',
      'RO' => 'Romania',
      'RU' => 'Russia',
      'SA' => 'Saudi Arabia',
      'SG' => 'Singapore',
      'ZA' => 'South Africa',
      'KR' => 'South Korea',
      'SE' => 'Sweden',
      'CH' => 'Switzerland',
      'TW' => 'Taiwan',
      'TH' => 'Thailand',
      'TR' => 'Turkey',
      'UA' => 'Ukraine',
      'GB' => 'United Kingdom',
      'US' => 'United States',
      'VN' => 'Vietnam',
    ];

    protected $popularFilters = ['all-time', 'daily', 'weekly', 'monthly', 'yearly'];

    public function __construct()
    {
        parent::__construct();

        /**
         * @event Fires before DashboardSettingsController is initialized
         */
        do_action('dashboard.settings_controller_init_before');

        breadcrumb_add('dashboard.settings', __('Settings'), '#settings');

        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);
        sp_enqueue_script('jquery-form-toggle', 2);

        view_set('settings__active', 'settings-active');
        //view_set('parent_tabs_key', 'settings');

        /**
         * @event Fires after DashboardSettingsController is initialized
         */
        do_action('dashboard.settings_controller_init_after');
    }

    public function index($type)
    {
        $app = app();
        $template = "admin::settings/{$type}.php";

        if (!has_template($template)) {
            return $app->notFound();
        }

        $data = [
            "settings_{$type}__active" => 'active',
            'type' => $type,
        ];


        if ($type === 'site') {
            $data['regions'] = $this->regions;
            $data['popular_posts_filters'] = $this->popularFilters;
        }


        return view($template, $data);
    }

    public function indexPOST($type)
    {
        $app = app();
        $type = strtolower($type);
        $method = "{$type}POST";

        $template = "admin::settings/{$type}.php";

        if (!has_template($template)) {
            return $app->notFound();
        }

        if ($method !== 'index' && is_callable([$this, $method])) {
            if (is_demo()) {
                flash('settings-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
                return redirect_to_current_route();
            }
            
            /**
             * @event Runs when a system setting form is submitted. Dynamic portion of the hook name `$type`
             *       refers to the setting template name. Eg.: `settings.general.submitted_before`
             */
            do_action("settings.{$type}.submitted_before");
            $this->{$method}();

            /**
             * @event Runs when a system setting form is submitted and everything is processed.
             *       Dynamic portion of the hook name `$type` refers to the setting template name.
             *       Eg.: `settings.general.submitted_after`
             */
            do_action("settings.{$type}.submitted_after");

            return;
        }

        return $app->notFound();
    }

    /**
     * Update general settings
     *
     * @return
     */
    public function generalPOST()
    {
        $app = app();
        $req = $app->request;

        $needsCleaning = [
            'site_name', 'site_tagline', 'site_description', 'site_logo', 'site_email', 'timezone',
            'site_locale',
        ];

        $data = [
            'site_name'           => $req->post('site_name'),
            'site_tagline'        => $req->post('site_tagline'),
            'site_locale'         => $req->post('site_locale'),
            'site_description'    => $req->post('site_description'),
            'site_logo'           => $req->post('site_logo'),
            'site_email'          => $req->post('site_email'),
            'timezone'            => $req->post('timezone'),
            'timezone'            => $req->post('timezone'),
            'enable_registration' => sp_int_bool($req->post('enable_registration')),
            'header_scripts'      => $req->post('header_scripts'),
            'footer_scripts'      => $req->post('footer_scripts'),
        ];

        $v = new Validator($data);
        $v->labels([
            'site_name' => __('Site Name'),
            'site_tagline' => __('Site Tagline'),
            'site_description' => __('Site Description'),
            'site_logo' => __('Site Logo'),
            'site_email' => __('Site E-mail'),
            'timezone' => __('Site Timezone'),
        ]);
        $v->rule('required', ['site_name', 'site_tagline', 'site_description', 'site_logo', 'site_email', 'timezone'])
          ->rule('email', 'site_email')
          ->rule('timezone', 'timezone');

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('settings-danger', $errors);
        } else {
            foreach ($data as $key => $value) {
                if (in_array($key, $needsCleaning)) {
                    $value = sp_strip_tags($value);
                }
                set_option($key, $value);
            }

            flash('settings-success', __('Settings were updated successfully.'));
        }

        return redirect_to_current_route();
    }

    /**
     * Handles site settings
     *
     * @return
     */
    public function sitePOST()
    {
        $app = app();
        $req = $app->request;

        $data = [
            'latest_posts_count' => (int) $req->post('latest_posts_count'),
            'related_posts_count' => (int) $req->post('related_posts_count'),
            'popular_posts_count' => (int) $req->post('popular_posts_count'),
            'category_posts_count' => (int) $req->post('category_posts_count'),
            'search_items_count' => (int) $req->post('search_items_count'),
            'max_slider_items' => (int) $req->post('max_slider_items'),
            'auto_delete_posts_after' => (int) $req->post('auto_delete_posts_after'),
            'feed_redirection' => sp_int_bool($req->post('feed_redirection')),
            'enable_search_ads' => sp_int_bool($req->post('enable_search_ads')),
            'use_search_as_default' => sp_int_bool($req->post('use_search_as_default')),
            'search_links_newwindow' => sp_int_bool($req->post('search_links_newwindow')),
            'trends_region' => trim($req->post('trends_region')),
            'default_thumb_url' => trim(sp_strip_tags($req->post('default_thumb_url'))),
            'safesearch_status' => trim($req->post('safesearch_status')),
            'iframe_allowed_domains' => trim(sp_strip_tags($req->post('iframe_allowed_domains'))),
            'popular_posts_interval' => trim(sp_strip_tags($req->post('popular_posts_interval'))),
        ];

        $domains = explode("\n", $data['iframe_allowed_domains']);
        $trustedDomains = '';

        foreach ($domains as $domain) {
            $domain = trim($domain);
            if (filter_var("https://{$domain}", FILTER_VALIDATE_URL)) {
                $trustedDomains .= $domain . "\n";
            }
        }

        $data['iframe_allowed_domains'] = trim($trustedDomains);

        if (!isset($this->regions[$data['trends_region']])) {
            unset($data['trends_region']);
        }

        // Uh-uh you don't
        if (!in_array($data['popular_posts_interval'], $this->popularFilters)) {
            unset($data['popular_posts_interval']);
        }


        if (!in_array($data['safesearch_status'], ['off', 'moderate', 'active'])) {
            $data['safesearch_status'] = 'off';
        }

        $needsCleaning = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $needsCleaning)) {
                $value = sp_strip_tags($value);
            }

            set_option($key, $value);
        }


        flash('settings-success', __('Settings were updated successfully.'));
        return redirect_to_current_route();
    }

    public function servicesPOST()
    {
        $app = app();
        $req = $app->request;

        $needsCleaning = [
            'google_recaptcha_secret_key', 'google_recaptcha_site_key', 'facebook_app_id', 'facebook_app_secret',
            'disqus_url'
        ];
        $data = [
            'google_recaptcha_secret_key' => $req->post('google_recaptcha_secret_key'),
            'google_recaptcha_site_key'   => $req->post('google_recaptcha_site_key'),
            'facebook_app_secret'         => $req->post('facebook_app_secret'),
            'facebook_app_id'             => $req->post('facebook_app_id'),
            'disqus_url'                  => trim($req->post('disqus_url'), ' /'),
            'captcha_enabled'             => sp_int_bool($req->post('captcha_enabled')),
            'disqus_enabled'              => sp_int_bool($req->post('disqus_enabled')),
            'fb_comments_enabled'         => sp_int_bool($req->post('fb_comments_enabled')),
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $needsCleaning)) {
                $value = sp_strip_tags($value);
            }

            set_option($key, $value);
        }


        flash('settings-success', __('Settings were updated successfully.'));
        return redirect_to_current_route();
    }

    public function socialPOST()
    {
        $app = app();
        $req = $app->request;

        $fields = ['facebook_username', 'twitter_username', 'youtube_username', 'instagram_username', 'linkedin_username'];
        foreach ($fields as $key) {
            $value = sp_strip_tags(trim($req->post($key)), true);
            set_option($key, $value);
        }

        flash('settings-success', __('Settings were updated successfully.'));

        return redirect_to_current_route();
    }

    public function emailPOST()
    {
        $app = app();
        $req = $app->request;

        $data = [
            'smtp_enabled'      => (bool) $req->post('smtp_enabled'),
            'smtp_auth_enabled' => (bool) $req->post('smtp_auth_enabled'),
            'smtp_host'         => sp_strip_tags($req->post('smtp_host')),
            'smtp_port'         => (int) $req->post('smtp_port'),
            'smtp_username'     => sp_strip_tags($req->post('smtp_username')),
            'smtp_password'     => $req->post('smtp_password'),
        ];

        foreach ($data as $key => $value) {
            set_option($key, $value);
        }

        flash('settings-success', __('Settings were updated successfully.'));

        return redirect_to_current_route();
    }

    public function adsPOST()
    {
        $app = app();
        $req = $app->request;

        $data = [];

        $units = 8;

        // No need to proccess anything here
        for ($i=1; $i < $units + 1; $i++) {
            $data["ad_unit_{$i}"] = $req->post("ad_unit_{$i}");
        }

        foreach ($data as $key => $value) {
            set_option($key, $value);
        }

        flash('settings-success', __('Settings were updated successfully.'));

        return redirect_to_current_route();
    }

    public function debugPOST()
    {
        // We may need a lot of time to run
        ignore_user_abort(1);
        set_time_limit(0);

        $app = app();
        $req = $app->request;

        $actions = [
            'clear_tokens' => sp_int_bool($req->post('clear_tokens')),
            'clear_attempts' => sp_int_bool($req->post('clear_attempts')),
            'flush_cache' => sp_int_bool($req->post('flush_cache')),
            'regen_cron_token' => sp_int_bool($req->post('regen_cron_token')),
        ];

        if ($actions['regen_cron_token']) {
            $token = str_random_secure(10);
            set_option('spark_cron_job_token', $token);
        }

        if ($actions['flush_cache']) {
            // Purge site wide cache
            $app->cache->clear();

            // clear thumbnails cache
            $path = trailingslashit(THUMB_CACHE) . '*.img.txt';
            $thumbnails = glob($path);

            foreach ($thumbnails as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        if ($actions['clear_attempts']) {
            $attemptModel = new AttemptModel;
            $attemptModel->truncate();
        }

        if ($actions['clear_tokens']) {
            $tokenModel = new TokenModel;
            $tokenModel->clearExpiredTokens();
        }


        flash('settings-success', __('Actions were performed successfully'));

        return redirect_to_current_route();
    }

    public function pluginOptions($plugin)
    {
        $pluginManager = app()->plugins;

        $template = registry_read("{$plugin}__options_template");

        // Make sure the plugin has registered templates
        if (!$template) {
            flash('dashboard-danger', sprintf("Plugin <em>%s</em> doesn't have any options registered", sp_strip_tags($plugin)));
            return redirect_to('dashboard');
        }


        breadcrumb_add("dashboard.plugins", __('Plugins'), url_for('dashboard.plugins'));

        // grab plugin meta
        $pluginMeta = $pluginManager->getPluginMeta($plugin);

        // Breadcrumbs
        breadcrumb_add("dashboard.settings.{$plugin}", sprintf(__('%s Options'), $pluginMeta['name']));


        // Mark current option page as active
        view_set("plugin-{$plugin}-options__active", 'active');

        $data = [
            'item' => $plugin,
            'meta' => $pluginMeta,
            'type' => 'plugin',
        ];

        return view($template, $data);
    }

    public function pluginOptionsPOST($plugin)
    {
        $pluginManager = app()->plugins;

        $template = registry_read("{$plugin}__options_template");

        // Make sure the plugin has registered templates
        if (!$template) {
            flash('dashboard-danger', sprintf("Plugin <em>%s</em> doesn't have any options registered", sp_strip_tags($plugin)));
            return redirect_to('dashboard');
        }

        /**
         * @event Fires whenever any plugin's options form is submitted
         *
         * @param string $plugin Plugin's key
         *
         */
        do_action('settings.plugin.options_submitted', $plugin);

        /**
         * @event Fires after plugin options form has been submitted. Dynamic portion of
         *             the hook name `plugin` refers to current plugin's directory name
         *
         * @param string $plugin Plugin's key
         */
        do_action("{$plugin}OnOptionsSubmit");

        return redirect_to_current_route();
    }

    public function themeOptions($theme)
    {
        $themeManager = app()->theme;

        $template = registry_read("theme.{$theme}__options_template");

        // Make sure the plugin has registered templates
        if (!$template) {
            flash('dashboard-danger', sprintf("Theme <em>%s</em> doesn't have any options registered", sp_strip_tags($theme)));
            return redirect_to('dashboard');
        }


        breadcrumb_add("dashboard.themes", __('Themes'), url_for('dashboard.themes'));

        // grab theme meta
        $themeMeta = $themeManager->getThemeMeta($theme);

        // Breadcrumbs
        breadcrumb_add("dashboard.settings.{$theme}", sprintf(__('%s Options'), $themeMeta['name']));

        // Mark current option page as active
        view_set("theme-{$theme}-options__active", 'active');

        $data = [
            'item' => $theme,
            'meta' => $themeMeta,
            'type' => 'theme',
        ];

        return view($template, $data);
    }

    public function themeOptionsPOST($theme)
    {
        $themeManager = app()->theme;

        $template = registry_read("theme.{$theme}__options_template");

        // Make sure the plugin has registered templates
        if (!$template) {
            flash('dashboard-danger', sprintf("Theme <em>%s</em> doesn't have any options registered", sp_strip_tags($theme)));
            return redirect_to('dashboard');
        }

        /**
         * @event Fires whenever current theme options form is submitted
         *
         * @param string $theme theme's key
         *
         */
        do_action('settings.theme.options_submitted', $theme);

        /**
         * @event Fires after theme options form has been submitted. Dynamic portion of
         *             the hook name `theme` refers to current theme's directory name
         *
         * @param string $theme Theme's key
         */
        do_action("theme.{$theme}OnOptionsSubmit", $theme);

        return redirect_to_current_route();
    }
}
