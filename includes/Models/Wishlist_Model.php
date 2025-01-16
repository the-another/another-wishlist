<?php
/**
 * Wishlist model
 */

declare( strict_types = 1 );

namespace Another\Plugin\Another_Wishlist\Models;

if ( ! \defined( 'WPINC' ) ) {
	exit;
}

/**
 * Class Wishlist_Model
 */
class Wishlist_Model {

	public const string VISIBILITY_PRIVATE = 'private';
	public const string VISIBILITY_PUBLIC  = 'public';

	/**
	 * Model attributes
	 *
	 * @var array<string, mixed> $attributes
	 */
	private array $attributes = array(
		'user_id'     => 0,
		'id'          => 0,
		'guid'        => '',
		'title'       => '',
		'description' => '',
		'object_ids'  => array(),
		'visibility'  => self::VISIBILITY_PRIVATE,
	);

	/**
	 * Wishlist_Model constructor
	 *
	 * @param array $data Input data.
	 */
	public function __construct( array $data = array() ) {
		$this->fill( $data );
		if ( empty( $this->attributes['guid'] ) ) {
			$this->attributes['guid'] = wp_generate_uuid4();
		}
	}

	/**
	 * Fill model attributes from input array
	 *
	 * @param array $data Input data.
	 */
	public function fill( array $data ): void {
		foreach ( $data as $key => $value ) {
			$key_setter_method = sprintf( 'set_%s', $key );
			if ( \array_key_exists( $key, $this->attributes ) && method_exists( $this, $key_setter_method ) ) {
				$this->$key_setter_method( $value );
			}
		}
	}

	/**
	 * Get user ID
	 *
	 * @return int
	 */
	public function user_id(): int {
		return $this->attributes['user_id'];
	}

	/**
	 * Get wishlist ID
	 *
	 * @return int
	 */
	public function id(): int {
		return $this->attributes['id'];
	}

	/**
	 * Get wishlist GUID
	 *
	 * @return string
	 */
	public function guid(): string {
		return $this->attributes['guid'];
	}

	/**
	 * Get wishlist title
	 *
	 * @return string
	 */
	public function title(): string {
		return $this->attributes['title'];
	}

	/**
	 * Get wishlist description
	 *
	 * @return string
	 */
	public function description(): string {
		return $this->attributes['description'];
	}

	/**
	 * Get object IDs
	 *
	 * @return int[]
	 */
	public function object_ids(): array {
		return $this->attributes['object_ids'];
	}

	/**
	 * Get wishlist visibility
	 *
	 * @return string
	 */
	public function visibility(): string {
		return $this->attributes['visibility'];
	}

	/**
	 * Set user ID
	 *
	 * @param int $id Wishlist ID.
	 */
	public function set_id( int $id ): void {
		$this->attributes['id'] = $id;
	}

	/**
	 * Get wishlist ID
	 *
	 * @deprecated Use Wishlist_Model::id() instead
	 */
	public function get_id(): int {
		trigger_deprecation( 'another-wishlist', '1.0.0', 'Wishlist_Model::get_id() is deprecated. Use Wishlist_Model::id() instead.' );

		return $this->id();
	}

	/**
	 * Set user ID
	 *
	 * @param int $user_id User ID.
	 */
	public function set_user_id( int $user_id ): void {
		$this->attributes['user_id'] = $user_id;
	}

	/**
	 * Get user ID
	 *
	 * @deprecated Use Wishlist_Model::user_id() instead
	 */
	public function get_user_id(): int {
		trigger_deprecation( 'another-wishlist', '1.0.0', 'Wishlist_Model::get_user_id() is deprecated. Use Wishlist_Model::user_id() instead.' );

		return $this->user_id();
	}

	/**
	 * Set wishlist GUID
	 *
	 * @param string $guid Wishlist GUID.
	 */
	public function set_guid( string $guid ): void {
		$this->attributes['guid'] = $guid;
	}

	/**
	 * Get wishlist GUID
	 *
	 * @deprecated Use Wishlist_Model::guid() instead
	 */
	public function get_guid(): string {
		trigger_deprecation( 'another-wishlist', '1.0.0', 'Wishlist_Model::get_guid() is deprecated. Use Wishlist_Model::guid() instead.' );

		return $this->guid();
	}

	/**
	 * Set wishlist title
	 *
	 * @param string $title Wishlist title.
	 */
	public function set_title( string $title ): void {
		$this->attributes['title'] = $title;
	}

	/**
	 * Get wishlist title
	 *
	 * @deprecated Use Wishlist_Model::title() instead
	 */
	public function get_title(): string {
		trigger_deprecation( 'another-wishlist', '1.0.0', 'Wishlist_Model::get_title() is deprecated. Use Wishlist_Model::title() instead.' );

		return $this->title();
	}

	/**
	 * Set wishlist description
	 *
	 * @param string $description Wishlist description.
	 */
	public function set_description( string $description ): void {
		$this->attributes['description'] = $description;
	}

	/**
	 * Get wishlist description
	 *
	 * @deprecated Use Wishlist_Model::description() instead
	 */
	public function get_description(): string {
		trigger_deprecation( 'another-wishlist', '1.0.0', 'Wishlist_Model::get_description() is deprecated. Use Wishlist_Model::description() instead.' );

		return $this->description();
	}

	/**
	 * Set object IDs
	 *
	 * @param int[] $object_ids Object IDs.
	 */
	public function set_object_ids( array $object_ids ): void {
		$this->attributes['object_ids'] = $object_ids;

		sort( $this->attributes['object_ids'] );
	}

	/**
	 * Get object IDs
	 *
	 * @deprecated Use Wishlist_Model::object_ids() instead
	 */
	public function get_object_ids(): array {
		trigger_deprecation( 'another-wishlist', '1.0.0', 'Wishlist_Model::get_object_ids() is deprecated. Use Wishlist_Model::object_ids() instead.' );

		return $this->object_ids();
	}

	/**
	 * Add object ID
	 *
	 * @param int $object_id Object ID.
	 */
	public function add_object_id( int $object_id ): void {
		$this->attributes['object_ids'][] = $object_id;

		sort( $this->attributes['object_ids'] );
	}

	/**
	 * Remove object ID
	 *
	 * @param int $object_id Object ID.
	 */
	public function remove_object_id( int $object_id ): void {
		$index = array_search( $object_id, $this->attributes['object_ids'], true );
		if ( false !== $index ) {
			unset( $this->attributes['object_ids'][ $index ] );

			sort( $this->attributes['object_ids'] );
		}
	}

	/**
	 * Set wishlist visibility
	 *
	 * @param string $visibility Wishlist visibility.
	 */
	public function set_visibility( string $visibility ): void {
		$this->attributes['visibility'] = $visibility;
	}

	/**
	 * Get wishlist visibility
	 *
	 * @deprecated Use Wishlist_Model::visibility() instead
	 */
	public function get_visibility(): string {
		trigger_deprecation( 'another-wishlist', '1.0.0', 'Wishlist_Model::get_visibility() is deprecated. Use Wishlist_Model::visibility() instead.' );

		return $this->visibility();
	}

	/**
	 * Export model attributes
	 *
	 * @return array<string, mixed>
	 */
	public function export(): array {
		return $this->attributes;
	}

	/**
	 * Export model attributes as JSON
	 *
	 * @return string
	 */
	public function json(): string {
		return wp_json_encode( $this->export() );
	}
}
