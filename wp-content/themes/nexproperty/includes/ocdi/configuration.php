<?php


function ocdi_import_files() {
	$pac_name = 'demo-content_premium.xml';
	update_option('nexproperty_install_ocdi_images_sizes_enable', 1);
	if(file_exists(ABSPATH."ocdi/nexproperty/".$pac_name)) {
		return [
			[
				'import_file_name'           => 'NexProperty Real Estate',
				'local_import_file'            => ABSPATH."ocdi/nexproperty/".$pac_name,
				'import_preview_image_url'   => get_template_directory_uri() . '/includes/ocdi/preview/preview_estates.jpg',
				'preview_url'                => 'https://www.wpdirectorykit.com/nexproperty/home-page/',
			],
			[
				'import_file_name'           => 'NexProperty Cars',
				'local_import_file'            =>  ABSPATH."ocdi/nexproperty/".$pac_name,
				'import_preview_image_url'   => get_template_directory_uri() . '/includes/ocdi/preview/preview_cars.jpg',
			]
		];
	} else {
		return [
			[
				'import_file_name'           => 'NexProperty Real Estate',
				'import_file_url'            => 'https://wpdirectorykit.com/nexproperty/'.$pac_name,
				'import_preview_image_url'   => get_template_directory_uri() . '/includes/ocdi/preview/preview_estates.jpg',
				'preview_url'                => 'https://www.wpdirectorykit.com/nexproperty/home-page/',
			],
			[
				'import_file_name'           => 'NexProperty Cars',
				'import_file_url'            => 'https://wpdirectorykit.com/nexproperty/'.$pac_name,
				'import_preview_image_url'   => get_template_directory_uri() . '/includes/ocdi/preview/preview_cars.jpg',
			]
		];

	}
  }

  add_filter( 'ocdi/import_files', 'ocdi_import_files' );

  function ocdi_register_plugins( $plugins ) {
	$theme_plugins = [
	  [ // A WordPress.org plugin repository example.
		'name'     => 'Elementor', // Name of the plugin.
		'slug'     => 'elementor', // Plugin slug - the same as on WordPress.org plugin repository.
		'required' => true,                     // If the plugin is required or not.
	  ],
	];

	if (file_exists(get_template_directory() .'/addons/elementinvader.zip')) {
        $theme_plugins[] = [
			'name'     => 'Element Invader',
			'slug'     => 'elementinvader',  // The slug has to match the extracted folder from the zip.
			'source'   =>  get_template_directory() . '/addons/elementinvader.zip',
			'required' => true,
        ];
    } else {
		$theme_plugins[] =  [ // A locally theme bundled plugin example.
            'name'     => 'Element Invader',
            'slug'     => 'elementinvader',         // The slug has to match the extracted folder from the zip.
            'required' => true,
        ];
	}

	if (file_exists(get_template_directory() .'/addons/elementinvader-addons-for-elementor.zip')) {
        $theme_plugins[] = [
			'name'     => 'ElementInvader Addons for Elementor',
			'slug'     => 'elementinvader-addons-for-elementor',  // The slug has to match the extracted folder from the zip.
			'source'   =>  get_template_directory() . '/addons/elementinvader-addons-for-elementor.zip',
			'required' => true,
        ];
    } else {
		$theme_plugins[] =  [ // A locally theme bundled plugin example.
            'name'     => 'ElementInvader Addons for Elementor',
            'slug'     => 'elementinvader-addons-for-elementor',         // The slug has to match the extracted folder from the zip.
            'required' => true,
        ];
	}

	if (file_exists(get_template_directory() .'/addons/wpdirectorykit.zip')) {
        $theme_plugins[] = [
			'name'     => 'WP Directory Kit',
			'slug'     => 'wpdirectorykit',  // The slug has to match the extracted folder from the zip.
			'source'   =>  get_template_directory() . '/addons/wpdirectorykit.zip',
			'required' => true,
			'preselected' => true,
        ];
    } else {
		$theme_plugins[] = [
			'name'     => 'WP Directory Kit',
			'slug'     => 'wpdirectorykit',  // The slug has to match the extracted folder from the zip.
			'required' => true,
			'preselected' => true,
        ];
	}

    if (file_exists(get_template_directory() .'/addons/wdk-bookings.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Bookings Addon',
              'slug'     => 'wdk-bookings',         // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-bookings.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	if (file_exists(get_template_directory() .'/addons/wdk-currency-conversion.zip')) {
		$theme_plugins[] = [
			  'name'     => 'WDK Currency Conversion Addon',
			  'slug'     => 'wdk-currency-conversion',         // The slug has to match the extracted folder from the zip.
			  'source'   =>  get_template_directory() . '/addons/wdk-currency-conversion.zip',
			  'required' => false,
			  'preselected' => true,
		];
	}
	if (file_exists(get_template_directory() .'/addons/wdk-favorites.zip')) {
		$theme_plugins[] = [
			  'name'     => 'WDK Favorites Addon',
			  'slug'     => 'wdk-favorites',         // The slug has to match the extracted folder from the zip.
			  'source'   =>  get_template_directory() . '/addons/wdk-favorites.zip',
			  'required' => false,
			  'preselected' => true,
		];
	}
	if (file_exists(get_template_directory() .'/addons/wdk-membership.zip')) {
		$theme_plugins[] = [
			  'name'     => 'WDK Membership Addon',
			  'slug'     => 'wdk-membership',         // The slug has to match the extracted folder from the zip.
			  'source'   =>  get_template_directory() . '/addons/wdk-membership.zip',
			  'required' => false,
			  'preselected' => true,
		];
	}
    if (file_exists(get_template_directory() .'/addons/wdk-mortgage.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Mortgage Addon',
              'slug'     => 'wdk-mortgage',         // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-mortgage.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/wdk-wp-all-import.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Wp All Import',
              'slug'     => 'wdk-wp-all-import',         // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-wp-all-import.zip',
              'required' => false,
              'preselected' => false,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/profile-picture-uploader.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Profile picture uploader',
              'slug'     => 'profile-picture-uploader',         // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/profile-picture-uploader.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/wdk-reviews.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Reviews',
              'slug'     => 'wdk-reviews',         // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-reviews.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/sweet-energy-efficiency.zip')) {
        $theme_plugins[] = [
              'name'     => 'Sweet Energy Efficiency',
              'slug'     => 'sweet-energy-efficiency',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/sweet-energy-efficiency.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/wdk-report-abuse.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Report Abuse',
              'slug'     => 'wdk-report-abuse',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-report-abuse.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/wdk-facebook-comments.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Facebook Comments',
              'slug'     => 'wdk-facebook-comments',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-facebook-comments.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/wdk-mailchimp.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Mailchimp',
              'slug'     => 'wdk-mailchimp',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-mailchimp.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/wdk-compare-listing.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Compare listing',
              'slug'     => 'wdk-compare-listing',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-compare-listing.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/wdk-save-search.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Save Search',
              'slug'     => 'wdk-save-search',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-save-search.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
    if (file_exists(get_template_directory() .'/addons/wdk-pdf-export.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK PDF Download',
              'slug'     => 'wdk-pdf-export',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-pdf-export.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
	
    if (file_exists(get_template_directory() .'/addons/wdk-listing-claim.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Claim / Take Ownership',
              'slug'     => 'wdk-listing-claim',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-listing-claim.zip',
              'required' => false,
              'preselected' => true,
        ];
    }

    if (file_exists(get_template_directory() .'/addons/wdk-duplicate-listing.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Duplicate Listing',
              'slug'     => 'wdk-duplicate-listing',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-duplicate-listing.zip',
              'required' => false,
              'preselected' => true,
        ];
    }

    if (file_exists(get_template_directory() .'/addons/wdk-geo.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Geo',
              'slug'     => 'wdk-geo',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-geo.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
		
    if (file_exists(get_template_directory() .'/addons/wdk-svg-map.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK SVG Maps',
              'slug'     => 'wdk-svg-map',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-svg-map.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
		
    if (file_exists(get_template_directory() .'/addons/wdk-api.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK API',
              'slug'     => 'wdk-api',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-api.zip',
              'required' => false,
              'preselected' => false,
        ];
    }
    if (file_exists(get_template_directory() .'/addons/wdk-messages-chat.zip')) {
        $theme_plugins[] = [
              'name'     => 'WDK Live Chat',
              'slug'     => 'wdk-messages-chat',  // The slug has to match the extracted folder from the zip.
              'source'   =>  get_template_directory() . '/addons/wdk-messages-chat.zip',
              'required' => false,
              'preselected' => true,
        ];
    }
    if (file_exists(get_template_directory() .'/addons/wdk-membership.zip')) {
        $theme_plugins[] = [
            'name'     => 'WooCommerce',
            'slug'     => 'woocommerce',  // The slug has to match the extracted folder from the zip.
            'required' => false,
            'preselected' => true,
        ];
	}
    
	return array_merge( $plugins, $theme_plugins );
  }
  add_filter( 'ocdi/register_plugins', 'ocdi_register_plugins' );

 /* after import */
function ocdi_after_import_setup($selected_import) {
    // Assign menus to their locations.
    $main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
 
    set_theme_mod( 'nav_menu_locations', array(
            'main-menu' => $main_menu->term_id, // replace 'main-menu' here with the menu location identifier from register_nav_menu() function in your theme.
	));

	$main_menu = get_term_by( 'Menu 1', 'Main Menu', 'nav_menu', 'Menu 1' );

	if(!$main_menu) {
		$main_menu = wp_get_nav_menu_object("Menu 1" );
		set_theme_mod( 'nav_menu_locations', array(
			'main_menu' => $main_menu->term_id,
		));
	}
 
    // Assign front page and posts page (blog page).
    $front_page_id = nexproperty_page_by_title( 'Home Page' );
    $listing_page_id  = nexproperty_page_by_title( 'Listing Preview' );
    $results_page_id  = nexproperty_page_by_title( 'Grid map' );
    $page_for_posts_id = nexproperty_page_by_title( 'Blog' );
 
    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $page_for_posts_id->ID );
	
	if($listing_page_id)
		update_option( 'wdk_listing_page', $listing_page_id->ID, TRUE);
	
	if($results_page_id)
		update_option( 'wdk_results_page', $results_page_id->ID, TRUE);

	/* remove default post */
		
	$post_default= nexproperty_page_by_title('Hello world!', OBJECT, 'post');
	if($post_default)
		wp_delete_post(  $post_default->ID, true );

	/* import wdk content */
	$WMVC = &wdk_get_instance();

	if ( 'NexProperty Cars' === $selected_import['import_file_name'] ) {
		$_GET['multipurpose'] = 'car-dealer.xml';
    }

	$WMVC->load_controller('wdk_settings','_api_import');

    /* search udpate */

    $search_form = '[{"field_id":"search","class":"","query_type":"","columns":""},{"field_id":"loc","class":"","query_type":"","columns":""},{"field_id":"cat","class":"","query_type":"","columns":""},{"field_id":"6","class":"","query_type":"slider_range","value_min":"","value_max":"","columns":""},{"field_id":"29","class":"","query_type":"","columns":""},{"field_id":"33","class":"","query_type":"","columns":""},{"field_id":"37","class":"","query_type":"","columns":""},{"field_id":"38","class":"","query_type":"","columns":""}]';
    if(function_exists('run_wdk_bookings')) {
        $search_form = '[{"field_id":"search","class":"","query_type":"","columns":""},{"field_id":"loc","class":"","query_type":"","columns":""},{"field_id":"cat","class":"","query_type":"","columns":""},{"field_id":"6","class":"","query_type":"slider_range","value_min":"","value_max":"","columns":""},{"field_id":"booking_date","class":"","query_type":"","columns":""},{"field_id":"29","class":"","query_type":"","columns":""},{"field_id":"33","class":"","query_type":"","columns":""},{"field_id":"37","class":"","query_type":"","columns":""},{"field_id":"38","class":"","query_type":"","columns":""}]';
    }

    global $wpdb;
    $wpdb->query("UPDATE ".$wpdb->prefix."wdk_searchform SET searchform_json = '{$search_form}' WHERE idsearchform = 1");
    
	/* udpate posts */	
	$posts = get_posts( array(
		'numberposts'=> 5,
		'orderby'   => 'id',
		'order'      => 'ASC',
		'post_type'  => 'post',
	));

	$date = date('Y-m-d H:i:s');
	foreach($posts as $post) {
		$post_udpate = array();
		$post_udpate['ID'] = $post->ID;
		$post_udpate['post_date'] = $date;
		$post_udpate['post_date_gmt'] = $date;
		$post_udpate['post_modified'] = $date;
		$post_udpate['post_modified_gmt'] = $date;
		wp_update_post($post_udpate );
	}

	/* Replace Links */
	/* login */
		
	$from = 'https://www.wpdirectorykit.com/nexproperty/wp-admin/admin.php?page=wdk_listing';
	$to = get_admin_url();
	nexproperty_replace_links($from, $to);

	$from = 'https://www.wpdirectorykit.com/nexproperty/wp-admin/';
	$to = get_admin_url();
	nexproperty_replace_links($from, $to);

	$from = 'https://www.wpdirectorykit.com/nexproperty/index.php/login/';
	$to = get_admin_url();
	nexproperty_replace_links($from, $to);
	
	$from = 'https://www.wpdirectorykit.com/nexproperty';
	$to = get_home_url();
	nexproperty_replace_links($from, $to);
	
	/* homepage */
	$from = 'home_page_link_replace';
	$to = get_home_url();
	nexproperty_replace_links($from, $to);
	
	/* homepage */
	$from = '2020';
	$to = date('Y');
	nexproperty_replace_links($from, $to);
	$from = '2021';
	nexproperty_replace_links($from, $to);
	
	/* wdk_listing_preview_feature_category */
	$from = 'wdk_listing_preview_feature_category';
	$to = 26;
	nexproperty_replace_links($from, $to);
	if ( 'NexProperty Cars' === $selected_import['import_file_name'] ) {
		/* homepage */
		$from = 'Properties';
		$to = 'Cars';
		nexproperty_replace_links($from, $to);
		$from = 'Popular House Types';
		$to = 'Popular Car Types';
		nexproperty_replace_links($from, $to);
		$from = 'House';
		$to = 'Car';
		nexproperty_replace_links($from, $to);
		$from = 'Add Property'; 
		$to = 'Add Car';
		nexproperty_replace_links($from, $to);
    }

	/* custom_logo */
	if(function_exists('wmvc_add_wp_image')) {
		$custom_logo_id = wmvc_add_wp_image(get_template_directory() .'/assets/images/logo.jpg');
		set_theme_mod( 'custom_logo', $custom_logo_id );

    	set_theme_mod( 'footer_logo', get_template_directory_uri() .'/assets/images/logo5.png');
    	set_theme_mod( 'footer_logo', get_template_directory_uri() .'/assets/images/logo5.png');
				
		$custom_logo_id = wmvc_add_wp_image(get_template_directory() .'/assets/images/fav.jpg');
		update_option( 'site_icon', $custom_logo_id );
	}

	set_theme_mod( 'footer_content', esc_html__('Aenean sollicitudin, lorem quis bibend auctor, nisi elit consequat ipsum, necittis sem nibh id elit. Duis sed odio enim.','nexproperty') );
	set_theme_mod( 'footer_phone_number', '(917) 382-2057' );
	set_theme_mod( 'footer_email_address', 'agent@info.com' );
	set_theme_mod( 'footer_copyright_text', 'Copyright © '.date('Y').' NexProperty' );

	/* sidebar */
	if(true){
		/* clear */
		$sidebars_widgets = get_option( 'sidebars_widgets' );
		$sidebars_widgets['sidebar-1'] = array();
		update_option('sidebars_widgets', $sidebars_widgets); //update sidebars
		
		nexproperty_insert_widget('sidebar-1', 'search');
		nexproperty_insert_widget('sidebar-1', 'recent-posts');
		nexproperty_insert_widget('sidebar-1', 'categories');

		/* clear */
		$sidebars_widgets = get_option( 'sidebars_widgets' );
		$sidebars_widgets['sidebar'] = array();
		update_option('sidebars_widgets', $sidebars_widgets); //update sidebars
		
		nexproperty_insert_widget('sidebar', 'search');
		nexproperty_insert_widget('sidebar', 'recent-posts');
		nexproperty_insert_widget('sidebar', 'categories');

		/* clear */
		$sidebars_widgets = get_option( 'sidebars_widgets' );
		$sidebars_widgets['footer'] = array();
		update_option('sidebars_widgets', $sidebars_widgets); //update sidebars
		
		nexproperty_insert_widget('footer', 'text', array('title' => esc_html__('Popular Properties', 'nexproperty'), 'text'=>'[wdk-latest-listings-list]'));
		nexproperty_insert_widget('footer', 'recent-posts');
		nexproperty_insert_widget('footer', 'text', array('title' => esc_html__('Newsletter', 'nexproperty'), 'text'=>'[eli-newsletter]'));
	}

	/* header buttons */
	if(true){
		set_theme_mod('show_sign_in_button','yes');
		set_theme_mod('show_property_button','yes');
		set_theme_mod('sign_in_button_text', esc_html__('Login', 'nexproperty'));

		if ( 'NexProperty Cars' === $selected_import['import_file_name'] ) {
			set_theme_mod('property_button_text', esc_html__('Add Car', 'nexproperty'));
		} else {
			set_theme_mod('property_button_text', esc_html__('Add Property', 'nexproperty'));
		}
	}

	update_option('nexproperty_install_ocdi_images_sizes_enable', 0);

	update_option('wdk_multi_locations_search_enable', 1);
	update_option('wdk_multi_categories_search_enable', 1);
	update_option('wdk_multi_locations_other_enable', 1);
	update_option('wdk_multi_categories_other_enable', 1);

    update_option('wdk_theme_nexproperty_installed', 1);
}

function nexproperty_replace_links($from = '', $to = '') {
	global $wpdb;
	// @codingStandardsIgnoreStart cannot use `$wpdb->prepare` because it remove's the backslashes
	$rows_affected = $wpdb->query(
		"UPDATE {$wpdb->postmeta} " .
		"SET `meta_value` = REPLACE(`meta_value`, '" . str_replace( '/', '\\\/', $from ) . "', '" . str_replace( '/', '\\\/', $to ) . "') " .
		"WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE '[%' ;" );
	/* end login */
}

add_action( 'ocdi/after_import', 'ocdi_after_import_setup' );

if(!function_exists('nexproperty_insert_widget'))
{
    function nexproperty_insert_widget($sidebar_id, $widget_name, $widget_options_new = array())
    {
        static $sidebar_cleared = array();
        
        static $widgets_array = array();
        $id = 1;
        
        if(isset($widgets_array[$widget_name])) {
            $widgets_array[$widget_name]++;
            $id = $widgets_array[$widget_name];
        } else {
            $widgets_array[$widget_name] = $id;
        }
        
        $sidebars_widgets = get_option( 'sidebars_widgets' );
        /* set teme mod */ 
        
        $widget_options = get_option('widget_'.$widget_name);
        if(empty($widget_options)) {
			$widget_options = array('_multiwidget'=>1);
		}
        $widget_options[$id] = array('title'=>'');
        
        $widget_options[$id] = $widget_options_new;
        
        
        // [Check and skip import if found]
        $skip_widget_import = false;
        if(isset($sidebars_widgets[$sidebar_id]))
        foreach($sidebars_widgets[$sidebar_id] as $val)
        {
            if(strpos($val, $widget_name) !== false)
                $skip_widget_import = true;
        }
        if(false && $skip_widget_import)
        {
            return FALSE;
        }
        // [/Check and skip import if found]

        if(isset($sidebars_widgets[$sidebar_id]) && !in_array($widget_name.'-'.$id, $sidebars_widgets[$sidebar_id])) { //check if sidebar exists and it is empty
            
            if(empty($sidebars_widgets[$sidebar_id]))
            {
                $sidebars_widgets[$sidebar_id] = array($widget_name.'-'.$id); //add a widget to sidebar
            }
            else
            {
                $sidebars_widgets[$sidebar_id][] = $widget_name.'-'.$id;
            }

            update_option('widget_'.$widget_name, $widget_options); //update widget default options
            update_option('sidebars_widgets', $sidebars_widgets); //update sidebars
        }
        else // if sidebar doesn't exists'
        {
            $sidebars_widgets[$sidebar_id] = array($widget_name.'-'.$id); //add a widget to sidebar
            $sidebars_widgets[$sidebar_id][] = $widget_name.'-'.$id;

            update_option('widget_'.$widget_name, $widget_options); //update widget default options
            update_option('sidebars_widgets', $sidebars_widgets); //update sidebars
        }

        
        return TRUE;
    }
}