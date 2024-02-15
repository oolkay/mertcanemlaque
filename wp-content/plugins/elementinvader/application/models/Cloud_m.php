<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Cloud_m extends Winter_MVC_Model {

	public $_table_name = 'wal_cloud';
	public $_order_by = 'idcloud DESC';
    public $_primary_key = 'idcloud';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';

    public $form_admin = array();

    public $fields_list = null;
    
	public function __construct(){
        parent::__construct();
 
        $this->form_admin = array();
	}

    /* [START] For dinamic data table */
    
    public function get_available_fields()
    {      
        $fields = $this->db->list_fields($this->_table_name);

        return $fields;
    }
    
    public function total_lang($where = array())
    {
        $this->db->select('*');
        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->order_by($this->_order_by);
        
        $query = $this->db->get();

        return $this->db->num_rows();
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
    
    
    /* [END] For dinamic data table */

}

?>