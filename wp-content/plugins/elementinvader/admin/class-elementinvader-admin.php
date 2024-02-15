<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       elementinvader.com 
 * @since      1.0.0
 *
 * @package    Elementinvader
 * @subpackage Elementinvader/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Elementinvader
 * @subpackage Elementinvader/admin
 * @author     ElementInvader <info@elementinvader.com >
 */
class Elementinvader_Admin {

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
		 * defined in Elementinvader_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Elementinvader_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/elementinvader-admin.css', array(), $this->version, 'all' );

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
		 * defined in Elementinvader_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Elementinvader_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/elementinvader-admin.js', array( 'jquery' ), $this->version.'.2', false );

		wp_enqueue_script( 'jquery-helpers', plugin_dir_url( __FILE__ ) . 'js/jquery.helpers.js', array( 'jquery' ), $this->version, false );
	}


	/**
	 * Admin AJAX
	 */

	public function elementinvader_action()
	{
		global $Winter_MVC_elementinvader;

		$page = '';
		$function = '';

		if(isset($_GET['page']))$page = wmvc_xss_clean($_GET['page']);
		if(isset($_GET['function']))$function = wmvc_xss_clean($_GET['function']);

		if(isset($_POST['page']))$page = wmvc_xss_clean($_POST['page']);
		if(isset($_POST['function']))$function = wmvc_xss_clean($_POST['function']);

		$Winter_MVC_elementinvader = new MVC_Loader(plugin_dir_path( __FILE__ ).'../');
		$Winter_MVC_elementinvader->load_helper('basic');
		$Winter_MVC_elementinvader->load_controller($page, $function, array());
	}

	/**
	 * Admin Page Display
	 */
	public function admin_page_display() {
		global $Winter_MVC_elementinvader;

		$page = '';
		$function = '';

		if(isset($_GET['page']))$page = wmvc_xss_clean($_GET['page']);
		if(isset($_GET['function']))$function = wmvc_xss_clean($_GET['function']);

		$Winter_MVC_elementinvader = new MVC_Loader(plugin_dir_path( __FILE__ ).'../');
		$Winter_MVC_elementinvader->load_helper('basic');
		$Winter_MVC_elementinvader->load_controller($page, $function, array());
		
	}

	function elementinvader_body_class( $classes ) {
		return "$classes elementinvader-page";
    }
    
    function modify_elementor_list_row_actions($actions, $post){
        // Check for your post type.
        if ( $post->post_type == "elementor_library" || $post->post_type == "envato_tk_templates"
			|| (get_post_meta( $post->ID, '_elementor_edit_mode', true ) && current_user_can( 'edit_pages' ) )) {
			
            // Build your links URL.
            $url = admin_url( 'admin.php?page=elementinvader&function=export_zip&post=' . $post->ID );

            $actions['elementinvader_export'] = '<a href="'.$url.'">'.esc_html( __( 'Export for ElementInvader', 'elementinvader' ) ).'</a>'; 
        }
    
        return $actions;
    }

	/**
	 * To add Plugin Menu and Settings page
	 */
	public function plugin_menu() {

		ob_start();
		//ob_flush();

		// Show menu only for approved admins
		$allowed_admins = get_option('elementinvader_allowed_admins');
		if(wmvc_user_in_role('administrator') || wmvc_user_in_role('super-admin'))
		if(is_array($allowed_admins) && count($allowed_admins) > 0)
		{
			if(!in_array(get_current_user_id(), $allowed_admins))
				return;
		}

		require_once ELEMENTINVADER_PATH . 'vendor/boo-settings-helper/class-boo-settings-helper.php';

		if(file_exists(get_template_directory().'/elementinvader/'))
		{
			add_menu_page(__('Element Invader Settings','elementinvader'), __('Element Invader','elementinvader'), 
				'manage_options', 'elementinvader', array($this, 'admin_page_display'),
				//plugin_dir_url( __FILE__ ) . 'resources/logo.png',
				'dashicons-schedule',
				32 );

			add_submenu_page('elementinvader', 
				__('Theme Layouts','elementinvader'), 
				__('Theme Layouts','elementinvader'),
				'manage_options', 'elementinvader', array($this, 'admin_page_display'));

			add_submenu_page('elementinvader', 
				__('Other Layouts','elementinvader'), 
				__('Other Layouts','elementinvader'),
				'manage_options', 'elementinvader_marketplace', array($this, 'admin_page_display'));

			add_submenu_page('elementinvader', 
				__('Contact Us','elementinvader'), 
				__('Contact Us','elementinvader'),
				'manage_options', 'elementinvader_contact', array($this, 'admin_page_display'));
		}
		else
		{
			add_menu_page(__('Element Invader Settings','elementinvader'), __('Element Invader','elementinvader'), 
				'manage_options', 'elementinvader_marketplace', array($this, 'admin_page_display'),
				//plugin_dir_url( __FILE__ ) . 'resources/logo.png',
				'dashicons-schedule',
				32 );

			add_submenu_page('elementinvader_marketplace', 
				__('Other Layouts','elementinvader'), 
				__('Other Layouts','elementinvader'),
				'manage_options', 'elementinvader_marketplace', array($this, 'admin_page_display'));

			add_submenu_page('elementinvader_marketplace', 
				__('Theme Layouts','elementinvader'), 
				__('Theme Layouts','elementinvader'),
				'manage_options', 'elementinvader', array($this, 'admin_page_display'));

			add_submenu_page('elementinvader_marketplace', 
				__('Contact Us','elementinvader'), 
				__('Contact Us','elementinvader'),
				'manage_options', 'elementinvader_contact', array($this, 'admin_page_display'));
		}
						


		// If not administrator
				
		$users_admins = get_users([ 'role__in' => [ 'administrator', 'super-admin' ] ]);
		$users_prepare = array();
		foreach($users_admins as $row)
		{
			$users_prepare[$row->ID] = $row->display_name;
		}

		$roles_prepare = array();
		$all_roles = wmvc_roles_array();

		foreach($all_roles as $row)
		{
			if($row['role'] == 'administrator')continue;

			$roles_prepare[$row['role']] = $row['role'].', '.$row['name'];
		}

		$general_class = 'elementinvader-pro';

		$elementinvader_settings = array(
			'tabs'     => true,
			'prefix'   => 'elementinvader_',
			'menu'     => array(
				'slug'       => 'elementinvader_settings',
				'page_title' => __( 'Element Invader Settings', 'elementinvader' ),
				'menu_title' => __( 'Settings ', 'elementinvader' ),
				'parent'     => 'elementinvader',
				'submenu'    => true
			),
			'sections' => array(
				//General Section
				array(
					'id'    => 'elementinvader_general_section',
					'title' => __( 'General Section', 'elementinvader' ),
					'desc'  => __( 'These are general settings', 'elementinvader' ),
				),
				//Logging level
				array(
					'id'    => 'elementinvader_log_level',
					'title' => __( 'Logging Level', 'elementinvader' ),
					'desc'  => __( 'These are Logging Level Settings', 'elementinvader' ),
				)
			),
			'fields'   => array(
				// fields for General section
				'elementinvader_general_section' => array(
					array(
						'id'    => 'allowed_admins',
						'label' => __( 'Only this admins allowed', 'elementinvader' ),
						'desc'  => __( 'Allow only this specific admins to see logs', 'elementinvader' ),
						'type'  => 'multicheck',
						'options' => $users_prepare
					),
					array(
						'id'    => 'checkbox_enable_winterlock_dash_styles',
						'label' => __( 'Enable ElementInvader Layout in complete dash', 'elementinvader' ),
						'desc'  => __( 'Make your WordPress Admin dashboard nicer', 'elementinvader' ),
						'type'  => 'checkbox',
						//'default' => '1',
					),
					array(
						'id'    => 'checkbox_disable_hints',
						'label' => __( 'Disable hints', 'elementinvader' ),
						'desc'  => __( 'Will hide questions and video guides in dashboard', 'elementinvader' ),
						'type'  => 'checkbox',
						'class'	=> $general_class,
					),
				),
				'elementinvader_log_level' => apply_filters( 'elementinvader/admin/settings/advance/fields',
					array(
						/*
						array(
							'id'    => 'test_field_xyz',
							'label' => __( 'Test Field xyz', 'elementinvader' ),
							'type'  => 'text',
							//'sanitize_callback' => 'absint'
						),
						*/
					)
				)
			)

		);

		//new Boo_Settings_Helper2( $elementinvader_settings );

		/*
		add_submenu_page('elementinvader', 
			__('Help','elementinvader'), 
			__('Help','elementinvader'),
			'manage_options', 'elementinvader_help', array($this, 'admin_page_display'));

		add_submenu_page('elementinvader', 
			__('Upgrade','elementinvader'), 
			__('Upgrade','elementinvader'),
			'manage_options', 'elementinvader_upgrade', array($this, 'admin_page_display'));

		*/

	}

}
