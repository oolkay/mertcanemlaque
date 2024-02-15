<?php

namespace NexProperty;

// Walker class used to display comments/reviews on listings.
class Comment_Walker extends \Walker_Comment {

	var $tree_type = 'comment';
	var $db_fields = array( 'parent' => 'comment_parent', 'id' => 'comment_ID' );
	var $current_reply_link;

	function __construct() {
		?><div class="comments-section"><h3><?php esc_html_e( 'Comments', 'nexproperty' ); ?></h3><ul class="comments-list"><?php
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		?><ul class="comments-list"><?php
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		?></ul></li><?php
	}

	function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
		$depth++;
		$parent_class = ( empty( $args['has_children'] ) ? '' : 'parent' );
		$other_classes = '';
		$is_reply = false;
		if ($depth > 1) {
			$other_classes .= '';
			$is_reply = true;
		}

		?>

		<li <?php comment_class( $parent_class . $other_classes ); ?> id="comment-<?php echo $comment->comment_ID ?>">
			<div class="ath-info">
				<div class="abt-miller-img">

					<?php if ($args['avatar_size'] != 0): ?>
						<div class="abt-miller-img">
							<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
						</div>
					<?php endif ?>
				</div>
				<div class="abt-miller-info">
					<?php if( $comment->comment_approved ) : ?>
						<h3><?php echo esc_html( get_comment_author( $comment->comment_ID )); ?></h3>
						<span><?php esc_html_e( 'Posted on ', 'nexproperty' ); comment_date( 'd F Y', $comment->comment_ID );?></span>
						<?php comment_text( $comment->comment_ID );// if we escape the html then avatar screenshot send by the theme author. by escaping html it just print the avatar html as it is.
						?>
					<?php endif; ?>
				</div>
				<div class="reply comment-info">
					<?php
					comment_reply_link( array_merge( $args, array(
						'depth' => $depth,
						'max_depth' => $args['max_depth'],
						'reply_text' => '<i class="mi insert_comment"></i>' . __( 'Reply', 'nexproperty' ),
						)), $comment->comment_ID); ?>
				</div>
			</div>

		<?php if (!$args['has_children']): ?>
			</li>
		<?php endif ?>

	<?php }

	function end_el(&$output, $comment, $depth = 0, $args = array() ) {
		?></li><?php
	}

	function __destruct() {
		?></div></ul><?php
	}
}
