<?php
/**
 * Empty catalog state.
 *
 * Override of woocommerce/templates/loop/no-products-found.php.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$has_filters = (bool) daimzap_get_active_filters();
?>
<div class="dz-empty">
	<?php daimzap_icon( 'box-open', 'dz-empty__icon' ); ?>

	<h2 class="dz-empty__title"><?php esc_html_e( 'Товары не найдены', 'daimzap' ); ?></h2>

	<p class="dz-empty__text">
		<?php if ( $has_filters ) : ?>
			<?php esc_html_e( 'По выбранным фильтрам ничего нет. Попробуйте убрать часть условий или поискать по артикулу.', 'daimzap' ); ?>
		<?php else : ?>
			<?php esc_html_e( 'В этом разделе пока нет товаров. Воспользуйтесь поиском по артикулу или выберите другую категорию.', 'daimzap' ); ?>
		<?php endif; ?>
	</p>

	<?php if ( $has_filters ) : ?>
		<a class="dz-btn dz-btn--dark" href="<?php echo esc_url( daimzap_clear_filters_url() ); ?>" rel="nofollow">
			<?php esc_html_e( 'Сбросить фильтры', 'daimzap' ); ?>
		</a>
	<?php else : ?>
		<a class="dz-btn dz-btn--dark" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
			<?php esc_html_e( 'Весь каталог', 'daimzap' ); ?>
		</a>
	<?php endif; ?>
</div>
