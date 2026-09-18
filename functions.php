<?php
/**
 * DaimZap theme bootstrap.
 *
 * Loads the theme modules. Keep this file free of business logic — every
 * feature lives in its own file under inc/ so the theme stays navigable.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'DAIMZAP_VERSION' ) ) {
	$daimzap_theme = wp_get_theme( get_template() );
	define( 'DAIMZAP_VERSION', $daimzap_theme->get( 'Version' ) ? $daimzap_theme->get( 'Version' ) : '1.0.0' );
}

if ( ! defined( 'DAIMZAP_DIR' ) ) {
	define( 'DAIMZAP_DIR', get_template_directory() );
}

/**
 * Require a theme file relative to the theme root.
 *
 * @param string $relative_path Path relative to the theme directory.
 * @return void
 */
function daimzap_require( $relative_path ) {
	$file = get_theme_file_path( $relative_path );

	if ( is_readable( $file ) ) {
		require_once $file;
	}
}

daimzap_require( 'inc/setup.php' );
daimzap_require( 'inc/skins.php' );
daimzap_require( 'inc/assets.php' );
daimzap_require( 'inc/template-tags.php' );
daimzap_require( 'inc/template-functions.php' );
daimzap_require( 'inc/class-daimzap-nav-walker.php' );
daimzap_require( 'inc/customizer.php' );
daimzap_require( 'inc/wholesale.php' );

if ( daimzap_is_woocommerce_active() ) {
	daimzap_require( 'inc/woocommerce/setup.php' );
	daimzap_require( 'inc/woocommerce/attributes.php' );
	daimzap_require( 'inc/woocommerce/hooks.php' );
	daimzap_require( 'inc/woocommerce/catalog.php' );
	daimzap_require( 'inc/woocommerce/search.php' );
	daimzap_require( 'inc/woocommerce/cart.php' );
	daimzap_require( 'inc/woocommerce/account.php' );
}
