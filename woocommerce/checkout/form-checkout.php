<?php
/**
 * Checkout form.
 *
 * Override of woocommerce/templates/checkout/form-checkout.php. Single-page
 * checkout, two columns on desktop: customer details on the left, a sticky
 * order summary and payment on the right. All WooCommerce hooks and field
 * rendering are untouched, so validation and payment gateways behave normally.
 *
 * @package DaimZap
 *
 * @var WC_Checkout $checkout Checkout object.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

// Guests may not be able to check out without an account.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'Для оформления заказа необходимо войти в аккаунт.', 'daimzap' ) ) );

	return;
}
?>
<div class="dz-container">
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
		<div class="dz-checkout">
			<div class="min-w-0">
				<?php if ( $checkout->get_checkout_fields() ) : ?>
					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div class="dz-checkout__block" id="customer_details">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				<?php endif; ?>
			</div>

			<div>
				<div class="dz-summary">
					<h2 class="dz-summary__title" id="order_review_heading"><?php esc_html_e( 'Ваш заказ', 'daimzap' ); ?></h2>

					<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
				</div>
			</div>
		</div>
	</form>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
