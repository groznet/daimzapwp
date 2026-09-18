<?php
/**
 * Account orders list.
 *
 * Override of woocommerce/templates/myaccount/orders.php. Orders are read
 * through the WC_Order API, so the template works with High-Performance Order
 * Storage without change.
 *
 * @package DaimZap
 *
 * @var stdClass $customer_orders Orders result object.
 * @var bool     $has_orders      Whether any orders exist.
 * @var int      $current_page    Current page of the orders list.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>
<h1 class="mb-4"><?php esc_html_e( 'Мои заказы', 'daimzap' ); ?></h1>

<?php if ( $has_orders ) : ?>
	<div class="dz-orders woocommerce-orders-table">
		<?php foreach ( $customer_orders->orders as $customer_order ) : ?>
			<?php
			$order    = wc_get_order( $customer_order );
			$item_qty = $order->get_item_count() - $order->get_item_count_refunded();
			?>
			<div class="dz-order-row">
				<div>
					<span class="dz-order-row__label"><?php esc_html_e( 'Заказ', 'daimzap' ); ?></span>
					<a class="dz-link" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
						#<?php echo esc_html( $order->get_order_number() ); ?>
					</a>
				</div>

				<div>
					<span class="dz-order-row__label"><?php esc_html_e( 'Дата', 'daimzap' ); ?></span>
					<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
						<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
					</time>
				</div>

				<div>
					<span class="dz-order-row__label"><?php esc_html_e( 'Статус', 'daimzap' ); ?></span>
					<span class="dz-order-status"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
				</div>

				<div>
					<span class="dz-order-row__label"><?php esc_html_e( 'Сумма', 'daimzap' ); ?></span>
					<?php
					printf(
						/* translators: 1: formatted order total, 2: number of items. */
						esc_html__( '%1$s за %2$s', 'daimzap' ),
						wp_kses_post( $order->get_formatted_order_total() ),
						esc_html( sprintf( _n( '%s товар', '%s товаров', $item_qty, 'daimzap' ), number_format_i18n( $item_qty ) ) )
					);
					?>
				</div>

				<div>
					<a class="dz-btn dz-btn--ghost dz-btn--sm" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
						<?php esc_html_e( 'Открыть', 'daimzap' ); ?>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination flex gap-2 mt-6">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="dz-btn dz-btn--ghost dz-btn--sm" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>">
					<?php esc_html_e( 'Назад', 'daimzap' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="dz-btn dz-btn--ghost dz-btn--sm" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>">
					<?php esc_html_e( 'Вперёд', 'daimzap' ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php else : ?>
	<div class="dz-empty">
		<?php daimzap_icon( 'box', 'dz-empty__icon' ); ?>
		<h2 class="dz-empty__title"><?php esc_html_e( 'Заказов пока нет', 'daimzap' ); ?></h2>
		<p class="dz-empty__text"><?php esc_html_e( 'Оформленные заказы появятся здесь вместе со статусами и составом.', 'daimzap' ); ?></p>
		<a class="dz-btn dz-btn--dark" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
			<?php esc_html_e( 'Перейти в каталог', 'daimzap' ); ?>
		</a>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
