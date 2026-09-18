<?php
/**
 * Empty state for post listings and search results.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="dz-empty">
	<?php daimzap_icon( 'magnifying-glass', 'dz-empty__icon' ); ?>

	<h2 class="dz-empty__title">
		<?php
		if ( is_search() ) {
			esc_html_e( 'Ничего не найдено', 'daimzap' );
		} else {
			esc_html_e( 'Здесь пока пусто', 'daimzap' );
		}
		?>
	</h2>

	<p class="dz-empty__text">
		<?php
		if ( is_search() ) {
			esc_html_e( 'Попробуйте изменить запрос: проверьте артикул или воспользуйтесь названием детали.', 'daimzap' );
		} else {
			esc_html_e( 'Материалы появятся здесь после публикации.', 'daimzap' );
		}
		?>
	</p>

	<a class="dz-btn dz-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php esc_html_e( 'На главную', 'daimzap' ); ?>
	</a>
</div>
