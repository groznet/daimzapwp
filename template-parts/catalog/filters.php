<?php
/**
 * Catalog filters.
 *
 * Desktop: a sidebar column. Mobile: an off-canvas drawer opened from the
 * toolbar. Every control is a plain link or a form submit, so filtering works
 * without JavaScript.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$price      = daimzap_get_price_range();
$base_url   = daimzap_catalog_base_url();
$persisted  = daimzap_current_filter_args();
$categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'number'     => 30,
	)
);

if ( is_wp_error( $categories ) ) {
	$categories = array();
}

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only filter state.
$min_price = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : '';
$max_price = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : '';
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$stock_args = $persisted;

if ( daimzap_stock_filter_active() ) {
	unset( $stock_args[ DAIMZAP_STOCK_QUERY_VAR ] );
} else {
	$stock_args[ DAIMZAP_STOCK_QUERY_VAR ] = '1';
}
?>
<aside class="dz-sidebar" id="dz-filters" data-dz-filters aria-label="<?php esc_attr_e( 'Фильтры каталога', 'daimzap' ); ?>">
	<div class="dz-sidebar__head">
		<strong><?php esc_html_e( 'Фильтры', 'daimzap' ); ?></strong>

		<button type="button" class="dz-btn dz-btn--ghost dz-btn--sm" data-dz-filters-close>
			<?php daimzap_icon( 'xmark' ); ?>
			<?php esc_html_e( 'Закрыть', 'daimzap' ); ?>
		</button>
	</div>

	<?php foreach ( array( 'make', 'model' ) as $role ) : ?>
		<?php
		$terms = daimzap_get_vehicle_terms_in_use( $role );

		if ( ! $terms ) {
			continue;
		}

		$taxonomy = daimzap_vehicle_taxonomy( $role );
		$chosen   = daimzap_get_chosen_vehicle_terms( $role );
		$list_id  = 'dz-filter-' . $role;
		?>
		<details class="dz-filter" <?php echo $chosen ? 'open' : ''; ?>>
			<summary class="dz-filter__summary">
				<?php echo esc_html( wc_attribute_label( $taxonomy ) ); ?>
			</summary>

			<div class="dz-filter__body">
				<?php if ( count( $terms ) > 8 ) : ?>
					<div class="dz-filter__search">
						<label class="screen-reader-text" for="<?php echo esc_attr( $list_id . '-search' ); ?>">
							<?php esc_html_e( 'Поиск в списке', 'daimzap' ); ?>
						</label>
						<input
							class="dz-input"
							id="<?php echo esc_attr( $list_id . '-search' ); ?>"
							type="search"
							placeholder="<?php esc_attr_e( 'Начните вводить…', 'daimzap' ); ?>"
							data-dz-filter-search="<?php echo esc_attr( $list_id ); ?>"
						>
					</div>
				<?php endif; ?>

				<ul class="dz-filter__list" id="<?php echo esc_attr( $list_id ); ?>">
					<?php foreach ( $terms as $term ) : ?>
						<?php $is_active = in_array( $term->slug, $chosen, true ); ?>
						<li>
							<a
								class="dz-filter__option<?php echo $is_active ? ' is-active' : ''; ?>"
								href="<?php echo esc_url( daimzap_toggle_filter_url( $taxonomy, $term->slug ) ); ?>"
								<?php echo $is_active ? 'aria-current="true"' : ''; ?>
								rel="nofollow"
							>
								<span class="dz-filter__option-box" aria-hidden="true"></span>
								<span class="dz-filter__option-text"><?php echo esc_html( $term->name ); ?></span>
								<span class="dz-filter__count"><?php echo esc_html( number_format_i18n( $term->count ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</details>
	<?php endforeach; ?>

	<?php if ( $categories && ! is_product_category() ) : ?>
		<details class="dz-filter" open>
			<summary class="dz-filter__summary"><?php esc_html_e( 'Категории', 'daimzap' ); ?></summary>

			<div class="dz-filter__body">
				<ul class="dz-filter__list">
					<?php foreach ( $categories as $category ) : ?>
						<li>
							<a class="dz-filter__option" href="<?php echo esc_url( get_term_link( $category ) ); ?>">
								<span class="dz-filter__option-text"><?php echo esc_html( $category->name ); ?></span>
								<span class="dz-filter__count"><?php echo esc_html( number_format_i18n( $category->count ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</details>
	<?php endif; ?>

	<details class="dz-filter" <?php echo ( '' !== $min_price || '' !== $max_price ) ? 'open' : ''; ?>>
		<summary class="dz-filter__summary"><?php esc_html_e( 'Цена', 'daimzap' ); ?></summary>

		<div class="dz-filter__body">
			<form method="get" action="<?php echo esc_url( $base_url ); ?>">
				<div class="dz-filter__price">
					<label class="screen-reader-text" for="dz-min-price"><?php esc_html_e( 'Цена от', 'daimzap' ); ?></label>
					<input
						class="dz-input"
						id="dz-min-price"
						type="number"
						name="min_price"
						inputmode="numeric"
						min="0"
						step="1"
						value="<?php echo esc_attr( $min_price ); ?>"
						placeholder="<?php echo esc_attr( $price['min'] > 0 ? (string) (int) floor( $price['min'] ) : '0' ); ?>"
					>

					<span aria-hidden="true">—</span>

					<label class="screen-reader-text" for="dz-max-price"><?php esc_html_e( 'Цена до', 'daimzap' ); ?></label>
					<input
						class="dz-input"
						id="dz-max-price"
						type="number"
						name="max_price"
						inputmode="numeric"
						min="0"
						step="1"
						value="<?php echo esc_attr( $max_price ); ?>"
						placeholder="<?php echo esc_attr( $price['max'] > 0 ? (string) (int) ceil( $price['max'] ) : '' ); ?>"
					>
				</div>

				<?php
				// Carry the rest of the catalog state through the form submit.
				foreach ( $persisted as $key => $value ) :
					if ( in_array( $key, array( 'min_price', 'max_price' ), true ) ) :
						continue;
					endif;
					?>
					<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<?php endforeach; ?>

				<div class="dz-filter__actions">
					<button class="dz-btn dz-btn--dark dz-btn--sm" type="submit"><?php esc_html_e( 'Применить', 'daimzap' ); ?></button>
				</div>
			</form>
		</div>
	</details>

	<details class="dz-filter" open>
		<summary class="dz-filter__summary"><?php esc_html_e( 'Наличие', 'daimzap' ); ?></summary>

		<div class="dz-filter__body">
			<ul class="dz-filter__list">
				<li>
					<a
						class="dz-filter__option<?php echo daimzap_stock_filter_active() ? ' is-active' : ''; ?>"
						href="<?php echo esc_url( $stock_args ? add_query_arg( $stock_args, $base_url ) : $base_url ); ?>"
						rel="nofollow"
					>
						<span class="dz-filter__option-box" aria-hidden="true"></span>
						<span class="dz-filter__option-text"><?php esc_html_e( 'Только в наличии', 'daimzap' ); ?></span>
					</a>
				</li>
			</ul>
		</div>
	</details>

	<?php if ( is_active_sidebar( 'catalog-sidebar' ) ) : ?>
		<div class="pt-4">
			<?php dynamic_sidebar( 'catalog-sidebar' ); ?>
		</div>
	<?php endif; ?>

	<div class="dz-filter__actions">
		<a class="dz-btn dz-btn--ghost dz-btn--sm dz-btn--block" href="<?php echo esc_url( daimzap_clear_filters_url() ); ?>" rel="nofollow">
			<?php esc_html_e( 'Сбросить фильтры', 'daimzap' ); ?>
		</a>
	</div>
</aside>
