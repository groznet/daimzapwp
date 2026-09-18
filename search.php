<?php
/**
 * Search results.
 *
 * Product searches are rendered by the WooCommerce catalog templates, so this
 * template only handles searches across the rest of the site.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();

$query_term = get_search_query();
?>

<?php
daimzap_page_header(
	/* translators: %s: search term. */
	sprintf( __( 'Результаты поиска: %s', 'daimzap' ), $query_term ),
	sprintf(
		/* translators: %d: number of results. */
		_n( 'Найден %d материал', 'Найдено материалов: %d', (int) $GLOBALS['wp_query']->found_posts, 'daimzap' ),
		(int) $GLOBALS['wp_query']->found_posts
	)
);
?>

<main id="main" class="dz-main">
	<div class="dz-container">
		<div class="dz-content-layout">
			<div class="max-w-2xl mb-8">
				<?php
				get_template_part(
					'template-parts/header/search-form',
					null,
					array( 'context' => 'page' )
				);
				?>
			</div>

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
	</div>
</main>

<?php
get_footer();
