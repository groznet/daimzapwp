<?php
/**
 * 404 template.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();

$shop_url = daimzap_is_woocommerce_active() ? wc_get_page_permalink( 'shop' ) : '';
?>

<main id="main" class="dz-main">
	<div class="dz-container">
		<div class="dz-404">
			<p class="dz-404__code" aria-hidden="true">404</p>
			<h1><?php esc_html_e( 'Страница не найдена', 'daimzap' ); ?></h1>
			<p class="dz-empty__text mt-3">
				<?php esc_html_e( 'Возможно, страница была удалена или адрес указан с ошибкой. Найдите нужную деталь по артикулу или вернитесь в каталог.', 'daimzap' ); ?>
			</p>

			<div class="dz-404__search">
				<?php
				get_template_part(
					'template-parts/header/search-form',
					null,
					array( 'context' => '404' )
				);
				?>
			</div>

			<ul class="dz-404__links">
				<li><a class="dz-btn dz-btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'На главную', 'daimzap' ); ?></a></li>
				<?php if ( $shop_url ) : ?>
					<li><a class="dz-btn dz-btn--ghost" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'В каталог', 'daimzap' ); ?></a></li>
				<?php endif; ?>
				<li><a class="dz-btn dz-btn--ghost" href="<?php echo esc_url( daimzap_wholesale_url() ); ?>"><?php esc_html_e( 'Оптовикам', 'daimzap' ); ?></a></li>
			</ul>
		</div>
	</div>
</main>

<?php
get_footer();
