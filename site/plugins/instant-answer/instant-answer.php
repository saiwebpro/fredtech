<?php

/*
Plugin Name: Instant Answer
Plugin URI: http://github.com/MirazMac
Description: Instant answer plugin provides quick short answers for typical search queries.
Author: Miraz Mac
Version: 1.0
Author URI: https://mirazmac.com/
*/

use inbefore\plugins\InstantAnswer\Provider;

// Absolute to Plugin Directory
define('IA_PLUGIN_PATH', sp_plugin_path(__FILE__));

// We need to register PSR-4 namespaces if we're gonna use OOP
sp_register_psr4('inbefore\\plugins\\InstantAnswer\\', trailingslashit(IA_PLUGIN_PATH) . 'lib/');

// Register our own templates to use in options and others
register_templates('ia', trailingslashit(IA_PLUGIN_PATH) . 'views');

$iaProvider = new Provider;

// Add the action
add_action('search_results_before', [$iaProvider, 'suggest'], 8);
