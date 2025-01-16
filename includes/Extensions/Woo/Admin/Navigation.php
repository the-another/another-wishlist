<?php
/**
 * Admin Navigation extension for WooCommerce.
 */

declare( strict_types = 1 );

namespace Another\Plugin\Another_Wishlist\Extensions\Woo\Admin;

use Another\Plugin\Another_Wishlist\Contracts\CanRegister;

/**
 * Class Navigation
 */
class Navigation implements CanRegister {

	/**
	 * Register hooks
	 */
	public function register(): void {
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_wishlist_menu_item' ) );
	}

	/**
	 * Add wishlist menu item
	 *
	 * @param array<string, string> $items Menu items.
	 *
	 * @return array<string, string>
	 */
	public function add_wishlist_menu_item( array $items ): array {
		$items['wishlist'] = __( 'Wishlist', 'another-wishlist' );

		return $items;
	}
}
