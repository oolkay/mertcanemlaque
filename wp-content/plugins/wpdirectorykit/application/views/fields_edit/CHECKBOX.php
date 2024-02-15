<?php
/**
 * The template for Edit field CHECKBOX.
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
if(!isset($field->class))$field->class = '';
if(!isset($field->columns_number))$field->columns_number = '';

$field_label = $field->field_label;

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';

if(isset($field->rules) && strpos($field->rules, 'required') !== FALSE)
    $required = '*';

$default = '';
if(isset($field->default) && $field->default == 1)
    $default = '1';

?>
<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <fieldset>
            <legend class="screen-reader-text"><span><?php echo __('Visible on','wpdirectorykit'); ?></span></legend>
            <label for="<?php echo esc_attr($field_id); ?>">
                <input name="<?php echo esc_attr($field_id); ?>" type="checkbox" id="<?php echo esc_attr($field_id); ?>" value="1" <?php echo !empty(wmvc_show_data($field_id, $db_data, $default))?'checked':''; ?>>
            </label>
        </fieldset>
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo wp_kses_post($field->hint); ?>
        </p>
        <?php endif;?>
    </div>
</div>
