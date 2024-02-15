<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Messages_m extends Winter_MVC_Model {

	public $_table_name = 'wdk_messages';
	public $_order_by = 'idmessage DESC';
    public $_primary_key = 'idmessage';
    public $_own_columns = array();
    public $_timestamps = TRUE;
    protected $_primary_filter = 'intval';
    public $form_admin = array();
    public $fields_list_dash = NULL;
    public $fields_list_dash_view = NULL;
    public $fields_list = NULL;
    public $current_user_id = NULL;
    
	public function __construct(){
        parent::__construct();
        $this->current_user_id = get_current_user_id();
                
        $this->fields_list_dash = array(
            array(
                'field' => 'post_id',
                'field_label' => __('Listing id', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'LISTING_READONLY', 
                'rules' => ''
            ),
            array(
                'field' => 'date',
                'field_label' => __('Date', 'wpdirectorykit'),
                'field_type' => 'DATE_READONLY', 
                'rules' => ''
            ),
            array(
                'field' => 'message',
                'field_label' => __('Message', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'TEXTAREA_READONLY', 
                'rules' => ''
            ),
            array(
                'field' => 'is_readed',
                'field_label' => __('Is Readed', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'CHECKBOX_READONLY', 
                'rules' => ''
            ),
            array(
                'field' => 'json_object',
                'field_label' => __('Full Mail Data', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'JSON_READONLY', 
                'rules' => ''
            ),
        );

        foreach($this->fields_list_dash as $key=>$field)
        {
            $this->fields_list_dash[$key]['label'] = $field['field_label'];
        }

        $this->fields_list_dash_view = array(
            array(
                'field' => 'post_id',
                'field_label' => __('Listing id', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'LISTING_READONLY', 
                'rules' => ''
            ),
            array(
                'field' => 'date',
                'field_label' => __('Date', 'wpdirectorykit'),
                'field_type' => 'DATE_READONLY', 
                'rules' => ''
            ),
            array(
                'field' => 'message',
                'field_label' => __('Message', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'TEXTAREA_READONLY', 
                'rules' => ''
            ),
            array(
                'field' => 'is_readed',
                'field_label' => __('Is Readed', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'CHECKBOX_READONLY', 
                'rules' => ''
            ),
            array(
                'field' => 'json_object',
                'field_label' => __('Full Mail Data', 'wpdirectorykit'),
                'hint' => '', 
                'field_type' => 'JSON_READONLY', 
                'rules' => ''
            ),
        );

        foreach($this->fields_list_dash_view as $key=>$field)
        {
            $this->fields_list_dash_view[$key]['label'] = $field['field_label'];
        }
	}

    /* [START] For dynamic data table */
    
    public function get_available_fields()
    {      
        $fields = $this->db->list_fields($this->_table_name);

        return $fields;
    }
    
    public function total($where = array(), $user_check = FALSE, $user_id=NULL)
    {
        $this->db->select('COUNT(*) as total_count');
        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->order_by($this->_order_by);

        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->_table_name.'.post_id', NULL, 'LEFT');
        if( $user_check || (!is_null($user_id) && !empty($user_id) ) )
        {
            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->where($this->db->prefix.'wdk_listings`.`user_id_editor', $user_id);
            }
            elseif($this->current_user_id) 
            {
                $this->db->where($this->db->prefix.'wdk_listings`.`user_id_editor', $this->current_user_id);
            }

        } 

        $query = $this->db->get();

        $res = $this->db->results();

        if(isset($res[0]->total_count))
            return $res[0]->total_count;

        return 0;
    }
        
    /*
        $chat_unread_count set TRUE only if activated Live Chat Addon
    */
    public function get_pagination($limit, $offset, $where = array(), $order_by = NULL, $user_check = FALSE, $user_id=NULL, $chat_unread_count = FALSE)
    {
        $post_table = $this->db->prefix.'posts';

        if($chat_unread_count) {
            $this->db->select($this->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->_table_name.'.date AS message_date,'.$post_table.'.post_title, 
                              COUNT('.$this->db->prefix.'wdk_messages_chat.related_key) AS chat_unread_counter');

            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->join($this->db->prefix.'wdk_messages_chat ON ('.$this->_table_name.'.idmessage = '.$this->db->prefix.'wdk_messages_chat.related_key AND chat_is_readed IS NULL AND `outgoing_msg_user_id`!= '.$user_id.')', NULL, 'LEFT');
            }
            elseif($this->current_user_id) 
            {
                $this->db->join($this->db->prefix.'wdk_messages_chat ON ('.$this->_table_name.'.idmessage = '.$this->db->prefix.'wdk_messages_chat.related_key AND chat_is_readed IS NULL AND `outgoing_msg_user_id`!= '.$this->current_user_id.')', NULL, 'LEFT');
            }
            $this->db->group_by('idmessage');
        } else {
            $this->db->select($this->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->_table_name.'.date AS message_date,'.$post_table.'.post_title');
        }

        if( $user_check || (!is_null($user_id) && !empty($user_id) ) )
        {
            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->where($this->db->prefix.'wdk_listings`.`user_id_editor', $user_id);
            }
            elseif($this->current_user_id) 
            {
                $this->db->where($this->db->prefix.'wdk_listings`.`user_id_editor', $this->current_user_id);
            }
        } 

        $this->db->group_by('idmessage');

        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->offset($offset);

        if(!empty($order_by)){
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by($this->_order_by);
        }

        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->_table_name.'.post_id', NULL, 'LEFT');
        $this->db->join($post_table.' ON '.$this->_table_name.'.post_id = '.$post_table.'.ID', NULL, 'LEFT');

        $query = $this->db->get();
        
        if ($this->db->num_rows() > 0)
            return $this->db->results();
        
        return array();
    }
    
    public function total_merge($where = array(), $user_check = FALSE, $user_id=NULL)
    {
        $this->db->select('COUNT(*) as total_count');
        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->order_by($this->_order_by);

        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->_table_name.'.post_id', NULL, 'LEFT');

        if( $user_check || (!is_null($user_id) && !empty($user_id) ) )
        {
            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->where('(`'.$this->db->prefix.'wdk_listings`.`user_id_editor` = '.$user_id.' OR user_id_sender = '.$user_id.'  OR user_id_receiver = '.$user_id.' )');
            }
            elseif($this->current_user_id) 
            {
                $this->db->where('(`'.$this->db->prefix.'wdk_listings`.`user_id_editor` = '. $this->current_user_id.' OR user_id_sender = '. $this->current_user_id.'  OR user_id_receiver = '. $this->current_user_id.' )');
            }
        } 

        $query = $this->db->get();

        $res = $this->db->results();

        if(isset($res[0]->total_count))
            return $res[0]->total_count;

        return 0;
    }


    /*
    $chat_unread_count set TRUE only if activated Live Chat Addon
    */
    
    public function get_pagination_merge($limit, $offset, $where = array(), $order_by = NULL, $user_check = FALSE, $user_id=NULL, $chat_unread_count = FALSE)
    {
        $post_table = $this->db->prefix.'posts';

        if($chat_unread_count) {
            $this->db->select($this->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->_table_name.'.date AS message_date,'.$post_table.'.post_title, 
                              COUNT('.$this->db->prefix.'wdk_messages_chat.related_key) AS chat_unread_counter');

            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->join($this->db->prefix.'wdk_messages_chat ON ('.$this->_table_name.'.idmessage = '.$this->db->prefix.'wdk_messages_chat.related_key AND chat_is_readed IS NULL AND `outgoing_msg_user_id`!= '.$user_id.')', NULL, 'LEFT');
            }
            elseif($this->current_user_id) 
            {
                $this->db->join($this->db->prefix.'wdk_messages_chat ON ('.$this->_table_name.'.idmessage = '.$this->db->prefix.'wdk_messages_chat.related_key AND chat_is_readed IS NULL AND `outgoing_msg_user_id`!= '.$this->current_user_id.')', NULL, 'LEFT');
            }
            $this->db->group_by('idmessage');
        } else {
            $this->db->select($this->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->_table_name.'.date AS message_date,'.$post_table.'.post_title');
        }

        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->_table_name.'.post_id', NULL, 'LEFT');
        $this->db->join($post_table.' ON '.$this->_table_name.'.post_id = '.$post_table.'.ID', NULL, 'LEFT');

        if( $user_check || (!is_null($user_id) && !empty($user_id) ) )
        {
            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->where('(`'.$this->db->prefix.'wdk_listings`.`user_id_editor` = '.$user_id.' OR user_id_sender = '.$user_id.'  OR user_id_receiver = '.$user_id.' )');
            }
            elseif($this->current_user_id) 
            {
                $this->db->where('(`'.$this->db->prefix.'wdk_listings`.`user_id_editor` = '. $this->current_user_id.' OR user_id_sender = '. $this->current_user_id.'  OR user_id_receiver = '. $this->current_user_id.' )');
            }
        } 

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
    
    public function total_outbox($where = array(), $user_check = FALSE, $user_id=NULL)
    {
        $this->db->select('COUNT(*) as total_count');
        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->order_by($this->_order_by);

        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->_table_name.'.post_id', NULL, 'LEFT');
        if( $user_check || (!is_null($user_id) && !empty($user_id) ) )
        {
            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->where('user_id_sender', $user_id);
            }
            elseif($this->current_user_id) 
            {
                $this->db->where('user_id_sender', $this->current_user_id);
            }

        } 

        $query = $this->db->get();

        $res = $this->db->results();

        if(isset($res[0]->total_count))
            return $res[0]->total_count;

        return 0;
    }
    
    /*
        $chat_unread_count set TRUE only if activated Live Chat Addon
    */
    public function get_pagination_outbox($limit, $offset, $where = array(), $order_by = NULL, $user_check = FALSE, $user_id=NULL, $chat_unread_count = FALSE)
    {
        $post_table = $this->db->prefix.'posts';
        if($chat_unread_count) {
            $this->db->select($this->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->_table_name.'.date AS message_date,'.$post_table.'.post_title, 
                              COUNT('.$this->db->prefix.'wdk_messages_chat.related_key) AS chat_unread_counter');
            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->join($this->db->prefix.'wdk_messages_chat ON ('.$this->_table_name.'.idmessage = '.$this->db->prefix.'wdk_messages_chat.related_key AND chat_is_readed IS NULL AND `outgoing_msg_user_id`!= '.$user_id.')', NULL, 'LEFT');
            }
            elseif($this->current_user_id) 
            {
                $this->db->join($this->db->prefix.'wdk_messages_chat ON ('.$this->_table_name.'.idmessage = '.$this->db->prefix.'wdk_messages_chat.related_key AND chat_is_readed IS NULL AND `outgoing_msg_user_id`!= '.$this->current_user_id.')', NULL, 'LEFT');
            }
            $this->db->group_by('idmessage');
        } else {
            $this->db->select($this->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->_table_name.'.date AS message_date,'.$post_table.'.post_title');
        }

        $this->db->from($this->_table_name);
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->offset($offset);

        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->_table_name.'.post_id', NULL, 'LEFT');
        $this->db->join($post_table.' ON '.$this->_table_name.'.post_id = '.$post_table.'.ID', NULL, 'LEFT');

        if( $user_check || (!is_null($user_id) && !empty($user_id) ) )
        {
            if(!is_null($user_id) && !empty($user_id))
            {
                $this->db->where('user_id_sender', $user_id);
            }
            elseif($this->current_user_id) 
            {
                $this->db->where('user_id_sender', $this->current_user_id);
            }

        } 

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
    
    public function check_deletable($item_id, $user_id=NULL)
    {

        if(empty($user_id))
            $user_id = get_current_user_id();
      
        $this->db->select($this->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->_table_name.'.date AS message_date');
        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->_table_name.'.post_id', NULL, 'LEFT');
        $this->db->from($this->_table_name);
        $row = $this->get($item_id, TRUE);

        if(wmvc_show_data('user_id_receiver', $row) == $user_id || wmvc_show_data('user_id_editor', $row) == $user_id )
            return true;

        if(wmvc_user_in_role('administrator') || current_user_can('wdk_listings_manage')) return true;

        return false;
    }
    
    
    /* [END] For dynamic data table */

    public function is_related($item_id, $user_id, $method = 'edit')
    {	 
        $this->db->select($this->_table_name.'.*,'.$this->db->prefix.'wdk_listings.*, '.$this->_table_name.'.date AS message_date');
        $this->db->join($this->db->prefix.'wdk_listings ON '.$this->db->prefix.'wdk_listings.post_id = '.$this->_table_name.'.post_id', NULL, 'LEFT');
        $this->db->from($this->_table_name);
        $row = $this->get($item_id, TRUE);

        if($method == 'view') {
            if(wmvc_show_data('user_id_receiver', $row) == $user_id || wmvc_show_data('user_id_editor', $row) == $user_id || wmvc_show_data('user_id_sender', $row) == $user_id )
                return true;
        } else {
            if(wmvc_show_data('user_id_receiver', $row) == $user_id || wmvc_show_data('user_id_editor', $row) == $user_id )
                return true;
        }

        if(wmvc_user_in_role('administrator') || current_user_can('wdk_listings_manage')) return true;
            
        return false;
    }

    public function do_notified_by_user($user_id = NULL)
    {
        if(empty($user_id))
            $user_id = get_current_user_id();

        global $wpdb;
        if($user_id){
            $wpdb->query("UPDATE `{$this->_table_name}` 
                            LEFT JOIN `{$this->db->prefix}wdk_listings` ON `{$this->db->prefix}wdk_listings`.post_id = `{$this->_table_name}`.post_id  
                            SET `is_notified` = 1 
                            WHERE 
                            (`user_id_editor` = {$user_id}
                            OR `user_id_sender` = {$user_id})
                        ");
            return true;
        }

        return false;
    }

    public function delete($id, $user_id=NULL) {

        if($user_id === NULL)
            $user_id = get_current_user_id();

        if(!$this->check_deletable($id, $user_id)) return false;
       
        /* remove */
        parent::delete($id);

        return true;
    }

}

?>