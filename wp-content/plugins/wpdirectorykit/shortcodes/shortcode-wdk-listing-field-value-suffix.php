<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Widget [wdk-listing-field-value-suffix], show latest listings in list view
* atts list:
*
* field_id (int) - field id
* post_id (int) - post_id
*
* Layout path : 
* get_template_directory().'/wpdirectorykit/shortcodes/views/shortcode-latest-listings-list.php'
* WPDIRECTORYKIT_PATH.'shortcodes/views/shortcode-latest-listings-list.php'
*/

add_shortcode('wdk-listing-field-value-suffix', 'shortcode_wdk_listing_field_value_suffix');
function shortcode_wdk_listing_field_value_suffix($atts, $content){
    $atts = shortcode_atts(array(
        'id'=>NULL,
        'field_id'=>'',
        'enable_html'=>'',
        'post_id'=>'',
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
    $data['field_suffix'] = '';
    $data['field_suffix'] = '';
    if(!empty($data['settings']['field_id'])){
        if(strpos($data['settings']['field_id'],'__') !== FALSE){
            $data['settings']['field_id'] = substr($data['settings']['field_id'], strpos($data['settings']['field_id'],'__')+2);
        }

        $data['field_suffix'] =  wdk_field_option ($data['settings']['field_id'], 'suffix');
        $data['field_suffix'] =  wdk_field_option ($data['settings']['field_id'], 'suffix');
    }

    $data['is_edit_mode']= false;          

    /* return false if no content */
    if(wdk_field_value($data['settings']['field_id'], $post_id) == '')
        return false;
        
    $data['field_suffix'] = apply_filters( 'wpdirectorykit/listing/field/suffix', $data['field_suffix'], $data['settings']['field_id']);

    return wdk_shortcodes_view('shortcode-wdk-listing-field-value-suffix', $data);
}

?>