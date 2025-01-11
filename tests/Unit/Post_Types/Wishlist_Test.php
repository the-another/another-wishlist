<?php
declare(strict_types=1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit\Post_Types;

use Another\Plugin\Another_Wishlist\Plugin;
use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;
use Another\Plugin\Another_Wishlist\Tests\Unit\TestCase;
use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use Mockery;

final class Wishlist_Test extends TestCase
{
	/**
	 * @return void
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type::register()
	 */
	public function test_register_success(): void
	{
		$expected_post_type = Mockery::mock('alias:WP_Post_Type');
		$expected_post_type->name = 'wishlist';

		Functions\when('register_post_type')->justReturn($expected_post_type);
		Functions\stubs([
			'__',
			'_x',
		]);

		$plugin_context = new Plugin();

		$wishlist_post_type = new Wishlist_Post_Type($plugin_context);
		$post_type = $wishlist_post_type->register();

		$this->assertEquals('wishlist', $post_type->name);
	}

	/**
	 * @return void
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type::register()
	 */
	public function test_register_failure(): void
	{
		$expected_error = Mockery::mock('alias:WP_Error');
		$expected_error->shouldReceive('get_error_message')->andReturn('Error message');

		Functions\when('register_post_type')->justReturn($expected_error);
		Functions\stubs([
			'__',
			'_x',
		]);

		$plugin_context = new Plugin();

		$wishlist_post_type = new Wishlist_Post_Type($plugin_context);
		$post_type = $wishlist_post_type->register();

		$this->assertInstanceOf('WP_Error', $post_type);
	}

	/**
	 * @return void
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type::register_post_type()
	 */
	public function test_register_post_type_success(): void
	{
		$plugin_context = new Plugin();

		$expected_post_type = Mockery::mock('alias:WP_Post_Type');
		$expected_post_type->name = 'wishlist';

		Functions\when('register_post_type')->justReturn($expected_post_type);
		Functions\stubs([
			'__',
			'_x',
		]);

		$wishlist_post_type = new Wishlist_Post_Type($plugin_context);
		$wishlist_post_type->register_post_type();

		$this->assertTrue(Actions\did('another_wishlist_post_type_registered') === 1);
	}

	/**
	 * @return void
	 * @throws ExpectationArgsRequired
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type::register_post_type()
	 */
	public function test_register_post_type_failed(): void
	{
		$plugin_context = new Plugin();

		$expected_error_message = 'Error message';
		$expected_error = Mockery::mock('alias:WP_Error');
		$expected_error->shouldReceive('get_error_message')->andReturn($expected_error_message);

		Functions\when('register_post_type')->justReturn($expected_error);
		Functions\stubs([
			'__',
			'_x',
		]);

		Functions\expect('_doing_it_wrong')->with(
			Wishlist_Post_Type::class . '::register_post_type',
			$expected_error_message,
			$plugin_context->version()
		)->andReturn();

		$wishlist_post_type = new Wishlist_Post_Type($plugin_context);
		$wishlist_post_type->register_post_type();

		$this->assertTrue(Actions\did('another_wishlist_post_type_registered') === 0);
	}
}
