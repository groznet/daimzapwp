<?php
/**
 * Single product layout.
 *
 * Override of woocommerce/templates/content-single-product.php. The two-column
 * grid replaces WooCommerce's float-based layout; all hooks are preserved.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core markup.

	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
	<div class="dz-container">
		<?php daimzap_breadcrumb(); ?>

		<div class="dz-product">
			<div class="min-w-0">
				<?php
				/**
				 * Hook: woocommerce_before_single_product_summary.
				 *
				 * @hooked woocommerce_show_product_sale_flash - 10
				 * @hooked woocommerce_show_product_images - 20
				 */
				do_action( 'woocommerce_before_single_product_summary' );
				?>
			</div>

			<div class="dz-product__summary summary entry-summary">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked daimzap_single_product_identity - 7
				 * @hooked woocommerce_template_single_price - 15
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked daimzap_single_product_compatibility - 25
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked daimzap_single_product_fulfilment - 35
				 * @hooked daimzap_single_product_specs - 40
				 */
				do_action( 'woocommerce_single_product_summary' );
				?>
			</div>
		</div>

		<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div>
</div>

<?php
/**
 * Hook: woocommerce_after_single_product.
 */
do_action( 'woocommerce_after_single_product' );
