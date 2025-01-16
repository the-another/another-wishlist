<?php
/**
 * Plugin Name: Another Woo Wishlist
 * Plugin URI: https://github.com/the-another/another-wishlist
 * Description: Another wishlist plugin.
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
	add_action(
		'plugins_loaded',
		static function (): void {
			Another_Wishlist(
				array(
					'version'     => '1.0.0',
					'text_domain' => 'another-wishlist',
					'plugin_name' => 'Another Wishlist',
					'plugin_file' => __FILE__,
					'plugin_path' => plugin_dir_path( __FILE__ ),
					'plugin_url'  => plugin_dir_url( __FILE__ ),
				)
			)->init();
		}
	);
} )();

/**
 * Main plugin function.
 *
 * @param array $params Plugin constructor parameters.
 *
 * @return Plugin
 */
function Another_Wishlist( array $params = array() ): Plugin {
	return Plugin::instance( $params );
}
