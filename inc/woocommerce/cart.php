<?php
/**
 * Cart behaviour.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the header cart count.
 *
 * Rendered server-side so the first paint is correct, and wrapped in the
 * fragment container so WooCommerce refreshes it after AJAX add-to-cart.
 *
 * @return void
 */
function daimzap_cart_count() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	?>
	<span class="dz-cart-count<?php echo $count > 0 ? ' is-filled' : ''; ?>" data-dz-cart-count>
		<?php echo esc_html( (string) $count ); ?>
		<span class="screen-reader-text">
			<?php
			/* translators: %d: number of items in the cart. */
			echo esc_html( sprintf( _n( 'товар в корзине', 'товаров в корзине', $count, 'daimzap' ), $count ) );
			?>
		</span>
	</span>
	<?php
}

/**
 * Refresh the header cart count through WooCommerce cart fragments.
 *
 * @param array $fragments Cart fragments.
 * @return array
 */
function daimzap_cart_count_fragment( $fragments ) {
	ob_start();
	daimzap_cart_count();
	$fragments['[data-dz-cart-count]'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'daimzap_cart_count_fragment' );

/**
 * Cart subtotal for the header, refreshed with the same fragment mechanism.
 *
 * @return void
 */
function daimzap_cart_total() {
	$total = WC()->cart ? WC()->cart->get_cart_subtotal() : '';
	?>
	<span class="dz-cart-total" data-dz-cart-total><?php echo wp_kses_post( $total ); ?></span>
	<?php
}

/**
 * Register the cart total fragment.
 *
 * @param array $fragments Cart fragments.
 * @return array
 */
function daimzap_cart_total_fragment( $fragments ) {
	ob_start();
	daimzap_cart_total();
	$fragments['[data-dz-cart-total]'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'daimzap_cart_total_fragment' );

/**
 * Keep shoppers in the catalog after adding an item.
 *
 * @param string $url Continue shopping URL.
 * @return string
 */
function daimzap_continue_shopping_url( $url ) {
	$shop_id = wc_get_page_id( 'shop' );

	return $shop_id > 0 ? (string) get_permalink( $shop_id ) : $url;
}
add_filter( 'woocommerce_continue_shopping_redirect', 'daimzap_continue_shopping_url' );

/**
 * Show the product SKU in cart and checkout line items.
 *
 * The article number is how this catalogue's customers verify they ordered the
 * right part, so it belongs next to the name in every order summary.
 *
 * @param string $name       Item name markup.
 * @param array  $cart_item  Cart item.
 * @param string $cart_item_key Cart item key.
 * @return string
 */
function daimzap_cart_item_name_with_sku( $name, $cart_item, $cart_item_key = '' ) {
	$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;

	if ( ! $product instanceof WC_Product ) {
		return $name;
	}

	$sku = $product->get_sku();

	if ( ! $sku ) {
		return $name;
	}

	return $name . '<span class="dz-cart-item__sku"><span class="screen-reader-text">' . esc_html__( 'Артикул:', 'daimzap' ) . ' </span>' . esc_html( $sku ) . '</span>';
}
add_filter( 'woocommerce_cart_item_name', 'daimzap_cart_item_name_with_sku', 10, 3 );

/**
 * Empty-cart message that points somewhere useful.
 *
 * @return void
 */
function daimzap_empty_cart_message() {
	$shop_id  = wc_get_page_id( 'shop' );
	$shop_url = $shop_id > 0 ? get_permalink( $shop_id ) : home_url( '/' );
	?>
	<div class="dz-empty">
		<?php daimzap_icon( 'cart-shopping', 'dz-empty__icon' ); ?>
		<h2 class="dz-empty__title"><?php esc_html_e( 'Корзина пуста', 'daimzap' ); ?></h2>
		<p class="dz-empty__text"><?php esc_html_e( 'Найдите нужную деталь по артикулу, марке или модели автомобиля.', 'daimzap' ); ?></p>
		<a class="dz-btn dz-btn--primary" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Перейти в каталог', 'daimzap' ); ?></a>
	</div>
	<?php
}
