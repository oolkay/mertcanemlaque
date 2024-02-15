<?php
namespace Wdk\DashWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class WDK_Dashboard_Widget_Stats_Usage extends WDK_DashWidgets {

    /**
     * The id of this widget.
     */
    static $widget_id = 'wdk_dashboard_widget_stats_usage';

    /**
     * Hook to wp_dashboard_setup to add the widget.
     */
    public static function init() {
        
        if(!wmvc_user_in_role('administrator') && !is_super_admin() && !current_user_can('wdk_listings_manage')) {
            return false;
        }

        //Register widget settings...
        self::update_dashboard_widget_options(
            self::$widget_id,                                  //The  widget id
            array(                                      //Associative array of options & default values
                'example_number' => 42,
            ),
            true                                        //Add only (will not update existing options)
        );

        //Register the widget...
        wp_add_dashboard_widget(
            self::$widget_id,                   //A unique slug/ID
            __( 'Wdk Usage Stats', 'wpdirectorykit' ),//Visible name for the widget
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Usage','widget'),      //Callback for the main widget content
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Usage','config')       //Optional callback for widget configuration content
        );
    }

    /**
     * Load the widget code
     */
    public static function widget() {

        $WMVC = &wdk_get_instance();
        $WMVC->model('listing_m');
        $WMVC->load_helper('listing');

        $data = array();

        $data['settings'] = array(
            'conf_limit' => 50,
        );

        wp_enqueue_style('dash-widgets-statistics-usage');

        /* default from settings */
        $data['id_element'] = self::$widget_id;

        /* widgets data */
        $stat_default = array('color' => '', 'class' => '', 'title' => '', 'value' => '', 'icon' => '', 'link' =>'');

        /* count users */
        $result_count_users = count_users();
        
        /* Will show number of users */
        $data['stats']['total_users'] = array('title' => __( 'Users On Portal', 'wpdirectorykit'), 'icon'=>'dashicons dashicons-groups', 'class'=>'nohover');
        $data['stats']['total_users']['value'] = $result_count_users['total_users'];
        $data['stats']['total_users'] = array_merge($stat_default, $data['stats']['total_users']);

        /* number of agents */
        $data['stats']['total_users_agents'] = array('title' => __( 'Agents on Portal', 'wpdirectorykit'), 'class'=>'blue nohover', 'icon'=>'dashicons dashicons-businessperson');
        $data['stats']['total_users_agents']['value'] = isset($result_count_users['avail_roles']['wdk_agent']) ? $result_count_users['avail_roles']['wdk_agent'] : 0;
        $data['stats']['total_users_agents'] = array_merge($stat_default, $data['stats']['total_users_agents']);


        $WMVC->db->select('COUNT(*) AS total,   
                            sum(case when is_activated = 1 then 1 else 0 end) AS activated_count,
                            sum(case when is_activated = 1 then 0 else 1 end) AS inactivated_count');
        $WMVC->db->from($WMVC->listing_m->_table_name);
        
        $query = $WMVC->db->get();
        $count_listings = $WMVC->db->row();

        /* number of active listings */
        $data['stats']['total_active_listings'] = array('title' => __( 'Active Listings', 'wpdirectorykit' ), 'class'=>'red nohover', 'icon'=>'dashicons dashicons-visibility');
        $data['stats']['total_active_listings']['value'] = isset($count_listings->activated_count) ? $count_listings->activated_count : 0;
        $data['stats']['total_active_listings'] = array_merge($stat_default, $data['stats']['total_active_listings']);


        /* number of inactive listings */
        $data['stats']['total_inactive_listings'] = array('title' => __( 'Inactive Listings', 'wpdirectorykit' ), 'class'=>'orange nohover', 'icon'=>'dashicons dashicons-hidden');
        $data['stats']['total_inactive_listings']['value'] = isset($count_listings->inactivated_count) ? $count_listings->inactivated_count : 0;
        $data['stats']['total_inactive_listings'] = array_merge($stat_default, $data['stats']['total_inactive_listings']);
      
        self::view('dash-widgets-statistics-usage', $data, true);      
    }

    /**
     * Load widget config code.
     *
     * This is what will display when an admin clicks
     */
    public static function config() {
        wp_redirect(admin_url("admin.php?page=listing_settings")); exit;
    }


}