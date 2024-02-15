<?php

global $pagename, $wp_query;

// $pagename = get_query_var('pagename');  
if ( !$pagename && $id > 0 ) {  
    // If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object  
    $current_post = $wp_query->get_queried_object();  
    $pagename = $current_post->post_name;
}

if( is_archive() ) { 
	$pagename = get_the_archive_title();
}

$page_heading = ( get_theme_mod( 'page_heading_text', 'Blog' ) ) ? get_theme_mod( 'page_heading_text', 'Blog' ) : $pagename;
$sidebar_active = ( is_active_sidebar( 'sidebar-1' ) && ( get_theme_mod( 'show_page_sidebar', 'yes' ) == 'yes' ) );
$show_sidebar_class =  ( $sidebar_active ) ? 'col-lg-9 col-md-12 col-sm-12 col-12' : 'col-lg-12 col-md-12 col-sm-12 col-12';

get_header();
?>
	<?php if( get_theme_mod( 'show_page_heading', 'yes' ) == 'yes' && $page_heading ) : ?>
		<section class="blog-standart">
			<h1 class="blog-hd"><?php echo esc_html( $page_heading ); ?></h1>
		</section><!--blog-standart end-->
	<?php endif; ?>

		<section class="main-content" id="content">
			<div class="container">
				<div class="row">
					<div class="<?php echo esc_attr( $show_sidebar_class ); ?>">
						<div class="blog-items">
							<?php
							if ( have_posts() ) :

								while ( have_posts() ) : the_post();

									get_template_part( 'template-parts/content', get_post_type() );

								endwhile;
								$total_pages = $wp_query->max_num_pages;

							    if ($total_pages > 1) : ?>
							    	<div class="pagi_nation">
									        <?php $args = array(
													'mid_size'  => 1,
													'prev_text' => '<i class="fa fa-angle-left"></i>',
													'next_text' => '<i class="fa fa-angle-right"></i>',
													'screen_reader_text' => __( 'Posts navigation', 'nexproperty' ),
													'show_all' => true,
												) ?>
									        <?php the_posts_pagination( $args ); ?>
									</div>
							    <?php endif;

							else :

								get_template_part( 'template-parts/content', 'none' );

							endif;
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
