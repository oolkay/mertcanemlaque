<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Widget [wdk-listing-fields-section], show latest listings in list view
* atts list:
*
* section_id (int) - section id
* post_id (int) - post_id
* field_label_hide (string) - yes|no, default no, hide labels
* field_group_icon_enable (string) - yes|no, default yes, show labels
* label_suffix (string) - text after label, can be ":"
* label_prefix (string) - text before label, can be ":"
* hide_onempty_complete (string) - yes|no, default yes, hide section if all fields empty
* custom_class (string) - custom css class
*
* Layout path : 
* get_template_directory().'/wpdirectorykit/shortcodes/views/shortcode-wdk-listing-fields-section.php'
* WPDIRECTORYKIT_PATH.'shortcodes/views/shortcode-wdk-listing-fields-section.php'
*/

add_shortcode('wdk-listing-fields-section', 'shortcode_wdk_listing_fields_section');
function shortcode_wdk_listing_fields_section($atts, $content){
    $atts = shortcode_atts(array(
        'id'=>NULL,
        'section_id'=>'',
        'post_id'=>'',
        'field_label_hide'=>'',
        'label_suffix'=>':',
        'label_prefix'=>'',
        'field_group_icon_enable'=>'no',
        'hide_onempty_complete'=>'yes',
        'custom_class'=>'',
    ), $atts);
    $data = array();

    /* settings from atts */
    $data['settings'] = $atts;
    $data['id_element'] = '';

    /* load css/js */

    $WMVC = &wdk_get_instance();
    $WMVC->model('listingfield_m');
    $WMVC->model('field_m');
    $WMVC->model('listing_m');
    $WMVC->load_helper('listing');

    global $wdk_listing_id;
    $post_id = $wdk_listing_id;
    if (!empty($data['settings']['post_id'])) {
        $post_id = $data['settings']['post_id'];
    }

    $data['post_id'] = $post_id;
    
    $data['section_label'] = 'Example Section';
    $data['sections_data'] =  $WMVC->field_m->get_fields_section();
    $data['section_data'] =  array();

    if(!empty($data['settings']['section_id'])){
        $data['section_label'] = wdk_field_label($data['settings']['section_id']);
        if(isset($data['sections_data'][$data['settings']['section_id']]))
            $data['section_data'] =  $data['sections_data'][$data['settings']['section_id']];
    }

    if(!empty($data['settings']['field_id']))
        $data['field_label'] = wdk_field_label($data['settings']['field_id']);

    if(empty($data['section_data'])){
        return false;
    }     

    /* return false if no content */
    if($data['settings']['hide_onempty_complete'] == 'yes') {
        $complete_empty = true;
        foreach($data['section_data']['fields'] as $field) {

            if(wdk_field_value('category_id', $post_id) && wdk_depend_is_hidden_field(wmvc_show_data('idfield', $field), wdk_field_value('category_id', $post_id))) {
                continue;
            } 

            if(!empty(wdk_field_value (wmvc_show_data('idfield', $field), $post_id))){
                $complete_empty = false;
                break;
            }
        }

        if($complete_empty)
            return false;
    }

    return wdk_shortcodes_view('shortcode-wdk-listing-fields-section', $data);
}

?>