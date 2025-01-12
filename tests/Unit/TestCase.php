<?php

declare(strict_types = 1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit;

use Brain\Monkey;
use Mockery;
use Yoast\WPTestUtils\BrainMonkey\YoastTestCase;

/**
 * TestCase base class.
 */
abstract class TestCase extends YoastTestCase {



	public function tear_down(): void {
		Mockery::close();
		Monkey\tearDown();
		parent::tear_down();
	}

	/**
	 * Sets up the test fixtures.
	 */
	protected function set_up(): void {
		parent::set_up();

		Monkey\Functions\stubs(
			array(
				// Null makes it so the function returns its first argument.
				'is_admin' => false,
			)
		);
	}
}
