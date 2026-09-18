<?php
/**
 * Navigation walker.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Menu walker that emits an accessible submenu toggle next to parent items.
 *
 * The toggle is a real button so submenus are reachable by keyboard and on
 * touch devices; CSS still opens desktop submenus on hover/focus-within, so the
 * menu degrades gracefully when JavaScript is unavailable.
 */
class Daimzap_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * ID of the submenu belonging to the item currently being rendered.
	 *
	 * Set in start_el() and consumed by start_lvl() so the toggle button's
	 * aria-controls matches the submenu it opens.
	 *
	 * @var string
	 */
	protected $current_submenu_id = '';

	/**
	 * Start the submenu level.
	 *
	 * @param string   $output Menu output.
	 * @param int      $depth  Depth of the item.
	 * @param stdClass $args   Menu arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );
		$id     = $this->current_submenu_id ? ' id="' . esc_attr( $this->current_submenu_id ) . '"' : '';

		$output .= "\n{$indent}<ul{$id} class=\"dz-menu__submenu dz-menu__submenu--depth-{$depth}\">\n";
	}

	/**
	 * End the submenu level.
	 *
	 * @param string   $output Menu output.
	 * @param int      $depth  Depth of the item.
	 * @param stdClass $args   Menu arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "{$indent}</ul>\n";
	}

	/**
	 * Start an element.
	 *
	 * @param string   $output            Menu output.
	 * @param WP_Post  $data_object       Menu item.
	 * @param int      $depth             Depth of the item.
	 * @param stdClass $args              Menu arguments.
	 * @param int      $current_object_id Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item          = $data_object;
		$classes       = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[]     = 'dz-menu__item';
		$classes[]     = 'dz-menu__item--depth-' . $depth;
		$has_children  = in_array( 'menu-item-has-children', $classes, true );
		$class_names   = implode( ' ', array_map( 'sanitize_html_class', array_filter( $classes ) ) );
		$is_current    = in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-ancestor', $classes, true );
		$submenu_id    = 'dz-submenu-' . (int) $item->ID;

		if ( $has_children ) {
			$this->current_submenu_id = $submenu_id;
		}

		$output .= '<li class="' . esc_attr( $class_names ) . '">';

		$atts = array(
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
			'href'   => ! empty( $item->url ) ? $item->url : '',
			'class'  => 'dz-menu__link',
		);

		if ( $is_current ) {
			$atts['aria-current'] = in_array( 'current-menu-item', $classes, true ) ? 'page' : 'true';
		}

		/** This filter is documented in wp-includes/class-walker-nav-menu.php */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' === $value || false === $value ) {
				continue;
			}

			$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		/** This filter is documented in wp-includes/class-walker-nav-menu.php */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$link  = isset( $args->before ) ? $args->before : '';
		$link .= '<a' . $attributes . '>';
		$link .= isset( $args->link_before ) ? $args->link_before : '';
		$link .= esc_html( $title );
		$link .= isset( $args->link_after ) ? $args->link_after : '';
		$link .= '</a>';
		$link .= isset( $args->after ) ? $args->after : '';

		$output .= $link;

		if ( $has_children ) {
			$output .= sprintf(
				'<button type="button" class="dz-menu__toggle" aria-expanded="false" aria-controls="%1$s" data-dz-submenu-toggle>%2$s<span class="screen-reader-text">%3$s</span></button>',
				esc_attr( $submenu_id ),
				daimzap_get_icon( 'chevron-down', 'dz-menu__toggle-icon' ),
				/* translators: %s: menu item name. */
				esc_html( sprintf( __( 'Раскрыть подменю «%s»', 'daimzap' ), $title ) )
			);
		}
	}

	/**
	 * End an element.
	 *
	 * @param string   $output      Menu output.
	 * @param WP_Post  $data_object Menu item.
	 * @param int      $depth       Depth of the item.
	 * @param stdClass $args        Menu arguments.
	 * @return void
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= "</li>\n";
	}
}
