<?php

declare(strict_types = 1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit;

use Another\Plugin\Another_Wishlist\Plugin;
use Brain\Monkey;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

/**
 * TestCase base class.
 */
abstract class TestCase extends PHPUnit_Framework_TestCase {

	// Adds Mockery expectations to the PHPUnit assertions count.
	use MockeryPHPUnitIntegration;

	/**
	 * Sets up the test fixtures.
	 */
	public function set_up(): void {
		Monkey\setUp();

		$this->stubTranslationFunctions();
		$this->stubEscapeFunctions();

		Plugin::instance(
			array(
				'version'     => '1.0.0',
				'text_domain' => 'another-wishlist',
				'plugin_name' => 'Another Woo Wishlist',
				'plugin_file' => 'another-wishlist.php',
				'plugin_path' => 'wp-content/plugins/another-wishlist/',
				'plugin_url'  => 'https://github.com/the-another/another-wishlist/',
			)
		);

		Monkey\Functions\stubs(
			array(
				// Passing "null" makes the function return it's first argument.
				'get_bloginfo'        => static function ( $show ) {
					switch ( $show ) {
						case 'charset':
							return 'UTF-8';
						case 'language':
							return 'English';
					}

					return $show;
				},
				'is_multisite'        => static function () {
					if ( \defined( 'WP_TESTS_MULTISITE' ) ) {
						return (bool) \WP_TESTS_MULTISITE;
					}

					return false;
				},
				'mysql2date'          => static fn( $format, $date ) => $date,
				'number_format_i18n'  => null,
				'sanitize_text_field' => null,
				'site_url'            => 'https://www.example.org',
				'wp_kses_post'        => null,
				'wp_parse_args'       => static fn( $args, $defaults ) => \array_merge( $defaults, $args ),
				'wp_strip_all_tags'   => static function ( $text, $remove_breaks = false ) {
					$text = \preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );
					$text = \strip_tags( $text );

					if ( $remove_breaks ) {
						$text = \preg_replace( '/[\r\n\t ]+/', ' ', $text );
					}

					return \trim( $text );
				},
				'wp_slash'            => null,
				'wp_unslash'          => static fn( $value ) => \is_string( $value ) ? \stripslashes( $value ) : $value,
				'is_admin'            => false,
			)
		);
	}

	public function setUp(): void {
		parent::setUp();
		$this->set_up();
	}

	public function tear_down(): void {
		Plugin::instance()->set_params(
			array(
				'version'     => '1.0.0',
				'text_domain' => 'another-wishlist',
				'plugin_name' => 'Another Woo Wishlist',
				'plugin_file' => 'another-wishlist.php',
				'plugin_path' => 'wp-content/plugins/another-wishlist/',
				'plugin_url'  => 'https://github.com/the-another/another-wishlist/',
			)
		);

		Mockery::close();
		Monkey\tearDown();
	}

	public function tearDown(): void {
		$this->tear_down();
		parent::tearDown();
	}

	/**
	 * Stub the WP native escaping functions.
	 *
	 * The stubs created by this function return the original input string unchanged.
	 *
	 * Alternative to the BrainMonkey `Monkey\Functions\stubTranslationFunctions()` function
	 * which does apply some form of escaping to the input if the function called is a
	 * "translate and escape" function.
	 *
	 * @return void
	 */
	public function stubTranslationFunctions(): void {
		Monkey\Functions\stubs(
			array(
				'__'         => null,
				'_x'         => null,
				'_n'         => static fn( $single, $plural, $number ) => $number === 1 ? $single : $plural,
				'_nx'        => static fn( $single, $plural, $number ) => $number === 1 ? $single : $plural,
				'translate'  => null,
				'esc_html__' => null,
				'esc_html_x' => null,
				'esc_attr__' => null,
				'esc_attr_x' => null,
			)
		);

		Monkey\Functions\when( '_e' )->echoArg();
		Monkey\Functions\when( '_ex' )->echoArg();
		Monkey\Functions\when( 'esc_html_e' )->echoArg();
		Monkey\Functions\when( 'esc_attr_e' )->echoArg();
	}

	/**
	 * Stub the WP native escaping functions.
	 *
	 * The stubs created by this function return the original input string unchanged.
	 *
	 * Alternative to the BrainMonkey `Monkey\Functions\stubEscapeFunctions()` function
	 * which does apply some form of escaping to the input.
	 *
	 * @return void
	 */
	public function stubEscapeFunctions(): void {
		Monkey\Functions\stubs(
			array(
				'esc_js',
				'esc_sql',
				'esc_attr',
				'esc_html',
				'esc_textarea',
				'esc_url',
				'esc_url_raw',
				'esc_xml',
			)
		);
	}
}
