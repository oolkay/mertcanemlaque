<?php
/**
 * The template for Edit field STARS READONLY.
 *
 * This is the template that field layout for edit form, readonly
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
if(!isset($field->prefix))$field->prefix = '';
if(!isset($field->suffix))$field->suffix = '';
if(!isset($field->class))$field->class = '';

$field_label = $field->field_label;

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';

?>

<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <?php if ($i <= wmvc_show_data($field_id, $db_data)): ?>
                <span class="dashicons dashicons-star-filled"></span>
            <?php elseif( abs(wmvc_show_data($field_id, $db_data, 0) - $i) < 1): ?>
                <span class="dashicons dashicons-star-half"></span>
            <?php else: ?>
                <span class="dashicons dashicons-star-empty"></span>
            <?php endif; ?>
        <?php endfor; ?>
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
    </div>
</div>