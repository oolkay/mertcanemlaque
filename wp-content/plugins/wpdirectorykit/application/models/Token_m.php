<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Token_m extends Winter_MVC_Model {

	public $_table_name = 'wdk_token';
	public $_order_by = 'idtoken';
    public $_primary_key = 'token';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = '';
    public $form_admin = array();
    public $fields_list = NULL;
    
	public function __construct(){
        parent::__construct();
	}

    /* only admin can edit */
    public function is_related($item_id, $user_id, $method = 'edit')
    {
        return false;
    }
}
?>