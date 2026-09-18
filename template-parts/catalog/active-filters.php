<?php
/**
 * Active filter chips.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$active = daimzap_get_active_filters();

if ( ! $active ) {
	return;
}
?>
<div class="dz-active-filters">
	<span class="dz-active-filters__label"><?php esc_html_e( 'Выбрано:', 'daimzap' ); ?></span>

	<?php foreach ( $active as $filter ) : ?>
		<a class="dz-active-filter" href="<?php echo esc_url( $filter['remove_url'] ); ?>" rel="nofollow">
			<span class="dz-active-filter__key"><?php echo esc_html( $filter['label'] ); ?>:</span>
			<span><?php echo esc_html( $filter['value'] ); ?></span>
			<?php daimzap_icon( 'xmark', '', __( 'Убрать фильтр', 'daimzap' ) ); ?>
		</a>
	<?php endforeach; ?>

	<a class="dz-link text-sm" href="<?php echo esc_url( daimzap_clear_filters_url() ); ?>" rel="nofollow">
		<?php esc_html_e( 'Сбросить всё', 'daimzap' ); ?>
	</a>
</div>
