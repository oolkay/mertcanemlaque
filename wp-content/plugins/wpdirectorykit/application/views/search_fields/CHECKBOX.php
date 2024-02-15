<?php
/**
 * The template for Search field CHECKBOX.
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
$field_value = false;
$placeholder = esc_html__($placeholder,'wpdirectorykit');

if(isset($predefinedfields_query) && !empty($predefinedfields_query[$field_key])) {
    $field_value = sanitize_text_field($predefinedfields_query[$field_key]);
}

if(isset($_GET[$field_key]) && $_GET[$field_key] == 1) {
    $field_value = true;
}

wdk_search_fields_toggle();
?>
<div class="wdk-field wdk-col wdk_search_<?php echo esc_attr($field_key);?> <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?> 
    wdk_field_id_<?php echo wmvc_show_data('idfield',$field_data);?>">
    <div class="wdk-field-group">
        <label for="<?php echo esc_attr($field_attr_id); ?>" class="wdk-field-label">
            <input class="wdk-control" name="<?php echo esc_attr($field_key); ?>" <?php if($field_value):?> checked="checked" <?php endif;?> type="checkbox" id="<?php echo esc_attr($field_attr_id); ?>" value="1">
            <?php echo esc_html__(wmvc_show_data('field_label', $field_data),'wpdirectorykit'); ?>
        </label>
    </div>
</div>