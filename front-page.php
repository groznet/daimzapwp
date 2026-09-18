<?php
/**
 * Home page.
 *
 * Every section degrades to nothing when the data behind it is missing, so the
 * page is coherent on an empty install and fills out as products are imported.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="dz-main">
	<?php get_template_part( 'template-parts/home/hero' ); ?>
	<?php get_template_part( 'template-parts/home/categories' ); ?>
	<?php get_template_part( 'template-parts/home/makes' ); ?>
	<?php get_template_part( 'template-parts/home/products' ); ?>
	<?php get_template_part( 'template-parts/home/advantages' ); ?>
	<?php get_template_part( 'template-parts/home/panels' ); ?>

	<?php
	// Content written on the assigned front page is rendered after the standard
	// sections, so the client can add copy without touching templates.
	if ( 'page' === get_option( 'show_on_front' ) ) :
		while ( have_posts() ) :
			the_post();

			if ( ! trim( (string) get_the_content() ) ) {
				continue;
			}
			?>
			<section class="dz-section">
				<div class="dz-container">
					<div class="dz-prose"><?php the_content(); ?></div>
				</div>
			</section>
			<?php
		endwhile;
	endif;
	?>
</main>

<?php
get_footer();
