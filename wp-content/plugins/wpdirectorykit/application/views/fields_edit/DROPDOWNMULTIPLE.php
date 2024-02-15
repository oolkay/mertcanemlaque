<?php
/**
 * The template for Edit field DROPDOWN.
 *
 * This is the template that field layout for edit form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php

//wmvc_dump($field);

if(isset($field->field))
{
    $field_id = $field->field;
}
else
{
    $field_id = 'field_'.$field->idfield;
}

if(!isset($field->hint))$field->hint = '';

$field_label = $field->field_label;

if(!isset($field->hint))$field->hint = '';
if(!isset($field->class))$field->class = '';
if(!isset($field->columns_number))$field->columns_number = '';
if(!isset($field->prefix))$field->prefix = '';
if(!isset($field->suffix))$field->suffix = '';

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';
    
    
if(isset($field->rules) && strpos($field->rules, 'required') !== FALSE)
    $required = '*';

if(!empty($field->values) && is_array($field->values)){
    $values = $field->values;
} else {
    $values = array();
    if(!empty($field->values_list)){
        $values = explode(',', $field->values_list);
        $values = array_combine($values, $values);
        if(isset($values[''])) {
            unset($values['']);
        }
    }
}
$empty_value = __('Selecte', 'wpdirectorykit').' '.$field_label;
$button_suffix = '';

//var_dump($values);
wp_enqueue_script('select2');
wp_enqueue_script('wdk-select2');
wp_enqueue_style('select2');


$post_values = wmvc_show_data($field_id, $db_data, '');
$post_values = explode(',', $post_values);
?>

<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <?php echo wdk_select_option_multiple($field_id.'_select2', $values, $post_values, "id='".$field_id."_select2' class='select_multi' data-maxselectlimit='20' data-placeholder='".esc_attr($empty_value)."'"); ?>
        <span class="suffix"><?php
            echo esc_html($field->prefix);
                if(!empty($field->prefix) && !empty($field->suffix)) echo ' / ';
            echo esc_html($field->suffix);
        ?></span>
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
        <?php endif;?>
        <input type="hidden" name="<?php echo esc_attr($field_id);?>" id="<?php echo esc_attr($field_id);?>" value="<?php echo esc_attr(wmvc_show_data($field_id, $db_data, '')); ?>"/>
    </div>
    <script>

        jQuery(document).ready(function ($) {
            $('#<?php echo esc_js($field_id);?>_select2').on('change', function(){
                $("#<?php echo esc_js($field_id);?>").val(jQuery(this).val().join(','));
            })
        });

    </script>
</div>