<?php
/**
 * Announcement bar with contact shortcuts.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$notice   = daimzap_option( 'daimzap_topbar_notice' );
$phone    = daimzap_option( 'daimzap_phone' );
$whatsapp = daimzap_option( 'daimzap_whatsapp' );

if ( ! $notice && ! $phone && ! $whatsapp ) {
	return;
}
?>
<div class="dz-topbar">
	<div class="dz-container">
		<div class="dz-topbar__inner">
			<?php if ( $notice ) : ?>
				<p class="dz-topbar__notice"><?php echo esc_html( $notice ); ?></p>
			<?php endif; ?>

			<ul class="dz-topbar__links">
				<?php if ( $phone ) : ?>
					<li>
						<a href="<?php echo esc_url( daimzap_phone_href( $phone ) ); ?>">
							<?php daimzap_icon( 'phone' ); ?>
							<?php echo esc_html( $phone ); ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if ( $whatsapp ) : ?>
					<li>
						<a href="<?php echo esc_url( daimzap_whatsapp_href( $whatsapp ) ); ?>" rel="noopener noreferrer" target="_blank">
							<?php daimzap_icon( 'comment-dots' ); ?>
							<span class="hidden sm:inline"><?php esc_html_e( 'WhatsApp', 'daimzap' ); ?></span>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
