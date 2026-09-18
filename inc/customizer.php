<?php
/**
 * Customizer settings.
 *
 * Contact details and homepage copy live here rather than in templates so the
 * client can maintain them without touching code. Defaults come from the
 * project documentation.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme option defaults.
 *
 * @return array
 */
function daimzap_option_defaults() {
	return array(
		'daimzap_phone'          => '8-928-022-21-40',
		'daimzap_whatsapp'       => '8-928-888-18-88',
		'daimzap_email'          => 'info@daimzap.ru',
		'daimzap_address'        => 'ул. Академика Х. Ибрагимова, 72, Грозный',
		'daimzap_hours'          => 'Пн–Сб: 9:00–19:00',
		'daimzap_instagram'      => 'https://instagram.com/daimzap/',
		'daimzap_youtube'        => 'https://youtube.com/@shimon_ekb9693',
		'daimzap_hero_title'     => 'Автозапчасти для любой марки и модели',
		'daimzap_hero_subtitle'  => 'Розница и опт в Грозном. Поиск по артикулу, марке и модели автомобиля. Доставка по России и самовывоз.',
		'daimzap_wholesale_page' => 0,
		'daimzap_topbar_notice'  => 'Оптовым покупателям — специальные условия. Оставьте заявку на странице «Стать оптовиком».',
	);
}

/**
 * Read a theme option with its documented default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function daimzap_option( $key ) {
	$defaults = daimzap_option_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Register Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function daimzap_customize_register( $wp_customize ) {
	$defaults = daimzap_option_defaults();

	$wp_customize->add_section(
		'daimzap_contacts',
		array(
			'title'       => __( 'DaimZap: контакты', 'daimzap' ),
			'priority'    => 30,
			'description' => __( 'Контактные данные, которые выводятся в шапке, подвале и на странице контактов.', 'daimzap' ),
		)
	);

	$contact_fields = array(
		'daimzap_phone'     => array( __( 'Телефон', 'daimzap' ), 'sanitize_text_field' ),
		'daimzap_whatsapp'  => array( __( 'WhatsApp', 'daimzap' ), 'sanitize_text_field' ),
		'daimzap_email'     => array( __( 'E-mail', 'daimzap' ), 'sanitize_email' ),
		'daimzap_address'   => array( __( 'Адрес', 'daimzap' ), 'sanitize_text_field' ),
		'daimzap_hours'     => array( __( 'Часы работы', 'daimzap' ), 'sanitize_text_field' ),
		'daimzap_instagram' => array( __( 'Instagram', 'daimzap' ), 'esc_url_raw' ),
		'daimzap_youtube'   => array( __( 'YouTube', 'daimzap' ), 'esc_url_raw' ),
	);

	foreach ( $contact_fields as $key => $field ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => $field[1],
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$key,
			array(
				'label'   => $field[0],
				'section' => 'daimzap_contacts',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'daimzap_home',
		array(
			'title'       => __( 'DaimZap: главная страница', 'daimzap' ),
			'priority'    => 31,
			'description' => __( 'Текст в первом экране главной страницы и объявление в верхней панели.', 'daimzap' ),
		)
	);

	$wp_customize->add_setting(
		'daimzap_hero_title',
		array(
			'default'           => $defaults['daimzap_hero_title'],
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'daimzap_hero_title',
		array(
			'label'   => __( 'Заголовок первого экрана', 'daimzap' ),
			'section' => 'daimzap_home',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'daimzap_hero_subtitle',
		array(
			'default'           => $defaults['daimzap_hero_subtitle'],
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'daimzap_hero_subtitle',
		array(
			'label'   => __( 'Подзаголовок первого экрана', 'daimzap' ),
			'section' => 'daimzap_home',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'daimzap_topbar_notice',
		array(
			'default'           => $defaults['daimzap_topbar_notice'],
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'daimzap_topbar_notice',
		array(
			'label'       => __( 'Объявление в верхней панели', 'daimzap' ),
			'description' => __( 'Оставьте поле пустым, чтобы скрыть панель.', 'daimzap' ),
			'section'     => 'daimzap_home',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'daimzap_wholesale_page',
		array(
			'default'           => $defaults['daimzap_wholesale_page'],
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'daimzap_wholesale_page',
		array(
			'label'       => __( 'Страница «Стать оптовиком»', 'daimzap' ),
			'description' => __( 'Используется для кнопок «Стать оптовиком» по всему сайту.', 'daimzap' ),
			'section'     => 'daimzap_home',
			'type'        => 'dropdown-pages',
		)
	);

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'daimzap_hero_title',
			array(
				'selector'        => '.dz-hero__title',
				'render_callback' => function () {
					return esc_html( daimzap_option( 'daimzap_hero_title' ) );
				},
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'daimzap_hero_subtitle',
			array(
				'selector'        => '.dz-hero__subtitle',
				'render_callback' => function () {
					return esc_html( daimzap_option( 'daimzap_hero_subtitle' ) );
				},
			)
		);
	}
}
add_action( 'customize_register', 'daimzap_customize_register' );

/**
 * URL of the wholesale page, falling back to a `/wholesale` slug lookup.
 *
 * @return string
 */
function daimzap_wholesale_url() {
	$page_id = (int) daimzap_option( 'daimzap_wholesale_page' );

	if ( $page_id > 0 ) {
		return (string) get_permalink( $page_id );
	}

	$page = get_page_by_path( 'wholesale' );

	if ( $page instanceof WP_Post ) {
		return (string) get_permalink( $page );
	}

	return home_url( '/wholesale/' );
}
