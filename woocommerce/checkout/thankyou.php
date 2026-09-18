<?php
/**
 * Order received.
 *
 * Override of woocommerce/templates/checkout/thankyou.php. Order data is read
 * through the WC_Order CRUD API only, which keeps the template compatible with
 * High-Performance Order Storage.
 *
 * @package DaimZap
 *
 * @var WC_Order $order Order object.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="dz-container">
	<div class="woocommerce-order py-6">
		<?php if ( $order ) : ?>
			<?php if ( $order->has_status( 'failed' ) ) : ?>
				<div class="dz-alert dz-alert--error">
					<span>
						<?php esc_html_e( 'Оплата не прошла. Проверьте данные карты и попробуйте ещё раз или выберите другой способ оплаты.', 'daimzap' ); ?>
					</span>
				</div>

				<p class="flex flex-wrap gap-2">
					<a class="dz-btn dz-btn--primary" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>">
						<?php esc_html_e( 'Оплатить', 'daimzap' ); ?>
					</a>

					<?php if ( is_user_logged_in() ) : ?>
						<a class="dz-btn dz-btn--ghost" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
							<?php esc_html_e( 'Мои заказы', 'daimzap' ); ?>
						</a>
					<?php endif; ?>
				</p>
			<?php else : ?>
				<h1><?php echo esc_html( apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Спасибо, заказ принят', 'daimzap' ), $order ) ); ?></h1>

				<p class="dz-help max-w-prose mt-2">
					<?php esc_html_e( 'Менеджер свяжется с вами для подтверждения наличия и способа получения.', 'daimzap' ); ?>
				</p>

				<ul class="dz-order-overview woocommerce-order-overview">
					<li>
						<?php esc_html_e( 'Номер заказа', 'daimzap' ); ?>
						<strong><?php echo esc_html( $order->get_order_number() ); ?></strong>
					</li>

					<li>
						<?php esc_html_e( 'Дата', 'daimzap' ); ?>
						<strong><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
					</li>

					<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
						<li>
							<?php esc_html_e( 'E-mail', 'daimzap' ); ?>
							<strong><?php echo esc_html( $order->get_billing_email() ); ?></strong>
						</li>
					<?php endif; ?>

					<li>
						<?php esc_html_e( 'Сумма', 'daimzap' ); ?>
						<strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
					</li>

					<?php if ( $order->get_payment_method_title() ) : ?>
						<li>
							<?php esc_html_e( 'Оплата', 'daimzap' ); ?>
							<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
						</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
		<?php else : ?>
			<h1><?php echo esc_html( apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Спасибо, заказ принят', 'daimzap' ), null ) ); ?></h1>
		<?php endif; ?>
	</div>
</div>
