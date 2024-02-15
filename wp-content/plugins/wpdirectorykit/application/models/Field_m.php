<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Field_m extends Winter_MVC_Model {

	public $_table_name = 'wdk_fields';
	public $_order_by = 'order_index,idfield';
    public $_primary_key = 'idfield';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';
    public $form_admin = array();
    public $fields_list = NULL;

    public $fields_validations = array();
    
	public function __construct(){
        $this->fields_validations = array(
            'is_numerical'=> __('Numerical', 'wpdirectorykit'),
            'is_phone'=> __('Phone', 'wpdirectorykit'),
            'is_email'=> __('Email', 'wpdirectorykit'),
        );

        parent::__construct();
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
        if(wmvc_user_in_role('administrator') || current_user_can('wdk_listings_manage')) return true;

        return false;
    }
    
    
    /* [END] For dynamic data table */

    public function get_fields_data($field_id = NULL) {
        static $fields_data = array();
        if(empty($fields_data)){
            $query = $this->get();
            if ($this->db->num_rows() > 0) {
                foreach($this->db->results() as $field) {
                    $fields_data[$field->idfield] = $field;
                }
            }
        }
        
        if(!empty($field_id))
            if(isset($fields_data[(int)$field_id])){
               return $fields_data[(int)$field_id];
            } else {
                return NULL;
            }

        return $fields_data;

    }

    public function get_sections() {
        static $sections = array();
        /* hard section, for uncategory */
       
        foreach ($this->field_m->get() as $field) {
            if(wmvc_show_data('field_type',$field) == 'SECTION'){
                $sections[wmvc_show_data('idfield',$field)] = wmvc_show_data('field_label',$field);
            } 
        }

        return $sections;
    }

    public function get_fields_section() {
        static $fields_section = array();

        $section_id = '0';
        /* hard section, for uncategory */
        $this->data['fields_sections'][$section_id] = array(
            "idfield" => $section_id,
            "field_type" => "SECTION",
            "is_locked" =>0,
            "is_table_visible" =>0,
            "is_visible_frontend" => 1,
            "is_visible_dashboard" => 1,
            "is_hardlocked" => 0,
            "is_required" => 0,
            "is_price_format" => 0,
            "columns_number" => "12",
            "field_label" => "Unsection",
            "prefix" =>  '',
            "suffix" => '',
            "values_list" => '',
            "placeholder" => '',
            "hint" =>''
        );
       
        foreach ($this->field_m->get() as $field) {
            if(wmvc_show_data('field_type',$field) == 'SECTION'){
                $section_id = wmvc_show_data('idfield', $field);
                $fields_section[$section_id] = array(
                    "idfield" => $section_id,
                    "field_type" => wmvc_show_data('field_type',$field),
                    "is_locked" =>wmvc_show_data('is_locked',$field),
                    "is_table_visible" =>wmvc_show_data('is_table_visible',$field),
                    "is_visible_frontend" => wmvc_show_data('is_visible_frontend',$field),
                    "is_visible_dashboard" => wmvc_show_data('is_visible_dashboard',$field),
                    "is_hardlocked" => wmvc_show_data('is_hardlocked',$field),
                    "is_required" => wmvc_show_data('is_required',$field),
                    "is_price_format" => wmvc_show_data('is_required',$field),
                    "columns_number" => wmvc_show_data('columns_number',$field),
                    "field_label" => wmvc_show_data('field_label',$field),
                    "prefix" =>  wmvc_show_data('prefix',$field),
                    "suffix" => wmvc_show_data('suffix',$field),
                    "values_list" => wmvc_show_data('values_list',$field),
                    "placeholder" => wmvc_show_data('placeholder',$field),
                    "hint" =>wmvc_show_data('hint',$field),
                    "fields" => array()
                );
            } else {
                $fields_section[$section_id]["fields"][] = $field;
            }
        }
        return $fields_section;
    }

    /* only admin can edit */
    public function is_related($item_id, $user_id, $method = 'edit')
    {
        return false;
    }
}
?>