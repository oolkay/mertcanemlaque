<?php
namespace Wdk\DashWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class WDK_Dashboard_Widget_Latest_Listings extends WDK_DashWidgets {

    /**
     * The id of this widget.
     */
    static $widget_id = 'wdk_dashboard_widget_latest_listings';

    /**
     * Hook to wp_dashboard_setup to add the widget.
     */
    public static function init() {
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
            __( 'Wdk Latest Listings', 'wpdirectorykit' ),//Visible name for the widget
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Latest_Listings','widget'),      //Callback for the main widget content
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Latest_Listings','config')       //Optional callback for widget configuration content
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

        wp_enqueue_style('wdk-dash-widgets-list-listings');

        /* default from settings */
        $data['id_element'] = self::$widget_id;

        $data['settings'] = array(
            'conf_limit' => 10,
        );
        
        $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
        $controller = 'listing';
        $custom_parameters = array();

        if(!empty($data['settings']['conf_query'])) {
            $qr_string = trim($data['settings']['conf_query'],'?');
            $string_par = array();
            parse_str($qr_string, $string_par);
            $custom_parameters += array_map('trim', $string_par);
        }
        
        $external_columns = array('location_id', 'category_id', 'post_title');
        wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters);

        $data['results'] = $WMVC->listing_m->get_pagination($data['settings']['conf_limit'], NULL, array('is_activated' => 1,'is_approved'=>1), TRUE);
        
        self::view('dash-widgets-list-listings', $data, true);      
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