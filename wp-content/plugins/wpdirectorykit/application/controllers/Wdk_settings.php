<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

/*

actions inlcuded
do_action('wdk/settings/import/run', $data);  // form data

apply_filters('wdk/settings/import/run/fields', $this->data['fields']); // fields
apply_filters('wdk/settings/import/run/post', $data); 

*/

class Wdk_settings extends Winter_MVC_Controller {
    public $import_log = '';
    public $info_log_message = '';
    public $import_images_dir = WPDIRECTORYKIT_PATH.'demo-data/images/';
    public $import_xml_file = '';
    public $import_xml_file_locations =  WPDIRECTORYKIT_PATH.'demo-data/locations.xml';

	public function __construct(){
		parent::__construct();
	}
    
    // Edit listing method
	public function index()
	{

        $this->load->model('settings_m');
        $this->data['db_data'] = NULL;
        $this->data['form'] = &$this->form;
        $this->data['fields'] = $this->settings_m->fields_list;
        $this->data['fields_list_tabs'] = $this->settings_m->fields_list_tabs;

        $this->form->add_error_message('wdk_slug_format', __('Custom Listing Preview Page Slug allowed only a-z 0-9 - _', 'wpdirectorykit'));

        if($this->form->run($this->data['fields']))
        {

            // Check _wpnonce
            check_admin_referer( 'wdk-settings-edit', '_wpnonce' );

            // Save procedure for basic data
    
            $data = $this->settings_m->prepare_data(wdk_get_post(), $this->data['fields']);
            $wdk_slug_listing_preview_page = get_option('wdk_slug_listing_preview_page');

            // Save standard wp post
            foreach($data as $key => $val)
            {
                update_option( $key, $val, TRUE);
            }  

            if(isset($data['wdk_slug_listing_preview_page']) && $wdk_slug_listing_preview_page != $data['wdk_slug_listing_preview_page']) {
                update_option('wdk_slug_listing_preview_page_changed', 1);
            }

            // redirect
            if(empty($listing_post_id) && !empty($id))
            {
                //wp_redirect(admin_url("admin.php?page=wdk_settings&is_updated=true"));
                exit;
            }
                
        }

        // fetch data, after update/insert to get updated last data
        $fields_data = $this->settings_m->get();

        /* slug udpate rules*/
        //flush_rewrite_rules();

        foreach($fields_data as $field)
        {
            $this->data['db_data'][$field->option_name] = $field->option_value;
        }

        $this->load->view('wdk_settings/index', $this->data);
    }

    function reset_data () {

        wdk_access_check('settings_m', 1);

        // Check _wpnonce
        check_admin_referer( 'reset-data', '_wpnonce' );
        
        $this->load->model('location_m');
        $this->load->model('category_m');
        $this->load->model('field_m');
        $this->load->model('listingfield_m');
        $this->load->model('listing_m');
        $this->load->model('resultitem_m');
        $this->load->model('searchform_m');

        /* remove fields */
        $this->db->delete($this->location_m->_table_name);
        /* end remove fields */

        /* remove fields */
        $this->db->delete($this->category_m->_table_name);
        /* end remove fields */

        /* remove fields */
        $this->db->delete($this->location_m->_table_name);
        /* end remove fields */

        /* remove fields */
        $this->db->delete($this->field_m->_table_name);
        $field_names = $this->listingfield_m->get_available_fields();
        foreach($field_names as $field_name => $value){
            if(strpos($field_name, 'field_') ===FALSE) continue;
            $this->db->query('ALTER TABLE '.$this->listingfield_m->_table_name.' DROP COLUMN '.$field_name.'');
        }   
        /* end remove fields */

        /* remove listings */
        $this->db->delete($this->listing_m->_table_name);
        $this->db->delete($this->listingfield_m->_table_name);
        /* end remove listings */

        /* reset autoincrement */
        $this->db->query('TRUNCATE TABLE `'.$this->location_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->category_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->field_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->listingfield_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->listing_m->_table_name.'`');
        /* end reset autoincrement */

        /* remove listings */
        $this->db->delete($this->resultitem_m->_table_name);
        $this->db->delete($this->searchform_m->_table_name);
        /* end remove listings */

        /* reset autoincrement */
        $this->db->query('TRUNCATE TABLE `'.$this->resultitem_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->searchform_m->_table_name.'`');

        $wdk_posts = get_posts( array( 'post_type' => 'wdk-listing', 'numberposts' => -1));
        foreach( $wdk_posts as $post ) {
            // Delete's each post.
            wp_delete_post( $post->ID, true);
            // Set to False if you want to send them to Trash.
        }

        $redirect_url = admin_url("admin.php?page=wdk_settings&is_updated");
        if(isset($_GET['redirect_url']) && strpos($_GET['redirect_url'], 'http') === FALSE && strpos($_GET['redirect_url'], '//') === FALSE)
            $redirect_url = admin_url(sanitize_text_field($_GET['redirect_url']));

        wp_redirect($redirect_url);
        exit;
    }
      
    // Import demo data listing method
	public function import_demo()
	{
        wdk_access_check('settings_m', 1);
        $this->load->model('field_m');
        $this->load->model('listingfield_m');
        $this->load->model('listing_m');
        $this->load->model('category_m');
        $this->load->model('location_m');
        $this->load->model('searchform_m');
        $this->load->model('resultitem_m');
        $this->load->model('listingusers_m');

        $this->data['installed'] = false;
        if($this->field_m->get() || $this->listingfield_m->get() || $this->listing_m->get() || $this->category_m->get() || $this->location_m->get())
            $this->data['installed'] = true;
            
        $this->data['required_plugins'] = false;
        if(!function_exists('eli_installer') || !function_exists('run_elementinvader') || !is_plugin_active('elementor/elementor.php'))
            $this->data['required_plugins'] = true;
        
        $this->data['db_data'] = NULL;
        $this->data['fields'] = array( 
            array('field' => 'import_locations', 'field_label' => __('Locations', 'wpdirectorykit'), 'hint' => __('All Locations will be removed and import demo locations', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'import_categories', 'field_label' => __('Categories', 'wpdirectorykit'), 'hint' => __('All categories will be removed and import demo categories', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'import_fields', 'field_label' => __('Fields', 'wpdirectorykit'), 'hint' => __('All fiedls will be removed and import demo fields', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'import_listings', 'field_label' => __('Listings', 'wpdirectorykit'), 'hint' => __('All listings will be removed and import demo listings', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'import_visual_data', 'field_label' => __('Search Form & Result Card', 'wpdirectorykit'), 'hint' => __('Import demo Search Form & Result Card', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'import_page_listing_preview', 'field_label' => __('Listing Preview Page', 'wpdirectorykit'), 'hint' => __('Import demo Listing Preivew Page', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
            array('field' => 'import_page_results', 'field_label' => __('Results Page', 'wpdirectorykit'), 'hint' => __('Import demo Results Page', 'wpdirectorykit'), 'field_type' => 'CHECKBOX', 'rules' => ''),
        );
     
        $multipurpose_values = array('' => __('Not Selected', 'wpdirectorykit'));
        if(file_exists(WPDIRECTORYKIT_PATH.'demo-data/'))
        {
            $files = array();
            $dir = WPDIRECTORYKIT_PATH.'demo-data/';
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if($file  == 'locations.xml') continue;
                        if(strpos($file, '.xml') !== false && strpos($file, '~') === false)
                            $multipurpose_values[$file] = ucwords(str_replace(array('_', '-', '.xml'), ' ', $file));
                    }
                    closedir($dh);
                }
            }
        }

        ksort($multipurpose_values); 
        if(count($multipurpose_values) > 0)
            $this->data['fields']['multipurpose'] = array('field'=>'multipurpose', 'field_label'=>__('Portal version', 'wpdirectorykit'), 'field_type'=>'DROPDOWN', 'rules'=>'required', 'values'=>$multipurpose_values);

            
        if(has_filter('wdk/settings/import/run/fields'))
            $this->data['fields'] = apply_filters('wdk/settings/import/run/fields', $this->data['fields']);

        $this->data['form'] = &$this->form;
        ini_set('max_execution_time', 900);           
        
        // [/Check requirements]
        
        foreach($this->data['fields'] as $field)
        {
            $this->data['db_data'][$field['field']] = 1;
        }

        if($this->field_m->get()) {
            $this->data['db_data']['import_fields'] = 0;
        }
        if($this->listing_m->get()) {
            $this->data['db_data']['import_listings'] = 0;
        }
        if($this->category_m->get()) {
            $this->data['db_data']['import_categories'] = 0;
        }
        if($this->location_m->get()) {
            $this->data['db_data']['import_locations'] = 0;
        }

        if($this->searchform_m->get() || $this->resultitem_m->get()){
            $this->data['db_data']['import_visual_data'] = 0;
        }

        if((get_option('wdk_listing_page')) && get_post_status(get_option('wdk_listing_page')) =='publish'){
            $this->data['db_data']['import_page_listing_preview'] = 0;
        }

        if((get_option('wdk_results_page')) && get_post_status(get_option('wdk_results_page')) == 'publish'){
            $this->data['db_data']['import_page_results'] = 0;
        }

        $this->data['db_data']['multipurpose'] = 'real-estate.xml';

        if(isset($_GET['multipurpose']))
            $this->data['db_data']['multipurpose'] = sanitize_text_field($_GET['multipurpose']);

        $this->data['import_log'] = '';
        $this->data['info_log_message'] = '';
        
        $rules = array(
            array(
                'field' => 'import_locations',
                'label' => __('Locations', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'import_categories',
                'label' => __('Categories', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'import_fields',
                'label' => __('Fields', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'import_listings',
                'label' => __('Listings', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'import_visual_data',
                'label' => __('Search Form & Result Card', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'import_page_listing_preview',
                'label' => __('Listing Preview Page', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'import_page_results',
                'label' => __('Results Page', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'multipurpose',
                'label' => __('Purpose', 'wpdirectorykit'),
                'rules' => 'required'
            ),
        );    

        $plugin = 'elementor/elementor.php';
        if (in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ))) && !class_exists('Elementor\Plugin') ) {
            $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Your Elementor Plugin is not fully active, usually this happen because of old PHP version on server, in such case eventually you can try older Elementor Version or Update PHP on your server').'</div>';
            $this->data['required_plugins'] = true;
        }

        if(!$this->data['required_plugins'])
        if($this->form->run($rules))
        {
            update_option('wdk_install_images_sizes_disable', 1);

            // Check _wpnonce
            check_admin_referer( 'wdk-settings_import', '_wpnonce' );
            
            // Save procedure for basic data
            $data = $this->input->post();

            $this->import_xml_file = WPDIRECTORYKIT_PATH.'demo-data/'.$data['multipurpose'];

            if(has_filter('wdk/settings/import/run/post'))
                $data = apply_filters('wdk/settings/import/run/post', $data);
            
            $this->import_images_dir = apply_filters('wdk/settings/import/run/import_images_dir', $this->import_images_dir, $data);
            $this->import_xml_file = apply_filters('wdk/settings/import/run/import_xml_file', $this->import_xml_file, $data);
            $this->import_xml_file_locations = apply_filters('wdk/settings/import/run/import_xml_file_locations', $this->import_xml_file_locations, $data);
            
            $this->import_settings();

            if( !empty($data['import_locations'])) {
                $this->import_locations();
            }
            
            if( !empty($data['import_categories'])) {
                $this->import_categories($data['multipurpose']);
            }

            if( !empty($data['import_fields'])) {
                $this->import_fields($data['multipurpose']);
            }

            if( !empty($data['import_listings'])) {  
                $this->import_listings($data['multipurpose']);
            }

            if( !empty($data['import_visual_data'])) {
                $this->import_visual_data($data['multipurpose']);
            }

            if( !empty($data['import_page_listing_preview'])) {
                $this->demo_page_listing($data['multipurpose']);
            }

            if( !empty($data['import_page_results'])) {
                $this->demo_page_results($data['multipurpose']);
            }

            $this->replace_content_data($data['multipurpose']);

            $this->data['info_log_message']  .= '<div class="alert alert-info" role="alert">'.esc_html__('Import completed successfully', 'wpdirectorykit').', <a href="'. esc_url((wdk_get_option('wdk_results_page')) ? get_permalink(wdk_get_option('wdk_results_page')): home_url()).'">'.__('Check your page now', 'wpdirectorykit').'</a></div>';

            $this->data['info_log_message']  = apply_filters('wdk/settings/import/run/info_log_message', $this->data['info_log_message'] , $data);
            
            do_action('wdk/settings/import/run', $data);
            update_option('wdk_install_images_sizes_disable', 0);
        } 

        $this->load->view('wdk_install/index', $this->data);
    }

    // Import demo data listing method
	public function remove()
	{

        wdk_access_check('settings_m', 1);
 
        // Check _wpnonce
        check_admin_referer( 'remove-data', '_wpnonce' );

        $this->load->model('field_m');
        $this->load->model('listingfield_m');
        $this->load->model('listing_m');
        $this->load->model('category_m');
        $this->load->model('location_m');

        $this->data['data_log'] = '';
      
        $this->load->model('location_m');
        $this->load->model('category_m');
        $this->load->model('field_m');
        $this->load->model('listingfield_m');
        $this->load->model('listing_m');
        $this->load->model('resultitem_m');
        $this->load->model('searchform_m');
        $this->load->model('dependfields_m');

        /* remove fields */
        $this->db->delete($this->location_m->_table_name);

        $this->data['data_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Locations removed').'</div>';
        /* end remove fields */

        /* remove fields */
        $this->db->delete($this->category_m->_table_name);
        $this->db->delete($this->dependfields_m->_table_name);

        $this->data['data_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Categories removed').'</div>';
        /* end remove fields */

        /* remove fields */
        $this->db->delete($this->field_m->_table_name);
        $field_names = $this->listingfield_m->get_available_fields();
        foreach($field_names as $field_name => $value){
            if(strpos($field_name, 'field_') ===FALSE) continue;
            $this->db->query('ALTER TABLE '.$this->listingfield_m->_table_name.' DROP COLUMN '.$field_name.'');
        }   

        $this->data['data_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Fields removed').'</div>';
        /* end remove fields */

        /* remove listings */
        $this->db->delete($this->listing_m->_table_name);
        $this->db->delete($this->listingfield_m->_table_name);

        $this->data['data_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Listings removed').'</div>';
        /* end remove listings */


        /* remove listings */
        $this->db->delete($this->resultitem_m->_table_name);
        $this->db->delete($this->searchform_m->_table_name);
        $this->data['data_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Result Card And Search form removed').'</div>';
        /* end remove listings */

        
        /* reset autoincrement */
        $this->db->query('TRUNCATE TABLE `'.$this->location_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->category_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->field_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->dependfields_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->listingfield_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->listing_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->resultitem_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->searchform_m->_table_name.'`');

        $this->data['data_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Tables reset').'</div>';
        /* end reset autoincrement */

        $wdk_posts = get_posts( array( 'post_type' => 'wdk-listing', 'numberposts' => -1));
        foreach( $wdk_posts as $post ) {
            // Delete's each post.
            wp_delete_post( $post->ID, true);
            // Set to False if you want to send them to Trash.
        }

        if(isset($_GET['redirect_url']) && strpos($_GET['redirect_url'], 'http') === FALSE && strpos($_GET['redirect_url'], '//') === FALSE){
            if(wmvc_xss_clean($_GET['redirect_url']) =='on_install') {
                $redirect_url = admin_url("admin.php?page=wdk_settings&function=import_demo&is_updated&multipurpose=".wmvc_show_data('multipurpose', $_GET, ''));
            } else {
                $redirect_url = admin_url(sanitize_text_field($_GET['redirect_url']));
            }
            wp_redirect($redirect_url);
        }

        $this->load->view('wdk_install/remove_log', $this->data);
    }

    /* add demo pages */
    private function demo_pages($purpose = '') {

        if(get_option('wdk_listing_page') || get_option('wdk_results_page')) return true;

        add_action('wpdirectorykit/elementor-elements/register_widget', function($self){
            $self->add_widget('Wdk\Elementor\Extensions\WdkContactFormExt');
        });
                
        add_action('wpdirectorykit/elementor-elements/register_widget', function(){
            add_action('eli/includes', function(){
                require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/class-contact-form.php';
            });

            add_action('eli/register_widget', function(){
                $object = new Wdk\Elementor\Extensions\WdkContactFormExt();
                \Elementor\Plugin::instance()->widgets_manager->register( $object );
            });
        });

        add_action('wpdirectorykit/elementor-elements/register_widget', function($self){
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldLabel');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldValue');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldImages');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldIcon');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingSimilarListings');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingSlider');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingFieldsSection');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingMap');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListinAgent');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingAgentField');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingAgentAvatar');
        });


        // Import elementor templates
        $page_listing_preview = $this->create_page(esc_html__('Listing Preview', 'wpdirectorykit'), '', 'elementor_canvas');
        $this->elementor_assign($page_listing_preview->ID, 'page-listing-preview.json');

        $page_results = $this->create_page(esc_html__('Results Listings', 'wpdirectorykit'), '', 'elementor_canvas');
        $this->elementor_assign($page_results->ID, 'page-results-listings.json');

 
        $menus = get_registered_nav_menus();
        // first menu defined by template
        $first_menu = key($menus);

        if ( has_nav_menu($first_menu) ) {    
            $menus = wp_get_nav_menus();
            $menu_id = (int)$menus[0]->term_id;
        } else {
            wp_delete_nav_menu('Primary menu');
            $menu_name = 'Primary menu';
            $menu_exists = wp_get_nav_menu_object( $menu_name );
            $menu_term = $menu_exists;
            $menu_id = wp_create_nav_menu($menu_name);
        }
       
        // first menu defined by template
        if (!empty($menu_id)) {
            $itemData =  array(
                'menu-item-object-id' => $page_results->ID,
                'menu-item-title' =>  esc_html__('Grid Map', 'wpdirectorykit'),
                'menu-item-parent-id' => 0,
                'menu-item-position' => 3,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status'    => 'publish'
            );
         
            wp_update_nav_menu_item((int)$menu_id, 0, $itemData);

            // assign menu to top menu
            $locations = get_theme_mod( 'nav_menu_locations' );
            $locations[$first_menu] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }

        // Assign page.
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $page_results->ID );
        
        if($page_listing_preview)
            update_option( 'wdk_listing_page', $page_listing_preview->ID, TRUE);
        
        if($page_results)
            update_option( 'wdk_results_page', $page_results->ID, TRUE);

        $this->replace_content_data($purpose);

        return true;
    }

        
    private function add_demo_user($username, $name, $surname, $email_address='', $type='wdk_agent', $password=NULL, $meta_data = array())
    {
        
        if ( username_exists( $username )) {
            return username_exists( $username );
        }

        if ( email_exists($email_address)) {
            return email_exists($email_address);
        }

        if ( empty($email_address)) {
            return false;
        }
        
        // Generate the password and create the user
        if(empty($password))
            $password = wp_generate_password( 12, false );

        $user_id = wp_create_user( $username, $password, $email_address );
        
        // Set the nickname
        wp_update_user(
            array(
                'ID'          =>    $user_id,
                'nickname'    =>    $name.' '.$surname,
                'first_name'  =>    $name,
                'last_name'   =>    $surname,
                'description' =>    'Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a sit amet maurs. Morbi accumsan ipsum velit. Nam nec tellus a odio tincidunt auctor a ornare odi non mauris vitae erat consequat Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit.',
                'display_name'=>    $name.' '.$surname,
                'user_url'    =>    '' , 
                'admin_bar_front'    =>    0 , 
                'wdk_phone'    =>    '123456789'  
            )
        );

        $user = new WP_User( $user_id );
        $user->set_role($type);

        if(!empty($meta_data)) 
            foreach ($meta_data as $meta_key => $meta_value){
                update_user_meta( $user_id, $meta_key, wmvc_xss_clean($meta_value) );
            }

        update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
        update_user_meta( $user_id, 'show_admin_bar_admin', 'false' );

        return $user_id;
    }

    private function replace_content_data($purpose = '') {
        /* Replace Links */
        /* login */
        $from = 'https://www.wpdirectorykit.com/nexproperty/wp-admin/wp-login.php';
        $to = get_admin_url();
        $this->replace_meta($from, $to);
        

        $from = 'https://www.wpdirectorykit.com/nexproperty';
        $to = get_home_url();
        $this->replace_meta($from, $to);
        
        /* homepage */
        $from = '2020';
        $to = date('Y');
        $this->replace_meta($from, $to);


        /* replace in db */
        if(file_exists($this->import_xml_file)) {
            $dom_array = $this->xmlstr_to_array(file_get_contents($this->import_xml_file));
            if(isset($dom_array['replace']) && !empty($dom_array['replace'])) {
                if(!empty($dom_array['replace']['text'])) {
                    foreach($dom_array['replace']['text'] as $string){
                        if(!isset($string['@attributes']) || !isset($string['@attributes']['from']) || !isset($string['@attributes']['to'])) continue;
                        if(empty($string['@attributes']['from'])  || empty( $string['@attributes']['to'])) continue;
                        $this->replace_meta($string['@attributes']['from'], $string['@attributes']['to']);
                    }
                }
            } else {

            }
        }
        return true;

    }

    /* add demo listing preview page */
    private function demo_page_listing($purpose = '') {

        if((get_option('wdk_listing_page')) && get_post_status(get_option('wdk_listing_page')) =='publish'){
            $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Listing Preview Page already exists').'</div>';
            return false;
        }

        add_action('wpdirectorykit/elementor-elements/register_widget', function($self){
            $self->add_widget('Wdk\Elementor\Extensions\WdkContactFormExt');
        });
                
        add_action('wpdirectorykit/elementor-elements/register_widget', function(){
            add_action('eli/includes', function(){
                require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/class-contact-form.php';
            });

            add_action('eli/register_widget', function(){
                $object = new Wdk\Elementor\Extensions\WdkContactFormExt();
                \Elementor\Plugin::instance()->widgets_manager->register( $object );
            });
        });

        add_action('wpdirectorykit/elementor-elements/register_widget', function($self){
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldLabel');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldValue');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldImages');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldIcon');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingSimilarListings');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingSlider');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingFieldsSection');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingMap');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListinAgent');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingAgentField');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingAgentAvatar');
        });

        // Import elementor templates
        $page_listing_preview = $this->create_page(esc_html__('Listing Preview', 'wpdirectorykit'), '', 'elementor_canvas');
        $this->elementor_assign($page_listing_preview->ID, 'page-listing-preview.json');

        // Assign page.
        if($page_listing_preview)
            update_option( 'wdk_listing_page', $page_listing_preview->ID, TRUE);
        
        $this->data['import_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Listing Preview Page imported').'</div>';

        return true;
    }

    /* add demo results page */
    private function demo_page_results($purpose = '') {

        if((get_option('wdk_results_page')) && get_post_status(get_option('wdk_results_page')) == 'publish'){
            $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Results Listings Page already exists').'</div>';
            return false;
        }

        add_action('wpdirectorykit/elementor-elements/register_widget', function($self){
            $self->add_widget('Wdk\Elementor\Extensions\WdkContactFormExt');
        });
                
        add_action('wpdirectorykit/elementor-elements/register_widget', function(){
            add_action('eli/includes', function(){
                require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/class-contact-form.php';
            });

            add_action('eli/register_widget', function(){
                $object = new Wdk\Elementor\Extensions\WdkContactFormExt();
                \Elementor\Plugin::instance()->widgets_manager->register( $object );
            });
        });

        add_action('wpdirectorykit/elementor-elements/register_widget', function($self){
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldLabel');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldValue');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldImages');
            $self->add_widget('Wdk\Elementor\Widgets\WdkFieldIcon');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingSimilarListings');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingSlider');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingFieldsSection');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingMap');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListinAgent');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingAgentField');
            $self->add_widget('Wdk\Elementor\Widgets\WdkListingAgentAvatar');
        });

        $page_results = $this->create_page(esc_html__('Results Listings', 'wpdirectorykit'), '', 'elementor_canvas');
        $this->elementor_assign($page_results->ID, 'page-results-listings.json');

        // Assign page.
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $page_results->ID );
        
        update_option( 'wdk_results_page', $page_results->ID, TRUE);
        
        $this->data['import_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Results Listings Page imported').'</div>';

        
        $menus = get_registered_nav_menus();
        // first menu defined by template
        $first_menu = key($menus);

        if ( has_nav_menu($first_menu) ) {    
            $menus = wp_get_nav_menus();
            $menu_id = (int)$menus[0]->term_id;
        } else {
            wp_delete_nav_menu('Primary menu');
            $menu_name = 'Primary menu';
            $menu_exists = wp_get_nav_menu_object( $menu_name );
            $menu_term = $menu_exists;
            $menu_id = wp_create_nav_menu($menu_name);
        }
       
        // first menu defined by template
        if (!empty($menu_id)) {
            $itemData =  array(
                'menu-item-object-id' => $page_results->ID,
                'menu-item-title' =>  esc_html__('Grid Map', 'wpdirectorykit'),
                'menu-item-parent-id' => 0,
                'menu-item-position' => 3,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status'    => 'publish'
            );
         
            wp_update_nav_menu_item((int)$menu_id, 0, $itemData);

            // assign menu to top menu
            $locations = get_theme_mod( 'nav_menu_locations' );
            $locations[$first_menu] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }

        return true;
    }

    /* Create Page */
    private function create_page($post_title, $post_content = '', $post_template = NULL, $post_parent=0)
    {
        $post = wdk_page_by_title($post_title, 'OBJECT', 'page' );
        
        $post_id = NULL;
        
        // Delete posts and rebuild
        if(!empty($post))
        {
            wp_delete_post($post->ID, true);
            $post=NULL;
        }
        
        if(!empty($post))
        $post_id   = $post->ID;

        if(empty($post_id))
        {
            $error_obj = NULL;
            $post_insert = array(
                'post_title'    => wp_strip_all_tags( $post_title ),
                'post_content'  => $post_content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_author'   => get_current_user_id(),
                'post_category' => array(1,2),
                'page_template' => $post_template,
                'post_parent'   => $post_parent
            );
            $post_id = wp_insert_post( $post_insert, $error_obj );
        }

        $post_insert = get_post( $post_id );
        
        return $post_insert;
    }

    /* Elementor Import Template */
    private function elementor_assign($page_id, $json_template_name = '')
    {

        $file = false;

        if(is_child_theme() && file_exists(get_stylesheet_directory().'/elementor-data/wpdirectorykit/'.$json_template_name))
        {
            $file = get_stylesheet_directory().'/elementor-data/wpdirectorykit/'.$json_template_name;
        }
        elseif(file_exists(get_template_directory().'/demo-data/wpdirectorykit/'.$json_template_name))
        {
            $file = get_template_directory().'/demo-data/wpdirectorykit/'.$json_template_name;
        }
        elseif(file_exists( WPDIRECTORYKIT_PATH. '/demo-data/'.$json_template_name))
        {
            $file = WPDIRECTORYKIT_PATH.'demo-data/'.$json_template_name;
        }
        
        if(!$file || !class_exists('Elementor\Plugin'))
        {
            return false;
        }

        $page_template =  get_page_template_slug( $page_id );

        add_post_meta( $page_id, '_elementor_edit_mode', 'builder' );

        global $wp_filesystem;
        // Initialize the WP filesystem, no more using 'file-put-contents' function
        if (empty($wp_filesystem)) {
            WP_Filesystem();
        }

        $string =  $wp_filesystem->get_contents($file);

        $json_template = json_decode($string, true);
        $elements = $json_template['content'];

        $data = array(
            'elements' => $elements,
            'settings' => array('post_status'=>'autosave', 'template'=>$page_template),
        );   
        // @codingStandardsIgnoreStart
        $document = Elementor\Plugin::$instance->documents->get( $page_id, false );
        // @codingStandardsIgnoreEnd
        return $document->save( $data );
    }

    private function import_visual_data($purpose = '') {
        $this->load->model('searchform_m');
        $this->load->model('resultitem_m');
        $this->load->model('field_m');

        if($this->searchform_m->get() || $this->resultitem_m->get()){
            $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Search and result card already imported', 'wpdirectorykit').'</div>';
            return false;
        }

        /* remove listings */
        $this->db->delete($this->resultitem_m->_table_name);
        $this->db->delete($this->searchform_m->_table_name);
        /* end remove listings */

        /* reset autoincrement */
        $this->db->query('TRUNCATE TABLE `'.$this->resultitem_m->_table_name.'`');
        $this->db->query('TRUNCATE TABLE `'.$this->searchform_m->_table_name.'`');

        if(file_exists($this->import_xml_file)) {
            $dom_array = $this->xmlstr_to_array(file_get_contents($this->import_xml_file));
            
            if(!isset($dom_array['search_form']) && empty($dom_array['search_form'])) {
            } else {
                // Save searchform_m
                $data = array();
                $data['searchform_name'] = 'Primary';
                $data['searchform_json'] = trim($dom_array['search_form']);

                if (function_exists('run_wdk_bookings') && isset($dom_array['search_form_premium']) && !empty($dom_array['search_form_premium'])) {
                    $data['searchform_json'] = trim($dom_array['search_form_premium']);
                }

                $data['idsearchform'] = 1;
                $insert_id = $this->searchform_m->insert($data, NULL);
            }

            if(!isset($dom_array['resultitem']) && empty($dom_array['resultitem'])) {
            } else {
                // Save searchform_m
                $data = array();
                $data['resultitem_name'] = 'Primary';
                $data['resultitem_json'] = trim($dom_array['resultitem']);
                $data['idresultitem'] = 1;
                $insert_id = $this->resultitem_m->insert($data, NULL);
            }

            $this->data['import_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Search Form & Result Card imported').'</div>';
            return true;
        }

        $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Missing xml file '.$this->import_xml_file, 'wpdirectorykit').'</div>';
        return false;
        
    }

    private function import_locations() {
        $this->load->model('location_m');

        if($this->location_m->get()){
            $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Locations already imported', 'wpdirectorykit').'</div>';
            return false;
        }

        if(file_exists($this->import_xml_file_locations)) {
            $dom_array = $this->xmlstr_to_array(file_get_contents($this->import_xml_file_locations));
            if(!isset($dom_array['locations']['location']) && empty($dom_array['locations']['location'])) {
                $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('No fields in xml file  '.$this->import_xml_file_locations, 'wpdirectorykit').'</div>';
                return false;
            }
            foreach ($dom_array['locations']['location'] as $key => $location_data) {
                $this->_recursive_add_locations($location_data);
            }  

            $this->data['import_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Locations imported').'</div>';
            return true;
        }

        $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Missing xml file '.$this->import_xml_file_locations, 'wpdirectorykit').'</div>';
        return false;
    }

    private function _recursive_add_locations ($data = array(), $parent_id = 0, $level = 0 ) {
        $base = array(
            'location_title' => '',
            'parent_id' => $parent_id,
            'order_index' => '',
            'level' => $level,
            'font_icon_code' => '',
            'image_id' => '',
            'icon_id' => '',
            'code' => '',
        );
        $location_data = array_intersect_key(array_merge($base, $data), $base);
        array_walk($location_data, function(&$item){$item = (is_array($item)) ? '' : trim(wmvc_xss_clean($item));});
       
        if(empty(wmvc_show_data('location_title', $location_data))) {
            return false;
        }
           
        if(!empty($location_data['image_id']) && file_exists($this->import_images_dir.$location_data['image_id'])){
            /* add files */
            $location_data['image_id'] = wmvc_add_wp_image($this->import_images_dir.$location_data['image_id']);
        }

        if(!empty($location_data['icon_id']) && file_exists($this->import_images_dir.$location_data['icon_id'])){
            /* add files */
            $location_data['icon_id'] = wmvc_add_wp_image($this->import_images_dir.$location_data['icon_id']);
        }

        $location_data['icon_path'] = wdk_generate_path_image($location_data['icon_id']);
        $location_data['image_path'] = wdk_generate_path_image($location_data['image_id']);

        /* add locations */
        $insert_id = $this->location_m->insert($location_data, NULL);
        if(isset($data['childs']["location"]) && !empty($data['childs']["location"])) {
            foreach ($data['childs']["location"] as $sub_location_data) {
                $this->_recursive_add_locations($sub_location_data, $insert_id, ($level+1));
            }  
        }
    }

    private function import_categories($purpose = '') {
        $this->load->model('category_m') ;
        $this->load->model('dependfields_m') ;
        
        if($this->category_m->get()){
            $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Categories already imported', 'wpdirectorykit').'</div>';
            return false;
        }

        if(file_exists($this->import_xml_file)) {
            $dom_array = $this->xmlstr_to_array(file_get_contents($this->import_xml_file));
            if(!isset($dom_array['categories']['category']) && empty($dom_array['categories']['category'])) {
                $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('No fields in xml file  '.$this->import_xml_file, 'wpdirectorykit').'</div>';
                return false;
            }

            foreach ($dom_array['categories']['category'] as $key => $category_data) {
                $this->_recursive_add_categories($category_data);
            }  

              /* indexes by new order and reorder */
              $results = $this->category_m->get_tree_table();
              $values = array();
              $order_index = 1;
              foreach( $results as $item)
              {
                  $values[] = array('order_index' => $order_index, 'idcategory' => $item->idcategory);
                  $order_index++;
              }
              $this->db->updateBatch( $this->category_m->_table_name, $values, 'idcategory');

            $this->data['import_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Categories imported').'</div>';
            return true;
        }

        $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Missing xml file '.$this->import_xml_file, 'wpdirectorykit').'</div>';
        return false;
    }

    private function _recursive_add_categories ($data = array(), $parent_id = 0, $level = 0 ) {
        $base = array(
            'category_title' => '',
            'parent_id' => $parent_id,
            'order_index' => '',
            'level' => $level,
            'font_icon_code' => '',
            'image_id' => '',
            'icon_id' => '',
            'code' => '',
        );

        $category_data = array_intersect_key(array_merge($base, $data), $base);
        array_walk($category_data, function(&$item){$item = (is_array($item)) ? '' : trim(wmvc_xss_clean($item));});
       
        if(empty(wmvc_show_data('category_title', $category_data))) {
            return false;
        }

        if(!empty($category_data['image_id'])){

            $file = false;
            if(is_child_theme() && file_exists(get_stylesheet_directory().'/elementor-data/wpdirectorykit/images/'.$category_data['image_id']))
            {
                $file = get_stylesheet_directory().'/elementor-data/wpdirectorykit/images/'.$category_data['image_id'];
            }
            elseif(file_exists(get_template_directory().'/demo-data/wpdirectorykit/images/'.$category_data['image_id']))
            {
                $file = get_template_directory().'/demo-data/wpdirectorykit/images/'.$category_data['image_id'];
            }
            elseif(file_exists( $this->import_images_dir.$category_data['image_id']))
            {
                $file = $this->import_images_dir.$category_data['image_id'];
            }
            /* add files */

            if($file)
                $category_data['image_id'] = wmvc_add_wp_image($file);
        }

        if(!empty($category_data['icon_id'])){
            /* add files */
            $file = false;
            if(is_child_theme() && file_exists(get_stylesheet_directory().'/elementor-data/wpdirectorykit/images/'.$category_data['icon_id']))
            {
                $file = get_stylesheet_directory().'/elementor-data/wpdirectorykit/images/'.$category_data['icon_id'];
            }
            elseif(file_exists(get_template_directory().'/demo-data/wpdirectorykit/images/'.$category_data['icon_id']))
            {
                $file = get_template_directory().'/demo-data/wpdirectorykit/images/'.$category_data['icon_id'];
            }
            elseif(file_exists( $this->import_images_dir.$category_data['icon_id']))
            {
                $file = $this->import_images_dir.$category_data['icon_id'];
            }
            /* add files */

            if($file)
                $category_data['icon_id'] = wmvc_add_wp_image($file);
        }

        $category_data['icon_path'] = wdk_generate_path_image($category_data['icon_id']);
        $category_data['image_path'] = wdk_generate_path_image($category_data['image_id']);
                        
        /* add categories */
        $insert_id = $this->category_m->insert($category_data, NULL);

        if(isset($data['hidden_fields_list']) && !empty($data['hidden_fields_list'])) {
            $data_insert = array(
                'main_field' => 'categories',
                'field_id' => $insert_id,
                'hidden_fields_list' => $data['hidden_fields_list'],
            );

            $this->dependfields_m->delete_where(array('field_id' => $insert_id, 'main_field' => 'categories'));
            $this->dependfields_m->insert($data_insert);
        }


        if(isset($data['childs']["category"]) && !empty($data['childs']["category"])) {
            foreach ($data['childs']["category"] as $sub_category_data) {
                $this->_recursive_add_categories($sub_category_data, $insert_id, ($level+1));
            }  
        }
    }

    private function import_fields($purpose = '') {
        $this->load->model('field_m');
        $this->load->model('listingfield_m');

        if($this->field_m->get()){
            $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Fields already imported', 'wpdirectorykit').'</div>';
            return false;
        }
        
        $sections_added_array = array();
        if(file_exists($this->import_xml_file)) {
            $dom_array = $this->xmlstr_to_array(file_get_contents($this->import_xml_file));

            $base = array(
                'field_type' => '',
                'field_label' => '',
                'hint' => '',
                'columns_number' => '',
                'is_visible_frontend' => '1',
                'is_visible_dashboard' => '1',
                'prefix' => '',
                'suffix' => '',
                'values_list' => '',
                'icon_id' => '',
                'is_required' => '',
                'order_index' => '',
            );

            if(!isset($dom_array['fields']['field']) && empty($dom_array['fields']['field'])) {
                $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('No fields in xml file  '.$this->import_xml_file, 'wpdirectorykit').'</div>';
                return false;
            }
            
            foreach ($dom_array['fields']['field'] as $key => $field) {
                $field_data = array_intersect_key(array_merge($base, $field), $base);
                array_walk($field_data, function(&$item){$item = (is_array($item)) ? '' : trim(wmvc_xss_clean($item));});
                
                if(empty(wmvc_show_data('field_type', $field_data)) || empty(wmvc_show_data('field_label', $field_data))) {
                    continue;
                }
                
                $field_data['is_visible_frontend'] = 1;
                $field_data['is_visible_dashboard'] = 1;
              
                
                /* special set number format for price fields */
                if($field_data['field_label'] == 'Rent Price' || $field_data['field_label'] == 'Sale Price') {
                    $field_data['is_price_format'] = 1;
                }

                if(!in_array($field['section'], $sections_added_array)) {
                    /* add section */
                    $section_data = array(
                        'field_type' => 'SECTION',
                        'field_label' => $field['section'],
                        'columns_number' => '12',
                        'is_visible_frontend' => '1',
                        'is_visible_dashboard' => '1', 
                        'order_index' =>  $field['order_index'],
                    );
                    /* add field */

                    if($section_data['field_label'] == 'SEO') {
                        $section_data['is_visible_frontend'] = 0;
                    }

                    $this->field_m->insert($section_data, NULL);

                    $sections_added_array[] = $field['section'];
                }
                
                /* add field */

                if($field_data['field_label'] == 'Keywords' || $field_data['field_label'] == 'Short Description') {
                    $field_data['is_visible_frontend'] = 0;
                }

                $insert_id = $this->field_m->insert($field_data, NULL);
                // check if column exists, add if nto exists
                if(!empty($insert_id))
                    $this->listingfield_m->create_table_column($field_data, $insert_id);
            }  

            $this->data['import_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Fields imported').'</div>';
            return true;
        }

        $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Missing xml file '.$this->import_xml_file, 'wpdirectorykit').'</div>';
        return false;
    }

    private function import_listings($purpose = '') {
        $this->load->model('field_m');
        $this->load->model('listingfield_m');
        $this->load->model('listing_m');
        $this->load->model('category_m');
        $this->load->model('location_m');
        $this->load->model('listingusers_m');
    
        if($this->listing_m->get()){
            $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Listings already imported', 'wpdirectorykit').'</div>';
            return false;
        }

        /* generate listings */
        $categories_list = $this->category_m->get();
        $locations_list  = $this->location_m->get();
        $categories = array();
        $locations = array();
        if($categories_list)
            foreach($categories_list as $category){
                $categories[] = $category->idcategory;
            }
        if($locations_list)
            foreach($locations_list as $location){
                $locations[] = $location->idlocation;
            }

        $this->db->where(array('field_type !='=> 'SECTION'));
        $fields = $this->field_m->get();

        $listingfield_list = $this->listingfield_m->get_available_fields();
        foreach($fields as $key => $field_data) {
            if(!isset($listingfield_list['field_'.$field_data->idfield.'_'.$field_data->field_type])) {
                unset($fields[$key]);
            }
        }
              
        // Open a known directory, and proceed to read its contents
        $purpose_name = str_replace('.xml','',$purpose);
        $files = array();
        if (is_dir($this->import_images_dir)) {
            if ($dh = opendir($this->import_images_dir)) {
                while (($file = readdir($dh)) !== false) {
                    if(strpos($file, '.jpg') !== false && strpos($file, $purpose_name) !== false && strpos($file, 'category') === false)
                    $files[] = $file;
                }
                closedir($dh);
            }
        }
        sort($files);

        if(file_exists($this->import_xml_file)) {
            $dom_array = $this->xmlstr_to_array(file_get_contents($this->import_xml_file));
            if(!isset($dom_array['listings']['listing']) && empty($dom_array['listings']['listing'])) {
                $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('No listings in xml file  '.$this->import_xml_file, 'wpdirectorykit').'</div>';
                return false;
            }

            /* demo agent add/get if exists */
            $user_ids = array();
            $user_ids[] = $this->add_demo_user('Debra_Moran', 'Debra', 'Moran', 'agent1@wpdirectorykit.com','wdk_agent', NULL, array('wdk_phone' =>'(917) 367-2058','wdk_facebook'=>'#','wdk_linkedin'=>'#','wdk_instagram'=>'#','wdk_telegram'=>'#','wdk_twitter'=>'#'));
            $user_ids[] = $this->add_demo_user('Garry_Novan', 'Garry', 'Novan', 'agent2@wpdirectorykit.com','wdk_agent', NULL, array('wdk_phone' =>'(918) 345-2054','wdk_facebook'=>'#','wdk_linkedin'=>'#','wdk_instagram'=>'#','wdk_telegram'=>'#','wdk_twitter'=>'#'));
            $user_ids[] = $this->add_demo_user('Kety_Spear', 'Kety', 'Spear', 'agent3@wpdirectorykit.com','wdk_agent', NULL, array('wdk_phone' =>'(919) 854-2056','wdk_facebook'=>'#','wdk_linkedin'=>'#','wdk_instagram'=>'#','wdk_telegram'=>'#','wdk_twitter'=>'#'));
          
            foreach ($dom_array['listings']['listing'] as $listing_num => $listing) {
                $location_id = '';
                $category_id = '';

                if(!empty($categories))
                    $category_id = $categories[rand(0, count($categories)-1)];

                if(!empty($location))
                    $location_id = $locations[rand(0, count($locations)-1)];

                $data_listing = array(
                    'post_title' => '',
                    'post_content' => '',
                    'category_id' => $category_id,
                    'location_id' => $location_id,
                    'address' => '',
                    'lat' => '',
                    'listing_images' => '',
                    'listing_images_path' => '',
                    'listing_images_path_medium' => '',
                    'date_modified' => date('Y-m-d H:i:s'),
                    'lng' => '',
                    'is_featured' => '',
                    'is_activated' => '1',
                    'is_approved' => '1',
                    'counter_views' => rand(20,250),
                    'user_id_editor' => NULL,
                );
                
                $data_listing = array_intersect_key(array_merge($data_listing, $listing), $data_listing);
                array_walk($data_listing, function(&$item){$item = (is_array($item)) ? '' : trim(wmvc_xss_clean($item));});

                if(empty(wmvc_show_data('post_title', $data_listing))) {
                    continue;
                }
           
                $hidden_fields_list = wdk_depend_get_hidden_fields($data_listing['category_id']);
                
                /*fields */
                $data_listings_fields = array();
                foreach($fields as $field_data) {
                    if(!empty($hidden_fields_list) && strpos(','.$hidden_fields_list.',', ','.$field_data->idfield.',')!==FALSE) {
                        continue;
                    }

                    if(isset($listing['field_'.$field_data->idfield])) {
                        $data_listings_fields['field_'.$field_data->idfield] = $listing['field_'.$field_data->idfield];
                    } else {
                        if($field_data->field_type == 'DROPDOWN' || $field_data->field_type == 'DROPDOWN_MULTIPLE')
                        {
                            if(empty($field_data->values_list)) continue;
                            $values = explode(',', $field_data->values_list);
                            if(count($values) > 0)
                            {
                                $start=0;
                                if(empty($values[0]))$start=1;
                                $data_listings_fields['field_'.$field_data->idfield] = $values[rand($start, count($values)-1)];
                            }
                        }
                        elseif($field_data->field_type == 'NUMBER')
                        {
                            $data_listings_fields['field_'.$field_data->idfield] = rand(2,499);

                        }
                        elseif($field_data->field_type == 'CHECKBOX')
                        {
                            $data_listings_fields['field_'.$field_data->idfield] = rand(0,1);
                        }
                        elseif($field_data->field_type == 'TEXTAREA')
                        {
                            //$data_listings_fields['field_'.$field_data->idfield] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
                        }
                    }
                }
              
                /* add files */
                $count_images = 2;
                if($purpose == 'car-dealer.xml') {
                   $count_images = 1;
                }
                if(isset($listing['images']) && !empty($listing['images'])) {

                    foreach (explode(',', $listing['images']) as $key => $file_name) {
                       
                        if(empty($file_name)) continue;

                        if(!empty($data_listing['listing_images']))
                            $data_listing['listing_images'].= ',';
                        $image_id = wmvc_add_wp_image($this->import_images_dir.esc_html($file_name));

                        $data_listing['listing_images'] .= $image_id;

                        $image_path = wp_get_original_image_path( $image_id);

                        if($image_path) {
                            /* path of image */
                            $next_path = str_replace(WP_CONTENT_DIR . '/uploads/','', $image_path);

                            /* check length listing_images_path + next image + comma, should be less 200*/
                            if(strlen($data_listing['listing_images_path'].$next_path)<195) {
                                if(!empty($data_listing['listing_images_path']))
                                    $data_listing['listing_images_path'] .= ',';

                                $data_listing['listing_images_path'] .= $next_path;
                            }
                        }

                        $image_url = wp_get_attachment_image_url($image_id, 'large');
                        if($image_url) {
                            $parsed = parse_url($image_url);
                            $next_path = substr($parsed['path'], strpos($parsed['path'], 'uploads/')+8);

                            if(!empty($data_listing['listing_images_path_medium']))
                                $data_listing['listing_images_path_medium'] .= ',';

                            $data_listing['listing_images_path_medium'] .= $next_path;
                        }
                    }

                } else {
                    /* auto import images */
                    for ($i=0,$image_num=$listing_num; $i<$count_images; $i++,$image_num++) {
                        if($image_num >= count($files))
                            $image_num = 0;

                        if(!empty($data_listing['listing_images']))
                            $data_listing['listing_images'].= ',';

                        if(!isset($files[$image_num])) continue;

                        $image_id = wmvc_add_wp_image($this->import_images_dir.esc_html($files[$image_num]));
                        $data_listing['listing_images'] .= $image_id;

                        $image_path = wp_get_original_image_path( $image_id);

                        if($image_path) {
                            /* path of image */
                            $next_path = str_replace(WP_CONTENT_DIR . '/uploads/','', $image_path);

                            /* check length listing_images_path + next image + comma, should be less 200*/
                            if(strlen($data_listing['listing_images_path'].$next_path)<195) {
                                if(!empty($data_listing['listing_images_path']))
                                    $data_listing['listing_images_path'] .= ',';

                                $data_listing['listing_images_path'] .= $next_path;
                            }
                        }

                        $image_url = wp_get_attachment_image_url($image_id, 'large');
                        if($image_url) {
                            $parsed = parse_url($image_url);
                            $next_path = substr($parsed['path'], strpos($parsed['path'], 'uploads/')+8);

                            if(!empty($data_listing['listing_images_path_medium']))
                                $data_listing['listing_images_path_medium'] .= ',';

                            $data_listing['listing_images_path_medium'] .= $next_path;
                        }
                    }
                }

                // Create post object
                $listing_post = array(
                    'ID' => NULL,
                    'post_type'     => 'wdk-listing',
                    'post_title'    => wp_strip_all_tags( $data_listing['post_title'] ),
                    'post_content'  => $data_listing['post_content'],
                    'post_status'   => 'publish',
                    'post_author'   => get_current_user_id()
                );
                // Insert the post into the database
                $id = wp_insert_post( $listing_post );

                $listing_data = array('post_id' => $id);
                $listing_data_fields = array('category_id', 'location_id', 'address', 'lat', 'lng', 'listing_images', 'is_featured', 'is_activated','is_approved','listing_images_path', 'listing_images_path_medium');
                foreach($listing_data_fields as $field_name)
                {
                    if(isset( $data_listing[$field_name]))
                        $listing_data[$field_name] = $data_listing[$field_name];
                }
                $listing_data['user_id_editor'] = $user_ids[array_rand($user_ids)];
                $listing_data['date_modified'] = date('Y-m-d H:i:s');
                $listing_data['counter_views'] = rand(20,250);
                
                $this->listing_m->insert($listing_data, NULL);

                $data_listings_fields['post_id'] = $id;
                $this->listingfield_m->insert_custom_fields($fields, $data_listings_fields, NULL);
            }  

            $this->data['import_log'] .= '<div class="alert alert-success" role="alert">'.esc_html__('Listings imported').'</div>';
            return true;
        }

        $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.esc_html__('Missing xml file '.$this->import_xml_file, 'wpdirectorykit').'</div>';
        return false;
    }

    protected function xmlstr_to_array($xmlstr) {
        $doc = new DOMDocument();
        
        if ( !@$doc->loadXML($xmlstr) ) {
            return false;
        }
        
        $root = $doc->documentElement;
        $output = $this->domnode_to_array($root);
        $output['@root'] = $root->tagName;
        return $output;
    }
    
    protected function domnode_to_array($node) {
        $output = array();
        switch ($node->nodeType) {
          case XML_CDATA_SECTION_NODE:
          case XML_TEXT_NODE:
            $output = trim($node->textContent);
          break;
          case XML_ELEMENT_NODE:
            for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
              $child = $node->childNodes->item($i);
              $v = $this->domnode_to_array($child);
              if(isset($child->tagName)) {
                $t = $child->tagName;
                if(!isset($output[$t])) {
                  $output[$t] = array();
                }
                $output[$t][] = $v;
              }
              elseif($v || $v === '0') {
                $output = (string) $v;
              }
            }
            if($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
              $output = array('@content'=>$output); //Change output into an array.
            }
            if(is_array($output)) {
              if($node->attributes->length) {
                $a = array();
                foreach($node->attributes as $attrName => $attrNode) {
                  $a[$attrName] = (string) $attrNode->value;
                }
                $output['@attributes'] = $a;
              }
              foreach ($output as $t => $v) {
                if(is_array($v) && count($v)==1 && $t!='@attributes') {
                  $output[$t] = $v[0];
                }
              }
            }
          break;
        }
        return $output;
    }
    
    public function run()
	{
        wdk_access_check('settings_m', 1);

        // Check _wpnonce
        check_admin_referer( 'wdk-import-data-run', '_wpnonce' );
        update_option('wdk_install_images_sizes_disable', 1);

        ini_set('max_execution_time', 900);
        $this->data['import_log'] = '';
        $multipurpose = 'real-estate.xml';
        if(isset($_GET['multipurpose']))
            $multipurpose = sanitize_text_field($_GET['multipurpose']);
        
        $redirect_url = admin_url("admin.php?page=wdk_fields&is_updated");
        if(isset($_GET['redirect_url']) && strpos($_GET['redirect_url'], 'http') === FALSE && strpos($_GET['redirect_url'], '//') === FALSE)
            $redirect_url = admin_url(sanitize_text_field($_GET['redirect_url']));

        $this->import_xml_file = WPDIRECTORYKIT_PATH.'demo-data/'.$multipurpose;
        $this->import_images_dir = apply_filters('wdk/settings/import/api_run/import_images_dir', $this->import_images_dir, $multipurpose);
        $this->import_xml_file = apply_filters('wdk/settings/import/api_run/import_xml_file', $this->import_xml_file, $multipurpose);
        $this->import_xml_file_locations = apply_filters('wdk/settings/import/api_run/import_xml_file_locations', $this->import_xml_file_locations, $multipurpose);
        

        $this->import_locations();
        $this->import_categories($multipurpose);
        $this->import_fields($multipurpose);
        $this->import_listings($multipurpose);
        $this->import_visual_data($multipurpose);
        $this->demo_pages($multipurpose);

        $message = urlencode(str_replace(" ", '+', __('Import completed successfully', 'wpdirectorykit')).' <a href="'.esc_url((wdk_get_option('wdk_results_page')) ? get_permalink(wdk_get_option('wdk_results_page')): home_url()).'">'.__('Check your page now', 'wpdirectorykit').'</a>');

        do_action('wpdirectorykit/install/api');
        do_action('wdk/settings/import/api_run', $multipurpose);
        update_option('wdk_install_images_sizes_disable', 0);

        wp_redirect($redirect_url.'&message='.$message);
        exit;
    }
    
    public function api_import()
	{
        wdk_access_check('settings_m', 1);
        update_option('wdk_install_images_sizes_disable', 1);
        /* block url access */
        if(
            (isset($_GET['function']) && $_GET['function'] == 'api_import' ) ||
            (isset($_POST['function']) && $_POST['function'] == 'api_import' )
            ) {
            echo esc_html__('Security blocked', 'wpdirectorykit');
            exit();
        } 

        ini_set('max_execution_time', 900);
        $this->data['import_log'] = '';
        $multipurpose = 'real-estate.xml';
        if(isset($_GET['multipurpose']))
            $multipurpose = sanitize_text_field($_GET['multipurpose']);

            
        $this->import_xml_file = WPDIRECTORYKIT_PATH.'demo-data/'.$multipurpose;
        $this->import_images_dir = apply_filters('wdk/settings/import/api_run/import_images_dir', $this->import_images_dir, $multipurpose);
        $this->import_xml_file = apply_filters('wdk/settings/import/api_run/import_xml_file', $this->import_xml_file, $multipurpose);
        $this->import_xml_file_locations = apply_filters('wdk/settings/import/api_run/import_xml_file_locations', $this->import_xml_file_locations, $multipurpose);

        $this->import_locations();
        $this->import_categories($multipurpose);
        $this->import_fields($multipurpose);
        $this->import_listings($multipurpose);
        $this->import_visual_data($multipurpose);
        $this->import_settings();

        /* action */
        do_action('wpdirectorykit/install/api');
        do_action('wdk/settings/import/api_run', $multipurpose);
        update_option('wdk_install_images_sizes_disable', 0);
        return true;
    }

    public function _api_import()
	{
        wdk_access_check('settings_m', 1);
        update_option('wdk_install_images_sizes_disable', 1);
        ini_set('max_execution_time', 900);
        $this->data['import_log'] = '';
        $multipurpose = 'real-estate.xml';
        if(isset($_GET['multipurpose']))
            $multipurpose = sanitize_text_field($_GET['multipurpose']);

        $this->import_xml_file = WPDIRECTORYKIT_PATH.'demo-data/'.$multipurpose;
        $this->import_images_dir = apply_filters('wdk/settings/import/api_run/import_images_dir', $this->import_images_dir, $multipurpose);
        $this->import_xml_file = apply_filters('wdk/settings/import/api_run/import_xml_file', $this->import_xml_file, $multipurpose);
        $this->import_xml_file_locations = apply_filters('wdk/settings/import/api_run/import_xml_file_locations', $this->import_xml_file_locations, $multipurpose);

        $this->import_locations();
        $this->import_categories($multipurpose);
        $this->import_fields($multipurpose);
        $this->import_listings($multipurpose);
        $this->import_visual_data($multipurpose);
        $this->import_settings();

        /* action */
        do_action('wpdirectorykit/install/api');
        do_action('wdk/settings/import/api_run', $multipurpose);
        update_option('wdk_install_images_sizes_disable', 0);
        return true;
    }
    
    private function replace_meta($from = '', $to = '') {
        global $wpdb;
        // @codingStandardsIgnoreStart cannot use `$wpdb->prepare` because it remove's the backslashes
        $rows_affected = $wpdb->query(
            "UPDATE ".esc_sql($wpdb->postmeta)." " .
            "SET `meta_value` = REPLACE(`meta_value`, '" . str_replace( '/', '\\\/', esc_sql($from) ) . "', '" . str_replace( '/', '\\\/', esc_sql($to) ) . "') " .
            "WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE '[%' ;" );
        /* end login */
    }
    
    private function import_settings() {
        wdk_access_check('settings_m', 1);

        update_option( 'wdk_is_category_enabled', '1' );
        update_option( 'wdk_is_location_enabled', '1' );
        update_option( 'wdk_is_address_enabled', '1' );
        update_option( 'wdk_is_results_page_require', '1' );
        update_option( 'wdk_seo_description', '2' );
        update_option( 'wdk_seo_keywords', '3' );
        update_option( 'wdk_card_slider_enable', '1' );
        update_option('elementor_load_fa4_shim', 'yes');
        
        /* disable elmentor experement feature */
        update_option( 'elementor_experiment-e_font_icon_svg', 'inactive');
        update_option( 'elementor_experiment-landing-pages', 'inactive' );
        update_option( 'elementor_experiment-e_dom_optimization', 'inactive');

        return true;
    }

    /* add demo listing preview page */
    public function create_custom_page($layout_json, $page_title = "New Page", $option = '', $custom_message_title = '') {
        $page_title_message = $page_title;
        if(!empty($custom_message_title)){
            $page_title_message = $custom_message_title;
        }

        if(!empty($option) && (get_option($option)) && get_post_status(get_option($option)) =='publish'){
            //  $this->data['import_log'] .= '<div class="alert alert-danger" role="alert">'.$page_title_message.' '.esc_html__('Page already exists', 'wdk-membership').'</div>';
            // return false;
        }

        add_action('wdk-membership/elementor-elements/register_widget', function($self){
            $self->add_widget('WdkMembership\Elementor\Extensions\WdkMembershipContactFormExt');
        });
                
        add_action('wdk-membership/elementor-elements/register_widget', function(){
            add_action('eli/includes', function(){
                require_once WDK_MEMBERSHIP_PATH . '/elementor-extensions/class-contact-form.php';
            });

            add_action('eli/register_widget', function(){
                $object = new WdkMembership\Elementor\Extensions\WdkMembershipContactFormExt();
                \Elementor\Plugin::instance()->widgets_manager->register( $object );
            });
        });

        add_action('wdk-membership/elementor-elements/register_widget', function($self){
            $self->add_widget('WdkMembership\Elementor\Widgets\WdkMembershipContent');
            $self->add_widget('WdkMembership\Elementor\Widgets\WdkMembershipMenu');
            $self->add_widget('WdkMembership\Elementor\Widgets\WdkMembershipProfileListings');
            $self->add_widget('WdkMembership\Elementor\Widgets\WdkMembershipProfileContent');
            $self->add_widget('WdkMembership\Elementor\Widgets\WdkMembershipProfileContent');
            $self->add_widget('WdkMembership\Elementor\Widgets\WdkMembershipLoginForm');
            $self->add_widget('WdkMembership\Elementor\Widgets\WdkMembershipRegisterForm');
            $self->add_widget('WdkMembership\Elementor\Widgets\WdkMembershipBreadcrumb');
        });

        // Import elementor templates
        $page = $this->generate_custom_page($page_title, '', 'elementor_canvas');
        $this->elementor_custom_assign($page->ID, $layout_json);

        // Assign page.
        if($page && !empty($option))
            update_option( $option, $page->ID, TRUE);
        
        $this->data['import_log'] .= '<div class="alert alert-success" role="alert">'.$page_title_message.' '.esc_html__('Page imported', 'wdk-membership').'</div>';

        return $page->ID;
    }

    
    /* Create Page */
    private function generate_custom_page($post_title, $post_content = '', $post_template = NULL, $post_parent=0)
    {
        $post = wdk_page_by_title($post_title, 'OBJECT', 'page' );
        
        $post_id = NULL;
        
        // Delete posts and rebuild
        if(!empty($post))
        {
            wp_delete_post($post->ID, true);
            $post=NULL;
        }
        
        if(!empty($post))
        $post_id   = $post->ID;

        if(empty($post_id))
        {
            $error_obj = NULL;
            $post_insert = array(
                'post_title'    => wp_strip_all_tags( $post_title ),
                'post_content'  => $post_content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_author'   => get_current_user_id(),
                'post_category' => array(1,2),
                'page_template' => $post_template,
                'post_parent'   => $post_parent
            );
            $post_id = wp_insert_post( $post_insert, $error_obj );
        }

        $post_insert = get_post( $post_id );
        
        return $post_insert;
    }

    /* Elementor Import Template */
    public function elementor_custom_assign($page_id, $json_template_name = '')
    {
        $file = false;

        if(file_exists( $json_template_name ))
        {
            $file = $json_template_name;
        }
        
        if(!$file || !class_exists('Elementor\Plugin'))
        {
            return false;
        }

        $page_template =  get_page_template_slug( $page_id );

        add_post_meta( $page_id, '_elementor_edit_mode', 'builder' );

        global $wp_filesystem;
        // Initialize the WP filesystem, no more using 'file-put-contents' function
        if (empty($wp_filesystem)) {
            WP_Filesystem();
        }

        $string =  $wp_filesystem->get_contents($file);
        
        $json_template = json_decode($string, true);

        $elements = $json_template['content'];

        $data = array(
            'elements' => $elements,
            'settings' => array('post_status'=>'autosave', 'template'=>$page_template),
        );   
        // @codingStandardsIgnoreStart
        $document = Elementor\Plugin::$instance->documents->get( $page_id, false );
        // @codingStandardsIgnoreEnd
        return $document->save( $data );
    }

}
