<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Categorieslistings_m extends Winter_MVC_Model {

	public $_table_name = 'wdk_listings_categories';
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

    public function delete_where($where)
    {
        $this->db->where($where);
        $this->db->delete($this->_table_name);
    }

    public function get_categories($post_id = NULL, $single = FALSE)
    {
        $this->load->model('category_m');
        $categories_table = $this->category_m->_table_name;

        $this->db->from($this->_table_name);
        $this->db->join($categories_table.' ON '.$categories_table.'.idcategory = '.$this->_table_name.'.category_id');

        return parent::get($post_id, $single);
    }
    

}
?>