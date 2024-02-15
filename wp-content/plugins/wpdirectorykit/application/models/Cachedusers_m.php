<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Cachedusers_m extends Winter_MVC_Model {

	public $_table_name = 'wdk_users';
	public $_order_by = 'idusers';
    public $_primary_key = 'cacheduser_user_id';
    public $_own_columns = array();
    public $_timestamps = FALSE;
    protected $_primary_filter = 'intval';
    public $form_admin = array();
    public $fields_list = NULL;
    
	public function __construct(){
        parent::__construct();
	}

    /* [START] For dynamic data table */
    
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
    
    public function get_pagination($limit, $offset, $where = array(), $order_by = NULL)
    {
        $this->db->select('*');
        $this->db->from($this->_table_name);

        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->offset($offset);

        if(!empty($order_by)){
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by($this->_order_by);
        }

        $query = $this->db->get();

        if ($this->db->num_rows() > 0)
            return $this->db->results();
        
        return array();
    }
    
    public function check_deletable($id, $user_id=NULL)
    {
        return true;
    }
    
}
?>