<?php

declare(strict_types = 1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit;

use Another\Plugin\Another_Wishlist\Plugin;

class Plugin_Test extends TestCase {



	public function set_up(): void {
		parent::set_up();
		Plugin::instance();
	}

	/**
	 * @covers \Another\Plugin\Another_Wishlist\Plugin::instance()
	 */
	public function test_plugin_instance(): void {
		$this->assertInstanceOf( Plugin::class, Plugin::instance() );
	}

	/**
	 * @covers \Another\Plugin\Another_Wishlist\Plugin::version()
	 */
	public function test_plugin_version(): void {
		$this->assertEquals( '1.0.0', Plugin::instance()->version() );
	}

	/**
	 * @covers \Another\Plugin\Another_Wishlist\Plugin::text_domain()
	 */
	public function test_plugin_text_domain(): void {
		$this->assertEquals( 'another-wishlist', Plugin::instance()->text_domain() );
	}

	/**
	 * @covers \Another\Plugin\Another_Wishlist\Plugin::plugin_name()
	 */
	public function test_plugin_name(): void {
		$this->assertEquals( 'Another Woo Wishlist', Plugin::instance()->plugin_name() );
	}

	/**
	 * @covers \Another\Plugin\Another_Wishlist\Plugin::plugin_file()
	 */
	public function test_plugin_file(): void {
		$this->assertTrue( str_ends_with( Plugin::instance()->plugin_file(), 'another-wishlist/includes/Plugin.php' ) );
	}

	/**
	 * @covers \Another\Plugin\Another_Wishlist\Plugin::init()
	 * @covers \Another\Plugin\Another_Wishlist\Plugin::initialized()
	 */
	public function test_initialized(): void {
		$this->assertFalse( Plugin::instance()->initialized() );
		Plugin::instance()->init();
		$this->assertTrue( Plugin::instance()->initialized() );
	}
}
