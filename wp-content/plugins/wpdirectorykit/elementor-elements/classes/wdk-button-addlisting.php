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
class WdkButtonAddlisting extends WdkElementorBase {

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

       
		if ($this->is_edit_mode_load()) {
            $this->enqueue_styles_scripts();
        }

        parent::__construct($data, $args);

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
        return 'wdk-button-addlisting';
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
        return esc_html__('Wdk Button Add Listing', 'wpdirectorykit');
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
        return 'eicon-dual-button';
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

        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        $this->data['settings']['link_url'] = get_admin_url() . "admin.php?page=wdk_listing";
        if(is_user_logged_in()) {

            if(function_exists('wdk_dash_url') && wdk_dash_url('dash_page=listings&function=edit')){
                if(current_user_can('edit_own_listings') || wmvc_user_in_role('administrator')){
                    $this->data['settings']['link_url'] = wdk_dash_url('dash_page=listings&function=edit');
                } else {
                    $this->data['settings']['link_text'] = esc_html__('My Profile', 'wpdirectorykit');
                    $this->data['settings']['link_url'] = wdk_dash_url('dash_page=profile');
                    $this->data['settings']['link_icon'] = '';
                }
            } else {
                $this->data['settings']['link_url'] =  get_admin_url() . "admin.php?page=wdk_listing";
            }

        } else {
            $redirect = get_admin_url() . "admin.php?page=wdk_listing";
            if(function_exists('wdk_dash_url') && wdk_get_option('wdk_membership_login_page')){
                $redirect = wdk_dash_url('dash_page=listings&function=edit');
            } 
            
            $this->data['settings']['link_url'] =  wp_login_url($redirect);
            if(wdk_get_option('wdk_membership_login_page')){
                $this->data['settings']['link_url'] = wdk_url_suffix(get_permalink(wdk_get_option('wdk_membership_login_page')), 'redirect_to='.urlencode($redirect));
            } 
        }

        
        if(!empty($this->data['settings']['custom_link']['url'])) {
            $this->add_link_attributes( 'custom_link', $this->data['settings']['custom_link'] );
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
        } else {
        }

        echo $this->view('wdk-button-addlisting', $this->data); 
    }

    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_control(
            'link_text',
            [
                'label' => __('Text Button', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Add Listing', 'wpdirectorykit'),
                'separator' => 'before',
            ]
        );
       
        $this->add_control(
            'link_id',
            [
                'label' => __('Secial attr id for link', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'link_icon',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-plus',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'link_icon_position',
            [
                'label' => esc_html__('icon Position', 'wpdirectorykit'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'wpdirectorykit'),
                    'right' => esc_html__('Right', 'wpdirectorykit'),
                ],
                'default' => 'left',
            ]
        );

        $this->add_control(
			'custom_link',
			[
				'label' => esc_html__( 'Link', 'wpdirectorykit' ),
				'desciption' => esc_html__( 'use this url instead default', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'wpdirectorykit' ),
				'options' => [ 'url', 'is_external', 'nofollow' ],
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
					// 'custom_attributes' => '',
				],
				'label_block' => true,
			]
		);

        $this->end_controls_section();
    }

    private function generate_controls_layout() {

    }

    private function generate_controls_styles() {
        $items = [
            [
                'key'=>'btn',
                'label'=> esc_html__('Button', 'wpdirectorykit'),
                'options'=>['margin','typo','color','background','border','border_radius','padding','shadow','transition'],
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
                'normal' => '{{WRAPPER}} .wdk-element .wdk-element-button',
                'hover'=>'{{WRAPPER}} .wdk-element .wdk-element-button%1$s'
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            $this->end_controls_section();
            /* END special for some elements */
        }

        $items = [
            [
                'key'=>'icon',
                'label'=> esc_html__('Icon', 'wpdirectorykit'),
                'options'=>['margin','font-size','color','background','border','border_radius','padding'],
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
                'normal' => '{{WRAPPER}} .wdk-element .wdk-element-button i,{{WRAPPER}} .wdk-element .wdk-element-button svg',
                'hover'=>'{{WRAPPER}} .wdk-element .wdk-element-button%1$s i,{{WRAPPER}} .wdk-element .wdk-element-button%1$s svg'
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            $this->end_controls_section();
            /* END special for some elements */
        }
    }


    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-element-button');
    }
}
