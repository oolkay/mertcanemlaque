<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_listing extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
    // Edit listing method
	public function index()
	{
        $this->load->model('field_m');
        $this->load->model('listing_m');
        $this->load->model('listingfield_m');
        $this->load->model('listingusers_m');
        $this->load->model('category_m');
        $this->load->model('location_m');
        $this->load->model('user_m');
        $this->load->model('categorieslistings_m');
        $this->load->model('locationslistings_m');
        $this->load->load_helper('listing');

        $listing_post_id = (int) $this->input->post_get('id');

        wdk_access_check('listing_m', $listing_post_id, NULL, 'edit');
        
        $this->data['db_data'] = NULL;
        $this->data['db_data']['listing_agents'] = array();
        $this->data['db_data']['listing_sub_locations'] = array();
        $this->data['db_data']['listing_sub_categories'] = array();
        $this->data['form'] = &$this->form;
        $this->data['edit_log'] = NULL;

        $this->data['categories'] = $this->category_m->get_parents();
        $this->data['locations']  = $this->location_m->get_parents();

        $or_like = "(meta_value LIKE '%administrator%' 
                    OR meta_value LIKE '%wdk_agency%' 
                    OR meta_value LIKE '%wdk_agent%' 
                    OR meta_value LIKE '%wdk_owner%')";

        $this->data['agents']  = $this->user_m->get_agents_names(array(), $or_like);

        if(!empty($listing_post_id))
        {
            $listing_post = get_post( $listing_post_id );

            $listing_db_data = $this->listing_m->get($listing_post_id, TRUE);

            $listingfield_db_data = $this->listingfield_m->get($listing_post_id, TRUE);
            $listingusers_db_data = $this->listingusers_m->get($listing_post_id);

            $this->data['db_data'] = array_merge((array) $listing_post, 
                                                 (array) $listing_db_data, 
                                                 (array) $listingfield_db_data);
           
            $this->data['db_data']['listing_agents'] = array();
            $this->data['db_data']['listing_sub_locations'] = array();
            $this->data['db_data']['listing_sub_categories'] = array();

            foreach ($listingusers_db_data as $key => $listinguser) {
                if(isset($this->data['agents'][$listinguser->user_id]))
                    $this->data['db_data']['listing_agents'] [$listinguser->user_id]= $this->data['agents'][$listinguser->user_id];
            }

            $this->data['db_data']['listing_sub_locations'] = wdk_generate_other_locations_keys($listing_post_id);
            $this->data['db_data']['listing_sub_categories'] = wdk_generate_other_categories_keys($listing_post_id);
            if(function_exists('run_wdk_payments')) {
                $this->data['packages'] = array();
                
                global $Winter_MVC_wdk_payments;
                $Winter_MVC_wdk_payments->model('package_m');
        
                $packages = $Winter_MVC_wdk_payments->package_m->get_by(array('(date_from IS NULL OR date_from < \''.current_time( 'mysql' ).'\')'=>NULL,
                                                                            '(date_to IS NULL OR date_to > \''.current_time( 'mysql' ).'\')'=>NULL,
                                                                            '(location_id IS NULL OR location_id = '.wdk_show_data('location_id', $listing_db_data, '0', TRUE, TRUE).')'=>NULL,
                                                                            '(category_id IS NULL OR category_id = '.wdk_show_data('category_id', $listing_db_data, '0', TRUE, TRUE).')'=>NULL));
                if (count($packages) > 0) {
                    foreach($packages as $package) {
                        $this->data['packages'][wdk_show_data('idpackage', $package, TRUE, TRUE)] = wdk_show_data('idpackage', $package,'', TRUE, TRUE)
                                                                                                    .', '.wdk_show_data('package_name', $package,'', TRUE, TRUE)
                                                                                                    .'('.wdk_get_date(wdk_show_data('date_to', $package,'', TRUE, TRUE)).')';
                    }
                }
            }
        
            $this->load->model('editlog_m');
            $this->data['edit_log'] = $this->editlog_m->get_pagination_listing($listing_post_id, 10, NULL);
        } 

        if(isset($_POST['listing_agents'])) {
            $this->data['db_data']['listing_agents'] = array();
            foreach ($_POST['listing_agents'] as $user_id) {
                if(isset($this->data['agents'][intval($user_id)]))
                    $this->data['db_data']['listing_agents'] [intval($user_id)]= $this->data['agents'][intval($user_id)];
            }
        }
        
        if(isset($_POST['listing_sub_locations'])) {
            $this->data['db_data']['listing_sub_locations'] = $_POST['listing_sub_locations'];
        }
        if(isset($_POST['listing_sub_categories'])) {
            $this->data['db_data']['listing_sub_categories'] = $_POST['listing_sub_categories'];
        }

        $this->data['subscriptions'] = array();
        $this->data['subscriptions_data'] = array();

        if(wdk_get_option('wdk_membership_is_enable_subscriptions') && function_exists('run_wdk_membership')){
            global $Winter_MVC_wdk_membership;
            $Winter_MVC_wdk_membership->model('subscription_user_m');
            $Winter_MVC_wdk_membership->model('subscription_m');

            $this->db->select('*');
            $this->db->join($Winter_MVC_wdk_membership->subscription_m->_table_name.' ON '.$Winter_MVC_wdk_membership->subscription_m->_table_name.'.idsubscription = '.$Winter_MVC_wdk_membership->subscription_user_m->_table_name.'.subscription_id');
            $this->db->join($this->db->prefix.'wdk_categories ON '.$this->db->prefix.'wdk_categories.idcategory = '.$Winter_MVC_wdk_membership->subscription_m->_table_name.'.category_id', TRUE, 'LEFT');
            $this->db->join($this->db->prefix.'wdk_locations ON '.$this->db->prefix.'wdk_locations.idlocation = '.$Winter_MVC_wdk_membership->subscription_m->_table_name.'.location_id', TRUE, 'LEFT');
            $this->db->where(array(
                                '(date_expire   > \''.current_time( 'mysql' ).'\')'=>NULL,
                                '(user_id = \''.wmvc_show_data('user_id_editor', $this->data['db_data'], false).'\')'=>NULL,
                                '(status = \'ACTIVE\')'=>NULL,
                                )
                            );

            $subscriptions = $Winter_MVC_wdk_membership->subscription_user_m->get();
            if (count($subscriptions) > 0) {
                foreach($subscriptions as $subscription) {
                    $this->data['subscriptions'][wdk_show_data('subscription_id',$subscription,'', TRUE, TRUE)] = wdk_show_data('subscription_id', $subscription,'', TRUE, TRUE)
                                                                                                .', '.wdk_show_data('subscription_name', $subscription,'', TRUE, TRUE)
                                                                                                .', '.wdk_show_data('location_title',$subscription, esc_html__('Any', 'wpdirectorykit'), TRUE, TRUE).' '. esc_html__('Location', 'wpdirectorykit')
                                                                                                .', '.wdk_show_data('category_title',$subscription, esc_html__('Any', 'wpdirectorykit'), TRUE, TRUE).' '. esc_html__('Category', 'wpdirectorykit')
                                                                                                .'('.wdk_get_date(wdk_show_data('date_expire', $subscription,'', TRUE, TRUE)).')';
                }
            }
        }


        $this->db->where(array('field_type !='=> 'SECTION'));
        $this->data['listing_fields'] = $this->field_m->get();
        
        $this->data['fields'] = $this->field_m->get();
 
        $rules = array(
                array(
                    'field' => 'post_title',
                    'label' => __('Title', 'wpdirectorykit'),
                    'rules' => 'required'
                ),
                array(
                    'field' => 'post_content',
                    'label' => __('Content', 'wpdirectorykit'),
                    'rules' => (wdk_get_option('wdk_is_post_content_enable', FALSE)) ? 'required' : ''
                ),
                array(
                    'field' => 'category_id',
                    'label' => __('Category', 'wpdirectorykit'),
                    'rules' => (wdk_get_option('wdk_listing_category_required')) ? 'required':''
                ),
                array(
                    'field' => 'location_id',
                    'label' => __('Location', 'wpdirectorykit'),
                    'rules' => (wdk_get_option('wdk_listing_location_required')) ? 'required':''
                ),
                array(
                    'field' => 'address',
                    'label' => __('Address', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'lat',
                    'label' => __('lat', 'wpdirectorykit'),
                    'rules' => (wdk_get_option('wdk_is_address_enabled')) ? 'wdk_gps_single':''
                ),
                array(
                    'field' => 'lng',
                    'label' => __('lng', 'wpdirectorykit'),
                    'rules' => (wdk_get_option('wdk_is_address_enabled')) ? 'wdk_gps_single':''
                ),
                array(
                    'field' => 'listing_images',
                    'label' => __('Listing images', 'wpdirectorykit'),
                    'rules' => (wdk_get_option('wdk_listings_images_required_enable')) ? 'required' : ''
                ),
                array(
                    'field' => 'listing_plans_documents',
                    'label' => __('Listing plans and documents', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'is_featured',
                    'label' => __('Is Featured', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'is_activated',
                    'label' => __('Is Activated', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'is_approved',
                    'label' => __('Is Approved', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'user_id_editor',
                    'label' => __('Agents', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'listing_agents',
                    'label' => __('Agents', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'listing_sub_locations',
                    'label' => __('Locations', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'listing_sub_categories',
                    'label' => __('Categories', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'package_id',
                    'label' => __('Package', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'subscription_id',
                    'label' => __('Subscription id', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'listing_parent_post_id',
                    'label' => __('Listing parent id', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'listing_related_ids',
                    'label' => __('Listing childs', 'wpdirectorykit'),
                    'rules' => ''
                ),
        );

        $rules[] =  array(
                        'field' => 'rank',
                        'label' => __('Rank', 'wpdirectorykit'),
                        'rules' => 'numeric'
                    );
        

        foreach($this->data['fields'] as $key => $field)
        {
            if($field->field_type == 'SECTION'){
                $this->data['fields'][$key]->is_required = '';
                continue;
            }

            $rule_required = (wmvc_show_data('is_required', $field) == 1) ? 'required' : '';

            if(wmvc_show_data('validation', $field)) {
                if(!empty($rule_required)) {
                    $rule_required .= '|';
                }
                $rule_required .= wmvc_show_data('validation', $field);
            }

            if(!empty(wmvc_show_data('min_length', $field))) {
                if(!empty($rule_required)) {
                    $rule_required .= '|';
                }
                
                if(wmvc_show_data('field_type', $field) == "NUMBER") {
                    $rule_required .= "min_number";
                } else {
                    $rule_required .= "min_length";
                }
                $rule_required .= "[".wmvc_show_data('min_length', $field)."]";

            }

            if(!empty(wmvc_show_data('max_length', $field))) {
                if(!empty($rule_required)) {
                    $rule_required .= '|';
                }
                if(wmvc_show_data('field_type', $field) == "NUMBER") {
                    $rule_required .= "max_number";
                } else {
                    $rule_required .= "max_length";
                }
                $rule_required .= "[".wmvc_show_data('max_length', $field)."]";
            }

            /* clear rules, because hidden field */
            if(isset($_POST['category_id']) && !empty($_POST['category_id'])) {
                if(wdk_depend_is_hidden_field($field->idfield, intval($_POST['category_id']))) {
                    $rule_required = '';
                } 
            }

            $rules[] = 
                array(
                    'field' => 'field_'.$field->idfield,
                    'label' => $field->field_label,
                    'rules' => $rule_required
                );

            if(isset($this->data['db_data']['field_'.$field->idfield.'_'.$field->field_type]))
                $this->data['db_data']['field_'.$field->idfield] = 
                    $this->data['db_data']['field_'.$field->idfield.'_'.$field->field_type];
        }

        $this->form->add_error_message('wdk_gps_single', __('Gps field is not valid, should be like xx.xxxxxx (between -180 and 180)', 'wpdirectorykit'));
        if($this->form->run($rules))
        {
            // Check _wpnonce
            check_admin_referer( 'wdk-listing-edit_'.$listing_post_id, '_wpnonce' );

            $data = $this->listing_m->prepare_data(wdk_get_post(), $rules, FALSE);
            // Save standard wp post
            
            if(!isset($data['post_content'])) {
                $data['post_content'] = '';
            }
            
            // Create post object
            $listing_post = array(
                'ID' => $listing_post_id,
                'post_type'     => 'wdk-listing',
                'post_title'    => wp_strip_all_tags( $data['post_title'] ),
                'post_content'  => $data['post_content'],
                'post_status'   => 'publish',
                'post_author'   => get_current_user_id()
            );
            
            // Insert the post into the database
            $id = wp_insert_post( $listing_post );

            // Save our main listing data

            $listing_data = array('post_id' => $id);

            $listing_data_fields = array('category_id', 'location_id', 'address', 'lat', 'lng', 'listing_images','listing_plans_documents', 'is_featured', 'is_activated','is_approved', 'package_id','rank','subscription_id',
                                         'user_id_editor', 'listing_parent_post_id','listing_related_ids');
            foreach($listing_data_fields as $field_name)
            {
                $listing_data[$field_name] = $data[$field_name];
            }

            $image_ids = explode(',', $data['listing_images']);
        
            $listing_data['listing_images_path'] = '';
            $listing_data['listing_images_path_medium'] = '';

            if(is_array($image_ids)) {
                foreach ($image_ids as $image_id) {
                    if(is_numeric($image_id))
                    {
                        $image_path = wp_get_original_image_path( $image_id);
                        if($image_path) {
                            /* path of image */
                            $next_path = str_replace(WP_CONTENT_DIR . '/uploads/','', $image_path);

                            if(!empty($listing_data['listing_images_path']))
                                $listing_data['listing_images_path'] .= ',';

                            $listing_data['listing_images_path'] .= $next_path;
                        }

                        $image_url = wp_get_attachment_image_url($image_id, 'large');
                        if($image_url) {
                            $parsed = parse_url($image_url);
                            $next_path = substr($parsed['path'], strpos($parsed['path'], 'uploads/')+8);
            
                            if(!empty($listing_data['listing_images_path_medium']))
                                $listing_data['listing_images_path_medium'] .= ',';
        
                            $listing_data['listing_images_path_medium'] .= $next_path;
                        }
                    }
                }
            } 

            if((wmvc_user_in_role('administrator') || current_user_can('wdk_listings_manage')) && $this->input->post('slug')) {
                // update the post slug
                wp_update_post( array(
                    'ID' => $id,
                    'post_name' => sanitize_text_field($this->input->post('slug'))
                ));

            }

            if(!function_exists('run_wdk_membership')) {
                if($listing_data['is_activated'] == 1) {
                    $listing_data['is_approved'] = 1;
                } else {
                    $listing_data['is_approved'] = 0;
                }
            }
            
            if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')) {
                unset($listing_data['rank']);
            }

            if(function_exists('run_wdk_payments') && wmvc_show_data('package_id', $listing_data_field, false)) {
                
                global $Winter_MVC_wdk_payments;
                $Winter_MVC_wdk_payments->model('package_m');
                $package = $Winter_MVC_wdk_payments->package_m->get(wmvc_show_data('package_id', $listing_data_field, false), TRUE);
                if ($packages) {
                    if(!isset($listing_data['rank']) || empty($listing_data['rank']))
                        $listing_data['rank'] = wdk_show_data('featured_rank',$package, 0, TRUE, TRUE);
                    $listing_data['date_package_expire'] = date('Y-m-d H:i:s', strtotime('+'.wdk_show_data('days_limit',$package, 0, TRUE, TRUE).'days'));
                }
            }

            /* dates set */
            if(isset($this->data['db_data']['date']))
                $listing_data['date'] = $this->data['db_data']['date'];

            $listing_data['date_modified'] = date('Y-m-d H:i:s');

            if(empty($listing_db_data))
            {
                $id_ret = $this->listing_m->insert($listing_data, NULL);
            }
            else
            {
                $id_ret = $this->listing_m->insert($listing_data, $id);
            }

            if($this->db->last_error() != '')
                exit('DB Error: '.$this->db->last_error());

            //var_dump($id_ret);

            // insert users/agents

            if(function_exists('run_wdk_membership')){
                $this->listingusers_m->delete_where(array('post_id' => $id));
                if(is_array($data['listing_agents']))
                foreach($data['listing_agents'] as $val)
                {
                    $this->listingusers_m->insert(array('post_id' => $id, 'user_id' => $val), NULL);
                }
            }

            $data_other_categories = array();
            if(isset($data['listing_sub_categories']) || is_null($data['listing_sub_categories'])) {
                $this->categorieslistings_m->delete_where(array('post_id' => $id));
                if(is_array($data['listing_sub_categories']))
                foreach($data['listing_sub_categories'] as $val)
                {
                    $this->categorieslistings_m->insert(array('post_id' => $id, 'category_id' => $val), NULL);
                    
                    $data_other_categories []=  $val;
                }
            }

            $data_other_locations = array();
            if(isset($data['listing_sub_locations']) || is_null($data['listing_sub_locations'])) {
                $this->locationslistings_m->delete_where(array('post_id' => $id));
                if(is_array($data['listing_sub_locations']))
                foreach($data['listing_sub_locations'] as $val)
                {
                    $this->locationslistings_m->insert(array('post_id' => $id, 'location_id' => $val), NULL);
                    $data_other_locations []=  $val;
                }
            }

            $data_update = array(
                'categories_list'=> '',
                'locations_list'=>''
            );

            if(!empty($data_other_categories)) {
                $data_update['categories_list'] = ','.join(',',$data_other_categories).',';
            }

            if(!empty($data_other_locations)) {
                $data_update['locations_list'] = ','.join(',',$data_other_locations).',';
            }


            if(!empty($data_update))
                $this->listing_m->insert($data_update, $id);

            //exit($this->db->last_query());

            // Save dynamic fields data

            $data['post_id'] = $id;

            global $wpdb;
            foreach($this->data['fields'] as $key => $field)
            {
                if($field->field_type == 'TEXTAREA'){
                    $data[ 'field_'.$field->idfield ] = wp_encode_emoji( $data['field_'.$field->idfield] );
                }
            }

            if(empty($listingfield_db_data))
            {
                $this->listingfield_m->insert_custom_fields($this->data['listing_fields'], $data, NULL);
            }
            else
            {
                $this->listingfield_m->insert_custom_fields($this->data['listing_fields'], $data, $id);
            }

            $this->load->model('editlog_m');
            $this->editlog_m->insert(array(
                                            'user_id' => get_current_user_id(),
                                            'post_id' => $id,
                                            'date' => date('Y-m-d H:i:s'),
                                            'ip' => $_SERVER['REMOTE_ADDR']
                                        ));


            do_action('wpdirectorykit/listing/saved', $id, $this->data['db_data']);

            if(wdk_get_option('wdk_sub_listings_enable')) {
                /* generate fast childs ids for parents */

                if(isset($listing_data['listing_parent_post_id']) && !empty($listing_data['listing_parent_post_id'])) {

                    $this->db->select('post_id');
                    $this->db->order_by('sublisting_order,idlisting');
                    $this->db->from($this->listing_m->_table_name);
                    $this->db->where(array('listing_parent_post_id' => intval($listing_data['listing_parent_post_id'])));
                    $query = $this->db->get();

                    $listings_childs = '';
                    if ($this->db->num_rows() > 0)
                        foreach ($this->db->results() as $listing) {
                            $listings_childs .= $listing->post_id.',';
                        }

                    if(!empty($listings_childs))
                        $listings_childs = substr($listings_childs,0, -1);

                    $this->listing_m->insert(array('listing_related_ids' => $listings_childs), intval($listing_data['listing_parent_post_id']));
                }

                if(isset($listing_data['listing_related_ids']) && !empty($listing_data['listing_related_ids'])) {

                    $old_childs_ids = wmvc_show_data('listing_related_ids', $this->data['db_data'],'',TRUE, TRUE);
                    $order_index = 0;
                    $listings_childs = '';

                    $old_childs_ids = explode(',', $old_childs_ids);
                    $old_childs_ids = array_flip($old_childs_ids);
                    foreach (explode(',', $listing_data['listing_related_ids']) as $idlisting) {
                        $this->listing_m->insert(array('listing_parent_post_id' => $listing_post_id, 'sublisting_order'=>$order_index++), intval($idlisting));

                        if(isset($old_childs_ids[$idlisting])) {
                            unset($old_childs_ids[$idlisting]);
                        }
                    }

                    /* clear old related listings */
                    if(!empty($old_childs_ids)) {
                        foreach ($old_childs_ids as $idlisting => $v) {
                            $this->listing_m->insert(array('listing_parent_post_id' => NULL), intval($idlisting));
                        }
                    }
                }
            }

            // redirect
            if(empty($listing_post_id) && !empty($id))
            {
                wp_redirect(admin_url("admin.php?page=wdk_listing&id=$id&is_updated=true"));
                exit;
            }

            /* fix checkbox after submit */
            if(!empty($id))
            {
                $is_approved_before_save = (isset($this->data['db_data']['is_approved'])) ? $this->data['db_data']['is_approved'] : 0;

                $listing_post = get_post( $id );
                $listing_db_data = $this->listing_m->get($id, TRUE);
                $listingfield_db_data = $this->listingfield_m->get($id, TRUE);
                $listingusers_db_data = $this->listingusers_m->get($id);
                $this->data['db_data'] = array_merge((array) $listing_post, 
                                                     (array) $listing_db_data, 
                                                     (array) $listingfield_db_data);

                $this->data['db_data']['listing_agents'] = array();
                $this->data['db_data']['listing_sub_locations'] = array();
                $this->data['db_data']['listing_sub_categories'] = array();

                foreach ($listingusers_db_data as $key => $listinguser) {
                    if(isset($this->data['agents'][$listinguser->user_id]))
                        $this->data['db_data']['listing_agents'] [$listinguser->user_id]= $this->data['agents'][$listinguser->user_id];
                }

                if(isset($_POST['listing_sub_locations'])) {
                    $this->data['db_data']['listing_sub_locations'] = $_POST['listing_sub_locations'];
                    unset($_POST['listing_sub_locations']);
                }
                if(isset($_POST['listing_sub_categories'])) {
                    $this->data['db_data']['listing_sub_categories'] = $_POST['listing_sub_categories'];
                    unset($_POST['listing_sub_categories']);
                }

                /* if approved message */
                if (!empty($listing_post_id) && wmvc_show_data('user_id', $data, false)) {
                    if( $is_approved_before_save != 1 && wmvc_show_data('is_approved', $data) == 1 &&  wmvc_show_data('is_activated', $data) == 1) {

                        $user = get_userdata( wmvc_show_data('user_id', $data) );

                        $data_message = array();
                        $data_message['user'] = $user;
                        $data_message['post_id'] = $id;
                        $data_message['post'] = $this->data['db_data'];
                        $ret = wdk_mail($user->user_email, __('Your Listing Approved', 'wpdirectorykit'), $data_message, 'new_listing_approved');
                    }
                }
            }
            
        }

        $this->data['calendar_id'] = NULL;
        if(!empty($listing_post_id) && function_exists('run_wdk_bookings')) {
            global $Winter_MVC_wdk_bookings;
            $Winter_MVC_wdk_bookings->model('calendar_m');
            $calendar = $Winter_MVC_wdk_bookings->calendar_m->get_by(array('post_id'=>$listing_post_id), TRUE);
            if($calendar) {
                $this->data['calendar_id'] = $calendar->idcalendar;
            }
        }

        $this->load->view('wdk_listing/index', $this->data);
    }
    
}
