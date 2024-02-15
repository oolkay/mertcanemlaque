<?php
/**
 * The template for Edit field SAVE SEARCH QUERY READONLY.
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
if(!isset($field->class))$field->class = '';

$field_label = $field->field_label;

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';
        
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->load_helper('listing');
?>

<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <div class="regular-span">
        <?php 
            $custom_parameters = array();
            $qr_string = trim(wmvc_show_data(esc_attr($field_id), $db_data, ''));
            $string_par = array();
            parse_str($qr_string, $string_par);
            $custom_parameters = $string_par;
        ?>
        <?php 
            $parsed_parameters = array();

            $function_parse_values = function($field_key, $field_value) {
                $value = '';
                $parse = function($field_key, $field_value) {
                    global $Winter_MVC_WDK;
                    $Winter_MVC_WDK->load_helper('listing');
    
                    $value = '';
                    if( $field_key == 'category') {
                        $Winter_MVC_WDK->model('category_m');
                        $tree_data = $Winter_MVC_WDK->category_m->get($field_value, TRUE);
                        $value = wmvc_show_data('category_title', $tree_data);
                    }
                    elseif( $field_key == 'location') {
                        $Winter_MVC_WDK->model('location_m');
                        $tree_data = $Winter_MVC_WDK->location_m->get($field_value, TRUE);
                        $value = wmvc_show_data('location_title', $tree_data);
                    }
                    else {
                        $value = $field_value;
                    }
                    return $value;
                };

                if(is_array($field_value)) {
                    foreach ($field_value as $v) {
                        if(!empty($value))
                            $value .= ', ';
                       
                        $value .= $parse($field_key, $v);
                    } 
                }  else {
                    $value = $parse($field_key, $field_value);
                }
                return $value;
            };

            foreach ($custom_parameters as $field_search_key => $field_value) {
                $field_search_key_parsed = explode('_', $field_search_key);
                if(!isset($field_search_key_parsed[1])) continue;

                $parameter_key = $field_search_key_parsed[1];
                $parameter_value = $function_parse_values($parameter_key, $field_value);

                
                if(!empty($parameter_value)) {
                    
                    if(substr($field_search_key, -4) == '_min') {
                        $parameter_value = esc_html__('from','wpdirectorykit').' '.$parameter_value;
                    }

                    if(substr($field_search_key, -4) == '_max') {
                        $parameter_value = esc_html__('to','wpdirectorykit').' '.$parameter_value;
                    }
                    
                    if(!empty($parsed_parameters[$parameter_key])) {
                        $parsed_parameters[$parameter_key] .= ', ';
                    } else {
                        $parsed_parameters[$parameter_key] = '';
                    }
                    $parsed_parameters[$parameter_key] .= $parameter_value;
                }
            }

            foreach ($parsed_parameters as $field_key => $field_value) {
                if(is_numeric($field_key)) {
                    echo '<b style="font-weight: 600;">'.esc_html(wdk_field_label($field_key)).'</b>: '.esc_html($field_value).' ';
                } 
                elseif($field_key == 'category') {
                    echo '<b style="font-weight: 600;">'.esc_html__('Category','wpdirectorykit').'</b>: '.esc_html($field_value).' ';
                }
                elseif($field_key == 'location') {
                    echo '<b style="font-weight: 600;">'.esc_html__('Location','wpdirectorykit').'</b>: '.esc_html($field_value).' ';
                }
                elseif($field_key == 'search') {
                    echo '<b style="font-weight: 600;">'.esc_html__('Search Smart','wpdirectorykit').'</b>: '.esc_html($field_value).' ';
                }
                else {
                    echo '<b style="font-weight: 600;">'.esc_html($field_key).'</b>: '.esc_html($field_value).' ';
                }
            }
        ?>
        </div>
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
        <?php endif;?>
    </div>
</div>