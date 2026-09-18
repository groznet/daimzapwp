<?php
/**
 * Default page template.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>

	<?php daimzap_page_header( get_the_title() ); ?>

	<main id="main" class="dz-main">
		<div class="dz-container">
			<div class="dz-content-layout">
				<article <?php post_class( 'dz-prose' ); ?>>
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="dz-help">' . esc_html__( 'Страницы:', 'daimzap' ) . ' ',
							'after'  => '</div>',
						)
					);
					?>
				</article>
			</div>
		</div>
	</main>

	<?php if ( comments_open() || get_comments_number() ) : ?>
		<div class="dz-container">
			<?php comments_template(); ?>
		</div>
	<?php endif; ?>
<?php endwhile; ?>

<?php
get_footer();
