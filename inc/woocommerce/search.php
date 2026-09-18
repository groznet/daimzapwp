<?php
/**
 * SKU-aware product search.
 *
 * Auto parts are found by article number more often than by name, so product
 * search is extended to match SKUs. The SKU lookup goes through
 * `wc_get_products()`, which supports partial SKU matching, rather than custom
 * SQL against post meta.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Maximum number of SKU matches folded into a search result set.
 */
const DAIMZAP_SKU_MATCH_LIMIT = 100;

/**
 * Product IDs whose SKU matches the search term.
 *
 * @param string $term Raw search term.
 * @return int[]
 */
function daimzap_get_sku_matches( $term ) {
	static $cache = array();

	$term = trim( (string) $term );

	if ( strlen( $term ) < 2 || ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	if ( isset( $cache[ $term ] ) ) {
		return $cache[ $term ];
	}

	$ids = wc_get_products(
		array(
			'sku'      => $term,
			'status'   => 'publish',
			'limit'    => DAIMZAP_SKU_MATCH_LIMIT,
			'return'   => 'ids',
			'orderby'  => 'ID',
			'order'    => 'ASC',
		)
	);

	$cache[ $term ] = is_array( $ids ) ? array_map( 'absint', $ids ) : array();

	return $cache[ $term ];
}

/**
 * Whether the current query is a front-end product search.
 *
 * @param WP_Query $query Query being inspected.
 * @return bool
 */
function daimzap_is_product_search_query( $query ) {
	if ( is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
		return false;
	}

	if ( ! $query->is_search() ) {
		return false;
	}

	$post_type = $query->get( 'post_type' );

	return 'product' === $post_type || ( is_array( $post_type ) && in_array( 'product', $post_type, true ) );
}

/**
 * Send an exact SKU match straight to the product page.
 *
 * Typing a full article number is an unambiguous request for one specific part,
 * so a results page with a single row would just be an extra click.
 *
 * @return void
 */
function daimzap_redirect_exact_sku_match() {
	if ( is_admin() || ! is_search() ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public search request.
	$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';

	if ( 'product' !== $post_type ) {
		return;
	}

	$term = trim( (string) get_search_query( false ) );

	if ( '' === $term || ! function_exists( 'wc_get_product_id_by_sku' ) ) {
		return;
	}

	$product_id = wc_get_product_id_by_sku( $term );

	if ( ! $product_id ) {
		return;
	}

	$product = wc_get_product( $product_id );

	if ( ! $product instanceof WC_Product || 'publish' !== $product->get_status() ) {
		return;
	}

	wp_safe_redirect( get_permalink( $product_id ), 302 );
	exit;
}
add_action( 'template_redirect', 'daimzap_redirect_exact_sku_match', 5 );

/**
 * Widen product search results to include SKU matches.
 *
 * Appends an ID condition to WordPress' own search SQL instead of replacing it,
 * so name and description matches still rank and behave normally.
 *
 * @param string   $search Search SQL fragment.
 * @param WP_Query $query  Query being run.
 * @return string
 */
function daimzap_search_include_skus( $search, $query ) {
	if ( '' === $search || ! daimzap_is_product_search_query( $query ) ) {
		return $search;
	}

	$ids = daimzap_get_sku_matches( $query->get( 's' ) );

	if ( ! $ids ) {
		return $search;
	}

	global $wpdb;

	$id_list = implode( ',', array_map( 'absint', $ids ) );

	/*
	 * $search is a chain of "AND ( ... )" groups — one per search word. Wrapping
	 * the whole chain, rather than widening the first group, keeps the meaning
	 * exact for multi-word searches: a SKU hit matches on its own, and the
	 * original word-by-word matching is untouched. The leading "1=1" makes the
	 * chain valid on its own, since $search starts with "AND".
	 */
	return " AND ( {$wpdb->posts}.ID IN ({$id_list}) OR ( 1=1 {$search} ) )";
}
add_filter( 'posts_search', 'daimzap_search_include_skus', 10, 2 );

/**
 * Make product search behave like the rest of the catalog.
 *
 * WooCommerce only runs `product_query()` for the shop page and product
 * taxonomy archives, so a search results page would otherwise ignore the
 * sidebar filters. Rather than reimplementing them, the query is handed the
 * same tax/meta/ordering arguments WooCommerce builds for the catalog, which
 * keeps layered navigation, visibility, price and sorting behaviour identical
 * on every listing.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function daimzap_scope_product_search( $query ) {
	if ( ! daimzap_is_product_search_query( $query ) ) {
		return;
	}

	$query->set( 'posts_per_page', daimzap_products_per_page() );
	$query->set( 'wc_query', 'product_query' );

	if ( ! class_exists( 'WC_Query' ) || ! WC()->query ) {
		return;
	}

	$query->set( 'tax_query', WC_Query::get_tax_query( (array) $query->get( 'tax_query' ), true ) );
	$query->set( 'meta_query', WC_Query::get_meta_query( (array) $query->get( 'meta_query' ), true ) );

	$ordering = WC()->query->get_catalog_ordering_args();

	if ( ! empty( $ordering['orderby'] ) ) {
		$query->set( 'orderby', $ordering['orderby'] );
	}

	if ( ! empty( $ordering['order'] ) ) {
		$query->set( 'order', $ordering['order'] );
	}

	if ( ! empty( $ordering['meta_key'] ) ) {
		$query->set( 'meta_key', $ordering['meta_key'] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Mirrors WooCommerce's own catalog ordering.
	}

	daimzap_filter_in_stock( $query );
}
add_action( 'pre_get_posts', 'daimzap_scope_product_search' );

/**
 * Apply WooCommerce's price filter clauses to product searches.
 *
 * WooCommerce registers these clauses inside `product_query()`; search results
 * never reach that method, so the same public handler is delegated to here.
 *
 * @param array    $clauses  Query clauses.
 * @param WP_Query $wp_query Query being run.
 * @return array
 */
function daimzap_search_price_filter_clauses( $clauses, $wp_query ) {
	if ( ! daimzap_is_product_search_query( $wp_query ) || ! WC()->query ) {
		return $clauses;
	}

	return WC()->query->price_filter_post_clauses( $clauses, $wp_query );
}
add_filter( 'posts_clauses', 'daimzap_search_price_filter_clauses', 10, 2 );
