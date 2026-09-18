<?php
/**
 * Popular vehicle makes.
 *
 * Renders only once the car make attribute exists and carries terms, which
 * happens with the first product import.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

if ( ! daimzap_is_woocommerce_active() ) {
	return;
}

$makes = daimzap_get_vehicle_terms_in_use( 'make', 16 );

if ( ! $makes ) {
	return;
}
?>
<section class="dz-section pt-0">
	<div class="dz-container">
		<div class="dz-section__head">
			<h2 class="dz-section__title"><?php esc_html_e( 'Подбор по марке автомобиля', 'daimzap' ); ?></h2>
		</div>

		<ul class="dz-makes">
			<?php foreach ( $makes as $make ) : ?>
				<li>
					<a class="dz-make" href="<?php echo esc_url( get_term_link( $make ) ); ?>">
						<?php echo esc_html( $make->name ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
