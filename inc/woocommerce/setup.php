<?php
/**
 * WooCommerce theme support and layout wrappers.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Declare WooCommerce support.
 *
 * Template overrides only take effect for themes that declare support, so this
 * is a hard requirement for the custom catalog templates.
 *
 * The product gallery features (zoom / lightbox / slider) are deliberately not
 * declared: the theme ships its own gallery, which keeps flexslider, photoswipe
 * and jquery-zoom off product pages entirely.
 *
 * @return void
 */
function daimzap_woocommerce_support() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width'         => 480,
			'gallery_thumbnail_image_width' => 120,
			'single_image_width'            => 800,
			'product_grid'                  => array(
				'default_rows'    => 4,
				'min_rows'        => 2,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 5,
			),
		)
	);
}
add_action( 'after_setup_theme', 'daimzap_woocommerce_support' );

/**
 * Open the theme's content wrapper around WooCommerce templates.
 *
 * Using the wrapper hooks (rather than a catch-all woocommerce.php) keeps the
 * WooCommerce template hierarchy intact, so archive-product.php and the
 * taxonomy templates still resolve normally.
 *
 * @return void
 */
function daimzap_woocommerce_wrapper_start() {
	echo '<main id="main" class="dz-main dz-main--shop">';
}

/**
 * Close the theme's content wrapper.
 *
 * @return void
 */
function daimzap_woocommerce_wrapper_end() {
	echo '</main>';
}

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'daimzap_woocommerce_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'daimzap_woocommerce_wrapper_end', 10 );

/*
 * Render the catalog sidebar only where a product listing is shown. WooCommerce
 * calls `woocommerce_sidebar` on single products too; the single product layout
 * is full width by design, so the sidebar is unhooked here and the archive
 * template renders it directly.
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Number of products per catalog page.
 *
 * @return int
 */
function daimzap_products_per_page() {
	return (int) apply_filters( 'daimzap_products_per_page', 24 );
}
add_filter( 'loop_shop_per_page', 'daimzap_products_per_page', 20 );

/**
 * Columns in the product grid.
 *
 * @return int
 */
function daimzap_loop_columns() {
	return (int) apply_filters( 'daimzap_loop_columns', 4 );
}
add_filter( 'loop_shop_columns', 'daimzap_loop_columns', 20 );

/**
 * Related products: keep them useful and cheap.
 *
 * @param array $args Related products query args.
 * @return array
 */
function daimzap_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'daimzap_related_products_args', 20 );

/**
 * Placeholder image for products without a photo.
 *
 * @return string
 */
function daimzap_placeholder_image_src() {
	return get_theme_file_uri( 'assets/images/product-placeholder.svg' );
}
add_filter( 'woocommerce_placeholder_img_src', 'daimzap_placeholder_image_src' );

/**
 * Markup for the placeholder image so it matches card dimensions.
 *
 * @param string       $image Placeholder markup.
 * @param string|array $size  Requested size.
 * @return string
 */
function daimzap_placeholder_image( $image, $size ) {
	$dimensions = wc_get_image_size( $size );

	return sprintf(
		'<img src="%1$s" alt="%2$s" width="%3$s" height="%4$s" class="dz-product-image dz-product-image--placeholder" loading="lazy" decoding="async" />',
		esc_url( daimzap_placeholder_image_src() ),
		esc_attr__( 'Фотография товара отсутствует', 'daimzap' ),
		esc_attr( $dimensions['width'] ? $dimensions['width'] : 480 ),
		esc_attr( $dimensions['height'] ? $dimensions['height'] : 480 )
	);
}
add_filter( 'woocommerce_placeholder_img', 'daimzap_placeholder_image', 10, 2 );

/**
 * Use the catalog image size in the product loop.
 *
 * @return string
 */
function daimzap_archive_thumbnail_size() {
	return 'woocommerce_thumbnail';
}
add_filter( 'single_product_archive_thumbnail_size', 'daimzap_archive_thumbnail_size' );

/**
 * Drop cart cross-sells.
 *
 * Auto parts are bought to fix a specific vehicle; upsell rows on the cart page
 * only slow the purchase down.
 *
 * @return void
 */
function daimzap_remove_cart_cross_sells() {
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
}
add_action( 'wp', 'daimzap_remove_cart_cross_sells' );

/**
 * Keep the "Additional information" tab, drop the default description tab title noise.
 *
 * @param array $tabs Product tabs.
 * @return array
 */
function daimzap_product_tabs( $tabs ) {
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = __( 'Описание', 'daimzap' );
	}

	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = __( 'Характеристики', 'daimzap' );
	}

	if ( isset( $tabs['reviews'] ) ) {
		$tabs['reviews']['title'] = __( 'Отзывы', 'daimzap' );
	}

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'daimzap_product_tabs', 98 );

/**
 * Stock display: exact quantities help wholesale buyers plan orders.
 *
 * @param array      $args    Availability args.
 * @param WC_Product $product Product object.
 * @return array
 */
function daimzap_stock_availability_text( $args, $product ) {
	if ( ! $product->is_in_stock() ) {
		$args['availability'] = __( 'Нет в наличии', 'daimzap' );

		return $args;
	}

	if ( $product->managing_stock() ) {
		$quantity = $product->get_stock_quantity();

		if ( null !== $quantity && $quantity > 0 ) {
			/* translators: %s: number of items in stock. */
			$args['availability'] = sprintf( __( 'В наличии: %s шт.', 'daimzap' ), wc_stock_amount( $quantity ) );

			return $args;
		}
	}

	$args['availability'] = __( 'В наличии', 'daimzap' );

	return $args;
}
add_filter( 'woocommerce_get_availability', 'daimzap_stock_availability_text', 10, 2 );

// The catalog header renders the shop title itself.
add_filter( 'woocommerce_show_page_title', '__return_false' );

/*
 * Result count and ordering are re-rendered together inside the catalog toolbar
 * (see template-parts/catalog/toolbar.php), so the default standalone output is
 * unhooked here.
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
