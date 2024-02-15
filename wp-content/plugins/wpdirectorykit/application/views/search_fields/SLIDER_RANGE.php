<?php
/**
 * The template for Search field BOOKINGS_DATE.
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
$field_value = '';
$placeholder = esc_html__($placeholder,'wpdirectorykit');
$query_type = wmvc_show_data('query_type', $field_data, '');

if(isset($predefinedfields_query) && !empty($predefinedfields_query[$field_key])) {
    $field_value = sanitize_text_field($predefinedfields_query[$field_key]);
}

if(isset($_GET[$field_key])) {
    $field_value = sanitize_text_field($_GET[$field_key]);
}

$suffix = '';
$prefix = '';
$prefix = apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix',$field_data), wmvc_show_data('idfield', $field_data));
$suffix = apply_filters( 'wpdirectorykit/listing/field/suffix',wmvc_show_data('suffix',$field_data), wmvc_show_data('idfield', $field_data));

if(!empty($prefix)) {
    $prefix = ' ('.$prefix.')';
}

if(!empty($suffix)) {
    $suffix = ' ('.$suffix.')';
}

wdk_search_fields_toggle();

wp_enqueue_script( 'ion.range-slider' );
wp_enqueue_style('ion.range-slider');
wp_enqueue_style('wdk-slider-range');
wp_enqueue_script('wdk-slider-range');

$min_value = 0;
$max_value = 250000;

if(wmvc_show_data('value_min', $field_data, false)) {
    $min_value = intval(wmvc_show_data('value_min', $field_data, false));
}

if(wmvc_show_data('value_max', $field_data, false)) {
    $max_value = intval(wmvc_show_data('value_max', $field_data, false));
} else {
    /* if not defined max value, try get from db */
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->load_helper('listing');
    $Winter_MVC_WDK->model('listing_m');
    
    $Winter_MVC_WDK->db->order_by('field_'.wmvc_show_data('idfield',$field_data).'_'.wdk_field_option(wmvc_show_data('idfield',$field_data), 'field_type').' DESC');
    $listing = $Winter_MVC_WDK->listing_m->get(NULL, TRUE);
    
    if($listing) {
        $max_value = wdk_field_value(wmvc_show_data('idfield',$field_data), $listing);
    }
}

?>

<div class="wdk-field wdk-col wdk-slider-range-col wdk_search_<?php echo esc_attr($field_key);?> <?php if($query_type == 'min_max'):?>min_max_wdk-field<?php endif;?>  <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?> 
    wdk_field_id_<?php echo wmvc_show_data('idfield',$field_data);?>">
    <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
    <div class="wdk-field-group">
        <div class="wdk-slider-range-field">
                <div class="hidden config-range"
                data-min="<?php echo $min_value;?>"
                data-max="<?php echo $max_value;?>"
                data-sufix="<?php echo $suffix;?>"
                data-prefix="<?php echo $prefix;?>"
                data-infinity="false"
                data-predifinedMin="<?php echo (isset($_GET[$field_key.'_min'])) ? esc_attr($_GET[$field_key.'_min']) : ''; ?>"
                data-predifinedMax="<?php echo (isset($_GET[$field_key.'_max'])) ? esc_attr($_GET[$field_key.'_max']) : ''; ?>"
            >
            </div>
            <input type="text" class="wdk-slider-range-input" name="skip_field_<?php echo esc_attr($field_key); ?>" value="" />
            <input id="<?php echo esc_attr($field_attr_id.'_min'); ?>" name="<?php echo esc_attr($field_key.'_min'); ?>" type="text" class="value-min wdk-hidden" value="<?php echo (isset($_GET[$field_key.'_min'])) ? esc_attr($_GET[$field_key.'_min']) : ''; ?>" />
            <input id="<?php echo esc_attr($field_attr_id.'_max'); ?>" name="<?php echo esc_attr($field_key.'_max'); ?>" type="text" class="value-max wdk-hidden" value="<?php echo (isset($_GET[$field_key.'_max'])) ? esc_attr($_GET[$field_key.'_max']) : ''; ?>" />
        </div>
    </div>
</div>
