<?php
/**
 * Plugin Name: Another Woo Wishlist
 * Plugin URI: https://github.com/the-another/another-wishlist
 * Description: Another wishlist plugin for WooCommerce.
 * Author: Nemanja Cimbaljevic <wpcimba@pm.me>
 * Version: 1.0.0
 * Author URI: http://cimba.blog/
 */

declare(strict_types = 1);

use Another\Plugin\Another_Wishlist\Plugin;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

// Autoloader for dependencies.
require_once plugin_dir_path( __FILE__ ) . 'vendor_prefixed/scoper-autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

( static function (): void {
	$plugin = Another_Wishlist();
	add_action(
		'plugins_loaded',
		static function () use ( $plugin ): void {
			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			$plugin->init();
		}
	);
} )();

/**
 * Main plugin function.
 *
 * @return Plugin
 */
function Another_Wishlist(): Plugin {
	return Plugin::instance();
}
