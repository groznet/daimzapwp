<?php
/**
 * Small filters that shape markup produced by WordPress core.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add layout-aware classes to the body element.
 *
 * @param array $classes Body classes.
 * @return array
 */
function daimzap_body_classes( $classes ) {
	if ( daimzap_is_catalog_context() ) {
		$classes[] = 'dz-catalog';
	}

	if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
		$classes[] = 'dz-no-blog-sidebar';
	}

	if ( is_singular() && ! is_front_page() ) {
		$classes[] = 'dz-singular';
	}

	return $classes;
}
add_filter( 'body_class', 'daimzap_body_classes' );

/**
 * Give post thumbnails sensible loading and sizing behaviour in the blog grid.
 *
 * @param array $attr Attachment image attributes.
 * @return array
 */
function daimzap_post_thumbnail_attributes( $attr ) {
	if ( empty( $attr['sizes'] ) && ( is_home() || is_archive() || is_search() ) ) {
		$attr['sizes'] = '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 380px';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'daimzap_post_thumbnail_attributes' );

/**
 * Archive titles without the "Category:" / "Архивы:" prefixes.
 *
 * @param string $title Archive title.
 * @return string
 */
function daimzap_archive_title( $title ) {
	if ( is_category() || is_tag() || is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_author() ) {
		$title = get_the_author();
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'daimzap_archive_title' );

/**
 * Give the "read more" link an accessible, descriptive label.
 *
 * @return string
 */
function daimzap_excerpt_read_more_link() {
	return sprintf(
		'<a class="dz-link" href="%1$s">%2$s<span class="screen-reader-text"> %3$s</span></a>',
		esc_url( get_permalink() ),
		esc_html__( 'Читать далее', 'daimzap' ),
		esc_html( get_the_title() )
	);
}

/**
 * Remove the WordPress emoji payload.
 *
 * Nothing in the theme relies on the emoji polyfill, and it costs a script plus
 * a DNS lookup on every page.
 *
 * @return void
 */
function daimzap_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'daimzap_disable_emojis' );

/**
 * Skip-link focus target styling relies on this class being present.
 *
 * @param string $content Content of the "more" link.
 * @return string
 */
function daimzap_link_more_class( $content ) {
	return str_replace( 'more-link', 'more-link dz-link', $content );
}
add_filter( 'the_content_more_link', 'daimzap_link_more_class' );
