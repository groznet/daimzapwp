<?php
/**
 * Asset loading.
 *
 * Every script is enqueued only on the pages that need it, and all of them are
 * deferred. The theme ships one compiled stylesheet; WooCommerce core styles
 * are trimmed to the parts we do not re-implement.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * File version based on mtime so cache busting survives edits between releases.
 *
 * @param string $relative_path Path relative to the theme directory.
 * @return string
 */
function daimzap_asset_version( $relative_path ) {
	$file = get_theme_file_path( $relative_path );

	if ( file_exists( $file ) ) {
		return DAIMZAP_VERSION . '.' . filemtime( $file );
	}

	return DAIMZAP_VERSION;
}

/**
 * Whether Font Awesome should be loaded.
 *
 * Icons are used sparingly (navigation, search, cart, stock status, contacts).
 * Sites that prefer to self-host the icon font can disable the CDN copy with
 * `add_filter( 'daimzap_load_font_awesome', '__return_false' )` and enqueue a
 * local build under the same `daimzap-icons` handle.
 *
 * @return bool
 */
function daimzap_load_font_awesome() {
	return (bool) apply_filters( 'daimzap_load_font_awesome', true );
}

/**
 * Enqueue front-end styles and scripts.
 *
 * @return void
 */
function daimzap_enqueue_assets() {
	$skin_stylesheet = daimzap_skin_stylesheet();

	wp_enqueue_style(
		'daimzap-main',
		get_theme_file_uri( $skin_stylesheet ),
		array(),
		daimzap_asset_version( $skin_stylesheet )
	);

	wp_style_add_data( 'daimzap-main', 'rtl', 'replace' );

	if ( daimzap_load_font_awesome() ) {
		$fa_version = '6.7.2';

		wp_enqueue_style(
			'daimzap-icons',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . $fa_version . '/css/fontawesome.min.css',
			array(),
			$fa_version
		);

		wp_enqueue_style(
			'daimzap-icons-solid',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . $fa_version . '/css/solid.min.css',
			array( 'daimzap-icons' ),
			$fa_version
		);
	}

	// Site chrome: mobile navigation, search panel, cart count. Needed everywhere.
	wp_enqueue_script(
		'daimzap-site',
		get_theme_file_uri( 'assets/js/site.js' ),
		array(),
		daimzap_asset_version( 'assets/js/site.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'daimzap-site',
		'daimzapL10n',
		array(
			'menuOpen'   => __( 'Открыть меню', 'daimzap' ),
			'menuClose'  => __( 'Закрыть меню', 'daimzap' ),
			'filterOpen' => __( 'Показать фильтры', 'daimzap' ),
		)
	);

	if ( daimzap_is_catalog_context() ) {
		wp_enqueue_script(
			'daimzap-catalog',
			get_theme_file_uri( 'assets/js/catalog.js' ),
			array(),
			daimzap_asset_version( 'assets/js/catalog.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_script(
			'daimzap-product',
			get_theme_file_uri( 'assets/js/product.js' ),
			array(),
			daimzap_asset_version( 'assets/js/product.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'daimzap_enqueue_assets' );

/**
 * Speed up the icon font handshake when the CDN copy is in use.
 *
 * @param array  $urls          URLs to print.
 * @param string $relation_type Relation type.
 * @return array
 */
function daimzap_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && daimzap_load_font_awesome() ) {
		$urls[] = array(
			'href'        => 'https://cdnjs.cloudflare.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'daimzap_resource_hints', 10, 2 );

/**
 * Drop the WooCommerce stylesheets the theme fully re-implements.
 *
 * `woocommerce-layout` and `woocommerce-smallscreen` are pure layout opinions
 * that fight the custom design.
 *
 * `woocommerce-general` goes too, and that is the change that makes the store
 * stop looking like WooCommerce. It ships unlayered, so its defaults — the
 * lilac `.button.alt` (#a46497), the tinted notice bars, the pill `a.remove`,
 * the grey payment box — outranked every themed rule inside `@layer components`
 * no matter how specific that rule was. The theme already owns the components
 * it was being kept for (select2, the password strength meter, notices), and
 * assets/css/src/parts/woocommerce.css supplies the few genuinely generic bits
 * that stylesheet also carried: the AJAX block overlay, star ratings and the
 * store notice.
 *
 * Sites running an extension that depends on Woo's own CSS can put it back with
 * `add_filter( 'daimzap_keep_woocommerce_general', '__return_true' )`.
 *
 * @param array $styles Registered WooCommerce styles.
 * @return array
 */
function daimzap_woocommerce_styles( $styles ) {
	unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );

	if ( ! apply_filters( 'daimzap_keep_woocommerce_general', false ) ) {
		unset( $styles['woocommerce-general'] );
	}

	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'daimzap_woocommerce_styles' );

/**
 * Remove WooCommerce block styles on pages that never render Woo blocks.
 *
 * The cart and checkout shortcode pages plus every classic template are served
 * by this theme's own CSS, so the block library payload is dead weight there.
 *
 * @return void
 */
function daimzap_dequeue_unused_block_styles() {
	if ( is_admin() || ! daimzap_is_woocommerce_active() ) {
		return;
	}

	if ( is_singular() && has_blocks( get_queried_object_id() ) ) {
		return;
	}

	wp_dequeue_style( 'wc-blocks-style' );
}
add_action( 'wp_enqueue_scripts', 'daimzap_dequeue_unused_block_styles', 99 );

/**
 * Keep WooCommerce cart scripts off pages that have no cart interaction.
 *
 * WooCommerce already scopes most of its scripts, but `wc-cart-fragments`
 * defeats full-page caching on every request. The theme renders the cart count
 * server-side and refreshes it from the fragments response only where a cart
 * action can actually happen.
 *
 * @return void
 */
function daimzap_dequeue_cart_fragments() {
	if ( is_admin() || ! daimzap_is_woocommerce_active() ) {
		return;
	}

	if ( ! apply_filters( 'daimzap_limit_cart_fragments', true ) ) {
		return;
	}

	$needs_fragments = is_woocommerce() || is_cart() || is_checkout() || is_front_page();

	if ( ! $needs_fragments ) {
		wp_dequeue_script( 'wc-cart-fragments' );
	}
}
add_action( 'wp_enqueue_scripts', 'daimzap_dequeue_cart_fragments', 99 );
