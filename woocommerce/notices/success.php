<?php
/**
 * Success notices.
 *
 * Override of woocommerce/templates/notices/success.php.
 *
 * @package DaimZap
 *
 * @var array $messages Notice messages.
 * @var array $notices  Notice data.
 */

defined( 'ABSPATH' ) || exit;

if ( ! $notices ) {
	return;
}
?>
<?php foreach ( $notices as $notice ) : ?>
	<div class="woocommerce-message dz-alert dz-alert--success" role="status" <?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WooCommerce. ?>>
		<?php echo wc_kses_notice( $notice['notice'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WooCommerce. ?>
	</div>
<?php endforeach; ?>
