<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Category_m extends Winter_MVC_Model {

	public $_table_name = 'wdk_categories';
	public $_order_by = 'order_index, idcategory';
    public $_primary_key = 'idcategory';
    public $_own_columns = array();
    public $_timestamps = TRUE;
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
        $this->load->model('listing_m');

        $this->db->select($this->_table_name.'.*, COUNT(DISTINCT '.$this->listing_m->_table_name.'.post_id) AS listings_counter, MAX('.$this->_table_name.'.level) as level');

        $this->db->join($this->_table_name.' AS category_table ON (CONCAT(",", category_table.parent_path, ",") LIKE CONCAT("%,", '.$this->_table_name.'.idcategory ,",%"))', TRUE, 'LEFT');
        $this->db->join($this->listing_m->_table_name.' ON (
            ('.$this->listing_m->_table_name.'.is_activated = 1 AND '.$this->listing_m->_table_name.'.is_approved = 1) AND
            ('.$this->listing_m->_table_name.'.category_id = '.$this->_table_name.'.idcategory
            OR '.$this->listing_m->_table_name.'.category_id = category_table.idcategory)

        )', TRUE, 'LEFT');

        $this->db->from($this->_table_name);

        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->group_by('idcategory');

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
    
    public function check_deletable($id)
    {
        return true;
    }
    
    
    /* [END] For dynamic data table */

    public function insert($data, $id = NULL)
    {
        if(!is_array($data))
        {
            echo 'Missing data for insert in model';
            return NULL;
        }

        if($this->_timestamps === TRUE && !isset($data['date']))
            $data['date'] = current_time( 'mysql' );

        $data['level_0_id'] = 0;
        $data['level'] = 0;
        $data['parent_path'] = '';

        if(!empty($data['parent_id']))
        {
            $parent = $this->get($data['parent_id']);

            if(isset($parent[0]))
            {
                $data['level'] = $parent[0]->level + 1;
                $data['parent_path'] = $parent[0]->parent_path.(!empty($parent[0]->parent_path)?',':'').$parent[0]->idcategory;

                if(strpos($data['parent_path'], ',') !== FALSE) {
                    $data['level_0_id'] = substr($data['parent_path'],0,strpos($data['parent_path'], ','));
                } else {
                    $data['level_0_id'] = $data['parent_path'];
                }
            }
        }

        if(empty($data['order_index']))
        {
            $data['order_index'] = $this->max_order($data['parent_id'])+1;
        }

        if(!empty($id))
        {
            return $this->db->update($this->_table_name, $data, $id, $this->_primary_key);
        }

        return $this->db->insert($this->_table_name, $data);
    }

    public function get_parents($id = NULL, $level_show=true)
    {
        $this->db->select('*');

        if(!empty($id))
        {
            $this->db->where(array('idcategory !=' => $id));
            $this->db->where(array('parent_id !=' => $id));
        }

        $query = $this->get();

        //$list_with_keys = array(0 => __('Root', 'wpdirectorykit'));
        $list_with_keys = array();
        if ($this->db->num_rows() > 0)
        {
            $results = $this->db->results();

            $results = $this->parent_ordered_list($results);

            foreach($results as $result)
            {
                $level_gen = str_pad('', $result->level*12, '&nbsp;');
            
                $level_gen.='|-';

                if(!$level_show)
                    $level_gen = '';

                $list_with_keys[$result->idcategory] = $level_gen.$result->category_title;
            }

            
        }

        //wmvc_dump($list_with_keys);

        return $list_with_keys;
    }

    public function get_tree_table()
    {
        $this->db->select('*');
        $query = $this->get();

        $results = array();
        if ($this->db->num_rows() > 0)
        {
            $results = $this->db->results();

            $results = $this->parent_ordered_list($results);
        }

        return $results;
    }

    public function parent_ordered_list(&$results)
    {
        $tree_list = array();
        
        foreach($results as $result)
        {
            $tree_list[$result->parent_id][$result->idcategory] = $result;
        }

        $ordered_list = array();

        $this->_parent_ordered_list(0, $tree_list, $ordered_list);

        return $ordered_list;
    }

    // recursive function because of tree structure and ordering
    private function _parent_ordered_list($parent_id , &$tree_list, &$ordered_list, $level=-1)
    {
        $level++;

        if(isset($tree_list[$parent_id]))
        foreach($tree_list[$parent_id] as $tree_id => $tree_item)
        {
            $tree_item->level = $level;
            $ordered_list[$tree_id] = $tree_item;

            $this->_parent_ordered_list($tree_item->idcategory , $tree_list, $ordered_list, $level);
        }
    }

    public function get_data($category_id = NULL) {
        static $data = array();
        if(empty($data)){
            $query = $this->get();
            if ($this->db->num_rows() > 0) {
                foreach($this->db->results() as $category) {
                    $data[$category->idcategory] = $category;
                }
            }
        }
        
        if(!empty($category_id))
            if(isset($data[(int)$category_id])){
               return $data[(int)$category_id];
            } else {
                return NULL;
            }

        return $data;

    }
    
    public function delete($id)
    {
        $filter = $this->_primary_filter;
        $id = $filter($id); 
        
        if(!$id)
        {
            return FALSE;
        }

        $item = $this->get($id, TRUE);

        if(is_object($item))
        {
            // update all childs parent_id
            $query = 'UPDATE '.$this->_table_name.' SET parent_id = '.$item->parent_id;
            $query.= ' , `level` = '.$item->level; 
            $query.= ' , `order_index` = '.$item->order_index; 
            $query.= ' WHERE parent_id ='.$id.';';    
            $this->db->query($query);
        }

        $this->db->where($this->_primary_key, $id);
        $this->db->limit(1);
        $this->db->delete($this->_table_name);
    }

    /* only admin can edit */
    public function is_related($item_id, $user_id, $method = 'edit')
    {
        return false;
    }

    public function get_all_childs($id)
    {
        $childs = array();

        // Fetch pages without parents
        $this->db->select('idcategory');
        $this->db->where(array('(CONCAT(",", parent_path, ",") LIKE CONCAT("%,", '.esc_sql($id).', ",%"))' => NULL));

        $this->db->from($this->_table_name);
        $categories = $this->get();
        if(count($categories))
        {
            foreach($categories as $category)
            {
                $childs[] = $category->idcategory;
            }
        }
        return $childs;
    }

    public function get_all_childs_deprecated($id)
    {
        $categories = array();
        $childs = array();
        // Fetch pages without parents
        $this->db->select('idcategory, level, parent_id');
        $this->db->where(array('(parent_id = '.esc_sql($id).')' => NULL));

        $this->db->from($this->_table_name);
        $categories = $this->get();
        if(count($categories))
        {
            foreach($categories as $category)
            {
                $childs[] = $category->idcategory;
                $this->_get_all_childs_recursive($category->idcategory, $childs);
            }
        }
        return $childs;
    }
    
    private function _get_all_childs_recursive($parent_id, &$childs = array())
    {
        $categories = array();
        // Fetch pages without parents
        $this->db->select('idcategory, level, parent_id');
        $this->db->where(array('(parent_id = '.esc_sql($parent_id).')' => NULL));

        $this->db->from($this->_table_name);
        $categories = $this->get();
        if(count($categories))
        {
            foreach($categories as $category)
            {
                $childs[] = $category->idcategory;
                $this->_get_all_childs_recursive($category->idcategory, $childs);
            }
        }
    }
            
    public function get_max_level()
    {
        $this->db->select('MAX(`level`) as `level`');
        $this->db->from($this->_table_name);
        $row = $this->get(NULL, TRUE);
        if(!empty($row))
        {
            return (wmvc_show_data('level', $row, 0, TRUE, TRUE) + 1);
        }
        
        return 1;
    }
}
?>