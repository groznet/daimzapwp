<?php
/**
 * Login and registration forms.
 *
 * Override of woocommerce/templates/myaccount/form-login.php. Field names,
 * nonces and hooks match WooCommerce core exactly — only the layout differs.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
?>
<div class="dz-container">
	<div class="dz-login-grid<?php echo ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) ? '' : ' max-w-lg'; ?>">
		<div class="dz-login-card u-column1 col-1">
			<h2 class="mb-4"><?php esc_html_e( 'Вход', 'daimzap' ); ?></h2>

			<form class="woocommerce-form woocommerce-form-login login" method="post">
				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="dz-field woocommerce-form-row form-row">
					<label class="dz-label" for="username"><?php esc_html_e( 'Логин или e-mail', 'daimzap' ); ?>&nbsp;<span class="dz-required">*</span></label>
					<input type="text" class="dz-input woocommerce-Input input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Repopulating after a failed submit; core does the same. ?>" required>
				</p>

				<p class="dz-field woocommerce-form-row form-row">
					<label class="dz-label" for="password"><?php esc_html_e( 'Пароль', 'daimzap' ); ?>&nbsp;<span class="dz-required">*</span></label>
					<input class="dz-input woocommerce-Input input-text" type="password" name="password" id="password" autocomplete="current-password" required>
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row">
					<label class="dz-checkbox woocommerce-form__label-for-checkbox">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever">
						<span><?php esc_html_e( 'Запомнить меня', 'daimzap' ); ?></span>
					</label>
				</p>

				<p class="form-row">
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="dz-btn dz-btn--dark woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Войти', 'daimzap' ); ?>">
						<?php esc_html_e( 'Войти', 'daimzap' ); ?>
					</button>
				</p>

				<p class="woocommerce-LostPassword lost_password">
					<a class="dz-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Забыли пароль?', 'daimzap' ); ?></a>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>

		<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
			<div class="dz-login-card u-column2 col-2">
				<h2 class="mb-4"><?php esc_html_e( 'Регистрация', 'daimzap' ); ?></h2>

				<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
						<p class="dz-field woocommerce-form-row form-row">
							<label class="dz-label" for="reg_username"><?php esc_html_e( 'Логин', 'daimzap' ); ?>&nbsp;<span class="dz-required">*</span></label>
							<input type="text" class="dz-input woocommerce-Input input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Repopulating after a failed submit; core does the same. ?>" required>
						</p>
					<?php endif; ?>

					<p class="dz-field woocommerce-form-row form-row">
						<label class="dz-label" for="reg_email"><?php esc_html_e( 'E-mail', 'daimzap' ); ?>&nbsp;<span class="dz-required">*</span></label>
						<input type="email" class="dz-input woocommerce-Input input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Repopulating after a failed submit; core does the same. ?>" required>
					</p>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
						<p class="dz-field woocommerce-form-row form-row">
							<label class="dz-label" for="reg_password"><?php esc_html_e( 'Пароль', 'daimzap' ); ?>&nbsp;<span class="dz-required">*</span></label>
							<input type="password" class="dz-input woocommerce-Input input-text" name="password" id="reg_password" autocomplete="new-password" required>
						</p>
					<?php else : ?>
						<p class="dz-help"><?php esc_html_e( 'Ссылка для создания пароля придёт на указанный e-mail.', 'daimzap' ); ?></p>
					<?php endif; ?>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<p class="woocommerce-form-row form-row">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="dz-btn dz-btn--primary woocommerce-Button woocommerce-button button" name="register" value="<?php esc_attr_e( 'Зарегистрироваться', 'daimzap' ); ?>">
							<?php esc_html_e( 'Зарегистрироваться', 'daimzap' ); ?>
						</button>
					</p>

					<?php do_action( 'woocommerce_register_form_end' ); ?>
				</form>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
