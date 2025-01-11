<?php
declare(strict_types=1);

namespace Another\Plugin\Another_Wishlist\Models;

if (!defined('WPINC')) {
	exit;
}

class Wishlist_Model
{
	const VISIBILITY_PRIVATE = 'private';
	const VISIBILITY_PUBLIC = 'public';

	private array $attributes = [
		'user_id' => 0,
		'id' => 0,
		'guid' => '',
		'title' => '',
		'description' => '',
		'object_ids' => [],
		'visibility' => self::VISIBILITY_PRIVATE,
	];

	public function __construct(array $data = array())
	{
		$this->fill($data);
		if (empty($this->attributes['guid'])) {
			$this->attributes['guid'] = wp_generate_uuid4();
		}
	}

	public function fill(array $data): void
	{
		foreach ($data as $key => $value) {
			$key_setter_method = sprintf('set_%s', $key);
			if (array_key_exists($key, $this->attributes) && method_exists($this, $key_setter_method)) {
				$this->$key_setter_method($value);
			}
		}
	}

	public function user_id(): int
	{
		return $this->attributes['user_id'];
	}

	public function id(): int
	{
		return $this->attributes['id'];
	}

	public function guid(): string
	{
		return $this->attributes['guid'];
	}

	public function title(): string
	{
		return $this->attributes['title'];
	}

	public function description(): string
	{
		return $this->attributes['description'];
	}

	public function object_ids(): array
	{
		return $this->attributes['object_ids'];
	}

	public function visibility(): string
	{
		return $this->attributes['visibility'];
	}

	public function set_id(int $id): void
	{
		$this->attributes['id'] = $id;
	}

	/**
	 * @return int
	 *
	 * @deprecated Use Wishlist_Model::id() instead
	 */
	public function get_id(): int
	{
		trigger_deprecation('another-wishlist', '1.0.0', 'Wishlist_Model::get_id() is deprecated. Use Wishlist_Model::id() instead.');
		return $this->id();
	}

	public function set_user_id(int $user_id): void
	{
		$this->attributes['user_id'] = $user_id;
	}

	/**
	 * @return int
	 *
	 * @deprecated Use Wishlist_Model::user_id() instead
	 */
	public function get_user_id(): int
	{
		trigger_deprecation('another-wishlist', '1.0.0', 'Wishlist_Model::get_user_id() is deprecated. Use Wishlist_Model::user_id() instead.');
		return $this->user_id();
	}

	public function set_guid(string $guid): void
	{
		$this->attributes['guid'] = $guid;
	}

	/**
	 * @return string
	 *
	 * @deprecated Use Wishlist_Model::guid() instead
	 */
	public function get_guid(): string
	{
		trigger_deprecation('another-wishlist', '1.0.0', 'Wishlist_Model::get_guid() is deprecated. Use Wishlist_Model::guid() instead.');
		return $this->guid();
	}

	public function set_title(string $title): void
	{
		$this->attributes['title'] = $title;
	}

	/**
	 * @return string
	 *
	 * @deprecated Use Wishlist_Model::title() instead
	 */
	public function get_title(): string
	{
		trigger_deprecation('another-wishlist', '1.0.0', 'Wishlist_Model::get_title() is deprecated. Use Wishlist_Model::title() instead.');
		return $this->title();
	}

	public function set_description(string $description): void
	{
		$this->attributes['description'] = $description;
	}

	/**
	 * @return string
	 *
	 * @deprecated Use Wishlist_Model::description() instead
	 */
	public function get_description(): string
	{
		trigger_deprecation('another-wishlist', '1.0.0', 'Wishlist_Model::get_description() is deprecated. Use Wishlist_Model::description() instead.');
		return $this->description();
	}

	public function set_object_ids(array $object_ids): void
	{
		$this->attributes['object_ids'] = $object_ids;
	}

	/**
	 * @return array
	 *
	 * @deprecated Use Wishlist_Model::object_ids() instead
	 */
	public function get_object_ids(): array
	{
		trigger_deprecation('another-wishlist', '1.0.0', 'Wishlist_Model::get_object_ids() is deprecated. Use Wishlist_Model::object_ids() instead.');
		return $this->object_ids();
	}

	public function set_visibility(string $visibility): void
	{
		$this->attributes['visibility'] = $visibility;
	}

	/**
	 * @return string
	 *
	 * @deprecated Use Wishlist_Model::visibility() instead
	 */
	public function get_visibility(): string {
		trigger_deprecation('another-wishlist', '1.0.0', 'Wishlist_Model::get_visibility() is deprecated. Use Wishlist_Model::visibility() instead.');
		return $this->visibility();
	}
}
