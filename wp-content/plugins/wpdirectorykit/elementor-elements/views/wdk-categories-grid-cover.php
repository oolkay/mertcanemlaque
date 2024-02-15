<?php
/**
 * The template for Element categories Grid Cover.
 * This is the template that elementor element categories, images, links
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php
$results_page = wmvc_show_data('conf_link', $settings);
if(!is_array($results_page) && !empty($results_page)) {
    $results_page = get_permalink($results_page);
} else {
    $results_page = get_permalink(wdk_get_option('wdk_results_page'));
}
?>

<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-categories-grid-cover">

            <?php if($settings['enable_carousel'] == 'yes'):?>
                <div class="wdk_slider_box <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo join(' ', [$settings['styles_carousel_dots_position_style'], $settings['styles_carousel_arrows_position']]);?>">
                <div class="wdk_slider_body">
                <div class="wdk_slider_ini">
            <?php else:?>
                <div class="wdk-row <?php if(isset($settings['is_mobile_view_enable']) && $settings['is_mobile_view_enable'] == 'yes'):?> WdkScrollMobileSwipe_enable <?php endif;?>">
            <?php endif;?>

            <?php if(count($results) > 0):?>
                <?php foreach ($results as $key => $value):?>
                <div class="wdk-col">
                    <div class="wdk-categories-card-cover">
                        <?php if(wmvc_show_data('layout_image_type', $settings) == 'icon'):?>
                            <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'icon_id', 'icon_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-icon">
                        <?php else:?>
                            <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'image_id', 'image_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-image">
                        <?php endif;?>
                        <div class="wdk-categories-card-body">
                            <div class="wdk-action-left">
                                <?php if(wmvc_show_data('content_icon_type', $settings) == 'image' && wdk_image_src($value, 'full', NULL,'icon_id')):?>
                                    <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'icon_id', 'icon_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>">
                                <?php elseif(wmvc_show_data('content_icon_type', $settings) == 'font'):?>
                                    <i class="<?php echo wmvc_show_data('font_icon_code', $value,'');?>"></i>
                                <?php endif;?>
                            </div>
                            <div class="wdk-left-content">
                                <h3 class="wdk-title"><?php echo wmvc_show_data('category_title', $value);?></h3>
                                <span class="wdk-listings-count">
                                    <?php
                                        echo esc_html(wdk_sprintf(_nx(
                                                '%1$s Listing',
                                                '%1$s Listings',
                                                wmvc_show_data('listings_counter', $value, '0'),
                                                'profile listings count',
                                                'wpdirectorykit'
                                        ), wmvc_show_data('listings_counter', $value, '0')));
                                    ?>
                                </span>
                            </div>
                            <div class="wdk-action-right">
                                <a class="wdk-category-btn" href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results">
                                    <?php \Elementor\Icons_Manager::render_icon( $settings['link_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                                </a>
                            </div>
                        </div>
                        <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results"  class="wdk-link"></a>
                        <div class="mask"></div>
                        <div class="overlay"></div>
                    </div>
                </div>
                <?php endforeach;?>
            <?php else:?>
                <div class="wdk-col wdk-col-full wdk-col-full-always">
                    <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('categories not found', 'wpdirectorykit');?></p>
                </div>
            <?php endif;?>

            <?php if($settings['enable_carousel'] == 'yes'):?>
                </div>
                    <div class="wdk_slider_arrows">
                        <a class="wdk-slider-prev wdk_lr_slider_arrow">
                            <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_left'], [ 'aria-hidden' => 'true' ] ); ?>
                        </a>
                        <a class="wdk-slider-next wdk_lr_slider_arrow">
                            <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_right'], [ 'aria-hidden' => 'true' ] ); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php else:?>
                </div>
            <?php endif;?>

        </div>
    </div>
    <?php if($settings['enable_carousel'] == 'yes'):?>
    <script>
        jQuery(document).ready(function($){
            var el = $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_slider_ini').slick({
                dots: true,
                arrows: true,
                slidesToShow: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns', $settings, '3')))) ? wmvc_show_data('layout_carousel_columns', $settings, '3') : 3;?>,
                slidesToScroll: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns', $settings, '3')))) ? wmvc_show_data('layout_carousel_columns', $settings, '3') : 3;?>,
                <?php if(!empty(wmvc_show_data('layout_carousel_is_infinite', $settings))):?>
                infinite: <?php echo wmvc_show_data('layout_carousel_is_infinite', $settings, 'true');?>,
                <?php endif;?>
                <?php if(!empty(wmvc_show_data('layout_carousel_is_autoplay', $settings))):?>
                autoplay: <?php echo wmvc_show_data('layout_carousel_is_autoplay', $settings, 'false');?>,
                <?php endif;?>
                nextArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_slider_arrows .wdk-slider-next'),
                prevArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_slider_arrows .wdk-slider-prev'),
                customPaging: function(slider, i) {
                    // this example would render "tabs" with titles
                    return '<span class="wdk_lr_dot"><?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_dots_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>';
                },
                responsive: [
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns_tablet', $settings, '2')))) ? wmvc_show_data('layout_carousel_columns_tablet', $settings, '2') : 2;?>,
                            slidesToScroll: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns_tablet', $settings, '2')))) ? wmvc_show_data('layout_carousel_columns_tablet', $settings, '2') : 2;?>,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns_mobile', $settings, '1')))) ? wmvc_show_data('layout_carousel_columns_mobile', $settings, '1') : 1;?>,
                            slidesToScroll: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns_mobile', $settings, '1')))) ? wmvc_show_data('layout_carousel_columns_mobile', $settings, '1') : 1;?>,
                        }
                    },
                ]
            }).on('breakpoint', function(event, slick, breakpoint){
                wdk_result_listings_thumbnail_slider(el);
                                
                if (typeof wdk_favorite == 'function') {
                    wdk_favorite('.wdk_slider_ini');
                }
                
                if (typeof wdk_init_compare_elem == 'function') {
                    wdk_init_compare_elem();
                }
            });

            wdk_slick_slider_init(el, ()=>{
                wdk_result_listings_thumbnail_slider(el);
                                
                if (typeof wdk_favorite == 'function') {
                    wdk_favorite('.wdk_slider_ini');
                }
                
                if (typeof wdk_init_compare_elem == 'function') {
                    wdk_init_compare_elem();
                }
            });
        })
    </script>
    <?php endif;?>
</div>
