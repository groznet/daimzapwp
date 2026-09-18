<?php
/**
 * Site header.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="dz-skip-link" href="#main"><?php esc_html_e( 'Перейти к содержимому', 'daimzap' ); ?></a>

<div class="dz-overlay" data-dz-overlay></div>

<header class="dz-header" role="banner">
	<?php get_template_part( 'template-parts/header/topbar' ); ?>

	<div class="dz-container">
		<div class="dz-header__bar">
			<div class="flex items-center">
				<button
					type="button"
					class="dz-burger"
					aria-expanded="false"
					aria-controls="dz-drawer"
					data-dz-menu-open
				>
					<?php daimzap_icon( 'bars', '', __( 'Открыть меню', 'daimzap' ) ); ?>
				</button>

				<?php get_template_part( 'template-parts/header/branding' ); ?>
			</div>

			<div class="dz-header__search">
				<?php
				get_template_part(
					'template-parts/header/search-form',
					null,
					array( 'context' => 'header' )
				);
				?>
			</div>

			<?php get_template_part( 'template-parts/header/actions' ); ?>
		</div>
	</div>

	<div class="dz-container dz-mobile-search">
		<?php
		get_template_part(
			'template-parts/header/search-form',
			null,
			array( 'context' => 'mobile' )
		);
		?>
	</div>

	<?php if ( has_nav_menu( 'primary' ) ) : ?>
		<nav class="dz-nav" aria-label="<?php esc_attr_e( 'Основное меню', 'daimzap' ); ?>">
			<div class="dz-container">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'dz-menu',
						'depth'          => 2,
						'walker'         => new Daimzap_Nav_Walker(),
					)
				);
				?>
			</div>
		</nav>
	<?php endif; ?>
</header>

<?php get_template_part( 'template-parts/header/drawer' ); ?>
