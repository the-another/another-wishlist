<?php
declare(strict_types=1);

if (!function_exists('trigger_deprecation')) {
	/**
	 * Triggers a silenced deprecation notice.
	 *
	 * @param string $package The name of the Composer package that is triggering the deprecation
	 * @param string $version The version of the package that introduced the deprecation
	 * @param string $message The message of the deprecation
	 * @param mixed  ...$args Values to insert in the message using printf() formatting
	 *
	 * @author Nicolas Grekas <p@tchwork.com>
	 */
	function trigger_deprecation(string $package, string $version, string $message, mixed ...$args): void
	{
		@trigger_error(($package || $version ? "Since $package $version: " : '').($args ? vsprintf($message, $args) : $message), E_USER_DEPRECATED);
	}
}

/**
 * Converts a snake_case string to CamelCase.
 *
 * For example: hello_world to HelloWorld
 *
 * @param string $string snake_case string
 *
 * @return string CamelCase string
 */
function to_camel_case(string $string): string
{
	return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
}

/**
 * Converts a CamelCase string to snake_case.
 *
 * For Example HelloWorld to hello_world
 *
 * @param string $string CamelCase String to Convert
 *
 * @return string SnakeCase string
 */
function to_snake_case(string $string): string
{
	return strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($string)));
}
