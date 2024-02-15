<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_index extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        $this->load->model('field_m');
        $this->load->model('listing_m');
        $this->load->model('listingfield_m');
        $this->load->model('location_m');
        $this->load->model('category_m');
        $this->load->load_helper('listing');

        /* [Table Actions Bulk Form] */
        $table_action = $this->input->post_get('table_action');
        $action = $this->input->post_get('action');
        $posts_selected = $this->input->post_get('post');

        if(!empty($table_action))
        {
            switch ($action) {
                case 'delete':
                    $this->bulk_delete($posts_selected);
                  break;
                case 'deactivate':
                    $this->bulk_deactivate($posts_selected);
                  break;
                case 'activate':
                    $this->bulk_activate($posts_selected);
                  break;
                case 'deapprove':
                    $this->bulk_deapprove($posts_selected);
                  break;
                case 'approve':
                    $this->bulk_approve($posts_selected);
                  break;
                default:
              } 
        }

        /* [Search Form] */

        $dbusers =  get_users( array( 'search' => '',
                                                'orderby' => 'display_name', 'order' => 'ASC'));

        foreach($dbusers as $dbuser) {
            $this->data['users'][wmvc_show_data('ID', $dbuser)] = '#'.wmvc_show_data('ID', $dbuser).', '.wmvc_show_data('display_name', $dbuser);
        }

        $this->data['fields_list'] = $this->field_m->get();

		$fields_data = $this->field_m->get();
        $this->data['fields_list'] = array();

        foreach($fields_data as $field)
        {
            if(wmvc_show_data('field_type', $field) == 'SECTION') {
            } else {
                $this->data['fields_list']['field_'.wmvc_show_data('idfield', $field)] = '#'.wmvc_show_data('idfield', $field).' '.wmvc_show_data('field_label', $field).'['.wmvc_show_data('field_type', $field).']';
            }
        }

        $controller = 'listing';
        $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by', 'address','user_id_editor', 'is_featured','is_activated','is_approved');
        $external_columns = array('location_id', 'category_id', 'post_title', 'user_id_editor',);

        $this->data['categories'] = $this->category_m->get_parents();
        $this->data['locations']  = $this->location_m->get_parents();
        $this->data['order_by']   = array('ID DESC' => __('ID', 'wpdirectorykit').' DESC', 
                                          'ID ASC' => __('ID', 'wpdirectorykit').' ASC', 
                                          'post_title ASC' => __('Post Title', 'wpdirectorykit').' ASC',
                                          'post_title DESC' => __('Post Title', 'wpdirectorykit').' DESC',
                                          'post_date ASC' => __('Post Date', 'wpdirectorykit').' ASC',
                                          'post_date DESC' => __('Post Date', 'wpdirectorykit').' DESC',
                                          'date_modified ASC' => __('Last Modified Date', 'wpdirectorykit').' ASC',
                                          'date_modified DESC' => __('Last Modified Date', 'wpdirectorykit').' DESC',
                                        );

        $rules = array(
                array(
                    'field' => 'location_id',
                    'label' => __('Location', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'category_id',
                    'label' => __('Category', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'search',
                    'label' => __('Search tag', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'order_by',
                    'label' => __('Order By', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'user_id_editor',
                    'label' => __('User', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'is_activated',
                    'label' => __('Is Activated', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'is_featured',
                    'label' => __('Is Featured', 'wpdirectorykit'),
                    'rules' => ''
                ),
        );

        $custom_parameters = array();
        foreach ($this->input->get() as $key => $value) {
            if(stripos($key, 'c_field_') !== FALSE && stripos($key, '_field',8) !== FALSE) {
                $field_id = substr($key, 8, (stripos($key, '_',8) - 8) );
                if(!empty($_GET['c_field_'.$field_id.'_field']) 
                    && !empty($_GET['c_field_'.$field_id.'_like']) 
                    && !empty($_GET['c_field_'.$field_id.'_value'])) {
                    switch ($_GET['c_field_'.$field_id.'_like']) {
                        case '>': $custom_parameters[sanitize_key($_GET['c_field_'.$field_id.'_field']).'_min'] = sanitize_text_field($_GET['c_field_'.$field_id.'_value']);
                                  break;
                        case '<': $custom_parameters[sanitize_key($_GET['c_field_'.$field_id.'_field']).'_max'] = sanitize_text_field($_GET['c_field_'.$field_id.'_value']);
                                  break;
                        case '==': $custom_parameters[sanitize_key($_GET['c_field_'.$field_id.'_field'])] = sanitize_text_field($_GET['c_field_'.$field_id.'_value']);
                                  break;
                    }
                }
            }
        };

        $this->data['db_data'] = $this->listing_m->prepare_data($this->input->get(), $rules);
        /* [/Search Form] */

        $where = array();
        if(isset($_GET['inactive']) && $_GET['inactive'] == 'on') {
            $where['(is_activated IS NULL OR is_approved IS NULL)'] = NULL;
        }

        wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters);
        $total_items = $this->listing_m->total($where);

        $current_page = 1;

        if(isset($_GET['paged']))
            $current_page = intval($_GET['paged']);

        $this->data['paged'] = $current_page;


        $per_page = 20;
        $offset = $per_page*($current_page-1);

        $this->data['pagination_output'] = '';

        if(function_exists('wmvc_wp_paginate'))
            $this->data['pagination_output'] = wmvc_wp_paginate($total_items, $per_page);

        wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters);
        $this->data['listings'] = $this->listing_m->get_pagination($per_page, $offset, $where);

        // Load view
        $this->load->view('wdk/index', $this->data);
    }

    public function delete()
    {
        $this->load->model('listing_m');

        $post_id = (int) $this->input->post_get('id');
        $paged = (int) $this->input->post_get('paged');

        // Check _wpnonce
        check_admin_referer( 'wdk-listing-delete_'.$post_id, '_wpnonce' );

        $this->listing_m->delete($post_id);

        do_action('wpdirectorykit/listing/removed');

        wp_redirect(admin_url("admin.php?page=wdk&paged=$paged"));
    }

    public function bulk_delete($posts_selected)
    {
        $this->load->model('listing_m');
        
        // Check _wpnonce
        check_admin_referer( 'wdk-listing-bulk', '_wpnonce');

        foreach($posts_selected as $key=>$post_id)
        {
            $this->listing_m->delete($post_id);
        }

        do_action('wpdirectorykit/listing/bulk_removed');
        wp_redirect(admin_url("admin.php?page=wdk"));
    }
    
    public function bulk_deactivate($posts_selected)
    {
        // Check _wpnonce
        check_admin_referer( 'wdk-listing-bulk', '_wpnonce');

        $this->load->model('listing_m');
        foreach($posts_selected as $key=>$post_id)
        {
            if($this->listing_m->check_deletable($post_id))
                $this->listing_m->insert(array('is_activated'=>NULL), $post_id);
        }
        do_action('wpdirectorykit/listing/updated');
        wp_redirect(admin_url("admin.php?page=wdk"));
    }

    public function bulk_activate($posts_selected)
    {
        // Check _wpnonce
        check_admin_referer( 'wdk-listing-bulk', '_wpnonce');

        $this->load->model('listing_m');
        foreach($posts_selected as $key=>$post_id)
        {
            if($this->listing_m->check_deletable($post_id))
                $this->listing_m->insert(array('is_activated'=>1), $post_id);
        }
        do_action('wpdirectorykit/listing/updated');
        wp_redirect(admin_url("admin.php?page=wdk"));
    }
    public function bulk_deapprove($posts_selected)
    {

        // Check _wpnonce
        check_admin_referer( 'wdk-listing-bulk', '_wpnonce');

        $this->load->model('listing_m');
        foreach($posts_selected as $key=>$post_id)
        {
            if($this->listing_m->check_deletable($post_id))
                $this->listing_m->insert(array('is_approved'=>NULL), $post_id);
        }
        do_action('wpdirectorykit/listing/updated');
        wp_redirect(admin_url("admin.php?page=wdk"));
    }

    public function bulk_approve($posts_selected)
    {
        // Check _wpnonce
        check_admin_referer( 'wdk-listing-bulk', '_wpnonce');

        $this->load->model('listing_m');
        foreach($posts_selected as $key=>$post_id)
        {
            if($this->listing_m->check_deletable($post_id))
                $this->listing_m->insert(array('is_approved'=>1), $post_id);
        }
        do_action('wpdirectorykit/listing/updated');
        wp_redirect(admin_url("admin.php?page=wdk"));
    }
}
