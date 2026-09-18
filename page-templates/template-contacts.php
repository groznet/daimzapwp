<?php
/**
 * Template Name: Контакты
 * Template Post Type: page
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

get_header();

$phone    = daimzap_option( 'daimzap_phone' );
$whatsapp = daimzap_option( 'daimzap_whatsapp' );
$email    = daimzap_option( 'daimzap_email' );
$address  = daimzap_option( 'daimzap_address' );
$hours    = daimzap_option( 'daimzap_hours' );
?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>

	<?php daimzap_page_header( get_the_title() ); ?>

	<main id="main" class="dz-main">
		<div class="dz-container">
			<div class="dz-content-layout">
				<ul class="dz-contacts mb-8">
					<?php if ( $phone ) : ?>
						<li class="dz-contact-card">
							<span class="dz-contact-card__label"><?php esc_html_e( 'Телефон', 'daimzap' ); ?></span>
							<a class="dz-contact-card__value" href="<?php echo esc_url( daimzap_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( $whatsapp ) : ?>
						<li class="dz-contact-card">
							<span class="dz-contact-card__label"><?php esc_html_e( 'WhatsApp', 'daimzap' ); ?></span>
							<a class="dz-contact-card__value" href="<?php echo esc_url( daimzap_whatsapp_href( $whatsapp ) ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html( $whatsapp ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( $email ) : ?>
						<li class="dz-contact-card">
							<span class="dz-contact-card__label"><?php esc_html_e( 'E-mail', 'daimzap' ); ?></span>
							<a class="dz-contact-card__value" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( $address ) : ?>
						<li class="dz-contact-card">
							<span class="dz-contact-card__label"><?php esc_html_e( 'Адрес', 'daimzap' ); ?></span>
							<span class="dz-contact-card__value"><?php echo esc_html( $address ); ?></span>
						</li>
					<?php endif; ?>

					<?php if ( $hours ) : ?>
						<li class="dz-contact-card">
							<span class="dz-contact-card__label"><?php esc_html_e( 'Часы работы', 'daimzap' ); ?></span>
							<span class="dz-contact-card__value"><?php echo esc_html( $hours ); ?></span>
						</li>
					<?php endif; ?>
				</ul>

				<div class="dz-prose"><?php the_content(); ?></div>
			</div>
		</div>
	</main>
<?php endwhile; ?>

<?php
get_footer();
