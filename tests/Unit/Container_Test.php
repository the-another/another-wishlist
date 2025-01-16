<?php
/**
 * Container tests
 */

declare(strict_types = 1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit;

use Another\Plugin\Another_Wishlist\Container;
use Another\Plugin\Another_Wishlist\Plugin;
use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;

class Container_Test extends Test_Case {
	public function test_new_instance(): void {
		$plugin    = Plugin::instance();
		$container = new Container(
			array(
				Wishlist_Post_Type::class => new Wishlist_Post_Type( $plugin ),
			)
		);
		$this->assertInstanceOf( Wishlist_Post_Type::class, $container->get( Wishlist_Post_Type::class ) );
	}

	public function test_register_service(): void {
		$plugin    = Plugin::instance();
		$container = new Container();
		$container->register( Wishlist_Post_Type::class, new Wishlist_Post_Type( $plugin ) );
		$this->assertInstanceOf( Wishlist_Post_Type::class, $container->get( Wishlist_Post_Type::class ) );
	}

	public function test_get_instance(): void {
		$plugin    = Plugin::instance();
		$container = new Container();
		$container->register( Wishlist_Post_Type::class, new Wishlist_Post_Type( $plugin ) );
		$this->assertInstanceOf( Container::class, $container );
	}

	public function test_get_service(): void {
		$plugin    = Plugin::instance();
		$container = new Container();
		$container->register( Wishlist_Post_Type::class, new Wishlist_Post_Type( $plugin ) );
		$this->assertInstanceOf( Wishlist_Post_Type::class, $container->get( Wishlist_Post_Type::class ) );
	}
}
