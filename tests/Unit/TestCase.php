<?php
declare(strict_types=1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Yoast\WPTestUtils\BrainMonkey\YoastTestCase;
use function call_user_func_array;

/**
 * TestCase base class.
 */
abstract class TestCase extends YoastTestCase {

	/**
	 * Sets up the test fixtures.
	 *
	 * @return void
	 * @throws ExpectationArgsRequired
	 */
	protected function set_up(): void
	{
		parent::set_up();

		Monkey\Functions\stubs(
			[
				// Null makes it so the function returns its first argument.
				'is_admin' => false,
			]
		);
	}

	public function tear_down(): void
	{
		Mockery::close();
		Monkey\tearDown();
		parent::tear_down();
	}
}
