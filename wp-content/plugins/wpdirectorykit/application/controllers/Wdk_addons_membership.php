<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_addons_membership extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        $this->load->view('wdk_addons_membership/index', $this->data);
    }


}
