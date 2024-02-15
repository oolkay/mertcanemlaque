<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       listing-themes.com
 * @since      1.0.0
 *
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/admin
 * @author     listing-themes.com <dev@listing-themes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Wpdirectorykit_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpdirectorykit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpdirectorykit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_register_style( 'wdk-treefield', WPDIRECTORYKIT_URL . 'public/js/wdk_treefield/treefield.css', array(), '1.0.0' );
		wp_register_style( 'wdk-modal', WPDIRECTORYKIT_URL. 'public/css/wdk-modal.css', array(), $this->version, 'all');
		wp_register_style( 'wdk-notify', WPDIRECTORYKIT_URL. 'public/css/wdk-notify.css', array(), $this->version, 'all');
		wp_register_style( 'leaflet', WPDIRECTORYKIT_URL. 'public/js/openstreetmap/leaflet.css', array(), '1.7.1', 'all' );
		wp_register_style( 'leaflet-cluster-def',  WPDIRECTORYKIT_URL. 'public/js/openstreetmap/MarkerCluster.Default.css', array(), '1.7.1', 'all' );
		wp_register_style( 'leaflet-cluster',  WPDIRECTORYKIT_URL. 'public/js/openstreetmap/MarkerCluster.css', array(), '1.7.1', 'all' );

		wp_register_style( 'select2', WPDIRECTORYKIT_URL. 'public/js/select2/css/select2.min.css', array(), '4.0.13', 'all' );
		wp_register_style('jquery-confirm',   WPDIRECTORYKIT_URL. 'public/js/jquery-confirm/css/jquery-confirm.css', array(), '3.3.4', 'all');

		wp_register_style( 'wdk-treefield-dropdown', WPDIRECTORYKIT_URL. 'public/js/wdk_treefield_dropdown/wdk_treefield_dropdown.css', array(), '1.0', 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpdirectorykit-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-responsive', plugin_dir_url( __FILE__ ) . 'css/wpdirectorykit-admin-responsive.css', array(), $this->version, 'all' );
		wp_register_style('jquery-ui', WPDIRECTORYKIT_URL. 'public/css/jquery-ui.css', array(), null );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpdirectorykit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpdirectorykit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( 'wdk-treefield', WPDIRECTORYKIT_URL . 'public/js/wdk_treefield/treefield.js', array( 'jquery' ), false, false );
		wp_register_script( 'wpmediaelement_file', WPDIRECTORYKIT_URL . 'admin/js/jquery.wpmediaelement_file.js', array( 'jquery' ), false, false );
		
		wp_register_script( 'wdk-modal', WPDIRECTORYKIT_URL. 'public/js/wdk-modal.js', array( 'jquery' ), $this->version, false);
		wp_register_script( 'wdk-notify', WPDIRECTORYKIT_URL. 'public/js/wdk-notify.js', array( 'jquery' ), $this->version, false);
		wp_register_script( 'leaflet', WPDIRECTORYKIT_URL. 'public/js/openstreetmap/leaflet.js', array( 'jquery' ), '1.7.1', false );
	
		wp_register_script( 'leaflet-cluster',  WPDIRECTORYKIT_URL. 'public/js/openstreetmap/leaflet.markercluster.js', array( 'jquery' ), '1.7.1', false );


		wp_register_script('select2', WPDIRECTORYKIT_URL. 'public/js/select2/js/select2.min.js', array( 'jquery' ), '4.0.13', false );
		wp_register_script('wdk-select2', WPDIRECTORYKIT_URL. 'public/js/wdk-select2.js', array( 'jquery' ), '4.0.13', false );

		wp_register_script('jquery-confirm',  WPDIRECTORYKIT_URL. 'public/js/jquery-confirm/js/jquery-confirm.js', array( 'jquery' ), '3.3.4', false );
		wp_register_script( 'wdk-dependfields-submitform',  WPDIRECTORYKIT_URL. 'public/js/wdk-dependfields-submitform.js', array( 'jquery' ), false, false );

		$params = array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        );
		wp_register_script('wdk-treefield-dropdown', WPDIRECTORYKIT_URL. 'public/js/wdk_treefield_dropdown/wdk_treefield_dropdown.js', array( 'jquery' ), '1.0', false );
		wp_localize_script( 'wdk-treefield-dropdown', 'script_parameters', $params);

		wp_register_script( 'wdk-dependfields-edit', plugin_dir_url( __FILE__ ) . 'js/wdk-dependfields-edit.js', array( 'jquery' ), $this->version, false );
        $params = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        );

        wp_localize_script('wdk-dependfields-edit', 'script_dependfields_parameters', $params);
		
		$params = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
			'format_date' => wdk_convert_date_format_js(get_option('date_format')),
			'format_datetime' => wdk_convert_date_format_js(get_option('date_format').' '.get_option('time_format')),
			'format_date_js' => wdk_convert_date_format_jquery(get_option('date_format')),
			'format_datetime_js' => wdk_convert_date_format_jquery(get_option('date_format').' '.get_option('time_format')),
        );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpdirectorykit-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'script_parameters', $params);
		
		
    }

	/**
	 * Admin AJAX
	 */

	public function ajax_admin()
	{
		global $Winter_MVC_WDK;

		$page = '';
		$function = '';

		if(isset($_POST['page']))$page = sanitize_text_field($_POST['page']);
		if(isset($_POST['function']))$function = sanitize_text_field($_POST['function']);

		$Winter_MVC_WDK->load_controller($page, $function, array());
	}

    /**
	 * Admin Page Display
	 */
	public function admin_page_display() {
		global $Winter_MVC_WDK, $submenu, $menu;

		$page = '';
        $function = '';

		if(isset($_GET['page']))$page = sanitize_text_field($_GET['page']);
		if(isset($_GET['function']))$function = sanitize_text_field($_GET['function']);

		if(substr($function,0,1) == '_') {
			exit(esc_html__('blocked for public', 'wpdirectorykit'));
		}

		$Winter_MVC_WDK->load_controller($page, $function, array());
	}

	private function multilingual_register_strings()
	{
		if (!function_exists('PLL'))return;

		global $Winter_MVC_WDK;
		$Winter_MVC_WDK->model('field_m');
		$Winter_MVC_WDK->model('category_m');
		$Winter_MVC_WDK->model('location_m');

        $fields = $Winter_MVC_WDK->field_m->get();

        foreach($fields as $field)
        { 	
            if(!empty($field->field_label))
                pll_register_string( 'wpdirectorykit', $field->field_label, 'wdk-fields');
            
            if(!empty($field->values_list))
                pll_register_string( 'wpdirectorykit', $field->values_list, 'wdk-fields');
            
            if(!empty($field->hint))
                pll_register_string( 'wpdirectorykit', $field->hint, 'wdk-fields');
        }

        $categories = $Winter_MVC_WDK->category_m->get_tree_table();

		foreach($categories as $category)
        { 	
            pll_register_string( 'wpdirectorykit', $category->category_title, 'wdk-categories');
        }

        $locations = $Winter_MVC_WDK->location_m->get_tree_table();

		foreach($locations as $location)
        { 	
            pll_register_string( 'wpdirectorykit', $location->location_title, 'wdk-locations');
        }
	}

    /**
     * To add Plugin Menu and Settings page
     */
    public function plugin_menu() {

        ob_start();

		$this->multilingual_register_strings();

        //require_once WPDIRECTORYKIT_PATH . 'vendor/boo-settings-helper/class-boo-settings-helper.php';

        add_menu_page(__('Directory Kit','wpdirectorykit'), __('Directory Kit','wpdirectorykit'), 
            'wdk_listings_manage', 'wdk', array($this, 'admin_page_display'),
            //plugin_dir_url( __FILE__ ) . 'resources/logo.png',
            'dashicons-category',
            28 );
        
        add_submenu_page('wdk', 
            __('Listings','wpdirectorykit'), 
            __('Listings','wpdirectorykit'),
            'wdk_listings_manage', 'wdk', array($this, 'admin_page_display'));
        
        add_submenu_page('wdk', 
            __('Add Listing','wpdirectorykit'), 
            __('Add Listing','wpdirectorykit'),
            'wdk_listings_manage', 'wdk_listing', array($this, 'admin_page_display'));

        add_submenu_page('wdk', 
                        __('Fields','wpdirectorykit'), 
                        __('Fields','wpdirectorykit'),
                        'wdk_listings_manage', 'wdk_fields', array($this, 'admin_page_display'));
        
        if(get_option('wdk_is_category_enabled', FALSE))
        add_submenu_page('wdk', 
                        __('Categories','wpdirectorykit'), 
                        __('Categories','wpdirectorykit'),
                        'wdk_listings_manage', 'wdk_category', array($this, 'admin_page_display'));
        
        if(get_option('wdk_is_location_enabled', FALSE))
        add_submenu_page('wdk', 
                        __('Locations','wpdirectorykit'), 
                        __('Locations','wpdirectorykit'),
                        'wdk_listings_manage', 'wdk_location', array($this, 'admin_page_display'));

        add_submenu_page('wdk', 
                        __('Search Form','wpdirectorykit'), 
                        __('Search Form','wpdirectorykit'),
                        'wdk_listings_manage', 'wdk_searchform', array($this, 'admin_page_display'));

        add_submenu_page('wdk', 
                        __('Result Card','wpdirectorykit'), 
                        __('Result Card','wpdirectorykit'),
                        'wdk_listings_manage', 'wdk_resultitem', array($this, 'admin_page_display'));
        
        add_submenu_page('wdk', 
                        __('Change Currency','wpdirectorykit'), 
                        __('Change Currency','wpdirectorykit'),
                        'wdk_listings_manage', 'wdk_change_currency', array($this, 'admin_page_display'));

        add_submenu_page('wdk', 
                        __('Messages','wpdirectorykit'), 
                        __('Messages','wpdirectorykit'),
                        'wdk_listings_manage', 'wdk_messages', array($this, 'admin_page_display'));

        add_submenu_page('wdk', 
                        __('Settings','wpdirectorykit'), 
                        __('Settings','wpdirectorykit'),
                        'wdk_listings_manage', 'wdk_settings', array($this, 'admin_page_display'));

		add_submenu_page('wdk', 
						__('Documentation','wpdirectorykit'), 
						__('Documentation','wpdirectorykit'),
						'wdk_listings_manage', 'https://wpdirectorykit.com/documentation');

        if(!file_exists(WPDIRECTORYKIT_PATH . 'premium_functions.php') || defined('WDK_FS_DISABLE'))
        {

            if(!file_exists(WP_PLUGIN_DIR.'/wdk-currency-conversion') || !is_plugin_active( 'wdk-currency-conversion/wdk-currency-conversion.php' ))
            add_submenu_page('wdk', 
                            __('Currencies Addon','wpdirectorykit'), 
                            __('Currencies Addon','wpdirectorykit'),
                            'wdk_listings_manage', 'wdk_addons_currencies', array($this, 'admin_page_display'));

            if(!file_exists(WP_PLUGIN_DIR.'/wdk-membership') || !is_plugin_active( 'wdk-membership/wdk-membership.php' ))
            add_submenu_page('wdk', 
                            __('Membership Addon','wpdirectorykit'), 
                            __('Membership Addon','wpdirectorykit'),
                            'wdk_listings_manage', 'wdk_addons_membership', array($this, 'admin_page_display'));

            if(!file_exists(WP_PLUGIN_DIR.'/wdk-bookings') || !is_plugin_active( 'wdk-bookings/wdk-bookings.php' ))
            add_submenu_page('wdk', 
                            __('Booking Addon','wpdirectorykit'), 
                            __('Booking Addon','wpdirectorykit'),
                            'wdk_listings_manage', 'wdk_addons_bookings', array($this, 'admin_page_display'));

            add_submenu_page('wdk', 
                            __('More Addons','wpdirectorykit'), 
                            __('More Addons','wpdirectorykit'),
                            'wdk_listings_manage', 'wdk_addons', array($this, 'admin_page_display'));

            add_submenu_page('wdk', 
                            __('Support Forum','wpdirectorykit'), 
                            __('Support Forum','wpdirectorykit'),
                            'wdk_listings_manage', 'https://wordpress.org/support/plugin/wpdirectorykit/');

            add_submenu_page('wdk', 
                            __('Contact','wpdirectorykit'), 
                            __('Contact','wpdirectorykit'),
                            'wdk_listings_manage', 'https://wpdirectorykit.com/contact.html');
        }

        $current_theme = wp_get_theme();

        if(	defined( 'WP_DEBUG' ) && WP_DEBUG && $current_theme->get( 'AuthorURI' ) == 'https://wpdirectorykit.com')
            add_submenu_page('tools.php', 
                        esc_html__('WDK Demo Import', 'wpdirectorykit'), esc_html__('WDK Demo Import', 'wpdirectorykit'), 'wdk_listings_manage', 'wdk_demo_import', array($this, 'admin_page_display'));

	
    }

}
