<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Sessions_m extends Winter_MVC_Model {

	public $_table_name = 'wal_log';
	public $_order_by = 'idlog DESC';
    public $_primary_key = 'idlog';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';

    public $form_admin = array();

    public $fields_list = null;
    
	public function __construct(){
        parent::__construct();
    }
    
    public function get_all_sessions($parameters = array())
    {
        $all_sessions = array();

        $search_tag = '';
        if(!empty($parameters['searck_tag']))
        {
            $search_tag = $parameters['searck_tag'];
        }

        $users = get_users();
        // Array of WP_User objects.
        $count=0;
        foreach ( $users as $user ) {
            $sessions = WP_Session_Tokens::get_instance( $user->ID );
            $user_sessions = $sessions->get_all();
            foreach($user_sessions as $key_s => $val_s)
            {
                if(!empty($search_tag))
                {
                    if(strpos($user->ID, $search_tag) === FALSE && strpos($user->display_name, $search_tag) === FALSE && strpos($val_s['ip'], $search_tag) === FALSE)
                        continue;
                }

                $all_sessions[] = array('idsessions'=>$user->ID.'_'.$key_s, 'user'=>'#'.$user->ID.', '.$user->display_name, 
                                        'login'=>$val_s['login'], 'expiration' => $val_s['expiration'], 'ip' => $val_s['ip'], 'user_id' => $user->ID);
            }

            $count++;
        }

        return $all_sessions;
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