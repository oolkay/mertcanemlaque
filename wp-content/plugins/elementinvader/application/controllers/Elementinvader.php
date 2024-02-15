<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Elementinvader_index extends Winter_MVC_Controller {

	public function __construct(){
        parent::__construct();
        
        wp_enqueue_script( 'jquery-magnific-popup', ELEMENTINVADER_URL . 'admin/js/magnific-popup/jquery.magnific-popup.js', false, false, false );
        wp_enqueue_style( 'jquery-magnific-popup', ELEMENTINVADER_URL . 'admin/js/magnific-popup/magnific-popup.css', false, '1.0.0' );
	}
    
	public function index()
	{
        $this->data['templates'] = array();

        $path = get_template_directory().'/elementinvader/';

        if(file_exists($path))
        {
             $templates = array();

             if (is_dir($path))
             {
                 if ($dh = opendir($path))
                 {
                   while (($file = readdir($dh)) !== false)
                   {
                       if(file_exists($path.$file.'/description.xml') || file_exists($path.$file.'/description.pac'))
                       {
                         $file_name = pathinfo($path.$file, PATHINFO_FILENAME);
                         $templates[] =  $file_name;
                       }
                   }
                   closedir($dh);
                 }
               }
         
             if(count($templates) > 0)
             {
                $this->data['templates'] = $templates;
             }

        }

        // Load view
        $this->load->view('elementinvader/index', $this->data);
    }
    
	public function import_pages()
	{       
        //ob_clean();
        $results = array();
        $results['status'] = 'success';

        $template = $this->input->post_get('template');
        $page_title = $this->input->post_get('page_title');
        $template_title = elementinvader_template_data($template, 'title');

        // Check for required plugins

        $path = get_template_directory().'/elementinvader/';

        $plugins_to_activate = array();
        if(file_exists($path))
        {
            $templates = array();

            if (is_dir($path))
            {
                if ($dh = opendir($path))
                {
                while (($file = readdir($dh)) !== false)
                {
                    if(file_exists($path.$file.'/description.xml') || file_exists($path.$file.'/description.pac'))
                    {
                        $required_plugins = elementinvader_template_data($file, 'required-plugins');

                        $plugins = array();
                        if(isset($required_plugins->plugin)) {
                            $plugins = $required_plugins->plugin;
                        } else if(is_array($required_plugins) || is_object($required_plugins)) {
                            $plugins = $required_plugins;
                        }
                       
                        foreach($plugins as $key=>$plugin)
                        {
                            $plugin = (string) $plugin;
                            if(!is_plugin_active($plugin.'/'.$plugin.'.php'))
                            {
                                $plugins_to_activate[$plugin] = $plugin;
                            }
                        }
                    }
                }
                closedir($dh);
                }
            }
        }

        if(count($plugins_to_activate) > 0)
        {
            $results['page_url'] = admin_url('admin.php?page=elementinvader&function=install_plugins_all');
            $results['message'] = __('Required plugins missing, click here to install','elementinvader');
            $results['plugins_required'] = $plugins_to_activate;
        }
        else
        {
            // Get menu
            $menu_name = 'Element Invader';
            $menu_exists = wp_get_nav_menu_object( $menu_name );

            if(!$menu_exists)
            {
                $menu_id = wp_create_nav_menu($menu_name);
            }
            else
            {
                $menu_id = $menu_exists->term_id;
            }

            $path = get_template_directory().'/elementinvader/';

            if(file_exists($path))
            {
                $templates = array();

                if (is_dir($path))
                {
                    if ($dh = opendir($path))
                    {
                    while (($file = readdir($dh)) !== false)
                    {
                        if(file_exists($path.$file.'/description.xml') || file_exists($path.$file.'/description.pac'))
                        {
                            $file_name = pathinfo($path.$file, PATHINFO_FILENAME);
                            $templates[] =  $file_name;
                        }
                    }
                    closedir($dh);
                    }
                }
            
                if(count($templates) > 0)
                {
                    foreach($templates as $key=>$item)
                    {
                        $page_title = elementinvader_template_data($item, 'page-title');

                        $for_replace = elementinvader_upload_files($item);

                        $new_page = elementinvader_create_page($page_title, '', 'elementor_canvas');

                        elementinvader_elementor_assign($new_page->ID, get_template_directory() . '/elementinvader/'.$item.'/template.json', $for_replace);

                        wp_update_nav_menu_item($menu_id, 0, array('menu-item-title' => $page_title,
                            'menu-item-object' => 'page',
                            'menu-item-object-id' => $new_page->ID,
                            //'menu-item-parent-id' => NULL,
                            'menu-item-type' => 'post_type',
                            'menu-item-status' => 'publish'));
                    }
                }
            }

            $results['page_url'] = admin_url('edit.php?post_type=page&orderby=date&order=desc');
            $results['message'] = __('Pages imported, click and open pages','elementinvader');
        }

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($results);
        exit();
    }

    public function install_plugins()
    {
        $template = $this->input->post_get('template');
        $page_title = $this->input->post_get('page_title');

        $this->data['template'] = $template;
        $this->data['page_title'] = $page_title;

        $this->load->view('elementinvader/install_plugins', $this->data);
    }

    public function install_plugins_all()
    {
        $template = $this->input->post_get('template');
        $page_title = $this->input->post_get('page_title');

        $this->data['template'] = $template;
        $this->data['page_title'] = $page_title;

        $this->load->view('elementinvader/install_plugins_all', $this->data);
    }

	public function add_page()
	{       

        ob_clean();
        
        $results = array();
        $results['status'] = 'success';
        $template = $this->input->post_get('template');
        $page_title = $this->input->post_get('page_title');
        $license_key = $this->input->post_get('license_key');
        $plugins_to_activate = array();


        // log add page counter
        if(strpos($template, 'download_kit_') === 0)
        {
            $online_kit_id = str_replace('download_kit_', '', $template);
            $ret_call = wmvc_api_call('POST', ELEMENTINVADER_WEBSITE.'index.php/marketplace/logdownload/'.$online_kit_id, 
                                                array(
                                                    'website_url'   =>  get_home_url(),
                                                    'api_token'     =>  get_option('elementinvader_api_token', ''),
                                                    'license_key'   =>  $license_key
                                                    )
                                            );    

            $ret_call_obj = json_decode($ret_call);

            if(isset($ret_call_obj->login_message))
            {
                $results['login_message'] = $ret_call_obj->login_message;
            }

            if(isset($ret_call_obj->{'required-plugins'}) && !empty($ret_call_obj->{'required-plugins'}))
            {
                $required_plugins = explode(',', $ret_call_obj->{'required-plugins'});

                $plugins = array();
                if(isset($required_plugins->plugin)) {
                    $plugins = $required_plugins->plugin;
                } else if(is_array($required_plugins) || is_object($required_plugins)) {
                    $plugins = $required_plugins;
                }
               
                foreach($plugins as $key=>$plugin)
                {
                    $plugin = (string) $plugin;
                    if(!is_plugin_active($plugin.'/'.$plugin.'.php'))
                    {
                        $plugins_to_activate[$plugin] = $plugin;
                    }
                }
            }
 
            if(count($plugins_to_activate) == 0)
            {
                if(isset($ret_call_obj->purchase_success) && $ret_call_obj->purchase_success == TRUE)
                {
                    $results['purchase_message'] = $ret_call_obj->purchase_message;
                    $results['purchase_success'] = 1;
                }
                else if(!empty($ret_call_obj->purchase_message))
                {
                    $results['purchase_message'] = $ret_call_obj->purchase_message;
                    $results['message'] = $ret_call_obj->purchase_message;
                    $results['purchase_success'] = 0;
                    $results['page_url'] = admin_url('admin.php?page=elementinvader_contact&template='.$template.'&page_title='.$page_title.'&subject='. __('License key issue','elementinvader')
                                            .'&message='. __('Issue related to license key:','elementinvader').' '.$license_key);
    
                    header('Pragma: no-cache');
                    header('Cache-Control: no-store, no-cache');
                    header('Content-Type: application/json; charset=utf8');
                    echo json_encode($results);
                    exit();
                }
            }

            // Check for required plugins

            elementinvader_template_data($template, 'required-plugins', array(
                'website_url'   =>  get_home_url(),
                'api_token'     =>  get_option('elementinvader_api_token', ''),
                'license_key'   =>  $license_key
            ));
        }
        else
        {
            // Check for required plugins

            $required_plugins = elementinvader_template_data($template, 'required-plugins', array(
                'website_url'   =>  get_home_url(),
                'api_token'     =>  get_option('elementinvader_api_token', ''),
                'license_key'   =>  $license_key
                ));
        }
        
        $plugins = array();
        if(isset($required_plugins->plugin)) {
            $plugins = $required_plugins->plugin;
        } else if(is_array($required_plugins) || is_object($required_plugins)) {
            $plugins = $required_plugins;
        }
      
        foreach($plugins as $key=>$plugin)
        {
            $plugin = (string) $plugin;
            if(!is_plugin_active($plugin.'/'.$plugin.'.php'))
            {
                $plugins_to_activate[$plugin] = $plugin;
            }
        }
        
        if(count($plugins_to_activate) > 0)
        {
            $results['page_url'] = admin_url('admin.php?page=elementinvader&function=install_plugins');
            $results['message'] = __('Required plugins missing, click here to install','elementinvader');
            $results['plugins_required'] = $plugins_to_activate;
        }
        else
        {
            // Get menu
            $menu_name = 'Element Invader';
            $menu_exists = wp_get_nav_menu_object( $menu_name );

            if(!$menu_exists)
            {
                $menu_id = wp_create_nav_menu($menu_name);
            }
            else
            {
                $menu_id = $menu_exists->term_id;
            }

            $for_replace = elementinvader_upload_files($template);

            $new_page = elementinvader_create_page($page_title, '', 'elementor_canvas');

            $import_result = TRUE;

            if(file_exists(get_template_directory() . '/elementinvader/'.$template.'/template.json'))
            {
                $import_result = elementinvader_elementor_assign($new_page->ID, get_template_directory() . '/elementinvader/'.$template.'/template.json', $for_replace);
            }
            elseif(strpos($template, 'download_kit_') === 0)
            {
                $online_kit_id = str_replace('download_kit_', '', $template);

                if(file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/template.json'))
                {
                    $import_result = elementinvader_elementor_assign($new_page->ID, WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/template.json', $for_replace);
                }
                else
                {
                    $import_result = FALSE;
                }
            }

            wp_update_nav_menu_item($menu_id, 0, array('menu-item-title' => $page_title,
                'menu-item-object' => 'page',
                'menu-item-object-id' => $new_page->ID,
                //'menu-item-parent-id' => NULL,
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish'));

            $results['page_url'] = admin_url('post.php?post='.$new_page->ID.'&action=elementor');
            $results['message'] = __('Page created, click and open in Elementor','elementinvader');

            if($import_result === FALSE)
            {        
                $results['page_url'] = admin_url('admin.php?page=elementinvader_contact&template_id='.str_replace('download_kit_', '', $template).'&page_title='.$page_title);
                $results['message'] = __('Import failed, json or zip extracting trouble on your server, please report issue here','elementinvader');
            }
            elseif(!empty($license_key) && !empty($online_kit_id))
            {
                $ret_call = wmvc_api_call('POST', ELEMENTINVADER_WEBSITE.'index.php/marketplace/logsuccess/'.$online_kit_id, 
                    array(
                        'website_url'   =>  get_home_url(),
                        'api_token'     =>  get_option('elementinvader_api_token', ''),
                        'license_key'   =>  $license_key
                        )
                );   
            }
        }

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($results);
        exit();
    }

    public function add_page_test()
    {
        ob_clean();

        $template = 'example-1';
        $page_title = 'example-test-1';
        $template_title = elementinvader_template_data($template, 'title');

        // Get menu
        $menu_name = 'Element Invader';
        $menu_exists = wp_get_nav_menu_object( $menu_name );

        if(!$menu_exists)
        {
            $menu_id = wp_create_nav_menu($menu_name);
        }
        else
        {
            $menu_id = $menu_exists->term_id;
        }

        //echo get_template_directory() . '/elementinvader/'.$template.'/template.json';

        $for_replace = elementinvader_upload_files($template);

        $new_page = elementinvader_create_page($page_title, '', 'elementor_canvas');
        $import_result = elementinvader_elementor_assign($new_page->ID, get_template_directory() . '/elementinvader/'.$template.'/template.json', $for_replace);

        wp_update_nav_menu_item($menu_id, 0, array('menu-item-title' => $page_title,
            'menu-item-object' => 'page',
            'menu-item-object-id' => $new_page->ID,
            //'menu-item-parent-id' => NULL,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish'));

        $results = array();
        $results['status'] = 'success';
        $results['page_url'] = admin_url('post.php?post='.$new_page->ID.'&action=elementor');
        $results['message'] = __('Page created, click and open in Elementor','elementinvader');

        wmvc_dump($results);

        exit();
    }   

	public function login()
	{       

        $results = wmvc_api_call('POST', ELEMENTINVADER_WEBSITE.'login/plugin', $_POST);

        $array_result = json_decode($results);

        if(isset($array_result->api_token))
        {
            update_option('elementinvader_api_token', $array_result->api_token);
        }

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        echo $results;
        exit();
    }

    public function icon_click()
    {
        if(!isset($_POST['icon']))exit('parameter icon missing');

        $_POST['api_token'] = wp_filter_kses(get_option('elementinvader_api_token', ''));

        if($_POST['icon'] == 'like')
        {
            $results = wmvc_api_call('POST', ELEMENTINVADER_WEBSITE.'index.php/elementor/like', $_POST);
        }
        
        if($_POST['icon'] == 'favourite')
        {
            $results = wmvc_api_call('POST', ELEMENTINVADER_WEBSITE.'index.php/elementor/favourite', $_POST);
        }

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        echo $results;
        exit();
    }

    public function export_json()
    {
        $elementor_post_id = $this->input->post_get('post');

        $elem = Elementor\Plugin::instance(); 
        $elem->templates_manager->export_template(array('source'=>'local', 'template_id'=> $elementor_post_id));
    }

    public function export_zip()
    {
        $elementor_post_id = $this->input->post_get('post');
        $template_id = $elementor_post_id;

        $this->data['elementor_post_id'] = $elementor_post_id;
        $this->data['form'] = &$this->form;
        $this->data['db_data'] = get_option('eli_export_'.$elementor_post_id);

        $rules = array(
            array(
                'field' => 'kit_title',
                'label' => __('Template Name', 'elementinvader'),
                'rules' => 'required'
            ),
            array(
                'field' => 'kit_page_title',
                'label' => __('Page title', 'elementinvader'),
                'rules' => 'required'
            ),
            array(
                'field' => 'kit_description',
                'label' => __('Description', 'elementinvader'),
                'rules' => 'required'
            ),
            array(
                'field' => 'kit_page_tags',
                'label' => __('Tags', 'elementinvader'),
                'rules' => ''
            ),
            array(
                'field' => 'required_plugins[]',
                'label' => __('Plugins', 'elementinvader'),
                'rules' => ''
            ),
            array(
                'field' => 'screenshoot', 
                'label' => __('Screenshoot', 'elementinvader'), 
                'rules' => 'required|elementinvader_size_maxheight_4000'
            ),
            array(
                'field' => 'screenshoot_large', 
                'label' => __('Screenshoot Large', 'elementinvader'), 
                'rules' => 'required|elementinvader_size_maxheight_6000'
            ),
        );

        $this->form->add_error_message('elementinvader_size_640', __('Screenshoot Size should have width 640px', 'elementinvader'));
        $this->form->add_error_message('elementinvader_size_1280', __('Screenshoot Large Size should have width 1280px', 'elementinvader'));

        $this->form->add_error_message('elementinvader_size_maxheight_2000', __('Screenshoot Size should have max height 2000px', 'elementinvader'));
        $this->form->add_error_message('elementinvader_size_maxheight_4000', __('Screenshoot Large Size should have max height 4000px', 'elementinvader'));

        if($this->form->run($rules))
        {
            // Save procedure for basic data
            $data = $this->input->post();
            $this->generate_zip($elementor_post_id, $data);
            
            if(!empty($data['save_data']))
                update_option('eli_export_'.$elementor_post_id, sanitize_post($data));

        } else {
            // Load view
            $this->load->view('elementinvader/export_zip', $this->data);
        }

    } 

    public function generate_zip($elementor_post_id = NULL, $data = array())
    {
        if(empty($elementor_post_id))
            $elementor_post_id = $this->input->post_get('post');

        echo '<br style="clear:both;" />';

        require_once ELEMENTINVADER_PATH . 'vendor/ElementorImporter/ElementorTemplateExporter.php';

        $exporter = new \Elementor\TemplateLibrary\Elementor_Template_Exporter();

        $file_data = $exporter->ei_prepare_template_export( $elementor_post_id );

        if(!file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'))
            mkdir(WP_CONTENT_DIR.'/uploads/elementinvader/');

        if(!file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id))
            mkdir(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id);

                    
        // json template file, filter if exists global colors
        $file_data['content'] = $this->filter_content($file_data['content']);
        
        $jsonfile = fopen(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id."/template.json", "w") or die("Unable to open file!");
        fwrite($jsonfile, $file_data['content']);
        fclose($jsonfile);
        // xml description file

        $blog_name = get_bloginfo('name');
        if(isset($data['kit_title'])) {
            $blog_name = sanitize_text_field($data['kit_title']);
        }

        $post_data = get_post( $elementor_post_id );
        $page_title = 'Default page title';
        if(!empty($post_data->post_title))
            $page_title = $post_data->post_title;
        if(isset($data['kit_page_title'])) {
            $page_title = sanitize_text_field($data['kit_page_title']);
        }

        $tags = esc_html__('Teg1, Tag2, Tag3', 'elementinvader');
        if(isset($data['kit_page_tags'])) {
            $tags = sanitize_text_field($data['kit_page_tags']);
        }

        $description = '';
        if(isset($data['kit_description'])) {
            $description = sanitize_text_field($data['kit_description']);
        }

        $xml = 
        '<?xml version="1.0" encoding="UTF-8"?>'."\r\n".
        '<template>'."\r\n".
        '  <kit-title><![CDATA['.$blog_name.']]></kit-title>'."\r\n".
        '  <page-title><![CDATA['.$page_title.']]></page-title>'."\r\n".
        '  <date>'.date('m-d-Y').'</date>'."\r\n".
        '  <tags><![CDATA['.$tags.']]></tags>'."\r\n".
        '  <description><![CDATA['.$description.']]></description>'."\r\n".
        '  <required-plugins>'."\r\n";

        if(isset($data['required_plugins']))foreach ($data['required_plugins'] as $plugin) {
            $xml .= '    <plugin>'.esc_html($plugin).'</plugin>'."\r\n";
        }

        $xml .= '  </required-plugins>'."\r\n".
        '</template>';

        $descriptionfile = fopen(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id."/description.xml", "w") or die("Unable to open file!");
        fwrite($descriptionfile, $xml);
        fclose($descriptionfile);

        // screenshots
        $screenshot_url = '';
        $screenshot_url_large = '';
        if(isset($data['screenshoot'])) {
            $screenshot_url = wp_get_original_image_path($data['screenshoot'], false);
        }

        if(isset($data['screenshoot_large'])) {
            $screenshot_url_large = wp_get_original_image_path($data['screenshoot_large'], true);
        }

        if(empty($screenshot_url) && empty($screenshot_url_large) && $post_data->post_type == "envato_tk_templates" || TRUE) {
            $attachment_id = wp_get_original_image_path( $elementor_post_id );
            if($attachment_id) {
                $screenshot_url_large = $screenshot_url = wp_get_original_image_path($attachment_id, true);
            }
            $elementor_post_id = $this->input->post_get('post');
        }

        /*
        if(empty($screenshot_url)) {

            $screenshot_url = 'http://image.thum.io/get/?url='.$post_data->guid;
            $response = wp_remote_get( $screenshot_url );
        }
        */
        if(empty($screenshot_url)) {
            $screenshot_url = ELEMENTINVADER_PATH.'public/img/no-photo.jpg';
        }

        if(empty($screenshot_url_large)) {
            $screenshot_url_large = ELEMENTINVADER_PATH.'public/img/no-photo.jpg';
        }

        @unlink(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id.'/screenshot.jpg');
        @unlink(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id.'/screenshot-large.jpg');

        copy (  $screenshot_url, WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id.'/screenshot.jpg' );
        copy (  $screenshot_url_large, WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id.'/screenshot-large.jpg' );

        // images folder

        if(!file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id.'/images'))
            mkdir(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id.'/images');

        $for_replace = elementinvader_export_add_files($elementor_post_id);

        /* replace images urls */
        if(!empty($for_replace)) {
            $file_data['content'] = $this->replace_content($file_data['content'], $for_replace);
                        
            $jsonfile = fopen(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id."/template.json", "w") or die("Unable to open file!");
            fwrite($jsonfile, $file_data['content']);
            fclose($jsonfile);
        }

        // create zip file

        $zip = new ZipArchive;
        if ($zip->open(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id.'.zip', ZipArchive::CREATE) === TRUE)
        {
            echo 'Open Zip: '.WP_CONTENT_DIR.'/uploads/elementinvader/export_'.wp_filter_kses($elementor_post_id).'.zip<br />';

            $folder_for_zip = WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id;

            if ($handle = opendir($folder_for_zip))
            {
                // Add all files inside the directory
                while (false !== ($entry = readdir($handle)))
                {
                    if ($entry != "." && $entry != ".." && !is_dir($folder_for_zip. '/' . $entry))
                    {
                        $zip->addFile($folder_for_zip. '/' . $entry, $entry);
                    }
                }
                closedir($handle);
            }

            $folder_for_images = WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$elementor_post_id.'/images';

            if ($handle = opendir($folder_for_images))
            {
                // Add all files inside the directory
                while (false !== ($entry = readdir($handle)))
                {
                    if ($entry != "." && $entry != ".." && !is_dir($folder_for_images. '/' . $entry))
                    {
                        $zip->addFile($folder_for_images. '/' . $entry, 'images/'.$entry);
                    }
                }
                closedir($handle);
            }
         
            $zip->close();
            echo '<br /><b>Global Colors</b>:';
            $get_global_colors = $this->get_global_colors();
            if($get_global_colors){
                foreach ($get_global_colors as $gl_key => $gl_value) {
                    echo '<br />'.esc_html($gl_key).': '.esc_html($gl_value);
                }
            } else {
                echo '<br />Not Detected Global Colors (If you use Global Colors in templates, please try resave in elementor settings one global colors) or contact with us';
            }
            echo '<br /><br /><a href="'.content_url().'/uploads/elementinvader/export_'.esc_html($elementor_post_id).'.zip'.'">Download Demo file (Please change screenshots and description in XML file)</a>';
        }
        else
        {
            echo 'FAILED to Open Zip: '.WP_CONTENT_DIR.'/uploads/elementinvader/export_'.esc_html($elementor_post_id).'.zip<br />';
        }
    } 
    
    private function get_global_colors(){
        $colors = [];
        global $wpdb;
        
        $_elementor_page_settings = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key ='_elementor_page_settings' AND `meta_value` LIKE '%system_colors%' LIMIT 1" );

        if($_elementor_page_settings) {
            $_elementor_page_settings = unserialize($_elementor_page_settings);
            if(isset($_elementor_page_settings['system_colors'])) {
                foreach ($_elementor_page_settings['system_colors'] as $key => $value) {
                    if(isset($value['color']))
                        $colors [$value["_id"]] = $value['color']; 
                }
            }
            if(isset($_elementor_page_settings['custom_colors'])) {
                foreach ($_elementor_page_settings['custom_colors'] as $key => $value) {
                    if(isset($value['color']))
                        $colors [$value['_id']] = $value['color']; 
                }
            }
        }
        
        return $this->_meth_return($colors);
    }
    
    /*
     * replace files in content
     */
    private function replace_content ($content, $replace_array = array()) {
        if(count($replace_array) > 0)
        {
            foreach($replace_array as $key=>$rep)
            {
                $content = str_replace($key, $rep, $content);
            }
        }
        
        return $content;
    }

    /*
     * filter json content
     */
    private function filter_content ($content) {
        $get_global_colors = $this->get_global_colors();
        /* regenerate array only if exists changes */
        $edited = false;
        if($get_global_colors){
            /* content colors */
            foreach ($get_global_colors as $gl_key => $gl_value) {
                $content = str_replace('var( --e-global-color-'.$gl_key.' )',$gl_value, $content);
            }
            
            $json_content = json_decode($content);
            $array_keys = [];
            $this->recursive_array_search_key('__globals__', $json_content,$array_keys);

            foreach ($array_keys as $key => $value) {
                $current_node = $this->get_node($json_content, $value);
                $globals = $current_node->__globals__;
                foreach ($globals as $gl_key => $gl_value) {
                    $gl_value = str_replace('globals/colors?id=', '', $gl_value);
                    if(isset($get_global_colors[$gl_value])){
                        $current_node->$gl_key = $get_global_colors[$gl_value];
                    }
                }
                unset($current_node->__globals__);
            }
            
            /* primary color for header */
            $array_keys = [];
            $this->recursive_array_search_key(['widgetType','heading'], $json_content,$array_keys);
            foreach ($array_keys as $key => $value) {
                $current_node = $this->get_node($json_content, $value, 'widgetType');
                if(!isset($current_node->settings->title_color))
                    $current_node->settings->title_color = $get_global_colors['primary'];
            }
            
            /* primary color for text */
            $array_keys = [];
            $this->recursive_array_search_key(['widgetType','text-editor'], $json_content,$array_keys);
            foreach ($array_keys as $key => $value) {
                $current_node = $this->get_node($json_content, $value, 'widgetType');
                if(!isset($current_node->settings->text_color))
                    $current_node->settings->text_color = $get_global_colors['text'];
            }
            
            /* primary color for text */
            $array_keys = [];
            $this->recursive_array_search_key(['widgetType','icon-list'], $json_content,$array_keys);
            foreach ($array_keys as $key => $value) {
                $current_node = $this->get_node($json_content, $value, 'widgetType');
                if(!isset($current_node->settings->icon_color))
                    $current_node->settings->icon_color = $get_global_colors['primary'];
            }
            
            /* primary color for text */
            $array_keys = [];
            $this->recursive_array_search_key(['widgetType','toggle'], $json_content,$array_keys);
            foreach ($array_keys as $key => $value) {
                $current_node = $this->get_node($json_content, $value, 'widgetType');
                if(!isset($current_node->settings->title_color))
                    $current_node->settings->title_color = $get_global_colors['primary'];
                if(!isset($current_node->settings->tab_active_color))
                    $current_node->settings->tab_active_color = $get_global_colors['accent'];
                if(!isset($current_node->settings->content_color))
                    $current_node->settings->content_color = $get_global_colors['text'];
            }
            $edited = true;
        }
        
        
        if($edited)
            return json_encode($json_content);
        else 
            return $content;
    }
        
    /* recursive search all keys and return to $array_keys */
    private function recursive_array_search_key($needle, $haystack, &$array_keys, $currentKey = '') {
        foreach($haystack as $key=>$value) {
            if(!empty($key) && is_array($needle) && $key==$needle[0] && $value==$needle[1]) {
                $array_keys[] = $currentKey . '->' .$key . '';
            }
            elseif(!empty($key) && $key==$needle) {
                $array_keys[] = $currentKey . '->' .$key . '';
            }
            elseif (is_object($value) || is_array($value)) {
                $nextKey = $this->recursive_array_search_key($needle,$value,$array_keys, $currentKey . '->' . $key . '');
                if ($nextKey) {
                   // return $nextKey;
                }
            }
        }
        return false;
    }
    
    /* get node by string */
    private function get_node(&$array, $path = '', $break='__globals__') {
        $return = &$array;
        foreach (explode('->', $path) as $key => $value) {
            if($value == $break) break;
            if($value != '') {
                if(is_numeric($value))
                    $return = &$return[$value];
                else
                    $return  = &$return->$value;
            }
        }
        return $return;
    }
    
    private function _meth_return($return = ''){
        if(!empty($return))
            return $return;
        else 
            return false;
    }
    
}
