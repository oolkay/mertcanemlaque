<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_messages extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        $this->load->model('messages_m');
        $this->data['form'] = &$this->form;

        $dbusers =  get_users( array( 'search' => '',
                                      'orderby' => 'display_name', 'order' => 'ASC'));
        $users = array();
        foreach($dbusers as $dbuser) {
            $this->data['users'][wmvc_show_data('ID', $dbuser)] = '#'.wmvc_show_data('ID', $dbuser).', '.wmvc_show_data('display_name', $dbuser);
        }

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
                default:
            } 
        }

        /* [Search Form] */
        
        $controller = 'messages';
        $columns = array('idmessage','user_id_editor','display_name','user_login', 'user_email', 'search', 'order_by');
        $external_columns = array('user_id_editor','display_name','user_login', 'user_email');

        $this->data['order_by']   = array('idmessage DESC' => __('ID DESC', 'wpdirectorykit'), 
                                        'idmessage ASC' => __('ID ASC', 'wpdirectorykit'),  
                                        'display_name DESC' => __('User DESC', 'wpdirectorykit'),  
                                        'display_name ASC' => __('User ASC', 'wpdirectorykit'),  
                                        );
  
        $rules = array(
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
        );

        $this->data['db_data'] = $this->messages_m->prepare_data($this->input->get(), $rules);
       
        global $wpdb;
        $wp_usermeta_table = $wpdb->users;

        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->messages_m->_table_name.'.post_id', NULL, 'LEFT');
        $this->db->join($wp_usermeta_table.' ON '.$this->db->prefix.'wdk_listings.user_id_editor = '.$wp_usermeta_table.'.ID', NULL, 'LEFT');
        wdk_messages_prepare_search_query_GET($columns, $controller.'_m', $external_columns);
        $total_items = $this->messages_m->total();

        $current_page = 1;
        if(isset($_GET['paged']))
            $current_page = intval($_GET['paged']);

        $this->data['paged'] = $current_page;

        $per_page = 20;
        $offset = $per_page*($current_page-1);

        $this->data['pagination_output'] = '';

        if(function_exists('wmvc_wp_paginate'))
            $this->data['pagination_output'] = wmvc_wp_paginate($total_items, $per_page);

        wdk_messages_prepare_search_query_GET($columns, $controller.'_m', $external_columns);

        $post_table = $this->db->prefix.'posts';
        $this->db->select($this->messages_m->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->messages_m->_table_name.'.date AS message_date,'.$post_table.'.post_title,'.$wp_usermeta_table.'.display_name,'.$wp_usermeta_table.'.user_login,'
                            .$wp_usermeta_table.'.user_email');
        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->messages_m->_table_name.'.post_id', NULL, 'LEFT');
        $this->db->join($wp_usermeta_table.' ON '.$this->db->prefix.'wdk_listings.user_id_editor = '.$wp_usermeta_table.'.ID', NULL, 'LEFT');
        $this->data['messages'] = $this->messages_m->get_pagination($per_page, $offset);

        // Load view
        $this->load->view('wdk_messages/index', $this->data);
    }

    public function delete()
    {
        $id = (int) $this->input->post_get('id');
        $paged = (int) $this->input->post_get('paged');
        
        // Check _wpnonce
        check_admin_referer( 'wdk-messages-delete_'.$id, '_wpnonce' );

        $this->load->model('messages_m');
        wdk_access_check('messages_m', $id);
        $this->messages_m->delete($id);

        wp_redirect(admin_url("admin.php?page=wdk_messages&paged=$paged"));
    }

    public function bulk_delete($posts_selected)
    {
        // Check _wpnonce
        check_admin_referer( 'wdk-messages-bulk', '_wpnonce');

        $this->load->model('messages_m');

        foreach($posts_selected as $id)
        {
            wdk_access_check('messages_m', $id);
            $this->messages_m->delete($id);
        }

        wp_redirect(admin_url("admin.php?page=wdk_messages&is_updated=true&custom_message=".urlencode(esc_html__('Selected Messages removed', 'wpdirectorykit'))));
        exit();
    }
    
	public function edit()
	{
        $this->load->model('messages_m');

        $id = (int) $this->input->post_get('id');
        wdk_access_check('messages_m', $id);
        $this->data['db_data'] = NULL;

        $this->data['form'] = &$this->form;

        //exit($this->db->last_query());

        $rules = array(
                array(
                    'field' => 'post_id',
                    'label' => __('Listing Id', 'wpdirectorykit'),
                    'rules' => 'required'
                ),
                array(
                    'field' => 'date',
                    'label' => __('Date', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'message',
                    'label' => __('Message', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'is_readed',
                    'label' => __('I read it', 'wpdirectorykit'),
                    'rules' => ''
                ),
        );

        if($this->form->run($rules))
        {

            // Check _wpnonce
            check_admin_referer( 'wdk-messages-edit_'.$id, '_wpnonce' );

            // Save procedure for basic data
            $data = $this->messages_m->prepare_data(wdk_get_post(), $rules);

            // Save standard wp post

            $insert_id = $this->messages_m->insert($data, $id);

            //exit($this->db->last_error());

            // redirect
            if(!empty($insert_id) && empty($id))
            {
                wp_redirect(admin_url("admin.php?page=wdk_messages&id=$insert_id&is_updated=true"));
                exit;
            }
                
        }

        if(!empty($id))
        {
            $this->data['db_data'] = $this->messages_m->get($id, TRUE);
        }

        $this->load->view('wdk_messages/edit', $this->data);
    }
    
}
