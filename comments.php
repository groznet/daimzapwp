<?php
/**
 * Comments template.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="dz-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="mb-4">
			<?php
			$comment_count = (int) get_comments_number();

			printf(
				/* translators: %d: comment count. */
				esc_html( _n( 'Комментарий: %d', 'Комментариев: %d', $comment_count, 'daimzap' ) ),
				esc_html( number_format_i18n( $comment_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'screen_reader_text' => __( 'Навигация по комментариям', 'daimzap' ),
				'class'              => 'dz-pagination',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="dz-help"><?php esc_html_e( 'Комментарии закрыты.', 'daimzap' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_submit'  => 'submit dz-btn dz-btn--dark',
			'title_reply'   => __( 'Оставить комментарий', 'daimzap' ),
			'label_submit'  => __( 'Отправить', 'daimzap' ),
			'comment_field' => sprintf(
				'<p class="comment-form-comment"><label for="comment">%1$s</label><textarea id="comment" name="comment" rows="6" required></textarea></p>',
				esc_html__( 'Комментарий', 'daimzap' )
			),
		)
	);
	?>
</section>
