<?php
/**
 * The template for Element Listing Similar Listings.
 * This is the template that elementor element listings, results, grid, list
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-listings-results <?php echo wmvc_show_data('styles_thmbn_des_type',$settings, '');?> view-<?php echo wmvc_show_data('layout_type',$settings, '');?>">
        <?php if(count($results) > 0):?>
            <?php if($settings['layout_type'] == 'carousel'):?>
                <div class="wdk_results_listings_slider_box <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo join(' ', [$settings['styles_carousel_dots_position_style'],$settings['styles_carousel_arrows_position']]);?>">
                <div class="wdk_results_listings_slider_body">
                <div class="wdk_results_listings_slider_ini">
            <?php else:?>
                <div class="wdk-row">
            <?php endif;?>
            <?php foreach($results as $listing):?>
                <div class="wdk-col">
                    <?php echo wdk_listing_card($listing, $settings);?>
                </div>
            <?php endforeach;?> 
            <?php if($settings['layout_type'] == 'carousel'):?>
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
        <?php elseif($settings['disable_not_found_alert'] != 'yes'):?>
            <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Agents listings not found', 'wpdirectorykit');?></p>
        <?php endif;?>
    </div>
    <?php if($settings['layout_type'] == 'carousel'):?>
    <script>
        jQuery(document).ready(function($){
            var el = $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_results_listings_slider_ini').slick({
                <?php if(!empty($results) && wmvc_show_data('layout_carousel_columns', $settings,1) < wmvc_count($results)):?>
                dots: true,
                arrows: true,
                <?php else:?>
                dots: false,
                arrows: false,
                <?php endif;?>
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
                    wdk_favorite('.wdk_results_listings_slider_ini');
                }
                
                if (typeof wdk_init_compare_elem == 'function') {
                    wdk_init_compare_elem();
                }
            });

            wdk_slick_slider_init(el, ()=>{
                wdk_result_listings_thumbnail_slider(el);
                                
                if (typeof wdk_favorite == 'function') {
                    wdk_favorite('.wdk_results_listings_slider_ini');
                }
                
                if (typeof wdk_init_compare_elem == 'function') {
                    wdk_init_compare_elem();
                }
            });
        })
    </script>
    <?php endif;?>
</div>

