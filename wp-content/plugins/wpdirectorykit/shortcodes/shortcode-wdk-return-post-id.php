<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Widget [wdk-return-post-id], show latest listings in list view
* atts list:
*
*/

add_shortcode('wdk-return-post-id', 'shortcode_wdk_return_post_id');
function shortcode_wdk_return_post_id($atts, $content){
    $atts = shortcode_atts(array(
        'id'=>NULL,
    ), $atts);
    $data = array();

    /* settings from atts */
    $data['settings'] = $atts;
    $data['id_element'] = '';

    global $wdk_listing_result_id;
    if(!empty($wdk_listing_result_id))
        return $wdk_listing_result_id;
    
    global $wdk_listing_id;
    if(!empty($wdk_listing_id))
        return $wdk_listing_id;

    return get_the_ID();

}

?>