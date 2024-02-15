<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_location extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        $this->data['form'] = &$this->form;

        /* [Table Actions Bulk Form] */
        $table_action = $this->input->post_get('table_action');
        $action = $this->input->post_get('action');
        $posts_selected = $this->input->post_get('post');

        if(!empty($table_action))
        {
            switch ($action) {
                case 'delete':
                    $this->bulk_delete($posts_selected);
                    break;
                default:
            } 
        }

        $this->load->model('location_m');

        $this->data['locations'] = $this->location_m->get_tree_table();
        // Load view
        $this->load->view('wdk_location/index', $this->data);
    }

    public function delete()
    {
        $id = (int) $this->input->post_get('id');
        $paged = (int) $this->input->post_get('paged');

        // Check _wpnonce
        check_admin_referer( 'wdk-location-delete_'.$id, '_wpnonce' );
        wdk_access_check('location_m', $id);
        $this->load->model('location_m');

        $this->location_m->delete($id);

        wp_redirect(admin_url("admin.php?page=wdk_location&paged=$paged"));
    }

    public function bulk_delete($posts_selected)
    {
        // Check _wpnonce
        check_admin_referer( 'wdk-location-bulk', '_wpnonce');

        $this->load->model('location_m');
        foreach($posts_selected as $id)
        {
            wdk_access_check('location_m', $id);
            $this->location_m->delete($id);
        }

        wp_redirect(admin_url("admin.php?page=wdk_location&is_updated=true&custom_message=".urlencode(esc_html__('Selected Locations removed', 'wpdirectorykit'))));
        exit;
    }

	public function edit()
	{
        $this->load->model('location_m');

        $id = (int) $this->input->post_get('id');
        wdk_access_check('location_m', $id);
        
        $this->data['db_data'] = NULL;

        $this->data['form'] = &$this->form;

        $this->data['parents'] = $this->location_m->get_parents($id);

        //exit($this->db->last_query());

        $rules = array(
                array(
                    'field' => 'location_title',
                    'label' => __('Title', 'wpdirectorykit'),
                    'rules' => 'required'
                ),
                array(
                    'field' => 'order_index',
                    'label' => __('Order Index', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'parent_id',
                    'label' => __('Parent', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'icon_id',
                    'label' => __('Parent', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'image_id',
                    'label' => __('Parent', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'related_svg_map',
                    'label' => __('Related SVG Map', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'related_svg_map_location',
                    'label' => __('Related SVG Map Location', 'wpdirectorykit'),
                    'rules' => ''
                ),
        );

        global $wp_filesystem;
        // Initialize the WP filesystem, no more using 'file-put-contents' function
        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        // @codingStandardsIgnoreEnd

        if(function_exists('run_wdk_svg_map')) {
            $maps_list_json = $wp_filesystem->get_contents(WDK_SVG_MAP_PATH.'resourse/maps-list-db.json');
            $maps_list = json_decode($maps_list_json, JSON_OBJECT_AS_ARRAY);
            
            $maps_data_json = $wp_filesystem->get_contents(WDK_SVG_MAP_PATH.'resourse/maps-db.json');
            $maps_data = json_decode($maps_data_json, JSON_OBJECT_AS_ARRAY);
            
            /* translate */
            $this->data['maps_list'] = array();
            foreach ($maps_list as $location_key => $location) {
                $this->data['maps_list'][str_replace('.svg', '', $location_key)] = esc_html__($location, 'wdk-svg-map');
            }
        }
        
        $this->data['map_related_locations'] = array();

        if(!empty($id))
        {
            $this->data['db_data'] = $this->location_m->get($id, TRUE);
        }
        
        if(function_exists('run_wdk_svg_map')) {
            if(wmvc_show_data('related_svg_map', $this->data['db_data'], false) && isset($maps_data[wmvc_show_data('related_svg_map', $this->data['db_data'])])) {
                foreach ($maps_data[wmvc_show_data('related_svg_map', $this->data['db_data'])]['locations'] as $location_key => $location) {
                    $this->data['map_related_locations'][$location_key] = esc_html__($location, 'wdk-svg-map');
                }

                asort($this->data['map_related_locations']);
            }
        }

        if($this->form->run($rules))
        {
            
            // Check _wpnonce
            check_admin_referer( 'wdk-location-edit_'.$id, '_wpnonce' );

            // Save procedure for basic data
            $data = $this->location_m->prepare_data(wdk_get_post(), $rules);

            if(empty($id) && strpos($data['location_title'], ',') !== FALSE) {
                
                foreach (explode(',', $data['location_title']) as $title) {
                    // Save standard wp post
                    $insert_id = $this->location_m->insert(array_merge($data, array('location_title' => trim($title))), NULL);
                }
                wp_redirect(admin_url("admin.php?page=wdk_location&is_updated=true&custom_message=".urlencode(esc_html__('Bulk Locations added', 'wpdirectorykit'))));
                exit;
            } else {

                $data['icon_path'] = wdk_generate_path_image($data['icon_id']);
                $data['image_path'] = wdk_generate_path_image($data['image_id']);
    
                // Save standard wp post
                $insert_id = $this->location_m->insert($data, $id);

                //exit($this->db->last_error());

                /* indexes by new order and reorder */
                $results = $this->location_m->get_tree_table();
                $values = array();
                $order_index = 1;
                foreach( $results as $item)
                {
                    $values[] = array('order_index' => $order_index, 'idlocation' => $item->idlocation);
                    $order_index++;
                }
                $this->db->updateBatch( $this->location_m->_table_name, $values, 'idlocation');

                // redirect
                if(!empty($insert_id) && empty($id))
                {
                    wp_redirect(admin_url("admin.php?page=wdk_location&id=$insert_id&is_updated=true"));
                    exit;
                }
            }
                
        }

        if(!empty($id))
        {
            $this->data['db_data'] = $this->location_m->get($id, TRUE);
        }

        $this->load->view('wdk_location/location_edit', $this->data);
    }

	public function import_from_svg()
	{

        if(!function_exists('run_wdk_svg_map')) {
            exit(esc_html__('Addon WDK SVG Map missing', 'wdk-svg-map'));
        }

        $this->load->model('location_m');

        $id = $this->input->post_get('id');
        wdk_access_check('location_m', $id);

        global $wp_filesystem;
        // Initialize the WP filesystem, no more using 'file-put-contents' function
        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        // @codingStandardsIgnoreEnd

        $maps_data_json = $wp_filesystem->get_contents(WDK_SVG_MAP_PATH.'resourse/maps-list-db.json');
        $maps_data = json_decode($maps_data_json, JSON_OBJECT_AS_ARRAY);

        /* translate */
        $this->data['maps_list'] = array();
        foreach ($maps_data as $location_key => $location) {
            $this->data['maps_list'][str_replace('.svg', '', $location_key)] = esc_html__($location, 'wdk-svg-map');
        }

        $this->data['db_data'] = NULL;

        $this->data['form'] = &$this->form;

        $this->data['location'] = $this->location_m->get($id, TRUE);
        $this->data['parents'] = $this->location_m->get_parents();

        //exit($this->db->last_query());

        $rules = array (
                array (
                    'field' => 'related_svg_map',
                    'label' => __('Related SVG Map', 'wpdirectorykit'),
                    'rules' => 'required'
                ),
                array (
                    'field' => 'related_svg_map_location',
                    'label' => __('Related SVG Map Location', 'wpdirectorykit'),
                    'rules' => ''
                ),
        );

        if($this->form->run($rules))
        {

            // Check _wpnonce
            check_admin_referer( 'wdk-svg-import', '_wpnonce' );

            // Save procedure for basic data
            $data = $this->location_m->prepare_data(wdk_get_post(), $rules);

            $maps_data_json = $wp_filesystem->get_contents(WDK_SVG_MAP_PATH.'resourse/maps-db.json');
            $maps_data = json_decode($maps_data_json, JSON_OBJECT_AS_ARRAY);
    
            if(isset($maps_data[strtolower(wmvc_show_data('related_svg_map',$data))])) {
                $insert_data = array('parent_id' => wmvc_show_data('related_svg_map_location',$data, NULL));
                foreach ($maps_data[strtolower(wmvc_show_data('related_svg_map',$data))]['locations'] as $location_key => $location) {
                    if(!$this->location_m->get_by(array('parent_id'=>wmvc_show_data('related_svg_map_location',$data, NULL),'location_title'=>esc_html__($location, 'wdk-svg-map')), TRUE))
                        $insert_id = $this->location_m->insert(array_merge($insert_data, array('location_title' => esc_html__($location, 'wdk-svg-map'))), NULL);
                }
            }

        }

        $this->load->view('wdk_location/import_from_svg', $this->data);
    }
    
}
