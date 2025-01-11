<?php
declare(strict_types=1);

namespace Another\Plugin\Another_Wishlist;

use Another\Plugin\Another_Wishlist\Post_Types\Wishlist_Post_Type;

if (!defined('WPINC')) {
	exit;
}

final class Plugin
{
	public static self $instance;

	private string $version = '1.0.0';
	private string $text_domain = 'another-wishlist';
	private string $plugin_name = 'Another Woo Wishlist';
	private string $plugin_file = __FILE__;

	private array $post_types = [
		'wishlist' => Wishlist_Post_Type::class,
	];

	private bool $initialized = false;

	public static function instance(): self
	{
		if (empty(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function version(): string
	{
		return $this->version;
	}

	public function text_domain(): string
	{
		return $this->text_domain;
	}

	public function plugin_name(): string
	{
		return $this->plugin_name;
	}

	public function plugin_file(): string
	{
		return $this->plugin_file;
	}

	public function plugin_dir(): string
	{
		return plugin_dir_path($this->plugin_file);
	}

	public function initialized(): bool
	{
		return $this->initialized;
	}

	public function init(): void
	{
		if ($this->initialized) {
			return;
		}

		foreach ($this->post_types as $post_type) {
			$post_type::hook();
		}

		$this->initialized = true;
	}
}
