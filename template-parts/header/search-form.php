<?php
/**
 * Product search form.
 *
 * Structured so that live search can be layered on later: the input carries a
 * stable `data-dz-search-input` hook and the form submits normally, so any
 * future autocomplete is an enhancement rather than a rebuild.
 *
 * @package DaimZap
 *
 * @var array $args Template arguments:
 *                  - `context`   header|mobile|hero|404|page|widget.
 *                  - `post_type` Override the searched post type; defaults to
 *                                products in catalog-facing contexts.
 */

defined( 'ABSPATH' ) || exit;

$context = isset( $args['context'] ) ? $args['context'] : 'header';

// Several search forms can render on one page (header, mobile, hero), so the
// label/input pairing needs a unique but deterministic id.
$search_id = wp_unique_id( 'dz-search-' . sanitize_html_class( $context ) . '-' );

// Catalog-facing forms search products; the blog sidebar and non-product result
// pages keep searching the whole site.
$catalog_contexts = array( 'header', 'mobile', 'hero', '404' );
$search_products  = daimzap_is_woocommerce_active() && in_array( $context, $catalog_contexts, true );

if ( isset( $args['post_type'] ) ) {
	$search_products = 'product' === $args['post_type'];
}

$placeholder = $search_products
	? __( 'Артикул, название детали, марка или модель', 'daimzap' )
	: __( 'Поиск по сайту', 'daimzap' );

$show_hint = $search_products && in_array( $context, array( 'header', 'hero' ), true );
?>
<form class="dz-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>">
		<?php esc_html_e( 'Поиск запчастей', 'daimzap' ); ?>
	</label>

	<div class="dz-search__row">
		<input
			class="dz-search__input"
			id="<?php echo esc_attr( $search_id ); ?>"
			type="search"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			autocomplete="off"
			data-dz-search-input
		>

		<button class="dz-search__submit" type="submit">
			<?php daimzap_icon( 'magnifying-glass' ); ?>
			<span class="dz-search__submit-label"><?php esc_html_e( 'Найти', 'daimzap' ); ?></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Найти', 'daimzap' ); ?></span>
		</button>
	</div>

	<?php if ( $search_products ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>

	<?php if ( $show_hint ) : ?>
		<p class="dz-search__hint">
			<?php esc_html_e( 'Точный артикул откроет карточку товара сразу. Можно искать и по названию детали.', 'daimzap' ); ?>
		</p>
	<?php endif; ?>
</form>
