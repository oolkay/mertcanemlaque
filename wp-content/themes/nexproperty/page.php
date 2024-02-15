<?php

global $pagename, $wp_query;

$pagename = get_query_var('pagename');  
if ( $pagename  ) {  
    // If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object  
    $current_post = $wp_query->get_queried_object();  
    $pagename = $current_post->post_title;  
}
$tags = get_the_tag_list();

$page_heading = $pagename;

if(empty($page_heading))
$page_heading = get_the_title();

if(is_page('blog') && get_theme_mod( 'page_heading_text' ))
	$page_heading = get_theme_mod( 'page_heading_text' );

$sidebar_active = ( is_active_sidebar( 'sidebar' ) && ( get_theme_mod( 'show_page_sidebar', 'yes' ) == 'yes' ) );
$show_sidebar_class =  ( $sidebar_active ) ? 'col-lg-9 col-md-12 col-sm-12 col-12' : 'col-lg-12 col-md-12 col-sm-12 col-12';

get_header();
?>
		
		<?php if(function_exists('is_account_page') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page())) : ?>
			<section class="blog-standart">
				<h1 class="blog-hd"><?php the_title(); ?></h1>
			</section><!--blog-standart end-->
		<?php else:?>
			<?php if( get_theme_mod( 'show_page_heading', 'yes' ) && $page_heading ) : ?>
				<section class="blog-standart">
					<h1 class="blog-hd"><?php echo esc_html( $page_heading ); ?></h1>
				</section><!--blog-standart end-->
			<?php endif; ?>
		<?php endif; ?>
		<section class="main-content" id="content">
			<div class="container">
				<div class="row">
					<div class="<?php echo esc_attr( $show_sidebar_class ); ?>">
						<div class="blog-items">
						<?php
						while ( have_posts() ) :
							the_post();

							get_template_part( 'template-parts/content' );
							if( get_theme_mod( 'show_author_info' ) == 'yes' ) {
								get_template_part( 'template-parts/authorinfo', get_post_type() );
							}
							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;

						endwhile; // End of the loop.
						?>
						</div><!--featur-prop-sec end-->
					</div>
					<?php 
					if( $sidebar_active ) : ?>
						<div class="col-lg-3 col-md-12 col-sm-12 col-12">
							<div class="sidebar">
								<?php dynamic_sidebar( 'sidebar' ); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section><!--standert-prop end-->

<?php
get_footer();
