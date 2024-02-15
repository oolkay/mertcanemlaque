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

if(!function_exists('run_wdk_bookings')) return false;

$field_key = 'field_'.wmvc_show_data('idfield',$field_data);
$field_attr_id = 'wdk_field_'.wmvc_show_data('idfield', $field_data);
$placeholder_to = esc_html__('End Date','wpdirectorykit');
$placeholder_from = esc_html__('Start Date','wpdirectorykit');

$field_value_from = '';
$field_value_to = '';

if(isset($predefinedfields_query) && !empty($predefinedfields_query[$field_key.'_from'])) {
    $field_value_from = sanitize_text_field($predefinedfields_query[$field_key.'_from']);
}

if(isset($predefinedfields_query) && !empty($predefinedfields_query[$field_key.'_from'])) {
    $field_value_to = sanitize_text_field($predefinedfields_query[$field_key.'_from']);
}

if(isset($_GET[$field_key.'_from'])) {
    $field_value_from = sanitize_text_field($_GET[$field_key.'_from']);
}

if(isset($_GET[$field_key.'_to'])) {
    $field_value_to = sanitize_text_field($_GET[$field_key.'_to']);
}

wdk_search_fields_toggle();

wp_enqueue_script( 'daterangepicker-moment' );
wp_enqueue_script( 'daterangepicker' );
wp_enqueue_style('daterangepicker');


$date_class = 'wdk-fielddate_range' ;

if(get_option('wdk_bookings_is_hours_enabled')) {
    $date_class = 'wdk-fielddatetime_range'; 
}

?>

<div class="wdk-field wdk-col min_max_wdk-field <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?>  wdk_field_id_booking_dates">
    <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
    <div class="wdk-field-group">
        <div class="wdk-row min_max_row">
            <div class="wdk-col wdk-col-6" style="width: 50%;">
                <input class="wdk-control <?php echo esc_attr($date_class );?> date_from center" <?php if(get_option('wdk_bookings_calendar_single')):?> data-wdksingle = 'true' <?php endif; ?> name="<?php echo esc_attr($field_key); ?>_from" type="text" id="<?php echo esc_attr($field_attr_id.'_from'); ?>" value="<?php echo esc_attr($field_value_from); ?>" placeholder="<?php echo esc_attr($placeholder_from);?>">
            </div>
            <div class="wdk-col wdk-col-6" style="width: 50%;">
                <input class="wdk-control <?php echo esc_attr($date_class );?> date_to center" <?php if(get_option('wdk_bookings_calendar_single')):?> data-wdksingle = 'true' <?php endif; ?> name="<?php echo esc_attr($field_key); ?>_to" type="text" id="<?php echo esc_attr($field_attr_id.'_to'); ?>" value="<?php echo esc_attr($field_value_to); ?>" placeholder="<?php echo esc_attr($placeholder_to);?>">
            </div>
        </div>
    </div>
</div>