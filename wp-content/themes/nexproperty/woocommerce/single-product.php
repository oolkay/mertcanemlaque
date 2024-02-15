<?php


$tags = get_the_tag_list();
$sidebar_active = ( is_active_sidebar( 'sidebar-woo-product' ) && ( get_theme_mod( 'show_page_sidebar', 'yes' ) == 'yes' ) );
$show_sidebar_class =  ( $sidebar_active ) ? 'col-lg-9 col-md-12 col-sm-12 col-12' : 'col-lg-12 col-md-12 col-sm-12 col-12';

get_header();
?>

		<section class="blog-standart">
			<h1 class="blog-hd">
            <?php if(is_product()):?>
                    <?php echo esc_html__("Product", "nexproperty");?>
                <?php elseif(is_single()):?>
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
                <?php elseif(is_shop()):?>
                    <?php echo esc_html__("Shop", "nexproperty");?>
                <?php elseif(is_archive()):?>
                    <?php echo esc_html__("Archive", "nexproperty");?>
                <?php else:?>
                    <?php echo esc_html(get_bloginfo('name')) ?>
                <?php endif;?>
            </h1>
		</section><!--blog-standart end-->

		<section class="main-content" id="content">
			<div class="container">
				<div class="row">
					<div class="<?php echo esc_attr( $show_sidebar_class ); ?>">
						<div class="blog-items">
                            <?php
                                    /**
                                     * woocommerce_before_main_content hook.
                                     *
                                     * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
                                     * @hooked woocommerce_breadcrumb - 20
                                     */
                                    do_action( 'woocommerce_before_main_content' );
                            ?>

                                    <?php while ( have_posts() ) : the_post(); ?>

                                            <?php wc_get_template_part( 'content', 'single-product' ); ?>

                                    <?php endwhile; // end of the loop. ?>

                            <?php
                                    /**
                                     * woocommerce_after_main_content hook.
                                     *
                                     * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
                                     */
                                    do_action( 'woocommerce_after_main_content' );
                            ?>
						</div><!--featur-prop-sec end-->
					</div>
					<?php 
					if( $sidebar_active ) : ?>
						<div class="col-lg-3 col-md-12 col-sm-12 col-12">
							<div class="sidebar">
								<?php dynamic_sidebar( 'sidebar-woo-product' ); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section><!--standert-prop end-->

<?php
get_footer();
