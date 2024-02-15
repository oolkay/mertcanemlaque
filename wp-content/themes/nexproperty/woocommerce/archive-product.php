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
$sidebar_active = ( is_active_sidebar( 'sidebar-woo-products' ) && ( get_theme_mod( 'show_page_sidebar', 'yes' ) == 'yes' ) );
$show_sidebar_class =  ( $sidebar_active ) ? 'col-lg-9 col-md-12 col-sm-12 col-12' : 'col-lg-12 col-md-12 col-sm-12 col-12';

get_header();
?>
	<?php if( get_theme_mod( 'show_page_heading', 'yes' ) == 'yes' && $page_heading ) : ?>
		<section class="blog-standart">
			<h1 class="blog-hd">
            <?php if(is_single()):?>
                    <?php echo esc_html__("Blog", "nexproperty");?>
                <?php elseif(isset($pagename) && $pagename == "blog-standard"):?>
                    <?php
                        $nexproperty_page = get_page_by_path( $pagename );
                        echo esc_html(get_the_title( $nexproperty_page ));
                    ?>
                <?php elseif(is_home()):?>
                    <?php echo esc_html__("Homepage", "nexproperty");?>
                <?php elseif(is_page()):?>
                    <?php echo esc_html__("Page", "nexproperty");?>
                <?php elseif(is_category()):?>
                    <?php
                            the_archive_title( );
                    ?>
                <?php elseif(is_search()):?>
                    <?php echo esc_html__("Search", "nexproperty");?>
                <?php elseif(is_tag()):?>
                    <?php the_archive_title( ); ?>
                <?php elseif(is_day()):?>
                    <?php echo esc_html__("Day", "nexproperty").': '.esc_html(get_the_time('d'));?>
                <?php elseif(is_month()):?>
                    <?php echo esc_html__("Month", "nexproperty").': '.esc_html(get_the_time('F'));?>
                <?php elseif(is_year()):?>
                    <?php echo esc_html__("Year", "nexproperty").': '.esc_html(get_the_time('Y'));?>
                <?php elseif(is_author()):?>
                    <?php echo esc_html__("Author", "nexproperty");?>
                <?php elseif(is_404()):?>
                    <?php echo esc_html__("404", "nexproperty");?>
                <?php elseif(is_product_tag()):?>
                    <?php
                            the_archive_title( );
                    ?>
                <?php elseif(is_product_category()):?>
                    <?php
                            the_archive_title( );
                    ?>
                <?php elseif(is_shop()):?>
                    <?php echo esc_html__("Shop", "nexproperty");?>
                <?php elseif(is_archive()):?>
                    <?php echo esc_html__("Archive", "nexproperty");?>
                <?php else:?>
                    <?php echo esc_html(get_bloginfo('name')) ?>
                <?php endif;?>
            </h1>
		</section><!--blog-standart end-->
	<?php endif; ?>

		<section class="main-content" id="content">
			<div class="container">
            <div class="blog-single-details">
            <div class="row">
                <div class="<?php echo esc_attr( $show_sidebar_class ); ?>">
                    <div class="blog-posts">
                        <?php
                            if ( woocommerce_product_loop() ) {
                                    /**
                                     * Hook: woocommerce_before_shop_loop.
                                     *
                                     * @hooked woocommerce_output_all_notices - 10
                                     * @hooked woocommerce_result_count - 20
                                     * @hooked woocommerce_catalog_ordering - 30
                                     */
                                    do_action( 'woocommerce_before_shop_loop' );

                                    woocommerce_product_loop_start();

                                    if ( wc_get_loop_prop( 'total' ) ) {
                                            while ( have_posts() ) {
                                                    the_post();

                                                    /**
                                                     * Hook: woocommerce_shop_loop.
                                                     */
                                                    do_action( 'woocommerce_shop_loop' );

                                                    wc_get_template_part( 'content', 'product' );
                                            }
                                    }

                                    woocommerce_product_loop_end();

                                    /**
                                     * Hook: woocommerce_after_shop_loop.
                                     *
                                     * @hooked woocommerce_pagination - 10
                                     */
                                    do_action( 'woocommerce_after_shop_loop' );
                            } else {
                                    /**
                                     * Hook: woocommerce_no_products_found.
                                     *
                                     * @hooked wc_no_products_found - 10
                                     */
                                    do_action( 'woocommerce_no_products_found' );
                            }
                        ?>
                    </div><!--blog-posts end-->
                </div>
                <?php 
                if( $sidebar_active ) : ?>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12">
                        <div class="sidebar">
                            <?php dynamic_sidebar( 'sidebar-woo-products' ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div><!--blog-single-details end-->
    </div>
</section><!--standert-prop end-->

<?php
get_footer();
