<?php
/**
 * Why DaimZap.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$advantages = array(
	array(
		'icon'  => 'barcode',
		'title' => __( 'Поиск по артикулу', 'daimzap' ),
		'text'  => __( 'Точный артикул сразу открывает карточку детали — без лишних переходов по каталогу.', 'daimzap' ),
	),
	array(
		'icon'  => 'car-side',
		'title' => __( 'Подбор по автомобилю', 'daimzap' ),
		'text'  => __( 'Фильтры по марке и модели показывают только те детали, которые подходят вашей машине.', 'daimzap' ),
	),
	array(
		'icon'  => 'arrows-rotate',
		'title' => __( 'Актуальные остатки', 'daimzap' ),
		'text'  => __( 'Наличие и цены обновляются из 1С: Управление торговлей.', 'daimzap' ),
	),
	array(
		'icon'  => 'truck-fast',
		'title' => __( 'Доставка и самовывоз', 'daimzap' ),
		'text'  => __( 'Отправка по России или самовывоз со склада в Грозном.', 'daimzap' ),
	),
);
?>
<section class="dz-section pt-0">
	<div class="dz-container">
		<div class="dz-section__head">
			<h2 class="dz-section__title"><?php esc_html_e( 'Почему DaimZap', 'daimzap' ); ?></h2>
		</div>

		<ul class="dz-advantages">
			<?php foreach ( $advantages as $advantage ) : ?>
				<li class="dz-advantage">
					<?php daimzap_icon( $advantage['icon'], 'dz-advantage__icon' ); ?>
					<h3 class="dz-advantage__title"><?php echo esc_html( $advantage['title'] ); ?></h3>
					<p class="dz-advantage__text"><?php echo esc_html( $advantage['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
