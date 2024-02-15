<?php
/**
 * The template for Edit field DATE READONLY.
 *
 * This is the template that field layout for edit form readonly
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

?>
<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <span class="regular-span">
            <?php 
                if(wmvc_show_data($field_id, $db_data, false)) {
                    echo esc_html(wdk_get_date(wmvc_show_data($field_id, $db_data, false)));
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
