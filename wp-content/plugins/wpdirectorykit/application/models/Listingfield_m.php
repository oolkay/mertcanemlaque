<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Listingfield_m extends Winter_MVC_Model {

	public $_table_name = 'wdk_listings_fields';
	public $_order_by = 'post_id DESC';
    public $_primary_key = 'post_id';
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

    public function insert_custom_fields($fields, $data, $post_id)
    {
        $prepared_data = array('post_id' => $data['post_id']);

        foreach($fields as $field)
        {
            if($field->field_type == 'SECTION')continue;

            $column = 'field_'.$field->idfield.'_'.$field->field_type;

            $prepared_data[$column] = NULL;

            if(!empty($data['field_'.$field->idfield]))
            {
                $prepared_data[$column] = wp_kses_post($data['field_'.$field->idfield]);
            }
        }

        if(empty($post_id)) // then insert
        {
            return $this->insert($prepared_data, NULL);
        }
        else // else update by post_id
        {
            return $this->insert($prepared_data, $post_id);
        }
    }

    // Create table column for new added or edited fields
    public function create_table_column($field_data, $field_id)
    {
        global $wpdb;

        $existing_fields = $this->get_available_fields();

        $table = $wpdb->prefix . 'wdk_listings_fields';
        $column_name = 'field_'.$field_id.'_'.$field_data['field_type'];

        if($field_data['field_type'] == 'INPUTBOX' || $field_data['field_type'] == 'DROPDOWN')
        {
            $sql = "ALTER TABLE `{$table}`
                ADD `$column_name` TEXT NULL DEFAULT NULL;";
        }
        else if($field_data['field_type'] == 'DROPDOWNMULTIPLE')
        {
            $sql = "ALTER TABLE `{$table}`
                ADD `$column_name` TEXT NULL DEFAULT NULL;";
        }
        else if($field_data['field_type'] == 'TEXTAREA')
        {
            $sql = "ALTER TABLE `{$table}`
                ADD `$column_name` TEXT NULL DEFAULT NULL;";
        }
        else if($field_data['field_type'] == 'TEXTAREA_WYSIWYG')
        {
            $sql = "ALTER TABLE `{$table}`
                ADD `$column_name` TEXT NULL DEFAULT NULL;";
        }
        else if($field_data['field_type'] == 'NUMBER')
        {
            $sql = "ALTER TABLE `{$table}`
                ADD `$column_name` DECIMAL(16,2) NULL DEFAULT NULL;";
        }
        else if($field_data['field_type'] == 'DATE')
        {
            $sql = "ALTER TABLE `{$table}`
                ADD `$column_name` DATETIME NULL DEFAULT NULL;";
        }
        else if($field_data['field_type'] == 'CHECKBOX')
        {
            $sql = "ALTER TABLE `{$table}`
                ADD `$column_name` BOOLEAN NULL DEFAULT NULL;";
        } else {
            /* if type not exactly */
            return false;
        }

        if(!isset($existing_fields[$column_name]))
            $query_result = $wpdb->query( $sql );
    }

}
?>