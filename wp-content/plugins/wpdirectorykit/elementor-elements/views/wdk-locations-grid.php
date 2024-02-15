<?php
/**
 * The template for Element Locations Grid.
 * This is the template that elementor element locations, grid
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
    <div class="wdk-locations-grid">
        <div class="wdk-row">
            <?php if(count($results) > 0):?>
                <?php foreach ($results as $key => $value):?>
                <div class="wdk-col">
                    <div class="wdk-locations-card">
                        <div class="wdk-thumbnail">
                            <?php if(wmvc_show_data('layout_image_type', $settings) == 'icon'):?>
                                <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'icon_id', 'icon_path'));?>" alt="<?php echo wmvc_show_data('location_title', $value);?>" class="wdk-icon">
                            <?php else:?>
                                <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'image_id', 'image_path'));?>" alt="<?php echo wmvc_show_data('location_title', $value);?>" class="wdk-image">
                            <?php endif;?>
                        </div>
                        <h3 class="wdk-title"><?php echo wmvc_show_data('location_title', $value);?></h3>
                        <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_location='.wmvc_show_data('idlocation', $value)));?>#results"  class="wdk-link"></a>
                    </div>
                </div>
                <?php endforeach;?>
            <?php else:?>
                <div class="wdk-col wdk-col-full wdk-col-full-always">
                    <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Locations not found', 'wpdirectorykit');?></p>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>

