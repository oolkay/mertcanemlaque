<?php
/**
 * The template for Edit field DATE.
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
if(!isset($field->columns_number))$field->columns_number = '';
if(!isset($field->class))$field->class = '';

$field_label = $field->field_label;

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';

if(isset($field->rules) && strpos($field->rules, 'required') !== FALSE)
    $required = '*';

?>
<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <input class="regular-text db-date" <?php if(false):?>date-format="<?php echo esc_attr(wdk_jsdateformat(get_option('date_format')));?>"<?php endif;?> name="<?php echo esc_attr($field_id); ?>" type="hidden" id="<?php echo esc_attr($field_id); ?>" value="<?php echo esc_attr(wmvc_show_data($field_id, $db_data, '')); ?>">
        <input class="regular-text wdk-fielddate" <?php if(false):?>date-format="<?php echo esc_attr(wdk_jsdateformat(get_option('date_format')));?>"<?php endif;?> name="<?php echo esc_attr($field_id); ?>_mask" type="text" id="<?php echo esc_attr($field_id); ?>_mask" value="<?php echo (wmvc_show_data($field_id, $db_data, false)) ? esc_attr(wdk_get_date(wmvc_show_data($field_id, $db_data),false)) : ''; ?>">
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
        <?php endif;?>
    </div>
</div>

<?php
 wp_enqueue_script( 'jquery-ui-datepicker' );
 wp_enqueue_style('jquery-ui');
?>
