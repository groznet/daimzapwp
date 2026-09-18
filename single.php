<?php
/**
 * Single post template.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="dz-main">
	<div class="dz-container">
		<div class="dz-content-layout">
			<article <?php post_class( 'dz-entry' ); ?>>
				<header class="mb-6">
					<?php daimzap_breadcrumb(); ?>
					<h1 class="mb-2"><?php the_title(); ?></h1>
					<?php daimzap_entry_meta(); ?>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="dz-entry__media">
						<?php the_post_thumbnail( 'large', array( 'sizes' => '(max-width: 1024px) 100vw, 1024px' ) ); ?>
					</figure>
				<?php endif; ?>

				<div class="dz-prose">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="dz-help">' . esc_html__( 'Страницы:', 'daimzap' ) . ' ',
							'after'  => '</div>',
						)
					);
					?>
				</div>

				<?php if ( comments_open() || get_comments_number() ) : ?>
					<?php comments_template(); ?>
				<?php endif; ?>
			</article>
		</div>
	</div>
</main>

<?php
get_footer();
