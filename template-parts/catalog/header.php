<?php
/**
 * Catalog header: breadcrumbs, title, description and subcategory shortcuts.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$term        = is_product_taxonomy() ? get_queried_object() : null;
$title       = woocommerce_page_title( false );
$description = '';
$subcats     = array();

if ( $term instanceof WP_Term ) {
	$description = term_description( $term );

	$children = get_terms(
		array(
			'taxonomy'   => $term->taxonomy,
			'parent'     => $term->term_id,
			'hide_empty' => true,
		)
	);

	if ( ! is_wp_error( $children ) ) {
		$subcats = $children;
	}
} elseif ( is_shop() ) {
	$shop_page_id = wc_get_page_id( 'shop' );
	$shop_page    = $shop_page_id > 0 ? get_post( $shop_page_id ) : null;

	if ( $shop_page instanceof WP_Post && $shop_page->post_excerpt ) {
		$description = wpautop( wp_kses_post( $shop_page->post_excerpt ) );
	}

	$parents = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'number'     => 12,
		)
	);

	if ( ! is_wp_error( $parents ) ) {
		$subcats = $parents;
	}
}

$counters = daimzap_loop_counters();
$total    = $counters['total'];
?>
<div class="dz-catalog-header">
	<div class="dz-container">
		<?php daimzap_breadcrumb(); ?>

		<h1 class="dz-catalog-header__title"><?php echo esc_html( $title ); ?></h1>

		<?php if ( $total > 0 ) : ?>
			<p class="dz-catalog-header__meta">
				<?php
				printf(
					/* translators: %s: number of products. */
					esc_html( _n( '%s товар в каталоге', '%s товаров в каталоге', $total, 'daimzap' ) ),
					esc_html( number_format_i18n( $total ) )
				);
				?>
			</p>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<div class="dz-catalog-header__description dz-prose"><?php echo wp_kses_post( $description ); ?></div>
		<?php endif; ?>

		<?php if ( $subcats ) : ?>
			<ul class="dz-subcats">
				<?php foreach ( $subcats as $subcat ) : ?>
					<li>
						<a class="dz-chip" href="<?php echo esc_url( get_term_link( $subcat ) ); ?>">
							<?php echo esc_html( $subcat->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
