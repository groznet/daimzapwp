<?php
/**
 * Delivery and wholesale panels.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$phone   = daimzap_option( 'daimzap_phone' );
$address = daimzap_option( 'daimzap_address' );
$hours   = daimzap_option( 'daimzap_hours' );
?>
<section class="dz-section pt-0">
	<div class="dz-container">
		<div class="dz-split">
			<div class="dz-panel">
				<h2 class="dz-panel__title"><?php esc_html_e( 'Доставка и самовывоз', 'daimzap' ); ?></h2>
				<p class="dz-panel__text"><?php esc_html_e( 'Заказ можно забрать со склада или получить доставкой — условия зависят от региона и веса детали.', 'daimzap' ); ?></p>

				<ul class="dz-panel__list">
					<?php if ( $address ) : ?>
						<li>
							<?php daimzap_icon( 'store' ); ?>
							<span><?php echo esc_html( $address ); ?></span>
						</li>
					<?php endif; ?>

					<?php if ( $hours ) : ?>
						<li>
							<?php daimzap_icon( 'clock' ); ?>
							<span><?php echo esc_html( $hours ); ?></span>
						</li>
					<?php endif; ?>

					<li>
						<?php daimzap_icon( 'truck-fast' ); ?>
						<span><?php esc_html_e( 'Отправка транспортными компаниями по России', 'daimzap' ); ?></span>
					</li>
				</ul>

				<div class="dz-panel__actions">
					<?php if ( $phone ) : ?>
						<a class="dz-btn dz-btn--ghost" href="<?php echo esc_url( daimzap_phone_href( $phone ) ); ?>">
							<?php daimzap_icon( 'phone' ); ?>
							<?php echo esc_html( $phone ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<div class="dz-panel dz-panel--dark">
				<h2 class="dz-panel__title"><?php esc_html_e( 'Оптовым покупателям', 'daimzap' ); ?></h2>
				<p class="dz-panel__text"><?php esc_html_e( 'Работаете с автосервисом или магазином? Оставьте заявку — менеджер свяжется с вами и согласует условия.', 'daimzap' ); ?></p>

				<ul class="dz-panel__list">
					<li>
						<?php daimzap_icon( 'check' ); ?>
						<span><?php esc_html_e( 'Отдельные цены для постоянных партнёров', 'daimzap' ); ?></span>
					</li>
					<li>
						<?php daimzap_icon( 'check' ); ?>
						<span><?php esc_html_e( 'Подбор позиций по списку артикулов', 'daimzap' ); ?></span>
					</li>
					<li>
						<?php daimzap_icon( 'check' ); ?>
						<span><?php esc_html_e( 'Розничные покупки — без регистрации', 'daimzap' ); ?></span>
					</li>
				</ul>

				<div class="dz-panel__actions">
					<a class="dz-btn dz-btn--primary" href="<?php echo esc_url( daimzap_wholesale_url() ); ?>">
						<?php esc_html_e( 'Стать оптовиком', 'daimzap' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
