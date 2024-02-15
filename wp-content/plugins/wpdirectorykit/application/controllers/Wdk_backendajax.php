<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly;

class Wdk_backendajax extends Winter_MVC_Controller
{

    public function __construct()
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(-1);
        }
        parent::__construct();

        $this->data['is_ajax'] = true;
    }

    public function index(&$output = NULL, $atts = array())
    {
    }


    public function plugin_news()
    {
        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $data['success'] = false;
        $data['response'] = NULL;
        $data['rss'] = array();

        /* protect */
        if(!current_user_can( 'read' ) && !wmvc_user_in_role('administrator')) {
            $this->output($data);
        }
        
        //https://wpdirectorykit.com/wp/last_news.php?f=news.json

        $request = wp_remote_get('https://wpdirectorykit.com/wp/last_news.php?f=news.json');

        // request failed
        if (is_wp_error($request)) {
            $data['response'] = $request;
        }
        $code = (int) wp_remote_retrieve_response_code($request);

        // make sure the fetch was successful
        if (empty($data['response']) && $code == 200) {
            $response = wp_remote_retrieve_body($request);

            // Decode the json
            $output = json_decode($response);
            $count = 0;
            foreach ($output  as $key => $value) {
                $data['rss'][] = array(
                    'date' => wdk_get_date(wmvc_show_data('date', $value, date('Y-m-d H:i:s'), TRUE, TRUE), false),
                    'title' => wmvc_show_data('title', $value, '', TRUE, TRUE),
                    'link' => wmvc_show_data('link', $value, '', TRUE, TRUE),
                );
                $count++;

                if ($count > 10) break;
            }
        } else {
            $data['response'] = get_status_header_desc($code);
        }

        $this->output($data);
    }

    public function plugin_upgrader($output = "", $atts = array(), $instance = NULL)
    {
        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $data['success'] = false;

        /* protect */
        if(!current_user_can( 'install_plugins' ) && !wmvc_user_in_role('administrator')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-plugin_upgrader', '_wpnonce' );

        ob_start();

        $parameters = array();

        foreach ($_POST as $key => $value) {
            $parameters[$key] = sanitize_text_field($value);
        }

        $source = 'https://downloads.wordpress.org/plugin/' . $parameters['slug'] . '.zip';

        if (!empty($parameters['source']))
            $source = $parameters['source'];

        if (!class_exists('Plugin_Upgrader', false)) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }

        $upgrader = new Plugin_Upgrader(new PluginInstallerSkinSilentWdk($skin_args));

        if (!file_exists(WP_PLUGIN_DIR . '/' . $parameters['slug'] . '/' . $parameters['slug'] . '.php')) {
            //exit(WP_PLUGIN_DIR .'/'.$parameters['slug'].'/'.$parameters['slug'].'.php');

            $upgrader->install($source);
        }

        ob_clean();

        $activate = activate_plugin($parameters['slug'] . '/' . $parameters['slug'] . '.php');

        if (is_wp_error($activate)) {
            $data['message'] = wp_kses_post($activate->get_error_message());
            $data['success'] = false;
        } else {
            $data['success'] = true;
        }

        $data['slug'] = $parameters['slug'];

        $data['parameters'] = $parameters;

        //$data['sql'] = $this->db->last_query();
        $this->output($data);
    }

    public function install_content($output = "", $atts = array(), $instance = NULL)
    {
        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $data['success'] = false;

        
        /* protect */
        if(!current_user_can( 'install_plugins' ) && !wmvc_user_in_role('administrator')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-install_content', '_wpnonce' );

        ob_start();

        $parameters = array();

        foreach ($_POST as $key => $value) {
            $parameters[$key] = sanitize_text_field($value);
        }

        if (!class_exists('\WP_Importer')) {
            require ABSPATH . '/wp-admin/includes/class-wp-importer.php';
        }

        require_once WPDIRECTORYKIT_PATH . 'vendor/WordPress-Importer/class-logger.php';
        require_once WPDIRECTORYKIT_PATH . 'vendor/WordPress-Importer/class-logger-html.php';
        require_once WPDIRECTORYKIT_PATH . 'vendor/WordPress-Importer/class-logger-serversentevents.php';
        require_once WPDIRECTORYKIT_PATH . 'vendor/WordPress-Importer/class-wxr-importer.php';
        require_once WPDIRECTORYKIT_PATH . 'vendor/WordPress-Importer/class-wxr-import-info.php';

        $importer_options = array(
            'fetch_attachments' => true
        );

        $logger = new WP_Importer_Logger();

        $importer = new WXR_Importer($importer_options);

        $importer->set_logger($logger);

        $current_theme = wp_get_theme();

        $tmp_file = download_url($current_theme->get('AuthorURI') . '/demo_themes/' . $current_theme->get('TextDomain') . '.xml');

        $results_importer = $importer->import($tmp_file);

        ob_clean();

        if (is_wp_error($results_importer)) {
            $data['message'] = wp_kses_post($results_importer->get_error_message());
            $data['success'] = false;
        } else {
            $data['success'] = true;
        }

        $data['parameters'] = $parameters;

        //$data['sql'] = $this->db->last_query();
        $this->output($data);
    }

    public function install_listings($output = "", $atts = array(), $instance = NULL)
    {
        $this->load->load_helper('listing');
        $this->load->model('listing_m');
        $this->load->model('listingfield_m');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $data['success'] = false;
        
        /* protect */
        if(!current_user_can( 'install_plugins' ) && !wmvc_user_in_role('administrator')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-install_listings', '_wpnonce' );

        ob_start();

        $parameters = array();

        foreach ($_POST as $key => $value) {
            $parameters[$key] = sanitize_text_field($value);
        }

        $data['parameters'] = $parameters;

        //$data['sql'] = $this->db->last_query();
        $this->output($data);
    }


    public function generated_listings_images_path()
    {
        $data = array();
        $data['message'] = '';
        $data['popup_text_success'] = '';
        $data['popup_text_error'] = '';
        $data['parameters'] = $_POST;
        $data['success'] = false;

        /* protect */
        if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-generated_listings_images_path', '_wpnonce' );

        $this->load->load_helper('listing');
        $this->load->model('listing_m');
        $listings = $this->listing_m->get();

        foreach ($listings as $listing) {
            $image_ids = explode(',', $listing->listing_images);
            $listing_data = array('listing_images_path' => '', 'listing_images_path_medium' => '');
            if (is_array($image_ids)) {
                foreach ($image_ids as $image_id) {
                    if (is_numeric($image_id)) {
                        $image_path = wp_get_original_image_path($image_id);
                        if (!$image_path) continue;

                        /* path of image */
                        $next_path = str_replace(WP_CONTENT_DIR . '/uploads/', '', $image_path);

                        if (!empty($listing_data['listing_images_path']))
                            $listing_data['listing_images_path'] .= ',';

                        $listing_data['listing_images_path'] .= $next_path;
                    }

                    $image_url = wp_get_attachment_image_url($image_id, 'large');
                    if ($image_url) {
                        $parsed = parse_url($image_url);
                        $next_path = substr($parsed['path'], strpos($parsed['path'], 'uploads/') + 8);

                        if (!empty($listing_data['listing_images_path_medium']))
                            $listing_data['listing_images_path_medium'] .= ',';

                        $listing_data['listing_images_path_medium'] .= $next_path;
                    }
                }
            }

            $this->listing_m->insert($listing_data, $listing->post_id);
        }

        $this->load->model('location_m');
        $locations = $this->location_m->get();
        if ($locations) foreach ($locations as $location) {
            $save_data = array(
                "parent_id" => $location->parent_id,
            );
            $save_data['icon_path'] = wdk_generate_path_image($location->icon_id);
            $save_data['image_path'] = wdk_generate_path_image($location->image_id);

            $this->location_m->insert($save_data, $location->idlocation);
        }

        $this->load->model('category_m');
        $categories = $this->category_m->get();
        if ($categories) foreach ($categories as $category) {
            $save_data = array(
                "parent_id" => $category->parent_id,
            );
            $save_data['icon_path'] = wdk_generate_path_image($category->icon_id);
            $save_data['image_path'] = wdk_generate_path_image($category->image_id);
            $save_data['marker_image_path'] = wdk_generate_path_image($category->marker_image_id);

            $this->category_m->insert($save_data, $category->idcategory);
        }

        $data['success'] = true;
        $data['popup_text_success'] = __('images path of listings,locations,categories generated', 'wpdirectorykit');
        $this->output($data);
    }

    public function optimization_listingfields_table()
    {
        $data = array();
        $data['message'] = '';
        $data['popup_text_success'] = '';
        $data['popup_text_error'] = '';
        $data['parameters'] = $_POST;
        $data['success'] = false;

        /* protect */
        if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-optimization_listingfields_table', '_wpnonce' );

        $this->load->load_helper('listing');
        $this->load->model('listingfield_m');
        $this->load->model('field_m');
        $listingfields = $this->listingfield_m->get_available_fields();

        global $wpdb;

        /* remove if missing */
        foreach ($listingfields as $listingfield => $field_data) {
            if (strpos($listingfield, 'field_') !== FALSE) {
                list($prefix, $field_id, $field_type) = explode('_', $listingfield);

                if (!wdk_field_option($field_id, 'field_type', false)) {
                    $wpdb->query("ALTER TABLE {$this->listingfield_m->_table_name} DROP COLUMN $listingfield;");
                }
            }
        }

        /* add if missing listingfield and  change from  varchar to TEXT  */
        $this->db->where(array('field_type !=' => 'SECTION'));
        $fields = $this->field_m->get();

        $listingfields = $this->listingfield_m->get_available_fields();
        foreach ($fields as $field) {
            $column_name = 'field_' . $field->idfield . '_' . $field->field_type;
            if (!isset($listingfields[$column_name])) {
                $this->listingfield_m->create_table_column(array('field_type' => $field->field_type), $field->idfield);
            } else {

                /* change from  varchar to TEXT */
                if (
                    stripos($listingfields[$column_name]->Type, 'VARCHAR') !== FALSE
                    && ($field->field_type == 'INPUTBOX' || $field->field_type == 'DROPDOWN' || $field->field_type == 'DROPDOWNMULTIPLE')
                ) {
                    $wpdb->query("ALTER TABLE `{$this->listingfield_m->_table_name}` CHANGE  `$column_name` `$column_name` TEXT NULL DEFAULT NULL;");
                }
            }
        }

        $data['success'] = true;
        $data['popup_text_success'] = __('Table listingfields optimized', 'wpdirectorykit');
        $this->output($data);
    }

    public function update_depend()
    {
        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $data['success'] = false;
        $data['parameters'] = $_POST;

        /* protect */
        if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-update_depend', '_wpnonce' );

        $this->load->load_helper('listing');
        $this->load->model('dependfields_m');

        if (wmvc_show_data('field_id', $data['parameters'], false) && wmvc_show_data('main_field', $data['parameters'], false)) {

            $hidden_fields = array();
            foreach ($data['parameters'] as $key => $value) {
                if (strpos($key, 'field_hide_') !== FALSE) {
                    $field_id = str_replace('field_hide_', '', $key);
                    if (is_intval($field_id)) {
                        $hidden_fields[] = $field_id;
                    }
                }
            }

            $data_insert = array(
                'main_field' => wmvc_show_data('main_field', $data['parameters'], false),
                'field_id' => wmvc_show_data('field_id', $data['parameters'], false),
                'hidden_fields_list' => join(',', $hidden_fields),
            );

            $this->dependfields_m->delete_where(array('field_id' => $data_insert['field_id'], 'main_field' => $data_insert['main_field']));
            $this->dependfields_m->insert($data_insert);
        }

        $this->output($data);
    }

    public function depend_copy_on_subcategories()
    {
        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $data['success'] = false;
        $data['parameters'] = $_POST;

        /* protect */
        if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk_depend_copy_on_subcategories', '_wpnonce' );

        $this->load->load_helper('listing');
        $this->load->model('dependfields_m');

        if (wmvc_show_data('category_id', $data['parameters'], false)) {

            $sub_categories_ids = wdk_category_get_all_childs(intval($data['parameters']['category_id'])); 

            $depends_list =  wdk_depend_get_hidden_fields(intval($data['parameters']['category_id'])); 

            
            $data['sub_categories_ids'] = $sub_categories_ids;
            $data['depends_list'] = $depends_list;
            
            if($sub_categories_ids){
                $this->dependfields_m->delete_where(array('field_id IN ('.join(',',$sub_categories_ids).')'=>NULL));
                foreach ($sub_categories_ids as $key => $id) {
                    # code...
                    $data_insert = array(
                        'main_field' => 'categories',
                        'field_id' => $id,
                        'hidden_fields_list' => $depends_list,
                    );
                    $this->dependfields_m->insert($data_insert);
                }
            }
        }

        $this->output($data);
    }

    public function generated_strings()
    {
        $data = array();
        $data['message'] = '';
        $data['popup_text_success'] = '';
        $data['popup_text_error'] = '';
        $data['parameters'] = $_POST;
        $data['success'] = false;

        /* protect */
        if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-backendajax', '_wpnonce' );

        global $wp_filesystem;
        // Initialize the WP filesystem, no more using 'file-put-contents' function
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        // @codingStandardsIgnoreEnd

        $this->load->load_helper('listing');
        $this->load->model('location_m');
        $this->load->model('category_m');


        /* locations strings */
        $this->db->select('location_title');
        $this->db->from($this->location_m->_table_name);
        $query = $this->db->get();
        $count_file = 1;
        $limit_size = '80000';
        $file_strings_content = '<?php ';
        if ($this->db->num_rows() > 0) {
            foreach ($this->db->results() as $location) {
                $file_strings_content .= '__(\'' . addslashes($location->location_title) . '\',\'wpdirectorykit\');' . "\n";

                if (strlen($file_strings_content) > $limit_size) {
                    $file_strings_content .= "\n" . '?>';
                    $wp_filesystem->put_contents(WPDIRECTORYKIT_PATH . 'translation_strings/locations_' . $count_file . '.php', $file_strings_content);

                    /* create new file */
                    $file_strings_content = '<?php ';
                    $count_file++;
                }
            }
            $file_strings_content .= "\n" . '?>';

            if (!empty($file_strings_content))
                $wp_filesystem->put_contents(WPDIRECTORYKIT_PATH . 'translation_strings/locations_' . $count_file . '.php', $file_strings_content);
        }


        /* categories strings */
        $this->db->select('category_title');
        $this->db->from($this->category_m->_table_name);
        $query = $this->db->get();
        $count_file = 1;
        $limit_size = '80000';
        $file_strings_content = '<?php ';
        if ($this->db->num_rows() > 0) {
            foreach ($this->db->results() as $category) {
                $file_strings_content .= '__(\'' . addslashes($category->category_title) . '\',\'wpdirectorykit\');' . "\n";

                if (strlen($file_strings_content) > $limit_size) {
                    $file_strings_content .= "\n" . '?>';
                    $wp_filesystem->put_contents(WPDIRECTORYKIT_PATH . 'translation_strings/categories_' . $count_file . '.php', $file_strings_content);

                    /* create new file */
                    $file_strings_content = '<?php ';
                    $count_file++;
                }
            }
            $file_strings_content .= "\n" . '?>';

            if (!empty($file_strings_content))
                $wp_filesystem->put_contents(WPDIRECTORYKIT_PATH . 'translation_strings/categories_' . $count_file . '.php', $file_strings_content);
        }

        /* fields */

        $this->load->model('field_m');
        $this->db->select('field_label,prefix,suffix,values_list,hint,placeholder');
        $this->db->from($this->field_m->_table_name);
        $query = $this->db->get();
        $count_file = 1;
        $limit_size = '70000';
        $file_strings_content = '<?php ';
        if ($this->db->num_rows() > 0) {
            foreach ($this->db->results() as $field) {

                if (!empty($field->field_label))
                    $file_strings_content .= '__(\'' . addslashes($field->field_label) . '\',\'wpdirectorykit\');' . "\n";

                if (!empty($field->prefix))
                    $file_strings_content .= '__(\'' . addslashes($field->prefix) . '\',\'wpdirectorykit\');' . "\n";

                if (!empty($field->suffix))
                    $file_strings_content .= '__(\'' . addslashes($field->suffix) . '\',\'wpdirectorykit\');' . "\n";

                if (!empty($field->values_list))
                    $file_strings_content .= '__(\'' . addslashes($field->values_list) . '\',\'wpdirectorykit\');' . "\n";

                if (!empty($field->placeholder))
                    $file_strings_content .= '__(\'' . addslashes($field->placeholder) . '\',\'wpdirectorykit\');' . "\n";

                if (strlen($file_strings_content) > $limit_size) {
                    $file_strings_content .= "\n" . '?>';
                    $wp_filesystem->put_contents(WPDIRECTORYKIT_PATH . 'translation_strings/fields_' . $count_file . '.php', $file_strings_content);

                    /* create new file */
                    $file_strings_content = '<?php ';
                    $count_file++;
                }
            }
            $file_strings_content .= "\n" . '?>';

            if (!empty($file_strings_content))
                $wp_filesystem->put_contents(WPDIRECTORYKIT_PATH . 'translation_strings/fields_' . $count_file . '.php', $file_strings_content);
        }


        $data['success'] = true;
        $data['popup_text_success'] = __('Strings generated', 'wpdirectorykit');
        $this->output($data);
    }

    public function loading_sublistings()
    {
        $data = array();
        $data['message'] = '';
        $data['popup_text_success'] = '';
        $data['popup_text_error'] = '';
        $data['parameters'] = $_POST;
        $data['success'] = false;
        $data['results'] = array();

        /* protect */
        if(!current_user_can('edit_own_listings') && !wmvc_user_in_role('administrator')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-backendajax', '_wpnonce' );

        $listing_id = intval(wmvc_show_data('listing_id', $data['parameters']));

        if (empty($listing_id)) {
            $data['popup_text_error'] = __('Missing Listing', 'wpdirectorykit');
        }

        if (empty($data['popup_text_error'])) {

            $this->load->load_helper('listing');
            $this->load->model('listing_m');

            
            $this->db->where($this->db->prefix . 'wdk_listings.post_id IN(' . wdk_field_value('listing_related_ids', $listing_id) . ')', null, false);
            $this->db->where(array('is_activated' => 1, 'is_approved' => 1));
            $this->db->order_by('FIELD(' . $this->db->prefix . 'wdk_listings.post_id, ' . wdk_field_value('listing_related_ids', $listing_id) . ')');

            $results = $this->listing_m->get();

            foreach ($results as $sublisting) {
                $_listing = array();
                $_listing['post_id'] = wdk_field_value('post_id', $sublisting);
                $_listing['post_title'] = wdk_field_value('post_title', $sublisting);
                $_listing['post_content_short'] = wmvc_character_limiter(wdk_field_value('post_content', $sublisting), 30);
                $_listing['date'] = wdk_get_date(wdk_field_value('date', $sublisting), false);
                $_listing['location'] = wdk_field_value('location_title', $sublisting);
                $_listing['category'] = wdk_field_value('category_title', $sublisting);
                $_listing['image_src'] = wdk_image_src($sublisting);
                $_listing['listing_remove_url'] = esc_url(get_admin_url() . "admin.php?page=wdk&function=delete&id=" . wdk_field_value('post_id', $sublisting));
                $_listing['listing_view_url'] = get_permalink($sublisting);
                $_listing['listing_edit_url'] = esc_url(get_admin_url() . "admin.php?page=wdk_listing&id=" . wdk_field_value('post_id', $sublisting));
                $_listing['listing_dash_remove_url'] = '';
                $_listing['listing_dash_edit_url'] = '';
                if(function_exists(' wdk_dash_url')) {
                    $_listing['listing_dash_remove_url'] = wdk_dash_url("dash_page=listings&table_action=table&action=delete&ids=" . wdk_field_value('post_id', $sublisting));
                    $_listing['listing_dash_edit_url'] = wdk_dash_url("dash_page=listings&function=edit&id=" . wdk_field_value('post_id', $sublisting));
                }

                $data['results'][] = $_listing;

            }
        }

        $data['success'] = true;
        $this->output($data);
    }

    public function remove_listing()
    {
        $data = array();
        $data['message'] = '';
        $data['popup_text_success'] = '';
        $data['popup_text_error'] = '';
        $data['parameters'] = $_POST;
        $data['success'] = false;
        $data['results'] = array();

        /* protect */
        if(!current_user_can('edit_own_listings') && !wmvc_user_in_role('administrator')) {
            $data['message'] = __('Disabled for current user', 'wpdirectorykit');
            $this->output($data);
        }

        // Check _wpnonce
        check_admin_referer( 'wdk-backendajax', '_wpnonce' );
        
        $listing_id = intval(wmvc_show_data('listing_id', $data['parameters']));

        if (empty($listing_id)) {
            $data['popup_text_error'] = __('Missing Listing', 'wpdirectorykit');
        }

        if (empty($data['popup_text_error'])) {

            $this->load->load_helper('listing');
            $this->load->model('listing_m');

            $listing = $this->listing_m->get($listing_id, TRUE);
            $parent_listing = $this->listing_m->get($listing->listing_parent_post_id, TRUE);

            $this->listing_m->delete($listing_id);

            if(isset($parent_listing->listing_related_ids) && !empty($parent_listing->listing_related_ids)) {
                $related_ids = trim(str_replace(','.$listing_id.'','', ','.$parent_listing->listing_related_ids.','),',');
                $this->listing_m->insert(array('listing_related_ids' => $related_ids), $listing->listing_parent_post_id);
            }

            $data['success'] = true;
        }

       
        $this->output($data);
    }

    private function output($data, $print = TRUE)
    {
        if ($print) {
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache');
            header('Content-Type: application/json; charset=utf8');
            //header('Content-Length: '.$length); // special characters causing troubles
            echo (wp_json_encode($data));
            exit();
        } else {
            return $data;
        }
    }
}

if (!class_exists('\Plugin_Upgrader', false)) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
}

/**
 * WordPress class extended for on-the-fly plugin installations.
 */
class PluginInstallerSkinSilentWdk extends \WP_Upgrader_Skin
{

    /**
     * Empty out the header of its HTML content.
     */
    public function header()
    {
    }

    /**
     * Empty out the footer of its HTML content.
     */
    public function footer()
    {
    }

    /**
     * Empty out the footer of its HTML content.
     *
     * @param string $string
     * @param mixed  ...$args Optional text replacements.
     */
    public function feedback($string, ...$args)
    {
    }

    /**
     * Empty out JavaScript output that calls function to decrement the update counts.
     *
     * @param string $type Type of update count to decrement.
     */
    public function decrement_update_count($type)
    {
    }

    /**
     * Empty out the error HTML content.
     *
     * @param string|WP_Error $errors A string or WP_Error object of the install error/s.
     */
    public function error($errors)
    {
    }
}
