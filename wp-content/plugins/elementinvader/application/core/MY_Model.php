<?php
class MY_Model extends CI_Model {
    
    protected $_table_name = '';
    protected $_primary_key = '';
    protected $_primary_filter = 'intval';
    protected $_order_by = '';
    public $rules = array();
    protected $_timestamps = FALSE;
    protected $_cache_temp = array();
    public $_own_columns = array();
    
    protected $users_table = 'users';
    
	public function __construct(){
		parent::__construct();
        
        if(defined('CUSTOM_USER_TABLE'))
            $this->users_table = '`'.CUSTOM_USER_TABLE.'`';
	}
    
    public function get_table_name()
    {
        return $this->_table_name;
    }
    
    public function get_id($obj)
    {
        if(isset($obj->{$this->_primary_key}))
        {
            return $obj->{$this->_primary_key};
        }
        
        return NULL;
    }
    
    public function array_from_post($fields, $xss_clean = TRUE)
    {
        $data = array();
        foreach($fields as $field)
        {
            $data[$field] = $this->input->post_get($field,$xss_clean);
            
            if(is_string($data[$field])){
                $data[$field] = str_replace( "'",'&#039;', $data[$field] );
                $data[$field] = str_replace( "\\",'&#92;', $data[$field] );
            }
            
            if($data[$field] == '')
                $data[$field] = NULL;
        }
        return $data;
    }

    public function array_from_custom($custom, $fields)
    {
        $data = array();
        foreach($fields as $field)
        {
            $data[$field] = NULL;

            if(isset($custom[$field]))
            {
                $data[$field] = $custom[$field];
                
                if(is_string($data[$field])){
                    $data[$field] = str_replace( '"','&quot;', $data[$field] );
                    $data[$field] = str_replace( "'",'&#039;', $data[$field] );
                    $data[$field] = str_replace( "\\",'&#92;', $data[$field] );
                }
                
                if($data[$field] == '')
                    $data[$field] = NULL;
            }

        }
        return $data;
    }
    
    public function array_from_rules($rules)
    {
        return $this->array_from_post($this->get_post_from_rules($rules));
    }
    
    public function get_post_from_rules($rules)
    {
        $post_fields = array();
        
        foreach($rules as $key=>$value)
        {
            $post_fields[] = $key;
        }
        
        return $post_fields;
    }
    
    public function get_lang_post_fields($custom_rules = 'rules_lang')
    {
        $post_fields = array();
        
        if(isset($this->$custom_rules)){
            foreach($this->$custom_rules as $key=>$value)
            {
                $post_fields[] = $key;
            }
        }
        
        return $post_fields;
    }
    
    public function get($id = NULL, $single = FALSE)
    {
        exit('test');
        if($id != NULL)
        {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->where($this->_primary_key, $id);
            $method = 'row';
        }

        //wmvc_dump($id);

        if($id==NULL)return NULL;
        
        if($single == TRUE)
        {
            $method = 'row';
        }
        else
        {
            $method = 'result';
        }

        $this->db->order_by($this->_order_by);
        
        $query = $this->db->get($this->_table_name);
        $result = $query->$method();

        return $result;
    }
    
    public function get_form_dropdown($column, $where = FALSE, $show_empty=TRUE)
    {
        $filter = $this->_primary_filter;
        
        if(!empty($this->_order_by))
        {
            $this->db->order_by($this->_order_by);
        }
        
        if($where)
            $this->db->where($where); 
        
        $dbdata = $this->db->get($this->_table_name)->result_array();
        
        $results = array();
        if($show_empty)
            $results[''] = '';
            
        foreach($dbdata as $key=>$row){
            if(isset($row[$column]))
            $results[$row[$this->_primary_key]] = $row[$column];
        }
        return $results;
    }
    
    public function get_by($where, $single = FALSE)
    {
        $this->db->where($where);       
        return $this->get(NULL, $single);
    }
    
    public function save($data, $id = NULL)
    {
        // Set timestamps
        if($this->_timestamps == TRUE)
        {
            $now = date('Y-m-d H:i:s');
            if(empty($id))$data['date_submit'] = $now;
            $data['date_modified'] = $now;
        }

        // Insert
        if($id === NULL)
        {
            !isset($data[$this->_primary_key]) || $data[$this->_primary_key] = NULL;
            $this->db->set($data);
            $this->db->insert($this->_table_name);
            $id = $this->db->insert_id();
        }
        // Update
        else
        {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->set($data);
            $this->db->where($this->_primary_key, $id);
            $this->db->update($this->_table_name);
        }
        
        if(empty($id))
        {
            echo $this->db->last_query();
            exit();
        }
        
        return $id;
    }
    
    public function delete($id)
    {
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
    
    public function max_order($parent_id = null)
    {
        //$this->db->select('MAX(`order`) as `order`', FALSE);
        
        if($parent_id == null)
        {
            // get max order
            $this->db->select('MAX(`order`) as `order`', FALSE);
        }
        else
        {
            // get max order
            $this->db->select('MAX(`order`) as `order`', FALSE);
            $this->db->where('parent_id', $parent_id);
            $this->db->or_where($this->_primary_key, $parent_id);
        }

        $query = $this->db->get($this->_table_name);
        
        if(is_object($query) && $query->num_rows() > 0)
        {
            $row = $query->row();
        }
        else
        {
            echo 'SQL problem in get max_order:';
            echo $this->db->last_query();
            exit();
        }
        
        return (int) $row->order + 1;
    }
    
    protected function cache_temp_load($var_name)
    {
        if(isset($this->_cache_temp[$var_name]))
        {
            return $this->_cache_temp[$var_name];
        }
        else
        {
            return FALSE;
        }
    }
    
    protected function cache_temp_save(&$var, $var_name)
    {
        $this->_cache_temp[$var_name] = $var;
    }
    
    /* [START] For dinamic data table */
    
    public function get_pagination($limit, $offset)
    {
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->_table_name);

        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }
    
    public function total($where = array())
    {
        $query = $this->db->get_where($this->_table_name, $where);
        return $query->num_rows();
    }
    
    /* [END] For dinamic data table */
    
    public function is_related($object_id, $user_id, $method='edit')
    {
        if(sw_count($this->_own_columns) > 0)
        {
            $obj = $this->get($object_id, TRUE);
            
            foreach($this->_own_columns as $column)
            {
                if(!array_key_exists($column, $obj))
                    return false;
                    
                if($obj->$column == $user_id)
                    return true;
            }
        }
        
        return false;
    }
    
}