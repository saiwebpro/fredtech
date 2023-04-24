<?php

/*
Plugin Name: Feed & Sitemap
Plugin URI: http://github.com/MirazMac
Description: This plugin provides dynamic XML sitemaps and RSS Feed for the site.
Author: Miraz Mac
Version: 1.0
Author URI: https://mirazmac.com/
*/


$app = app();

// Absolute to Plugin Directory
define('SITEMAP_PLUGIN_PATH', sp_plugin_path(__FILE__));

// We need to register PSR-4 namespaces if we're gonna use OOP
sp_register_psr4('mirazmac\\plugins\\SiteMap\\', trailingslashit(SITEMAP_PLUGIN_PATH) . 'lib/');

// Register our own templates to use in options and others
register_templates('sitemap', trailingslashit(SITEMAP_PLUGIN_PATH) . 'views');

// Define the routes
add_action('plugins.loaded', function () use ($app) {
    $app->get('/sitemap.xml', 'mirazmac\\plugins\\SiteMap\\SiteMapController:sitemapIndex')->name('sitemap.index');
    $app->get('/sitemap-:id.xml', 'mirazmac\\plugins\\SiteMap\\SiteMapController:sitemap')->name('sitemap.list');


    $app->get('/rss.xml', 'mirazmac\\plugins\\SiteMap\\SiteMapController:rssIndex')->name('rss.index');
});


register_plugin_options(
    __FILE__,
    'sitemap::options.php',
    function () use ($app) {
        if (is_demo()) {
            flash('settings-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return false;
        }
    
        $data = [
            'sitemap_links_per_page' => (int) $app->request->post('sitemap_links_per_page'),
            'rss_items_per_page' => (int) $app->request->post('rss_items_per_page'),
            'rss_show_fulltext' => sp_int_bool($app->request->post('rss_show_fulltext')),
        ];

        foreach ($data as $key => $value) {
            set_option($key, $value);
        }

        flash('settings-success', 'Settings were updated successfully!');
    },
    'Sitemap Options'
);


add_action('sp.head_after', function () {
    echo "\t". '<link rel="alternate" type="application/rss+xml" title="RSS Feed for ' . e_attr(get_option('site_name')) . '" href="' . e_attr(url_for('rss.index')) . '">' . "\n";
});


add_action('dashboard.categories.actions_after', function (array $item) {
    echo '<a href="' . e_attr(url_for('rss.index')) . '?category='.e_attr($item['category_id']).'" target="_blank" class="btn btn-sm btn-warning">'.svg_icon('rss', 'mr-1').' RSS</a>';
});
