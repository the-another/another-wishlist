<?php

declare(strict_types = 1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit\Repositories;

use Another\Plugin\Another_Wishlist\Exceptions\Repository_Exception;
use Another\Plugin\Another_Wishlist\Models\Wishlist_Model;
use Another\Plugin\Another_Wishlist\Plugin;
use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;
use Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository;
use Another\Plugin\Another_Wishlist\Tests\Unit\TestCase;
use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use Mockery;

class Wishlist_Test extends TestCase {

	public function set_up(): void {
		global $wpdb;
		parent::set_up();

		$wpdb           = Mockery::mock( 'wpdb' );
		$wpdb->prefix   = 'wp_';
		$wpdb->posts    = $wpdb->prefix . 'posts';
		$wpdb->postmeta = $wpdb->prefix . 'postmeta';

		Functions\when( 'wp_generate_uuid4' )->justReturn( '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850' );
	}

	/**
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::next_order_increment()
	 */
	public function test_next_order_first(): void {
		global $wpdb;
		$plugin  = new Plugin();
		$repo    = new Wishlist_Repository( $plugin );
		$user_id = 1;

		$wpdb->shouldReceive( 'prepare' )
			->with(
				Mockery::type( 'string' ),
				$user_id,
				Wishlist_Post_Type::POST_TYPE_NAME
			)
			->once()
			->andReturn( "SELECT MAX(post_order) FROM {$wpdb->posts} WHERE post_author = 1 AND post_type = 'wishlist'" );

		$wpdb->shouldReceive( 'get_var' )
			->with(
				Mockery::on(
					static fn( $sql ) => "SELECT MAX(post_order) FROM {$wpdb->posts} WHERE post_author = 1 AND post_type = 'wishlist'" === $sql
				)
			)
			->once()
			->andReturn( null );

		$next_order = $repo->next_order_increment( $user_id );
		$this->assertEquals( 0, $next_order );
	}

	/**
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::next_order_increment()
	 */
	public function test_next_order_up(): void {
		global $wpdb;
		$plugin  = new Plugin();
		$repo    = new Wishlist_Repository( $plugin );
		$user_id = 1;

		$wpdb->shouldReceive( 'prepare' )
			->with(
				Mockery::type( 'string' ),
				$user_id,
				Wishlist_Post_Type::POST_TYPE_NAME
			)
			->once()
			->andReturn( "SELECT MAX(post_order) FROM {$wpdb->posts} WHERE post_author = 1 AND post_type = 'wishlist'" );

		$wpdb->shouldReceive( 'get_var' )
			->with(
				Mockery::on(
					static fn( $sql ) => "SELECT MAX(post_order) FROM {$wpdb->posts} WHERE post_author = 1 AND post_type = 'wishlist'" === $sql
				)
			)
			->once()
			->andReturn( 3 );

		$next_order = $repo->next_order_increment( $user_id );
		$this->assertEquals( 4, $next_order );
	}

	/**
	 * @throws ExpectationArgsRequired
	 * @throws Repository_Exception
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::create_wishlist()
	 */
	public function test_create_wishlist(): void {
		global $wpdb;

		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		$wishlist = new Wishlist_Model(
			array(
				'title'      => 'Test Wishlist',
				'user_id'    => 1,
				'object_ids' => array( 1, 2, 3 ),
			)
		);

		Functions\expect( 'wp_insert_post' )
			->once()
			->with(
				Mockery::on(
					static fn( $args ) => $args['post_type'] === Wishlist_Post_Type::POST_TYPE_NAME
						&& $args['post_title'] === $wishlist->title()
						&& $args['post_name'] === '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850'
						&& $args['post_author'] === $wishlist->user_id()
						&& $args['post_status'] === $wishlist->visibility()
				)
			)->andReturn( 1 );
		Functions\expect( 'add_post_meta' )
			->once()
			->with( 1, Wishlist_Repository::OBJECT_IDS_META_KEY, json_encode( $wishlist->object_ids() ), true )
			->andReturn( 1 );

		$wishlist_id = $repo->create_wishlist( $wishlist );
		$this->assertEquals( 1, $wishlist_id );
	}

	/**
	 * @throws ExpectationArgsRequired
	 * @throws Repository_Exception
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::create_wishlist()
	 */
	public function test_create_wishlist_failure(): void {
		global $wpdb;

		$error = Mockery::mock( 'alias:WP_Error' );
		$error->shouldReceive( 'get_error_message' )->andReturn( 'Failed to create wishlist' );

		Functions\expect( 'is_wp_error' )->andReturn( true );

		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		$wishlist = new Wishlist_Model(
			array(
				'title'      => 'Test Wishlist',
				'user_id'    => 1,
				'object_ids' => array( 1, 2, 3 ),
			)
		);

		Functions\expect( 'wp_insert_post' )
			->once()
			->with(
				Mockery::on(
					static fn( $args ) => Wishlist_Post_Type::POST_TYPE_NAME === $args['post_type']
						&& $args['post_title'] === $wishlist->title()
						&& '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850' === $args['post_name']
						&& $args['post_author'] === $wishlist->user_id()
						&& $args['post_status'] === $wishlist->visibility()
				)
			)->andReturn( $error );

		$this->expectException( Repository_Exception::class );
		$this->expectExceptionMessage( 'Failed to create wishlist' );

		$repo->create_wishlist( $wishlist );
	}

	/**
	 * @throws ExpectationArgsRequired
	 * @throws Repository_Exception
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::create_wishlist()
	 */
	public function test_create_wishlist_failure_meta(): void {
		global $wpdb;

		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		$wishlist = new Wishlist_Model(
			array(
				'title'      => 'Test Wishlist',
				'user_id'    => 1,
				'object_ids' => array( 1, 2, 3 ),
			)
		);

		Functions\expect( 'wp_insert_post' )
			->once()
			->with(
				Mockery::on(
					static fn( $args ) => Wishlist_Post_Type::POST_TYPE_NAME === $args['post_type']
						&& $args['post_title'] === $wishlist->title()
						&& '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850' === $args['post_name']
						&& $args['post_author'] === $wishlist->user_id()
						&& $args['post_status'] === $wishlist->visibility()
				)
			)->andReturn( 1 );

		Functions\expect( 'add_post_meta' )
			->once()
			->with( 1, Wishlist_Repository::OBJECT_IDS_META_KEY, json_encode( $wishlist->object_ids() ), true )
			->andReturn( false );

		$this->expectException( Repository_Exception::class );
		$this->expectExceptionMessage( 'Failed to save object ids' );

		$repo->create_wishlist( $wishlist );
	}

	/**
	 * @throws Repository_Exception
	 * @throws ExpectationArgsRequired
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::find_wishlist()
	 */
	public function test_find_wishlist_by_numeric_id(): void {
		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		$wishlist_post               = Mockery::mock( 'WP_Post' );
		$wishlist_post->ID           = 1;
		$wishlist_post->post_author  = 1;
		$wishlist_post->post_title   = 'Test Wishlist';
		$wishlist_post->post_name    = '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850';
		$wishlist_post->post_status  = Wishlist_Model::VISIBILITY_PRIVATE;
		$wishlist_post->post_content = '';


		Functions\expect( 'get_post' )->with( 1 )->andReturn( $wishlist_post );

		Functions\expect( 'get_post_meta' )
			->with( 1, Wishlist_Repository::OBJECT_IDS_META_KEY, true )
			->once()
			->andReturn( json_encode( array( 1, 2, 3 ) ) );

		Functions\expect( 'wp_is_uuid' )->andReturn( false );

		$wishlist = $repo->find_wishlist( 1 );
		$this->assertEquals( 1, $wishlist->id() );
		$this->assertEquals( 'Test Wishlist', $wishlist->title() );
		$this->assertEquals( '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850', $wishlist->guid() );
		$this->assertEquals( 1, $wishlist->user_id() );
		$this->assertEquals( Wishlist_Model::VISIBILITY_PRIVATE, $wishlist->visibility() );
		$this->assertEquals( array( 1, 2, 3 ), $wishlist->object_ids() );
	}

	/**
	 * @throws ExpectationArgsRequired
	 * @throws Repository_Exception
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::find_wishlist()
	 */
	public function test_find_wishlist_by_uuid(): void {
		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		$wishlist_post               = Mockery::mock( 'WP_Post' );
		$wishlist_post->ID           = 1;
		$wishlist_post->post_author  = 1;
		$wishlist_post->post_title   = 'Test Wishlist';
		$wishlist_post->post_name    = '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850';
		$wishlist_post->post_status  = Wishlist_Model::VISIBILITY_PRIVATE;
		$wishlist_post->post_content = '';

		Functions\expect( 'get_page_by_path' )
			->with(
				'4d59e1ac-0e1b-4d20-94b6-2dbfa8159850',
				OBJECT,
				Wishlist_Post_Type::POST_TYPE_NAME
			)
			->andReturn( $wishlist_post );

		Functions\expect( 'get_post_meta' )
			->with( 1, Wishlist_Repository::OBJECT_IDS_META_KEY, true )
			->once()
			->andReturn( json_encode( array( 1, 2, 3 ) ) );

		Functions\expect( 'wp_is_uuid' )->andReturn( true );

		$wishlist = $repo->find_wishlist( '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850' );
		$this->assertEquals( 1, $wishlist->id() );
		$this->assertEquals( 'Test Wishlist', $wishlist->title() );
		$this->assertEquals( '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850', $wishlist->guid() );
		$this->assertEquals( 1, $wishlist->user_id() );
		$this->assertEquals( Wishlist_Model::VISIBILITY_PRIVATE, $wishlist->visibility() );
		$this->assertEquals( array( 1, 2, 3 ), $wishlist->object_ids() );
	}

	/**
	 * @throws Repository_Exception
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::find_wishlist()
	 */
	public function test_find_wishlist_by_numeric_id_failure(): void {
		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		Functions\expect( 'get_post' )->andReturn( null );
		Functions\expect( 'wp_is_uuid' )->andReturn( false );

		$this->expectException( Repository_Exception::class );
		$this->expectExceptionMessage( 'Wishlist not found' );

		$repo->find_wishlist( 1 );
	}

	/**
	 * @throws Repository_Exception
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::find_wishlist()
	 */
	public function test_find_wishlist_by_uuid_failure(): void {
		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		Functions\expect( 'get_page_by_path' )->andReturn( null );
		Functions\expect( 'wp_is_uuid' )->andReturn( true );

		$this->expectException( Repository_Exception::class );
		$this->expectExceptionMessage( 'Wishlist not found' );

		$repo->find_wishlist( '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850' );
	}

	/**
	 * @throws Repository_Exception
	 *
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::find_wishlist()
	 */
	public function test_find_wishlist_invalid_id(): void {
		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		Functions\expect( 'wp_is_uuid' )->andReturn( false );

		$this->expectException( Repository_Exception::class );
		$this->expectExceptionMessage( 'Invalid wishlist id' );

		// this can be anything but numeric or uuid, it should fail
		$repo->find_wishlist( 'invalid' );
	}

	/**
	 * @throws ExpectationArgsRequired
	 * @throws Repository_Exception
	 * @covers \Another\Plugin\Another_Wishlist\Repositories\Wishlist_Repository::find_wishlist()
	 */
	public function test_find_wishlist_empty_objects(): void {
		$plugin = new Plugin();
		$repo   = new Wishlist_Repository( $plugin );

		$wishlist_post               = Mockery::mock( 'WP_Post' );
		$wishlist_post->ID           = 1;
		$wishlist_post->post_author  = 1;
		$wishlist_post->post_title   = 'Test Wishlist';
		$wishlist_post->post_name    = '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850';
		$wishlist_post->post_status  = Wishlist_Model::VISIBILITY_PRIVATE;
		$wishlist_post->post_content = '';

		Functions\expect( 'get_post' )->with( 1 )->andReturn( $wishlist_post );

		Functions\expect( 'get_post_meta' )
			->with( 1, Wishlist_Repository::OBJECT_IDS_META_KEY, true )
			->once()
			->andReturn( '' );

		Functions\expect( 'wp_is_uuid' )->andReturn( false );

		$wishlist = $repo->find_wishlist( 1 );
		$this->assertEquals( 1, $wishlist->id() );
		$this->assertEquals( 'Test Wishlist', $wishlist->title() );
		$this->assertEquals( '4d59e1ac-0e1b-4d20-94b6-2dbfa8159850', $wishlist->guid() );
		$this->assertEquals( 1, $wishlist->user_id() );
		$this->assertEquals( Wishlist_Model::VISIBILITY_PRIVATE, $wishlist->visibility() );
		$this->assertEquals( array(), $wishlist->object_ids() );
	}
}
