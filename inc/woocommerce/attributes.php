<?php
/**
 * Vehicle compatibility attributes.
 *
 * Car make and model are modelled as WooCommerce *global product attributes*
 * (`pa_car-make`, `pa_car-model`) rather than bespoke taxonomies. That choice
 * matters for this project:
 *
 * - The WooCommerce product CSV importer maps attribute columns natively, so
 *   the 1C/Excel export populates compatibility without custom import code.
 * - `WC_Query::get_layered_nav_chosen_attributes()` already turns
 *   `?filter_car-make=toyota` into a tax query on the product loop, so the
 *   catalog filters are core behaviour, not a parallel filtering system.
 * - Attribute archives give real, crawlable URLs per make and model.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attribute definitions used for vehicle compatibility.
 *
 * Attribute names are filterable so a store that already imported its data
 * under different slugs can point the theme at them instead of creating new
 * ones.
 *
 * @return array[] Keyed by role: `make` and `model`.
 */
function daimzap_vehicle_attributes() {
	return apply_filters(
		'daimzap_vehicle_attributes',
		array(
			'make'  => array(
				'name'  => 'car-make',
				'label' => __( 'Марка автомобиля', 'daimzap' ),
				'slug'  => 'catalog/marka',
			),
			'model' => array(
				'name'  => 'car-model',
				'label' => __( 'Модель автомобиля', 'daimzap' ),
				'slug'  => 'catalog/model',
			),
		)
	);
}

/**
 * Taxonomy name for a vehicle attribute role.
 *
 * @param string $role Either `make` or `model`.
 * @return string Taxonomy name, e.g. `pa_car-make`, or an empty string.
 */
function daimzap_vehicle_taxonomy( $role ) {
	$attributes = daimzap_vehicle_attributes();

	if ( ! isset( $attributes[ $role ] ) ) {
		return '';
	}

	return wc_attribute_taxonomy_name( $attributes[ $role ]['name'] );
}

/**
 * Whether a vehicle attribute taxonomy exists and is registered.
 *
 * Every consumer of compatibility data must check this: a fresh install has no
 * attributes until the first import runs.
 *
 * @param string $role Either `make` or `model`.
 * @return bool
 */
function daimzap_has_vehicle_taxonomy( $role ) {
	$taxonomy = daimzap_vehicle_taxonomy( $role );

	return $taxonomy && taxonomy_exists( $taxonomy );
}

/**
 * Create the vehicle attributes if the store does not have them yet.
 *
 * Runs on theme activation only. Attribute rows are store data, so the theme
 * creates them once and never rewrites an existing attribute — a store that has
 * already configured these attributes keeps its own settings.
 *
 * @return void
 */
function daimzap_register_vehicle_attributes() {
	if ( ! function_exists( 'wc_create_attribute' ) ) {
		return;
	}

	foreach ( daimzap_vehicle_attributes() as $attribute ) {
		if ( wc_attribute_taxonomy_id_by_name( $attribute['name'] ) ) {
			continue;
		}

		$result = wc_create_attribute(
			array(
				'name'         => $attribute['label'],
				'slug'         => $attribute['name'],
				'type'         => 'select',
				'order_by'     => 'name',
				'has_archives' => true,
			)
		);

		if ( is_wp_error( $result ) ) {
			wc_get_logger()->warning(
				sprintf( 'DaimZap: не удалось создать атрибут %s: %s', $attribute['name'], $result->get_error_message() ),
				array( 'source' => 'daimzap' )
			);
		}
	}

	// Attribute taxonomies are registered on init; flush once so their archive
	// permalinks resolve immediately after activation.
	delete_transient( 'wc_attribute_taxonomies' );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'daimzap_register_vehicle_attributes' );

/**
 * Give the vehicle attribute archives catalog-scoped permalinks.
 *
 * WooCommerce runs attribute taxonomy args through
 * `woocommerce_taxonomy_args_{$taxonomy}`, which lets the theme place make and
 * model archives under /catalog/ as the project's URL map requires.
 *
 * @return void
 */
function daimzap_filter_vehicle_taxonomy_args() {
	foreach ( daimzap_vehicle_attributes() as $attribute ) {
		$taxonomy = wc_attribute_taxonomy_name( $attribute['name'] );
		$slug     = $attribute['slug'];

		add_filter(
			"woocommerce_taxonomy_args_{$taxonomy}",
			static function ( $args ) use ( $slug ) {
				$args['rewrite'] = array(
					'slug'         => untrailingslashit( $slug ),
					'with_front'   => false,
					'hierarchical' => false,
				);

				$args['public']             = true;
				$args['publicly_queryable'] = true;
				$args['show_in_nav_menus']  = true;

				return $args;
			}
		);
	}
}
add_action( 'init', 'daimzap_filter_vehicle_taxonomy_args', 5 );

/**
 * Allow vehicle attributes to be added to navigation menus.
 *
 * @param bool   $register Whether the attribute is available in menus.
 * @param string $name     Attribute name without the `pa_` prefix.
 * @return bool
 */
function daimzap_attribute_show_in_nav_menus( $register, $name = '' ) {
	foreach ( daimzap_vehicle_attributes() as $attribute ) {
		if ( $attribute['name'] === $name ) {
			return true;
		}
	}

	return $register;
}
add_filter( 'woocommerce_attribute_show_in_nav_menus', 'daimzap_attribute_show_in_nav_menus', 10, 2 );

/**
 * Compatibility terms for a product.
 *
 * Returns an empty array when the attribute is missing or the product has no
 * values — every caller renders conditionally on that.
 *
 * @param WC_Product|int $product Product or product ID.
 * @param string         $role    Either `make` or `model`.
 * @return WP_Term[]
 */
function daimzap_get_vehicle_terms( $product, $role ) {
	if ( ! daimzap_has_vehicle_taxonomy( $role ) ) {
		return array();
	}

	$product_id = $product instanceof WC_Product ? $product->get_id() : (int) $product;

	if ( ! $product_id ) {
		return array();
	}

	$terms = wc_get_product_terms(
		$product_id,
		daimzap_vehicle_taxonomy( $role ),
		array( 'fields' => 'all' )
	);

	return is_wp_error( $terms ) || ! is_array( $terms ) ? array() : $terms;
}

/**
 * Flat, human readable compatibility summary for a product.
 *
 * Used in compact contexts such as product cards where the full table would be
 * too heavy. Returns an empty string when nothing is known about the product.
 *
 * @param WC_Product $product Product object.
 * @param int        $limit   Maximum number of values to list.
 * @return string
 */
function daimzap_get_compatibility_summary( $product, $limit = 3 ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	$makes  = wp_list_pluck( daimzap_get_vehicle_terms( $product, 'make' ), 'name' );
	$models = wp_list_pluck( daimzap_get_vehicle_terms( $product, 'model' ), 'name' );
	$values = $models ? $models : $makes;

	if ( ! $values ) {
		return '';
	}

	$shown     = array_slice( $values, 0, $limit );
	$remaining = count( $values ) - count( $shown );
	$summary   = implode( ', ', $shown );

	if ( $remaining > 0 ) {
		/* translators: %d: number of additional compatible vehicles. */
		$summary .= ' ' . sprintf( _n( '+%d модель', '+%d моделей', $remaining, 'daimzap' ), $remaining );
	}

	if ( $models && $makes ) {
		$summary = implode( ', ', array_slice( $makes, 0, 2 ) ) . ' · ' . $summary;
	}

	return $summary;
}

/**
 * Terms of a vehicle attribute that are actually used by published products.
 *
 * `get_terms()` with `hide_empty` keeps the filter lists honest: only makes and
 * models that have products appear.
 *
 * @param string $role   Either `make` or `model`.
 * @param int    $number Maximum number of terms, 0 for all.
 * @return WP_Term[]
 */
function daimzap_get_vehicle_terms_in_use( $role, $number = 0 ) {
	if ( ! daimzap_has_vehicle_taxonomy( $role ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => daimzap_vehicle_taxonomy( $role ),
			'hide_empty' => true,
			'orderby'    => 'name',
			'number'     => $number,
		)
	);

	return is_wp_error( $terms ) ? array() : $terms;
}
