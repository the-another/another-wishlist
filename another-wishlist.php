<?php
declare(strict_types=1);

use Another\Plugin\Another_Wishlist\Plugin;

if (!defined('WPINC')) {
	exit;
}

// Autoloader for dependencies.
require_once plugin_dir_path(__FILE__) . 'vendor_prefixed/scoper-autoload.php';
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

(function () {
	$plugin = Another_Wishlist();
	add_action('plugins_loaded', function () use ($plugin) {
		if (!function_exists('WC')) {
			return;
		}

		$plugin->init();
	});
})();

function Another_Wishlist(): Plugin
{
	return Plugin::instance();
}


