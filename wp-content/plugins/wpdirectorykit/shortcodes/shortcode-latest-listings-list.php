<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Widget [wdk-latest-listings-list], show latest listings in list view
* atts list:
*
* custom_class (string) - custom css class
* order (string) - order by for results
* conf_query (string) - custom string query like field=xx&field_2=xxx
* limit (int) - limit listings, default 2
* only_is_featured (string), yes, if yes show only featured
* conf_custom_results (string) - like 1,2,3,4,5 where id is id of listing
*
* Layout path : 
* get_template_directory().'/wpdirectorykit/shortcodes/views/shortcode-latest-listings-list.php'
* WPDIRECTORYKIT_PATH.'shortcodes/views/shortcode-latest-listings-list.php'
*/

add_shortcode('wdk-latest-listings-list', 'shortcode_latest_listings_list');
function shortcode_latest_listings_list($atts, $content){
    $atts = shortcode_atts(array(
        'id'=>NULL,
        'custom_class'=>'',
        'order'=>'',
        'conf_query'=>'',
        'limit'=>'2',
        'only_is_featured'=>'',
        'conf_custom_results'=>'',
    ), $atts);
    $data = array();

    /* settings from atts */
    $data['settings'] = $atts;
    $data['id_element'] = '';

    $data['listings_count'] = 0;
    $data['results'] = array();
    $data['pagination_output'] = '';


    /* load css/js */
    wp_enqueue_style('wdk-listings-list');

    $WMVC = &wdk_get_instance();
    $WMVC->model('listingfield_m');
    $WMVC->model('listing_m');
    $WMVC->load_helper('listing');
    $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
    $external_columns = array('location_id', 'category_id', 'post_title');
    
    if(!empty($data['settings']['conf_custom_results'])) {
        /* where in */
        if(!empty($listings_ids)){
            $WMVC->db->where( $WMVC->db->prefix.'wdk_listings.post_id IN(' . $data['settings']['conf_custom_results']  . ')', null, false);
            $data['results'] = $WMVC->listing_m->get();
        }
    } else {
        $controller = 'listing';
        $offset = NULL;
        $original_GET = wmvc_xss_clean($_GET);
        $_GET =  array();

        $_GET['order_by'] = $data['settings']['order'];

        if(!empty($data['settings']['conf_query'])) {
            $qr_string = trim($data['settings']['conf_query'],'?');
            parse_str($qr_string, $_GET);
            $_GET = array_map('trim', $_GET);
        }
        if($data['settings']['only_is_featured'] == 'yes') {
            $_GET['is_featured'] = 'on';
        }

        wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns);
        $data['results'] = $WMVC->listing_m->get_pagination($data['settings']['limit'], $offset, array('is_activated' => 1,'is_approved'=>1));

    }  
    
    if(isset($original_GET))
        $_GET = $original_GET;

    return wdk_shortcodes_view('shortcode-latest-listings-list', $data);;
}

?>