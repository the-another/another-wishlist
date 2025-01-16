<?php

declare( strict_types = 1 );

namespace Another\Plugin\Another_Wishlist\Tests\Unit\Models;

use Another\Plugin\Another_Wishlist\Models\Wishlist_Model;
use Another\Plugin\Another_Wishlist\Tests\Unit\Test_Case;
use Brain\Monkey\Functions;

class Wishlist_Test extends Test_Case {

	public function set_up(): void {
		parent::set_up(); // TODO: Change the autogenerated stub
		Functions\when( 'wp_generate_uuid4' )->justReturn( '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850' );
	}

	/**
	 * @return void
	 */
	public function test_user_id(): void {
		$wishlist = new Wishlist_Model( array( 'user_id' => 1 ) );
		$this->assertEquals( 1, $wishlist->user_id() );
	}

	/**
	 * @return void
	 */
	public function test_id(): void {
		$wishlist = new Wishlist_Model( array( 'id' => 1 ) );
		$this->assertEquals( 1, $wishlist->id() );
	}

	/**
	 * @return void
	 */
	public function test_guid(): void {
		$wishlist = new Wishlist_Model( array( 'guid' => '123' ) );
		$this->assertEquals( '123', $wishlist->guid() );
	}

	/**
	 * @return void
	 */
	public function test_title(): void {
		$wishlist = new Wishlist_Model( array( 'title' => 'test' ) );
		$this->assertEquals( 'test', $wishlist->title() );
	}

	/**
	 * @return void
	 */
	public function test_description(): void {
		$wishlist = new Wishlist_Model( array( 'description' => 'test' ) );
		$this->assertEquals( 'test', $wishlist->description() );
	}

	/**
	 * @return void
	 */
	public function test_object_ids(): void {
		$wishlist = new Wishlist_Model( array( 'object_ids' => array( 1, 2, 3 ) ) );
		$this->assertEquals( array( 1, 2, 3 ), $wishlist->object_ids() );
	}

	/**
	 * @return void
	 */
	public function test_object_ids_sort(): void {
		$wishlist = new Wishlist_Model( array( 'object_ids' => array( 3, 2, 1 ) ) );
		$this->assertEquals( array( 1, 2, 3 ), $wishlist->object_ids() );
	}

	/**
	 * @return void
	 */
	public function test_add_object_id(): void {
		$wishlist = new Wishlist_Model( array( 'object_ids' => array( 2, 1, 3 ) ) );
		$wishlist->add_object_id( 4 );
		$this->assertEquals( array( 1, 2, 3, 4 ), $wishlist->object_ids() );
	}

	/**
	 * @return void
	 */
	public function test_remove_object_id(): void {
		$wishlist = new Wishlist_Model( array( 'object_ids' => array( 3, 2, 1 ) ) );
		$wishlist->remove_object_id( 2 );
		$this->assertEquals( array( 1, 3 ), $wishlist->object_ids() );
	}

	/**
	 * @return void
	 */
	public function test_visibility(): void {
		$wishlist = new Wishlist_Model( array( 'visibility' => Wishlist_Model::VISIBILITY_PUBLIC ) );
		$this->assertEquals( Wishlist_Model::VISIBILITY_PUBLIC, $wishlist->visibility() );
	}

	/**
	 * @return void
	 */
	public function test_multiple_attributes(): void {
		$wishlist = new Wishlist_Model(
			array(
				'user_id'     => 1,
				'id'          => 2,
				'guid'        => '123',
				'title'       => 'test',
				'description' => 'test',
				'object_ids'  => array( 1, 2, 3 ),
				'visibility'  => Wishlist_Model::VISIBILITY_PUBLIC,
			)
		);

		$this->assertEquals( 1, $wishlist->user_id() );
		$this->assertEquals( 2, $wishlist->id() );
		$this->assertEquals( '123', $wishlist->guid() );
		$this->assertEquals( 'test', $wishlist->title() );
		$this->assertEquals( 'test', $wishlist->description() );
		$this->assertEquals( array( 1, 2, 3 ), $wishlist->object_ids() );
		$this->assertEquals( 'public', $wishlist->visibility() );
	}

	/**
	 * @return void
	 */
	public function test_export(): void {
		$wishlist = new Wishlist_Model(
			array(
				'user_id'     => 1,
				'id'          => 2,
				'guid'        => '123',
				'title'       => 'test',
				'description' => 'test',
				'object_ids'  => array( 1, 2, 3 ),
				'visibility'  => Wishlist_Model::VISIBILITY_PUBLIC,
			)
		);

		$exported = $wishlist->export();
		$this->assertEquals( $wishlist->user_id(), $exported['user_id'] );
		$this->assertEquals( $wishlist->id(), $exported['id'] );
		$this->assertEquals( $wishlist->guid(), $exported['guid'] );
		$this->assertEquals( $wishlist->title(), $exported['title'] );
		$this->assertEquals( $wishlist->description(), $exported['description'] );
		$this->assertEquals( $wishlist->object_ids(), $exported['object_ids'] );
		$this->assertEquals( $wishlist->visibility(), $exported['visibility'] );
	}

	/**
	 * @return void
	 */
	public function test_json(): void {
		$wishlist = new Wishlist_Model(
			array(
				'user_id'     => 1,
				'id'          => 2,
				'guid'        => '123',
				'title'       => 'test',
				'description' => 'test',
				'object_ids'  => array( 1, 2, 3 ),
				'visibility'  => Wishlist_Model::VISIBILITY_PUBLIC,
			)
		);

		$this->assertJsonStringEqualsJsonString(
			'{
				"user_id": 1,
				"id": 2,
				"guid": "123",
				"title": "test",
				"description": "test",
				"object_ids": [1, 2, 3],
				"visibility": "public"
			}',
			$wishlist->json()
		);
	}
}
