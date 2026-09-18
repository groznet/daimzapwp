<?php
/**
 * Cart page.
 *
 * Override of woocommerce/templates/cart/cart.php. The default table becomes a
 * responsive grid list; all WooCommerce hooks, nonces and field names are
 * preserved so cart updates, coupons and quantity changes behave normally.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>
<div class="dz-container">
	<div class="dz-cart">
		<div class="min-w-0">
			<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
				<?php do_action( 'woocommerce_before_cart_table' ); ?>

				<div class="dz-cart-items">
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							continue;
						}

						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						?>
						<div class="dz-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
							<div class="dz-cart-item__media">
								<?php
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_gallery_thumbnail' ), $cart_item, $cart_item_key );

								if ( $product_permalink ) {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
								} else {
									echo wp_kses_post( $thumbnail );
								}
								?>
							</div>

							<div class="min-w-0">
								<p class="dz-cart-item__name">
									<?php
									if ( $product_permalink ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
									}

									do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

									/*
									 * The formatted item data (variation attributes and custom item
									 * meta) is deliberately not printed here: the cart row is kept to
									 * name, article number, price and quantity. Anything a customer
									 * needs beyond that belongs on the product page.
									 */

									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Доступно под заказ', 'daimzap' ) . '</p>', $product_id ) );
									}
									?>
								</p>
							</div>

							<div class="dz-cart-item__meta">
								<div class="dz-cart-item__price">
									<span class="dz-order-row__label"><?php esc_html_e( 'Цена', 'daimzap' ); ?></span>
									<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) ); ?>
								</div>

								<div class="dz-cart-item__qty">
									<?php
									if ( $_product->is_sold_individually() ) {
										$min_quantity = 1;
										$max_quantity = 1;
									} else {
										$min_quantity = 0;
										$max_quantity = $_product->get_max_purchase_quantity();
									}

									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $max_quantity,
											'min_value'    => $min_quantity,
											'product_name' => $_product->get_name(),
										),
										$_product,
										false
									);

									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WooCommerce.
									?>
								</div>

								<div class="dz-cart-item__subtotal">
									<span class="dz-order-row__label"><?php esc_html_e( 'Сумма', 'daimzap' ); ?></span>
									<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) ); ?>
								</div>

								<div>
									<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WooCommerce.
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="dz-cart-item__remove remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><span aria-hidden="true">&times;</span></a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											/* translators: %s: product name. */
											esc_attr( sprintf( __( 'Удалить «%s» из корзины', 'daimzap' ), $_product->get_name() ) ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$cart_item_key
									);
									?>
								</div>
							</div>
						</div>
						<?php
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>
					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</div>

				<div class="dz-cart-actions">
					<?php if ( wc_coupons_enabled() ) : ?>
						<div class="dz-cart-coupon coupon">
							<label class="screen-reader-text" for="coupon_code"><?php esc_html_e( 'Промокод', 'daimzap' ); ?></label>
							<input type="text" name="coupon_code" class="dz-input input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Промокод', 'daimzap' ); ?>">
							<button type="submit" class="dz-btn dz-btn--ghost" name="apply_coupon" value="<?php esc_attr_e( 'Применить', 'daimzap' ); ?>">
								<?php esc_html_e( 'Применить', 'daimzap' ); ?>
							</button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php endif; ?>

					<button type="submit" class="dz-btn dz-btn--ghost" name="update_cart" value="<?php esc_attr_e( 'Пересчитать', 'daimzap' ); ?>">
						<?php esc_html_e( 'Пересчитать', 'daimzap' ); ?>
					</button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>

				<?php do_action( 'woocommerce_after_cart_table' ); ?>
			</form>

			<p class="mt-4">
				<a class="dz-link" href="<?php echo esc_url( apply_filters( 'woocommerce_continue_shopping_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<?php esc_html_e( 'Продолжить покупки', 'daimzap' ); ?>
				</a>
			</p>
		</div>

		<div class="cart-collaterals">
			<?php
			/**
			 * Hook: woocommerce_cart_collaterals.
			 *
			 * @hooked woocommerce_cart_totals - 10
			 */
			do_action( 'woocommerce_cart_collaterals' );
			?>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
