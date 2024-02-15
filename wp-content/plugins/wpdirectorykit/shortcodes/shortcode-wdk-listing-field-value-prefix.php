<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Widget [wdk-listing-field-value-prefix], show latest listings in list view
* atts list:
*
* field_id (int) - field id
* post_id (int) - post_id
*
* Layout path : 
* get_template_directory().'/wpdirectorykit/shortcodes/views/shortcode-latest-listings-list.php'
* WPDIRECTORYKIT_PATH.'shortcodes/views/shortcode-latest-listings-list.php'
*/

add_shortcode('wdk-listing-field-value-prefix', 'shortcode_wdk_listing_field_value_prefix');
function shortcode_wdk_listing_field_value_prefix($atts, $content){
    $atts = shortcode_atts(array(
        'id'=>NULL,
        'field_id'=>'',
        'post_id'=>'',
        'enable_html'=>'',
    ), $atts);
    $data = array();

    /* settings from atts */
    $data['settings'] = $atts;
    $data['id_element'] = '';

    /* load css/js */

    $WMVC = &wdk_get_instance();
    $WMVC->model('listingfield_m');
    $WMVC->model('listing_m');
    $WMVC->load_helper('listing');
    global $wdk_listing_id;
    $post_id = $wdk_listing_id;
    if (!empty($data['settings']['post_id'])) {
        $post_id = $data['settings']['post_id'];
    }
    
    $data['field_value'] = '';
    $data['field_prefix'] = '';
    $data['field_suffix'] = '';
    if(!empty($data['settings']['field_id'])){
        if(strpos($data['settings']['field_id'],'__') !== FALSE){
            $data['settings']['field_id'] = substr($data['settings']['field_id'], strpos($data['settings']['field_id'],'__')+2);
        }

        $data['field_prefix'] =  wdk_field_option ($data['settings']['field_id'], 'prefix');
        $data['field_suffix'] =  wdk_field_option ($data['settings']['field_id'], 'suffix');
    }

    $data['is_edit_mode']= false;          

    /* return false if no content */
    if(wdk_field_value($data['settings']['field_id'], $post_id) == '')
        return false;
        
    $data['field_prefix'] = apply_filters( 'wpdirectorykit/listing/field/prefix', $data['field_prefix'], $data['settings']['field_id']);

    return wdk_shortcodes_view('shortcode-wdk-listing-field-value-prefix', $data);
}

?>