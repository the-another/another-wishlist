<?php
/**
 * HasHook interface
 */

declare( strict_types = 1 );

namespace Another\Plugin\Another_Wishlist\Contracts;

interface WillRegister {
	/**
	 * Register hooks
	 */
	public function register(): void;
}
