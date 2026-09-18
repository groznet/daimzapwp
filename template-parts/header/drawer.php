<?php
/**
 * Mobile navigation drawer.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$phone = daimzap_option( 'daimzap_phone' );
?>
<div class="dz-drawer" id="dz-drawer" data-dz-drawer aria-hidden="true" aria-label="<?php esc_attr_e( 'Мобильное меню', 'daimzap' ); ?>">
	<div class="dz-drawer__head">
		<span class="dz-brand__name">Daim<span class="dz-brand__mark">Zap</span></span>

		<button type="button" class="dz-burger" data-dz-menu-close>
			<?php daimzap_icon( 'xmark', '', __( 'Закрыть меню', 'daimzap' ) ); ?>
		</button>
	</div>

	<nav class="dz-drawer__body" aria-label="<?php esc_attr_e( 'Мобильное меню', 'daimzap' ); ?>">
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'dz-menu',
					'depth'          => 2,
					'walker'         => new Daimzap_Nav_Walker(),
				)
			);
			?>
		<?php else : ?>
			<p class="dz-help"><?php esc_html_e( 'Меню не назначено. Создайте меню в разделе «Внешний вид → Меню».', 'daimzap' ); ?></p>
		<?php endif; ?>
	</nav>

	<div class="dz-drawer__foot">
		<a class="dz-btn dz-btn--primary dz-btn--block" href="<?php echo esc_url( daimzap_wholesale_url() ); ?>">
			<?php esc_html_e( 'Стать оптовиком', 'daimzap' ); ?>
		</a>

		<?php if ( $phone ) : ?>
			<a class="dz-btn dz-btn--ghost dz-btn--block" href="<?php echo esc_url( daimzap_phone_href( $phone ) ); ?>">
				<?php daimzap_icon( 'phone' ); ?>
				<?php echo esc_html( $phone ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
