<?php
/**
 * The template for Search field INPUTBOX.
 *
 * This is the template that field layout for search form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php
$field_key = 'field_'.wmvc_show_data('idfield',$field_data);
$field_attr_id = 'wdk_field_'.wmvc_show_data('idfield', $field_data);

$placeholder = wmvc_show_data('field_label', $field_data);
if(!empty(wmvc_show_data('placeholder', $field_data,'')))
    $placeholder = wmvc_show_data('placeholder', $field_data);

$placeholder = esc_html__($placeholder,'wpdirectorykit');

$field_value = '';

$query_type = wmvc_show_data('query_type', $field_data, '');

if(!empty($query_type) && $query_type !='min_max')
    $field_key .='_'.$query_type;

if(isset($predefinedfields_query) && !empty($predefinedfields_query[$field_key])) {
    $field_value = sanitize_text_field($predefinedfields_query[$field_key]);
}

if(isset($_GET[$field_key])) {
    $field_value = sanitize_text_field($_GET[$field_key]);
} elseif($field_key == 'field_search') {
    if(function_exists('run_wdk_geo') && (get_option('wdk_geo_autodetect_by_ip_enable') && !get_option('wdk_geo_autodetect_by_google_js_enable'))) {
        $user_locations = wdk_geo_get_locations();
        if(!empty($user_locations) && !wdk_geo_get_location_id()) {
            $field_value = current($user_locations);
        }
    }
}

if($query_type =='min')
    $placeholder = esc_html__('Min','wpdirectorykit').' '.$placeholder;

if($query_type =='max')
    $placeholder = esc_html__('Max','wpdirectorykit').' '.$placeholder;

wdk_search_fields_toggle();
?>

<div class="wdk-field wdk-col wdk_search_<?php echo esc_attr($field_key);?> <?php if($query_type == 'min_max'):?>min_max_wdk-field<?php endif;?>  <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?> 
    wdk_field_id_<?php echo wmvc_show_data('idfield',$field_data);?>">
    <label class="wdk-field-label"><?php echo esc_html__(wmvc_show_data('field_label', $field_data),'wpdirectorykit'); ?></label>
    <div class="wdk-field-group">
        <?php if($query_type == 'min_max'):?>
            <div class="wdk-row min_max_row">
                <div class="wdk-col wdk-col-6">
                    <input class="wdk-control" name="<?php echo esc_attr($field_key.'_min'); ?>" type="text" id="<?php echo esc_attr($field_attr_id.'_min'); ?>" value="<?php echo (isset($_GET[$field_key.'_min'])) ? esc_attr($_GET[$field_key.'_min']) : ''; ?>" placeholder="<?php echo esc_html__('Min','wpdirectorykit').' '.esc_attr(trim($placeholder));?>">
                </div>
                <div class="wdk-col wdk-col-6">
                    <input class="wdk-control" name="<?php echo esc_attr($field_key.'_max'); ?>" type="text" id="<?php echo esc_attr($field_attr_id.'_max'); ?>" value="<?php echo (isset($_GET[$field_key.'_max'])) ? esc_attr($_GET[$field_key.'_max']) : ''; ?>" placeholder="<?php echo esc_html__('Max','wpdirectorykit').' '.esc_attr(trim($placeholder));?>">
                </div>
            </div>
        <?php else:?>
            <input class="wdk-control" name="<?php echo esc_attr($field_key); ?>" type="text" id="<?php echo esc_attr($field_attr_id); ?>" value="<?php echo esc_attr($field_value); ?>" placeholder="<?php echo esc_attr(trim($placeholder));?>">
        <?php endif;?>
    </div>
</div>