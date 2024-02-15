<?php
/**
 * The template for Element Categories Grid.
 * This is the template that elementor element grid, categories results
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
    <div class="wdk-categories-grid">
        <?php if(wmvc_show_data('layout_carousel_enable', $settings) == 'yes'):?>
            <div class="wdk-carousel_ini">
        <?php else:?>
            <div class="wdk-row">
        <?php endif;?>
        <?php if(count($results) > 0):?>
                <?php foreach ($results as $key => $value):?>
                    <?php if(wmvc_show_data('layout_carousel_enable', $settings) != 'yes'):?>
                        <div class="wdk-col">
                    <?php endif;?>
                        <div class="wdk-categories-card">
                            <div class="wdk-thumbnail">
                                <?php if(wmvc_show_data('layout_image_type', $settings) == 'icon'):?>
                                    <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'icon_id', 'icon_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-icon">
                                <?php else:?>
                                    <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'image_id','image_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-image">
                                <?php endif;?>
                            </div>
                            <h3 class="wdk-title"><?php echo wmvc_show_data('category_title', $value);?></h3>
                            <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results"  class="wdk-link"></a>
                        </div>
                    <?php if(wmvc_show_data('layout_carousel_enable', $settings) != 'yes'):?>
                        </div>
                    <?php endif;?>
                <?php endforeach;?>
            <?php else:?>
                <div class="wdk-col wdk-col-full wdk-col-full-always">
                    <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Categories not found', 'wpdirectorykit');?></p>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
<?php if(wmvc_show_data('layout_carousel_enable', $settings) == 'yes'):?>
<script>
 jQuery(document).ready(function($){
            $('#wdk_el_<?php echo esc_html($id_element);?> .wdk-carousel_ini').slick({
                <?php if(!empty($results) && wmvc_show_data('layout_carousel_columns', $settings,1) < wmvc_count($results)):?>
                dots: true,
                <?php else:?>
                dots: false,
                <?php endif;?>
                arrows: false,
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
                <?php if(wmvc_show_data('layout_carousel_columns', $settings, 1) == 1 && in_array($settings['layout_carousel_animation_style'], ['fade','fade_in_in'])):?>
                fade: true,
                <?php endif;?>
                cssEase: '<?php echo esc_html($settings['layout_carousel_cssease'], 'linear');?>',
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
<?php endif;?>