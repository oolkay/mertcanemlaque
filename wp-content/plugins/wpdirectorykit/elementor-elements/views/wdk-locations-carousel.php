<?php
/**
 * The template for Element Locations Carousel.
 * This is the template that elementor element carousel, slider, locations
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
    <div class="wdk-locations-carousel">
        <div class="wdk-location-slider-box <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo esc_attr(join(' ', [$settings['t_styles_dots_position_style'], $settings['t_styles_arrows_position_style'],$settings['t_styles_arrows_position'],$settings['t_styles_arrows_position_style']]));?>">
            <div class="wdk-locations-carousel_ini <?php echo esc_html($settings['t_styles_img_des_type']);?> ">
                <?php foreach ($results as $key => $item):?>
                    <div class="wdk-slider-item">
                        <img src="<?php echo esc_url(wdk_image_src($item, 'full', NULL,'image_id', 'image_path'));?>" class="wdk-slider-item_thumbnail" alt="<?php echo esc_html(wmvc_show_data('location_title', $item));?>">
                        <div class="wdk-locations-carousel_mask"></div>
                        <?php if(!empty(wmvc_show_data('location_title', $item))):?>
                        <div class="wdk-slider-item_box_line"> <div class="wdk-slider-item_box_title"> <?php echo esc_html(wmvc_show_data('location_title', $item));?> </div></div>
                        <?php endif;?>
                        <div class="wdk-slider-item_box_line"> <div class="wdk-slider-item_box_content"> <?php echo esc_html(wmvc_show_data('listings_counter', $item));?> <?php echo esc_html__('Listings','wpdirectorykit');?> </div></div>
                        <div class="wdk-slider-item_box_line"> <a class="wdk-slider-item_box_link" href="<?php echo esc_url(wdk_url_suffix($results_page,'search_location='.wmvc_show_data('idlocation', $item)));?>#results"> <?php echo esc_html($settings['t_content_basic_link_text']);?> </a></div>
                        <?php if(!wmvc_show_data('complete_link_enable', $item) == 'yes'):?>
                            <a class="wdk-slider-complete_link" href="<?php echo esc_url(wdk_url_suffix($results_page,'search_location='.wmvc_show_data('idlocation', $item)));?>#results"></a>
                        <?php endif;?>
                    </div>
                <?php endforeach;?>
            </div>
            <?php if(!empty($results) && wmvc_show_data('layout_carousel_columns', $settings,1) < wmvc_count($results)):?>
                <div class="wdk-locations-carousel_arrows">
                    <a class="wdk-slider-prev wdk-locations-carousel_arrow">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_left'], [ 'aria-hidden' => 'true' ] ); ?>
                    </a>
                    <a class="wdk-slider-next wdk-locations-carousel_arrow">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_right'], [ 'aria-hidden' => 'true' ] ); ?>
                    </a>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
<script>
 jQuery(document).ready(function($){
            $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-locations-carousel_ini').slick({
                <?php if(!empty($results) && wmvc_show_data('layout_carousel_columns', $settings,1) < wmvc_count($results)):?>
                dots: true,
                arrows: true,
                <?php else:?>
                dots: false,
                arrows: false,
                <?php endif;?>
                <?php if($settings['layout_carousel_center']=='yes'):?>
                    centerMode: true,
                <?php endif;?>
                <?php if($settings['layout_carousel_variableWidth']=='yes'):?>
                    variableWidth: true,
                <?php endif;?>
                speed: '<?php echo esc_html($settings['layout_carousel_speed'], '100');?>',
                slidesToShow: <?php echo wmvc_show_data('layout_carousel_columns', $settings, 1);?>,
                slidesToScroll: <?php echo wmvc_show_data('layout_carousel_columns', $settings,1);?>,
                <?php if(!empty(wmvc_show_data('layout_carousel_is_infinite', $settings))):?>
                infinite: <?php echo wmvc_show_data('layout_carousel_is_infinite', $settings, 'true');?>,
                <?php endif;?>
                <?php if(!empty(wmvc_show_data('layout_carousel_is_autoplay', $settings))):?>
                autoplay: <?php echo wmvc_show_data('layout_carousel_is_autoplay', $settings, 'false');?>,
                autoplaySpeed: <?php echo wmvc_show_data('layout_carousel_autoplaySpeed', $settings, '1500');?>,
                <?php endif;?>
                <?php if(wmvc_show_data('layout_carousel_columns', $settings, 1) == 1 &&  in_array($settings['layout_carousel_animation_style'], ['fade','fade_in_in'])):?>
                fade: true,
                <?php endif;?>
                cssEase: '<?php echo esc_html($settings['layout_carousel_cssease'], 'linear');?>',
                nextArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-locations-carousel_arrows .wdk-slider-next'),
                prevArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-locations-carousel_arrows .wdk-slider-prev'),
                customPaging: function(slider, i) {
                    // this example would render "tabs" with titles
                    return '<span class="wdk_lr_dot"><?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_dots_position_style'], [ 'aria-hidden' => 'true' ] ); ?></span>';
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
        })
</script>