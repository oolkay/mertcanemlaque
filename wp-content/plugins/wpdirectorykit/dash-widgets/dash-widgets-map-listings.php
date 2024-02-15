<?php
namespace Wdk\DashWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class WDK_Dashboard_Widget_Map_Listings extends WDK_DashWidgets {

    /**
     * The id of this widget.
     */
    static $widget_id = 'wdk_dashboard_widget_map_listings';

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
            __( 'Wdk Listings map', 'wpdirectorykit' ),//Visible name for the widget
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Map_Listings','widget'),      //Callback for the main widget content
            array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Map_Listings','config')       //Optional callback for widget configuration content
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

        wp_enqueue_style('leaflet');
        wp_enqueue_style('leaflet-cluster-def');
        wp_enqueue_style('leaflet-cluster');
        wp_enqueue_script('leaflet');
        wp_enqueue_script('leaflet-cluster');
        wp_enqueue_script('wdk-dash-widgets-map-listings');
        wp_enqueue_style('wdk-dash-widgets-map-listings');
        
        wp_enqueue_style('wdk-notify');
        wp_enqueue_script('wdk-notify');
		wp_enqueue_style( 'wdk-listings-map' );
        if(defined('ELEMENTOR_ASSETS_URL')){
            wp_register_style(
                'elementor-font-awesome',
                ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/font-awesome.min.css',
                [],
                '4.7.0'
            );
            wp_register_style(
                'elementor-font-awesome-solid',
                ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/solid.css'
            );
            wp_register_style(
                'elementor-font-awesome-5',
                ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/fontawesome.css'
            );
            wp_register_style(
                'elementor-font-awesome-brands',
                ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/brands.css'
            );
            wp_register_style(
                'elementor-font-awesome-regular',
                ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/regular.css'
            );
        }

        wp_enqueue_style( 'elementor-font-awesome-regular' );
        wp_enqueue_style( 'elementor-font-awesome-brands' );
        wp_enqueue_style( 'elementor-font-awesome-solid' );
        wp_enqueue_style( 'elementor-font-awesome-5' );
        wp_enqueue_style( 'elementor-font-awesome' );

        /* default from settings */
        $data['lat'] = wdk_get_option('wdk_default_lat', 51.505);
        $data['lng'] = wdk_get_option('wdk_default_lng', -0.09);

        $data['id_element'] = self::$widget_id;

        $data['settings'] = array(
            'conf_limit' => 50,
            'conf_custom_map_zoom_index' => 10,
            'conf_custom_dragging' => 'yes',
            'conf_custom_map_style' => '',
            'conf_custom_map_pin_icon' => '',
            'conf_custom_map_pin' => '',
            'conf_custom_map_height' => 450,
            'enable_custom_gps_center' => '',
            
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
        
        self::view('dash-widgets-map-listings', $data, true);      
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