<?php
namespace Wdk\DashWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WDK_Dashboard_Widget_Stats_Earning extends WDK_DashWidgets {

    /**
     * The id of this widget.
     */
    static $widget_id = 'wdk_dashboard_widget_stats_earning';

    /**
     * Hook to wp_dashboard_setup to add the widget.
     */
    public static function init() {

        if(!wmvc_user_in_role('administrator') && !is_super_admin() && !current_user_can('wdk_listings_manage')) {
            return false;
        }

        global $Winter_MVC_wdk_membership, $Winter_MVC_wdk_payments;
        /* if missing addon membership and payments, hide widget */
        if(!isset($Winter_MVC_wdk_membership) && !isset($Winter_MVC_wdk_payments)) {
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
            __( 'Wdk Earning Stats', 'wpdirectorykit' ),//Visible name for the widget
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Earning','widget'),      //Callback for the main widget content
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Earning','config')       //Optional callback for widget configuration content
        );
    }

    /**
     * Load the widget code
     */
    public static function widget() {
        global $Winter_MVC_wdk_membership, $Winter_MVC_wdk_payments;

        $WMVC = &wdk_get_instance();
        $WMVC->model('listing_m');

        $data = array();

        $data['settings'] = array(
            'conf_limit' => 50,
        );

        wp_enqueue_style('dash-widgets-statistics-earning');

        /* default from settings */
        $data['id_element'] = self::$widget_id;

        /* widgets data */
        $stat_default = array('color' => '', 'class' => '', 'title' => '', 'value' => '', 'icon' => '', 'link' =>'');

        /* membership */
        $count_membership = null;

        /* if missing addon membership and payments, hide widget */
        if(!isset($Winter_MVC_wdk_membership) && !isset($Winter_MVC_wdk_payments)) {
            return false;
        }

        if(isset($Winter_MVC_wdk_membership)) {
            $Winter_MVC_wdk_membership->model('subscription_m');
            $Winter_MVC_wdk_membership->model('subscription_user_m');

            $WMVC->db->select('COUNT(*) AS total,   
                                sum(days_limit) AS total_days,
                                sum(price) AS total_cost');
            $WMVC->db->from($Winter_MVC_wdk_membership->subscription_user_m->_table_name);
            $WMVC->db->join($Winter_MVC_wdk_membership->subscription_m->_table_name.' ON '.$Winter_MVC_wdk_membership->subscription_m->_table_name.'.idsubscription = '.$Winter_MVC_wdk_membership->subscription_user_m->_table_name.'.subscription_id', TRUE, 'left');
            
            $WMVC->db->where(array('(date_expire > "'.current_time('mysql').'")'=>NULL));
            
            $query = $WMVC->db->get();
            $count_membership = $WMVC->db->row();
        }
        /* packages */
        $count_packages = null;

        if(isset($Winter_MVC_wdk_payments)) {
            $Winter_MVC_wdk_payments->model('package_m');

            $WMVC->db->select('COUNT(*) AS total,   
                                sum(days_limit) AS total_days,
                                sum(price) AS total_cost');
            $WMVC->db->from($WMVC->listing_m->_table_name);
            $WMVC->db->join($Winter_MVC_wdk_payments->package_m->_table_name.' ON '.$WMVC->listing_m->_table_name.'.package_id = '.$Winter_MVC_wdk_payments->package_m->_table_name.'.idpackage', TRUE, 'left');
            
            $WMVC->db->where(array('(date_package_expire > "'.current_time('mysql').'")'=>NULL, '(package_id IS NOT NULL)'=>NULL));
            
            $query = $WMVC->db->get();
            $count_packages = $WMVC->db->row();
        }

        /* Total number of active Membership subscriptions */
        if($count_membership){
            $data['stats']['total_membership_subscriptions'] = array('title' => __( 'Total Membership Subscriptions', 'wpdirectorykit' ), 'class'=>'nohover');
            $data['stats']['total_membership_subscriptions']['value'] = $count_membership->total;
            $data['stats']['total_membership_subscriptions'] = array_merge($stat_default, $data['stats']['total_membership_subscriptions']);
        }
        
        /* Total number of active Listings subscriptions */
        if($count_packages){
            $data['stats']['total_listings_packages'] = array('title' => __( 'Total Listings Subscriptions', 'wpdirectorykit'), 'class'=>'blue nohover');
            $data['stats']['total_listings_packages']['value'] = $count_packages->total;
            $data['stats']['total_listings_packages'] = array_merge($stat_default, $data['stats']['total_listings_packages']);
        }

        /* Approx Earnings per month from Membership subscriptions */
        if($count_membership){
            $data['stats']['total_membership_subscriptions_earns'] = array('title' => __( 'Membership Subscriptions', 'wpdirectorykit' ), 'class'=>'red nohover');

            $data['stats']['total_membership_subscriptions_earns']['value'] =  '0'.wdk_currency_symbol().'/'.__( 'm', 'wpdirectorykit');
            if(!empty($count_membership->total_cost) && !empty($count_membership->total_days))
                $data['stats']['total_membership_subscriptions_earns']['value'] = round(intval($count_membership->total_cost)/intval($count_membership->total_days)*30).wdk_currency_symbol().'/'.__( 'm', 'wpdirectorykit');
           
            $data['stats']['total_membership_subscriptions_earns'] = array_merge($stat_default, $data['stats']['total_membership_subscriptions_earns']);
        }

        /* Approx Earnings per month from Listings subscriptions */
        if($count_packages){
            $data['stats']['total_listings_packages_earns'] = array('title' => __( 'Listings Subscriptions', 'wpdirectorykit' ), 'class'=>'orange nohover');

            $data['stats']['total_listings_packages_earns']['value'] = '0'.wdk_currency_symbol().'/'.__( 'm', 'wpdirectorykit');
            if(!empty($count_packages->total_cost) && !empty($count_packages->total_days))
                $data['stats']['total_listings_packages_earns']['value'] = round(intval($count_packages->total_cost)/intval($count_packages->total_days)*30).wdk_currency_symbol().'/'.__( 'm', 'wpdirectorykit');
            $data['stats']['total_listings_packages_earns'] = array_merge($stat_default, $data['stats']['total_listings_packages_earns']);
        }

        self::view('dash-widgets-statistics-earning', $data, true);      
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