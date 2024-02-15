<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Editlog_m extends Winter_MVC_Model {

	public $_table_name = 'wdk_editlog';
	public $_order_by = 'ideditlog';
    public $_primary_key = 'ideditlog';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';
    public $form_admin = array();
    public $fields_list = NULL;
    
	public function __construct(){
        parent::__construct();

        $this->fields_list = array(
            array(
                'field' => 'post_id',
                'field_label' => __('Listing Id', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'INPUTBOX', 
                'rules' => 'required'
            ),
            array(
                'field' => 'user_id',
                'field_label' => __('User Id', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'INPUTBOX', 
                'rules' => 'required'
            ),
            array(
                'field' => 'date',
                'field_label' => __('Date', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'DATETIME', 
                'rules' => 'trim'
            ),
            array(
                'field' => 'ip',
                'field_label' => __('IP', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'INPUTBOX', 
                'rules' => 'trim'
            ),
            array(
                'field' => 'comment',
                'field_label' => __('Comment', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'INPUTBOX', 
                'rules' => 'trim'
            ),
        );

        foreach($this->fields_list as $key=>$field)
        {
            $this->fields_list[$key]['label'] = $field['field_label'];
        }

	}
   
    public function get_available_fields()
    {      
        $fields = $this->db->list_fields($this->_table_name);

        return $fields;
    }
    
    public function total($where = array())
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
    
    public function get_pagination_listing($listing_id, $limit = NULL, $offset = 0, $where = array(), $order_by = NULL, $custom_select = NULL)
    {
        if(!empty($custom_select)) {
            $this->db->select($custom_select);
        } else {
            $this->db->select('*');
        }
        
        global $wpdb;
        $this->db->join($wpdb->users.' ON '.$this->_table_name.'.user_id = '.$wpdb->users.'.ID', NULL, 'LEFT');

        $this->db->from($this->_table_name);

        $this->db->where($where);
        $this->db->where('post_id', $listing_id);

        if(!empty($limit)) {
            $this->db->limit($limit);
            if(empty($offset)) $offset = 0;
            $this->db->offset($offset);
        }

        if(!empty($order_by)){
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by($this->_order_by);
        }
        
        $query = $this->get();

        if ($this->db->num_rows() > 0)
            return $this->db->results();
        
        return array();
    }
    
    public function get_pagination($limit, $offset = 0, $where = array(), $order_by = NULL, $custom_select = NULL)
    {
        if(!empty($custom_select)) {
            $this->db->select($custom_select);
        } else {
            $this->db->select('*');
        }
        
        $this->db->where($where);

        global $wpdb;
        $this->db->join($wpdb->users.' ON '.$this->_table_name.'.user_id = '.$wpdb->users.'.ID', NULL, 'LEFT');

        $this->db->from($this->_table_name);

        $this->db->where($where);

        if(!empty($limit)) {
            $this->db->limit($limit);
            if(empty($offset)) $offset = 0;
            $this->db->offset($offset);
        }

        if(!empty($order_by)){
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by($this->_order_by);
        }
        
        $query = $this->get();

        if ($this->db->num_rows() > 0)
            return $this->db->results();
        
        return array();
    }
    
    public function check_deletable($id)
    {
        if(wmvc_user_in_role('administrator') || current_user_can('wdk_listings_manage')) return true;
    }

    public function delete($id) {

        if(!$this->check_deletable($id)) return false;

        parent::delete($id);

        return true;
    }

    public function is_related($item_id, $user_id, $method = 'edit')
    {	 
        if(wmvc_user_in_role('administrator') || current_user_can('wdk_listings_manage')) return true;
    }

    
    public function delete_where($where)
    {
        $this->db->where($where);
        $this->db->delete($this->_table_name);
    }

}
?>