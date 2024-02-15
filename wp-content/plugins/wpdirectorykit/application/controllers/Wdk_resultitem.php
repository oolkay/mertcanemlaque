<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_resultitem extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        $this->load->model('resultitem_m');
        $this->load->model('field_m');

        $id = 1;
        wdk_access_check('resultitem_m', $id);
        $this->data['db_data'] = NULL;

        $this->data['form'] = &$this->form;

        $this->data['fields'] = $this->field_m->get();

        $rules = array(
                array(
                    'field' => 'resultitem_name',
                    'label' => __('Name', 'wpdirectorykit'),
                    'rules' => 'required'
                ),
                array(
                    'field' => 'is_multiline_enabled',
                    'label' => __('Multiline enabled', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'is_label_disable',
                    'label' => __('Show only icons', 'wpdirectorykit'),
                    'rules' => ''
                ),
                array(
                    'field' => 'resultitem_json',
                    'label' => __('Search Form Json/Structure', 'wpdirectorykit'),
                    'rules' => ''
                ),
        );

        if($this->form->run($rules))
        {

            // Check _wpnonce
            check_admin_referer( 'wdk-resultitem-edit_'.$id, '_wpnonce' );

            // Save procedure for basic data
            $data = $this->resultitem_m->prepare_data(wdk_get_post(), $rules);

            // Save standard wp post

            if($this->resultitem_m->total() == 0)
            {
                $id = NULL;
                $data['idresultitem'] = 1;
            }

            $insert_id = $this->resultitem_m->insert($data, $id);

            // redirect
            
            if(!empty($insert_id) && empty($id))
            {
                wp_redirect(admin_url("admin.php?page=wdk_resultitem&id=$insert_id&is_updated=true"));
                exit;
            }
        }

        if(!empty($id))
        {
            $this->data['db_data'] = $this->resultitem_m->get($id, TRUE);

            // generate/decode used fields
            $used_fields = NULL;

            if(is_object($this->data['db_data']))
                $used_fields = json_decode($this->data['db_data']->resultitem_json);
            $this->data['used_fields'] = array();
            $this->data['used_fields_sub'] = array();

            if(is_array($used_fields))
            foreach($used_fields as $key=>$used_fields_sub)
            {
                foreach($used_fields_sub as $used_field)
                {
                    $this->data['used_fields'][$used_field->field_id] = $used_field;
                    $this->data['used_fields_sub'][$key+1][$used_field->field_id] = $used_field;
                }
                
            }
        }

        $this->load->view('wdk_resultitem/resultitem_edit', $this->data);
    }
    
}
