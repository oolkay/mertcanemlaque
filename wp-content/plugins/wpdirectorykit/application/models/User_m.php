<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class User_m extends Winter_MVC_Model {

	public $_table_name = '';
    public $_table_name_meta = '';
	public $_order_by = 'user_nicename';
    public $_primary_key = 'ID';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';
    public $form_admin = array();
    public $fields_list = NULL;
    
	public function __construct(){
        parent::__construct();

        global $wpdb;

        $this->_table_name = $wpdb->users;
        $this->_table_name_meta = $wpdb->usermeta;
	}

    public function get_agents_names($where = array(), $like = "meta_value LIKE '%wdk_agent%'")
    {
        $wp_usermeta_table = $this->_table_name_meta;

        $this->db->select('*');
        $this->db->from($this->_table_name);
        $this->db->join($wp_usermeta_table.' ON '.$this->_table_name.'.ID = '.$wp_usermeta_table.'.user_id');
        $this->db->where('meta_key', $this->db->prefix.'capabilities');
        $this->db->where($where);
        $this->db->like($like);
        
        $query = $this->get();

        $list_with_keys = array();
        if ($this->db->num_rows() > 0)
        {
            $results = $this->db->results();

            foreach($results as $result)
            {
                $list_with_keys[$result->user_id] = '#'.$result->ID.', '.$result->display_name;
            }
        }

        return $list_with_keys;
    }

    public function get_agents($where = array(), $like = "meta_value LIKE '%wdk_agent%'")
    {
        $wp_usermeta_table = $this->_table_name_meta;

        $this->db->select('*');
        $this->db->from($this->_table_name);
        $this->db->join($wp_usermeta_table.' ON '.$this->_table_name.'.ID = '.$wp_usermeta_table.'.user_id');
        $this->db->where('meta_key', $this->db->prefix.'capabilities');
        $this->db->where($where);
        $this->db->like($like);
        
        /*
        if(!empty($or_like) && is_array($or_like)){
            foreach($or_like as $key=>$value)
            {
                if(empty($value)) continue;
                $this->db->or_like('meta_value', "%$value%");
            }
        }*/

        $query = $this->get();

        //$list_with_keys = array(0 => __('Root', 'wpdirectorykit'));
        $list_with_keys = array();
        if ($this->db->num_rows() > 0)
        {
            $results = $this->db->results();

            foreach($results as $result)
            {
                $list_with_keys[$result->user_id] = $result->user_email.', '.$result->display_name;
            }
        }
        //wmvc_dump($list_with_keys);

        return $list_with_keys;
    }

    public function get_pagination($limit = NULL, $offset = NULL, $where = array(), $order_by = NULL, $like = "meta_value LIKE '%wdk_agent%'", $show_other_agents_litings = FALSE, $only_listings_assigned = FALSE, $count_agency_listings_false = TRUE)
    {
        $wp_usermeta_table = $this->_table_name_meta;
        $this->load->model('listing_m');
        $this->load->model('cachedusers_m');
        
        $alt_agents_table = $this->db->prefix.'wdk_listings_users';
        $select_list = 'ID,user_login,user_nicename,user_email,display_name,user_status,user_url, COUNT('.$this->db->prefix.'wdk_listings.user_id_editor) AS listings_counter';

        if(defined( 'WDK_EXTENSIONS_CACHED_USERS_ACTIVATED' ) && WDK_EXTENSIONS_CACHED_USERS_ACTIVATED ) {
            $select_list .=','.$this->cachedusers_m->_table_name.'.*';
        }

        if(function_exists('run_wdk_membership') && file_exists(WDK_MEMBERSHIP_PATH.'application/models/Agency_agent_m.php') && $count_agency_listings_false ) {
            $select_list .=', SUM('.$this->db->prefix.'wdk_membership_agency_agent.listings_count) AS agency_listings_counter';
        }

        $this->db->select($select_list);
        $this->db->from($this->_table_name);

        $this->db->join($wp_usermeta_table.' ON '.$this->_table_name.'.ID = '.$wp_usermeta_table.'.user_id');
        $this->db->join($this->listing_m->_table_name.' ON ('.$this->listing_m->_table_name.'.user_id_editor = '.$this->_table_name.'.ID AND ('.$this->listing_m->_table_name.'.is_activated = 1 AND '.$this->listing_m->_table_name.'.is_approved = 1))', TRUE, 'LEFT');

        $this->db->join($alt_agents_table.' ON '.$alt_agents_table.'.user_id = '.$this->_table_name.'.ID', TRUE, 'LEFT');


        if(function_exists('run_wdk_membership') && file_exists(WDK_MEMBERSHIP_PATH.'application/models/Agency_agent_m.php') && $count_agency_listings_false) {
            $this->db->join($this->db->prefix.'wdk_membership_agency_agent ON ('.$this->db->prefix.'wdk_membership_agency_agent.agency_id = '.$this->_table_name.'.ID  AND '.$this->db->prefix.'wdk_membership_agency_agent.status = "CONFIRMED")', TRUE, 'LEFT');
        }

        if(defined( 'WDK_EXTENSIONS_CACHED_USERS_ACTIVATED' ) && WDK_EXTENSIONS_CACHED_USERS_ACTIVATED ) {
            $this->db->join($this->cachedusers_m->_table_name.' ON '.$this->cachedusers_m->_table_name.'.cacheduser_user_id = '.$this->_table_name.'.ID', TRUE, 'LEFT');
        }

        $this->db->where($where);
        $this->db->where('meta_key', $this->db->prefix.'capabilities');


        if($only_listings_assigned) {
            $this->db->where(array($this->db->prefix.'wdk_listings.user_id_editor != 0'=>NULL));
        }

        if(!empty($like))
            $this->db->like($like);

        $this->db->group_by('ID');

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
    
    public function total($where = array(), $like = "meta_value LIKE '%wdk_agent%'", $only_listings_assigned = FALSE)
    {
        $wp_usermeta_table = $this->_table_name_meta;
        $this->load->model('listing_m');
        $this->db->select('COUNT(DISTINCT ID) as total_count, COUNT('.$this->db->prefix.'wdk_listings.user_id_editor) AS listings_counter');
        $this->db->from($this->_table_name);

        $this->db->join($wp_usermeta_table.' ON '.$this->_table_name.'.ID = '.$wp_usermeta_table.'.user_id');
        $this->db->join($this->listing_m->_table_name.' ON ('.$this->listing_m->_table_name.'.user_id_editor = '.$this->_table_name.'.ID AND ('.$this->listing_m->_table_name.'.is_activated = 1 AND '.$this->listing_m->_table_name.'.is_approved = 1) )', TRUE, 'LEFT');

        $this->db->where($where);
        $this->db->where('meta_key', $this->db->prefix.'capabilities');

        if($only_listings_assigned) {
            $this->db->where(array($this->db->prefix.'wdk_listings.user_id_editor != 0'=>NULL));
        }

        if(!empty($like))
            $this->db->like($like);

        $this->db->where($where);
        $this->db->order_by($this->_order_by);
        $query = $this->db->get();

        $res = $this->db->results();

        if(isset($res[0]->total_count))
            return $res[0]->total_count;

        return 0;
    }
    
    public function get_available_fields()
    {      
        $fields = $this->db->list_fields($this->_table_name);

        return $fields;
    }

    public function remove_user_data($user_id = NULL) {
        if(empty($user_id)) return false;

        /* listings */
        $this->_remove_data_from('this', 'listing_m', 'user_id_editor', 'post_id', $user_id);

        /* favorites */
        $this->_remove_data_from('Winter_MVC_wdk_favorites', 'favorite_m', 'user_id', 'idfavorite', $user_id);

        /* reservation */
        $this->_remove_data_from('Winter_MVC_wdk_bookings', 'reservation_m', 'user_id', 'idreservation', $user_id);

        /* membership subscription user */
        $this->_remove_data_from('Winter_MVC_wdk_membership', 'subscription_user_m', 'user_id', 'idsubscription_user', $user_id);

        /* reviews */
        $this->_remove_data_from('Winter_MVC_wdk_reviews', 'reviews_m', 'user_id', 'idreviews', $user_id);

        /* save search */
        $this->_remove_data_from('Winter_MVC_wdk_save_search', 'save_search_m', 'user_id', 'idsave_search', $user_id);

        /* listing claim */
        $this->_remove_data_from('Winter_MVC_wdk_listing_claim', 'listing_claim_m', 'user_id', 'idlistingclaim', $user_id);

        /* report abuse */
        $this->_remove_data_from('Winter_MVC_wdk_report_abuse', 'report_abuse_m', 'user_id', 'idreportabuse', $user_id);

        /* messages */
        $this->_remove_data_from('this', 'messages_m', 'user_id_sender', 'idmessage', $user_id);

        return TRUE;
    }


    private function _remove_data_from($addon_object_name, $model_name, $user_column, $row_id, $user_id) {

        if($addon_object_name != 'this')
            global ${$addon_object_name};

        if($addon_object_name == 'this' || isset(${$addon_object_name})) {
            if($addon_object_name != 'this') {
                ${$addon_object_name}->model($model_name);
            } else {
                ${$addon_object_name}->load->model($model_name);
            }

            $this->db->select('*');
            $this->db->where($user_column, $user_id);
            $this->db->from(${$addon_object_name}->{$model_name}->_table_name);
            $query = $this->db->get();
            if ($this->db->num_rows() > 0){
                foreach ($this->db->results() as $row) {
                    ${$addon_object_name}->{$model_name}->delete($row->{$row_id}, $user_id);
                }
            }

        }
    }

}
?>