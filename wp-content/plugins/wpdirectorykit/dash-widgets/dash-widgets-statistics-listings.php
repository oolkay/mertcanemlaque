<?php
namespace Wdk\DashWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class WDK_Dashboard_Widget_Stats_Listings extends WDK_DashWidgets {

    /**
     * The id of this widget.
     */
    static $widget_id = 'wdk_dashboard_widget_stats_listings';

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
            __( 'Wdk Listings Stats', 'wpdirectorykit' ),//Visible name for the widget
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Listings','widget'),      //Callback for the main widget content
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Listings','config')       //Optional callback for widget configuration content
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

        wp_enqueue_style('dash-widgets-statistics-listings');

        /* default from settings */
        $data['id_element'] = self::$widget_id;

        /* widgets data */
        $stat_default = array('color' => '', 'class' => '', 'title' => '', 'value' => '', 'icon' => '', 'link' =>'');

        /* count users */
        $post_table = $WMVC->db->prefix.'posts';
        $fields_table = $WMVC->db->prefix.'wdk_listings_fields';

        $WMVC->db->select('COUNT(*) AS total,   
                            sum( IF((is_activated = 1 AND is_approved = 1), 1, 0)) AS activated_count,
                            sum(case when is_featured = 1 then 1 else 0 end) AS featured_count,
                            sum(case when is_activated IS NULL then 1 when is_approved IS NULL then 1 else 0 end) AS inactivated_count
                            ');
        $WMVC->db->from($WMVC->listing_m->_table_name);
        $WMVC->db->join($post_table.' ON '.$WMVC->listing_m->_table_name.'.post_id = '.$post_table.'.ID');
        $WMVC->db->join($fields_table.' ON '.$WMVC->listing_m->_table_name.'.post_id = '.$fields_table.'.post_id');
        
        $query = $WMVC->db->get();
        $count_listings = $WMVC->db->row();
        

        /* Total Listings*/
        $data['stats']['total_listings'] = array('title' => __( 'Total Listings', 'wpdirectorykit' ));
        $data['stats']['total_listings']['value'] = isset($count_listings->total) ? $count_listings->total : 0;
        $data['stats']['total_listings']['link'] = admin_url('/admin.php?page=wdk');
        $data['stats']['total_listings'] = array_merge($stat_default, $data['stats']['total_listings']);
        
        /* number of active listings */
        $data['stats']['total_active_listings'] = array('title' => __( 'Active Listings', 'wpdirectorykit' ), 'class'=>'red', 'icon'=>'dashicons dashicons-visibility');
        $data['stats']['total_active_listings']['value'] = isset($count_listings->activated_count) ? $count_listings->activated_count : 0;
        $data['stats']['total_active_listings']['link'] = admin_url('/admin.php?page=wdk&is_activated=on&is_approved=on');
        if(isset($_GET['is_activated']) && $_GET['is_activated'] == 'on') {
            $data['stats']['total_active_listings']['class'] .= ' active';
        }
        $data['stats']['total_active_listings'] = array_merge($stat_default, $data['stats']['total_active_listings']);
        
        /* number of inactive listings */
        $data['stats']['total_inactive_listings'] = array('title' => __( 'Inactive/Unapproved Listings', 'wpdirectorykit' ), 'class'=>'orange', 'icon'=>'dashicons dashicons-hidden');
        $data['stats']['total_inactive_listings']['value'] = isset($count_listings->inactivated_count) ? $count_listings->inactivated_count : 0;
        $data['stats']['total_inactive_listings']['link'] = admin_url('/admin.php?page=wdk&inactive=on');
        if(isset($_GET['is_activated']) && $_GET['is_activated'] == 'off') {
            $data['stats']['total_inactive_listings']['class'] .= ' active';
        }
        $data['stats']['total_inactive_listings'] = array_merge($stat_default, $data['stats']['total_inactive_listings']);
        
        /* number of featured listings */
        if(wdk_get_option('wdk_is_featured_enabled', FALSE)){
            $data['stats']['total_featured_listings'] = array('title' => __( 'Featured Listings', 'wpdirectorykit'), 'class'=>'blue', 'icon'=>'dashicons dashicons-superhero-alt');
            $data['stats']['total_featured_listings']['value'] = isset($count_listings->featured_count) ? $count_listings->featured_count : 0;
            $data['stats']['total_featured_listings']['link'] = admin_url('/admin.php?page=wdk&is_featured=on');
            if(isset($_GET['is_featured']) && $_GET['is_featured'] == 'on') {
                $data['stats']['total_featured_listings']['class'] .= ' active';
            }

            $data['stats']['total_featured_listings'] = array_merge($stat_default, $data['stats']['total_featured_listings']);
        }
        
        self::view('dash-widgets-statistics-listings', $data, true);      
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