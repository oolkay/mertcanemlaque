<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       listing-themes.com
 * @since      1.0.0
 *
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/includes
 * @author     listing-themes.com <dev@listing-themes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Wpdirectorykit {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wpdirectorykit_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WPDIRECTORYKIT_VERSION' ) ) {
			$this->version = WPDIRECTORYKIT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wpdirectorykit';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
        
        $this->load_frameworks();
        $this->define_plugins_upgrade_hooks();
        $this->define_shortcode_hooks();
        $this->define_widget_hooks();
        $this->define_filter_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wpdirectorykit_Loader. Orchestrates the hooks of the plugin.
	 * - Wpdirectorykit_i18n. Defines internationalization functionality.
	 * - Wpdirectorykit_Admin. Defines all hooks for the admin area.
	 * - Wpdirectorykit_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpdirectorykit-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpdirectorykit-i18n.php';

        // Class asking for plugin review after some time
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpdirectorykit-review-request.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpdirectorykit-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpdirectorykit-public.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'tgm-pa/configuration.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'filters.php';
		
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'actions.php';
		
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'custom_post_type.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'custom_user_roles.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'custom_user_fields.php';

        // Load Winter MVC core
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/Winter_MVC/init.php';

        // Shortcodes
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes/shortcodes-init.php';

        // Dash Widgets
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'dash-widgets/dash-widgets-init.php';

        // Widgets
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/widgets-init.php';

        // Load Elementor Elements
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor-elements/elementor-init.php';

		$this->loader = new Wpdirectorykit_Loader();
		//global $Winter_MVC;
		
	}

    private function load_frameworks()
    {
        global $Winter_MVC_WDK;

        if(empty($Winter_MVC_WDK))
        {
            $Winter_MVC_WDK = new MVC_Loader(plugin_dir_path( __FILE__ ).'../');
        }
        else
        {
            $Winter_MVC_WDK->plugin_directory = plugin_dir_path( __FILE__ ).'../';
        }

        $Winter_MVC_WDK->load_helper('basic');
    }


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wpdirectorykit_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wpdirectorykit_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wpdirectorykit_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
        /**
		 * Adding Plugin Admin Menu
		 */
		$this->loader->add_action(
			'admin_menu',
			$plugin_admin,
			'plugin_menu'
        );

        $this->loader->add_action(
			'wp_ajax_wdk_admin_action',
			$plugin_admin,
			'ajax_admin'
		);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wpdirectorykit_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		
        $this->loader->add_action(
			'wp_ajax_wdk_public_action',
			$plugin_public,
			'ajax_public'
		);
		
        $this->loader->add_action(
			'wp_ajax_nopriv_wdk_public_action',
			$plugin_public,
			'ajax_public'
		);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wpdirectorykit_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    public function define_plugins_upgrade_hooks()
	{
		require_once  plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpdirectorykit-activator.php';

        $this->loader->add_action( 'plugins_loaded', 'Wpdirectorykit_Activator', 'plugins_loaded' );

		//$Winter_MVC_WDK = new MVC_Loader(plugin_dir_path( __FILE__ ).'../');
		//$Winter_MVC_WDK->load_helper('basic');
		
        /*
        $this->loader->add_action(
			'wp_ajax_nopriv_activitytime_action',
			$this,
			'activitytime_action'
        );
        
        $this->loader->add_action(
			'wp_ajax_activitytime_action',
			$this,
			'activitytime_action'
        );
        
        $this->loader->add_action(
			'wp_ajax_activitytime_mvc_action',
			$this,
			'activitytime_mvc_action'
        );*/

    }
    
    public function define_shortcode_hooks()
    {
        //require(plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes/xxx.php');

    }

    public function define_widget_hooks()
    {
        //require(plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/xxx.php');

    }

    public function define_filter_hooks()
    {
        $this->loader->add_filter( 'ajax_query_attachments_args', $this, 'show_current_user_attachments' );
    }

    function show_current_user_attachments( $query ) {
        $user_id = get_current_user_id();
                if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts
            ') ) {
                    $query['author'] = $user_id;
                }
        return $query;
    } 

}
