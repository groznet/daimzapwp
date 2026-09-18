<?php
/**
 * Product catalog: shop page, product categories/tags and vehicle archives.
 *
 * Override of woocommerce/templates/archive-product.php. All WooCommerce hooks
 * are preserved so extensions keep working; only the layout differs.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked daimzap_woocommerce_wrapper_start - 10 (opens <main>)
 */
do_action( 'woocommerce_before_main_content' );
?>

<?php get_template_part( 'template-parts/catalog/header' ); ?>

<div class="dz-container">
	<div class="dz-catalog-layout">
		<?php get_template_part( 'template-parts/catalog/filters' ); ?>

		<div class="min-w-0">
			<?php
			/**
			 * Hook: woocommerce_shop_loop_header.
			 *
			 * @hooked woocommerce_product_taxonomy_archive_header - 10
			 */
			do_action( 'woocommerce_shop_loop_header' );
			?>

			<?php if ( woocommerce_product_loop() ) : ?>
				<?php get_template_part( 'template-parts/catalog/active-filters' ); ?>
				<?php get_template_part( 'template-parts/catalog/toolbar' ); ?>

				<?php
				/**
				 * Hook: woocommerce_before_shop_loop.
				 *
				 * @hooked woocommerce_output_all_notices - 10
				 * @hooked WC_Structured_Data::generate_product_data() - 10
				 */
				do_action( 'woocommerce_before_shop_loop' );

				woocommerce_product_loop_start();

				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();

						/**
						 * Hook: woocommerce_shop_loop.
						 */
						do_action( 'woocommerce_shop_loop' );

						wc_get_template_part( 'content', 'product' );
					}
				}

				woocommerce_product_loop_end();

				/**
				 * Hook: woocommerce_after_shop_loop.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
				?>
			<?php else : ?>
				<?php
				/**
				 * Hook: woocommerce_no_products_found.
				 *
				 * @hooked wc_no_products_found - 10
				 */
				do_action( 'woocommerce_no_products_found' );
				?>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked daimzap_woocommerce_wrapper_end - 10 (closes <main>)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * The catalog sidebar is rendered inside the layout grid above, so core's
 * sidebar callback is unhooked in inc/woocommerce/setup.php. The action itself
 * is kept for extensions that hook into it.
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
