<?php
/**
 * My Account behaviour.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Account menu items in a useful order with clear Russian labels.
 *
 * Downloads are removed: DaimZap sells physical parts only.
 *
 * @param array $items Menu items.
 * @return array
 */
function daimzap_account_menu_items( $items ) {
	unset( $items['downloads'] );

	$ordered = array(
		'dashboard'       => __( 'Обзор', 'daimzap' ),
		'orders'          => __( 'Мои заказы', 'daimzap' ),
		'edit-address'    => __( 'Адреса', 'daimzap' ),
		'edit-account'    => __( 'Личные данные', 'daimzap' ),
		'customer-logout' => __( 'Выйти', 'daimzap' ),
	);

	$result = array();

	foreach ( $ordered as $key => $label ) {
		if ( isset( $items[ $key ] ) ) {
			$result[ $key ] = $label;
			unset( $items[ $key ] );
		}
	}

	// Anything a plugin added stays, just after the theme's own ordering.
	return array_merge( $result, $items );
}
add_filter( 'woocommerce_account_menu_items', 'daimzap_account_menu_items', 20 );

/**
 * Font Awesome icon name for an account menu endpoint.
 *
 * @param string $endpoint Endpoint key.
 * @return string
 */
function daimzap_account_menu_icon( $endpoint ) {
	$icons = array(
		'dashboard'       => 'gauge-high',
		'orders'          => 'box',
		'edit-address'    => 'location-dot',
		'edit-account'    => 'user',
		'customer-logout' => 'right-from-bracket',
	);

	return isset( $icons[ $endpoint ] ) ? $icons[ $endpoint ] : 'circle-dot';
}

/**
 * Orders-per-page on the account orders list.
 *
 * @return int
 */
function daimzap_account_orders_per_page() {
	return (int) apply_filters( 'daimzap_account_orders_per_page', 10 );
}

/**
 * Limit the account orders query.
 *
 * Uses the documented query args filter so the lookup goes through the order
 * data store and stays compatible with High-Performance Order Storage — the
 * theme never queries order posts directly.
 *
 * @param array $args Order query args.
 * @return array
 */
function daimzap_account_orders_query( $args ) {
	$args['limit'] = daimzap_account_orders_per_page();

	return $args;
}
add_filter( 'woocommerce_my_account_my_orders_query', 'daimzap_account_orders_query' );

/**
 * Registration and login form copy aimed at this store's two audiences.
 *
 * @return void
 */
function daimzap_account_form_intro() {
	?>
	<p class="dz-account-intro">
		<?php esc_html_e( 'Для розничных покупок регистрация не нужна — оформить заказ можно без аккаунта.', 'daimzap' ); ?>
		<a class="dz-link" href="<?php echo esc_url( daimzap_wholesale_url() ); ?>"><?php esc_html_e( 'Оптовым покупателям — сюда', 'daimzap' ); ?></a>
	</p>
	<?php
}
add_action( 'woocommerce_before_customer_login_form', 'daimzap_account_form_intro' );
