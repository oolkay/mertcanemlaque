<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_addons_currencies extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        $this->load->view('wdk_addons_currencies/index', $this->data);
    }


}
