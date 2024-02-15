<?php

namespace Wdk\Extensions;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define( 'WDK_EXTENSIONS_CACHED_USERS_ACTIVATED', '1' );

class WdkCachedUsers
{
    /**
     * data array
     *
     * @var array
     */
    public $data = array();
    public $option_key = 'wdk_users_cached';
    
    public function __construct($data = array(), $args = null)
    {

        add_action( 'edit_user_profile_update', array($this, 'update'), 11);
        add_action( 'init', array($this, 'regenerate_cache_activation'), 11);

        /* call with GET['wdk_cached_users_regenerate'] */
        add_action( 'init', array($this, 'regenerate_cache_regenerate'), 11);

    }

    public function update($user_id = NULL)
    {
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('cachedusers_m');
        $Winter_MVC_WDK->load_helper('listing');

        $user_data = wdk_get_user_data($user_id);

        $Winter_MVC_WDK->db->where('cacheduser_user_id', $user_id);
        $Winter_MVC_WDK->db->delete($Winter_MVC_WDK->cachedusers_m->_table_name);

        $user_all_meta = get_user_meta($user_id);
        $usermetadata = array_map( function( $a ){ return $a[0]; }, $user_all_meta );
        $json_data = $usermetadata;

        $meta_fields = array('wdk_address','wdk_phone','wdk_city','wdk_company_name','wdk_facebook','wdk_youtube','wdk_linkedin','wdk_twitter','wdk_instagram','wdk_whatsapp',
                            'wdk_viber','wdk_iban','wdk_telegram','wdk_position_title','session_tokens','wc_last_active','show_welcome_panel','dismissed_wp_pointers','locale','show_admin_bar_front','use_ssl','admin_color',
                            'comment_shortcuts','syntax_highlighting','rich_editing','description','community-events-location','_woocommerce_persistent_cart_1','_woocommerce_tracks_anon_id',
                            $Winter_MVC_WDK->db->prefix.'capabilities',$Winter_MVC_WDK->db->prefix.'user_level',$Winter_MVC_WDK->db->prefix.'user-settings',$Winter_MVC_WDK->db->prefix.'user-settings-time',$Winter_MVC_WDK->db->prefix.'dashboard_quick_press_last_post_id');

        /* remove from json fields from above array */
        foreach ($meta_fields as $key_meta_field) {
            if(isset($json_data[$key_meta_field]))
                unset($json_data[$key_meta_field]);
        }

        $insert_id = $Winter_MVC_WDK->cachedusers_m->insert(array(
            'cacheduser_user_id' => $user_id,
            'cacheduser_profile_url' => $user_data['profile_url'],
            'cacheduser_avatar_url' => $user_data['avatar'],
            'cacheduser_display_name' =>  wdk_get_user_field ($user_id, 'display_name'),
            'cacheduser_email' =>  wdk_get_user_field ($user_id, 'user_email'),
            'cacheduser_wdk_address' =>  wdk_get_user_field ($user_id, 'wdk_address'),
            'cacheduser_wdk_phone' =>  wdk_get_user_field ($user_id, 'wdk_phone'),
            'cacheduser_wdk_city' =>  wdk_get_user_field ($user_id, 'wdk_city'),
            'cacheduser_wdk_company_name' =>  wdk_get_user_field ($user_id, 'wdk_company_name'),
            'cacheduser_wdk_facebook' =>  wdk_get_user_field ($user_id, 'wdk_facebook'),
            'cacheduser_wdk_youtube' =>  wdk_get_user_field ($user_id, 'wdk_youtube'),
            'cacheduser_wdk_linkedin' =>  wdk_get_user_field ($user_id, 'wdk_linkedin'),
            'cacheduser_wdk_twitter' =>  wdk_get_user_field ($user_id, 'wdk_twitter'),
            'cacheduser_wdk_instagram' =>  wdk_get_user_field ($user_id, 'wdk_instagram'),
            'cacheduser_wdk_whatsapp' =>  wdk_get_user_field ($user_id, 'wdk_whatsapp'),
            'cacheduser_wdk_viber' =>  wdk_get_user_field ($user_id, 'wdk_viber'),
            'cacheduser_wdk_iban' =>  wdk_get_user_field ($user_id, 'wdk_iban'),
            'cacheduser_wdk_telegram' =>  wdk_get_user_field ($user_id, 'wdk_telegram'),
            'cacheduser_wdk_position_title' =>  wdk_get_user_field ($user_id, 'wdk_position_title'),
            'cacheduser_agency_name' =>  wdk_get_user_field ($user_id, 'agency_name'),
            'cacheduser_description' =>  wdk_get_user_field ($user_id, 'description'),
            'cacheduser_user_url' =>  wdk_get_user_field ($user_id, 'user_url'),
            'cacheduser_roles' =>  join(',', ( array ) wdk_get_user_field ($user_id, 'roles')),
            'cacheduser_json_data' => json_encode($json_data),
            'cacheduser_date_updated' => date('Y-m-d H:i:s'),
        ), NULL);
    }

    public function regenerate_cache_activation()
    {
        if(wdk_get_option($this->option_key)) {
            return true; 
        }
        
        $this->regenerate_cache($limit=20);

        return TRUE;
    }

    public function regenerate_cache_regenerate()
    {
        if(isset($_GET['wdk_cached_users_regenerate'])) {
            $this->regenerate_cache();

            return TRUE;
        }
    }

    public function regenerate_cache($limit = NULL)
    {
        global $wpdb;
        $sql = "SELECT * FROM $wpdb->users";

        if(!empty($limit))
            $sql .= " LIMIT ".esc_sql($limit);

        $dbusers = $wpdb->get_results($sql);

        foreach($dbusers as $dbuser) {
            $this->update(wmvc_show_data('ID', $dbuser));
        }

        update_option( $this->option_key, 1);

        return TRUE;
    }

}
