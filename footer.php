<?php
/**
 * Site footer.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$phone     = daimzap_option( 'daimzap_phone' );
$whatsapp  = daimzap_option( 'daimzap_whatsapp' );
$email     = daimzap_option( 'daimzap_email' );
$address   = daimzap_option( 'daimzap_address' );
$hours     = daimzap_option( 'daimzap_hours' );
$instagram = daimzap_option( 'daimzap_instagram' );
$youtube   = daimzap_option( 'daimzap_youtube' );
?>
<footer class="dz-footer" role="contentinfo">
	<div class="dz-container">
		<div class="dz-footer__grid">
			<div>
				<span class="dz-brand__name">Daim<span class="dz-brand__mark">Zap</span></span>
				<p class="dz-footer__about">
					<?php esc_html_e( 'Каталог автозапчастей для легковых и коммерческих автомобилей. Розница и опт, доставка по России и самовывоз в Грозном.', 'daimzap' ); ?>
				</p>

				<?php if ( $instagram || $youtube ) : ?>
					<div class="dz-footer__social">
						<?php if ( $instagram ) : ?>
							<a href="<?php echo esc_url( $instagram ); ?>" rel="noopener noreferrer" target="_blank">
								<?php daimzap_icon( 'camera', '', __( 'Instagram', 'daimzap' ) ); ?>
							</a>
						<?php endif; ?>

						<?php if ( $youtube ) : ?>
							<a href="<?php echo esc_url( $youtube ); ?>" rel="noopener noreferrer" target="_blank">
								<?php daimzap_icon( 'play', '', __( 'YouTube', 'daimzap' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div>
				<h2 class="dz-footer__title"><?php esc_html_e( 'Каталог', 'daimzap' ); ?></h2>
				<?php if ( has_nav_menu( 'catalog' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'catalog',
							'container'      => false,
							'menu_class'     => 'dz-footer__list',
							'depth'          => 1,
						)
					);
					?>
				<?php else : ?>
					<?php daimzap_footer_category_links(); ?>
				<?php endif; ?>
			</div>

			<div>
				<h2 class="dz-footer__title"><?php esc_html_e( 'Покупателям', 'daimzap' ); ?></h2>
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'dz-footer__list',
							'depth'          => 1,
						)
					);
					?>
				<?php else : ?>
					<ul class="dz-footer__list">
						<li><a href="<?php echo esc_url( daimzap_wholesale_url() ); ?>"><?php esc_html_e( 'Стать оптовиком', 'daimzap' ); ?></a></li>
					</ul>
				<?php endif; ?>
			</div>

			<div>
				<h2 class="dz-footer__title"><?php esc_html_e( 'Контакты', 'daimzap' ); ?></h2>
				<ul class="dz-footer__list">
					<?php if ( $phone ) : ?>
						<li class="dz-footer__contact">
							<?php daimzap_icon( 'phone' ); ?>
							<a href="<?php echo esc_url( daimzap_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( $whatsapp ) : ?>
						<li class="dz-footer__contact">
							<?php daimzap_icon( 'comment-dots' ); ?>
							<a href="<?php echo esc_url( daimzap_whatsapp_href( $whatsapp ) ); ?>" rel="noopener noreferrer" target="_blank">
								<?php echo esc_html( $whatsapp ); ?>
							</a>
						</li>
					<?php endif; ?>

					<?php if ( $email ) : ?>
						<li class="dz-footer__contact">
							<?php daimzap_icon( 'envelope' ); ?>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( $address ) : ?>
						<li class="dz-footer__contact">
							<?php daimzap_icon( 'location-dot' ); ?>
							<span><?php echo esc_html( $address ); ?></span>
						</li>
					<?php endif; ?>

					<?php if ( $hours ) : ?>
						<li class="dz-footer__contact">
							<?php daimzap_icon( 'clock' ); ?>
							<span><?php echo esc_html( $hours ); ?></span>
						</li>
					<?php endif; ?>
				</ul>

				<?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
					<div class="mt-6">
						<?php dynamic_sidebar( 'footer-widgets' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="dz-footer__bar">
			<p>
				<?php
				printf(
					/* translators: %1$s: current year, %2$s: site name. */
					esc_html__( '© %1$s %2$s. Все права защищены.', 'daimzap' ),
					esc_html( gmdate( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</p>

			<?php if ( has_nav_menu( 'utilities' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'utilities',
						'container'      => false,
						'menu_class'     => 'flex flex-wrap gap-x-4 gap-y-1 list-none m-0 p-0',
						'depth'          => 1,
					)
				);
				?>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
