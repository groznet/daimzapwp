<?php
/**
 * Selected products on the home page.
 *
 * Prefers featured products and falls back to the newest ones, so the section
 * is useful immediately after an import and curatable later.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

if ( ! daimzap_is_woocommerce_active() ) {
	return;
}

$limit    = 8;
$featured = wc_get_featured_product_ids();
$shop_url = wc_get_page_permalink( 'shop' );

$query_args = array(
	'status'  => 'publish',
	'limit'   => $limit,
	'orderby' => 'date',
	'order'   => 'DESC',
	'return'  => 'ids',
);

if ( $featured ) {
	$query_args['include'] = array_slice( $featured, 0, $limit );
	$query_args['orderby'] = 'include';
	$title                 = __( 'Рекомендуемые товары', 'daimzap' );
} else {
	$title = __( 'Новые поступления', 'daimzap' );
}

$product_ids = wc_get_products( $query_args );

if ( ! $product_ids ) {
	return;
}
?>
<section class="dz-section pt-0">
	<div class="dz-container">
		<div class="dz-section__head">
			<h2 class="dz-section__title"><?php echo esc_html( $title ); ?></h2>

			<?php if ( $shop_url ) : ?>
				<a class="dz-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Смотреть все', 'daimzap' ); ?></a>
			<?php endif; ?>
		</div>

		<ul class="dz-products">
			<?php
			global $post, $product;

			$original_post    = $post;
			$original_product = $product;

			foreach ( $product_ids as $product_id ) {
				$post_object = get_post( $product_id );

				if ( ! $post_object ) {
					continue;
				}

				// Set up the globals WooCommerce loop templates rely on.
				$post = $post_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.
				setup_postdata( $post );
				$product = wc_get_product( $product_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.

				if ( $product instanceof WC_Product && $product->is_visible() ) {
					wc_get_template_part( 'content', 'product' );
				}
			}

			$post    = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring.
			$product = $original_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring.

			wp_reset_postdata();
			?>
		</ul>
	</div>
</section>
