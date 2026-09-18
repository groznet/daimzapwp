<?php
/**
 * Related products.
 *
 * Override of woocommerce/templates/single-product/related.php.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

if ( ! $related_products ) {
	return;
}

$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Похожие запчасти', 'daimzap' ) );
?>
<section class="dz-related related products">
	<div class="dz-section__head">
		<h2 class="dz-section__title"><?php echo esc_html( $heading ); ?></h2>
	</div>

	<?php
	woocommerce_product_loop_start();

	foreach ( $related_products as $related_product ) {
		$post_object = get_post( $related_product->get_id() );

		setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found -- Matches WooCommerce core's own related.php.

		wc_get_template_part( 'content', 'product' );
	}

	woocommerce_product_loop_end();
	?>
</section>
<?php
wp_reset_postdata();
