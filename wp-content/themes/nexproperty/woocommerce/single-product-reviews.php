<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">
	<div id="comments" class="comment-section comment_p-section">
		<h3 class="woocommerce-Reviews-title p-title">
			<?php
			$count = $product->get_review_count();
			if ( $count && wc_review_ratings_enabled() ) {
				/* translators: 1: reviews count 2: product name */
				$reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'nexproperty' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
				echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
			} else {
				esc_html_e( 'Reviews', 'nexproperty' );
			}
			?>
		</h3>

		<?php if ( have_comments() ) : ?>
		<ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'avatar_size' => 100,'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

                        <?php
                            the_comments_pagination(array(
                                'prev_text' => '<span class="screen-reader-text">' . esc_html__('Previous', 'nexproperty') . '</span>',
                                'next_text' => '<span class="screen-reader-text">' . esc_html__('Next', 'nexproperty') . '</span>',
                            ));
			?>
		<?php else : ?>
			<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'nexproperty' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
				$commenter = wp_get_current_commenter();
                                $fields = array(
                                    'author' =>
                                    '<div class="row"><div class="col-lg-6 col-md-6"><div class="form-field">' .
                                    '<input id="author" name="author" placeholder="' . esc_attr__('Your Name', 'nexproperty') . ( $req ? '*' : '' ) . '" type="text" value="' . esc_attr($commenter['comment_author']) .
                                    '" /></div></div>',
                                    'email' =>
                                    '<div class="col-lg-6 col-md-6"><div class="form-field">' .
                                    '<input id="email" name="email" type="text" placeholder="' . esc_attr__('Email', 'nexproperty') . ( $req ? '*' : '' ) . '" value="' . esc_attr($commenter['comment_author_email']) .
                                    '"  /></div></div></div>',
                                );
				$comment_form = array(
                                        'class_form' => 'nexproperty-form',
					/* translators: %s is product title */
					'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'nexproperty' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'nexproperty' ), get_the_title() ),
					/* translators: %s is product title */
					'title_reply_to'      => esc_html__( 'Leave a Review to %s', 'nexproperty' ),
					'title_reply_before'  => '<h3 id="reply-title" class="p-title">',
					'title_reply_after'   => '</h3>',
                                        'cancel_reply_link' => esc_html__('Cancel Review', 'nexproperty'),
					'comment_notes_after' => '',
                                        'fields' => apply_filters('comment_form_default_fields', $fields),
                                        'format' => 'xhtml',
                                                    'submit_button' => '<span class="col-lg-12 pl-0 pr-0"><button  name="submit" type="submit" id="submit" class="btn-default">' . esc_html__('Post Your Review', 'nexproperty') . '</button></span>',
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

				$account_page_url = wc_get_page_permalink( 'myaccount' );
				if ( $account_page_url ) {
					/* translators: %s opening and closing link tags respectively */
					$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'nexproperty' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
				}
                                $comment_form['comment_field'] ='';
				if ( wc_review_ratings_enabled() ) {
					$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'nexproperty' ) . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__( 'Rate&hellip;', 'nexproperty' ) . '</option>
						<option value="5">' . esc_html__( 'Perfect', 'nexproperty' ) . '</option>
						<option value="4">' . esc_html__( 'Good', 'nexproperty' ) . '</option>
						<option value="3">' . esc_html__( 'Average', 'nexproperty' ) . '</option>
						<option value="2">' . esc_html__( 'Not that bad', 'nexproperty' ) . '</option>
						<option value="1">' . esc_html__( 'Very poor', 'nexproperty' ) . '</option>
					</select></div>';
				}

				$comment_form['comment_field'] .= '
                                                                <div class="w-comment form-field">
                                                                    <textarea id="comment" name="comment" placeholder="' . esc_attr__('Comment', 'nexproperty') . '*" required="required"></textarea>
                                                                </div>
                                                            ';

				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>
	<?php else : ?>
		<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'nexproperty' ); ?></p>
	<?php endif; ?>

	<div class="clear"></div>
</div>
