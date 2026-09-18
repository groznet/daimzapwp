<?php
/**
 * Product data tabs.
 *
 * Override of woocommerce/templates/single-product/tabs/tabs.php.
 *
 * Rendered as a real ARIA tablist. Without JavaScript every panel is visible
 * and readable, which also keeps the description and attribute table crawlable.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filter tabs and allow third parties to add their own.
 *
 * @since 2.4.0
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! $product_tabs ) {
	return;
}

$tab_index = 0;
?>
<div class="dz-tabs woocommerce-tabs wc-tabs-wrapper" data-dz-tabs>
	<ul class="dz-tabs__list wc-tabs" role="tablist">
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
			<li class="<?php echo esc_attr( $key ); ?>_tab" role="presentation">
				<button
					type="button"
					class="dz-tabs__tab"
					id="tab-title-<?php echo esc_attr( $key ); ?>"
					role="tab"
					aria-controls="tab-<?php echo esc_attr( $key ); ?>"
					aria-selected="<?php echo 0 === $tab_index ? 'true' : 'false'; ?>"
					tabindex="<?php echo 0 === $tab_index ? '0' : '-1'; ?>"
				>
					<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
				</button>
			</li>
			<?php $tab_index++; ?>
		<?php endforeach; ?>
	</ul>

	<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
		<div
			class="dz-tabs__panel dz-prose woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab"
			id="tab-<?php echo esc_attr( $key ); ?>"
			role="tabpanel"
			aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>"
		>
			<?php
			if ( isset( $product_tab['callback'] ) ) {
				call_user_func( $product_tab['callback'], $key, $product_tab );
			}
			?>
		</div>
	<?php endforeach; ?>

	<?php do_action( 'woocommerce_product_after_tabs' ); ?>
</div>
