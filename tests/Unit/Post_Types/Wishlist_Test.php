<?php

declare(strict_types = 1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit\Post_Types;

use Another\Plugin\Another_Wishlist\Plugin;
use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;
use Another\Plugin\Another_Wishlist\Tests\Unit\Test_Case;
use Brain\Monkey\Actions;
use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use Mockery;

final class Wishlist_Test extends Test_Case {

	/**
	 * @return void
	 */
	public function test_register_post_type_success(): void {
		$expected_post_type       = Mockery::mock( 'alias:WP_Post_Type' );
		$expected_post_type->name = 'wishlist';

		Functions\when( 'register_post_type' )->justReturn( $expected_post_type );
		Functions\stubs(
			array(
				'__',
				'_x',
			)
		);

		$plugin_context = Plugin::instance();

		$wishlist_post_type = new Wishlist_Post_Type( $plugin_context );
		$post_type          = $wishlist_post_type->register_post_type();

		$this->assertEquals( 'wishlist', $post_type->name );
	}

	/**
	 * @return void
	 */
	public function test_register_post_type_failure(): void {
		$expected_error = Mockery::mock( 'alias:WP_Error' );
		$expected_error->shouldReceive( 'get_error_message' )->andReturn( 'Error message' );

		Functions\when( 'register_post_type' )->justReturn( $expected_error );
		Functions\stubs(
			array(
				'__',
				'_x',
			)
		);

		$plugin_context = Plugin::instance();

		$wishlist_post_type = new Wishlist_Post_Type( $plugin_context );
		$post_type          = $wishlist_post_type->register_post_type();

		$this->assertInstanceOf( 'WP_Error', $post_type );
	}

	/**
	 * @return void
	 */
	public function test_register_success(): void {
		$plugin_context = Plugin::instance();

		$expected_post_type       = Mockery::mock( 'alias:WP_Post_Type' );
		$expected_post_type->name = 'wishlist';

		Functions\when( 'register_post_type' )->justReturn( $expected_post_type );
		Functions\stubs(
			array(
				'__',
				'_x',
			)
		);

		$wishlist_post_type = new Wishlist_Post_Type( $plugin_context );
		$wishlist_post_type->register();

		$this->assertTrue( Actions\did( 'another_wishlist_post_type_registered' ) === 1 );
	}

	/**
	 * @throws ExpectationArgsRequired
	 *
	 * @return void
	 */
	public function test_register_failed(): void {
		$plugin_context = Plugin::instance();

		$expected_error_message = 'Error message';
		$expected_error         = Mockery::mock( 'alias:WP_Error' );
		$expected_error->shouldReceive( 'get_error_message' )->andReturn( $expected_error_message );

		Functions\when( 'register_post_type' )->justReturn( $expected_error );
		Functions\stubs(
			array(
				'__',
				'_x',
				'esc_html',
			)
		);

		Functions\expect( '_doing_it_wrong' )->with(
			Wishlist_Post_Type::class . '::register_post_type',
			$expected_error_message,
			$plugin_context->version()
		)->andReturn();

		$wishlist_post_type = new Wishlist_Post_Type( $plugin_context );
		$wishlist_post_type->register();

		$this->assertTrue( Actions\did( 'another_wishlist_post_type_registered' ) === 0 );
	}

	/**
	 * @return void
	 */
	public function test_supports(): void {
		$plugin_context     = Plugin::instance();
		$wishlist_post_type = new Wishlist_Post_Type( $plugin_context );

		$this->assertEquals( array( 'title', 'author', 'comments' ), $wishlist_post_type->supports() );
	}

	/**
	 * @return void
	 */
	public function test_rewrite(): void {
		$plugin_context     = Plugin::instance();
		$wishlist_post_type = new Wishlist_Post_Type( $plugin_context );

		$rewrite = $wishlist_post_type->rewrite();
		$this->assertTrue( isset( $rewrite['slug'] ) );
		$this->assertEquals( 'wishlist', $rewrite['slug'] );
		$this->assertTrue( isset( $rewrite['with_front'] ) );
		$this->assertFalse( $rewrite['with_front'] );
	}

	/**
	 * @return void
	 */
	public function test_labels(): void {
		$plugin_context     = Plugin::instance();
		$wishlist_post_type = new Wishlist_Post_Type( $plugin_context );

		Functions\stubs(
			array(
				'__',
				'_x',
			)
		);

		$labels = $wishlist_post_type->labels();
		$this->assertTrue( isset( $labels['name'] ) );
		$this->assertTrue( isset( $labels['singular_name'] ) );
		$this->assertTrue( isset( $labels['menu_name'] ) );
	}
}
