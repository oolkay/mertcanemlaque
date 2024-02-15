<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       listing-themes.com
 * @since      1.0.0
 *
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/public
 * @author     listing-themes.com <dev@listing-themes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Wpdirectorykit_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		/* load tree dropdown */
		wp_register_style( 'wdk-listing-carousel', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-carousel.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-sliders-carousel', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-sliders-carousel.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-sliders-more-grid-images', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-sliders-more-grid-images.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-sliders-grid-images', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-sliders-grid-images.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-sliders', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-sliders.css', array(), $this->version, 'all' );

		wp_register_style( 'wdk-suggestion', plugin_dir_url( __FILE__ ) . 'js/wdk_suggestion/wdk_suggestion.css', array(), '1.0.0' );
		wp_register_style( 'wdk-treefield', plugin_dir_url( __FILE__ ) . 'js/wdk_treefield/treefield.css', array(), '1.0.0' );
		wp_register_style( 'wdk-treefield-checkboxes', plugin_dir_url( __FILE__ ) . 'js/wdk_treefield_checkboxes/wdk_treefield_checkboxes.css', array(), '1.0.0' );

		wp_register_style( 'wdk-element-button', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-element-button.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-agent-listings', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-agent-listings.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-agent', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-agent.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listings-list', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listings-list.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-fields-section', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-fields-section.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-slider', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-slider.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-locations-carousel', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-locations-carousel.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-categories-carousel', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-categories-carousel.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-field-files', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-field-files.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-field-files-list', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-field-files-list.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-field-images', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-field-images.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-locations-grid', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-locations-grid.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-categories-grid-cover', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-categories-grid-cover.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-locations-grid-cover', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-locations-grid-cover.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-categories-grid', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-categories-grid.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-categories-list', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-categories-list.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-locations-tree', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-locations-tree.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-categories-tree', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-categories-tree.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-categories-tree-top', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-categories-tree-top.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-locations-list', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-locations-list.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listings-map', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-map.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listings-carousel', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listings-carousel.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-listing-related-listings-table', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listing-related-listings-table.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-search-popup', WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-search-popup.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-notify', plugin_dir_url( __FILE__ ) . 'css/wdk-notify.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-modal', plugin_dir_url( __FILE__ ) . 'css/wdk-modal.css', array(), $this->version, 'all' );
		wp_register_style( 'wdk-hover', plugin_dir_url( __FILE__ ) . 'css/wdk-hover.css', array(), $this->version, 'all' );
		wp_register_style( 'leaflet', plugin_dir_url( __FILE__ ) . 'js/openstreetmap/leaflet.css', array(), '1.7.1', 'all' );
		wp_register_style( 'leaflet-cluster-def', plugin_dir_url( __FILE__ ) . 'js/openstreetmap/MarkerCluster.Default.css', array(), '1.7.1', 'all' );
		wp_register_style( 'leaflet-cluster', plugin_dir_url( __FILE__ ) . 'js/openstreetmap/MarkerCluster.css', array(), '1.7.1', 'all' );
		wp_register_style( 'leaflet-fullscreen', plugin_dir_url( __FILE__ ) . 'js/openstreetmap/leaflet.fullscreen.css', array(), '1.7.1', 'all' );
		wp_register_style( 'ion.range-slider', plugin_dir_url( __FILE__ ) . 'js/ion.range-slider/css/ion.range-slider.min.css', array(), '2.3.1', 'all' );
		wp_register_style( 'wdk-slider-range', plugin_dir_url( __FILE__ ) . 'css/wdk-slider-range.css', array(), '1.0', 'all' );
		wp_register_style( 'select2', plugin_dir_url( __FILE__ ) . 'js/select2/css/select2.min.css', array(), '4.0.13', 'all' );
		wp_register_style( 'wdk-treefield-dropdown', plugin_dir_url( __FILE__ ) . 'js/wdk_treefield_dropdown/wdk_treefield_dropdown.css', array(), '1.0', 'all' );
		wp_register_style( 'leaflet-draw', plugin_dir_url( __FILE__ ) . 'js/leaflet-draw/leaflet.draw.css', array(), '1.0', 'all' );
	
		wp_register_style('blueimp-gallery',  plugin_dir_url( __FILE__ ).'js/blueimp-gallery/css/blueimp-gallery.min.css', array(), '1.8', 'all');

		wp_register_style('slick',  plugin_dir_url( __FILE__ ).'js/slick/slick.css', array(), '1.8', 'all');
        wp_register_style('slick-theme',  plugin_dir_url( __FILE__ ).'js/slick/slick-theme.css', array(), '1.8', 'all');

        wp_register_style('jquery-confirm',  plugin_dir_url( __FILE__ ).'js/jquery-confirm/css/jquery-confirm.css', array(), '3.3.4', 'all');
        wp_register_style('wdk-scroll-mobile-swipe',  plugin_dir_url( __FILE__ ).'css/wdk-scroll-mobile-swipe.css', array(), '1.0.2', 'all');
        wp_enqueue_style('wdk-scroll-mobile-swipe');

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpdirectorykit-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-responsive', plugin_dir_url( __FILE__ ) . 'css/wpdirectorykit-public-responsive.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-conflicts', plugin_dir_url( __FILE__ ) . 'css/wpdirectorykit-public-conflicts.css', array(), $this->version, 'all' );
		
		if(is_rtl())
			wp_enqueue_style( $this->plugin_name.'-rtl', plugin_dir_url( __FILE__ ) . 'css/wpdirectorykit-public-rtl.css', array(), $this->version, 'all' );

		global $Winter_MVC_WDK;
		$Winter_MVC_WDK->model('category_m');
		$categories = $Winter_MVC_WDK->category_m->get_by(array('(category_color IS NOT NULL AND category_color !="")'=>NULL));

		/* categories colors special */
		$custom_css = '';
		foreach ($categories as $category) {
			/* marker map */
			$custom_css .= " 
							.wdk-element .wdk-map .wdk_marker-container.category_id_".esc_attr(wmvc_show_data('idcategory', $category),'',TRUE, TRUE)." .wdk_face.front i {
								color: ".esc_attr(wmvc_show_data('category_color', $category),'',TRUE, TRUE).";
							}
							.wdk-element .wdk-map .wdk_marker-container.category_id_".esc_attr(wmvc_show_data('idcategory', $category),'',TRUE, TRUE)." .wdk_marker-container:hover .wdk_marker-card::after,
							.wdk-element .wdk-map .wdk_marker-container.category_id_".esc_attr(wmvc_show_data('idcategory', $category),'',TRUE, TRUE)." .wdk_marker-card::before {
								background: ".esc_attr(wmvc_show_data('category_color', $category),'',TRUE, TRUE).";
							}
						";
			/* marker map (label) */			
			$custom_css .= " 
							.wdk-element .wdk-map .wdk_marker-container.category_id_".esc_attr(wmvc_show_data('idcategory', $category),'',TRUE, TRUE).".wdk_marker_label:not(.wdk_marker_clear) {
								border-color: ".esc_attr(wmvc_show_data('category_color', $category),'',TRUE, TRUE).";
								background: ".esc_attr(wmvc_show_data('category_color', $category),'',TRUE, TRUE).";
							}
						";
		}
		wp_add_inline_style( $this->plugin_name, $custom_css);

	}
	
	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_register_script( 'wdk-dependfields-submitform', plugin_dir_url( __FILE__ ) . 'js/wdk-dependfields-submitform.js', array( 'jquery' ), false, false );
		wp_register_script( 'wdk-dependfields-search', plugin_dir_url( __FILE__ ) . 'js/wdk-dependfields-search.js', array( 'jquery' ), false, false );

		/* load tree dropdown */
		wp_register_script( 'wdk-suggestion', plugin_dir_url( __FILE__ ) . 'js/wdk_suggestion/wdk_suggestion.js', array( 'jquery' ), false, false );
		wp_register_script( 'wdk-treefield', plugin_dir_url( __FILE__ ) . 'js/wdk_treefield/treefield.js', array( 'jquery' ), false, false );
		wp_register_script( 'wdk-treefield-checkboxes', plugin_dir_url( __FILE__ ) . 'js/wdk_treefield_checkboxes/wdk_treefield_checkboxes.js', array( 'jquery' ), false, false );
		
		wp_register_script( 'wdk-notify', plugin_dir_url( __FILE__ ) . 'js/wdk-notify.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'wdk-modal', plugin_dir_url( __FILE__ ) . 'js/wdk-modal.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'leaflet', plugin_dir_url( __FILE__ ) . 'js/openstreetmap/leaflet.js', array( 'jquery' ), '1.7.1', false );
		wp_register_script( 'leaflet-cluster', plugin_dir_url( __FILE__ ) . 'js/openstreetmap/leaflet.markercluster.js', array( 'jquery' ), '1.7.1', false );
		wp_register_script( 'leaflet-fullscreen', plugin_dir_url( __FILE__ ) . 'js/openstreetmap/leaflet.fullscreen.js', array( 'jquery' ), '1.7.1', false );
		wp_register_script('blueimp-gallery', plugin_dir_url( __FILE__ ).'js/blueimp-gallery/js/blueimp-gallery.min.js', array( 'jquery' ), '3.4', false );
		wp_register_script('wdk-blueimp-gallery', plugin_dir_url( __FILE__ ).'js/wdk-blueimp-gallery.js', array( 'jquery' ), '1.0', false );
		wp_register_script('wdk-blueimp-slider', plugin_dir_url( __FILE__ ).'js/wdk-blueimp-slider.js', array( 'jquery' ), '1.0', false );
		wp_register_script('slick', plugin_dir_url( __FILE__ ).'js/slick/slick.min.js', array( 'jquery' ), '1.8', false );
		wp_register_script('ion.range-slider', plugin_dir_url( __FILE__ ).'js/ion.range-slider/js/ion.range-slider.min.js', array( 'jquery' ), '2.3.1', false );
		wp_register_script('wdk-slider-range', plugin_dir_url( __FILE__ ).'js/wdk-slider-range.js', array( 'jquery' ), '1.0', false );
		wp_register_script('select2', plugin_dir_url( __FILE__ ).'js/select2/js/select2.min.js', array( 'jquery' ), '4.0.13', false );
		wp_register_script('wdk-select2', plugin_dir_url( __FILE__ ).'js/wdk-select2.js', array( 'jquery' ), '4.0.13', false );
		wp_register_script('leaflet-googlemutant', plugin_dir_url( __FILE__ ).'js/leaflet-gridlayer-googlemutant/leaflet-googlemutant.js', array( 'jquery' ), '4.0.13', false );
		wp_register_script('wdk-ajax-loading-listings', plugin_dir_url( __FILE__ ).'js/wdk-ajax-loading-listings.js', array( 'jquery' ), '1.0', false );
		wp_register_script('leaflet-draw', plugin_dir_url( __FILE__ ).'js/leaflet-draw/leaflet.draw.js', array( 'jquery' ), '1.0', false );
		wp_register_script('wdk-map-rectangle', plugin_dir_url( __FILE__ ).'js/wdk-map-rectangle.js', array( 'jquery', 'leaflet-draw' ), '1.0', false );
		

		if( wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_popup')) {
			wp_register_script('wdk-popup-listings', plugin_dir_url( __FILE__ ).'js/wdk-popup-listings.js', array( 'jquery' ), '1.0', false );
			wp_enqueue_script('wdk-popup-listings');
        }
		
		$params = array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        );
		wp_register_script('wdk-treefield-dropdown', plugin_dir_url( __FILE__ ).'js/wdk_treefield_dropdown/wdk_treefield_dropdown.js', array( 'jquery' ), '1.0', false );
		wp_localize_script( 'wdk-treefield-dropdown', 'script_parameters', $params);

		wp_register_script('jquery-confirm', plugin_dir_url( __FILE__ ).'js/jquery-confirm/js/jquery-confirm.js', array( 'jquery' ), '3.3.4', false );

		$params = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'text' =>array(
				'price' => esc_html__('Price', 'wpdirectorykit'),
				'total_price' => esc_html__('Total', 'wpdirectorykit'),
				'loading' => esc_html__('Price loading...', 'wpdirectorykit'),
			),
        );
		wp_register_script('wdk-booking-calculator-price', plugin_dir_url( __FILE__ ).'js/wdk-booking-calculator-price.js', array( 'jquery' ), '1.0', false );
		wp_localize_script('wdk-booking-calculator-price', 'wdk_booking_script_parameters', $params);

		wp_deregister_script('wdk-scroll-mobile-swipe');
		wp_register_script('wdk-scroll-mobile-swipe', plugin_dir_url( __FILE__ ).'js/wdk-scroll-mobile-swipe.js', array( 'jquery' ), '1.1', false );
		wp_enqueue_script('wdk-scroll-mobile-swipe');
		 
		$params = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'format_date' => wdk_convert_date_format_js(get_option('date_format')),
			'format_datetime' => wdk_convert_date_format_js(get_option('date_format').' '.get_option('time_format')),
			'format_date_js' => wdk_convert_date_format_jquery(get_option('date_format')),
			'format_datetime_js' => wdk_convert_date_format_jquery(get_option('date_format').' '.get_option('time_format')),
			'settings_wdk_field_search_suggestion_disable' => (wdk_get_option('wdk_field_search_suggestion_disable')) ? 1 : 0,
        );
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpdirectorykit-public.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'script_parameters', $params);
		
		wp_register_style('jquery-ui', WPDIRECTORYKIT_URL. 'public/css/jquery-ui.css', array(), '1.12.1' );
		
		$custom_css = '.wdk-image:not(.media):not(.jsplaceholder){background-image: url('.esc_url(wdk_placeholder_image_src()).');background-size: cover;background-position: center; }';
		wp_add_inline_style( $this->plugin_name, $custom_css);
	}

	
	public function ajax_public()
	{
		$page = '';
		$function = '';

		if(isset($_POST['page']))$page = sanitize_text_field($_POST['page']);
		if(isset($_POST['function']))$function = sanitize_text_field($_POST['function']);

		/* protect access only to ajax controller */
		if($page != 'wdk_frontendajax' && $page != 'wdk_backendajax') {
			exit(esc_html__('Access denied','wdk-bookings'));
		} 

		$WMVC = &wdk_get_instance();
		$WMVC->load_controller($page, $function, array());
	}

}
