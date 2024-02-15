<?php
/**
 * The template for Element Locations List.
 * This is the template that elementor element
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
    <div class="wdk-locations-list">
        <ul class="wdk-locations">
            <?php if(count($results) > 0):?>
                <?php foreach ($results as $key => $value):?>
                <li class="wdk-item">
                    <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_location='.wmvc_show_data('idlocation', $value)));?><?php echo (wmvc_show_data('conf_query_params', $this->data['settings'], false)) ? '&'.wmvc_show_data('conf_query_params', $this->data['settings']):''; ?>#results"  class="wdk-link">
                    <?php if(wmvc_show_data('show_icon', $settings) == 'true'):?>
                            <?php if(wmvc_show_data('icon_id', $value, false)):?>
                                <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'icon_id', 'icon_path'));?>" alt="<?php echo wmvc_show_data('location_title', $value);?>" class="wdk-icon">
                            <?php endif;?>
                        <?php else:?>
                            <?php \Elementor\Icons_Manager::render_icon( $settings['item_icon_i'], [ 'aria-hidden' => 'true' ] );?>
                        <?php endif;?>
                        <span class="wdk-title"><?php echo wmvc_show_data('prefix', $settings, '').wmvc_show_data('location_title', $value).wmvc_show_data('suffix', $settings, '');?></span>
                        <span class="wdk-count">(<?php echo wmvc_show_data('listings_counter', $value);?>)</span>
                    </a>
                </li>
                <?php endforeach;?>
            <?php else:?>
                <div class="wdk-col wdk-col-full wdk-col-full-always">
                    <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Locations not found', 'wpdirectorykit');?></p>
                </div>
            <?php endif;?>
        </ul> 
    </div>
</div>

