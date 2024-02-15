<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Widget [wdk-listing-field-value-text], show latest listings in list view
* atts list:
*
* field_id (int) - field id
* post_id (int) - post_id
*
* Layout path : 
* get_template_directory().'/wpdirectorykit/shortcodes/views/shortcode-latest-listings-list.php'
* WPDIRECTORYKIT_PATH.'shortcodes/views/shortcode-latest-listings-list.php'
*/

add_shortcode('wdk-listing-field-value-text', 'shortcode_wdk_listing_field_value_text');
function shortcode_wdk_listing_field_value_text($atts, $content){
    $atts = shortcode_atts(array(
        'id'=>NULL,
        'field_id'=>'',
        'post_id'=>'',
        'enable_html'=>'',
        'norender'=>'',
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

        if($data['settings']['norender'] == 1) {
            $data['field_value'] = wdk_field_value ($data['settings']['field_id'], $post_id);
        } elseif(wdk_field_option($data['settings']['field_id'], 'field_type') == "CHECKBOX") {
            if(wdk_field_value ($data['settings']['field_id'], $post_id) == 1){
                $data['field_value'] = '<span class="field_checkbox_success"><span class="label label-success"><span class="dashicons dashicons-saved"></span></span></span>';
            } else {
                $data['field_value'] = '<span class="field_checkbox_unsuccess"><span class="label label-success"><span class="dashicons dashicons-unsaved"></span></span></span>';
            }  
        } else if(wdk_field_option($data['settings']['field_id'],'field_type') == "INPUTBOX") {
            $data['field_value'] = wdk_field_value ($data['settings']['field_id'], $post_id);

            if(strpos($data['field_value'], 'vimeo.com') !== FALSE)
            {
                    $data['field_value'] = wp_oembed_get($data['field_value'], array("width"=>"800", "height"=>"450"));
            }
            elseif(strpos($data['field_value'], 'watch?v=') !== FALSE)
            {
                $embed_code = substr($data['field_value'], strpos($data['field_value'], 'watch?v=')+8);
                $data['field_value'] =  wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code, array("width"=>"800", "height"=>"455"));
            }
            elseif(strpos($data['field_value'], 'youtu.be/') !== FALSE)
            {
                $embed_code = substr($data['field_value'], strpos($data['field_value'], 'youtu.be/')+9);
                $data['field_value'] = wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code, array("width"=>"800", "height"=>"455"));
            } 
            elseif(filter_var($data['field_value'], FILTER_VALIDATE_URL) !== FALSE && preg_match('/\.(mp4|flv|wmw|ogv|webm|ogg)$/i', $data['field_value']))
            {
                $data['field_value']  = '<video src="'.$data['field_value'].'" controls></video> ';
            }
            elseif(filter_var($data['field_value'] , FILTER_VALIDATE_URL) !== FALSE) {
                $data['field_value']  = '<a href="'.$data['field_value'] .'">'.wdk_field_label($data['settings']['field_id']).'</a>';
            }
        }
        elseif($data['settings']['field_id'] == 'category_id') {
            if(wdk_field_value ($data['settings']['field_id'], $post_id)){
                $WMVC->model('category_m');
                $tree_data = $WMVC->category_m->get(wdk_field_value ($data['settings']['field_id'], $post_id), TRUE);
                $data['field_value'] = wmvc_show_data('category_title', $tree_data);
            }
        }
        elseif($data['settings']['field_id'] == 'location_id') {
            if(wdk_field_value ($data['settings']['field_id'], $post_id)){
                $WMVC->model('location_m');
                $tree_data = $WMVC->location_m->get(wdk_field_value ($data['settings']['field_id'], $post_id), TRUE);
                $data['field_value'] = wmvc_show_data('location_title', $tree_data);
            }
        }
        else {
            $data['field_value'] = wdk_field_value ($data['settings']['field_id'], $post_id);
        }
   
        $data['field_prefix'] =  wdk_field_option ($data['settings']['field_id'], 'prefix');
        $data['field_suffix'] =  wdk_field_option ($data['settings']['field_id'], 'suffix');
    }

    $data['is_edit_mode']= false;          

    /* return false if no content */
    if(wdk_field_value($data['settings']['field_id'], $post_id) == '')
        return false;
        
    $data['field_value'] = apply_filters( 'wpdirectorykit/listing/field/value', (wmvc_show_data('value', $data['field_value'])), $data['settings']['field_id']);

    if($data['settings']['norender'] == 1) {
        return 'https://www.youtube.com/embed/cewZBOGzbPg';
        //echo $data['field_value'];
    } else {
        return wdk_shortcodes_view('shortcode-wdk-listing-field-value-text', $data);
    }
}


function link_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'url' => '#',
        'text' => 'Click Here'
    ), $atts ) );
    return '<a href="' . esc_url( $url ) . '">' . esc_attr( $text ) . '</a>';
}
add_shortcode( 'link', 'link_shortcode' );

?>