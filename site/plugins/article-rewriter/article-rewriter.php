<?php

use mirazmac\plugins\Rewriter\Rewriter;

/*
Plugin Name: Article Rewriter
Plugin URI: http://github.com/MirazMac
Description: This plugin provides basic article rewriting facilities by replacing the words using their synonyms. Currently only English is supported.
Author: Miraz Mac
Version: 1.0
Author URI: https://mirazmac.com/
*/

defined('SPARKIN') or exit('lol xd');

$app = app();

// Absolute to Plugin Directory
define('REWRITER_PLUGIN_PATH', sp_plugin_path(__FILE__));

// We need to register PSR-4 namespaces if we're gonna use OOP
sp_register_psr4('mirazmac\\plugins\\Rewriter\\', trailingslashit(REWRITER_PLUGIN_PATH) . 'lib/');

// Register our own templates to use in options and others
register_templates('article-rewriter', trailingslashit(REWRITER_PLUGIN_PATH) . 'views');

$rewriter = new Rewriter;

fire_on_enable(__FILE__, [$rewriter, 'onEnable']);

fire_on_disable(__FILE__, [$rewriter, 'onDisable']);


add_action('dashboard.feeds.actions_before', [$rewriter, 'addButtonsToFeed']);

add_action('post_imported_after', [$rewriter, 'rewriteArticle']);


// Define the routes
add_action('plugins.loaded', function () use ($app) {
    $app->get('/rewriter/feed/:id', 'mirazmac\\plugins\\Rewriter\\RewriterController:manageFeed')
        ->name('rewriter.feed');

    $app->post('/rewriter/feed/:id', 'mirazmac\\plugins\\Rewriter\\RewriterController:manageFeedPOST')
        ->name('rewriter.feed_post');
});
