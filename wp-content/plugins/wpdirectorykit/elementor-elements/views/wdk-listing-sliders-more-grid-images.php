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
    <div class="wdk-listing-sliders-more-grid-images <?php if($settings['gallery_enable'] == 'yes'):?> wdk_js_gallery <?php endif;?>">
    <?php if(count($images) > 0):?>
        <?php if(count($images_left) > 1):?>
            <div class="wdk-cls-banner-thumbs images_left
                <?php if(count($images_left) >= 4):?> rows-4
                <?php endif;?>
                ">
                <div class="banner-thumbs">
                    <?php foreach($images_left as $key =>$image):?>
                        <?php //if($i >= $i_max)break; ?>
                        <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                            <div class="banner-grid">
                                <div class="banner-thumb">
                                    <video src="<?php echo esc_url(wmvc_show_data('src',$image));?>" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image"></video>
                                    <a data-key="<?php echo esc_attr($key);?>" href="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="banner-thumb-link wdk-listing-image-card"></a>
                                </div>
                            </div>
                        <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                            <div class="banner-grid">
                                <div class="banner-thumb">
                                    <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                                    <a data-key="<?php echo esc_attr($key);?>" href="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="banner-thumb-link wdk-listing-image-card"></a>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?> 
                </div><!--banner-thumbs end-->
            </div>
        <?php endif;?>

        <?php if($settings['gallery_enable'] == 'yes'):?>
            <?php if(count($images_main) > 0):?>
                <div class="wdk-cls-banner-thumbs images_main
                    <?php if(count($images_main) >9):?> cols-4
                    <?php endif;?>
                    ">
                    <div class="banner-thumbs">
                        <?php foreach($images_main as $image):?>
                            <?php //if($i >= $i_max)break; ?>
                            <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                                <div class="banner-grid">
                                    <div class="banner-thumb">
                                        <video src="<?php echo esc_url(wmvc_show_data('src',$image));?>" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image"></video>
                                        <a href="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="banner-thumb-link wdk-listing-image-card"></a>
                                    </div>
                                </div>
                            <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                                <div class="banner-grid">
                                    <div class="banner-thumb">
                                        <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                                        <a href="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="banner-thumb-link wdk-listing-image-card"></a>
                                    </div>
                                </div>
                            <?php endif;?>
                        <?php endforeach;?> 
                    </div><!--banner-thumbs end-->
                </div>
            <?php endif;?>
        <?php else:?>
            <?php if(count($images) > 0):?>
                <div class="wdk-cls-banner-thumbs images_main 
                    <?php if(count($images_main) >9):?> cols-4
                    <?php endif;?>
                    <?php if($settings['gallery_enable'] != 'yes' && $settings['gallery_main_enable'] == 'true'):?> wdk_js_gallery <?php endif;?>
                ">
                <div class="banner-thumbs">
                <div class="banner-grid <?php if(count($images) == 1):?> full_width <?php endif;?> wdk_listing_slider_box wdk-listing-sliders-more-grid-images_arrows_in wdk-listing-sliders-more-grid-images_dots_in <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo esc_attr(join(' ', [$settings['styles_carousel_arrows_position'],$settings['direction']]));?>">
                    <div class="wdk_listing_slider_ini">
                    <?php foreach($images as $image):?>
                        <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                            <div class="wdk-col">
                                <a class="wdk-listing-image-card <?php if($settings['gallery_enable'] != 'yes' && $settings['gallery_main_enable'] == 'true'):?> wdk-listing-image-card <?php endif;?>">
                                    <video controls src="<?php echo esc_url(wmvc_show_data('src',$image));?>"  alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image"></video>
                                </a>
                            </div>
                        <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                            <div class="wdk-col">
                                <a class="wdk-listing-image-card <?php if($settings['gallery_enable'] != 'yes' && $settings['gallery_main_enable'] == 'true'):?> wdk-listing-image-card <?php endif;?>">
                                    <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                                </a>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?> 
                    <?php if(!empty($images) && 1 < wmvc_count($images)):?>
                        </div>
                            <div class="wdk-listing-sliders-more-grid-images_arrows">
                                <a class="wdk-slider-prev wdk-listing-sliders-more-grid-images_arrow">
                                    <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_left'], [ 'aria-hidden' => 'true' ] ); ?>
                                </a>
                                <a class="wdk-slider-next wdk-listing-sliders-more-grid-images_arrow">
                                    <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_right'], [ 'aria-hidden' => 'true' ] ); ?>
                                </a>
                            </div>
                        </div>
                    <?php else:?>
                    </div>
                    <?php endif;?>
                </div>
                </div>
            <?php endif;?>
        <?php endif;?>
       
        <?php if(count($images_right) > 1):?>
            <div class="wdk-cls-banner-thumbs images_right
                <?php if(count($images_right) >=8):?> rows-4
                <?php endif;?>
                <?php if(count($images_right) == 3 || count($images_right) == 2):?> col-2
                <?php endif;?>
                ">
                <div class="banner-thumbs">
                    <?php foreach($images_right as $key =>$image):?>
                        <?php //if($i >= $i_max)break; ?>
                        <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                            <div class="banner-grid">
                                <div class="banner-thumb">
                                    <video src="<?php echo esc_url(wmvc_show_data('src',$image));?>" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image"></video>
                                    <a href="<?php echo esc_url(wmvc_show_data('src',$image));?>" data-key="<?php echo esc_attr($key);?>" class="banner-thumb-link wdk-listing-image-card"></a>
                                </div>
                            </div>
                        <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                            <div class="banner-grid">
                                <div class="banner-thumb">
                                    <img src="<?php echo esc_url(wmvc_show_data('src',$image));?>" class="wdk-listing-image" alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>">
                                    <a href="<?php echo esc_url(wmvc_show_data('src',$image));?>" data-key="<?php echo esc_attr($key);?>" class="banner-thumb-link wdk-listing-image-card"></a>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?> 
                </div><!--banner-thumbs end-->
            </div>
        <?php endif;?>
    </div>
    <?php endif;?>
    <?php if($settings['gallery_enable'] != 'yes'):?>
    <script>
        jQuery(document).ready(function($){
            $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_listing_slider_ini').slick({
                <?php if(1 < wmvc_count($images)):?>
                dots: true,
                arrows: true,
                <?php else:?>
                dots: false,
                arrows: false,
                <?php endif;?>
                slidesToShow: 1,
                slidesToScroll: 1,
                <?php if(!empty(wmvc_show_data('layout_carousel_is_infinite', $settings))):?>
                infinite: <?php echo wmvc_show_data('layout_carousel_is_infinite', $settings, 'true');?>,
                <?php endif;?>
                <?php if(!empty(wmvc_show_data('layout_carousel_is_autoplay', $settings))):?>
                autoplay: <?php echo wmvc_show_data('layout_carousel_is_autoplay', $settings, 'false');?>,
                <?php endif;?>
                autoplaySpeed: '<?php echo esc_html($settings['layout_carousel_speed'], '100');?>',
                <?php if(in_array($settings['layout_carousel_animation_style'], ['fade','fade_in'])):?>
                fade: true,
                <?php endif;?>
                nextArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-listing-sliders-more-grid-images_arrows .wdk-slider-next'),
                prevArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-listing-sliders-more-grid-images_arrows .wdk-slider-prev'),
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

            $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-cls-banner-thumbs .banner-thumb .banner-thumb-link').off().on('click', function(e){
                e.preventDefault();
                
                $('#wdk_el_<?php echo esc_html($id_element);?> .banner-thumb').removeClass('wdk-active-nav');
                $(this).closest('.banner-thumb').addClass('wdk-active-nav');
                var imgIndex = $(this).attr('data-key');
                $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_listing_slider_ini').slick('slickGoTo', imgIndex);
                return false;
            })

            // After cahnge
            $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_listing_slider_ini').on('afterChange', function(event, slick, currentSlide){
                console.log(currentSlide)
                $('#wdk_el_<?php echo esc_html($id_element);?> .banner-thumb').removeClass('wdk-active-nav');
                $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-cls-banner-thumbs .banner-thumb .banner-thumb-link[data-key="'+currentSlide+'"]').closest('.banner-thumb').addClass('wdk-active-nav');
            });
        })
    </script>
    <?php endif;?>
</div>

