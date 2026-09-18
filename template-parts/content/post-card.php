<?php
/**
 * Blog post card.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'dz-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="dz-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'medium_large' ); ?>
		</a>
	<?php endif; ?>

	<div class="dz-post-card__body">
		<?php daimzap_entry_meta(); ?>

		<h2 class="dz-post-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<?php if ( has_excerpt() || get_the_content() ) : ?>
			<p class="dz-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>
		<?php endif; ?>
	</div>
</article>
