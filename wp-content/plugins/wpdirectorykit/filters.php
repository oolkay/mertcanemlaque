<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* fix for redirect */
add_action('init', 'wdk_do_output_buffer');
function wdk_do_output_buffer() {
    ob_start();
} 

/* add elementor body class */
add_filter( 'body_class', function( $classes ) {
    global $wp_query;
    global $wdk_listing_id;
    global $wdk_listing_page_id;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        $wdk_listing_id = $wp_query->post->ID;
        $wdk_listing_page_id = get_option('wdk_listing_page');
        if(!empty($wdk_listing_page_id) && get_post_status($wdk_listing_page_id) =='publish')
        {
            return array_merge( $classes, array( 'elementor-page-'.$wdk_listing_page_id ) );
        }
    }
    
    return array_merge( $classes, array( 'class-name' ) );
} );

add_filter('the_content', 'wdk_content');  

function wdk_content( $content )
{  
    global $wp_query;
    global $wdk_listing_id;
    global $wdk_listing_page_id;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        $wdk_listing_id = $wp_query->post->ID;
        $wdk_listing_page_id = get_option('wdk_listing_page');
        if(!empty($wdk_listing_page_id) && get_post_status($wdk_listing_page_id) =='publish')
        {
            global $Winter_MVC_WDK;
            $Winter_MVC_WDK->load_helper('listing');

            if(
                (
                    wdk_field_value('is_activated', $wdk_listing_id,false) && wdk_field_value('is_approved', $wdk_listing_id,false)) || 
                    (get_current_user_id() != 0 && get_current_user_id() == wdk_field_value('user_id_editor', $wdk_listing_id,false)) || 
                    (get_current_user_id() != 0 && wmvc_user_in_role('administrator'))
                ) {

                $Winter_MVC_WDK->model('listing_m');
                $Winter_MVC_WDK->listing_m->update_counter($wdk_listing_id);
                
                if(wdk_get_option('wdk_membership_is_enable_subscriptions') &&  wdk_get_option('wdk_membership_subscriptions_view_listing_enabled') && function_exists('run_wdk_membership')){
                    /* if required subscription with View details listing */

                    /* if not login, return alert for ask login */
                    if(is_user_logged_in()) {

                        global $Winter_MVC_wdk_membership;
                        $Winter_MVC_wdk_membership->model('subscription_m');
                        $Winter_MVC_wdk_membership->model('subscription_user_m');
                        $user_subscriptions_listings_view = $Winter_MVC_wdk_membership->subscription_user_m->get_pagination(NULL, FALSE, array('is_activated'=>1,'is_view_private_listings'=>1));

                        /* if not allow subscription, return alert for subscription on dash page */
                        if(empty($user_subscriptions_listings_view)){
                            $content = '<div style="margin: 35px auto;max-width:768px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Required subscription for "View Listings Details", please', 'wpdirectorykit').' <a href="'.esc_url(wdk_dash_url('dash_page=membership')).'">'.esc_html__('purchase subscription here', 'wpdirectorykit').'</a></p></div>';
                        }  else {
                            if(class_exists('Elementor\Plugin'))
                            {
                                $content = '';
                                $elementor_instance = Elementor\Plugin::instance();
                                $content = $elementor_instance->frontend->get_builder_content_for_display($wdk_listing_page_id);
                            }
                        }
                    } else {
                        $content = '<div style="margin: 35px auto;max-width:768px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Required subscription for "View Listings Details", please', 'wpdirectorykit').' <a href="'.wdk_login_url(wdk_server_current_url(), esc_html__('Login for open Listing', 'wpdirectorykit')).'">'.esc_html__('register or login here', 'wpdirectorykit').'</a></p></div>';
                    }

                } 
                /* if login required */
                elseif(get_option('wdk_membership_login_required_listing_preview') && !is_user_logged_in()){
                    if(get_option('wdk_membership_login_page')){
                        wp_redirect(wdk_url_suffix(get_permalink(get_option('wdk_membership_login_page')),'redirect_to='.urlencode(wdk_current_url()).'&custom_message='.urlencode(esc_html__('Please login for open Listing', 'wpdirectorykit'))));
                      //  exit();
                    }  else {
                        wp_redirect(wp_login_url(wdk_current_url()));
                    }
                }
                else {
                    if(class_exists('Elementor\Plugin'))
                    {
                        $content = '';

                        if(!wdk_field_value('is_approved', $wdk_listing_id, false) || !wdk_field_value('is_activated', $wdk_listing_id, false)) {
                            $content = '<div style="margin: 35px auto;max-width:768px;margin-bottom: 35px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Listing is not activated/approved and not visible for public', 'wpdirectorykit').'</p></div>';
                        }

                        $elementor_instance = Elementor\Plugin::instance();
                        $content .= $elementor_instance->frontend->get_builder_content_for_display($wdk_listing_page_id);
                        
                        $custom_css = '';

                        $wdk_listing_id;
                        $Winter_MVC_WDK->model('field_m');

                        $Winter_MVC_WDK->db->where(array('field_type !='=> 'SECTION'));
                        $fields = $Winter_MVC_WDK->field_m->get();

                        foreach($fields as $field) {
                            if(wdk_field_value ($field->idfield, $wdk_listing_id)) continue;

                            if(!empty($custom_css)) $custom_css .= ',';

                            $custom_css .= '.wdk_'.$field->idfield.'_hide_empty';
                        }

                        if(!wdk_field_value ('lat', $wdk_listing_id, false) || !wdk_field_value ('lng', $wdk_listing_id)) {
                            if(!empty($custom_css)) $custom_css .= ',';
                            $custom_css .= '.wdk_map_hide_empty';
                        }

                        if(!wdk_field_value ('is_featured', $wdk_listing_id, false)) {
                            if(!empty($custom_css)) $custom_css .= ',';
                            $custom_css .= '.wdk_is_featured_hide_empty';
                        }

                        $custom_css .= '{display: none !important}';

                        wp_enqueue_style('wdk-custom-inline', WPDIRECTORYKIT_URL.'public/css/custom-inline.css');
                        wp_add_inline_style( 'wdk-custom-inline', $custom_css);

                    }
                }

            } else {
                $content = '<div style="margin: 35px auto;max-width:768px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Listing missing or not activated', 'wpdirectorykit').', <a href="'.get_home_url().'">'.esc_html__('return to Homepage', 'wpdirectorykit').'</a></p></div>';
            }
        } else {
            $content = '<div style="margin: 35px auto;max-width:768px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Missing listing Preview Page', 'wpdirectorykit').', <a href="'.esc_url(get_admin_url()).'admin.php?page=wdk_settings&function=import_demo">'.esc_html__('Import demo Page', 'wpdirectorykit').'</a></p></div>';
        }

    }
    return $content;
}

function wdk_do_header() {
    // get all elementor pro builder conditions
    $pro_theme_builder_conditions = get_option('elementor_pro_theme_builder_conditions');
    $listing_page_id = get_option('wdk_listing_page');

    if(!empty($pro_theme_builder_conditions))
    {
        foreach($pro_theme_builder_conditions['header'] as $header_post_id => $header_on_pages)
        {
            foreach($header_on_pages as $pages)
            {
                $accepted_page_id = substr($pages, strrpos($pages, '/')+1);

                if($accepted_page_id == $listing_page_id)
                {
                    // render header content

                    $elementor_instance = \Elementor\Plugin::instance();
                    $content = $elementor_instance->frontend->get_builder_content_for_display($header_post_id);
            
                    echo wp_kses_post( $content );
                    return;
                }
            }
        }
    }
}

function wdk_do_footer() {
    // get all elementor pro builder conditions
    $pro_theme_builder_conditions = get_option('elementor_pro_theme_builder_conditions');
    $listing_page_id = get_option('wdk_listing_page');

    if(!empty($pro_theme_builder_conditions))
    {
        foreach($pro_theme_builder_conditions['footer'] as $footer_post_id => $footer_on_pages)
        {
            foreach($footer_on_pages as $pages)
            {
                $accepted_page_id = substr($pages, strrpos($pages, '/')+1);

                if($accepted_page_id == $listing_page_id)
                {
                    // render footer content

                    $elementor_instance = \Elementor\Plugin::instance();
                    $content = $elementor_instance->frontend->get_builder_content_for_display($footer_post_id);
            
                    echo wp_kses_post( $content );
                    return;
                }
            }
        }
    }
}

function wdk_do_body_class($classes)
{
    if(class_exists('Elementor\Plugin'))
    {
        $elementor_instance = \Elementor\Plugin::instance();
        $classes = $elementor_instance->frontend->body_class();
    }

    return $classes;
}

function wdk_page_template ($template) {
    global $wp_query;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        $listing_page_id = get_option('wdk_listing_page');
        $template_elem = get_post_meta($listing_page_id, '_wp_page_template', true);

        if($template_elem == 'elementor_theme')
        {
            // for elementor pro theme builder basic support
            $template_elem = 'elementor_canvas';

            add_filter( 'body_class', 'wdk_do_body_class' );
            add_action( 'elementor/page_templates/canvas/before_content', 'wdk_do_header' , 0 );
            add_action( 'elementor/page_templates/canvas/after_content', 'wdk_do_footer' , 0 );
        }

        if($template_elem == 'elementor_canvas' || $template_elem == 'elementor_header_footer' ){
            /**
			 * @var \Elementor\Modules\PageTemplates\Module $page_templates_module
			 */
            if(class_exists('Elementor\Plugin'))
            {
                $page_templates_module =  Elementor\Plugin::instance()->modules_manager->get_modules( 'page-templates' );
                $template = $page_templates_module->get_template_path( $template_elem );
            }
        }
    }
    return $template;
}
add_filter ('single_template', 'wdk_page_template');

add_action('admin_bar_menu', 'wdk_add_toolbar_items', 100);
function wdk_add_toolbar_items($admin_bar){
    global $wp_query;
    if(isset($wp_query->post))
    if ($wp_query->post->post_type == 'wdk-listing') {
        $admin_bar->add_menu(array(
            'id'    => 'edit',
            'title' => __('Edit Listing', 'wpdirectorykit'),
            'href'  => admin_url('admin.php?page=wdk_listing&id='.$wp_query->post->ID),
        ));
    }
}

if(get_option('wdk_slug_listing_preview_page')){
    function wdk_custom_listing_preview_page_slug( $args, $post_type ) {
                
        if(get_option('wdk_slug_listing_preview_page_changed')){
            update_option('wdk_slug_listing_preview_page_changed', 0);
            flush_rewrite_rules();
        }

        /*item post type slug*/   
        if ( 'wdk-listing' === $post_type ) {
           $args['rewrite']['slug'] = get_option('wdk_slug_listing_preview_page');
        }
     
        return $args;
    }
    add_filter( 'register_post_type_args', 'wdk_custom_listing_preview_page_slug', 10, 2 );
}

/* seo wdk feature, wp overwrite wp_head */
add_action( 'wp_head', 'wdk_seo_metatags');
function wdk_seo_metatags(){
    global $wp_query;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        $wdk_listing_id = $wp_query->post->ID;
        if($wdk_listing_id) {
            global $Winter_MVC_WDK;
            $Winter_MVC_WDK->model('field_m');
            $Winter_MVC_WDK->load_helper('listing');
            
            echo '<meta name="title" content="'.esc_attr(wp_get_document_title()).'">'.PHP_EOL;
            echo '<meta property="og:type" content="'.esc_attr(get_post_type()).'" />'.PHP_EOL;
            echo '<meta property="og:title"  content="'.esc_attr(wp_get_document_title()).'" />'.PHP_EOL;

            if(get_option('wdk_seo_description')) {
                echo '<meta name="description" content="'.esc_attr(wp_trim_words(wp_strip_all_tags(wpautop(wdk_field_value(get_option('wdk_seo_description'), $wdk_listing_id))), 20)).'">'.PHP_EOL;
                echo '<meta property="og:description" content="'.esc_attr(wp_trim_words(wp_strip_all_tags(wpautop(wdk_field_value(get_option('wdk_seo_description'), $wdk_listing_id))), 20)).'">'.PHP_EOL;
            }
            
            if(get_option('wdk_seo_keywords')) {
                echo '<meta name="keywords" content="'.esc_attr(wp_trim_words(wp_strip_all_tags(wpautop(wdk_field_value(get_option('wdk_seo_keywords'), $wdk_listing_id))), 20)).'">'.PHP_EOL;
            }

            if(!in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
                echo '<meta property="og:image" content="'.esc_url(wdk_image_src(array('listing_images'=>wdk_field_value('listing_images', $wdk_listing_id)), 'full')).'" />'.PHP_EOL;
            }
        }
    }
}

/* disable some sw_seo feature if Yost seo plugin detected, for only for listing preview page and profile page */
add_action( 'template_redirect', 'wdk_disable_seo' );
function wdk_disable_seo() {
    global $wp_query;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        if(in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
            add_filter( 'wpseo_title', '__return_false');
            add_filter( 'wpseo_metadesc', '__return_false');
            add_filter( 'wpseo_opengraph_url', '__return_false');
            add_filter( 'wpseo_opengraph_title', '__return_false');
            add_filter( 'wpseo_opengraph_image', '__return_false');
            add_filter( 'wpseo_opengraph_type', '__return_false');
            add_filter( 'wpseo_opengraph_desc', '__return_false');
        }
    }
}

function wdk_yoast_presentation($presentation) {
    global $wp_query;
    if(isset($wp_query->post))
    if ($wp_query->post->post_type == 'wdk-listing')
    {
        $wdk_listing_id = $wp_query->post->ID;
        if($wdk_listing_id) {
            global $Winter_MVC_WDK;
            $Winter_MVC_WDK->model('field_m');
            $Winter_MVC_WDK->load_helper('listing');

            $presentation->open_graph_images = [
                [
                    'url' => esc_url(wdk_image_src(array('listing_images'=>wdk_field_value('listing_images', $wdk_listing_id)), 'full')),
                ]
            ];

        }
    }
    return $presentation;
}
add_filter('wpseo_frontend_presentation', 'wdk_yoast_presentation', 30);

add_filter( 'wp_kses_allowed_html', 'wpdirectorykit_wpkses_post_iframe', 10, 2 );
function wpdirectorykit_wpkses_post_iframe( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['iframe'] = array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
	}
	return $tags;
}

add_action('wdk-membership/dash/homepage/widgets', function() {
    if(!current_user_can('edit_own_listings') && !wmvc_user_in_role('administrator')) return false;
    
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->model('listing_m');
    $total_items = $Winter_MVC_WDK->listing_m->total(array(), TRUE, get_current_user_id());
    if(function_exists('wdk_dash_url')){
    ?>
    <div class="wdk-col-12 wdk-col-md-3 wdk-membership-dash-widget_listings"> 
        <a href="<?php echo esc_url(wdk_dash_url('dash_page=listings'));?>" class="wdk-membership-dash-widget">
            <span class="wdk-content">
                <span class="icon"><span class="dashicons dashicons-location"></span></span>
            </span>
            <span class="wdk-side">
            <span class="title"><?php echo esc_html__('Listings','wpdirectorykit');?></span>
                <span class="wdk-count"><?php echo esc_html($total_items);?></span>
            </span>
        </a>
    </div>
<?php
    }
},1);

add_action('wdk-membership/dash/homepage/widgets', function() {
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->model('messages_m');
    $total_items = $Winter_MVC_WDK->messages_m->total_merge(array(), TRUE, get_current_user_id());
    if(function_exists('wdk_dash_url')){
    ?>
    <div class="wdk-col-12 wdk-col-md-3 wdk-membership-dash-widget_messsages"> 
        <a href="<?php echo esc_url(wdk_dash_url('dash_page=messages'));?>" class="wdk-membership-dash-widget">
            <span class="wdk-content">
                <span class="icon"><span class="dashicons dashicons-email"></span></span>
            </span>
            <span class="wdk-side">
            <span class="title"><?php echo esc_html__('Messages','wpdirectorykit');?></span>
                <span class="wdk-count"><?php echo esc_html($total_items);?></span>
            </span>
        </a>
    </div>
<?php
    }
});

add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );


/* detected in smart search location and replace to location id */
add_action( 'template_redirect', 'wdk_search_parameters' );
function wdk_search_parameters() {
    global $wp_query;
    if(!empty($_GET['field_search'])) {
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('location_m');
        $location = $Winter_MVC_WDK->location_m->get_by(array('location_title'=>sanitize_text_field($_GET['field_search'])), TRUE);
        if($location) {
            $url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
          
            $query_link = substr($url, strpos($url, '?')+1);
            $clear_link = substr($url, 0, strpos($url, '?'));

            $string_par = array();
            parse_str( $query_link, $string_par);
            unset($string_par['field_search']);
            $string_par['search_location'] = $location->idlocation;
            
            $new_url =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http").'://'.$clear_link.'?'.http_build_query($string_par);
            wp_redirect($new_url);
            exit;        
        }

    }
   
}

add_filter( 'plugin_action_links_wpdirectorykit/wpdirectorykit.php', 'wdk_buy_link' );
function wdk_buy_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( get_admin_url().'admin.php?page=wdk_addons' );
	// Create the link.
	$settings_link = "<a style=\"color:rgb(0, 163, 42);font-weight:bold;\" href='$url'>" . __( 'Check Premium Features', 'wpdirectorykit') . '</a>';
	// Adds the link to the end of the array.
    $links[] = $settings_link;
	return $links;
}


add_filter( 'wpdirectorykit/listing/field/value', 'wdk_filter_field_format_date', 2, 10);
if(!function_exists('wdk_filter_field_format_date')) {
    function wdk_filter_field_format_date ($field_value = '', $field_id = NULL, $number_format = TRUE) {
        if(in_array($field_id, array('date', 'date-modified'))) {
           $field_value = wdk_get_date($field_value, TRUE);
        }
        
        return $field_value;
    }
}

	
// fix sitemap issue
function wdk_modify_sitemap_index($content) {
    // Modify the $content as needed
    // For example, replace & with &amp;
    $content = str_replace('&', '&amp;', $content);
    return $content;
}

// Hook the filter to the wpseo_sitemap_index filter hook
add_filter('wpseo_sitemap_index', 'wdk_modify_sitemap_index');

?>