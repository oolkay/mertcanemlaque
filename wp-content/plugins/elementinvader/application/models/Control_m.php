<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Control_m extends Winter_MVC_Model {

	public $_table_name = 'wal_control';
	public $_order_by = 'idcontrol DESC';
    public $_primary_key = 'idcontrol';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';

    public $form_admin = array();

    public $fields_list = null;
    
	public function __construct(){
        parent::__construct();
 
        $this->form_admin = array(
            'listing_id' => array('field'=>'listing_id', 'label'=>__('Listing', 'elementinvader'), 'design'=>'dropdown_listing', 'rules'=>'trim|callback__calendar_exists|required')
        );
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

    public function save_rules($id, $data)
    {
        $data = wmvc_xss_clean_array($data);
        $id = wmvc_xss_clean($id);

        // first remove existing
        $this->db->where('control_id', $id);
        $this->db->delete($this->db->prefix.'wal_control_rule');

        foreach($data as $key=>$post_val)
        {
            if( substr($key, 0, strlen('control_type_')) != 'control_type_' )continue;

            $i_fieldnum = substr($key, -1, 1);

            if(!is_numeric($i_fieldnum))continue;

            if(empty($data['control_type_'.$i_fieldnum]))continue;

            $data_one = array(
                'control_id' => $id,
                'type'       => $data['control_type_'.$i_fieldnum],
                'operator'   => $data['control_operator_'.$i_fieldnum],
                'parameter'  => $data['control_parameter_'.$i_fieldnum],
                'value'      => $data['control_value_'.$i_fieldnum]
            );

            $this->db->insert($this->db->prefix.'wal_control_rule', $data_one);
        }
    }

    public function get_rules($id)
    {
        $id = wmvc_xss_clean($id);

        $this->db->select('*');
        $this->db->from($this->db->prefix.'wal_control_rule');
        $this->db->where('control_id', $id);
        $this->db->order_by('idcontrol_rule ASC');

        $query = $this->db->get();

        if ($this->db->num_rows() > 0)
            return $this->db->results();
    
        return array();
    }

    public function delete($id)
    {
        // first remove existing rules
        $this->db->where('control_id', $id);
        $this->db->delete($this->db->prefix.'wal_control_rule');

        $filter = $this->_primary_filter;
        $id = $filter($id); 
        
        if(!$id)
        {
            return FALSE;
        }
        
        $this->db->where($this->_primary_key, $id);
        $this->db->limit(1);
        $this->db->delete($this->_table_name);
    }

}













?>