<?php
declare(strict_types=1);

namespace Another\Plugin\Another_Wishlist\Repositories;

use Another\Plugin\Another_Wishlist\Exceptions\Repository_Exception;
use Another\Plugin\Another_Wishlist\Models\Wishlist_Model;
use Another\Plugin\Another_Wishlist\Plugin;
use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;

if (!defined('WPINC')) {
	exit;
}

class Wishlist_Repository
{
	const OBJECT_IDS_META_KEY = '_object_ids';

	private Plugin $context;

	public function __construct(Plugin $context = null)
	{
		if (is_null($context)) {
			$context = clone Plugin::instance();
		}

		$this->context = $context;
	}

	/**
	 * @param Wishlist_Model $wishlist
	 * @param int $order
	 * @return int
	 * @throws Repository_Exception
	 */
	public function create_wishlist(Wishlist_Model $wishlist, int $order = 0): int
	{
		$comment_status = apply_filters('another_wishlist_create_comment_status', 'closed');
		$ping_status = apply_filters('another_wishlist_create_ping_status', 'closed');

		$result = wp_insert_post([
			'post_type' => Wishlist_Post_Type::POST_TYPE_NAME,
			'post_title' => $wishlist->title(),
			'post_name' => $wishlist->guid(),
			'post_author' => $wishlist->user_id(),
			'post_status' => $wishlist->visibility(),
			'comment_status' => $comment_status,
			'ping_status' => $ping_status,
			'post_order' => $order,
		]);

		if (is_wp_error($result)) {
			throw new Repository_Exception($result->get_error_message());
		}

		$wishlist_id = $result;

		// save object ids
		$meta_id = add_post_meta($wishlist_id, self::OBJECT_IDS_META_KEY, json_encode($wishlist->object_ids()), true);
		if (false === $meta_id) {
			throw new Repository_Exception('Failed to save object ids');
		}

		return $wishlist_id;
	}

	/**
	 * @param int|string $wishlist_id
	 * @return Wishlist_Model
	 * @throws Repository_Exception
	 */
	public function find_wishlist(int|string $wishlist_id): Wishlist_Model
	{
		if (wp_is_uuid($wishlist_id)) {
			$wishlist_post = get_page_by_path($wishlist_id, OBJECT, Wishlist_Post_Type::POST_TYPE_NAME);
		} elseif (is_numeric($wishlist_id)) {
			$wishlist_post = get_post($wishlist_id);
		} else {
			throw new Repository_Exception('Invalid wishlist id');
		}

		/**
		 * both get_page_by_path and get_post return null if post is not found
		 *
		 * @see https://developer.wordpress.org/reference/functions/get_page_by_path/
		 * @see https://developer.wordpress.org/reference/functions/get_post/
		 */
		if (is_null($wishlist_post)) {
			throw new Repository_Exception('Wishlist not found');
		}

		$object_ids = get_post_meta($wishlist_id, self::OBJECT_IDS_META_KEY, true);
		if (empty($object_ids)) {
			$object_ids = array();
		} else {
			$object_ids = json_decode($object_ids, true);
		}

		return new Wishlist_Model([
			'user_id' => $wishlist_post->post_author,
			'id' => $wishlist_post->ID,
			'guid' => $wishlist_post->post_name,
			'title' => $wishlist_post->post_title,
			'description' => $wishlist_post->post_content,
			'object_ids' => $object_ids,
			'visibility' => $wishlist_post->post_status,
		]);
	}

	public function next_order_increment(int $user_id): int
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT MAX(post_order) FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s",
			$user_id,
			Wishlist_Post_Type::POST_TYPE_NAME
		);

		$order = $wpdb->get_var($query);

		// if it doesn't exist, null is returned. This means it is very first one, so we can return zero
		if (is_null($order)) {
			return 0;
		}

		// if exists, we can return incremented value
		return intval($order) + 1;
	}
}
