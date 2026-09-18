<?php
/**
 * DaimZap product card.
 *
 * Override of woocommerce/templates/content-product.php.
 *
 * Information hierarchy, top to bottom: image → name → SKU → compatibility →
 * stock → price → add to cart. Nothing else is shown; ratings and excerpts are
 * noise in a parts catalog.
 *
 * WooCommerce's own loop hooks are unhooked in inc/woocommerce/hooks.php but
 * still fired here so extensions can inject into the card.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$sku       = $product->get_sku();
$compat    = daimzap_get_compatibility_summary( $product );
$in_stock  = $product->is_in_stock();
$permalink = get_permalink( $product->get_id() );
$classes   = array( 'dz-card' );

if ( ! $in_stock ) {
	$classes[] = 'dz-card--out';
}
?>
<li <?php wc_product_class( implode( ' ', $classes ), $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * The default product-link opener is unhooked: the card uses a stretched
	 * link on the title instead, so the add-to-cart button stays clickable.
	 */
	do_action( 'woocommerce_before_shop_loop_item' );
	?>

	<div class="dz-card__flags">
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - removed, rendered below
		 */
		do_action( 'woocommerce_before_shop_loop_item_title' );

		// Re-rendered here so the badge sits in the card's flag column.
		woocommerce_show_product_loop_sale_flash();
		?>
	</div>

	<a class="dz-card__media" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
		<?php
		echo wp_kses_post(
			$product->get_image(
				'woocommerce_thumbnail',
				array(
					'class' => 'dz-product-image',
					'sizes' => '(max-width: 640px) 45vw, (max-width: 1024px) 30vw, 300px',
				)
			)
		);
		?>
	</a>

	<div class="dz-card__body">
		<?php if ( $sku ) : ?>
			<p class="dz-card__sku">
				<span class="screen-reader-text"><?php esc_html_e( 'Артикул:', 'daimzap' ); ?> </span>
				<?php echo esc_html( $sku ); ?>
			</p>
		<?php endif; ?>

		<h2 class="dz-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h2>

		<?php
		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 */
		do_action( 'woocommerce_shop_loop_item_title' );
		?>

		<?php if ( $compat ) : ?>
			<p class="dz-card__compat" title="<?php echo esc_attr( $compat ); ?>">
				<?php daimzap_icon( 'car-side', 'dz-card__compat-icon' ); ?>
				<span><?php echo esc_html( $compat ); ?></span>
			</p>
		<?php endif; ?>

		<?php daimzap_stock_badge( $product ); ?>

		<?php
		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );
		?>

		<div class="dz-card__foot">
			<?php if ( $product->get_price_html() ) : ?>
				<span class="dz-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			<?php else : ?>
				<span class="dz-card__price-request"><?php esc_html_e( 'Цена по запросу', 'daimzap' ); ?></span>
			<?php endif; ?>

			<div class="dz-card__action">
				<?php woocommerce_template_loop_add_to_cart(); ?>
			</div>
		</div>
	</div>

	<?php
	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
