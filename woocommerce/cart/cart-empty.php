<?php
/**
 * Empty cart.
 *
 * Override of woocommerce/templates/cart/cart-empty.php.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="dz-container">
	<div class="py-8">
		<?php
		/**
		 * Hook: woocommerce_cart_is_empty.
		 */
		do_action( 'woocommerce_cart_is_empty' );

		daimzap_empty_cart_message();
		?>
	</div>
</div>
