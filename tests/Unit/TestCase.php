<?php
declare(strict_types=1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Yoast\WPTestUtils\BrainMonkey\YoastTestCase;
use function call_user_func_array;

/**
 * TestCase base class.
 */
abstract class TestCase extends YoastTestCase {

	/**
	 * Options being mocked.
	 *
	 * @var array
	 */
	protected array $mocked_options = [ 'wpseo', 'wpseo_titles', 'wpseo_taxonomy_meta', 'wpseo_social', 'wpseo_ms' ];

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

		Monkey\Functions\expect( 'get_option' )
			->zeroOrMoreTimes()
			->with( call_user_func_array( 'Mockery::anyOf', $this->mocked_options ) )
			->andReturn( [] );

		Monkey\Functions\expect( 'get_site_option' )
			->zeroOrMoreTimes()
			->with( call_user_func_array( 'Mockery::anyOf', $this->mocked_options ) )
			->andReturn( [] );
	}
}
