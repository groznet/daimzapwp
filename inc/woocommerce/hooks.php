<?php
/**
 * WooCommerce hook re-wiring.
 *
 * The default loop and single-product layouts are rebuilt here with hooks
 * wherever that is enough, which keeps the number of template overrides small.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/*
 * ---------------------------------------------------------------------------
 * Product loop
 * ---------------------------------------------------------------------------
 * The card markup lives in woocommerce/content-product.php. WooCommerce's own
 * loop hooks are removed so nothing injects into the card unexpectedly; the
 * card template calls the remaining hooks it needs itself.
 */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

/*
 * ---------------------------------------------------------------------------
 * Single product
 * ---------------------------------------------------------------------------
 * The summary column is rebuilt in a compatibility-first order:
 * title → identity (SKU + stock) → price → short description → compatibility →
 * key specs → add to cart → fulfilment note.
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_single_product_summary', 'daimzap_single_product_identity', 7 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 15 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'daimzap_single_product_compatibility', 25 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'daimzap_single_product_fulfilment', 35 );
add_action( 'woocommerce_single_product_summary', 'daimzap_single_product_specs', 40 );
add_action( 'woocommerce_single_product_summary', 'daimzap_single_product_taxonomies', 45 );

// Breadcrumbs are rendered by the theme's catalog header, not by WooCommerce.
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// The gallery template overlays the sale badge on the image itself.
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

// The custom gallery renders its own thumbnails; the core callback would
// duplicate them when the `woocommerce_product_thumbnails` action fires.
remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );

/**
 * SKU and stock state, shown directly under the product title.
 *
 * @return void
 */
function daimzap_single_product_identity() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$sku = $product->get_sku();
	?>
	<div class="dz-product-identity">
		<?php if ( $sku ) : ?>
			<p class="dz-product-identity__sku">
				<span class="dz-product-identity__label"><?php esc_html_e( 'Артикул', 'daimzap' ); ?></span>
				<span class="dz-sku" data-dz-sku><?php echo esc_html( $sku ); ?></span>
			</p>
		<?php endif; ?>

		<?php daimzap_stock_badge( $product ); ?>
	</div>
	<?php
}

/**
 * Stock badge used on cards and the single product page.
 *
 * @param WC_Product $product Product object.
 * @return void
 */
function daimzap_stock_badge( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	if ( ! $product->is_in_stock() ) {
		printf(
			'<span class="dz-badge dz-badge--out">%s%s</span>',
			daimzap_get_icon( 'circle-xmark', 'dz-badge__icon' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in daimzap_get_icon().
			esc_html__( 'Нет в наличии', 'daimzap' )
		);

		return;
	}

	if ( $product->managing_stock() && $product->get_stock_quantity() !== null ) {
		$quantity = (int) $product->get_stock_quantity();
		$low      = (int) get_option( 'woocommerce_notify_low_stock_amount', 2 );

		if ( $quantity > 0 && $quantity <= $low ) {
			printf(
				'<span class="dz-badge dz-badge--low">%s%s</span>',
				daimzap_get_icon( 'triangle-exclamation', 'dz-badge__icon' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in daimzap_get_icon().
				/* translators: %d: remaining stock quantity. */
				esc_html( sprintf( __( 'Осталось %d шт.', 'daimzap' ), $quantity ) )
			);

			return;
		}

		if ( $quantity > 0 ) {
			printf(
				'<span class="dz-badge dz-badge--in">%s%s</span>',
				daimzap_get_icon( 'circle-check', 'dz-badge__icon' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in daimzap_get_icon().
				/* translators: %d: stock quantity. */
				esc_html( sprintf( __( 'В наличии: %d шт.', 'daimzap' ), $quantity ) )
			);

			return;
		}
	}

	printf(
		'<span class="dz-badge dz-badge--in">%s%s</span>',
		daimzap_get_icon( 'circle-check', 'dz-badge__icon' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in daimzap_get_icon().
		esc_html__( 'В наличии', 'daimzap' )
	);
}

/**
 * Vehicle compatibility block on the single product page.
 *
 * Renders nothing when the product carries no compatibility data, which is the
 * expected state for consumables and universal parts.
 *
 * @return void
 */
function daimzap_single_product_compatibility() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$makes  = daimzap_get_vehicle_terms( $product, 'make' );
	$models = daimzap_get_vehicle_terms( $product, 'model' );

	if ( ! $makes && ! $models ) {
		return;
	}

	$groups = array(
		array(
			'label' => __( 'Марки', 'daimzap' ),
			'terms' => $makes,
		),
		array(
			'label' => __( 'Модели', 'daimzap' ),
			'terms' => $models,
		),
	);
	?>
	<section class="dz-compat" aria-labelledby="dz-compat-title">
		<h2 class="dz-compat__title" id="dz-compat-title">
			<?php daimzap_icon( 'car-side', 'dz-compat__icon' ); ?>
			<?php esc_html_e( 'Совместимость', 'daimzap' ); ?>
		</h2>

		<?php foreach ( $groups as $group ) : ?>
			<?php if ( $group['terms'] ) : ?>
			<div class="dz-compat__group">
				<h3 class="dz-compat__group-label"><?php echo esc_html( $group['label'] ); ?></h3>
				<ul class="dz-compat__list">
					<?php foreach ( $group['terms'] as $term ) : ?>
						<li>
							<a class="dz-chip" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
								<?php echo esc_html( $term->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<p class="dz-compat__note">
			<?php esc_html_e( 'Перед покупкой сверьте артикул с деталью на вашем автомобиле или уточните совместимость у менеджера.', 'daimzap' ); ?>
		</p>
	</section>
	<?php
}

/**
 * Key specifications shown inline in the summary column.
 *
 * Only visible attributes that are not vehicle compatibility are listed here —
 * compatibility has its own block, and the full attribute table stays in the
 * "Характеристики" tab.
 *
 * @return void
 */
function daimzap_single_product_specs() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$skip = array_filter(
		array(
			daimzap_vehicle_taxonomy( 'make' ),
			daimzap_vehicle_taxonomy( 'model' ),
		)
	);

	$rows = array();

	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute->get_visible() ) {
			continue;
		}

		if ( $attribute->is_taxonomy() && in_array( $attribute->get_name(), $skip, true ) ) {
			continue;
		}

		$values = array();

		if ( $attribute->is_taxonomy() ) {
			$terms = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );

			if ( ! is_wp_error( $terms ) ) {
				$values = $terms;
			}
		} else {
			$values = $attribute->get_options();
		}

		$values = array_filter( array_map( 'trim', (array) $values ) );

		if ( ! $values ) {
			continue;
		}

		$rows[] = array(
			'label' => wc_attribute_label( $attribute->get_name(), $product ),
			'value' => implode( ', ', $values ),
		);
	}

	$rows = array_slice( $rows, 0, (int) apply_filters( 'daimzap_summary_specs_limit', 6 ) );

	if ( ! $rows ) {
		return;
	}
	?>
	<section class="dz-specs" aria-labelledby="dz-specs-title">
		<h2 class="dz-specs__title" id="dz-specs-title"><?php esc_html_e( 'Кратко о товаре', 'daimzap' ); ?></h2>
		<dl class="dz-specs__list">
			<?php foreach ( $rows as $row ) : ?>
				<div class="dz-specs__row">
					<dt><?php echo esc_html( $row['label'] ); ?></dt>
					<dd><?php echo esc_html( $row['value'] ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</section>
	<?php
}

/**
 * Product categories and tags as chips.
 *
 * Replaces the default product meta block, which is unhooked above. Keeping
 * these links on the page matters for internal linking and gives buyers a way
 * back into the part group they came from.
 *
 * @return void
 */
function daimzap_single_product_taxonomies() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$groups = array(
		array(
			'label' => __( 'Категории', 'daimzap' ),
			'terms' => get_the_terms( $product->get_id(), 'product_cat' ),
		),
		array(
			'label' => __( 'Метки', 'daimzap' ),
			'terms' => get_the_terms( $product->get_id(), 'product_tag' ),
		),
	);

	$groups = array_filter(
		$groups,
		static function ( $group ) {
			return is_array( $group['terms'] ) && $group['terms'];
		}
	);

	if ( ! $groups ) {
		return;
	}
	?>
	<div class="dz-product-taxonomies product_meta">
		<?php foreach ( $groups as $group ) : ?>
			<div class="dz-product-taxonomies__group">
				<span class="dz-product-taxonomies__label"><?php echo esc_html( $group['label'] ); ?></span>

				<ul class="dz-compat__list">
					<?php foreach ( $group['terms'] as $term ) : ?>
						<li>
							<a class="dz-chip" href="<?php echo esc_url( (string) get_term_link( $term ) ); ?>">
								<?php echo esc_html( $term->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Delivery and pickup note under the add-to-cart form.
 *
 * @return void
 */
function daimzap_single_product_fulfilment() {
	$address = daimzap_option( 'daimzap_address' );
	?>
	<ul class="dz-fulfilment">
		<li class="dz-fulfilment__item">
			<?php daimzap_icon( 'truck-fast', 'dz-fulfilment__icon' ); ?>
			<span><?php esc_html_e( 'Доставка по Грозному, Чечне и другим регионам России', 'daimzap' ); ?></span>
		</li>
		<?php if ( $address ) : ?>
			<li class="dz-fulfilment__item">
				<?php daimzap_icon( 'store', 'dz-fulfilment__icon' ); ?>
				<span>
					<?php esc_html_e( 'Самовывоз:', 'daimzap' ); ?>
					<?php echo esc_html( $address ); ?>
				</span>
			</li>
		<?php endif; ?>
		<li class="dz-fulfilment__item">
			<?php daimzap_icon( 'boxes-stacked', 'dz-fulfilment__icon' ); ?>
			<span>
				<?php esc_html_e( 'Нужен опт?', 'daimzap' ); ?>
				<a class="dz-link" href="<?php echo esc_url( daimzap_wholesale_url() ); ?>"><?php esc_html_e( 'Стать оптовиком', 'daimzap' ); ?></a>
			</span>
		</li>
	</ul>
	<?php
}

/**
 * Sale badge text used on cards and the single product page.
 *
 * Shows the actual discount when both prices are known — more useful to a buyer
 * than a generic "Sale!".
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function daimzap_get_sale_badge( $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
		return '';
	}

	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();

	if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
		$percent = (int) round( ( ( $regular - $sale ) / $regular ) * 100 );

		if ( $percent > 0 ) {
			/* translators: %d: discount percentage. */
			return sprintf( __( '−%d%%', 'daimzap' ), $percent );
		}
	}

	return __( 'Акция', 'daimzap' );
}

/**
 * Replace the default sale flash markup.
 *
 * @param string     $html    Default markup.
 * @param WP_Post    $post    Post object.
 * @param WC_Product $product Product object.
 * @return string
 */
function daimzap_sale_flash( $html, $post, $product ) {
	$label = daimzap_get_sale_badge( $product );

	if ( ! $label ) {
		return '';
	}

	return '<span class="dz-badge dz-badge--sale">' . esc_html( $label ) . '</span>';
}
add_filter( 'woocommerce_sale_flash', 'daimzap_sale_flash', 10, 3 );

/**
 * Add-to-cart button label for products that can actually be added.
 *
 * WooCommerce reuses this filter for "Read more" (out of stock) and
 * "Select options" (variable products), so those cases keep their own wording.
 *
 * @param string     $text    Button text.
 * @param WC_Product $product Product object.
 * @return string
 */
function daimzap_add_to_cart_text( $text, $product = null ) {
	if ( $product instanceof WC_Product && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_type( 'variable' ) ) {
		return __( 'В корзину', 'daimzap' );
	}

	return $text;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'daimzap_add_to_cart_text', 10, 2 );

/**
 * Add-to-cart label on the single product page.
 *
 * @param string $text Button text.
 * @return string
 */
function daimzap_single_add_to_cart_text( $text ) {
	return __( 'Добавить в корзину', 'daimzap' );
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'daimzap_single_add_to_cart_text', 10 );
