<?php

declare(strict_types = 1);

namespace Another\Plugin\Another_Wishlist\Tests\Unit;

require_once __DIR__ . '/../../vendor/autoload.php';

$GLOBALS['wp_version'] = '1.0';

if ( ! \defined( 'WPINC' ) ) {
	\define( 'WPINC', 'wp-includes' );
}

if ( ! \defined( 'OBJECT' ) ) {
	\define( 'OBJECT', 'OBJECT' );
}

/*
 * Make a number of commonly used WP constants available.
 */
\define( 'ABSPATH', true );

\define( 'MINUTE_IN_SECONDS', 60 );
\define( 'HOUR_IN_SECONDS', 3600 );
\define( 'DAY_IN_SECONDS', 86400 );
\define( 'WEEK_IN_SECONDS', 604_800 );
\define( 'MONTH_IN_SECONDS', 2_592_000 );
\define( 'YEAR_IN_SECONDS', 31_536_000 );

\define( 'DB_HOST', 'nowhere' );
\define( 'DB_NAME', 'none' );
\define( 'DB_USER', 'nobody' );
\define( 'DB_PASSWORD', 'nothing' );

/*
 * Clear the opcache if it exists.
 *
 * Wrapped in a `function exists()` as the extension may not be enabled.
 */
if ( \function_exists( 'opcache_reset' ) ) {
	\opcache_reset();
}
