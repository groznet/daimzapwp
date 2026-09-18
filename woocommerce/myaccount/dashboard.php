<?php
/**
 * My Account dashboard.
 *
 * Override of woocommerce/templates/myaccount/dashboard.php.
 *
 * @package DaimZap
 *
 * @var string $current_user Display name of the logged in customer.
 */

defined( 'ABSPATH' ) || exit;

$customer = wp_get_current_user();
?>
<h1 class="mb-2">
	<?php
	printf(
		/* translators: %s: customer name. */
		esc_html__( 'Здравствуйте, %s', 'daimzap' ),
		esc_html( $customer->display_name )
	);
	?>
</h1>

<p class="dz-help">
	<?php esc_html_e( 'Здесь собраны ваши заказы, адреса доставки и личные данные.', 'daimzap' ); ?>
</p>

<div class="dz-account-cards">
	<div class="dz-account-card">
		<h2 class="dz-account-card__title"><?php esc_html_e( 'Заказы', 'daimzap' ); ?></h2>
		<p class="dz-account-card__text"><?php esc_html_e( 'История заказов, статусы и детали каждой покупки.', 'daimzap' ); ?></p>
		<a class="dz-btn dz-btn--ghost dz-btn--sm" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
			<?php esc_html_e( 'Открыть', 'daimzap' ); ?>
		</a>
	</div>

	<div class="dz-account-card">
		<h2 class="dz-account-card__title"><?php esc_html_e( 'Адреса', 'daimzap' ); ?></h2>
		<p class="dz-account-card__text"><?php esc_html_e( 'Адреса для выставления счёта и доставки.', 'daimzap' ); ?></p>
		<a class="dz-btn dz-btn--ghost dz-btn--sm" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>">
			<?php esc_html_e( 'Открыть', 'daimzap' ); ?>
		</a>
	</div>

	<div class="dz-account-card">
		<h2 class="dz-account-card__title"><?php esc_html_e( 'Оптовое сотрудничество', 'daimzap' ); ?></h2>
		<p class="dz-account-card__text"><?php esc_html_e( 'Отправьте заявку, чтобы получить условия для оптовых закупок.', 'daimzap' ); ?></p>
		<a class="dz-btn dz-btn--ghost dz-btn--sm" href="<?php echo esc_url( daimzap_wholesale_url() ); ?>">
			<?php esc_html_e( 'Подробнее', 'daimzap' ); ?>
		</a>
	</div>
</div>

<?php
/**
 * Hook: woocommerce_account_dashboard.
 */
do_action( 'woocommerce_account_dashboard' );
