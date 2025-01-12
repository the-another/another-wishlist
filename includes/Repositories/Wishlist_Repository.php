<?php
/**
 * Wishlist repository
 */

declare( strict_types = 1 );

namespace Another\Plugin\Another_Wishlist\Repositories;

use Another\Plugin\Another_Wishlist\Exceptions\Repository_Exception;
use Another\Plugin\Another_Wishlist\Models\Wishlist_Model;
use Another\Plugin\Another_Wishlist\Plugin;
use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;

if ( ! \defined( 'WPINC' ) ) {
	exit;
}

/**
 * Wishlist repository
 */
class Wishlist_Repository {

	public const string OBJECT_IDS_META_KEY = '_object_ids';
	public const string CACHE_GROUP         = 'another-wishlist';

	private Plugin $context;

	/**
	 * Wishlist_Repository constructor.
	 *
	 * @param Plugin|null $context Plugin context.
	 */
	public function __construct( ?Plugin $context = null ) {
		if ( \is_null( $context ) ) {
			$context = clone Plugin::instance();
		}

		$this->context = $context;
	}

	/**
	 * Create wishlist entry
	 *
	 * @param Wishlist_Model $wishlist Wishlist model.
	 * @param int            $order Order increment.
	 *
	 * @return int
	 * @throws Repository_Exception If failed to create wishlist.
	 */
	public function create_wishlist( Wishlist_Model $wishlist, int $order = 0 ): int {
		$comment_status = apply_filters( 'another_wishlist_create_comment_status', 'closed' );
		$ping_status    = apply_filters( 'another_wishlist_create_ping_status', 'closed' );

		$result = wp_insert_post(
			array(
				'post_type'      => Wishlist_Post_Type::POST_TYPE_NAME,
				'post_title'     => $wishlist->title(),
				'post_name'      => $wishlist->guid(),
				'post_author'    => $wishlist->user_id(),
				'post_status'    => $wishlist->visibility(),
				'comment_status' => $comment_status,
				'ping_status'    => $ping_status,
				'post_order'     => $order,
			)
		);

		if ( is_wp_error( $result ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new Repository_Exception( $result->get_error_message() );
		}

		$wishlist_id = $result;

		// save object ids.
		$meta_id = add_post_meta( $wishlist_id, self::OBJECT_IDS_META_KEY, wp_json_encode( $wishlist->object_ids() ), true );
		if ( false === $meta_id ) {
			throw new Repository_Exception( 'Failed to save object ids' );
		}

		$this->set_cache( $wishlist );

		return $wishlist_id;
	}

	/**
	 * Find wishlist by numeric ID or GUID
	 *
	 * @param int|string $wishlist_id Wishlist ID or GUID.
	 *
	 * @return Wishlist_Model
	 * @throws Repository_Exception If wishlist not found, or invalid ID.
	 */
	public function find_wishlist( int|string $wishlist_id ): Wishlist_Model {
		if ( wp_is_uuid( $wishlist_id ) ) {
			$wishlist_post = get_page_by_path( $wishlist_id, OBJECT, Wishlist_Post_Type::POST_TYPE_NAME );
		} elseif ( is_numeric( $wishlist_id ) ) {
			$wishlist_post = get_post( $wishlist_id );
		} else {
			throw new Repository_Exception( 'Invalid wishlist id' );
		}

		/**
		 * Both get_page_by_path and get_post return null if post is not found.
		 *
		 * @see https://developer.wordpress.org/reference/functions/get_page_by_path/
		 * @see https://developer.wordpress.org/reference/functions/get_post/
		 */
		if ( \is_null( $wishlist_post ) ) {
			throw new Repository_Exception( 'Wishlist not found' );
		}

		$object_ids = get_post_meta( $wishlist_id, self::OBJECT_IDS_META_KEY, true );
		if ( empty( $object_ids ) ) {
			$object_ids = array();
		} else {
			$object_ids = json_decode( $object_ids, true );
		}

		return new Wishlist_Model(
			array(
				'user_id'     => $wishlist_post->post_author,
				'id'          => $wishlist_post->ID,
				'guid'        => $wishlist_post->post_name,
				'title'       => $wishlist_post->post_title,
				'description' => $wishlist_post->post_content,
				'object_ids'  => $object_ids,
				'visibility'  => $wishlist_post->post_status,
			)
		);
	}

	/**
	 * Update wishlist.
	 *
	 * Returns true if update was made on post or post meta.
	 * Return false if no changes were made.
	 * Throws exception if ID, user ID or GUID are changed.
	 *
	 * @param Wishlist_Model $wishlist Wishlist model.
	 *
	 * @return bool
	 * @throws Repository_Exception If user ID cannot be changed.
	 */
	public function update_wishlist( Wishlist_Model $wishlist ): bool {
		$existing_wishlist = $this->find_wishlist( $wishlist->id() );
		// check if ID, user ID and GUID are the same, as those values are not allowed to be changed.
		if ( $existing_wishlist->id() !== $wishlist->id() ) {
			throw new Repository_Exception( 'ID cannot be changed' );
		}
		if ( $existing_wishlist->user_id() !== $wishlist->user_id() ) {
			throw new Repository_Exception( 'User ID cannot be changed' );
		}
		if ( $existing_wishlist->guid() !== $wishlist->guid() ) {
			throw new Repository_Exception( 'GUID cannot be changed' );
		}

		$update_post = false;
		if (
			$existing_wishlist->title() !== $wishlist->title() ||
			$existing_wishlist->description() !== $wishlist->description() ||
			$existing_wishlist->visibility() !== $wishlist->visibility()
		) {
			$update_post = true;
		}

		$update_meta = false;
		if ( $existing_wishlist->object_ids() !== $wishlist->object_ids() ) {
			$update_meta = true;
		}

		if ( ! $update_post && ! $update_meta ) {
			// nothing to update.
			return false;
		}

		if ( $update_post ) {
			$result = wp_update_post(
				array(
					'ID'           => $wishlist->id(),
					'post_title'   => $wishlist->title(),
					'post_content' => $wishlist->description(),
					'post_status'  => $wishlist->visibility(),
				),
				true,
				false
			);

			if ( is_wp_error( $result ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
				throw new Repository_Exception( $result->get_error_message() );
			}
		}

		if ( $update_meta ) {
			// save object ids.
			update_post_meta( $wishlist->id(), self::OBJECT_IDS_META_KEY, wp_json_encode( $wishlist->object_ids() ) );
		}

		$this->set_cache( $wishlist );

		return true;
	}

	/**
	 * Delete wishlist
	 *
	 * @param Wishlist_Model $wishlist Wishlist object.
	 *
	 * @return bool
	 * @throws Repository_Exception If user ID of existing wishlist doesn't match user ID in input model.
	 */
	public function delete_wishlist( Wishlist_Model $wishlist ): bool {
		$existing_wishlist = $this->find_wishlist( $wishlist->id() );
		// check if ID, user ID and GUID are the same, as those values are not allowed to be changed.
		if ( $existing_wishlist->user_id() !== $wishlist->user_id() ) {
			throw new Repository_Exception( 'User ID is different' );
		}

		$result = wp_trash_post( $wishlist->id() );

		if ( empty( $result ) ) {
			throw new Repository_Exception( 'Failed to delete wishlist' );
		}

		wp_cache_delete_multiple(
			array(
				sprintf( 'wishlist-%s', $wishlist->guid() ),
				sprintf( 'wishlist-id:%d', $wishlist->id() ),
			),
			self::CACHE_GROUP
		);

		return true;
	}

	/**
	 * Load wishlist by numeric ID or GUID from cache
	 *
	 * @throws Repository_Exception If invalid ID.
	 *
	 * @param int|string $wishlist_id Wishlist ID or GUID.
	 */
	public function load_wishlist( int|string $wishlist_id ): Wishlist_Model {
		if ( wp_is_uuid( $wishlist_id ) ) {
			$cache_key = sprintf( 'wishlist-%s', $wishlist_id );
		} elseif ( is_numeric( $wishlist_id ) ) {
			$cache_key = sprintf( 'wishlist-id:%d', $wishlist_id );
		} else {
			throw new Repository_Exception( 'Invalid wishlist id' );
		}

		// load wishlist object from cache.
		$wishlist_json = wp_cache_get( $cache_key, self::CACHE_GROUP );
		if ( false !== $wishlist_json ) {
			// if cache hit, return object.
			return new Wishlist_Model( json_decode( $wishlist_json, true ) );
		}

		$wishlist = $this->find_wishlist( $wishlist_id );
		$this->set_cache( $wishlist );

		return $wishlist;
	}

	/**
	 * Update wishlist
	 *
	 * @param int $user_id User ID.
	 *
	 * @return int
	 */
	public function next_order_increment( int $user_id ): int {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$order = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT MAX(post_order) FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s",
				$user_id,
				Wishlist_Post_Type::POST_TYPE_NAME
			)
		);

		// if it doesn't exist, null is returned. This means it is very first one, so we can return zero.
		if ( \is_null( $order ) ) {
			return 0;
		}

		// if exists, we can return incremented value.
		return \intval( $order ) + 1;
	}

	/**
	 * Set cache for wishlist
	 *
	 * @param Wishlist_Model $wishlist Wishlist model.
	 */
	public function set_cache( Wishlist_Model $wishlist ): void {
		wp_cache_set_multiple(
			array(
				sprintf( 'wishlist-%s', $wishlist->guid() )  => $wishlist->json(),
				sprintf( 'wishlist-id:%d', $wishlist->id() ) => $wishlist->json(),
			),
			self::CACHE_GROUP,
			$this->cache_ttl()
		);
	}

	/**
	 * Get cache expire time
	 *
	 * @return int
	 */
	public function cache_ttl(): int {
		return apply_filters( 'another_wishlist_cache_ttl', 60 * 60 * 24 ); // 1 day by default.
	}
}
