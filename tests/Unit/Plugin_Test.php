<?php
/**
 * Plugin test case
 */

declare( strict_types = 1 );

namespace Another\Plugin\Another_Wishlist\Tests\Unit;

use Another\Plugin\Another_Wishlist\Plugin;
use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;
use Brain\Monkey\Actions;

class Plugin_Test extends Test_Case {
	public function test_set_params(): void {
		Plugin::instance()->set_params(
			array(
				'version' => '2.0.0',
			)
		);
		$this->assertEquals( '2.0.0', Plugin::instance()->version() );
	}

	public function test_container(): void {
		$this->assertInstanceOf( Wishlist_Post_Type::class, Plugin::instance()->container()->get( Wishlist_Post_Type::class ) );
	}

	public function test_plugin_instance(): void {
		$this->assertInstanceOf( Plugin::class, Plugin::instance() );
	}

	public function test_plugin_version(): void {
		$this->assertEquals( '1.0.0', Plugin::instance()->version() );
	}

	public function test_plugin_text_domain(): void {
		$this->assertEquals( 'another-wishlist', Plugin::instance()->text_domain() );
	}

	public function test_plugin_name(): void {
		$this->assertEquals( 'Another Woo Wishlist', Plugin::instance()->plugin_name() );
	}

	public function test_plugin_file(): void {
		$this->assertEquals( 'another-wishlist.php', Plugin::instance()->plugin_file() );
	}

	public function test_plugin_path(): void {
		$this->assertEquals( 'wp-content/plugins/another-wishlist/', Plugin::instance()->plugin_path() );
	}

	public function test_plugin_url(): void {
		$this->assertEquals( 'https://github.com/the-another/another-wishlist/', Plugin::instance()->plugin_url() );
	}

	public function test_init_global(): void {
		Actions\expectAdded( 'init' )
			->atLeast()
			->once();

		Plugin::instance()->init_global();
	}

	public function test_init_frontend(): void {
		Actions\expectAdded( 'wp_enqueue_scripts' )
			->atLeast()
			->once();


		Plugin::instance()->init_frontend();
	}

	public function test_initialized(): void {
		$this->assertFalse( Plugin::instance()->initialized() );
		Plugin::instance()->init();
		$this->assertTrue( Plugin::instance()->initialized() );
	}
}
