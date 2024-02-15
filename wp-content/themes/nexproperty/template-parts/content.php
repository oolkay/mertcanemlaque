<?php

$categories = get_the_category( get_the_ID() );
$cat_name = '';
if( ! empty( $categories ) ) { 
	$cat_name = $categories[0]->cat_name;
}

$show_date = ( get_theme_mod( 'show_post_date', 'yes' ) == 'yes' ) ? true : false;
$show_author = ( get_theme_mod( 'show_post_author', 'yes' ) == 'yes' ) ? true : false;
$show_comments = ( get_theme_mod( 'show_post_comments', 'yes' ) == 'yes' ) ? true : false;
$show_cat = ( get_theme_mod( 'show_post_cat', 'yes' ) == 'yes' ) ? true : false;
$show_tags = ( get_theme_mod( 'show_post_tags', 'yes' ) == 'yes' ) ? true : false;
$show_post_excerpt = ( get_theme_mod( 'show_post_excerpt' ) == 'yes' ) ? true : false;

$num_words = ( abs( get_theme_mod( 'words_limit' ) ) ) ? : 0;
$word_count = str_word_count( strip_tags( get_the_content() ) );
?>
<div <?php post_class('blog-item'); ?>>
	<?php if( get_the_post_thumbnail() && (!function_exists('is_account_page') || (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page())) ) : ?>
		<div class="blog-thumbnail">
			<?php if((!is_single() && !is_page())):?>
			<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" title="<?php the_title(); ?>">
				<?php echo get_the_post_thumbnail( get_the_ID(), 'nexproperty-post-thumbnail-nocrop' ); ?>
			</a>
			<?php else: ?>   
				<?php echo get_the_post_thumbnail( get_the_ID(), 'full' ); ?>
			<?php endif; ?>    
			<span class="category-name"><?php echo esc_html( $cat_name); ?></span>
		</div><!--blog-thumbnail end-->
	<?php endif; ?>
	<div class="blog-info">
		<?php if( $show_author || $show_date || $show_comments ) : ?>
			<ul class="meta">
				<?php if( $show_date ): ?>
					<li>
						<a href="<?php echo esc_url( get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j') ) );  ?>" title="<?php  echo esc_attr( 'Post Date' ); ?>"><?php echo get_the_date( '', get_the_ID() ); ?></a>
					</li>
				<?php endif; 
					if( $show_author ) : ?>
					<li class="posted-by">
						<span><?php esc_html_e( 'by', 'nexproperty' ); ?></span><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo esc_attr( 'Post Author' ); ?>"><?php the_author(); ?></a>
					</li>
				<?php endif; 
				if( $show_comments ) : ?>
					<li>
						<a href="<?php comments_link(); ?>" title="<?php echo esc_attr( 'Number of comments' ); ?>"><?php comments_number( 'No Comments', '1 Comment', '% Comments'); ?></a>
					</li>
				<?php endif; 
				if( $show_cat && !empty( get_the_category() ) ) : ?>
					<li>
						<?php the_category( ' | ' ); ?>
					</li>
				<?php endif;
				if( $show_tags && !empty( get_the_tags() ) ) : ?>
					<li>
						<?php the_tags( '', ' | ' ); ?>
					</li>
				<?php endif; ?>
			</ul><!--meta end-->
		<?php endif; ?>
		<?php  
			if ( is_sticky() ) { ?>
				<span><?php esc_html_e('Featured', 'nexproperty'); ?></span>
			<?php }
		?>
		<?php if( !function_exists('is_account_page') || (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page())) : ?>
			<?php if((!is_single() && !is_page())):?>
				<h3 class="blog-title">
					<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" title="<?php the_title(); ?>">
						<?php the_title(); ?>
					</a>
				</h3>
			<?php endif; ?>      
		<?php endif; ?>      
		<div class="blog-body">
		<?php if( ! is_singular( 'post' ) ) : ?>
			<?php if( $num_words ) : ?>
				<?php echo wp_kses_post( wp_trim_words( get_the_content(), $num_words ) ); ?>
			<?php else: ?>
				<?php if((!is_single() && !is_page()) && $show_post_excerpt):?>
					<?php the_excerpt(); ?>
				<?php else:?>
					<?php the_content(); ?>
				<?php endif;?>
			<?php endif; ?>
			<?php if( $word_count > $num_words && $num_words > 0 ) : ?>
				<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" title="<?php echo esc_attr( 'read more' ) ?>" class="read-more-btn"><?php esc_html_e( 'Continue Reading ', 'nexproperty' ) ?><i class="fa fa-angle-right"></i></a>
			<?php endif; ?>
		<?php else: 
			the_content(); 
		endif; 
		wp_link_pages(
			array(
				'before'      => '<nav class="post-nav-links bg-light-background" aria-label="' . esc_attr__( 'Page', 'nexproperty' ) . '"><span class="label">' . __( 'Pages:', 'nexproperty' ) . '</span>',
				'after'       => '</nav>',
				'link_before' => '<span class="page-number">',
				'link_after'  => '</span>',
			)
		);
		?>
                </div>

	</div><!--blog-info end-->
</div><!--blog-item end-->
