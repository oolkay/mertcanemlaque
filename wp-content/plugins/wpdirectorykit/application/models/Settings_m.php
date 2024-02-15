<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Settings_m extends Winter_MVC_Model {

	public $_table_name = 'options';
	public $_order_by = 'option_id';
    public $_primary_key = 'option_id';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';
    public $fields_list = array();
    public $fields_list_tabs = array();
    
	public function __construct(){
        parent::__construct();

        $pages = array('' => __('Not Selected', 'wpdirectorykit'));
        foreach(get_pages(array('sort_column' => 'post_title')) as $page)
        {
            $pages[$page->ID] = $page->post_title.' #'.$page->ID;
        }

        $WMVC = &wdk_get_instance();
        $WMVC->model('field_m');
		$fields_data = $WMVC->field_m->get();
        $fields_list = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
        $fields_list_inputs = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
        $order_i = 0;

        $fields_list ['section'] = esc_html__('-- Section Custom fields --', 'wpdirectorykit');
        $fields_list ['post_title'] = esc_html__('WP Title', 'wpdirectorykit');
        $fields_list ['post_content'] = esc_html__('WP Content', 'wpdirectorykit');

        foreach($fields_data as $field)
        {
            if(wmvc_show_data('field_type', $field) == 'SECTION') {
                $fields_list ['section__'.wmvc_show_data('idfield', $field)] = '-- '.esc_html__('Section', 'wpdirectorykit').' '.wmvc_show_data('field_label', $field).' --';
            } else {
                $fields_list[wmvc_show_data('idfield', $field)] = '#'.wmvc_show_data('idfield', $field).' '.wmvc_show_data('field_label', $field).'['.wmvc_show_data('field_type', $field).']';
            }

            if(wmvc_show_data('field_type', $field) == 'INPUTBOX') {
                $fields_list_inputs [wmvc_show_data('idfield', $field)] = '#'.wmvc_show_data('idfield', $field).' '.wmvc_show_data('field_label', $field).'['.wmvc_show_data('field_type', $field).']';
            } 
        }

        /* General */ 

        $this->fields_list_tabs['general'] = array( 
            array('field' => 'wdk_default_lat', 'field_label' => __('Default GPS Position', 'wpdirectorykit'), 'hint' => '', 'field_type' => 'MAP', 'rules' => ''),
            array('field' => 'wdk_default_lng', 'field_label' => __('Default GPS Longitude', 'wpdirectorykit'), 'hint' => '', 'field_type' => 'INPUTBOX', 'rules' => '', 'class' => 'hidden'),
            array('field' => 'wdk_fixed_map_results_position', 'field_label' => __('Fix map center on default location', 'wpdirectorykit'), 'hint' => '', 'field_type' => 'CHECKBOX', 'rules' => '', 'class' => ''),
           
            array('field' => 'wdk_is_category_enabled', 'field_label' => __('Enable Categories', 'wpdirectorykit'), 'hint' => __('If you don\'t need categories features you can disable that here', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_location_enabled', 'field_label' => __('Enable Locations', 'wpdirectorykit'), 'hint' => __('If you don\'t need location features you can disable that here', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => '', 'class' => ''),
            
           
            array('field' => 'wdk_is_featured_enabled', 'field_label' => __('Enable Field and Feature Featured Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_rank_enabled', 'field_label' => __('Enable Field and Feature Rank Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_user_editor_enabled', 'field_label' => __('Enable Field and Feature Agent Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_post_content_enable', 'field_label' => __('Enable Field and Feature Post Content Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_alt_agent_enabled', 'field_label' => __('Enable Field and Feature Agent Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            
            array('field' => 'wdk_slug_listing_preview_page', 'field_label' => __('Custom Listing Preview Page Slug', 'wpdirectorykit'), 'hint' => __('Slug used for listing preview page, if empty, default will be used', 'wpdirectorykit'), 'field_type' => 'INPUTBOX', 'rules' => 'wdk_slug_format'),
            array('field' => 'wdk_listing_page', 'field_label' => __('Listing Page', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'DROPDOWN', 'rules' => '', 'values' => $pages),
            array('field' => 'wdk_results_page', 'field_label' => __('Results Page', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for results page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'DROPDOWN', 'rules' => '', 'values' => $pages),
            array('field' => 'wdk_is_results_page_require', 'field_label' => __('Always use results page', 'wpdirectorykit'), 'hint' => __('Always even if on current page you have results where can be showed', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            
            array(
                'field' => 'wdk_card_video_enable', 
                'field_label' => __('Card Video From gallery in result card enable', 'wpdirectorykit'), 
                'hint' => __('If you upload video on first place in listing, will become visible in result card', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_listing_category_required', 
                'field_label' => __('Category is required field', 'wpdirectorykit'), 
                'hint' => __('if this is enabled, listing required category field', 'wpdirectorykit'),
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_listing_location_required', 
                'field_label' => __('Location is required field', 'wpdirectorykit'), 
                'hint' => __('if this is enabled, listing required location field', 'wpdirectorykit'),
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),

            array(
                'field' => 'wdk_sub_listings_enable', 
                'field_label' => __('Related Listings', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_listings_images_required_enable', 
                'field_label' => __('Listing Images required', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_default_currency_symbol', 
                'field_label' => __('Default Currency Symbol1', 'wpdirectorykit'), 
                'hint' => __('Put default currency symbol like $', 'wpdirectorykit'), 
                'field_type' => 'INPUTBOX', 
                'rules' => '', 
            ),
        ); 


        /* Templates */ 
        $this->fields_list_tabs['templates'] = array(
            array(
                'field' => 'wdk_placeholder', 
                'field_label' => __('Cooming soon listing image', 'wpdirectorykit'), 
                'hint' => '', 
                'field_type' => 'UPLOAD', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_card_slider_enable', 
                'field_label' => __('Show slider in result card', 'wpdirectorykit'), 
                'hint' => '', 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_card_video_field', 
                'field_label' => __('Card video field', 'wpdirectorykit'), 
                'hint' => '', 
                'field_type' => 'DROPDOWN', 
                'values' => $fields_list_inputs,
                'rules' => '', 
                'hint' => '', 
            ),
            array(
                'field' => 'wdk_mobile_bottom_navbar_enable', 
                'field_label' => __('Mobile bottom navbar', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_field_search_suggestion_disable', 
                'field_label' => __('Smart Search Suggestion Disable', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
        );

        /* Fields */ 
        $this->fields_list_tabs['fields'] = array(
            array('field' => 'wdk_seo_keywords', 'field_label' => __('SEO Listing Page Keywords', 'wpdirectorykit'), 'hint' => __('Set field for meta tag keywords', 'wpdirectorykit'), 'field_type' => 'DROPDOWN', 'values' => $fields_list, 'rules' => ''),
            array('field' => 'wdk_seo_description', 'field_label' => __('SEO Listing Page Description', 'wpdirectorykit'), 'hint' => __('Set field for meta tag description', 'wpdirectorykit'), 'field_type' => 'DROPDOWN', 'values' => $fields_list, 'rules' => ''),
           
            array(
                'field'  => 'wdk_multi_locations_search_field_type', 
                'field_label' => __('Location field Search', 'wpdirectorykit'), 
                'field_type' => 'RADIO', 
                'hint' => __('Visible on search form', 'wpdirectorykit'), 
                'rules' => '', 
                'values' => array(
                    '' => esc_html__('Single Tree Dropdown', 'wpdirectorykit'),
                    'select2' => esc_html__('Multi Select Dropdown', 'wpdirectorykit'),
                    'wdk_treefield_dropdown' => esc_html__('Multiple Dropdowns', 'wpdirectorykit'),
                    'wdk_treefield_checkboxes' => esc_html__('Single Tree Dropdown with checkboxes', 'wpdirectorykit'),
                )
            ),

            array(
                'field' => 'wdk_multi_categories_search_field_type', 
                'field_label' => __('Categoreis field Search', 'wpdirectorykit'), 
                'field_type' => 'RADIO', 
                'hint' => __('Visible on search form. Hiding field in search form based on category is only possible when using single tree dropdown', 'wpdirectorykit'), 
                'rules' => '', 
                'values' => array(
                    '' => esc_html__('Single Tree Dropdown', 'wpdirectorykit'),
                    'select2' => esc_html__('Multi Select Dropdown', 'wpdirectorykit'),
                    'wdk_treefield_dropdown' => esc_html__('Multiple Dropdowns', 'wpdirectorykit'),
                    'wdk_treefield_checkboxes' => esc_html__('Single Tree Dropdown with checkboxes', 'wpdirectorykit'),
                )
            ),
           
            array(
                'field'  => 'wdk_multi_locations_edit_field_type', 
                'field_label' => __('Location field edit', 'wpdirectorykit'), 
                'field_type' => 'RADIO', 
                'hint' => __('Visible on edit form', 'wpdirectorykit'), 
                'rules' => '', 
                'values' => array(
                    '' => esc_html__('Single Tree Dropdown', 'wpdirectorykit'),
                    'wdk_treefield_dropdown' => esc_html__('Multiple Dropdowns', 'wpdirectorykit'),
                )
            ),

            array(
                'field' => 'wdk_multi_categories_edit_field_type', 
                'field_label' => __('Categoreis field edit', 'wpdirectorykit'), 
                'field_type' => 'RADIO', 
                'hint' => __('Visible on edit form. Hiding field in edit form based on category is only possible when using single tree dropdown', 'wpdirectorykit'), 
                'rules' => '', 
                'values' => array(
                    '' => esc_html__('Single Tree Dropdown', 'wpdirectorykit'),
                    'wdk_treefield_dropdown' => esc_html__('Multiple Dropdowns', 'wpdirectorykit'),
                )
            ),
            
            array('field' => 'wdk_multi_locations_other_enable', 'field_label' => __('Enable multi location on edit listing', 'wpdirectorykit'), 'hint' => __('Enable multi location on edit listing', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_multi_categories_other_enable', 'field_label' => __('Enable multi categories on edit listing', 'wpdirectorykit'), 'hint' => __('Enable multi categories on edit listing', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array(
                'field' => 'wdk_listing_plangs_documents_disable', 
                'field_label' => __('Disable "Listing plans and documents"', 'wpdirectorykit'), 
                'hint' => '', 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_listing_images_disable', 
                'field_label' => __('Disable "Listing images"', 'wpdirectorykit'), 
                'hint' => '', 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),

                       
            array('field' => 'wdk_is_category_enabled', 'field_label' => __('Enable Categories', 'wpdirectorykit'), 'hint' => __('If you don\'t need categories features you can disable that here', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_location_enabled', 'field_label' => __('Enable Locations', 'wpdirectorykit'), 'hint' => __('If you don\'t need location features you can disable that here', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => '', 'class' => ''),
            array('field' => 'wdk_is_address_enabled', 'field_label' => __('Enable Address', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_featured_enabled', 'field_label' => __('Enable Field and Feature Featured Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_rank_enabled', 'field_label' => __('Enable Field and Feature Rank Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_user_editor_enabled', 'field_label' => __('Enable Field and Feature Agent Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_post_content_enable', 'field_label' => __('Enable Field and Feature Post Content Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'wdk_is_alt_agent_enabled', 'field_label' => __('Enable Field and Feature Agent Listing', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array(
                'field' => 'wdk_sub_listings_enable', 
                'field_label' => __('Related Listings', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_listings_images_required_enable', 
                'field_label' => __('Listing Images required', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
        );

        /* Api-s */ 
        $this->fields_list_tabs['apis'] = array(
            array(
                'field' => 'wdk_recaptcha_site_key', 
                'field_label' => __('Recaptcha site key', 'wpdirectorykit'), 
                'hint' => __('Please add Recaptcha site and Secret keys for enable recaptcha Add Google Recaptcha site key (use V2 recaptcha key)', 'wpdirectorykit').'. '.wdk_sprintf(__('Get key %1$s here %2$s','wpdirectorykit'),'<a target="_blank" href="https://www.google.com/recaptcha/admin">','</a>'), 
                'field_type' => 'INPUTBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_recaptcha_secret_key', 
                'field_label' => __('Recaptcha site secret key', 'wpdirectorykit'), 
                'hint' => __('Add Google Recaptcha secret key', 'wpdirectorykit'), 
                'field_type' => 'INPUTBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_geo_google_api_key', 
                'field_label' => __('Google API Key', 'wpdirectorykit'), 
                'hint' => __('Google API Key, for address to gps coordinates conversion', 'wpdirectorykit').'. '.wdk_sprintf(__('Get key %1$s here %2$s','wpdirectorykit'),'<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">','</a>'), 
                'field_type' => 'INPUTBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_import_google_api_enable', 
                'field_label' => __('Google API for import listings', 'wpdirectorykit'), 
                'hint' => __('Google API key will be used to autodetect GPS coordinates when importing listing', 'wpdirectorykit'),
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_geo_coding_results_location', 
                'field_label' => __('Auto Search based on Geo Coding', 'wpdirectorykit'), 
                'hint' => __('if this is enabled, auto search listings based on found geo location', 'wpdirectorykit'),
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
        );

        $this->fields_list_tabs['general'] = array( 
            array('field' => 'wdk_default_lat', 'field_label' => __('Default GPS Position', 'wpdirectorykit'), 'hint' => '', 'field_type' => 'MAP', 'rules' => ''),
            array('field' => 'wdk_default_lng', 'field_label' => __('Default GPS Longitude', 'wpdirectorykit'), 'hint' => '', 'field_type' => 'INPUTBOX', 'rules' => '', 'class' => 'hidden'),
            array('field' => 'wdk_fixed_map_results_position', 'field_label' => __('Fix map center on default location', 'wpdirectorykit'), 'hint' => '', 'field_type' => 'CHECKBOX', 'rules' => '', 'class' => ''),

           
            array('field' => 'wdk_slug_listing_preview_page', 'field_label' => __('Custom Listing Preview Page Slug', 'wpdirectorykit'), 'hint' => __('Slug used for listing preview page, if empty, default will be used', 'wpdirectorykit'), 'field_type' => 'INPUTBOX', 'rules' => 'wdk_slug_format'),
            array('field' => 'wdk_listing_page', 'field_label' => __('Listing Page', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for listing preview page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'DROPDOWN', 'rules' => '', 'values' => $pages),
            array('field' => 'wdk_results_page', 'field_label' => __('Results Page', 'wpdirectorykit'), 'hint' => __('Select regular page which ill be used for results page on frontend, you can create new one also for this purpose', 'wpdirectorykit'), 'field_type' => 'DROPDOWN', 'rules' => '', 'values' => $pages),
            array('field' => 'wdk_is_results_page_require', 'field_label' => __('Always use results page', 'wpdirectorykit'), 'hint' => __('Always even if on current page you have results where can be showed', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            
            array(
                'field' => 'wdk_card_video_enable', 
                'field_label' => __('Card Video From gallery in result card enable', 'wpdirectorykit'), 
                'hint' => __('If you upload video on first place in listing, will become visible in result card', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_listing_category_required', 
                'field_label' => __('Category is required field', 'wpdirectorykit'), 
                'hint' => __('if this is enabled, listing required category field', 'wpdirectorykit'),
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_listing_location_required', 
                'field_label' => __('Location is required field', 'wpdirectorykit'), 
                'hint' => __('if this is enabled, listing required location field', 'wpdirectorykit'),
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),

            array(
                'field' => 'wdk_default_currency_symbol', 
                'field_label' => __('Default Currency Symbol', 'wpdirectorykit'), 
                'hint' => __('Put default currency symbol like $', 'wpdirectorykit'), 
                'field_type' => 'INPUTBOX', 
                'rules' => '', 
            ),
            array(
                'field' => 'wdk_experimental_features', 
                'field_label' => __('Experimental features', 'wpdirectorykit'), 
                'hint' => '', 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            ),
        );

        if(function_exists('run_wdk_geo')) {
            $this->fields_list_tabs['apis'][] = array( 
                'field' => 'wdk_geo_autodetect_by_google_js_enable', 
                'field_label' => __('Enable location autodetect by Google JS', 'wpdirectorykit'), 
                'hint' => __('This using Google JS API, require API key and user need to allow this in Browser (is most accurate)', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            );
        
            $this->fields_list_tabs['apis'][] = array( 
                'field' => 'wdk_geo_autodetect_by_ip_enable', 
                'field_label' => __('Enable location autodatect by IP', 'wpdirectorykit'), 
                'hint' => __('This using Free API www.geoplugin.net, not require API key and is not so accurate as Google version, if both enabled then this will be used if Google failed', 'wpdirectorykit'), 
                'field_type' => 'CHECKBOX', 
                'rules' => '', 
            );
        }

         /* Templates */ 
         if(get_option('wdk_experimental_features')) {
            $this->fields_list_tabs['experimental'] = array(
                array(
                    'field' => 'wdk_experimental_search_popup', 
                    'field_label' => __('Search Popup', 'wpdirectorykit'), 
                    'hint' => __('Element open search form in popup', 'wpdirectorykit'), 
                    'field_type' => 'CHECKBOX', 
                    'rules' => '', 
                ),
                array(
                    'field' => 'wdk_experimental_listing_card_elementor_layout', 
                    'field_label' => __('Listing Card', 'wpdirectorykit'), 
                    'hint' => __('Allow Listing card layout in elementor', 'wpdirectorykit'), 
                    'field_type' => 'CHECKBOX', 
                    'rules' => '', 
                ),
                array(
                    'field' => 'wdk_experimental_listing_popup', 
                    'field_label' => __('Listing Preview Popup', 'wpdirectorykit'), 
                    'hint' => __('Open Listing preview in popup', 'wpdirectorykit'), 
                    'field_type' => 'CHECKBOX', 
                    'rules' => '', 
                ),
                array(
                    'field' => 'wdk_experimental_ajax_results', 
                    'field_label' => __('Ajax results listings', 'wpdirectorykit'), 
                    'hint' => __('Allow ajax load listings for map, results container. Reload results with pagination/search form. After activate settings, also action "Reload content with ajax" in elementor options', 'wpdirectorykit'), 
                    'field_type' => 'CHECKBOX', 
                    'rules' => '', 
                ),
            );
        }

        foreach ($this->fields_list_tabs as $tab_fields) {
            foreach ($tab_fields as $field) {
                $this->fields_list[] = $field;
            }
        }
	}

    /* [START] For dynamic data table */
    
    public function get_available_fields()
    {      
        $fields = $this->db->list_fields($this->_table_name);

        return $fields;
    }
    
    public function total_lang($where = array())
    {
        $this->db->select('COUNT(*) as total_count');
        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->order_by($this->_order_by);
        $query = $this->db->get();
        $res = $this->db->results();

        if(isset($res[0]->total_count))
            return $res[0]->total_count;

        return 0;
    }
    
    public function get_pagination_lang($limit, $offset, $where = array())
    {
        $this->db->select('*');
        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->offset($offset);
        $this->db->order_by($this->_order_by);
        $query = $this->db->get();

        if ($this->db->num_rows() > 0)
            return $this->db->results();
        
        return array();
    }
    
    public function check_deletable($id)
    {
        return true;
    }
    
    /* [END] For dynamic data table */

    
    /* only admin can edit */
    public function is_related($item_id, $user_id, $method = 'edit')
    {
        return false;
    }
}
