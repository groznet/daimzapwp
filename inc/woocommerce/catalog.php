<?php
/**
 * Catalog query behaviour and filter state.
 *
 * Filtering is built on WooCommerce core mechanics rather than a parallel
 * system:
 *
 * - Attribute filters use `filter_{attribute}` query args, which
 *   `WC_Query::get_layered_nav_chosen_attributes()` already folds into the
 *   product loop's tax query.
 * - Price filtering uses core's `min_price` / `max_price`.
 * - Stock filtering uses the `product_visibility` taxonomy and its `outofstock`
 *   term — the same mechanism WooCommerce uses for "hide out of stock items".
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Query var used by the in-stock filter.
 */
const DAIMZAP_STOCK_QUERY_VAR = 'in_stock';

/**
 * Register the theme's own catalog query vars.
 *
 * @param array $vars Public query vars.
 * @return array
 */
function daimzap_query_vars( $vars ) {
	$vars[] = DAIMZAP_STOCK_QUERY_VAR;

	return $vars;
}
add_filter( 'query_vars', 'daimzap_query_vars' );

/**
 * Whether the in-stock filter is currently active.
 *
 * @return bool
 */
function daimzap_stock_filter_active() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only catalog filter state.
	return isset( $_GET[ DAIMZAP_STOCK_QUERY_VAR ] ) && '1' === sanitize_text_field( wp_unslash( $_GET[ DAIMZAP_STOCK_QUERY_VAR ] ) );
}

/**
 * Restrict the product loop to in-stock products when the filter is on.
 *
 * @param WP_Query $query Product query.
 * @return void
 */
function daimzap_filter_in_stock( $query ) {
	if ( ! daimzap_stock_filter_active() ) {
		return;
	}

	$tax_query   = (array) $query->get( 'tax_query', array() );
	$tax_query[] = array(
		'taxonomy' => 'product_visibility',
		'field'    => 'name',
		'terms'    => 'outofstock',
		'operator' => 'NOT IN',
	);

	$query->set( 'tax_query', $tax_query );
}
add_action( 'woocommerce_product_query', 'daimzap_filter_in_stock' );

/**
 * Catalog sort options.
 *
 * Relevance-style sorting is dropped in favour of options a parts buyer uses:
 * novelty, price and name.
 *
 * @param array $options Sort options.
 * @return array
 */
function daimzap_catalog_orderby( $options ) {
	unset( $options['rating'], $options['popularity'] );

	$options['menu_order'] = __( 'По умолчанию', 'daimzap' );
	$options['date']       = __( 'Сначала новые', 'daimzap' );
	$options['price']      = __( 'Цена: по возрастанию', 'daimzap' );
	$options['price-desc'] = __( 'Цена: по убыванию', 'daimzap' );
	$options['title']      = __( 'По названию', 'daimzap' );

	return $options;
}
add_filter( 'woocommerce_catalog_orderby', 'daimzap_catalog_orderby' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'daimzap_catalog_orderby' );

/**
 * Support sorting by product title.
 *
 * @param array  $args    Ordering args.
 * @param string $orderby Requested ordering.
 * @return array
 */
function daimzap_catalog_ordering_args( $args, $orderby = '' ) {
	if ( 'title' === $orderby ) {
		$args['orderby']  = 'title';
		$args['order']    = 'ASC';
		$args['meta_key'] = ''; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Clearing, not querying.
	}

	return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'daimzap_catalog_ordering_args', 10, 2 );

/**
 * Chosen vehicle attribute terms for the current request.
 *
 * Reads WooCommerce's own parsed layered-nav state so the filter UI and the
 * query can never disagree.
 *
 * @param string $role Either `make` or `model`.
 * @return string[] Chosen term slugs.
 */
function daimzap_get_chosen_vehicle_terms( $role ) {
	if ( ! daimzap_has_vehicle_taxonomy( $role ) || ! class_exists( 'WC_Query' ) ) {
		return array();
	}

	$taxonomy = daimzap_vehicle_taxonomy( $role );
	$chosen   = WC_Query::get_layered_nav_chosen_attributes();

	if ( ! isset( $chosen[ $taxonomy ]['terms'] ) ) {
		return array();
	}

	return array_map( 'sanitize_title', (array) $chosen[ $taxonomy ]['terms'] );
}

/**
 * Build a catalog URL with one filter value toggled on or off.
 *
 * Preserves every other active filter, drops pagination, and keeps the current
 * archive as the base so filtering works identically on the shop page, category
 * archives and vehicle archives.
 *
 * @param string $taxonomy Attribute taxonomy name.
 * @param string $term     Term slug to toggle.
 * @return string
 */
function daimzap_toggle_filter_url( $taxonomy, $term ) {
	$filter_name = 'filter_' . str_replace( 'pa_', '', $taxonomy );
	$base        = daimzap_catalog_base_url();
	$args        = daimzap_current_filter_args();

	$current = isset( $args[ $filter_name ] ) ? explode( ',', $args[ $filter_name ] ) : array();
	$current = array_filter( array_map( 'sanitize_title', $current ) );

	if ( in_array( $term, $current, true ) ) {
		$current = array_diff( $current, array( $term ) );
	} else {
		$current[] = $term;
	}

	if ( $current ) {
		$args[ $filter_name ] = implode( ',', $current );
	} else {
		unset( $args[ $filter_name ] );
	}

	return $args ? add_query_arg( $args, $base ) : $base;
}

/**
 * Base URL of the current catalog listing, without filters or pagination.
 *
 * @return string
 */
function daimzap_catalog_base_url() {
	if ( is_product_taxonomy() ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			$link = get_term_link( $term );

			if ( ! is_wp_error( $link ) ) {
				return $link;
			}
		}
	}

	if ( is_search() ) {
		// The search term and post type are carried in the filter args, so the
		// site root is the right base here.
		return home_url( '/' );
	}

	$shop_id = wc_get_page_id( 'shop' );

	return $shop_id > 0 ? (string) get_permalink( $shop_id ) : home_url( '/' );
}

/**
 * Currently active catalog query args worth preserving across filter links.
 *
 * @return array
 */
function daimzap_current_filter_args() {
	$args = array();

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only catalog filter state.
	foreach ( $_GET as $key => $value ) {
		$key = sanitize_key( $key );

		$is_attribute_filter = 0 === strpos( $key, 'filter_' ) || 0 === strpos( $key, 'query_type_' );
		$is_known_filter     = in_array( $key, array( 'min_price', 'max_price', 'orderby', 's', 'post_type', 'product_cat', DAIMZAP_STOCK_QUERY_VAR ), true );

		if ( ! $is_attribute_filter && ! $is_known_filter ) {
			continue;
		}

		if ( is_array( $value ) ) {
			continue;
		}

		$clean = sanitize_text_field( wp_unslash( $value ) );

		if ( '' !== $clean ) {
			$args[ $key ] = $clean;
		}
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	return $args;
}

/**
 * URL that clears every catalog filter.
 *
 * Sorting and the search term survive, because clearing filters should not
 * throw away what the visitor was looking for.
 *
 * @return string
 */
function daimzap_clear_filters_url() {
	$args = daimzap_current_filter_args();
	$keep = array();

	foreach ( array( 'orderby', 's', 'post_type' ) as $key ) {
		if ( isset( $args[ $key ] ) ) {
			$keep[ $key ] = $args[ $key ];
		}
	}

	$base = daimzap_catalog_base_url();

	return $keep ? add_query_arg( $keep, $base ) : $base;
}

/**
 * Human readable list of active filters, for the "active filters" bar.
 *
 * @return array[] Each entry has `label`, `value` and `remove_url`.
 */
function daimzap_get_active_filters() {
	$active = array();

	foreach ( array( 'make', 'model' ) as $role ) {
		if ( ! daimzap_has_vehicle_taxonomy( $role ) ) {
			continue;
		}

		$taxonomy = daimzap_vehicle_taxonomy( $role );
		$label    = wc_attribute_label( $taxonomy );

		foreach ( daimzap_get_chosen_vehicle_terms( $role ) as $slug ) {
			$term = get_term_by( 'slug', $slug, $taxonomy );

			$active[] = array(
				'label'      => $label,
				'value'      => $term instanceof WP_Term ? $term->name : $slug,
				'remove_url' => daimzap_toggle_filter_url( $taxonomy, $slug ),
			);
		}
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only catalog filter state.
	$min_price = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : '';
	$max_price = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	if ( '' !== $min_price || '' !== $max_price ) {
		$parts = array();

		if ( '' !== $min_price ) {
			/* translators: %s: formatted minimum price. */
			$parts[] = sprintf( __( 'от %s', 'daimzap' ), wp_strip_all_tags( wc_price( (float) $min_price ) ) );
		}

		if ( '' !== $max_price ) {
			/* translators: %s: formatted maximum price. */
			$parts[] = sprintf( __( 'до %s', 'daimzap' ), wp_strip_all_tags( wc_price( (float) $max_price ) ) );
		}

		$active[] = array(
			'label'      => __( 'Цена', 'daimzap' ),
			'value'      => implode( ' ', $parts ),
			'remove_url' => add_query_arg(
				array_diff_key( daimzap_current_filter_args(), array_flip( array( 'min_price', 'max_price' ) ) ),
				daimzap_catalog_base_url()
			),
		);
	}

	if ( daimzap_stock_filter_active() ) {
		$active[] = array(
			'label'      => __( 'Наличие', 'daimzap' ),
			'value'      => __( 'Только в наличии', 'daimzap' ),
			'remove_url' => add_query_arg(
				array_diff_key( daimzap_current_filter_args(), array_flip( array( DAIMZAP_STOCK_QUERY_VAR ) ) ),
				daimzap_catalog_base_url()
			),
		);
	}

	return $active;
}

/**
 * Price bounds across published products in the store.
 *
 * Used to seed the price filter inputs with sensible placeholders. These are
 * store-wide bounds rather than bounds for the current query: the numbers only
 * hint at the range, and recomputing them per filter combination would cost a
 * query on every catalog view for no real gain.
 *
 * Reads WooCommerce's own product meta lookup table (the table it maintains for
 * exactly this kind of aggregate) and caches the result against WooCommerce's
 * product query cache version, so it invalidates when products change.
 *
 * @return array{min:float,max:float}
 */
function daimzap_get_price_range() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$empty = array(
		'min' => 0.0,
		'max' => 0.0,
	);

	if ( ! class_exists( 'WC_Cache_Helper' ) ) {
		return $empty;
	}

	global $wpdb;

	if ( empty( $wpdb->wc_product_meta_lookup ) ) {
		return $empty;
	}

	$transient_key = 'daimzap_price_range_' . WC_Cache_Helper::get_transient_version( 'product' );
	$cached        = get_transient( $transient_key );

	if ( is_array( $cached ) && isset( $cached['min'], $cached['max'] ) ) {
		$cache = $cached;

		return $cache;
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Aggregate over WooCommerce's own lookup table with no user input; the result is cached immediately below.
	$row = $wpdb->get_row(
		"SELECT MIN( lookup.min_price ) AS min_price, MAX( lookup.max_price ) AS max_price
		FROM {$wpdb->wc_product_meta_lookup} AS lookup
		INNER JOIN {$wpdb->posts} AS posts ON posts.ID = lookup.product_id
		WHERE posts.post_status = 'publish'",
		ARRAY_A
	);

	$cache = array(
		'min' => isset( $row['min_price'] ) ? (float) $row['min_price'] : 0.0,
		'max' => isset( $row['max_price'] ) ? (float) $row['max_price'] : 0.0,
	);

	set_transient( $transient_key, $cache, DAY_IN_SECONDS );

	return $cache;
}

/**
 * Loop counters with a fallback to the main query.
 *
 * WooCommerce sets these loop props for shop and taxonomy archives. Product
 * searches reach the catalog template through a different path, so the values
 * fall back to the main query rather than rendering "0 products".
 *
 * @return array{total:int,per_page:int,current_page:int}
 */
function daimzap_loop_counters() {
	$total        = (int) wc_get_loop_prop( 'total' );
	$per_page     = (int) wc_get_loop_prop( 'per_page' );
	$current_page = (int) wc_get_loop_prop( 'current_page' );

	if ( ! $total && isset( $GLOBALS['wp_query'] ) ) {
		$total = (int) $GLOBALS['wp_query']->found_posts;
	}

	if ( ! $per_page ) {
		$per_page = daimzap_products_per_page();
	}

	if ( ! $current_page ) {
		$current_page = max( 1, (int) get_query_var( 'paged' ) );
	}

	return array(
		'total'        => $total,
		'per_page'     => $per_page,
		'current_page' => $current_page,
	);
}
