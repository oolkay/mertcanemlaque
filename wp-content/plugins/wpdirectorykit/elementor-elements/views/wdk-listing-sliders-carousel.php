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
    <div class="wdk-listing-sliders-carousel">
        <?php if(count($images) > 2):?>
            <?php if(count($images) > 0):?>
                <div class="wdk_listing_slider_box wdk_js_gallery <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo esc_attr(join(' ', [$settings['styles_carousel_dots_position_style'], $settings['styles_carousel_arrows_position_style'],$settings['styles_carousel_arrows_position'],$settings['styles_carousel_arrows_position_style']]));?>">
                    <div class="wdk_listing_slider_ini">
                    <?php foreach($images as $image):?>
                        <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                            <div class="wdk-col">
                                <video controls src="<?php echo esc_url(wmvc_show_data('src',$image));?>"  alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image-card wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>"></video>
                            </div>
                        <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                            <div class="wdk-col">
                                <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image-card wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                            </div>
                        <?php endif;?>
                    <?php endforeach;?> 

                    <?php if(!empty($images) && 1 < wmvc_count($images)):?>
                        </div>
                            <div class="wdk-listing-sliders-carousel_arrows">
                                <a class="wdk-slider-prev wdk-listing-sliders-carousel_arrow">
                                    <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_left'], [ 'aria-hidden' => 'true' ] ); ?>
                                </a>
                                <a class="wdk-slider-next wdk-listing-sliders-carousel_arrow">
                                    <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_right'], [ 'aria-hidden' => 'true' ] ); ?>
                                </a>
                            </div>
                        </div>
                    <?php else:?>
                    </div>
                    <?php endif;?>
            <?php endif;?>

            <?php if(count($images) > 1):?>
                <div class="wdk-cls-banner-thumbs">
                    <div class="banner-thumbs">
                        <?php foreach($images as $image):?>
                            <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                                <div class="banner-thumb">
                                    <video src="<?php echo esc_url(wmvc_show_data('src',$image));?>"  alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>"></video>
                                </div>
                            <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                                <div class="banner-thumb">
                                    <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                                </div>
                            <?php endif;?>
                        <?php endforeach;?> 
                    </div><!--banner-thumbs end-->
                </div>
            <?php endif;?>
        <?php else:?>
            <div class="wdk-static wdk_listing_slider_box wdk_js_gallery <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo esc_attr(join(' ', [$settings['styles_carousel_dots_position_style'], $settings['styles_carousel_arrows_position_style'],$settings['styles_carousel_arrows_position'],$settings['styles_carousel_arrows_position_style']]));?>">
                <?php foreach($images as $image):?>
                    <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                        <div class="wdk-col">
                            <video controls src="<?php echo esc_url(wmvc_show_data('src',$image));?>"  alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image-card wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>"></video>
                        </div>
                    <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                        <div class="wdk-col">
                            <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image-card wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                        </div>
                    <?php endif;?>
                <?php endforeach;?> 
            </div>
        <?php endif;?>
    </div>
    <script>
        jQuery(document).ready(function($){
            <?php if(count($images) > 2):?>
                $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_listing_slider_ini').slick({
                    dots: false,
                    infinite: true,
                    speed: 300,
                    slidesToShow: 1,
                    variableWidth: true,
                    slidesToShow: 1,
                
                    <?php if(!empty(wmvc_show_data('layout_carousel_is_autoplay', $settings))):?>
                    autoplay: <?php echo wmvc_show_data('layout_carousel_is_autoplay', $settings, 'false');?>,
                    <?php endif;?>
                    autoplaySpeed: '<?php echo esc_html($settings['layout_carousel_speed'], '100');?>',
                
                    nextArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-listing-sliders-carousel_arrows .wdk-slider-next'),
                    prevArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-listing-sliders-carousel_arrows .wdk-slider-prev'),
                    customPaging: function(slider, i) {
                        // this example would render "tabs" with titles
                        return '<span class="wdk_dot"><?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_dots_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>';
                    },
                    responsive: [
                        {
                        breakpoint: 600,
                        settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        },
                    ]
                });

                $('#wdk_el_<?php echo esc_html($id_element);?> .banner-thumbs').slick({
                    asNavFor: '#wdk_el_<?php echo esc_html($id_element);?> .wdk_listing_slider_ini',
                    dots: false,
                    arrows: false,
                    focusOnSelect: true,
                    slidesToScroll: 1,

                    
                    <?php if(wmvc_show_data('layout_carousel_nav_is_infinite', $settings) == 'true'):?>
                        infinite: <?php echo wmvc_show_data('layout_carousel_nav_is_infinite', $settings, 'true');?>,
                    <?php endif;?>

                    <?php if(!empty(wmvc_show_data('layout_carousel_nav_speed', $settings))):?>
                        speed: <?php echo intval(wmvc_show_data('layout_carousel_nav_speed', $settings, 100));?>,
                    <?php endif;?>

                    <?php if(wmvc_show_data('layout_carousel_nav_is_center', $settings) == 'true'):?>
                        centerMode: true,
                    <?php endif;?>

                    <?php if(wmvc_show_data('layout_carousel_nav_is_autoplay', $settings) == 'true'):?>
                        autoplay: true,
                    <?php endif;?>

                    <?php if(!empty(wmvc_show_data('layout_carousel_nav_autoplay_speed', $settings))):?>
                        autoplaySpeed: <?php echo intval(wmvc_show_data('layout_carousel_nav_autoplay_speed', $settings, 2000));?>,
                    <?php endif;?>

                    <?php if(wmvc_show_data('layout_carousel_nav_variableWidth', $settings) == 'true'):?>
                        variableWidth: true,
                    <?php endif;?>

                    slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_nav_columns', $settings, '4')))) ? wmvc_show_data('styles_thmbn_nav_columns', $settings, '4') : 4;?>,
                    responsive: [
                        {
                            breakpoint: 991,
                            settings: {
                                slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_nav_columns_tablet', $settings, '3')))) ? wmvc_show_data('styles_thmbn_nav_columns_tablet', $settings, '3') : 3;?>,
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_nav_columns_mobile', $settings, '2')))) ? wmvc_show_data('styles_thmbn_nav_columns_mobile', $settings, '2') : 2;?>,
                            }
                        },
                    ]
                });
            <?php endif;?>
        })
    </script>
</div>

