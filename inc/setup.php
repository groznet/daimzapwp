<?php
/**
 * Theme setup: supports, menus, image sizes, sidebars.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether WooCommerce is active.
 *
 * Uses class_exists() rather than a plugin-file check so it also works when
 * WooCommerce is loaded as a must-use plugin.
 *
 * @return bool
 */
function daimzap_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Register theme supports and navigation menus.
 *
 * @return void
 */
function daimzap_setup() {
	load_theme_textdomain( 'daimzap', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'               => 64,
			'width'                => 240,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => true,
		)
	);

	register_nav_menus(
		array(
			'primary'   => __( 'Основное меню', 'daimzap' ),
			'catalog'   => __( 'Меню каталога', 'daimzap' ),
			'footer'    => __( 'Меню в подвале', 'daimzap' ),
			'utilities' => __( 'Служебные ссылки', 'daimzap' ),
		)
	);

	add_image_size( 'daimzap-card', 480, 480, true );
	add_image_size( 'daimzap-hero', 1440, 640, true );

	// Editor styles use the compiled front-end stylesheet so the admin preview
	// stays close to the real thing without a second build target.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );
}
add_action( 'after_setup_theme', 'daimzap_setup' );

/**
 * Set the content width used by oEmbeds and wide images.
 *
 * @return void
 */
function daimzap_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'daimzap_content_width', 1280 );
}
add_action( 'after_setup_theme', 'daimzap_content_width', 0 );

/**
 * Register widget areas.
 *
 * The catalog sidebar is intentionally separate from the blog sidebar: the
 * catalog one is reserved for filtering widgets, not generic blog widgets.
 *
 * @return void
 */
function daimzap_widgets_init() {
	$defaults = array(
		'before_widget' => '<section id="%1$s" class="dz-widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="dz-widget__title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array_merge(
			$defaults,
			array(
				'name'        => __( 'Сайдбар каталога', 'daimzap' ),
				'id'          => 'catalog-sidebar',
				'description' => __( 'Отображается в каталоге под фильтрами. Подходит для виджетов фильтрации WooCommerce.', 'daimzap' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$defaults,
			array(
				'name'        => __( 'Сайдбар блога', 'daimzap' ),
				'id'          => 'blog-sidebar',
				'description' => __( 'Отображается в новостях и на страницах записей.', 'daimzap' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$defaults,
			array(
				'name'        => __( 'Подвал', 'daimzap' ),
				'id'          => 'footer-widgets',
				'description' => __( 'Дополнительная колонка в подвале сайта.', 'daimzap' ),
			)
		)
	);
}
add_action( 'widgets_init', 'daimzap_widgets_init' );

/**
 * Trim the default excerpt and give it a neutral ellipsis.
 *
 * @param string $more Current "more" string.
 * @return string
 */
function daimzap_excerpt_more( $more ) {
	return is_admin() ? $more : '…';
}
add_filter( 'excerpt_more', 'daimzap_excerpt_more' );

/**
 * Shorter excerpts read better in the compact card layouts.
 *
 * @param int $length Current excerpt length.
 * @return int
 */
function daimzap_excerpt_length( $length ) {
	return is_admin() ? $length : 28;
}
add_filter( 'excerpt_length', 'daimzap_excerpt_length' );

/**
 * Add a pingback header only where it is actually needed.
 *
 * @return void
 */
function daimzap_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'daimzap_pingback_header' );
