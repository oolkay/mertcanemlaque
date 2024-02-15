<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_category extends Winter_MVC_Controller {

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
              } 
        }

        $this->load->model('category_m');

        $this->data['categories'] = $this->category_m->get_tree_table();

        // Load view
        $this->load->view('wdk_category/index', $this->data);
    }

    public function delete()
    {
        $id = (int) $this->input->post_get('id');
        $paged = (int) $this->input->post_get('paged');

         // Check _wpnonce
         check_admin_referer( 'wdk-category-delete_'.$id, '_wpnonce' );

        $this->load->model('category_m');
        wdk_access_check('category_m', $id);
        
        $this->category_m->delete($id);

        wp_redirect(admin_url("admin.php?page=wdk_category&paged=$paged"));
    }

    public function bulk_delete($posts_selected)
    {
        // Check _wpnonce
        check_admin_referer( 'wdk-category-bulk', '_wpnonce');
        
        $this->load->model('category_m');
        foreach($posts_selected as $id)
        {
            wdk_access_check('category_m', $id);
            $this->category_m->delete($id);
        }

        wp_redirect(admin_url("admin.php?page=wdk_category&is_updated=true&custom_message=".urlencode(esc_html__('Selected Categories removed', 'wpdirectorykit'))));
        exit();
    }
    
	public function edit()
	{
        $this->load->model('category_m');

        $id = (int) $this->input->post_get('id');
        wdk_access_check('category_m', $id);
        $this->data['db_data'] = NULL;

        $this->data['form'] = &$this->form;

        $this->data['parents'] = $this->category_m->get_parents($id);

        //exit($this->db->last_query());

        $rules = array(
                array(
                    'field' => 'category_title',
                    'label' => __('Title', 'wpdirectorykit'),
                    'rules' => 'required'
                ),
                array(
                    'field' => 'order_index',
                    'label' => __('Order Index', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'font_icon_code',
                    'label' => __('Font icon code', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'marker_image_id',
                    'label' => __('Custom Map Marker Image', 'wpdirectorykit'),
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
                    'field' => 'category_color',
                    'label' => __('Color', 'wpdirectorykit'),
                    'rules' => ''
                ),
        );

        if($this->form->run($rules))
        {
            // Check _wpnonce
            check_admin_referer( 'wdk-category-edit_'.$id, '_wpnonce' );

            // Save procedure for basic data
            $data = $this->category_m->prepare_data(wdk_get_post(), $rules);

            if(empty($id) && strpos($data['category_title'], ',') !== FALSE) {
                
                foreach (explode(',', $data['category_title']) as $title) {
                    // Save standard wp post
                    $insert_id = $this->category_m->insert(array_merge($data, array('category_title' => trim($title))), NULL);
                }
                wp_redirect(admin_url("admin.php?page=wdk_category&is_updated=true&custom_message=".urlencode(esc_html__('Bulk Categories added', 'wpdirectorykit'))));
                exit;
            } else {
                // Save standard wp post
                $data['icon_path'] = wdk_generate_path_image($data['icon_id']);
                $data['image_path'] = wdk_generate_path_image($data['image_id']);
                $data['marker_image_path'] = wdk_generate_path_image($data['marker_image_id']);

                $insert_id = $this->category_m->insert($data, $id);

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

                //exit($this->db->last_error());

                // redirect
                
                if(!empty($insert_id) && empty($id))
                {
                    wp_redirect(admin_url("admin.php?page=wdk_category&id=$insert_id&is_updated=true"));
                    exit;
                }
            }   
        }

        if(!empty($id))
        {
            $this->data['db_data'] = $this->category_m->get($id, TRUE);
        }

        $this->load->view('wdk_category/category_edit', $this->data);
    }
    
}
