<?php
/**
 * My Account navigation.
 *
 * Override of woocommerce/templates/myaccount/navigation.php.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );
?>
<nav class="dz-account-nav woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e( 'Меню личного кабинета', 'daimzap' ); ?>">
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<?php $is_active = wc_is_current_account_menu_item( $endpoint ); ?>
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?><?php echo $is_active ? ' is-active' : ''; ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
					<?php daimzap_icon( daimzap_account_menu_icon( $endpoint ), 'dz-account-nav__icon' ); ?>
					<span><?php echo esc_html( $label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php
do_action( 'woocommerce_after_account_navigation' );
