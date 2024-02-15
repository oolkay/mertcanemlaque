<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_fields extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        $this->load->model('field_m');

        $this->data['fields'] = $this->field_m->get();

        //wmvc_dump($this->data['fields']);


        $this->load->view('wdk_fields/index', $this->data);
    }

	public function field_edit()
	{

        $this->load->model('field_m');
        $this->load->model('listingfield_m');

        $field_id = (int) $this->input->post_get('id');
        wdk_access_check('category_m', $field_id);
        $this->data['field_types'] = array(
            'INPUTBOX' =>   __('INPUTBOX', 'wpdirectorykit'),
            'SECTION' =>    __('SECTION', 'wpdirectorykit'),
            'TEXTAREA' =>   __('TEXTAREA', 'wpdirectorykit'),
            'TEXTAREA_WYSIWYG' =>   __('TEXTAREA WYSIWYG', 'wpdirectorykit'),
            'NUMBER' =>     __('NUMBER', 'wpdirectorykit'),
            'DATE' =>       __('DATE', 'wpdirectorykit'),
            'DROPDOWN' =>   __('DROPDOWN', 'wpdirectorykit'),
            'DROPDOWNMULTIPLE' =>   __('DROPDOWN MULTIPLE', 'wpdirectorykit'),
            'CHECKBOX' =>   __('CHECKBOX', 'wpdirectorykit'),
        );
        $this->data['section_list'] = array(
            '' =>   __('Not Selected', 'wpdirectorykit'),
        );
        
        $fields_list = $this->field_m->get();
        $this->data['fields_tree'] = array();
        $this->data['fields_categories'] = array();
        $section = '';
        foreach ($fields_list as $key => $field) {
            if($field->field_type == "SECTION") {
                $section = $field->idfield;
                $this->data['fields_tree'][$section] = array();
            } else {
                $this->data['fields_tree'][$section][$field->idfield] =$field->field_label;
                $this->data['fields_categories'][$field->idfield] = $section;
            }
        }

        $this->db->where(array('field_type'=> 'SECTION'));
        $section_list = $this->field_m->get();
        foreach($section_list as $section) {
            $this->data['section_list'][$section->idfield] = $section->field_label;
        }

        $this->data['db_data'] = NULL;

        $this->data['form'] = &$this->form;

        /* default data */
        if(empty($field_id) && empty($_POST)) {
            $this->data['db_data']['is_visible_frontend'] = 1;
            $this->data['db_data']['is_visible_dashboard'] = 1;
        }

        $rules = array(
            array(
                'field' => 'field_type',
                'label' => __('Field Type', 'wpdirectorykit'),
                'rules' => 'required'
            ),
            array(
                'field' => 'field_label',
                'label' => __('Field Label', 'wpdirectorykit'),
                'rules' => 'required'
            ),
            array(
                'field' => 'hint',
                'label' => __('Hint', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'placeholder',
                'label' => __('Placeholder', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'columns_number',
                'label' => __('Columns number/Width', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'is_visible_frontend',
                'label' => __('Visible on Frontend', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'is_required',
                'label' => __('Field is Required', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'is_price_format',
                'label' => __('Field is Price Format', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'is_visible_dashboard',
                'label' => __('Visible in Dashboard', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'prefix',
                'label' => __('Prefix', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'suffix',
                'label' => __('Suffix', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'values_list',
                'label' => __('Values', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'icon_id',
                'label' => __('Parent', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'validation',
                'label' => __('Validation', 'wpdirectorykit'),
                'rules' => ''
            ),
            array(
                'field' => 'min_length',
                'label' => __('Min Length', 'wpdirectorykit'),
                'rules' => 'is_numerical'
            ),
            array(
                'field' => 'max_length',
                'label' => __('Max Length', 'wpdirectorykit'),
                'rules' => 'is_numerical'
            ),
        );
        
        if($this->form->run($rules))
        {
            // Check _wpnonce
            check_admin_referer( 'wdk-fields-edit_'.$field_id, '_wpnonce' );

            // Save procedure for basic data
 
            $data = $this->field_m->prepare_data(wdk_get_post(), $rules);


            $insert_id = $this->field_m->insert($data, $field_id);

            // check if column exists, add if not exists

            if(!empty($insert_id))
                $this->listingfield_m->create_table_column($data, $insert_id);

            //exit($this->db->last_error());

            /* Change section */
            if($this->input->post('section'))
            if(empty($field_id) || ($field_id && $this->data['fields_categories'][$field_id] != $this->input->post('section'))) {
                /* add section in new category */
                $this->data['fields_tree'][$this->input->post('section')][$insert_id] = true;
                
                /* remove section from old category */
                if(isset($this->data['fields_categories'][$field_id]))
                    unset($this->data['fields_tree'][$this->data['fields_categories'][$field_id]][$field_id]);

                /* indexes by new order and reorder */
                $values = array();
                $order_index = 1;
                foreach( $this->data['fields_tree'] as $key_section => $section)
                {
                    if(empty($key_section)) continue;

                    $values[] = array('order_index' => $order_index, 'idfield' => $key_section);
                    $order_index++;
                    if(is_array($section))
                        foreach($section as $key_field => $field)
                        {
                            $values[] = array('order_index' => $order_index, 'idfield' => $key_field);
                            $order_index++;
                        }
                }
                $this->db->updateBatch( $this->field_m->_table_name, $values, 'idfield');

            }


            // redirect

            if(!empty($insert_id) && empty($field_id))
            {
                wp_redirect(admin_url("admin.php?page=wdk_fields&function=field_edit&id=$insert_id&is_updated=true"));
                exit;
            } else {
                $fields_list = $this->field_m->get();
                $this->data['fields_categories'] = array();
                $section = '';
                foreach ($fields_list as $key => $field) {
                    if($field->field_type == "SECTION") {
                        $section = $field->idfield;
                    } else {
                        $this->data['fields_categories'][$field->idfield] = $section;
                    }
                }
            }
        }

        if(!empty($field_id))
            $this->data['db_data'] = $this->field_m->get($field_id, TRUE);

        $this->load->view('wdk_fields/field_edit', $this->data);
    }

    public function ajax_save_order()
    {
        $this->load->model('field_m');

        $data_fields_list = $this->input->post_get('data_fields_list');
        

        $splitted = explode(';', $data_fields_list);

        $values = array();
        $order_index = 1;
        
        foreach($splitted as $field_id)
        {
            if(!empty($field_id))
                $values[] = array('order_index' => $order_index, 'idfield' => $field_id);
                
            $order_index++;
        }

        echo esc_html($this->db->updateBatch( $this->field_m->_table_name, $values, 'idfield'));

        //wmvc_dump($_POST);
        exit();
    }

    public function delete()
    {
        $field_id = (int) $this->input->post_get('id');
        wdk_access_check('field_m', $field_id);
        // Check _wpnonce
        check_admin_referer( 'wdk-fields-delete_'.$field_id, '_wpnonce' );

        $this->load->model('field_m');

        $this->field_m->delete($field_id);

        wp_redirect(admin_url("admin.php?page=wdk_fields"));
    }
    
}
