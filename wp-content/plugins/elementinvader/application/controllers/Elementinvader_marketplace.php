<?php

defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Elementinvader_marketplace extends Winter_MVC_Controller {

	public function __construct(){
        parent::__construct();
        
        wp_enqueue_script( 'jquery-magnific-popup', ELEMENTINVADER_URL . 'admin/js/magnific-popup/jquery.magnific-popup.js', false, false, false );
        wp_enqueue_style( 'jquery-magnific-popup', ELEMENTINVADER_URL . 'admin/js/magnific-popup/magnific-popup.css', false, '1.0.0' );
	}
    
    
	public function index()
	{
        // Load view
        $this->load->view('elementinvader_marketplace/index', $this->data);
    }
    
}
