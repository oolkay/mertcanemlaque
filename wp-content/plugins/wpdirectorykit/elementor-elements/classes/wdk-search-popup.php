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
class WdkSearchPopup extends WdkElementorBase {

    public function __construct($data = array(), $args = null) {

        \Elementor\Controls_Manager::add_tab(
            'tab_conf',
            esc_html__('Settings', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_contructor',
            esc_html__('Constructor', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_layout',
            esc_html__('Style', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_form_styles',
            esc_html__('Form Styles', 'wpdirectorykit')
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
        return 'wdk-search-popup';
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
        return esc_html__('Wdk Search Popup', 'wpdirectorykit');
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
        return 'eicon-search';
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

        wp_enqueue_script('select2');
        wp_enqueue_script('wdk-select2');
        wp_enqueue_style('select2');
  
        $qr_string = trim($this->data['settings']['conf_predefields_query'],'?');
        $string_par = array();
        parse_str($qr_string, $string_par);
        $this->data['predefields_query'] = array_map('trim', $string_par);

        if(!empty($this->data['settings']['custom_category_root'])) {
            $this->data['predefields_query']['custom_category_root'] = 
                                substr($this->data['settings']['custom_category_root'], strpos($this->data['settings']['custom_category_root'],'__')+2);
        }

        if(!empty($this->data['settings']['custom_location_root'])) {
            $this->data['predefields_query']['custom_location_root'] = 
                                substr($this->data['settings']['custom_location_root'], strpos($this->data['settings']['custom_location_root'],'__')+2);
        }

        $this->data['is_edit_mode'] = false;          
        if(Plugin::$instance->editor->is_edit_mode()) {
            $this->data['is_edit_mode'] = true;
        }

        echo $this->view('wdk-search-popup', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
			'section_config',
			[
				'label' => __( 'Configuration', 'wpdirectorykit' ),
			]
		);
                
		$this->add_control(
			'auto_search_enable',
			[
				'label' => __( 'Auto Search Enable', 'wpdirectorykit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'wpdirectorykit' ),
				'label_off' => __( 'Hide', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
                
        $pages = array('' => __('Not Selected', 'wpdirectorykit'));
        foreach(get_pages(array('sort_column' => 'post_title')) as $page)
        {
            $pages[$page->ID] = $page->post_title.' #'.$page->ID;
        }
        
		$this->add_control(
			'conf_link',
			[
				'label' => __( 'Open results on page', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' =>  $pages
			]
		);
        
        $this->add_responsive_control(
            'search_scroll',
            [
                    'label' => __( 'On search scroll to', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('No scroll', 'wpdirectorykit'),
                        'results' => esc_html__('Results', 'wpdirectorykit'),
                        'wdk_map_results' => esc_html__('Map', 'wpdirectorykit'),
                    ],
                    'default' => 'results', 
            ]
        );

        $WMVC = &wdk_get_instance();
        $WMVC->model('field_m');
		$fields = $WMVC->field_m->get_by(array('field_type' => 'DROPDOWN'));

        $fields_list = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
        $order_i = 0;
        foreach($fields as $field)
        {
            $fields_list[(++$order_i).'__'.wmvc_show_data('idfield', $field)] = '#'.wmvc_show_data('idfield', $field).' '.wmvc_show_data('field_label', $field);
        }

        $this->add_control(
            'conf_predefields_query',
            [
                'label' => __( 'Default Search Fields Values', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 5,
                'default' => '',
                'placeholder' => __( 'Type your query here, example xxx', 'wpdirectorykit' ),
                'description' => '<span style="word-break: break-all;">'.__( 'Example (same like on url):', 'wpdirectorykit' ).
                                  ' field_6_min=100&field_6_max=200&field_5=rent&is_featured=on&search_category=3&search_location=4'.
                                  '</span>',
            ]
        );

        $WMVC = &wdk_get_instance();
        $WMVC->model('category_m');
        $WMVC->model('location_m');
		$categories_data = $WMVC->category_m->get_by(array('(parent_id = 0 OR parent_id IS NULL)' => NULL));
        $categories_list = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
        $order_i = 0;

        foreach($categories_data as $category)
        {
            $categories_list[(++$order_i).'__'.wmvc_show_data('idcategory', $category)] = '#'.wmvc_show_data('idcategory', $category).' '.wmvc_show_data('category_title', $category);
        }
        $this->add_control(
            'custom_category_root',
            [
                    'label' => __( 'Custom Category Root', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => $categories_list,
                    'default' => 'results', 
            ]
        );
        
		$locations_data = $WMVC->location_m->get_by(array('(parent_id = 0 OR parent_id IS NULL)' => NULL));
        $locations_list = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
        $order_i = 0;

        foreach($locations_data as $location)
        {
            $locations_list[(++$order_i).'__'.wmvc_show_data('idlocation', $location)] = '#'.wmvc_show_data('idlocation', $location).' '.wmvc_show_data('location_title', $location);
        }
        $this->add_control(
            'custom_location_root',
            [
                    'label' => __( 'Custom Location Root', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => $locations_list,
                    'default' => 'results', 
            ]
        );       

        $this->end_controls_section();
    }

    private function generate_controls_layout() {

        /* Buttons Search / Save / More */ 
        $this->start_controls_section(
            'section_filter_button',
            [
                'label' => __( 'Open Form Button', 'wpdirectorykit' ),
                  'tab' => 'tab_contructor',
            ]
        );

        $this->add_control(
            'text_toggle_button',
            [
                'label' => __( 'Text for open Filters Button', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( 'Filters', 'wpdirectorykit' ),
            ]
        );

        $this->add_responsive_control(
            'section_filter_button_header_1',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'text_toggle_button_icon',
            [
                'label' => __( 'Icon', 'text-domain' ),
                'type' => Controls_Manager::ICONS,
            ]
        );

        $this->add_control(
            'text_toggle_button_icon_position',
            [
                'label' => esc_html__('icon Position', 'wdk-compare-listing'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'wdk-compare-listing'),
                    'right' => esc_html__('Right', 'wdk-compare-listing'),
                ],
                'default' => 'left',
            ]
        );

        $selectors = array();
        $selectors['normal'] = '{{WRAPPER}} .toggle-btn .icon_popup';
        $selectors['hover'] = '{{WRAPPER}} .toggle-btn .icon_popup%1$s';
        $this->generate_renders_tabs($selectors, 'text_toggle_button_icon_dynamic', array('margin','font-size'));

        $this->add_responsive_control(
            'section_filter_button_header_2',
            [
                'label' => esc_html__('Button Styles', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $selectors = array();
        $selectors['normal'] = '{{WRAPPER}} .toggle-btn';
        $selectors['hover'] = '{{WRAPPER}} .toggle-btn%1$s';
        $this->generate_renders_tabs($selectors, 'text_toggle_button_dynamic', array('align','margin','typo','color','border','border_radius','padding','shadow','transition', 'height', 'width','background_group'));
        
        $this->end_controls_section();  
        
        /* TAB_STYLE */ 
        $this->start_controls_section(
            'section_form_style',
            [
                'label' => __( 'Search Form Popup', 'wpdirectorykit' ),
                  'tab' => 'tab_contructor',
            ]
        );

        $this->add_control(
            'text_popup',
            [
                'label' => __( 'Popup Title Text', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( 'Search Form', 'wpdirectorykit' ),
                'render_type' => 'ui'
            ]
        );

        $this->add_responsive_control(
            'row_gap_col_inline',
            [
                    'label' => __( 'Gaps', 'wpdirectorykit' ),
                    'type' => Controls_Manager::GAPS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'vm', 'custom' ],
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
                    'selectors' => [
                        '#wdk_search_popup_modal_'.$this->get_id().' .wdk-fields-list' => 'gap:{{ROW}}{{UNIT}} {{COLUMN}}{{UNIT}}',
                    ],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'separator' => 'before',
                   
            ]
        );

        $this->add_responsive_control(
            'section_form_style_heigth',
           [
               'label' => esc_html__('Height', 'wpdirectorykit'),
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
               'selectors' => [
                    '#wdk_search_popup_modal_'.$this->get_id().' .modal-dialog' => 'height: {{SIZE}}{{UNIT}}',
               ],
               
           ]
       );

       $this->add_responsive_control(
            'section_form_style_width',
           [
               'label' => esc_html__('Width', 'wpdirectorykit'),
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
               'selectors' => [
                '#wdk_search_popup_modal_'.$this->get_id().' .modal-dialog' => 'width: {{SIZE}}{{UNIT}}',
               ],
               
           ]
        );

                      
        $this->add_control(
            'section_form_style_header_hr_1',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->add_responsive_control(
            'section_form_style_header_1',
            [
                'label' => esc_html__('Popup Styles', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );
                    
        $this->add_control(
            'section_form_style_hr_2',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $selectors = array();
        $selectors['normal'] = '#wdk_search_popup_modal_'.$this->get_id().' .wdk-modal .modal-dialog';
        $this->generate_renders_tabs($selectors, 'section_form_style_popup_dynamic', array('border','border_radius','shadow'));

                      
        $this->add_control(
            'section_form_style_header_hr_3',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->add_responsive_control(
            'section_form_style_header_5',
            [
                'label' => esc_html__('Popup Header Styles', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );
                    
        $this->add_control(
            'section_form_style_header_hr_4',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $selectors = array();
        $selectors['normal'] = '#wdk_search_popup_modal_'.$this->get_id().' .wdk-modal .modal-dialog .modal-header';
        $this->generate_renders_tabs($selectors, 'section_form_style_header_dynamic', array('align','typo','color','padding','background_group'));

        /*          
        $this->add_control(
            'section_form_style_header_hr_5',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );*/

        $this->add_responsive_control(
            'section_form_style_header_2',
            [
                'label' => esc_html__('Popup Content Styles', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );
                    
        $this->add_control(
            'section_form_style_header_hr_6',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $selectors = array();
        $selectors['normal'] = '#wdk_search_popup_modal_'.$this->get_id().' .wdk-modal .modal-dialog .modal-body';
        $this->generate_renders_tabs($selectors, 'section_form_style_body_dynamic', array('padding','background_group'));
        /*                   
        $this->add_control(
            'section_form_style_header_hr_7',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        */
        $this->add_responsive_control(
            'section_form_style_header_3',
            [
                'label' => esc_html__('Popup Footer Styles', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );
                    
        $this->add_control(
            'section_form_style_header_hr_8',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $selectors = array();
        $selectors['normal'] = '#wdk_search_popup_modal_'.$this->get_id().' .wdk-modal .modal-dialog .modal-footer';
        $this->generate_renders_tabs($selectors, 'section_form_style_footer_dynamic', array('padding','background_group'));
        
        $this->add_control(
			'heading_suc_message',
			[
				'label' => __( 'Fields', 'wpdirectorykit' ),
				'type' => Controls_Manager::HEADING,
			]
		);
                
		$this->add_control(
			'fields_height',
			[
                'label' => __( 'Fields height', 'wpdirectorykit' ),
				'type' => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 400,
					],
				],
                'selectors' => [
					'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field label.checkbox, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="text"],#wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="number"], #wdk_search_popup_modal_'.$this->get_id().' .wdk-field select' => 'height: {{SIZE}}{{UNIT}};',
					'#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree > .btn-group' => 'height: {{SIZE}}{{UNIT}};',
					'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CHECKBOX' => 'height: {{SIZE}}{{UNIT}};',
					'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field button.wdk-search-popup-additional-btn, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field button.wdk-search-popup-start' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
                
		$this->add_control(
			'fields_height_multi',
			[
                'label' => __( 'Select Multiple', 'wpdirectorykit' ),
				'type' => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 400,
					],
				],
                'selectors' => [
					'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field select[multiple="multiple"]' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        if(true){

            $fields_data = wdk_cached_field_get();
            $fields_list = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
            $order_i = 0;
            $fields_allow_types = array();
            $fields_allow_tree_types = array();

            $fields_list [(++$order_i).'__section'] = esc_html__('-- Section Custom fields --', 'wpdirectorykit');
            $fields_list [(++$order_i).'__search'] = esc_html__('Smart Search', 'wpdirectorykit');

            if(function_exists('run_wdk_bookings'))
                $fields_list [(++$order_i).'__booking_date'] = esc_html__('Booking Date', 'wpdirectorykit');

            $fields_list [(++$order_i).'__post_title'] = esc_html__('WP Title', 'wpdirectorykit');
            $fields_list [(++$order_i).'__address'] = esc_html__('Address', 'wpdirectorykit');
            $fields_list [(++$order_i).'__category_id'] = esc_html__('Category', 'wpdirectorykit');
            $fields_allow_tree_types[] = $order_i.'__category_id';
            $fields_list [(++$order_i).'__location_id'] = esc_html__('Location', 'wpdirectorykit');
            $fields_allow_tree_types[] = $order_i.'__location_id';

            foreach($fields_data as $field)
            {
                if(in_array(wmvc_show_data('field_type', $field),array('TEXTAREA','TEXTAREA_WYSIWYG'))) {
                    continue;
                } else if(in_array(wmvc_show_data('field_type', $field),array('SECTION'))) {
                    $fields_list [(++$order_i).'section__'.wmvc_show_data('idfield', $field)] = '-- '.esc_html__('Section', 'wpdirectorykit').' '.wmvc_show_data('field_label', $field).' --';
                } else {
                    $fields_list[(++$order_i).'__'.wmvc_show_data('idfield', $field)] = '#'.wmvc_show_data('idfield', $field).' '.wmvc_show_data('field_label', $field).'['.wmvc_show_data('field_type', $field).']';
                }

                if(wmvc_show_data('field_type', $field) == 'NUMBER') {
                    $fields_allow_types[] = $order_i.'__'.wmvc_show_data('idfield', $field);
                }
            }

            $repeater = new Repeater();
            $repeater->start_controls_tabs( 'custom_fields_repeat' );
                
            $repeater->add_control(
                'field_id',
                [
                    'label' => __( 'Field id', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '',
                    'options' => $fields_list,
                    'separator' => 'after',
                ]
            );
                
            $repeater->add_control(
                'field_css_class',
                [
                    'label' => __( 'Css Class', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                ]
            );
                
            $repeater->add_control(
                'field_placeholder',
                [
                    'label' => __( 'Placeholder', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => $repeater->get_id(),
                ]
            );

                            
            $repeater->add_control(
                'field_columns_number',
                [
                    'label' => __( 'Columns/Width', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'min' => '0',
                    'max' => '12',
                    'step' => '1',
                    'default' => '',
                ]
            );
                
            $repeater->add_control(
                'field_columns_width',
                [
                        'label' => __( 'Columns', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            'auto' => esc_html__('Auto', 'wpdirectorykit'),
                            '25%' => '25%',
                            '50%' => '50%',
                            '100%' => "100%"
                        ],
                        'selectors_dictionary' => [
                            'auto' => 'width:auto;-webkit-flex:0 0 auto;flex:0 0 auto',
                            '100%' =>  'grid-column: span 12;',
                            '50%' =>  'grid-column: span 6;',
                            'calc(100% / 3)' =>  'grid-column: span 4;',
                            '25%' =>  'grid-column: span 3;',
                            '20%' =>  'width:20%;-webkit-flex:0 0 20%;flex:0 0 20%',
                            'auto' =>  'grid-column: span 12;',
                        ],
                        'default' => '100%', 
                        'separator' => 'before',
                        'selectors' => [
							'#wdk_search_popup_modal_'.$this->get_id().' .wdk-fields-list {{CURRENT_ITEM}}' => '{{UNIT}}',
						],
                ]
            );

            $repeater->add_control(
                'search_type_tree',
                [
                    'label' => __( 'Field Layout', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '',
                    'options' => array(
                        '' => __( 'Default', 'wpdirectorykit' ),
                        '_checkboxes' => __( 'Checkboxes', 'wpdirectorykit' ),
                        /*'_tree' => __( 'Tree with Search', 'wpdirectorykit' ),
                        '_multi_selector' => __( 'Tree multi Select', 'wpdirectorykit' ),
                        '_multi_selects' => __( 'Multi Selects', 'wpdirectorykit' ),*/
                    ),
                    'condition' => [
                        'field_id' => $fields_allow_tree_types,
                    ],
                ]
            );

            $repeater->add_control(
                'search_type_tree_hide',
                [
                    'label' => __( 'Hide by id', 'wpdirectorykit' ),
                    'description' => __( 'Hide locations/categories based on id, example 1,2,3,4,xxx', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                    'condition' => [
                        'field_id' => $fields_allow_tree_types,
                    ],
                ]
            );

            $repeater->add_control(
                'search_type',
                [
                    'label' => __( 'Search Type', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '',
                    'options' => array(
                        '' => __( 'None', 'wpdirectorykit' ),
                        'min' => __( 'Min', 'wpdirectorykit' ),
                        'max' => __( 'Max', 'wpdirectorykit' ),
                        'min_max' => __( 'Min/Max', 'wpdirectorykit' ),
                        'slider_range' => __( 'Slider Range', 'wpdirectorykit' ),
                    ),
                    'condition' => [
                        'field_id' => $fields_allow_types,
                    ],
                ]
            );

            $repeater->add_control(
                'value_min',
                [
                    'label' => __( 'Value Min', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => '',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'search_type',
                                'operator' => '==',
                                'value' => 'slider_range',
                            ]
                        ],
                    ],
                ]
            );

            $repeater->add_control(
                'value_max',
                [
                    'label' => __( 'Value Max', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => '',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'search_type',
                                'operator' => '==',
                                'value' => 'slider_range',
                            ]
                        ],
                    ],
                ]
            );

            $repeater->end_controls_tabs();

                        
            $this->add_control(
                'custom_fields_header_main_hr_1',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $this->add_responsive_control(
                'custom_fields_header_main_1',
                [
                    'label' => esc_html__('Popup Constructor', 'wpdirectorykit').':',
                    'type' => Controls_Manager::HEADING,
                ]
            );
            /*        
            $this->add_control(
                'custom_fields_header_main_hr_2',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );*/
                        
            $this->add_control(
                'custom_fields_header_hr_1',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $this->add_responsive_control(
                'custom_fields_header_1',
                [
                    'label' => esc_html__('Search Fields:', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                ]
            );
                        
            $this->add_control(
                'custom_fields_header_hr_2',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );


            $this->add_control(
                'custom_fields',
                [
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'label' => __( 'Fields', 'wpdirectorykit' ),
                    'render_type' => 'template',
                    'default' => [
                    ],
                    'title_field' => "<# "
                                        . "let labels = ".json_encode($fields_list)."; "
                                        . "let label = labels[field_id]; "
                                    . "#>"
                                    . "{{{ label }}}",
                ]
            );
        }

        if(true){

            $action_list_button = array(
                'button_reset' => __( 'Reset Button', 'wpdirectorykit' ),
                'button_search' => __( 'Search Button', 'wpdirectorykit' ),
                'button_close' => __( 'Close Button', 'wpdirectorykit' ),
            );

            $repeater = new Repeater();
            $repeater->start_controls_tabs( 'custom_buttons_repeat' );
                
            $repeater->add_control(
                'action_list_field_id',
                [
                    'label' => __( 'Elements', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '',
                    'options' => $action_list_button,
                    'separator' => 'after',
                    'render_type' => 'template',
                ]
            );
                
            $repeater->add_control(
                'placeholder',
                [
                    'label' => __( 'Title of button', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                ]
            );

            
                
            $repeater->end_controls_tabs();

                        
            $this->add_control(
                'custom_fields_header_hr_3',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $this->add_responsive_control(
                'custom_fields_header_2',
                [
                    'label' => esc_html__('Footer Actions:', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                ]
            );
                        
            $this->add_control(
                'custom_fields_header_hr_4',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $this->add_control(
                'custom_buttons',
                [
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'label' => __( 'Buttons', 'wpdirectorykit' ),
                    'default' => [
                        [
                            'action_list_field_id' => 'button_search',
                        ],
                        [
                            'action_list_field_id' => 'button_reset',
                        ],
                        [
                            'action_list_field_id' => 'button_close',
                        ],
                    ],
                    'title_field' => "<# "
                                        . "let labels = ".json_encode($action_list_button)."; "
                                        . "let label = labels[action_list_field_id]; "
                                    . "#>"
                                    . "{{{ label }}}",
                ]
            );

        }

        $this->end_controls_section();  

    }

    private function generate_controls_styles() {
        $items = [
            [
                'key'=>'field_label',
                'label'=> esc_html__('Field Label', 'wpdirectorykit'),
                //'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field:not(.CHECKBOX) .wdk-field-label',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field:not(.CHECKBOX) .wdk-field-label',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field:not(.CHECKBOX) .wdk-field-label%1$s',
                'options'=>'full',
            ],
            [
                'key'=>'field_text',
                'label'=> esc_html__('Field Text/Integer', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.INPUTBOX,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.NUMBER,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.TEXTAREA',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="text"],#wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="number"]',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="text"]%1$s,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="number"]%1$s',
                'selector_focus'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="text"]:focus,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="number"]:focus',
                'options'=>'full',
            ],
            [
                'key'=>'field_select',
                'label'=> esc_html__('Field Select', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWN',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWN select',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWN select%1$s',
                'options'=>'full',
            ], 
            [
                'key'=>'field_checkbox',
                'label'=> esc_html__('Field Checkbox', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CHECKBOX',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CHECKBOX .wdk-field-label',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CHECKBOX .wdk-field-label%1$s',
                'options'=>'full',
            ],
            [
                'key'=>'field_tree',
                'label'=> esc_html__('Field Category / Location', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree .btn-group, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY  .select2, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION  .select2',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree .btn-group%1$s, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY  .select2%1$s, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION  .select2%1$s',
                'options'=> ['margin','background','border','border_radius','padding','shadow','transition'],
            ],
            [
                'key'=>'field_select2',
                'label'=> esc_html__('Dropdown Multi-Select', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWNMULTIPLE',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWNMULTIPLE .select2',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWNMULTIPLE .select2%1$s',
                'options'=>['margin','background','border','border_radius','padding','shadow','transition'],
            ],
            [
                'key'=>'field_slider_range',
                'label'=> esc_html__('Field Slider Range', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.SLIDER_RANGE',
                'selector'=>'',
                'selector_hover'=>'',
                'options'=>'',
            ],
            [
                'key'=>'field_button_search',
                'label'=> esc_html__('Search Button', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .modal-footer button.wdk-button-search-start',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .modal-footer button.wdk-button-search-start',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .modal-footer button.wdk-button-search-start%1$s',
                'options'=>'full',
            ],
            [
                'key'=>'field_button_reset',
                'label'=> esc_html__('Reset Button', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .modal-footer .wdk-button-search-reset',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .modal-footer .wdk-button-search-reset',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().'.modal-footer .wdk-button-search-reset%1$s',
                'options'=>['typo','color','border','border_radius','shadow','transition','background_group'],
            ],   
            [
                'key'=>'field_button_close',
                'label'=> esc_html__('Close Button', 'wpdirectorykit'),
                'selector_hide'=>'#wdk_search_popup_modal_'.$this->get_id().' .modal-footer .wdk-button-close',
                'selector'=>'#wdk_search_popup_modal_'.$this->get_id().' .modal-footer .wdk-button-close',
                'selector_hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .modal-footer .wdk-field .wdk-button-close%1$s',
                'options'=>['typo','color','border','border_radius','shadow','transition','background_group'],
            ],   
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => 'tab_form_styles',
                ]
            );

            if($item['key'] == 'field_label') {
                $this->add_responsive_control (
                    'f_label_hide',
                        [
                                'label' => esc_html__( 'Field Label Hide', 'wpdirectorykit' ),
                                
                                'type' => Controls_Manager::SWITCHER,
                                'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                                'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                                'return_value' => 'none',
                                'default' => '',
                                'selectors' => [
                                    '#wdk_search_popup_modal_'.$this->get_id().' .wdk-field:not(.CHECKBOX) .wdk-field-label' => 'display: {{VALUE}};',
                                ],
                                'separator' => 'before',
                        ] 
                );
            } else {

                if(!empty($item['selector_hide'])) {
                    $this->add_responsive_control(
                        $item['key'].'_hide',
                        [
                            'label' => esc_html__( 'Hide Element', 'wdk-svg-map' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'Hide', 'wdk-svg-map' ),
                            'block' => esc_html__( 'Show', 'wdk-svg-map' ),
                            'return_value' =>  'none',
                            'default' => ($item['key'] == 'field_button_reset' ) ? 'none':'',
                            'selectors' => [
                                $item['selector_hide'] => 'display: {{VALUE}};',
                            ],
                        ]
                    );
                }
            }

            if($item['key'] !='field_slider_range'){
                $selectors = array();

                if(!empty($item['selector']))
                    $selectors['normal'] = $item['selector'];
    
                if(!empty($item['selector_hover']))
                    $selectors['hover'] = $item['selector_hover'];
    
                if(!empty($item['selector_focus']))
                    $selectors['focus'] = $item['selector_hover'];
                    
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);
            }
    
            if($item['key'] =='field_text'){
                $this->add_control(
                    'field_text_pl_header',
                    [
                        'label' => __( 'Placeholder', 'wpdirectorykit' ),
                        'type' => Controls_Manager::HEADING,
                    ]
                );

                $selectors = array(
                    'normal' => '#wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="text"]::placeholder, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field input[type="number"]::placeholder',
                );
             
                $this->generate_renders_tabs($selectors, 'field_text_pl_dynamic', ['align','typo','color']);
            }

            if($item['key'] =='field_slider_range') {
                    
                $this->add_responsive_control(
                    'field_slider_range_color_circle',
                    [
                            'label' => esc_html__( 'Circle Color', 'wpdirectorykit' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '#wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-handle' => 'border-color: {{VALUE}};',
                            ],
                    ]
                );
                    
                $this->add_responsive_control(
                    'field_slider_range_color_line',
                    [
                            'label' => esc_html__( 'Line Color', 'wpdirectorykit' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '#wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-bar' => 'background-color: {{VALUE}};',
                            ],
                    ]
                );
                    
                $this->add_responsive_control(
                    'field_slider_range_color_label',
                    [
                            'label' => esc_html__( 'Label Color', 'wpdirectorykit' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '#wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-from, #wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-to, #wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-single' => 'background-color: {{VALUE}};',
                                    '#wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-from::before, #wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-to::before, #wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-single::before' => 'border-top-color: {{VALUE}};',
                            ],
                    ]
                );
                    
                $this->add_responsive_control(
                    'field_slider_range_color_text_label',
                    [
                            'label' => esc_html__( 'Label Text Color', 'wpdirectorykit' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '#wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-from, #wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-to, #wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-single' => 'color: {{VALUE}};',
                            ],
                    ]
                );

                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                            'name' => 'field_slider_range_color_typo',
                            'selector' =>  '#wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-from, #wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-to, #wdk_search_popup_modal_'.$this->get_id().' .irs--round .irs-single',
                    ]
                );

                $this->add_responsive_control(
                    'field_slider_range_color_text_line',
                    [
                            'label' => esc_html__( 'Line Text Color', 'wpdirectorykit' ),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                    '#wdk_search_popup_modal_'.$this->get_id().' .wdk-slider-range-field .irs--round .irs-grid-text' => 'color: {{VALUE}};',
                            ],
                    ]
                );

            }

            if($item['key'] =='field_tree') {
                    
                $this->add_control(
                    'styles_field_tree_pl_hr',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
    
                $this->add_control(
                    'styles_field_tree_pl_header',
                    [
                        'label' => __( 'Placeholder', 'wpdirectorykit' ),
                        'type' => Controls_Manager::HEADING,
                    ]
                );

                      
                $this->add_control(
                    'styles_field_tree_pl_hr2',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );

                $selectors = array(
                    'normal' => '#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree .btn-group:not(.sel_class) button:first-child, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY .select2 .select2-search__field::placeholder,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION .select2 .select2-search__field::placeholder',
                    'hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree .btn-group:not(.sel_class) button:first-child%1$s,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY .select2 .select2-search__field%1$s::placeholder,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION .select2 .select2-search__field%1$s::placeholder',
                );
             
                $this->generate_renders_tabs($selectors, 'styles_field_tree_pl_list_dynamic', ['align','typo','color']);
                    
                $this->add_control(
                    'styles_field_text_tree_pl_hr',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
    
                $this->add_control(
                    'styles_field_text_tree_pl_header',
                    [
                        'label' => __( 'Text field', 'wpdirectorykit' ),
                        'type' => Controls_Manager::HEADING,
                    ]
                );

                      
                $this->add_control(
                    'styles_field_text_tree_pl_hr2',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );

                $selectors = array(
                    'normal' => '#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree .btn-group button, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY .select2 .select2-search__field,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION .select2 .select2-search__field',
                    'hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree .btn-group%1$s button,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY .select2 .select2-search__field%1$s,#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION .select2 .select2-search__field%1$s',
                );
             
                $this->generate_renders_tabs($selectors, 'styles_field_text_tree_pl_list_dynamic', ['typo','color']);


                $this->add_control(
                    'styles_field_tree_hr',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
    
                $this->add_control(
                    'styles_field_tree_header',
                    [
                        'label' => __( 'List Items', 'wpdirectorykit' ),
                        'type' => Controls_Manager::HEADING,
                    ]
                );

                      
                $this->add_control(
                    'styles_field_tree_hr2',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
    
                $selectors = array(
                    'normal' => '#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree .list_scroll ul li, .select_multi_dropdown_tree .select2-dropdown .select2-results__options .select2-results__option',
                    'hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk_dropdown_tree .list_scroll ul li, .select_multi_dropdown_tree .select2-dropdown .select2-results__options .select2-results__option%1$s',
                );
             
                $this->generate_renders_tabs($selectors, 'styles_field_tree_list_dynamic', ['margin','align','typo','color','background','border','padding','transition']);

                if(wdk_get_option('wdk_multi_categories_search_field_type')=='select2' || wdk_get_option('wdk_multi_locations_search_field_type')=='select2' ) {
                    $this->add_control(
                        'styles_field_tree_items_hr',
                        [
                                'type' => \Elementor\Controls_Manager::DIVIDER,
                        ]
                    );
        
                    $this->add_control(
                        'styles_field_tree_items_header',
                        [
                            'label' => __( 'Multi Items for Multiple Dropdowns', 'wpdirectorykit' ),
                            'type' => Controls_Manager::HEADING,
                        ]
                    );

                        
                    $this->add_control(
                        'styles_field_tree_items_hr2',
                        [
                                'type' => \Elementor\Controls_Manager::DIVIDER,
                        ]
                    );
                
                    $selectors = array(
                        'normal' => '#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY .select2 .select2-selection__choice, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION .select2 .select2-selection__choice',
                        'hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.CATEGORY .select2 .select2-selection__choice%1$s, #wdk_search_popup_modal_'.$this->get_id().' .wdk-field.LOCATION .select2 .select2-selection__choice%1$s',
                    );
                
                    $this->generate_renders_tabs($selectors, 'styles_field_tree_list_items_dynamic', ['margin','typo','color','background','border','border_radius','padding','transition']);
                }
            }

            if($item['key'] =='field_select2') {
                    
                $this->add_control(
                    'styles_field_select2_pl_hr',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
    
                $this->add_control(
                    'styles_field_select2_pl_header',
                    [
                        'label' => __( 'Placeholder', 'wpdirectorykit' ),
                        'type' => Controls_Manager::HEADING,
                    ]
                );

                      
                $this->add_control(
                    'styles_field_select2_pl_hr2',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );

                $selectors = array(
                    'normal' => '#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWNMULTIPLE .select2 .select2-search__field::placeholder',
                    'hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWNMULTIPLE .select2 .select2-search__field%1$s::placeholder',
                );
             
                $this->generate_renders_tabs($selectors, 'styles_field_select2_pl_list_dynamic', ['align','typo','color']);

                $this->add_control(
                    'styles_field_select2_hr',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
    
                $this->add_control(
                    'styles_field_select2_header',
                    [
                        'label' => __( 'List Items', 'wpdirectorykit' ),
                        'type' => Controls_Manager::HEADING,
                    ]
                );

                      
                $this->add_control(
                    'styles_field_select2_hr2',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
    

                $selectors = array(
                    'normal' => '.select_multi_dropdown .select2-dropdown .select2-results__options .select2-results__option',
                    'hover'=>'.select_multi_dropdown .select2-dropdown .select2-results__options .select2-results__option%1$s',
                );
             
                $this->generate_renders_tabs($selectors, 'styles_field_select2_list_dynamic', ['margin','align','typo','color','background','border','padding','transition']);

                $this->add_control(
                    'styles_field_select2_items_hr',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
    
                $this->add_control(
                    'styles_field_select2_items_header',
                    [
                        'label' => __( 'Multi Items', 'wpdirectorykit' ),
                        'type' => Controls_Manager::HEADING,
                    ]
                );

                    
                $this->add_control(
                    'styles_field_select2_items_hr2',
                    [
                            'type' => \Elementor\Controls_Manager::DIVIDER,
                    ]
                );
            
                $selectors = array(
                    'normal' => '#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWNMULTIPLE .select2 .select2-selection__choice',
                    'hover'=>'#wdk_search_popup_modal_'.$this->get_id().' .wdk-field.DROPDOWNMULTIPLE .select2 .select2-selection__choice%1$s',
                );
            
                $this->generate_renders_tabs($selectors, 'styles_field_select2_list_items_dynamic', ['margin','typo','color','background','border','border_radius','padding','transition']);

            }

            $this->end_controls_section();
            /* END special for some elements */

        }
    }

    private function generate_controls_content() {
        
    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-search-popup');
        wp_enqueue_style('slick');
        wp_enqueue_style('wdk-suggestion');
        wp_enqueue_style('slick-theme');
        wp_enqueue_script('slick');
        wp_enqueue_script('wdk-treefield');
        wp_enqueue_script('wdk-suggestion');
        wp_enqueue_style('wdk-treefield');

        wp_enqueue_script('wdk-modal');
        wp_enqueue_style('wdk-modal');

        wp_enqueue_script('select2');
        wp_enqueue_script('wdk-select2');
        wp_enqueue_style('select2');

        wp_enqueue_script( 'ion.range-slider' );
        wp_enqueue_style('ion.range-slider');
        wp_enqueue_style('wdk-slider-range');
        wp_enqueue_script('wdk-slider-range');

        wp_enqueue_style( 'wdk-treefield-checkboxes');
        wp_enqueue_script( 'wdk-treefield-checkboxes');
    }
}
