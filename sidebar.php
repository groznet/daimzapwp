<?php
/**
 * Blog sidebar.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
	return;
}
?>
<aside class="dz-blog-sidebar" aria-label="<?php esc_attr_e( 'Боковая панель', 'daimzap' ); ?>">
	<?php dynamic_sidebar( 'blog-sidebar' ); ?>
</aside>
