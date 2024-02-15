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
    <div class="wdk-listing-slider">
        <?php if(count($images) > 0):?>
            <div class="<?php if($settings['popup_enable'] == 'yes'):?> wdk_js_gallery <?php endif;?> wdk_listing_slider_box <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo esc_attr(join(' ', [$settings['styles_carousel_dots_position_style'], $settings['styles_carousel_arrows_position_style'],$settings['styles_carousel_arrows_position'],$settings['styles_carousel_arrows_position_style']]));?>">
                <div class="wdk_listing_slider_ini">
                <?php foreach($images as $image):?>
                    <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                        <div class="wdk-col">
                            <div class="wdk-listing-image-card">
                                <video controls src="<?php echo esc_url(wmvc_show_data('src',$image));?>"  alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>"></video>
                            </div> 
                        </div>
                    <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                        <div class="wdk-col">
                            <div class="wdk-listing-image-card">
                                <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                            </div>
                        </div>
                    <?php endif;?>
                <?php endforeach;?> 

                <?php if(!empty($images) && wmvc_show_data('layout_carousel_columns', $settings,1) < wmvc_count($images)):?>
                    </div>
                        <div class="wdk-listing-slider_arrows">
                            <a class="wdk-slider-prev wdk-listing-slider_arrow">
                                <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_left'], [ 'aria-hidden' => 'true' ] ); ?>
                            </a>
                            <a class="wdk-slider-next wdk-listing-slider_arrow">
                                <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_right'], [ 'aria-hidden' => 'true' ] ); ?>
                            </a>
                        </div>
                    </div>
                <?php else:?>
                </div>
                </div>
                <?php endif;?>
        <?php endif;?>

        <?php if(count($images) > 1):?>
            <div class="banner-thumbs-con elementor-section elementor-section-boxed">
                <div class="elementor-container">
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
            </div>
        <?php endif;?>
    </div>
    <script>
        jQuery(document).ready(function($){
            $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_listing_slider_ini').slick({
                <?php if(!empty($images) && wmvc_show_data('layout_carousel_columns', $settings,1) < wmvc_count($images)):?>
                dots: true,
                arrows: true,
                <?php else:?>
                dots: false,
                arrows: false,
                <?php endif;?>
                slidesToShow: <?php echo wmvc_show_data('layout_carousel_columns', $settings, 1);?>,
                slidesToScroll: <?php echo wmvc_show_data('layout_carousel_columns', $settings,1);?>,
                <?php if(!empty(wmvc_show_data('layout_carousel_is_infinite', $settings))):?>
                infinite: <?php echo wmvc_show_data('layout_carousel_is_infinite', $settings, 'true');?>,
                <?php endif;?>
                <?php if(!empty(wmvc_show_data('layout_carousel_is_autoplay', $settings))):?>
                autoplay: <?php echo wmvc_show_data('layout_carousel_is_autoplay', $settings, 'false');?>,
                <?php endif;?>
                nextArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-listing-slider_arrows .wdk-slider-next'),
                prevArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-listing-slider_arrows .wdk-slider-prev'),
                customPaging: function(slider, i) {
                    // this example would render "tabs" with titles
                    return '<span class="wdk_lr_dot"><?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_dots_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>';
                },
                responsive: [
                    {
                    breakpoint: 600,
                    settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    },
                ],
                asNavFor: '#wdk_el_<?php echo esc_html($id_element);?> .banner-thumbs',
            })

            $('#wdk_el_<?php echo esc_html($id_element);?> .banner-thumbs').slick({
                slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_nav_columns', $settings, '4')))) ? wmvc_show_data('styles_thmbn_nav_columns', $settings, '4') : 4;?>,
                slidesToScroll: 1,
                asNavFor: '#wdk_el_<?php echo esc_html($id_element);?> .wdk_listing_slider_ini',
                dots: false,
                centerMode: false,
                dots: false,
                arrows: false,
                focusOnSelect: true,
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

        })
    </script>
</div>

