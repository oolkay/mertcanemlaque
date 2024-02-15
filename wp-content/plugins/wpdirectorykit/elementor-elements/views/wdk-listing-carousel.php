<?php
/**
 * The template for Element Listing Images Slider.
 * This is the template that elementor element slider, carousel
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-listing-carousel wdk_js_gallery">
        <?php if(count($images) > 0):?>
            <div class=" <?php if(count($images) == 1):?> full_width <?php endif;?> wdk_listing_slider_box wdk-listing-carousel_arrows_in <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo esc_attr(join(' ', [$settings['styles_carousel_dots_position_style'],$settings['styles_carousel_arrows_position'],$settings['direction']]));?>">
                <div class="wdk_listing_slider_ini">
                <?php foreach($images as $image):?>
                    <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                        <div class="wdk-col">
                            <a class="wdk-listing-image-card">
                                <video controls src="<?php echo esc_url(wmvc_show_data('src',$image));?>"  alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image"></video>
                            </a>
                        </div>
                    <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                        <div class="wdk-col">
                            <a class="wdk-listing-image-card">
                                <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                            </a>
                        </div>
                    <?php endif;?>
                <?php endforeach;?> 

                <?php if(!empty($images) && 1 < wmvc_count($images)):?>
                    </div>
                        <div class="wdk-listing-carousel_arrows">
                            <a class="wdk-slider-prev wdk-listing-carousel_arrow">
                                <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_left'], [ 'aria-hidden' => 'true' ] ); ?>
                            </a>
                            <a class="wdk-slider-next wdk-listing-carousel_arrow">
                                <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_right'], [ 'aria-hidden' => 'true' ] ); ?>
                            </a>
                        </div>
                    </div>
                <?php else:?>
                </div>
                <?php endif;?>
        <?php endif;?>
    </div>
    <script>
        jQuery(document).ready(function($){
            $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_listing_slider_ini').slick({
                <?php if(1 == wmvc_count($images)):?>
                dots: false,
                arrows: false,
                <?php else:?>
                dots: true,
                <?php endif;?>

                <?php if(wmvc_show_data('layout_carousel_is_infinite', $settings) == 'true'):?>
                    infinite: <?php echo wmvc_show_data('layout_carousel_nav_is_infinite', $settings, 'true');?>,
                <?php endif;?>

                <?php if(!empty(wmvc_show_data('layout_carousel_speed', $settings))):?>
                    speed: <?php echo intval(wmvc_show_data('layout_carousel_nav_speed', $settings, 100));?>,
                <?php endif;?>

                <?php if(wmvc_show_data('layout_carousel_is_center', $settings) == 'true'):?>
                    centerMode: true,
                <?php endif;?>

                <?php if(wmvc_show_data('layout_carousel_is_autoplay', $settings) == 'true'):?>
                    autoplay: true,
                <?php endif;?>

                <?php if(!empty(wmvc_show_data('layout_carousel_autoplay_speed', $settings))):?>
                    autoplaySpeed: <?php echo intval(wmvc_show_data('layout_carousel_autoplay_speed', $settings, 2000));?>,
                <?php endif;?>

                <?php if(wmvc_show_data('layout_carousel_variableWidth', $settings) == 'true'):?>
                    variableWidth: true,
                <?php endif;?>
                nextArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-listing-carousel_arrows .wdk-slider-next'),
                prevArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-listing-carousel_arrows .wdk-slider-prev'),
                customPaging: function(slider, i) {
                    // this example would render "tabs" with titles
                    return '<span class="wdk_dot"><?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_dots_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>';
                },
                slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_columns', $settings, '4')))) ? wmvc_show_data('styles_thmbn_columns', $settings, '4') : 4;?>,
                responsive: [
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_columns_tablet', $settings, '3')))) ? wmvc_show_data('styles_thmbn_columns_tablet', $settings, '3') : 3;?>,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_columns_mobile', $settings, '1')))) ? wmvc_show_data('styles_thmbn_columns_mobile', $settings, '1') : 1;?>,
                        }
                    },
                ]
            });
        })
    </script>
</div>

