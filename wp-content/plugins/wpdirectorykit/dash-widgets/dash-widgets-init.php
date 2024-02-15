<?php
namespace Wdk\DashWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WDK_DashWidgets {

    protected $data = array();
    protected $version = 1.0;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( ) {}

    /**
     * Start Elementor Addon
     */
    public function run() { 

		add_action( 'admin_enqueue_scripts',[ $this, 'enqueue_styles' ]);
		add_action( 'admin_enqueue_scripts',[ $this, 'enqueue_scripts' ]);

        $this->includes();
        $this->add_widgets();

		do_action('wpdirectorykit/dash-widgets/run');

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
        wp_enqueue_style( 'dash-widgets-statistics-listings', plugin_dir_url( __FILE__ ) . 'assets/css/dash-widgets-statistics-listings.css' );
        wp_enqueue_style( 'dash-widgets-statistics-earning', plugin_dir_url( __FILE__ ) . 'assets/css/dash-widgets-statistics-earning.css' );
        wp_enqueue_style( 'dash-widgets-statistics-usage', plugin_dir_url( __FILE__ ) . 'assets/css/dash-widgets-statistics-usage.css' );
        wp_enqueue_style( 'wdk-dash-widgets-news', plugin_dir_url( __FILE__ ) . 'assets/css/wdk-news.css' );
        wp_enqueue_style( 'wdk-dash-widgets-list-listings', plugin_dir_url( __FILE__ ) . 'assets/css/wdk-latest-listings.css' );
        wp_enqueue_style( 'wdk-dash-widgets-map-listings', plugin_dir_url( __FILE__ ) . 'assets/css/wdk-map-listings.css' );
        wp_enqueue_style( 'wdk-dash-widgets-main', plugin_dir_url( __FILE__ ) . 'assets/css/wdk-main.css' );
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

        $params = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'empty_results' => esc_html__('Loading issue, please check news on our website','wpdirectorykit'),
        );

        wp_register_script( 'wdk-dash-widgets-news', plugin_dir_url( __FILE__ ) . 'assets/js/wdk-widget-news.js', array(), $this->version, true );
        wp_localize_script('wdk-dash-widgets-news', 'script_parameters', $params);
        wp_register_script( 'wdk-dash-widgets-map-listings', plugin_dir_url( __FILE__ ) . 'assets/js/wdk-widget-map-listings.js', array(), $this->version, true );
        wp_enqueue_script( 'wdk-dash-widgets-main', plugin_dir_url( __FILE__ ) . 'assets/js/wdk-main.js', array(), $this->version, true );
    }

    /**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
    */
	private function includes() {
		require_once plugin_dir_path( __FILE__ ) . '/dash-widgets-news.php';
		require_once plugin_dir_path( __FILE__ ) . '/dash-widgets-list-listings.php';
		require_once plugin_dir_path( __FILE__ ) . '/dash-widgets-map-listings.php';
		require_once plugin_dir_path( __FILE__ ) . '/dash-widgets-statistics-usage.php';
		require_once plugin_dir_path( __FILE__ ) . '/dash-widgets-statistics-earning.php';
		require_once plugin_dir_path( __FILE__ ) . '/dash-widgets-statistics-listings.php';

		do_action('wpdirectorykit/dash-widgets/includes');
    }

    /**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
    */
	private function add_widgets() {

		add_action('wp_dashboard_setup', array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_News','init') );
		add_action('wp_dashboard_setup', array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Latest_Listings','init') );
		add_action('wp_dashboard_setup', array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Map_Listings','init') );
		add_action('wp_dashboard_setup', array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Usage','init') );
		add_action('wp_dashboard_setup', array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Earning','init') );
		add_action('wp_dashboard_setup', array('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Listings','init') );

		do_action('wpdirectorykit/dash-widgets/add_widgets');
    }


    /**
     * Hook to wp_dashboard_setup to add the widget.
     */
    public static function init() {}

    /**
     * Load the widget code
     */
    public static function widget() { }

    /**
     * Load widget config code.
     *
     * This is what will display when an admin clicks
     */
    public static function config() {
        wp_redirect(''); exit;
    }

    /**
     * Gets the options for a widget of the specified name.
     *
     * @param string $widget_id Optional. If provided, will only get options for the specified widget.
     * @return array An associative array containing the widget's options and values. False if no opts.
     */
    public static function get_dashboard_widget_options( $widget_id='' )
    {
        //Fetch ALL dashboard widget options from the db...
        $opts = get_option( 'dashboard_widget_options' );

        //If no widget is specified, return everything
        if ( empty( $widget_id ) )
            return $opts;

        //If we request a widget and it exists, return it
        if ( isset( $opts[$widget_id] ) )
            return $opts[$widget_id];

        //Something went wrong...
        return false;
    }

    /**
     * Gets one specific option for the specified widget.
     * @param $widget_id
     * @param $option
     * @param null $default
     *
     * @return string
     */
    public static function get_dashboard_widget_option( $widget_id, $option, $default=NULL ) {

        $opts = self::get_dashboard_widget_options($widget_id);

        //If widget opts dont exist, return false
        if ( ! $opts )
            return false;

        //Otherwise fetch the option or use default
        if ( isset( $opts[$option] ) && ! empty($opts[$option]) )
            return $opts[$option];
        else
            return ( isset($default) ) ? $default : false;

    }

    /**
     * Saves an array of options for a single dashboard widget to the database.
     * Can also be used to define default values for a widget.
     *
     * @param string $widget_id The name of the widget being updated
     * @param array $args An associative array of options being saved.
     * @param bool $add_only If true, options will not be added if widget options already exist
     */
    public static function update_dashboard_widget_options( $widget_id , $args=array(), $add_only=false )
    {
        //Fetch ALL dashboard widget options from the db...
        $opts = get_option( 'dashboard_widget_options' );

        if(!$opts) $opts  = array();

        //Get just our widget's options, or set empty array
        $w_opts = ( isset( $opts[$widget_id] ) ) ? $opts[$widget_id] : array();

        if ( $add_only ) {
            //Flesh out any missing options (existing ones overwrite new ones)
            $opts[$widget_id] = array_merge($args,$w_opts);
        }
        else {
            //Merge new options with existing ones, and add it back to the widgets array
            $opts[$widget_id] = array_merge($w_opts,$args);
        }

        //Save the entire widgets array back to the db
        return update_option('dashboard_widget_options', $opts);
    }

    public static function view($view_file = '', $element = array(), $print = false)
    {
        if(empty($view_file)) return false;
        $file = false;
        if(is_child_theme() && file_exists(get_stylesheet_directory().'/wpdirectorykit/dash-widgets/views/'.$view_file.'.php'))
        {
            $file = get_stylesheet_directory().'/wpdirectorykit/dash-widgets/views/'.$view_file.'.php';
        }
        elseif(file_exists(get_template_directory().'/wpdirectorykit/dash-widgets/views/'.$view_file.'.php'))
        {
            $file = get_template_directory().'/wpdirectorykit/dash-widgets/views/'.$view_file.'.php';
        }
        elseif(file_exists(WPDIRECTORYKIT_PATH.'dash-widgets/views/'.$view_file.'.php'))
        {
            $file = WPDIRECTORYKIT_PATH.'dash-widgets/views/'.$view_file.'.php';
        }

        if($file)
        {
            extract($element);
            if($print) {
                include $file;
            } else {
                ob_start();
                include $file;
                return ob_get_clean();
            }
        }
        else
        {
            if($print) {
                echo 'View file not found in: '.esc_html(WPDIRECTORYKIT_PATH.'dash-widgets/views/'.$view_file.'.php');
            } else {
                return 'View file not found in: '.WPDIRECTORYKIT_PATH.'dash-widgets/views/'.$view_file.'.php';
            } 
        }
    }

}

$WDK_DashWidgets = new WDK_DashWidgets( );
$WDK_DashWidgets->run();

?>