<?php
/**
 * Popular product categories.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

if ( ! daimzap_is_woocommerce_active() ) {
	return;
}

$terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'number'     => 12,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);

if ( is_wp_error( $terms ) || ! $terms ) {
	return;
}

$shop_url = wc_get_page_permalink( 'shop' );
?>
<section class="dz-section">
	<div class="dz-container">
		<div class="dz-section__head">
			<h2 class="dz-section__title"><?php esc_html_e( 'Категории запчастей', 'daimzap' ); ?></h2>

			<?php if ( $shop_url ) : ?>
				<a class="dz-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Весь каталог', 'daimzap' ); ?></a>
			<?php endif; ?>
		</div>

		<ul class="dz-tiles">
			<?php foreach ( $terms as $term ) : ?>
				<?php $thumbnail_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true ); ?>
				<li>
					<a class="dz-tile" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
						<?php if ( $thumbnail_id ) : ?>
							<span class="dz-tile__media">
								<?php echo wp_get_attachment_image( $thumbnail_id, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
							</span>
						<?php endif; ?>

						<span class="dz-tile__name"><?php echo esc_html( $term->name ); ?></span>
						<span class="dz-tile__count">
							<?php
							printf(
								/* translators: %s: number of products. */
								esc_html( _n( '%s товар', '%s товаров', (int) $term->count, 'daimzap' ) ),
								esc_html( number_format_i18n( $term->count ) )
							);
							?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
