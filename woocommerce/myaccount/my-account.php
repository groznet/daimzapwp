<?php
/**
 * My Account shell.
 *
 * Override of woocommerce/templates/myaccount/my-account.php.
 *
 * @package DaimZap
 *
 * @var string $current_view Current endpoint.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="dz-container">
	<div class="dz-account woocommerce-MyAccount">
		<?php
		/**
		 * Hook: woocommerce_account_navigation.
		 *
		 * @hooked woocommerce_account_navigation - 10
		 */
		do_action( 'woocommerce_account_navigation' );
		?>

		<div class="woocommerce-MyAccount-content min-w-0">
			<?php
			/**
			 * Hook: woocommerce_account_content.
			 *
			 * @hooked woocommerce_account_content - 10
			 */
			do_action( 'woocommerce_account_content' );
			?>
		</div>
	</div>
</div>
