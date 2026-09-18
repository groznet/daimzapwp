<?php
/**
 * Product gallery.
 *
 * Override of woocommerce/templates/single-product/product-image.php.
 *
 * A deliberately small custom gallery: every image is rendered into the markup
 * and all but the first are hidden, so the first image shows and the thumbnails
 * stay usable without JavaScript. This is why the theme does not declare
 * support for `wc-product-gallery-zoom/lightbox/slider` — flexslider,
 * photoswipe and jquery-zoom never load.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

global $product;

$post_thumbnail_id = $product->get_image_id();
$attachment_ids    = $product->get_gallery_image_ids();
$image_ids         = array_values( array_filter( array_merge( array( $post_thumbnail_id ), $attachment_ids ) ) );
$image_size        = apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single' );
$full_size         = apply_filters( 'woocommerce_gallery_full_size', 'full' );
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array( 'dz-gallery', 'woocommerce-product-gallery' )
);
?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-dz-gallery>
	<div class="dz-gallery__stage">
		<div class="dz-gallery__flags">
			<?php
			/**
			 * Hook: woocommerce_product_thumbnails is fired below; the sale flash
			 * is rendered here so it overlays the gallery.
			 */
			woocommerce_show_product_sale_flash();
			?>
		</div>

		<?php if ( ! $image_ids ) : ?>
			<div class="dz-gallery__image">
				<?php echo wp_kses_post( wc_placeholder_img( $image_size ) ); ?>
			</div>
		<?php else : ?>
			<?php foreach ( $image_ids as $index => $image_id ) : ?>
				<div class="dz-gallery__image" data-dz-gallery-slide <?php echo 0 === $index ? '' : 'hidden'; ?>>
					<?php
					echo wp_get_attachment_image(
						$image_id,
						$image_size,
						false,
						array(
							'class'         => 'dz-product-image',
							'sizes'         => '(max-width: 1024px) 100vw, 640px',
							'loading'       => 0 === $index ? 'eager' : 'lazy',
							'fetchpriority' => 0 === $index ? 'high' : 'auto',
						)
					);
					?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<?php if ( count( $image_ids ) > 1 ) : ?>
		<ul class="dz-gallery__thumbs">
			<?php foreach ( $image_ids as $index => $image_id ) : ?>
				<li>
					<a
						class="dz-gallery__thumb"
						href="<?php echo esc_url( (string) wp_get_attachment_image_url( $image_id, $full_size ) ); ?>"
						aria-current="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						data-dz-gallery-thumb
					>
						<?php
						echo wp_get_attachment_image(
							$image_id,
							'woocommerce_gallery_thumbnail',
							false,
							array(
								'alt'     => sprintf(
									/* translators: %d: image number. */
									esc_attr__( 'Фото %d', 'daimzap' ),
									$index + 1
								),
								'loading' => 'lazy',
							)
						);
						?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_product_thumbnails.
	 *
	 * Kept for extensions; the theme renders its own thumbnails above.
	 */
	do_action( 'woocommerce_product_thumbnails' );
	?>
</div>
