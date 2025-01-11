<?php
declare(strict_types=1);

namespace Another\Plugin\Another_Wishlist\Post_Types;

use Another\Plugin\Another_Wishlist\Plugin;
use WP_Error;
use WP_Post_Type;
use function register_post_type;

if (!defined('WPINC')) {
	exit;
}

class Wishlist_Post_Type
{
	const POST_TYPE_NAME = 'wishlist';

	private Plugin $context;

	public static function hook(): void
	{
		$instance = new self();
		add_action('init', [$instance, 'register_post_type']);
	}

	public function __construct(Plugin $context = null)
	{
		if (is_null($context)) {
			$context = clone Plugin::instance();
		}

		$this->context = $context;
	}

	public function register_post_type(): void
	{
		$response = $this->register();
		if (is_wp_error($response)) {
			// TODO: implement logger to plugin
			_doing_it_wrong(__METHOD__, $response->get_error_message(), $this->context->version());

			return;
		}
		/**
		 * @var WP_Post_Type $response
		 */

		do_action('another_wishlist_post_type_registered', $response);
	}

	public function register(): WP_Error|WP_Post_Type
	{
		return register_post_type(
			self::POST_TYPE_NAME,
			array(
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'show_in_nav_menus' => false,
				'show_in_rest' => false,
				'menu_icon' => 'dashicons-archive',
				'capability_type' => 'product',
				'map_meta_cap' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
				'rewrite' => $this->rewrite(),
				'query_var' => true,
				'supports' => $this->supports(),
				'has_archive' => true,
				'description' => __('Another Wishlist Items.', $this->context->text_domain()),
				'labels' => $this->labels(),
			),
		);
	}

	private function supports(): array
	{
		return array('title', 'editor', 'excerpt', 'custom-fields', 'product');
	}

	private function rewrite(): array
	{
		return array(
			'slug' => 'wishlist',
			'with_front' => false,
			'feeds' => true,
		);
	}

	private function labels(): array
	{
		return array(
			'name' => __('Wishlists', $this->context->text_domain()),
			'singular_name' => __('Wishlist', $this->context->text_domain()),
			'all_items' => __('All wishlists', $this->context->text_domain()),
			'menu_name' => _x('Wishlists', 'Admin menu name', $this->context->text_domain()),
			'add_new' => __('Add new', $this->context->text_domain()),
			'add_new_item' => __('Add new wishlist', $this->context->text_domain()),
			'edit' => __('Edit', $this->context->text_domain()),
			'edit_item' => __('Edit wishlist', $this->context->text_domain()),
			'new_item' => __('New wishlist', $this->context->text_domain()),
			'view_item' => __('View wishlist', $this->context->text_domain()),
			'view_items' => __('View wishlists', $this->context->text_domain()),
			'search_items' => __('Search wishlists', $this->context->text_domain()),
			'not_found' => __('No wishlists found', $this->context->text_domain()),
			'not_found_in_trash' => __('No wishlists found in trash', $this->context->text_domain()),
			'parent' => __('Parent wishlist', $this->context->text_domain()),
			'featured_image' => __('Wishlist image', $this->context->text_domain()),
			'set_featured_image' => __('Set wishlist image', $this->context->text_domain()),
			'remove_featured_image' => __('Remove wishlist image', $this->context->text_domain()),
			'use_featured_image' => __('Use as wishlist image', $this->context->text_domain()),
			'insert_into_item' => __('Insert into wishlists', $this->context->text_domain()),
			'uploaded_to_this_item' => __('Uploaded to this wishlist', $this->context->text_domain()),
			'filter_items_list' => __('Filter wishlists', $this->context->text_domain()),
			'items_list_navigation' => __('Wishlists navigation', $this->context->text_domain()),
			'items_list' => __('Wishlists list', $this->context->text_domain()),
			'item_link' => __('Wishlist link', $this->context->text_domain()),
			'item_link_description' => __('A link to a wishlist.', $this->context->text_domain()),
		);
	}
}
