<?php
/**
 * Template Name: Оптовикам
 * Template Post Type: page
 *
 * Wholesale landing page with the application form.
 *
 * The approval workflow itself is not implemented here — a valid submission
 * fires `daimzap_wholesale_application`, which is where a plugin or the future
 * 1C integration takes over.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();

$status = daimzap_wholesale_status();
$fields = daimzap_wholesale_fields();
$phone  = daimzap_option( 'daimzap_phone' );
$email  = daimzap_option( 'daimzap_email' );
?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>

	<?php daimzap_page_header( get_the_title(), __( 'Условия для автосервисов, магазинов и постоянных партнёров.', 'daimzap' ) ); ?>

	<main id="main" class="dz-main">
		<div class="dz-container">
			<div class="dz-wholesale">
				<div class="min-w-0">
					<?php if ( trim( (string) get_the_content() ) ) : ?>
						<div class="dz-prose mb-8"><?php the_content(); ?></div>
					<?php else : ?>
						<div class="dz-prose mb-8">
							<p><?php esc_html_e( 'Розничные покупки на DaimZap доступны без регистрации. Оптовое сотрудничество оформляется отдельно: после заявки менеджер связывается с вами, уточняет профиль закупок и согласует условия.', 'daimzap' ); ?></p>
						</div>
					<?php endif; ?>

					<h2 class="mb-4"><?php esc_html_e( 'Как это работает', 'daimzap' ); ?></h2>

					<ol class="dz-steps">
						<li>
							<span class="dz-steps__title"><?php esc_html_e( 'Заявка', 'daimzap' ); ?></span>
							<span class="dz-steps__text"><?php esc_html_e( 'Заполните форму — укажите компанию и контакты.', 'daimzap' ); ?></span>
						</li>
						<li>
							<span class="dz-steps__title"><?php esc_html_e( 'Проверка', 'daimzap' ); ?></span>
							<span class="dz-steps__text"><?php esc_html_e( 'Администрация рассматривает заявку и связывается с вами.', 'daimzap' ); ?></span>
						</li>
						<li>
							<span class="dz-steps__title"><?php esc_html_e( 'Условия', 'daimzap' ); ?></span>
							<span class="dz-steps__text"><?php esc_html_e( 'Согласуем цены, порядок отгрузки и доставку.', 'daimzap' ); ?></span>
						</li>
						<li>
							<span class="dz-steps__title"><?php esc_html_e( 'Работа', 'daimzap' ); ?></span>
							<span class="dz-steps__text"><?php esc_html_e( 'Оформляйте заказы по списку артикулов — остатки обновляются из 1С.', 'daimzap' ); ?></span>
						</li>
					</ol>

					<?php if ( $phone || $email ) : ?>
						<h2 class="mt-8 mb-4"><?php esc_html_e( 'Связаться напрямую', 'daimzap' ); ?></h2>

						<ul class="dz-contacts">
							<?php if ( $phone ) : ?>
								<li class="dz-contact-card">
									<span class="dz-contact-card__label"><?php esc_html_e( 'Телефон', 'daimzap' ); ?></span>
									<a class="dz-contact-card__value" href="<?php echo esc_url( daimzap_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
								</li>
							<?php endif; ?>

							<?php if ( $email ) : ?>
								<li class="dz-contact-card">
									<span class="dz-contact-card__label"><?php esc_html_e( 'E-mail', 'daimzap' ); ?></span>
									<a class="dz-contact-card__value" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
				</div>

				<div class="dz-wholesale__form" id="wholesale-form">
					<h2 class="mb-1"><?php esc_html_e( 'Стать оптовиком', 'daimzap' ); ?></h2>
					<p class="dz-help mb-4"><?php esc_html_e( 'Поля со звёздочкой обязательны.', 'daimzap' ); ?></p>

					<?php if ( 'sent' === $status ) : ?>
						<div class="dz-alert dz-alert--success" role="status">
							<span><?php esc_html_e( 'Заявка отправлена. Менеджер свяжется с вами в рабочее время.', 'daimzap' ); ?></span>
						</div>
					<?php elseif ( 'invalid' === $status ) : ?>
						<div class="dz-alert dz-alert--error" role="alert">
							<span><?php esc_html_e( 'Проверьте обязательные поля и корректность e-mail.', 'daimzap' ); ?></span>
						</div>
					<?php elseif ( 'throttled' === $status ) : ?>
						<div class="dz-alert dz-alert--info" role="alert">
							<span><?php esc_html_e( 'Заявка уже отправлена. Попробуйте ещё раз через минуту.', 'daimzap' ); ?></span>
						</div>
					<?php elseif ( 'nonce' === $status ) : ?>
						<div class="dz-alert dz-alert--error" role="alert">
							<span><?php esc_html_e( 'Срок действия формы истёк. Заполните её заново.', 'daimzap' ); ?></span>
						</div>
					<?php endif; ?>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="daimzap_wholesale">
						<?php wp_nonce_field( DAIMZAP_WHOLESALE_NONCE, 'daimzap_wholesale_nonce' ); ?>

						<p class="dz-wholesale__honeypot" aria-hidden="true">
							<label for="daimzap_website"><?php esc_html_e( 'Не заполняйте это поле', 'daimzap' ); ?></label>
							<input type="text" id="daimzap_website" name="daimzap_website" tabindex="-1" autocomplete="off">
						</p>

						<?php foreach ( $fields as $key => $field ) : ?>
							<?php $field_id = 'daimzap_' . $key; ?>
							<div class="dz-field">
								<label class="dz-label" for="<?php echo esc_attr( $field_id ); ?>">
									<?php echo esc_html( $field['label'] ); ?>
									<?php if ( $field['required'] ) : ?>
										<span class="dz-required" aria-hidden="true">*</span>
									<?php endif; ?>
								</label>

								<?php if ( 'textarea' === $field['type'] ) : ?>
									<textarea
										class="dz-textarea"
										id="<?php echo esc_attr( $field_id ); ?>"
										name="<?php echo esc_attr( $field_id ); ?>"
										rows="4"
										<?php echo $field['required'] ? 'required' : ''; ?>
									></textarea>
								<?php else : ?>
									<input
										class="dz-input"
										id="<?php echo esc_attr( $field_id ); ?>"
										name="<?php echo esc_attr( $field_id ); ?>"
										type="<?php echo esc_attr( $field['type'] ); ?>"
										<?php echo $field['autocomplete'] ? 'autocomplete="' . esc_attr( $field['autocomplete'] ) . '"' : ''; ?>
										<?php echo $field['required'] ? 'required' : ''; ?>
									>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>

						<p class="dz-help mb-4">
							<?php
							$privacy_url = get_privacy_policy_url();

							if ( $privacy_url ) {
								printf(
									/* translators: %s: privacy policy link. */
									esc_html__( 'Отправляя заявку, вы соглашаетесь с %s.', 'daimzap' ),
									'<a class="dz-link" href="' . esc_url( $privacy_url ) . '">' . esc_html__( 'политикой конфиденциальности', 'daimzap' ) . '</a>'
								);
							} else {
								esc_html_e( 'Отправляя заявку, вы соглашаетесь на обработку персональных данных.', 'daimzap' );
							}
							?>
						</p>

						<button type="submit" class="dz-btn dz-btn--primary dz-btn--block">
							<?php esc_html_e( 'Отправить заявку', 'daimzap' ); ?>
						</button>
					</form>
				</div>
			</div>
		</div>
	</main>
<?php endwhile; ?>

<?php
get_footer();
