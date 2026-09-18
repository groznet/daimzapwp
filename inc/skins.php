<?php
/**
 * Skins.
 *
 * The theme ships two complete visual directions over identical markup. A skin
 * is a compiled stylesheet plus a body class; no template renders differently,
 * which is what keeps a second look cheap to maintain and safe to switch.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default skin slug.
 */
const DAIMZAP_DEFAULT_SKIN = 'graphite';

/**
 * Registered skins.
 *
 * @return array[] Keyed by slug, each with `label`, `description` and `stylesheet`.
 */
function daimzap_skins() {
	return apply_filters(
		'daimzap_skins',
		array(
			'graphite' => array(
				'label'       => __( 'Графит', 'daimzap' ),
				'description' => __( 'Светлое премиальное оформление: графитовые нейтральные тона, красный акцент, мягкие скругления.', 'daimzap' ),
				'stylesheet'  => 'assets/css/skin-graphite.css',
			),
			'terminal' => array(
				'label'       => __( 'Терминал', 'daimzap' ),
				'description' => __( 'Тёмное техническое оформление: почти чёрные панели, янтарный акцент, моноширинные артикулы и цены, прямые углы.', 'daimzap' ),
				'stylesheet'  => 'assets/css/skin-terminal.css',
			),
		)
	);
}

/**
 * Slug of the skin to render.
 *
 * Resolution order: a `dz-skin` preview parameter for users who can edit theme
 * options, then the saved Customizer value, then the default. The preview
 * parameter is capability-gated so a skin cannot be forced into a shared page
 * cache by an anonymous visitor, and the check is deferred past `init` so it
 * never resolves the current user early.
 *
 * @return string
 */
function daimzap_current_skin() {
	$skins = daimzap_skins();

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only preview switch, gated on capability below.
	$preview = isset( $_GET['dz-skin'] ) ? sanitize_key( wp_unslash( $_GET['dz-skin'] ) ) : '';

	/*
	 * The capability check is deferred until after `init`. Calling
	 * current_user_can() during `after_setup_theme` — which is when
	 * add_editor_style() asks for the stylesheet — would resolve the current
	 * user before plugins have had a chance to set it.
	 */
	if ( $preview && isset( $skins[ $preview ] ) && did_action( 'init' ) && current_user_can( 'edit_theme_options' ) ) {
		return $preview;
	}

	$saved = sanitize_key( (string) get_theme_mod( 'daimzap_skin', DAIMZAP_DEFAULT_SKIN ) );

	if ( ! isset( $skins[ $saved ] ) ) {
		$saved = DAIMZAP_DEFAULT_SKIN;
	}

	/**
	 * Filters the active skin slug.
	 *
	 * @param string $saved Skin slug.
	 */
	$slug = (string) apply_filters( 'daimzap_current_skin', $saved );

	return isset( $skins[ $slug ] ) ? $slug : DAIMZAP_DEFAULT_SKIN;
}

/**
 * Theme-relative path of the active skin's stylesheet.
 *
 * @return string
 */
function daimzap_skin_stylesheet() {
	$skins = daimzap_skins();
	$slug  = daimzap_current_skin();

	if ( ! empty( $skins[ $slug ]['stylesheet'] ) ) {
		return (string) $skins[ $slug ]['stylesheet'];
	}

	return 'assets/css/skin-' . DAIMZAP_DEFAULT_SKIN . '.css';
}

/**
 * Tag the body with the active skin so skin-specific rules can hook onto it.
 *
 * @param array $classes Body classes.
 * @return array
 */
function daimzap_skin_body_class( $classes ) {
	$classes[] = 'dz-skin-' . daimzap_current_skin();

	return $classes;
}
add_filter( 'body_class', 'daimzap_skin_body_class' );

/**
 * Register the skin picker in the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function daimzap_customize_register_skin( $wp_customize ) {
	$skins   = daimzap_skins();
	$choices = array();

	foreach ( $skins as $slug => $skin ) {
		$choices[ $slug ] = $skin['label'];
	}

	$wp_customize->add_section(
		'daimzap_skin_section',
		array(
			'title'       => __( 'DaimZap: оформление', 'daimzap' ),
			'priority'    => 29,
			'description' => __( 'Визуальный стиль сайта. Вёрстка и функциональность одинаковы, меняется только оформление.', 'daimzap' ),
		)
	);

	$wp_customize->add_setting(
		'daimzap_skin',
		array(
			'default'           => DAIMZAP_DEFAULT_SKIN,
			'sanitize_callback' => 'daimzap_sanitize_skin',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'daimzap_skin',
		array(
			'label'       => __( 'Стиль оформления', 'daimzap' ),
			'description' => daimzap_skin_choices_description(),
			'section'     => 'daimzap_skin_section',
			'type'        => 'radio',
			'choices'     => $choices,
		)
	);
}
add_action( 'customize_register', 'daimzap_customize_register_skin' );

/**
 * Human readable summary of every registered skin, for the Customizer control.
 *
 * @return string
 */
function daimzap_skin_choices_description() {
	$lines = array();

	foreach ( daimzap_skins() as $skin ) {
		$lines[] = '<strong>' . esc_html( $skin['label'] ) . '</strong> — ' . esc_html( $skin['description'] );
	}

	return implode( '<br><br>', $lines );
}

/**
 * Sanitize the skin setting against the registered skins.
 *
 * @param string $value Submitted value.
 * @return string
 */
function daimzap_sanitize_skin( $value ) {
	$value = sanitize_key( (string) $value );
	$skins = daimzap_skins();

	return isset( $skins[ $value ] ) ? $value : DAIMZAP_DEFAULT_SKIN;
}

/**
 * Let editors jump between skins from the admin bar while reviewing.
 *
 * @param WP_Admin_Bar $admin_bar Admin bar instance.
 * @return void
 */
function daimzap_skin_admin_bar( $admin_bar ) {
	if ( is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$skins   = daimzap_skins();
	$current = daimzap_current_skin();

	$admin_bar->add_node(
		array(
			'id'    => 'daimzap-skin',
			'title' => __( 'Оформление:', 'daimzap' ) . ' ' . $skins[ $current ]['label'],
			'href'  => admin_url( 'customize.php?autofocus[section]=daimzap_skin_section' ),
		)
	);

	foreach ( $skins as $slug => $skin ) {
		$admin_bar->add_node(
			array(
				'id'     => 'daimzap-skin-' . $slug,
				'parent' => 'daimzap-skin',
				'title'  => ( $slug === $current ? '● ' : '○ ' ) . $skin['label'],
				'href'   => add_query_arg( 'dz-skin', $slug ),
			)
		);
	}
}
add_action( 'admin_bar_menu', 'daimzap_skin_admin_bar', 90 );
