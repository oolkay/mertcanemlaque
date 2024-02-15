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
class WdkButton extends WdkElementorBase {

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
        return 'wdk-button';
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
        return esc_html__('Wdk Button', 'wpdirectorykit');
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
        return 'eicon-download-button';
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

        $enable = false;
        if(wmvc_show_data('event_is_login_all', $this->data['settings'])) {
            if(is_user_logged_in()){
                $enable = true;
            }
        }

        if(wmvc_show_data('event_is_not_login', $this->data['settings'])) {
            if(!is_user_logged_in()){
                $enable = true;
            }
        }

        if(wmvc_show_data('event_is_login_custom_user_type', $this->data['settings'])) {
            if(is_user_logged_in()){
                foreach ($this->data['settings']['event_is_login_custom_user_type_list'] as $user_type) {
                    if(wmvc_user_in_role($user_type['role'])) {
                        $enable = true;
                    }
                }
            }
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
        } else {
            if(!$enable) {
                return false;
            }
        }

        echo $this->view('wdk-button', $this->data); 
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
                'label' => __('Text Link', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Text button', 'wpdirectorykit'),
                'separator' => 'before',
            ]
        );
       
        $this->add_control(
            'link_url',
            [
                'label' => __('Link Url', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '#',
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
                    'value' => 'fa fa-home',
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
        $this->end_controls_section();

        $this->start_controls_section(
            'tab_conf_main_section_event',
            [
                'label' => esc_html__('Events When Visible Button', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_control(
            'event_is_not_login',
            [
                'label' => __( 'Visible if Not Login', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'event_is_login_all',
            [
                'label' => __( 'Visible if Login All Users', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'description' => __( 'Disable for set by user type', 'wpdirectorykit' ),
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'true',
                'default' => 'true',
                
            ]
        );

        $this->add_control(
            'event_is_login_custom_user_type',
            [
                'label' => __( 'Visible if Login By Custom user type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'true',
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'event_is_login_all',
                            'operator' => '==',
                            'value' => '',
                        ]
                    ],
                ],
            ]
        );

        global $wp_roles;
        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new \WP_Roles();
        }

        if(true){
            $repeater = new Repeater();
            $repeater->start_controls_tabs( 'user_roles' );
            $repeater->add_control(
                'role',
                [
                    'label' => esc_html__('icon Position', 'wpdirectorykit'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $wp_roles->role_names,
                ]
            );

            $repeater->end_controls_tabs();

                            
            $this->add_control(
                'event_is_login_custom_user_type_list',
                [
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                    ],
                    'title_field' => '{{{ role }}}',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'event_is_login_custom_user_type',
                                'operator' => '==',
                                'value' => 'true',
                            ],
                            [
                                'name' => 'event_is_login_all',
                                'operator' => '==',
                                'value' => '',
                            ]
                        ],
                    ],
                ]
            );

        }

        $this->end_controls_section();
    }

    private function generate_controls_layout() {

    }

    private function generate_controls_styles() {
        $items = [
            [
                'key'=>'btn',
                'label'=> esc_html__('Button', 'wpdirectorykit'),
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
