<?php
/**
 * Site branding.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;
?>
<?php if ( has_custom_logo() ) : ?>
	<div class="dz-brand">
		<?php the_custom_logo(); ?>
	</div>
<?php else : ?>
	<a class="dz-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<span>
			<span class="dz-brand__name">
				Daim<span class="dz-brand__mark">Zap</span>
			</span>
			<span class="dz-brand__tagline"><?php esc_html_e( 'Автозапчасти · Грозный', 'daimzap' ); ?></span>
		</span>
	</a>
<?php endif; ?>
