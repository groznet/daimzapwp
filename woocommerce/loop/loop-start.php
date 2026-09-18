<?php
/**
 * Product loop start.
 *
 * Override of woocommerce/templates/loop/loop-start.php.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

$columns = (int) wc_get_loop_prop( 'columns' );
$columns = $columns > 0 ? $columns : 4;
?>
<ul class="dz-products" style="--dz-columns: <?php echo esc_attr( (string) $columns ); ?>;">
