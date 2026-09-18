<?php
/**
 * Header actions: account and cart.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$has_woo      = daimzap_is_woocommerce_active();
$account_url  = $has_woo ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$cart_url     = $has_woo ? wc_get_cart_url() : '';
$account_text = is_user_logged_in() ? __( 'Кабинет', 'daimzap' ) : __( 'Войти', 'daimzap' );
?>
<div class="dz-header__actions">
	<a class="dz-action" href="<?php echo esc_url( daimzap_wholesale_url() ); ?>">
		<?php daimzap_icon( 'boxes-stacked', 'dz-action__icon' ); ?>
		<span class="dz-action__label"><?php esc_html_e( 'Оптовикам', 'daimzap' ); ?></span>
		<span class="screen-reader-text"><?php esc_html_e( 'Стать оптовиком', 'daimzap' ); ?></span>
	</a>

	<?php if ( $account_url ) : ?>
		<a class="dz-action" href="<?php echo esc_url( $account_url ); ?>">
			<?php daimzap_icon( 'user', 'dz-action__icon' ); ?>
			<span class="dz-action__label"><?php echo esc_html( $account_text ); ?></span>
			<span class="screen-reader-text"><?php echo esc_html( $account_text ); ?></span>
		</a>
	<?php endif; ?>

	<?php if ( $has_woo && $cart_url ) : ?>
		<a class="dz-action" href="<?php echo esc_url( $cart_url ); ?>">
			<?php daimzap_icon( 'cart-shopping', 'dz-action__icon' ); ?>
			<span class="dz-action__label"><?php esc_html_e( 'Корзина', 'daimzap' ); ?></span>
			<?php daimzap_cart_count(); ?>
		</a>
	<?php endif; ?>
</div>
