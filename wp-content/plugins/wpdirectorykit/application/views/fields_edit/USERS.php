<?php
/**
 * The template for Edit field USERS.
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

$dbusers =  get_users( array( 'search' => '',
                                    'orderby' => 'display_name', 'order' => 'ASC'));

$users = array();
foreach($dbusers as $dbuser) {
    $users[wmvc_show_data('ID', $dbuser)] = '#'.wmvc_show_data('ID', $dbuser).', '.wmvc_show_data('display_name', $dbuser);
}

?>

<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <?php echo wdk_treefield_option($field_id, 'user_m', wmvc_show_data($field_id, $db_data, ''), 'display_name', '', __('Not Selected', 'wpdirectorykit'));?>
        
        <?php
          //  echo wmvc_select_option($field_id, $users, wmvc_show_data($field_id, $db_data, ''), NULL, __('Not Selected', 'wpdirectorykit'));
        ?>
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
        <?php endif;?>
    </div>
</div>