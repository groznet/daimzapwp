<?php
/**
 * Informational notices.
 *
 * Override of woocommerce/templates/notices/notice.php.
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
	<div class="woocommerce-info dz-alert dz-alert--info" <?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WooCommerce. ?>>
		<?php echo wc_kses_notice( $notice['notice'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WooCommerce. ?>
	</div>
<?php endforeach; ?>
