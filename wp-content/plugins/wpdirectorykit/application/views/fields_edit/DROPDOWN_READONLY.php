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
    
if(!empty($field->values) && is_array($field->values)){
    $values = $field->values;
} else {
    $values = array();
    if(!empty($field->values_list)){
        $values = explode(',', $field->values_list);
        $values = array(''=> __('Not Selected', 'wpdirectorykit')) + array_combine($values, $values);
    }
}

$button_suffix = '';

//var_dump($values);
//var_dump(wmvc_show_data($field_id, $db_data, ''));

?>

<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <span class="regular-span">
            <?php 
                if(wmvc_show_data($field_id, $db_data, false)) {
                    ?>
                       <?php echo esc_html( $values[wmvc_show_data($field_id, $db_data, false)]); ?>
                    <?php
                } else {
                    ?>
                        <div class="wdk_alert wdk_alert-info" role="alert"><?php echo esc_html__('Not defined','wpdirectorykit'); ?></div>
                    <?php
                }
            ?>
        </span>
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
        <?php endif;?>
    </div>
</div>
