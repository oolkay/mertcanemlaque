<?php
/**
 * The template for Search field LOCATION.
 *
 * This is the template that field layout for search form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php

$field_key = 'search_location';
$field_attr_id = 'wdk_search_'.wmvc_show_data('idfield', $field_data);
$placeholder = wmvc_show_data('field_label', $field_data);
$field_value = '';
$filter_ids = array();
if(isset($predefinedfields_query) && !empty($predefinedfields_query[$field_key])) {
    $field_value = intval($predefinedfields_query[$field_key]);
}    

if(isset($_GET['location_root'])) {
    $predefinedfields_query['custom_location_root'] = sanitize_text_field($_GET['location_root']);
}

if(isset($predefinedfields_query) && !empty($predefinedfields_query['custom_location_root'])) {
    $custom_field_value = intval($predefinedfields_query['custom_location_root']);
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->load_helper('listing');
    $Winter_MVC_WDK->model('location_m');
    $location = $Winter_MVC_WDK->location_m->get($custom_field_value, TRUE); 

    /* if root search childs */
    if(isset($_GET['location_root']) || empty(wmvc_show_data('parent_id', $location, false, TRUE, TRUE))) {
        $filter_ids = wdk_location_get_all_childs($custom_field_value); 
        if(!empty($filter_ids)) {
            if(in_array($field_value, $filter_ids) === FALSE) {
                $field_value = $custom_field_value;
            }
        } else {
            $filter_ids[] = $custom_field_value;
        }
    }
}    

if(isset($_GET[$field_key])) {
    $field_value = ($_GET[$field_key]);
} else {
    if(function_exists('run_wdk_geo') && (get_option('wdk_geo_autodetect_by_ip_enable') && !get_option('wdk_geo_autodetect_by_google_js_enable'))) {
        $field_value  = wdk_geo_get_location_id();
    }
}

wdk_search_fields_toggle();

$hide_fields = '';
if(wmvc_show_data('search_type_tree_hide', $field_data)) {
    $hide_fields = wmvc_show_data('search_type_tree_hide', $field_data);
}

?>

<?php if(isset($custom_field_value)):?>
    <input type="hidden" name="search_location[]" value="<?php echo esc_attr($custom_field_value);?>">
<?php endif;?>

<?php if(wdk_get_option('wdk_multi_locations_search_field_type') == 'wdk_treefield_dropdown'):?>
    <?php
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->load_helper('listing');
        $Winter_MVC_WDK->model('location_m');

        $locations = array();
        if(!empty($field_value)) {
            $locations[] = $field_value;
            $location = $Winter_MVC_WDK->location_m->get($field_value, TRUE); 
            
            while(!empty($location->parent_id)) {
                $location = $Winter_MVC_WDK->location_m->get($location->parent_id, TRUE); 
                $locations[] = $location->idlocation;
            }
            krsort($locations);
        } else {
            $locations[] = 0;
        }

        wp_enqueue_style('wdk-treefield-dropdown');
        wp_enqueue_script('wdk-treefield-dropdown');
        wp_enqueue_style( 'dashicons' );

        $level_max = $Winter_MVC_WDK->location_m->get_max_level();
        
        $placeholder = [
            0 => esc_html__('Select Country','wpdirectorykit'),
            1 => esc_html__('Select City','wpdirectorykit'),
            2 => esc_html__('Select Neighborhood','wpdirectorykit'),
            3 => esc_html__('Select Sub Area','wpdirectorykit'),
            4 => esc_html__('Select Sub Area','wpdirectorykit'),
            5 => esc_html__('Select Sub Area','wpdirectorykit'),
        ];


    ?>

    <input name="<?php echo esc_attr($field_key); ?>" type="hidden" value="<?php echo esc_attr($field_value); ?>" class="wdk_field_id_location">
    <?php
        $level = 0;
        foreach ($locations as $location) {
            $current = $Winter_MVC_WDK->location_m->get($location, TRUE); 

            $list = $Winter_MVC_WDK->location_m->get_by(array('parent_id = '.$current->parent_id => NULL)); 

          
            if(isset($placeholder[$level])) {
                $values_list = array(''=> $placeholder[$level]);
            } else {
                $values_list = array(''=> esc_html__('Select Sub Categories','wpdirectorykit'));
            }

            foreach ($list as $list_value) {
                $values_list[$list_value->idlocation] = $list_value->location_title; 
            }
            ?>

            <div data-level="<?php echo esc_attr($level);?>" data-field="<?php echo esc_attr($field_key); ?>" class="wdk-field wdk-col wdk_treefield_dropdown <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?>">
                <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
                <div class="wdk-field-group">
                    <?php echo wmvc_select_option('location_'.$level, $values_list, $location, 'class="wdk-control"');?>
                </div>
            </div>

            <?php
            $level++;
        }
        
        if($level<$level_max ) {
            for (; $level<$level_max;) {
               
            if(isset($placeholder[$level])) {
                $values_list = array(''=> $placeholder[$level]);
            } else {
                $values_list = array(''=> esc_html__('Select Sub Categories','wpdirectorykit'));
            }
    
                ?>
    
                <div data-level="<?php echo esc_attr($level);?>" data-field="<?php echo esc_attr($field_key); ?>" class="wdk-field wdk-col wdk_treefield_dropdown <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?>">
                    <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
                    <div class="wdk-field-group">
                        <?php echo wmvc_select_option('location_'.$level, $values_list, NULL, 'class="wdk-control"');?>
                    </div>
                </div>
    
                <?php
                $level++;
            }
        }
    ?>
<?php else:?>

    <div class="wdk-field wdk-col <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?>">
        <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
        <div class="wdk-field-group">
            <?php if(wdk_get_option('wdk_multi_locations_search_field_type') == 'select2'):?>
                <?php echo wdk_treefield_select_ajax ($field_key.'[]', 'location_m', $field_value, 'location_title','idlocation', '', __('All Locations', 'wpdirectorykit'), $filter_ids);?>
            <?php elseif(wdk_get_option('wdk_multi_locations_search_field_type') == 'wdk_treefield_checkboxes'):?>
                <?php
                    wp_enqueue_style( 'wdk-treefield-checkboxes');
                    wp_enqueue_script( 'wdk-treefield-checkboxes');
                ?>
                <?php echo wdk_treefield_option_checkboxes ('search_location', 'location_m', $field_value, 'location_title', '', __('All Locations', 'wpdirectorykit'), $filter_ids);?>
            <?php else:?>
                <?php echo wdk_treefield_option ('search_location', 'location_m', $field_value, 'location_title', '', __('All Locations', 'wpdirectorykit'), $filter_ids, FALSE, '', $hide_fields);?>
            <?php endif;?>
        </div>
    </div>

<?php endif;?>