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
class WdkButtonLogin extends WdkElementorBase {

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
        return 'wdk-button-login';
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
        return esc_html__('Wdk Button Login', 'wpdirectorykit');
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
        return 'eicon-button';
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

        $this->data['settings']['link_not_login_url'] =  wp_login_url();
        $this->data['settings']['link_login_url'] = wp_logout_url(get_home_url());
        $this->data['settings']['link_dash_url'] = get_admin_url() . "admin.php?page=wdk";

        if(wdk_get_option('wdk_membership_login_page')){
            $this->data['settings']['link_not_login_url'] = get_permalink(wdk_get_option('wdk_membership_login_page'));
        } 
        
        if(function_exists('wdk_dash_url') && wdk_get_option('wdk_membership_dash_page')){
            $this->data['settings']['link_dash_url'] = wdk_dash_url();
        } 

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
        } else {
        }

        echo $this->view('wdk-button-login', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __( 'Background', 'wpdirectorykit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wdk-element .wdk-element-button',
			]
		);

        $this->add_responsive_control(
            'link_not_login_header',
            [
                'label' => esc_html__('Not Login Button', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'link_not_login_text',
            [
                'label' => __('Login Title', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Login', 'wpdirectorykit'),
                'separator' => 'before',
            ]
        );
       
        $this->add_control(
            'link_not_login_id',
            [
                'label' => __('Special attr id for link', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'link_not_login_icon',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-user',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'link_not_login_icon_position',
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

        $this->add_responsive_control(
            'link_login_header',
            [
                'label' => esc_html__('When Login Button', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'link_login_text',
            [
                'label' => __('Logout Text', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'link_login_text_attr',
            [
                'label' => __('Logout Text Attribute', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Logout', 'wpdirectorykit'),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'link_login_id',
            [
                'label' => __('Special attr id for link', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'link_login_icon',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-user',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'link_login_icon_position',
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

        $this->add_responsive_control(
            'link_dash_header',
            [
                'label' => esc_html__('Dashboard Button', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'link_dash_text',
            [
                'label' => __('Dash Text', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'link_dash_text_attr',
            [
                'label' => __('Dash Text Attribute', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Dash', 'wpdirectorykit'),
                'separator' => 'before',
            ]
        );
       
        $this->add_control(
            'link_dash_id',
            [
                'label' => __('Special attr id for link', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'link_dash_icon',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-tachometer-alt',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'link_dash_icon_position',
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

        /* chat button */
        $this->add_responsive_control(
            'link_chat_header',
            [
                'label' => esc_html__('Chat Button', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Only for addon <a href="%1$s" target="_blank"> Live Messages Chat </a>', 'wpdirectorykit' ), '//wpdirectorykit.com/plugins/wp-directory-messages-chat.html'),
                'content_classes' => 'wdk_elementor_hint',
            ]
        );

        
        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-button-login .dash-span .count_messages',
            'hover'=>'{{WRAPPER}} .wdk-button-login .dash-span .count_messages%1$s'
        );
        $this->generate_renders_tabs($selectors, 'link_chat_dynamic', ['margin','typo','color','background','border','border_radius','padding','shadow','transition', 'height', 'width','background_group']);

        $this->end_controls_section();

    }

    private function generate_controls_layout() {

    }

    private function generate_controls_styles() {
        $this->start_controls_section(
            'link_style_section',
            [
                'label' => esc_html__('Style Button', 'wpdirectorykit'),
                'tab' => 'tab_layout'
            ]
        );

        $items = [
            [
                'key'=>'btn',
                'label'=> esc_html__('Button', 'wpdirectorykit'),
                'options'=>['margin','typo','color','background','border','border_radius','padding','shadow','transition'],
            ]
        ];

        foreach ($items as $item) {
            $this->add_responsive_control(
                $item['key'].'_header',
                [
                    'label' => $item['label'],
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-element',
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_box_dynamic', ['align']);

            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-element .wdk-element-button',
                'hover'=>'{{WRAPPER}} .wdk-element .wdk-element-button%1$s'
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

           
            /* END special for some elements */
        }

        $items = [
            [
                'key'=>'icon',
                'label'=> esc_html__('Icon', 'wpdirectorykit'),
                'options'=>'full',
            ]
        ];

        foreach ($items as $item) {
            $this->add_responsive_control(
                $item['key'].'_header',
                [
                    'label' => $item['label'],
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-element .wdk-element-button i,{{WRAPPER}} .wdk-element .wdk-element-button svg',
                'hover'=>'{{WRAPPER}} .wdk-element .wdk-element-button%1$s i,{{WRAPPER}} .wdk-element .wdk-element-button%1$s svg'
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);


            /* END special for some elements */
        }
        $this->end_controls_section();

        /* special for login */
        if(true){
            $this->start_controls_section(
                'link_style_login_section',
                [
                    'label' => esc_html__('Style Special For Login Button', 'wpdirectorykit'),
                    'tab' => 'tab_layout'
                ]
            );

            $items = [
                [
                    'key'=>'btn_login',
                    'label'=> esc_html__('Button', 'wpdirectorykit'),
                    'options'=>'full',
                ]
            ];
    
            foreach ($items as $item) {
                $this->add_responsive_control(
                    $item['key'].'_header',
                    [
                        'label' => $item['label'],
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );
    
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-element .wdk-element-button.login',
                    'hover'=>'{{WRAPPER}} .wdk-element .wdk-element-button.login%1$s'
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);
    
               
                /* END special for some elements */
            }
    
            $items = [
                [
                    'key'=>'icon_login',
                    'label'=> esc_html__('Icon', 'wpdirectorykit'),
                    'options'=>'full',
                ]
            ];
    
            foreach ($items as $item) {
                $this->add_responsive_control(
                    $item['key'].'_header',
                    [
                        'label' => $item['label'],
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-element .wdk-element-button.login i,{{WRAPPER}} .wdk-element .wdk-element-button.login svg',
                    'hover'=>'{{WRAPPER}} .wdk-element .wdk-element-button.login%1$s i,{{WRAPPER}} .wdk-element .wdk-element-button.login%1$s svg'
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);
    
    
                /* END special for some elements */
            }
            $this->end_controls_section();
        }

        /* special for logout */
        if(true){
            $this->start_controls_section(
                'link_style_logout_section',
                [
                    'label' => esc_html__('Style Special For Logout Button', 'wpdirectorykit'),
                    'tab' => 'tab_layout'
                ]
            );

            $items = [
                [
                    'key'=>'btn_logout',
                    'label'=> esc_html__('Button', 'wpdirectorykit'),
                    'options'=>'full',
                ]
            ];
            foreach ($items as $item) {
                $this->add_responsive_control(
                    $item['key'].'_logout_header',
                    [
                        'label' => $item['label'],
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );
    
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-element .wdk-element-button.logout',
                    'hover'=>'{{WRAPPER}} .wdk-element .wdk-element-button.logout%1$s'
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);
    
               
                /* END special for some elements */
            }
    
            $items = [
                [
                    'key'=>'icon_logout',
                    'label'=> esc_html__('Icon', 'wpdirectorykit'),
                    'options'=>['margin','font-size','color','background','border','border_radius','padding'],
                ]
            ];
    
            foreach ($items as $item) {
                $this->add_responsive_control(
                    $item['key'].'_header',
                    [
                        'label' => $item['label'],
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-element .wdk-element-button.logout i,{{WRAPPER}} .wdk-element .wdk-element-button.logout svg',
                    'hover'=>'{{WRAPPER}} .wdk-element .wdk-element-button.logout%1$s i,{{WRAPPER}} .wdk-element .wdk-element-button.logout%1$s svg'
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);
    
    
                /* END special for some elements */
            }
            $this->end_controls_section();
        }
    }


    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-element-button');
    }
}
