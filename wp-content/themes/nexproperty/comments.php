<?php

	// You can start editing here -- including this comment!
	if ( have_comments() ) :
		?>
		


			<?php
			wp_list_comments( array(
				'style'      => 'ul',
				'walker'	=> new \NexProperty\Comment_Walker,
				'avatar_size' => 80,
			) );
			?>

		<?php

	

	endif; // Check for have_comments()

	?>
	<div class="comment-section comment_p-section">

    <?php 
    if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
    ?>

    <p class="no-comments"><?php esc_html_e('Comments are closed.', 'nexproperty'); ?></p>
    <?php else: ?>
    <div class="post-comment-sec" id="comment-form">
        <?php
        $required_text = '';

        $fields = array(
            'author' =>
            '<div class="row"><div class="col-lg-4 col-md-4"><div class="form-field">' .
            '<input id="author" name="author" placeholder="' . esc_attr__('Your Name', 'nexproperty') . ( $req ? '*' : '' ) . '" type="text" value="' . esc_attr($commenter['comment_author']) .
            '" /></div></div>',
            'email' =>
            '<div class="col-lg-4 col-md-4"><div class="form-field">' .
            '<input id="email" name="email" type="text" placeholder="' . esc_attr__('Email', 'nexproperty') . ( $req ? '*' : '' ) . '" value="' . esc_attr($commenter['comment_author_email']) .
            '"  /></div></div> ',
            'url' =>
            '<div class="col-lg-4 col-md-4"><div class="form-field">' .
            '<input id="url" name="url" type="text"  placeholder="' . esc_attr__('Website', 'nexproperty') . ( $req ? '*' : '' ) . '" value="' . esc_attr($commenter['comment_author_url']) .
            '"  /></div></div></div> ',
        );

        $args = array(
            'id_form' => 'commentform',
            'class_form' => 'nexproperty-form',
            'title_reply_before' => '<h3 id="reply-title" class="p-title">',
            'title_reply' => esc_html__('Leave A Reply', 'nexproperty'),
            /* translators: 1: number of comments, 2: post title */
            'title_reply_to' => esc_html__('Leave a Reply to %s', 'nexproperty'),
            'cancel_reply_link' => esc_html__('Cancel Reply', 'nexproperty'),
            'format' => 'xhtml',
            'fields' => apply_filters('comment_form_default_fields', $fields),
            'submit_button' => '<span class="col-lg-12 pl-0 pr-0"><button  name="submit" type="submit" id="submit" class="btn-default">' . esc_html__('Post Your Reply', 'nexproperty') . '</button></span>',
            'comment_field' => '<div class="col-lg-12 pl-0 pr-0">
                                    <div class="form-field">
                                        <textarea id="comment" name="comment" placeholder="' . esc_attr__('Post comment', 'nexproperty') . '*" required="required"></textarea>
                                    </div>
                                </div>',
            'must_log_in' => '<p class="must-log-in">' .
            sprintf(
                    /* translators: link to post comments */
                    wp_kses_post(__('You must be <a href="%s">logged in</a> to post a comment.', 'nexproperty')), wp_login_url(apply_filters('the_permalink', get_permalink()))
            ) . '</p>',
            'logged_in_as' => '<p class="logged-in-as">' .
            sprintf(
                    /* translators: logged link to post comments */
                    wp_kses_post(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'nexproperty')), admin_url('profile.php'), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink()))
            ) . '</p>',
            'comment_notes_before' => '',
            'comment_notes_after' => '',
        );

        function nexproperty_move_comment_field_to_bottom($fields) {
            if (isset($fields['comment'])) {
                $comment_field = $fields['comment'];
                unset($fields['comment']);
                $fields['comment'] = $comment_field;
            }

            if (isset($fields['cookies'])) {
                $consent = $fields['cookies'];
                unset($fields['cookies']);
                $fields['cookies'] = '<p class="comment-form-cookies-consent input-field">'
                        . '<label for="wp-comment-cookies-consent" class="checkbox-styles"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes">'
                        . '<span class="checkmark"></span>' . esc_html__("Save my name, email, and website in this browser for the next time I comment", "nexproperty") . '</label>'
                        . '</p>';
            }
            return $fields;
        }

        add_filter('comment_form_fields', 'nexproperty_move_comment_field_to_bottom');
        comment_form($args);
        ?>
    </div>
<?php
endif;
?>
</div><!--comment-section end-->