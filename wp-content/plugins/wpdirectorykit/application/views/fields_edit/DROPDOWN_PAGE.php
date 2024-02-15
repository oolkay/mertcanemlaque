<?php
/**
 * The template for Edit field DROPDOWN PAGE.
 *
 * This is the template that field layout for edit form, pages list
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php

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

?>

<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <?php echo wmvc_select_option($field_id, $values, wmvc_show_data($field_id, $db_data, '')); ?>
        <span class="suffix"><?php
            echo esc_html($field->prefix);
                if(!empty($field->prefix) && !empty($field->suffix)) echo ' / ';
            echo esc_html($field->suffix);
        ?>
        <?php if(!empty(wmvc_show_data($field_id, $db_data, '')) && get_post_status(wmvc_show_data($field_id, $db_data, '')) == 'publish'):?>
            <a class="button button-primary" target="_blank" href="<?php echo get_permalink(wmvc_show_data($field_id, $db_data, ''));?>" style="margin-top: -5px;">
                <?php echo esc_html__('View Page','wpdirectorykit');?>
            </a>
            <a class="button button" target="_blank" href="<?php echo admin_url('post.php?post='.wmvc_show_data($field_id, $db_data, '').'&action=edit');?>" style="margin-top: -5px;">
                <span class="dashicons dashicons-edit"></span>
            </a>
        <?php else:?>
            <?php if(isset($_GET['page']) && $_GET['page'] == 'wpdirectorykit'):?>
            <a class="button button-primary" target="_blank" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk-membership&function=import_demo"); ?>" style="margin-top: -5px;">
                <?php echo esc_html__('Create Demo Page','wpdirectorykit');?>
            </a>
            <?php endif;?>
        <?php endif;?></span>
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
        <?php endif;?>
    </div>
</div>