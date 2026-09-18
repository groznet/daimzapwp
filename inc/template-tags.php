<?php
/**
 * Template tags shared across templates and template parts.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a Font Awesome icon.
 *
 * Icons are decorative by default and hidden from assistive technology. Pass a
 * label when the icon is the only content of a control.
 *
 * @param string $name  Font Awesome icon name without the `fa-` prefix.
 * @param string $class Extra classes.
 * @param string $label Optional accessible label.
 * @return string
 */
function daimzap_get_icon( $name, $class = '', $label = '' ) {
	if ( ! daimzap_load_font_awesome() ) {
		return $label ? '<span class="screen-reader-text">' . esc_html( $label ) . '</span>' : '';
	}

	$markup = sprintf(
		'<i class="fa-solid fa-%1$s %2$s" aria-hidden="true"></i>',
		esc_attr( $name ),
		esc_attr( $class )
	);

	if ( $label ) {
		$markup .= '<span class="screen-reader-text">' . esc_html( $label ) . '</span>';
	}

	return $markup;
}

/**
 * Echo a Font Awesome icon.
 *
 * @param string $name  Icon name.
 * @param string $class Extra classes.
 * @param string $label Optional accessible label.
 * @return void
 */
function daimzap_icon( $name, $class = '', $label = '' ) {
	echo daimzap_get_icon( $name, $class, $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in daimzap_get_icon().
}

/**
 * Whether the current request renders a product listing.
 *
 * Covers the shop page, product categories/tags, vehicle attribute archives and
 * product search results — everywhere the catalog sidebar and toolbar belong.
 *
 * @return bool
 */
function daimzap_is_catalog_context() {
	if ( ! daimzap_is_woocommerce_active() ) {
		return false;
	}

	if ( is_shop() || is_product_taxonomy() ) {
		return true;
	}

	if ( is_search() && 'product' === get_query_var( 'post_type' ) ) {
		return true;
	}

	return false;
}

/**
 * Site-wide page heading used by non-WooCommerce templates.
 *
 * @param string $title    Heading text.
 * @param string $subtitle Optional supporting line.
 * @return void
 */
function daimzap_page_header( $title, $subtitle = '' ) {
	?>
	<header class="dz-page-header">
		<div class="dz-container">
			<?php daimzap_breadcrumb(); ?>
			<h1 class="dz-page-header__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="dz-page-header__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Render breadcrumbs.
 *
 * Defers to WooCommerce breadcrumbs when available (they already integrate with
 * SEO plugins and product taxonomies) and falls back to a minimal trail
 * elsewhere. Structured data is left to the SEO plugin on purpose.
 *
 * @return void
 */
function daimzap_breadcrumb() {
	if ( is_front_page() ) {
		return;
	}

	if ( daimzap_is_woocommerce_active() && function_exists( 'woocommerce_breadcrumb' ) ) {
		woocommerce_breadcrumb(
			array(
				'wrap_before' => '<nav class="dz-breadcrumb" aria-label="' . esc_attr__( 'Хлебные крошки', 'daimzap' ) . '"><ol class="dz-breadcrumb__list">',
				'wrap_after'  => '</ol></nav>',
				'before'      => '<li class="dz-breadcrumb__item">',
				'after'       => '</li>',
				'delimiter'   => '',
				'home'        => __( 'Главная', 'daimzap' ),
			)
		);

		return;
	}

	$items = array(
		array(
			'url'   => home_url( '/' ),
			'label' => __( 'Главная', 'daimzap' ),
		),
	);

	if ( is_singular() ) {
		$items[] = array(
			'url'   => '',
			'label' => get_the_title(),
		);
	} elseif ( is_archive() || is_home() ) {
		$items[] = array(
			'url'   => '',
			'label' => wp_strip_all_tags( get_the_archive_title() ),
		);
	} elseif ( is_search() ) {
		$items[] = array(
			'url'   => '',
			'label' => __( 'Результаты поиска', 'daimzap' ),
		);
	} elseif ( is_404() ) {
		$items[] = array(
			'url'   => '',
			'label' => __( 'Страница не найдена', 'daimzap' ),
		);
	}

	echo '<nav class="dz-breadcrumb" aria-label="' . esc_attr__( 'Хлебные крошки', 'daimzap' ) . '"><ol class="dz-breadcrumb__list">';

	foreach ( $items as $item ) {
		echo '<li class="dz-breadcrumb__item">';

		if ( $item['url'] ) {
			echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		} else {
			echo '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
		}

		echo '</li>';
	}

	echo '</ol></nav>';
}

/**
 * Render numbered pagination for non-WooCommerce archives.
 *
 * @return void
 */
function daimzap_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => daimzap_get_icon( 'chevron-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Предыдущая страница', 'daimzap' ) . '</span>',
			'next_text'          => daimzap_get_icon( 'chevron-right' ) . '<span class="screen-reader-text">' . esc_html__( 'Следующая страница', 'daimzap' ) . '</span>',
			'screen_reader_text' => __( 'Навигация по страницам', 'daimzap' ),
			'class'              => 'dz-pagination',
		)
	);
}

/**
 * Entry meta for blog posts.
 *
 * @return void
 */
function daimzap_entry_meta() {
	printf(
		'<p class="dz-entry__meta"><time datetime="%1$s">%2$s</time></p>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
}

/**
 * Digits-only phone number, safe for a `tel:` href.
 *
 * @param string $phone Human readable phone number.
 * @return string
 */
function daimzap_phone_href( $phone ) {
	$digits = preg_replace( '/[^0-9+]/', '', (string) $phone );

	return 'tel:' . $digits;
}

/**
 * WhatsApp deep link for a phone number.
 *
 * Russian numbers are commonly written starting with 8; wa.me needs the 7 country code.
 *
 * @param string $phone Human readable phone number.
 * @return string
 */
function daimzap_whatsapp_href( $phone ) {
	$digits = preg_replace( '/[^0-9]/', '', (string) $phone );

	if ( '8' === substr( $digits, 0, 1 ) && 11 === strlen( $digits ) ) {
		$digits = '7' . substr( $digits, 1 );
	}

	return 'https://wa.me/' . $digits;
}

/**
 * Fallback catalog links for the footer when no catalog menu is assigned.
 *
 * Lists the product categories that actually have products, so the footer is
 * useful from the first import onward without any menu configuration.
 *
 * @return void
 */
function daimzap_footer_category_links() {
	if ( ! daimzap_is_woocommerce_active() ) {
		return;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'number'     => 6,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}

	echo '<ul class="dz-footer__list">';

	foreach ( $terms as $term ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( get_term_link( $term ) ),
			esc_html( $term->name )
		);
	}

	echo '</ul>';
}

/**
 * Headline numbers for the home hero.
 *
 * Every figure is read from real catalog data and omitted when it is zero, so
 * the hero never advertises an empty store.
 *
 * @return array[] Each entry has `value` and `label`.
 */
function daimzap_home_stats() {
	if ( ! daimzap_is_woocommerce_active() ) {
		return array();
	}

	$stats  = array();
	$counts = wp_count_posts( 'product' );
	$total  = isset( $counts->publish ) ? (int) $counts->publish : 0;

	if ( $total > 0 ) {
		$stats[] = array(
			'value' => number_format_i18n( $total ),
			'label' => _n( 'позиция в каталоге', 'позиций в каталоге', $total, 'daimzap' ),
		);
	}

	$categories = (int) wp_count_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
		)
	);

	if ( $categories > 0 ) {
		$stats[] = array(
			'value' => number_format_i18n( $categories ),
			'label' => _n( 'категория запчастей', 'категорий запчастей', $categories, 'daimzap' ),
		);
	}

	$makes = count( daimzap_get_vehicle_terms_in_use( 'make' ) );

	if ( $makes > 0 ) {
		$stats[] = array(
			'value' => number_format_i18n( $makes ),
			'label' => _n( 'марка автомобилей', 'марок автомобилей', $makes, 'daimzap' ),
		);
	}

	return $stats;
}
