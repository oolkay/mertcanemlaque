<?php
/**
 * The template for Search field TEXTAREA.
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
$placeholder = wmvc_show_data('placeholder', $field_data, wmvc_show_data('field_label', $field_data));
$field_value = '';
$placeholder = esc_html__($placeholder,'wpdirectorykit');

$query_type = wmvc_show_data('query_type', $field_data, '');

if(!empty($query_type) && $query_type !='min_max')
    $field_key .='_'.$query_type;

if(isset($_GET[$field_key])) {
    $field_value = sanitize_text_field($_GET[$field_key]);
}

if($query_type =='min')
    $placeholder = esc_html__('Min','wpdirectorykit').' '.$placeholder;

if($query_type =='max')
    $placeholder = esc_html__('Max','wpdirectorykit').' '.$placeholder;
wdk_search_fields_toggle();
?>

<div class="wdk-field wdk-col wdk_search_<?php echo esc_attr($field_key);?> <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?> wdk_field_id_<?php echo wmvc_show_data('idfield',$field_data);?>">
    <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
    <div class="wdk-field-group">
        <input class="wdk-control" name="<?php echo esc_attr($field_key); ?>" type="text" id="<?php echo esc_attr($field_attr_id); ?>" value="<?php echo esc_attr($field_value); ?>" placeholder="<?php echo esc_attr(trim($placeholder));?>">
    </div>
</div>