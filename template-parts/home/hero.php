<?php
/**
 * Home hero: the primary "find a part" entry point.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$title    = daimzap_option( 'daimzap_hero_title' );
$subtitle = daimzap_option( 'daimzap_hero_subtitle' );
$stats    = daimzap_home_stats();
?>
<section class="dz-hero">
	<div class="dz-container">
		<div class="dz-hero__inner">
			<p class="dz-hero__eyebrow"><?php esc_html_e( 'Каталог автозапчастей · Грозный', 'daimzap' ); ?></p>

			<h1 class="dz-hero__title"><?php echo esc_html( $title ); ?></h1>

			<?php if ( $subtitle ) : ?>
				<p class="dz-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>

			<div class="dz-hero__search">
				<?php
				get_template_part(
					'template-parts/header/search-form',
					null,
					array( 'context' => 'hero' )
				);
				?>
			</div>

			<?php if ( $stats ) : ?>
				<ul class="dz-hero__stats">
					<?php foreach ( $stats as $stat ) : ?>
						<li>
							<span class="dz-hero__stat-value"><?php echo esc_html( $stat['value'] ); ?></span>
							<span class="dz-hero__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
