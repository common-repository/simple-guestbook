<?php

/**
 * The file that defines the walker comment class
 *
 * @since      1.0.0
 *
 * @package    Simple_Guestbook
 * @subpackage Simple_Guestbook/public
 */

class Simple_Guestbook_Walker_Comment extends Walker_Comment {
    protected function html5_comment( $comment, $depth, $args ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

		$commenter = wp_get_current_commenter();
		$show_pending_links = ! empty( $commenter['comment_author'] );
		$avatar_size = $args['avatar_size'] ? $args['avatar_size'] : 32;
		$show_avatar = 0 != $avatar_size ? true : false;

		if ($show_avatar) {
			// Get options
			$use_custom_avatar = '1' === Simple_Guestbook_Options::get_plugin_option('avataroption') ? true : false;
			$avatar_url = Simple_Guestbook_Options::get_plugin_option('avatarurl');

			if ($use_custom_avatar)
			{
				$avatar = '<img src="' . $avatar_url . '" width="' . $avatar_size . '" height="' . $avatar_size . '" class="avatar avatar-' . $avatar_size . ' photo wp-block-avatar__image" style="border-radius: ' . $avatar_size . 'px;" />';
			} else {
				// Set avatar CSS
				$author_args = array(
					'class'			=> 'avatar avatar-' . $avatar_size . ' photo wp-block-avatar__image',
					'extra_attr'	=> 'style="border-radius: ' . $avatar_size . 'px"'
				);
				$avatar = get_avatar( $comment, $avatar_size, '', $commenter['comment_author'], $author_args);
			}
		}

		// If this is a reply set css class accordingly
		$body_class = '0' != $comment->comment_parent ? 'simple-guestbook-body simple-guestbook-reply' : 'simple-guestbook-body';

		// Begin output
		?>
		<<?php echo esc_attr($tag); ?> id="simple-guestbook-entry-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '', $comment ); ?>>
			<article id="div-simple-guestbook-<?php comment_ID(); ?>" class="<?php echo esc_attr($body_class); ?>">
				<footer>
					<div class="simple-guestbook-meta">
						<?php
						if ( $show_avatar ) {
							echo '<div id="simple-guestbook-avatar" class="simple-guestbook-avatar">' . wp_kses_post($avatar) . '</div>';
						}

						if ( '1' == $comment->comment_approved && get_comment_author_url( $comment ) != '') {
							echo '<div id="simple-guestbook-author" class="simple-guestbook-author fn"><a href="' . esc_url(get_comment_author_url( $comment )) . '" target="_blank" style="text-decoration:none;">' . esc_html(get_comment_author( $comment )) . '</a></div>';
						}
						else {
							echo '<div id="simple-guestbook-author" class="simple-guestbook-author fn">' . esc_html(get_comment_author( $comment )) .  '</div>';
						}
						?>
				
						<div class="simple-guestbook-metadata">
							<?php
							printf(
								'<time datetime="%s">%s</time>',
								esc_attr(get_comment_time( 'c' )),
								sprintf(
									/* translators: 1: Comment date, 2: Comment time. */
									esc_html__( '%1$s at %2$s' ),
									esc_attr(get_comment_date( '', $comment )),
									esc_attr(get_comment_time())
								)
							);
							?>
						</div>
					</div>
				</footer>

				<?php
				if ( '1' == $comment->comment_approved || $show_pending_links ) {
					comment_reply_link(
						array_merge(
							$args,
							array(
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<span class="simple-guestbook-reply-link">',
								'after'     => '</span>',
							)
						)
					);
				}
				edit_comment_link( esc_html__( 'Edit' ), ' <span class="simple-guestbook-edit-link">', '</span>' );
				?>

				<div class="simple-guestbook-content">
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p><em class="comment-awaiting-moderation">
							<?php
								if ( $commenter['comment_author_email'] ) {
									esc_html_e('Your comment is awaiting moderation.');
								} else {
									esc_html_e('Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.');
								}
							?>
						</em></p>
					<?php endif; ?>
					<?php
					if ( '1' == $comment->comment_approved || $show_pending_links ) {
						wp_kses_data(comment_text());
					}
					?>
				</div>
			</article>
		<?php
	}
}