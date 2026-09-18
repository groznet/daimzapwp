<?php
/**
 * Archive template for categories, tags, dates and authors.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();

$sidebar_class = is_active_sidebar( 'blog-sidebar' ) ? ' dz-content-layout--sidebar' : '';
?>

<?php daimzap_page_header( wp_strip_all_tags( get_the_archive_title() ), wp_strip_all_tags( get_the_archive_description() ) ); ?>

<main id="main" class="dz-main">
	<div class="dz-container">
		<div class="dz-content-layout<?php echo esc_attr( $sidebar_class ); ?>">
			<div>
				<?php if ( have_posts() ) : ?>
					<div class="dz-posts">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content/post-card' );
						endwhile;
						?>
					</div>

					<?php daimzap_pagination(); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/content/none' ); ?>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>
</main>

<?php
get_footer();
