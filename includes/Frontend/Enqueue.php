<?php
/**
 * Enqueue class
 */

declare( strict_types = 1 );

namespace Another\Plugin\Another_Wishlist\Frontend;

use Another\Plugin\Another_Wishlist\Contracts\CanRegister;
use Another\Plugin\Another_Wishlist\Exceptions\Frontend_Exception;
use Another\Plugin\Another_Wishlist\Plugin;

/**
 * Class Enqueue
 */
class Enqueue implements CanRegister {

	private Plugin $context;

	/**
	 * Wishlist_Post_Type constructor.
	 *
	 * @param Plugin|null $context Plugin context.
	 */
	public function __construct( ?Plugin $context = null ) {
		if ( \is_null( $context ) ) {
			$context = clone Plugin::instance();
		}

		$this->context = $context;
	}

	/**
	 * Enqueue scripts
	 *
	 * @throws Frontend_Exception If failed to register script.
	 */
	public function register(): void {
		$registered = wp_register_script(
			'another-wishlist',
			$this->context->plugin_url() . 'assets/js/another-wishlist.js',
			array( 'jquery' ),
			$this->context->version(),
			array(
				'in_footer' => true,
			)
		);

		if ( ! $registered ) {
			throw new Frontend_Exception( 'Failed to register script' );
		}

		wp_enqueue_script( 'another-wishlist' );
	}
}
