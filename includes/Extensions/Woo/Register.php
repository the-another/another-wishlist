<?php
/**
 * Register WooCommerce extension
 */

declare( strict_types = 1 );

namespace Another\Plugin\Another_Wishlist\Extensions\Woo;

use Another\Plugin\Another_Wishlist\Contracts\CanRegister;
use Another\Plugin\Another_Wishlist\Plugin;

class Register implements CanRegister {

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
	 * Register WooCommerce extensions
	 */
	public function register(): void {
		$extensions = array(
			new Admin\Navigation(),
		);

		foreach ( $extensions as $extension ) {
			$extension->register();
		}
	}
}
