<?php

namespace Wdk\Elementor\Widgets;

use Wdk\Elementor\Widgets\WdkElementorBase;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Typography;
use Elementor\Editor;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Core\Schemes;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * @since 1.1.0
 */
class WdkListingAgentField extends WdkElementorBase {

    public $field_id = NULL;
    public $fields_list = array();

    public function __construct($data = array(), $args = null) {

        \Elementor\Controls_Manager::add_tab(
            'tab_conf',
            esc_html__('Settings', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_layout',
            esc_html__('Layout', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_content',
            esc_html__('Main', 'wpdirectorykit')
        );

        add_action( 'elementor/editor/after_enqueue_styles', function()
        {
            wp_add_inline_style( 'elementor-editor', '.elementor-control-content select option[value*="section"]{color:#fff;background:#000}');
        } ); 
        
		if ($this->is_edit_mode_load()) {
            $this->enqueue_styles_scripts();
        }

        parent::__construct($data, $args);

    }

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'wdk-elementor-listing-preview' ];
	}

    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'wdk-listing-agent-field';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Wdk Listing Agent Field', 'wpdirectorykit');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-animation-text';
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function register_controls() {
        $this->generate_controls_conf();
        $this->generate_controls_layout();
        $this->generate_controls_styles();
        $this->generate_controls_content();
        
        $this->insert_pro_message('1');
        parent::register_controls();
    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function render() {
        parent::render();
        global $wdk_listing_id;

        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        $this->data['field_value'] = '';
        $this->data['field_prefix'] = $this->data['settings']['field_prefix'];
        $this->data['field_suffix'] = $this->data['settings']['field_suffix'];

        if(!Plugin::$instance->editor->is_edit_mode()){
            $this->WMVC->model('listing_m');
            if($this->data['settings']['index_user'] == 0) {
                $listing = $this->WMVC->listing_m->get($wdk_listing_id, TRUE);
                if($listing && !empty(wmvc_show_data('user_id_editor', $listing)) ) {
                    $userdata = get_userdata(wmvc_show_data('user_id_editor', $listing));
                    if($userdata && !empty(wmvc_show_data($this->data['settings']['field_id'], $userdata))) {
                        $this->data['field_value'] = wmvc_show_data($this->data['settings']['field_id'], $userdata);
                    }
                }
            } else {

                $this->WMVC->model('listingusers_m');

                $this->WMVC->db->limit(1);
                $this->WMVC->db->offset($this->data['settings']['index_user']-1);
    
                $listing_alt_agents = $this->WMVC->listingusers_m->get($wdk_listing_id);
                if(!empty($listing_alt_agents)) {
                    $listing_alt_agents = current($listing_alt_agents);
                    $userdata = get_userdata(wmvc_show_data('user_id', $listing_alt_agents));
                    if($userdata && !empty(wmvc_show_data($this->data['settings']['field_id'], $userdata))) {
                        $this->data['field_value'] = wmvc_show_data($this->data['settings']['field_id'], $userdata);
                    }
                }

            }

        }
            
        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
            if(!empty($this->data['settings']['field_id'])) {
                $this->data['field_value'] .=  esc_html__('Example Field').' '.$this->data['settings']['field_id'];
            } else {
                $this->data['field_value'] = esc_html__('Please Set Field');
            }

        } else {
            /* return false if no content */
            if(empty($this->data['field_value']))
                return false;
        }

        echo $this->view('wdk-listing-agent-field', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $meta_fields = array(
            '' => __('Not Selected', 'wpdirectorykit'),
            'display_name' => __('Display Name', 'wpdirectorykit'),
            'wdk_phone' => __('Phone', 'wpdirectorykit'),
            'user_email' => __('Email', 'wpdirectorykit'),
            'user_url' => __('Url', 'wpdirectorykit'),
            'description' => __('Bio', 'wpdirectorykit'),
            'wdk_facebook' => __('Facebook', 'wpdirectorykit'),
            'wdk_youtube' => __('Youtube', 'wpdirectorykit'),
            'wdk_address' => __('Address', 'wpdirectorykit'),
            'wdk_city' => __('City', 'wpdirectorykit'),
            'wdk_position_title' => __('Position Title', 'wpdirectorykit'),
            'wdk_linkedin' => __('Linkedin', 'wpdirectorykit'),
            'wdk_twitter' => __('Twitter', 'wpdirectorykit'),
            'wdk_telegram' => __('Telegram', 'wpdirectorykit'),
            'wdk_whatsapp' => __('What`s App', 'wpdirectorykit'),
            'wdk_viber' => __('Viber', 'wpdirectorykit'),
            'wdk_iban' => __('IBAN', 'wpdirectorykit'),
            'wdk_company_name' => __('Company name', 'wpdirectorykit'),
            'agency_name' => __('Agency Name', 'wpdirectorykit'),
        );

        $this->add_control(
            'field_id',
            [
                'label' => __( 'Field id', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $meta_fields,
                'separator' => 'after',
            ]
        );
                                
        $this->add_control(
            'index_user',
            [
                'label' => __( 'Agent Index', 'wpdirectorykit' ),
                'description' => __( 'Agent id, where  0 is user edit, then 1,2,3,4 ... is alternative agent by index', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'max' => 100,
                'step' => 1,
            ]
        );

        $this->add_control(
            'field_suffix',
            [
                'label' => __( 'Label suffix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'field_prefix',
            [
                'label' => __( 'Label prefix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );
        
        $this->end_controls_section();

    }

    private function generate_controls_layout() {
    }


    private function generate_controls_styles() {
        $items = [
            [
                'key'=>'field_value',
                'label'=> esc_html__('Field Label', 'wpdirectorykit'),
                'selector'=>'.wdk-listing-agent-field',
                'options'=>'full',
            ]
        ];


        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => 'tab_layout'
                ]
            );

            $selectors = array(
                'normal' => '{{WRAPPER}} '.$item['selector'],
                'hover'=>'{{WRAPPER}} '.$item['selector'].'%1$s'
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);


            $this->end_controls_section();
            /* END special for some elements */
        }
    }

    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
      
    }
}
