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
class WdkListingFieldsSection extends WdkElementorBase {

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
        return 'wdk-listing-fields-section';
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
        return esc_html__('Wdk Fields Section', 'wpdirectorykit');
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
        return 'eicon-toggle';
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
        $this->data['wdk_listing_id'] = $wdk_listing_id;

        
        $this->data['section_label'] = 'Example Section';
        $this->data['sections_data'] =  $this->WMVC->field_m->get_fields_section();
        $this->data['section_data'] =  array();

        if(!empty($this->data['settings']['section_id'])){
            $this->data['section_label'] = wdk_field_label($this->data['settings']['section_id']);
            if(isset($this->data['sections_data'][$this->data['settings']['section_id']]))
                $this->data['section_data'] =  $this->data['sections_data'][$this->data['settings']['section_id']];
        }

        if(!empty($this->data['settings']['field_id']))
            $this->data['field_label'] = wdk_field_label($this->data['settings']['field_id']);

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
            if(empty($this->data['section_data'])){
                echo '<p class="wdk_alert wdk_alert-danger">'.wdk_sprintf(esc_html__('Section #%1$s not found', 'wpdirectorykit'), $this->data['settings']['section_id']).'</p>';
                return false;
            }
        } else {
            if(empty($this->data['section_data'])){
                return false;
            }     

            /* return false if no content */
            if($this->data['settings']['hide_onempty_complete'] == 'yes') {
                $complete_empty = true;
                foreach($this->data['section_data']['fields'] as $field) {

                    if(wdk_field_value('category_id', $wdk_listing_id) && wdk_depend_is_hidden_field(wmvc_show_data('idfield', $field), wdk_field_value('category_id', $wdk_listing_id))) {
                        continue;
                    } 

                    if(!empty(wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id))){
                        $complete_empty = false;
                        break;
                    }
                }

                if($complete_empty)
                    return false;
            }
        }

        echo $this->view('wdk-listing-fields-section', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $WMVC = &wdk_get_instance();
        $WMVC->model('field_m'); 

        $sections_list = array('0' => esc_html__('Not Selected', 'wpdirectorykit'));
        foreach($WMVC->field_m->get_sections() as $section_id => $section_label)
        {
            $sections_list[$section_id] = '#'.$section_id.' '.$section_label;
        }

        $this->add_control(
            'section_id',
            [
                'label' => __( 'Section id', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $sections_list,
                'separator' => 'after',
            ]
        );
        
        $this->add_control(
            'label_suffix',
            [
                'label' => __( 'Label suffix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'label_prefix',
            [
                'label' => __( 'Label prefix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );
        
        $this->add_control(
			'hide_onempty',
			[
				'label' => __( 'Hide if empty field', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
        
        $this->add_control(
			'hide_onempty_checkbox',
			[
				'label' => __( 'Hide unchecked Checkboxes', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
        
        $this->add_control(
			'hide_onempty_complete',
			[
				'label' => __( 'Hide section if empty all field', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->add_control(
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Manager Fields <a href="%1$s" target="_blank"> open </a>', 'wpdirectorykit' ), admin_url('admin.php?page=wdk_fields')),
                'content_classes' => 'wdk_elementor_hint',
            ]
        );

        $this->end_controls_section();

    }


    private function generate_controls_layout() {
        /* TAB_STYLE */ 
        $this->start_controls_section(
            'section_form_style',
            [
                'label' => __( 'Grid', 'wpdirectorykit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'field_layout',
            [
                    'label' => __( 'Field Layout', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Default', 'wpdirectorykit'),
                        'reverse' => esc_html__('Reverse', 'wpdirectorykit'),
                        'column' => esc_html__('Column', 'wpdirectorykit'),
                        'column-reverse' => esc_html__('Column Reverse', 'wpdirectorykit'),
                    ],
                    'default' => '', 
                    'separator' => 'before',
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
                        'auto' => 'width:auto;-webkit-flex:0 0 auto;flex:0 0 auto',
                        '100%' =>  'width:100%;-webkit-flex:0 0 100%;flex:0 0 100%',
                        '50%' =>  'width:50%;-webkit-flex:0 0 50%;flex:0 0 50%',
                        'calc(100% / 3)' =>  'width:calc(100% / 3);-webkit-flex:0 0 calc(100% / 3);flex:0 0 calc(100% / 3)',
                        '25%' =>  'width:25%;-webkit-flex:0 0 25%;flex:0 0 25%',
                        '20%' =>  'width:20%;-webkit-flex:0 0 20%;flex:0 0 20%',
                        'auto' =>  'width:auto;-webkit-flex:0 0 auto;flex:0 0 auto',
                        'auto_flexible' =>  'width:auto;-webkit-flex:1 2 auto;flex:1 2 auto',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-row .wdk-col' => '{{UNIT}}',
                    ],
                    'default' => 'calc(100% / 3)', 
                    'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
                'column_gap',
                [
                    'label' => esc_html__('Columns Gap', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 0,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-row .wdk-col' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};;',
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
                        '{{WRAPPER}} .wdk-row .wdk-col' => 'padding-bottom: {{SIZE}}{{UNIT}}; padding-top: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .wdk-row' => 'margin-bottom: -{{SIZE}}{{UNIT}}; margin-top: -{{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_control(
			'item_border_header',
			[
				'label' => __( 'Border', 'wpdirectorykit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
                
        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-row .wdk-col',
        );

        $this->generate_renders_tabs($selectors, 'item_border', ['border']);

        $this->add_control(
			'field_group_position',
			[
				'label' => __( 'Positions', 'wpdirectorykit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
            'field_group_align_h',
            [
                'label' => __( 'Align Horizontal', 'wpdirectorykit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                            'title' => esc_html__( 'Left', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                            'title' => esc_html__( 'Center', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                            'title' => esc_html__( 'Right', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                            'title' => esc_html__( 'Justified', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'render_type' => 'template',
                'selectors_dictionary' => [
                    'left' => 'justify-content: flex-start;',
                    'center' => 'justify-content: center;',
                    'right' => 'justify-content: flex-end;',
                    'justify' => 'justify-content: space-between;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-listing-fields-section .wdk-col .field-group' => '{{VALUE}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_layout',
                            'operator' => '==',
                            'value' => '',
                        ]
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'field_group_align_h_reverse',
            [
                'label' => __( 'Align Horizontal', 'wpdirectorykit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                            'title' => esc_html__( 'Left', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                            'title' => esc_html__( 'Center', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                            'title' => esc_html__( 'Right', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                            'title' => esc_html__( 'Justified', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'render_type' => 'template',
                'selectors_dictionary' => [
                    'left' => 'justify-content: flex-end;',
                    'center' => 'justify-content: center;',
                    'right' => 'justify-content: flex-start;',
                    'justify' => 'justify-content: space-between;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-listing-fields-section .wdk-col .field-group' => '{{VALUE}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_layout',
                            'operator' => '==',
                            'value' => 'reverse',
                        ]
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'field_group_align_v',
            [
                'label' => __( 'Align Vertical', 'wpdirectorykit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                            'title' => esc_html__( 'Top', 'wpdirectorykit' ),
                            'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                            'title' => esc_html__( 'Center', 'wpdirectorykit' ),
                            'icon' => 'eicon-v-align-middle',
                    ],
                    'right' => [
                            'title' => esc_html__( 'Bottom', 'wpdirectorykit' ),
                            'icon' => 'eicon-v-align-bottom',
                    ]
                ],
                'default' => 'center',
                'render_type' => 'template',
                'selectors_dictionary' => [
                    'left' => 'align-items: flex-start;',
                    'center' => 'align-items: center;',
                    'right' => 'align-items: flex-end;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-listing-fields-section .wdk-col .field-group' => '{{VALUE}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_group_icon_enable',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );
        $this->end_controls_section();  

    }

    private function generate_controls_styles() {

        $this->start_controls_section(
            'field_group',
            [
                'label' => esc_html__('Field Icon', 'wpdirectorykit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'field_group_icon_enable',
                [
                    'label' => esc_html__( 'Enable Icon', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
        );

        $this->add_responsive_control (
            'field_group_icon_max_heigth',
            [
                'label' => esc_html__('Max Height', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 1500,
                    ],   
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', 'vw','%' ],
                'default' => [
					'unit' => 'px',
					'size' => 18,
				],
                'selectors' => [
                    '{{WRAPPER}} .field_icon .wdk-icon' => 'max-height: {{SIZE}}{{UNIT}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_group_icon_enable',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );

        $this->add_responsive_control (
            'field_group_icon_max_width',
            [
                'label' => esc_html__('Max Width', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 1500,
                    ],   
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', 'vw','%' ],
                'default' => [
					'unit' => 'px',
					'size' => 18,
				],
                'selectors' => [
                    '{{WRAPPER}} .field_icon .wdk-icon' => 'max-width: {{SIZE}}{{UNIT}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_group_icon_enable',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'field_group_icon',
            [
                    'label' => esc_html__( 'Margin Icon', 'wpdirectorykit' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .field_icon .wdk-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'default' => [
                        'top' => 0,
                        'right' => 4,
                        'bottom' => 0,
                        'left' => 0,
                    ],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'field_group_icon_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
            ]
        );

        $this->add_responsive_control(
            'field_group_icon_position',
            [
                'label' => __( 'Icon Position', 'wpdirectorykit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                            'title' => esc_html__( 'Left', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-left',
                    ],
                    'top' => [
                            'title' => esc_html__( 'Top', 'wpdirectorykit' ),
                            'icon' => 'eicon-v-align-top',
                    ],
                    'right' => [
                            'title' => esc_html__( 'Right', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-right',
                    ],
                    'bottom' => [
                            'title' => esc_html__( 'Bottom', 'wpdirectorykit' ),
                            'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'render_type' => 'template',
                'selectors_dictionary' => [
                    'left' => 'width: auto;',
                    'top' => 'width: 100%;',
                    'right' => 'width:auto;order: 4;',
                    'bottom' => 'width:100%;order: 4;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .field_icon' => '{{VALUE}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_group_icon_enable',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'field_layout',
                            'operator' => '==',
                            'value' => '',
                        ]
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'field_group_icon_position_reverse',
            [
                'label' => __( 'Icon Position', 'wpdirectorykit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                            'title' => esc_html__( 'Left', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-left',
                    ],
                    'top' => [
                            'title' => esc_html__( 'Top', 'wpdirectorykit' ),
                            'icon' => 'eicon-v-align-top',
                    ],
                    'right' => [
                            'title' => esc_html__( 'Right', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-right',
                    ],
                    'bottom' => [
                            'title' => esc_html__( 'Bottom', 'wpdirectorykit' ),
                            'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'render_type' => 'template',
                'selectors_dictionary' => [
                    'left' => 'width: auto;order: 4;',
                    'top' => 'width: 100%;order: 1;',
                    'right' => 'width:auto;order: 1;',
                    'bottom' => 'width:100%;order: 4;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .field_icon' => '{{VALUE}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_group_icon_enable',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'field_layout',
                            'operator' => '==',
                            'value' => 'reverse',
                        ]
                    ],
                ],
            ]
        );
            
        $this->add_responsive_control(
            'field_group_icon_position_align',
            [
                'label' => __( 'Icon Align', 'wpdirectorykit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                            'title' => esc_html__( 'Left', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                            'title' => esc_html__( 'Center', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                            'title' => esc_html__( 'Right', 'wpdirectorykit' ),
                            'icon' => 'eicon-text-align-right',
                    ],
                ],
                'render_type' => 'template',
                'selectors_dictionary' => [
                    'left' => 'text-align:left',
                    'center' => 'text-align:center',
                    'right' => 'text-align:right',
                ],
                'selectors' => [
                    '{{WRAPPER}} .field_icon' => '{{VALUE}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_group_icon_enable',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );
            
        $this->end_controls_section();

        /* dynamic */
        $items = [
            [
                'key'=>'section_title',
                'label'=> esc_html__('Section Title', 'wpdirectorykit'),
                'selector'=>'.section-title',
                'options'=>'full',
            ],
            [
                'key'=>'field_label',
                'label'=> esc_html__('Field Label', 'wpdirectorykit'),
                'selector'=>'.field_label',
                'options'=>['margin','typo','color','padding','transition'],
            ],
            [
                'key'=>'field_prefix',
                'label'=> esc_html__('Field Prefix', 'wpdirectorykit'),
                'selector'=>'.field_value .prefix',
                'options'=>['margin','typo','color','padding','transition'],
            ],
            [
                'key'=>'field_suffix',
                'label'=> esc_html__('Field Suffix', 'wpdirectorykit'),
                'selector'=>'.field_value .suffix',
                'options'=>['margin','typo','color','padding','transition'],
            ],
            [
                'key'=>'field_value',
                'label'=> esc_html__('Field Value', 'wpdirectorykit'),
                'selector'=>'.field_value .value',
                'options'=>['margin','typo','color','padding','transition'],
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

            $this->add_responsive_control(
                $item['key'].'_hide',
                    [
                        'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                        'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                        'return_value' => 'none',
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} '.$item['selector'] => 'display: {{VALUE}};',
                        ],
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

        $this->start_controls_section(
            'field_checkbox_icon',
            [
                'label' => esc_html__('Checkbox', 'wpdirectorykit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
        $this->add_responsive_control(
            'field_checkbox_icon_success_header',
            [
                'label' => esc_html__('Success checkbox', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'field_checkbox_hide',
                [
                    'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                    'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                    'return_value' => 'none',
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listing-fields-section .wdk-col.reverse.CHECKBOX .field_value' => 'display: {{VALUE}};',
                    ],
                ]
        );

        $this->add_responsive_control(
            'field_checkbox_position',
            [
                    'label' => __( 'Columns', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'left' => esc_html__('Left', 'wpdirectorykit'),
                        'right' => esc_html__('Right', 'wpdirectorykit'),
                    ],
                    'selectors_dictionary' => [
                        'left' => 'order:1',
                        'right' =>  'order: 3',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listing-fields-section .wdk-col.reverse.CHECKBOX .field_value' => '{{UNIT}}',
                    ],
                    'default' => 'left', 
                    'separator' => 'before',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'field_layout',
                                'operator' => '==',
                                'value' => '',
                            ]
                        ],
                    ],
            ]
        );
                            
        $this->add_responsive_control(
            'field_checkbox_position_reverse',
            [
                    'label' => __( 'Columns', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'left' => esc_html__('Left', 'wpdirectorykit'),
                        'right' => esc_html__('Right', 'wpdirectorykit'),
                    ],
                    'selectors_dictionary' => [
                        'left' => 'order:3',
                        'right' =>  'order: 1',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listing-fields-section .wdk-col.reverse.CHECKBOX .field_value' => '{{UNIT}}',
                    ],
                    'default' => 'left', 
                    'separator' => 'before',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'field_layout',
                                'operator' => '==',
                                'value' => 'reverse',
                            ]
                        ],
                    ],
            ]
        );
    
        $this->add_control (
            'field_checkbox_icon_success',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-check',
                    'library' => 'solid',
                ],
            ] 
        );
    
        $selectors = array(
            'normal' => '{{WRAPPER}} .field_checkbox_success',
        );
        $this->generate_renders_tabs($selectors, 'field_checkbox_icon_success_dynamic', 'full');
    
            
        $this->add_responsive_control(
            'field_checkbox_icon_unsuccess_header',
            [
                'label' => esc_html__('Unsuccess checkbox', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
    
        $this->add_control(
            'field_checkbox_icon_unsuccess',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-close',
                    'library' => 'solid',
                ],
            ] 
        );
    
        $selectors = array(
            'normal' => '{{WRAPPER}} .field_checkbox_unsuccess',
        );
        
        $this->generate_renders_tabs($selectors, 'field_checkbox_icon_unsuccess_dynamic', 'full');
    
        $this->end_controls_section();
    }


    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-listing-fields-section');
    }
}
