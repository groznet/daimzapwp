<?php
/**
 * Fallback template: blog index and anything without a more specific template.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();

$sidebar_class = is_active_sidebar( 'blog-sidebar' ) ? ' dz-content-layout--sidebar' : '';
?>

<?php
daimzap_page_header(
	is_home() && ! is_front_page() ? get_the_title( (int) get_option( 'page_for_posts' ) ) : __( 'Новости', 'daimzap' ),
	__( 'Статьи, советы по подбору запчастей и новости магазина.', 'daimzap' )
);
?>

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
