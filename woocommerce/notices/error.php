<?php
/**
 * Error notices.
 *
 * Override of woocommerce/templates/notices/error.php.
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
<ul class="woocommerce-error list-none m-0 p-0" role="alert">
	<?php foreach ( $notices as $notice ) : ?>
		<li class="dz-alert dz-alert--error" <?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WooCommerce. ?>>
			<?php echo wc_kses_notice( $notice['notice'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by WooCommerce. ?>
		</li>
	<?php endforeach; ?>
</ul>
