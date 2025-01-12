<?php
/**
 * Main plugin file
 */

declare(strict_types = 1);

namespace Another\Plugin\Another_Wishlist;

use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;

if ( ! \defined( 'WPINC' ) ) {
	exit;
}

/**
 * Class Plugin
 */
final class Plugin {

	public static self $instance;

	private string $version     = '1.0.0';
	private string $text_domain = 'another-wishlist';
	private string $plugin_name = 'Another Woo Wishlist';
	private string $plugin_file = __FILE__;

	/**
	 * Registered post types
	 *
	 * @var string[] $post_types
	 */
	private array $post_types = array(
		'wishlist' => Wishlist_Post_Type::class,
	);

	private bool $initialized = false;

	/**
	 * Get plugin version
	 */
	public function version(): string {
		return $this->version;
	}

	/**
	 * Get plugin text domain string
	 */
	public function text_domain(): string {
		return $this->text_domain;
	}

	/**
	 * Get plugin name
	 */
	public function plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * Get plugin file path
	 */
	public function plugin_file(): string {
		return $this->plugin_file;
	}

	/**
	 * Get plugin directory path
	 */
	public function plugin_dir(): string {
		return plugin_dir_path( $this->plugin_file );
	}

	/**
	 * Get plugin directory URL
	 */
	public function initialized(): bool {
		return $this->initialized;
	}

	/**
	 * Initialize plugin
	 */
	public function init(): void {
		if ( $this->initialized ) {
			return;
		}

		foreach ( $this->post_types as $post_type ) {
			$post_type::hook();
		}

		$this->initialized = true;
	}

	/**
	 * Get plugin instance
	 */
	public static function instance(): self {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
