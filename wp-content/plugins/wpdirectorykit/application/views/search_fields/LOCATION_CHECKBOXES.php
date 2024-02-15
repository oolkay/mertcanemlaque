<?php
/**
 * The template for Search field LOCATION.
 *
 * This is the template that field layout for search form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php

$field_key = 'search_location';
$field_attr_id = 'wdk_search_'.wmvc_show_data('idfield', $field_data);
$placeholder = wmvc_show_data('field_label', $field_data);
$field_value = '';
$filter_ids = array();
if(isset($predefinedfields_query) && !empty($predefinedfields_query[$field_key])) {
    $field_value = intval($predefinedfields_query[$field_key]);
}    

if(isset($predefinedfields_query) && !empty($predefinedfields_query['custom_location_root'])) {
    $custom_field_value = intval($predefinedfields_query['custom_location_root']);
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->load_helper('listing');
    $Winter_MVC_WDK->model('location_m');
    $location = $Winter_MVC_WDK->location_m->get($custom_field_value, TRUE); 

    /* if root search childs */
    if(empty(wmvc_show_data('parent_id', $location, false, TRUE, TRUE))) {
        $filter_ids = wdk_location_get_all_childs($custom_field_value); 
        if(!empty($filter_ids)) {
            $filter_ids[] = $custom_field_value;
            if(in_array($field_value, $filter_ids) === FALSE) {
                $field_value = $custom_field_value;
            }
        }
    }
}    

if(isset($_GET[$field_key])) {
    $field_value = ($_GET[$field_key]);
} else {
    if(function_exists('run_wdk_geo') && (get_option('wdk_geo_autodetect_by_ip_enable') && !get_option('wdk_geo_autodetect_by_google_js_enable'))) {
        $field_value  = wdk_geo_get_location_id();
    }
}

$hide_fields = '';
if(wmvc_show_data('search_type_tree_hide', $field_data)) {
    $hide_fields = wmvc_show_data('search_type_tree_hide', $field_data);
}

wdk_search_fields_toggle();
?>

<div class="wdk-field wdk-col <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?>">
    <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
    <div class="wdk-field-group">
        <?php
            wp_enqueue_style( 'wdk-treefield-checkboxes');
            wp_enqueue_script( 'wdk-treefield-checkboxes');
        ?>
        <?php echo wdk_treefield_option_checkboxes ('search_location', 'location_m', $field_value, 'location_title', '', __('All Locations', 'wpdirectorykit'), $filter_ids, FALSE, '', $hide_fields);?>
    </div>
</div>