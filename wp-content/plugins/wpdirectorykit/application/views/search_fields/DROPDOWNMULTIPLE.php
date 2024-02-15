<?php
/**
 * The template for Search field DROPDOWN.
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
    $field_value = ($_GET[$field_key]);
}

$values = array();
if(!empty(wmvc_show_data('values_list', $field_data))){
    $values = explode(',', wmvc_show_data('values_list', $field_data));
    $values = array(''=> $placeholder) + array_combine($values, $values);
}

if(isset($values[''])) {
    unset($values['']);
}

$empty_value = __('Select', 'wpdirectorykit').' '.$placeholder;

wdk_search_fields_toggle();
?>

<div class="wdk-field wdk-col wdk_search_<?php echo esc_attr($field_key);?> <?php if($query_type == 'min_max'):?>min_max_wdk-field<?php endif;?>  <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?> 
    wdk_field_id_<?php echo wmvc_show_data('idfield',$field_data);?>">
    <label class="wdk-field-label"><?php echo esc_html__(wmvc_show_data('field_label', $field_data),'wpdirectorykit'); ?></label>
    <div class="wdk-field-group">
            <?php echo wdk_select_option_multiple($field_key.'[]', $values, $field_value, "class='select_multi' data-placeholder='".esc_attr($empty_value)."'"); ?>
    </div>
</div>

<?php
wp_enqueue_script( 'jquery-ui-core', false, array('jquery') );
wp_enqueue_script( 'jquery-ui-sortable', false, array('jquery') );
wp_enqueue_script( 'jquery-ui-selectmenu', false, array('jquery') );
wp_enqueue_style( 'jquery-ui');
wp_enqueue_style( 'jquery-ui-selectmenu');
?>