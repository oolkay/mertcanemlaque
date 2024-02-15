<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Widget [wdk-listing-field-label], show latest listings in list view
* atts list:
*
* field_id (int) - field id
* post_id (int) - post_id
*
* Layout path : 
* get_template_directory().'/wpdirectorykit/shortcodes/views/shortcode-latest-listings-list.php'
* WPDIRECTORYKIT_PATH.'shortcodes/views/shortcode-latest-listings-list.php'
*/

add_shortcode('wdk-listing-field-label', 'shortcode_wdk_listing_field_label');
function shortcode_wdk_listing_field_label($atts, $content){
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
    
    $data['field_label'] = '';
    if(!empty($data['settings']['field_id'])) {
        $data['field_label'] = wdk_field_label($data['settings']['field_id']);
    }

    return wdk_shortcodes_view('shortcode-wdk-listing-field-label', $data);
}

?>