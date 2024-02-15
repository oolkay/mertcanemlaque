<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_searchform extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        $this->load->model('searchform_m');
        $this->load->model('field_m');

        $id = 1;
        wdk_access_check('searchform_m', $id);
        $this->data['db_data'] = NULL;

        $this->data['form'] = &$this->form;

        $this->data['fields'] = $this->field_m->get();

        //exit($this->db->last_query());

        $rules = array(
                array(
                    'field' => 'searchform_name',
                    'label' => __('Name', 'wpdirectorykit'),
                    'rules' => 'required'
                ),
                array(
                    'field' => 'searchform_json',
                    'label' => __('Search Form Json/Structure', 'wpdirectorykit'),
                    'rules' => ''
                ),
        );

        if($this->form->run($rules))
        {

            // Check _wpnonce
            check_admin_referer( 'wdk-searchform-edit_'.$id, '_wpnonce' );

            // Save procedure for basic data
            $data = $this->searchform_m->prepare_data(wdk_get_post(), $rules);

            // Save standard wp post

            if($this->searchform_m->total() == 0)
            {
                $id = NULL;
                $data['idsearchform'] = 1;
            }

            $insert_id = $this->searchform_m->insert($data, $id);

            //exit($this->db->last_error().$insert_id);

            // redirect
            
            if(!empty($insert_id) && empty($id))
            {
                wp_redirect(admin_url("admin.php?page=wdk_searchform&id=$insert_id&is_updated=true"));
                exit;
            }
        }

        if(!empty($id))
        {
            $this->data['db_data'] = $this->searchform_m->get($id, TRUE);

            // generate/decode used fields
            $used_fields = NULL;

            if(is_object($this->data['db_data']))
                $used_fields = json_decode($this->data['db_data']->searchform_json);
            $this->data['used_fields'] = array();

            if(is_array($used_fields))
            foreach($used_fields as $used_field)
            {
                $this->data['used_fields'][$used_field->field_id] = $used_field;
            }
        }

        $this->load->view('wdk_searchform/searchform_edit', $this->data);
    }
    
}
