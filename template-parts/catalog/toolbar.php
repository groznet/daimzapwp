<?php
/**
 * Catalog toolbar: result count, mobile filter trigger and sorting.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$counters = daimzap_loop_counters();
$total    = $counters['total'];
$per_page = $counters['per_page'];
$current  = $counters['current_page'];
$first    = ( $per_page * $current ) - $per_page + 1;
$last     = min( $total, $per_page * $current );

$catalog_orderby_options = apply_filters(
	'woocommerce_catalog_orderby',
	array(
		'menu_order' => __( 'По умолчанию', 'daimzap' ),
		'date'       => __( 'Сначала новые', 'daimzap' ),
		'price'      => __( 'Цена: по возрастанию', 'daimzap' ),
		'price-desc' => __( 'Цена: по убыванию', 'daimzap' ),
	)
);

$default_orderby = wc_get_loop_prop( 'is_search' ) ? 'relevance' : get_option( 'woocommerce_default_catalog_orderby', 'menu_order' );

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only sorting state.
$orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby;
?>
<div class="dz-toolbar">
	<?php if ( $total > 0 ) : ?>
		<p class="dz-toolbar__count">
			<?php
			if ( 1 === $total ) {
				esc_html_e( 'Показан 1 товар', 'daimzap' );
			} elseif ( $total <= $per_page || -1 === $per_page ) {
				printf(
					/* translators: %s: total number of products. */
					esc_html( _n( 'Показан %s товар', 'Показано товаров: %s', $total, 'daimzap' ) ),
					esc_html( number_format_i18n( $total ) )
				);
			} else {
				printf(
					/* translators: 1: first result, 2: last result, 3: total results. */
					esc_html__( 'Показано %1$s–%2$s из %3$s', 'daimzap' ),
					esc_html( number_format_i18n( $first ) ),
					esc_html( number_format_i18n( $last ) ),
					esc_html( number_format_i18n( $total ) )
				);
			}
			?>
		</p>
	<?php endif; ?>

	<div class="dz-toolbar__controls">
		<button
			type="button"
			class="dz-btn dz-btn--ghost dz-btn--sm dz-toolbar__filter-btn"
			aria-expanded="false"
			aria-controls="dz-filters"
			data-dz-filters-open
		>
			<?php daimzap_icon( 'sliders' ); ?>
			<?php esc_html_e( 'Фильтры', 'daimzap' ); ?>
		</button>

		<form class="dz-toolbar__sort" method="get" data-dz-auto-submit>
			<label class="screen-reader-text" for="dz-orderby"><?php esc_html_e( 'Сортировка', 'daimzap' ); ?></label>

			<select class="dz-select" name="orderby" id="dz-orderby">
				<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>>
						<?php echo esc_html( $name ); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<button class="dz-btn dz-btn--ghost dz-btn--sm" type="submit" data-dz-submit>
				<?php esc_html_e( 'Применить', 'daimzap' ); ?>
			</button>

			<?php
			// Preserve the rest of the catalog state when sorting changes.
			foreach ( daimzap_current_filter_args() as $key => $value ) :
				if ( 'orderby' === $key ) :
					continue;
				endif;
				?>
				<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
			<?php endforeach; ?>
		</form>
	</div>
</div>
