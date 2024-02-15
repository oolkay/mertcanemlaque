<?php
/**
 * The template for Edit field MAP.
 *
 * This is the template that field layout for edit form, gps
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php

//wmvc_dump($field);

/* 
*
* Should be exists in name _lat
* Should be exists same field for lng, example
* gps_lat and gps_lng
*
*/

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

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';

if(strpos($field_id, '_lat') === FALSE) return false;

$field_id = str_replace('_lat', '', $field_id);

?>

<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?>" id="inputbox_map_<?php echo esc_attr($field_id); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <div class="inputbox_map" id="<?php echo esc_attr($field_id); ?>_map"></div>

        <input class="regular-text hidden" name="<?php echo esc_attr($field_id).'_lat'; ?>" type="text" id="<?php echo esc_attr($field_id).'_lat'; ?>" value="<?php echo esc_attr(wmvc_show_data($field_id.'_lat', $db_data, '')); ?>">
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
    </div>
    <?php
        wp_enqueue_style('leaflet');
        wp_enqueue_script('leaflet');
        
        wp_enqueue_style('wdk-notify');
        wp_enqueue_script('wdk-notify');
    ?>
    <script>
        jQuery(document).ready(function($) {    
            if(!$('#<?php echo esc_attr($field_id); ?>_lat').length || !$('#<?php echo esc_attr($field_id); ?>_lng').length){$('#inputbox_map_<?php echo esc_attr($field_id); ?>').hide()}
            let map,marker; 
            map = L.map('<?php echo esc_attr($field_id); ?>_map', {
                center: [<?php echo wmvc_show_data($field_id.'_lat', $db_data, get_option('wdk_default_lat', 51.505)); ?>, <?php echo wmvc_show_data($field_id.'_lng', $db_data, get_option('wdk_default_lng', -0.09)); ?>],
                zoom: 4,
            });     
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            marker = L.marker(
                [<?php echo wmvc_show_data($field_id.'_lat', $db_data, get_option('wdk_default_lat', 51.505)); ?>, <?php echo wmvc_show_data($field_id.'_lng', $db_data, get_option('wdk_default_lng', -0.09)); ?>],
                {draggable: true}
            ).addTo(map);

            marker.on('dragend', (event) => {
                let marker = event.target;
                let {lat,lng} = marker.getLatLng();
                $('#<?php echo esc_attr($field_id); ?>_lat').val(lat);
                $('#<?php echo esc_attr($field_id); ?>_lng').val(lng);
                //retrieved the position
            });
        });  
    </script>
</div>