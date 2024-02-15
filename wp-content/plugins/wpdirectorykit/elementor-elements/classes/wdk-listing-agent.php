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
class WdkListinAgent extends WdkElementorBase {

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
        return 'wdk-listing-agent';
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
        return esc_html__('Wdk Listing Agent', 'wpdirectorykit');
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
        return 'eicon-user-circle-o';
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
        global $wdk_listing_id, $Winter_MVC_wdk_membership;

        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        $this->WMVC->model('listing_m');
        $user = NULL;
        $listing = $this->WMVC->listing_m->get($wdk_listing_id, TRUE);
        $this->data['userdata'] = false;
        $this->data['user_id'] = false;

        if($this->data['settings']['user_editor_disabled'] !='yes') {
            if(wmvc_show_data('user_id_editor', $listing, '',TRUE, TRUE)) {
                $this->data['user_id'] = wmvc_show_data('user_id_editor', $listing);
                // Get user data by user id
                $this->data['userdata'] = get_userdata($this->data['user_id']);
            }
        }
        
        $this->WMVC->model('listingusers_m');

        if(Plugin::$instance->editor->is_edit_mode()) {
            $this->WMVC->db->limit(3);
        }

        $this->data['listing_alt_agents'] = array();
        if($this->data['settings']['alternative_agents_disabled'] !='yes') {
            $this->data['listing_alt_agents'] = $this->WMVC->listingusers_m->get($wdk_listing_id);
        }

        $this->data['listing_agency'] = false;
        if($this->data['settings']['listing_agency_disabled'] !='yes') {
            if(function_exists('run_wdk_membership') && file_exists(WDK_MEMBERSHIP_PATH.'application/models/Agency_agent_m.php')) {
                if(wmvc_show_data('user_id_editor', $listing, '',TRUE, TRUE)) {
                    $Winter_MVC_wdk_membership->model('agency_agent_m');
                    $agent_id = wmvc_show_data('user_id_editor', $listing, '',TRUE, TRUE);
                    $agent_agency = $Winter_MVC_wdk_membership->agency_agent_m->get_by(array('agent_id' => $agent_id, 'status' => 'CONFIRMED'), TRUE);
                    if($agent_agency) {
                        $user = wdk_get_user_data( wmvc_show_data('agency_id', $agent_agency, 0));
                        if($user){
                            $this->data['listing_agency'] = $user;
                        }
                    }   
                }
            }
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()) {
            $this->data['is_edit_mode']= true;
            echo $this->view('wdk-listing-agent-demo', $this->data); 
        } else {
            echo $this->view('wdk-listing-agent', $this->data); 
        }
        
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
            'layout',
            [
                'label' => __( 'Layout', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => array(
                    '' => __( 'Horizontal', 'wpdirectorykit' ),
                    'vertical' => __( 'Vertical', 'wpdirectorykit' ),
                ),
                'separator' => 'after',
            ]
        );

        $this->add_control (
			'listing_agency_disabled',
			[
				'label' => __( 'Hide Agency', 'wpdirectorykit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Hide', 'wpdirectorykit' ),
				'label_off' => __( 'Show', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

        $this->add_control (
			'user_editor_disabled',
			[
				'label' => __( 'Hide Agent Editor', 'wpdirectorykit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Hide', 'wpdirectorykit' ),
				'label_off' => __( 'Show', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

        $this->add_control(
			'alternative_agents_disabled',
			[
				'label' => __( 'Hide Alternative agents', 'wpdirectorykit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Hide', 'wpdirectorykit' ),
				'label_off' => __( 'Show', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
                'separator' => 'after',
			]
		);
        $this->add_responsive_control(
            'row_gap_col',
            [
                    'label' => __( 'Columns', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Default', 'wpdirectorykit'),
                        'auto' => esc_html__('Auto', 'wpdirectorykit'),
                        '100%' => '1',
                        '50%' => '2',
                        'calc(100% / 3)' => '3',
                        '25%' => '4',
                        '20%' => '5',
                        'auto_flexible' => 'auto flexible',
                    ],
                    'selectors_dictionary' => [
                        'auto' => '-webkit-flex:1 2 auto;flex:1 2 auto',
                        '100%' =>  '-webkit-flex:1 2 100%;flex:1 2 100%',
                        '50%' =>  '-webkit-flex:1 2 50%;flex:1 2 50%',
                        'calc(100% / 3)' =>  '-webkit-flex:1 2 calc(100% / 3);flex:1 2 calc(100% / 3)',
                        '25%' =>  '-webkit-flex:1 2 25%;flex:1 2 25%',
                        '20%' =>  '-webkit-flex:1 2 20%;flex:1 2 20%',
                        'auto' =>  '-webkit-flex:1 2 auto;flex:1 2 auto',
                        'auto_flexible' =>  '-webkit-flex:1 2 auto;flex:1 2 auto',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-element.wdk-element-listing-agent .wdk-row .wdk-col' => '{{UNIT}}',
                    ],
                    'default' => '100%', 
                    'separator' => 'before',
            ]
    );

    $this->add_responsive_control(
            'column_gap',
            [
                'label' => esc_html__('Columns Gap', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-element.wdk-element-listing-agent .wdk-row .wdk-col' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};;',
                    '{{WRAPPER}} .wdk-element.wdk-element-listing-agent .wdk-row' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
                ],
            ]
    );

    $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Rows Gap', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-element.wdk-element-listing-agent .wdk-row .wdk-col' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wdk-element.wdk-element-listing-agent .wdk-row' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
                ],
            ]
    );


        $this->add_control(
			'thumbnail_hide',
			[
				'label' => __( 'Thumbnail hide', 'wpdirectorykit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wpdirectorykit' ),
				'label_off' => __( 'Hide', 'wpdirectorykit' ),
				'return_value' => 'none',
				'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wdk-element .wdk-listing-agent .agent-thumbnail' => 'display: {{VALUE}};',
                ],
                'separator' => 'before',
			]
		);
             
        $this->add_responsive_control (
            'thumbnail_width',
            [
                'label' => esc_html__('Thumbnail width', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 3000,
                    ],   
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', 'vw' ],
                'default' => [
                    'size' => 90,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-element .wdk-listing-agent .agent-thumbnail' => 'flex: 0 0 {{SIZE}}{{UNIT}};
                                                                                        min-width: {{SIZE}}{{UNIT}};
                                                                                        width: {{SIZE}}{{UNIT}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'layout',
                            'operator' => '==',
                            'value' => '',
                        ],
                        [
                            'name' => 'thumbnail_hide',
                            'operator' => '==',
                            'value' => '',
                        ]
                    ],
                ],
            ]
        );
             
        $this->add_responsive_control (
            'thumbnail_height_max',
            [
                'label' => esc_html__('Max Thumbnail height', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 500,
                    ],   
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-element .wdk-listing-agent .agent-thumbnail img' => 'max-height: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'layout',
                            'operator' => '==',
                            'value' => '',
                        ],
                        [
                            'name' => 'thumbnail_hide',
                            'operator' => '==',
                            'value' => '',
                        ]
                    ],
                ],
            ]
        );
        
        $this->add_responsive_control (
            'thumbnail_height',
            [
                'label' => esc_html__('Thumbnail max height', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 3000,
                    ],   
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', 'vw' ],
                'default' => [
                    'size' => 250,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-element .wdk-listing-agent .agent-thumbnail img' => 'max-height:{{SIZE}}{{UNIT}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'layout',
                            'operator' => '==',
                            'value' => 'vertical',
                        ],
                        [
                            'name' => 'thumbnail_hide',
                            'operator' => '==',
                            'value' => '',
                        ]
                    ],
                ],
            ]
        );

        $meta_fields = array(
            '' => __('Not Selected', 'wpdirectorykit'),
            'wdk_phone' => __('Phone', 'wpdirectorykit'),
            'user_email' => __('Email', 'wpdirectorykit'),
            'user_url' => __('Url', 'wpdirectorykit'),
            'display_name' => __('Display Name', 'wpdirectorykit'),
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

        $repeater = new Repeater();
        $repeater->start_controls_tabs( 'meta_fields' );
        $repeater->add_control(
            'meta_field',
			[
				'label' => __( 'Meta Fields', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'description' => __( 'Only one field per type will be visible', 'wpdirectorykit' ),
				'default' => '',
				'options' =>  $meta_fields
			]
        );

        $repeater->end_controls_tabs();

        
        $this->add_control(
            'meta_title',
            [
                'label' => __( 'Meta Fields', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'meta_fields_list',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'meta_field' => 'wdk_phone',
                    ],
                    [
                        'meta_field' => 'user_email',
                    ],
                ],
                'title_field' => '{{{ meta_field }}}',
            ]
        );
        
        
        $this->end_controls_section();

    }

    private function generate_controls_layout() {
    }


    private function generate_controls_styles() {
        $items = [
            [
                'key'=>'agent_cont',
                'label'=> esc_html__('Content Box', 'wpdirectorykit'),
                'selector'=>'.wdk-listing-agent .agent-cont',
                'options'=> 'block',
            ],
            [
                'key'=>'agent_title',
                'label'=> esc_html__('Agent Name', 'wpdirectorykit'),
                'selector'=>'.wdk-listing-agent .agent-cont .title',
                'options'=>'full',
            ],
            [
                'key'=>'agent_meta',
                'label'=> esc_html__('Agent Meta', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-agent .agent-cont .meta-item',
                'options'=>'full',
            ],
            [
                'key'=>'agent_meta_icon',
                'label'=> esc_html__('Agent Meta Icon', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-agent .agent-cont .meta-item i',
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

            if( $item ['key'] == 'field_value'){
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-agent',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic_align', ['align']);
            }

            $selectors = array(
                'normal' => '{{WRAPPER}} '.$item['selector'],
                'hover'=>'{{WRAPPER}} '.$item['selector'].'%1$s'
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options'],  ['align']);

            $this->end_controls_section();
            /* END special for some elements */
        }

    }

    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-listing-agent');
    }
}
