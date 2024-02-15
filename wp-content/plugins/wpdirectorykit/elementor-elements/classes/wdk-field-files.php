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
class WdkFieldFiles extends WdkElementorBase {

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
        return 'wdk-field-files';
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
        return esc_html__('Wdk Listing Plans And Documents', 'wpdirectorykit');
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
        return 'eicon-document-file';
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
        /* test data */ 
        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        $this->data['images'] = array();

        if(!Plugin::$instance->editor->is_edit_mode()) {
            $this->data['images'] = wdk_listing_images_data (wdk_field_value('listing_plans_documents', $wdk_listing_id), 'full', '', $this->data['settings']['extension_list']);
            $this->data['images'] = array_slice($this->data['images'], ($this->data['settings']['offset_images']-1), $this->data['settings']['limit_images']);
        } else {
            $this->data['images'] = array_fill(0, $this->data['settings']['limit_images'], wdk_placeholder_image_src());
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()) {
            $this->data['is_edit_mode']= true;
        } else {
            /* return false if no content */
        }
      
        echo $this->view('wdk-field-files', $this->data); 
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
			'extension_list',
			[
				'label' => __( 'Show Only Files By Extension', 'wpdirectorykit' ),
				'description' => __( 'Put file`s extension, if you want show only these files, separate like bmp,xml,pdf,jpg. Responsive only on live version', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
			]
		);
        
        $this->add_control(
			'limit_images',
			[
				'label' => __( 'Limit Images', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 50,
			]
		);

        $this->add_control(
			'offset_images',
			[
				'label' => __( 'Offset Images', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 1,
			]
		);

        $this->add_control(
			'enable_js_gallery',
			[
				'label' => __( 'Enable Gallery Popup', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->end_controls_section();

    }

    private function generate_controls_layout() {
    }

    private function generate_controls_styles() {
        $this->start_controls_section(
            'styles_thmbn_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => Controls_Manager::TAB_STYLE,
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
                        'size' => 10,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-row .wdk-col' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};;',
                        '{{WRAPPER}} .wdk-row' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_responsive_control(
                'row_gap',
                [
                    'label' => esc_html__('Rows Gap', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-row  .wdk-col' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .wdk-row' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'styles_thmbn_type',
            [
                'label' => esc_html__('Thumbnail', 'wpdirectorykit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'layout_image_design',
            [
                'label' => __( 'Size style thumbnail', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'None', 'wpdirectorykit' ),
                    'contain' => __( 'Contain', 'wpdirectorykit' ),
                    'cover' => __( 'Cover', 'wpdirectorykit' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-field-files .wdk-listing-image' => 'object-fit: {{VALUE}}',
                ],
            ]
        );

        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-field-files .wdk-listing-image',
        );
        $this->generate_renders_tabs($selectors, 'layout_image_dynamic', ['margin','padding','background','border','border_radius','css_filters','shadow','transition']);

        $this->add_control(
			'enable_fixed_height',
			[
				'label' => __( 'Fixed Height', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->add_responsive_control (
            'styles_thmbn_des_height',
            [
                'label' => esc_html__('Height', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1500,
                    ],   
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-field-files .wdk-listing-image' => 'height: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'after',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'enable_fixed_height',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        $items = [
            [
                'key'=>'card',
                'label'=> esc_html__('Card', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-field-files figcaption',
                'selector_hover'=>'{{WRAPPER}} .wdk-field-files figcaption%1$s',
                'options'=>['margin','padding','background','border','border_radius','css_filters','shadow','transition'],
            ],
            [
                'key'=>'image',
                'label'=> esc_html__('Image', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-field-files .wdk-listing-image-card .wdk-listing-image',
                'selector_hover'=>'{{WRAPPER}} .wdk-field-files .wdk-listing-image-card%1$s .wdk-listing-image',
                'options'=> ['margin','background','border','border_radius','padding','shadow','transition','css_filters'],
            ],
            [
                'key'=>'caption',
                'label'=> esc_html__('Caption', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-field-files figure figcaption',
                'selector_hover'=>'{{WRAPPER}} .wdk-field-files figure%1$s figcaption',
                'options'=> ['margin','align','typo','color','background','border','border_radius','padding','shadow','transition'],
            ],
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' =>  Controls_Manager::TAB_STYLE,
                ]
            );

            if($item['key'] == 'caption')
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
                                $item['selector'] => 'display: {{VALUE}};',
                            ],
                    ]
            );
       
            $selectors = array(
                'normal' => $item['selector'],
                'hover'=>$item['selector_hover'],
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            $this->end_controls_section();
        }


        /* files */
        if(true) {
                
            $this->start_controls_section(
                'files_section',
                [
                    'label' => esc_html__('Files List', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                'files_section_hide',
                    [
                        'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                        'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                        'return_value' => 'yes',
                        'default' => '',
                    ]
            );

            $this->add_control(
                'files_section_box_header',
                [
                    'label' => __('Container', 'wpdirectorykit'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                ]
            );
    
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-field-files .files-row ul.files',
            );
    
            $this->generate_renders_tabs($selectors, 'files_section_bo_dynamic', ['background','border','border_radius','padding','margin','shadow','transition' ]);
    
            $this->add_control(
                'files_section_li',
                [
                    'label' => __('List Items', 'wpdirectorykit'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                ]
            );
    
            $this->add_control(
                'files_section_li_hr',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $this->add_control(
                'files_section_space',
                [
                    'label' => __('Vertical Space', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 250,
                            'step' => 1,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-field-files .files-row ul.files li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
    
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-field-files .files-row ul.files li',
            );
    
            $this->generate_renders_tabs($selectors, 'files_section_item_dynamic', ['align','background','border','border_radius','padding','shadow','transition' ]);
    
            $this->add_control(
                'files_section_hr',
                [
                        'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );
            
            $this->add_control(
                'files_section_items',
                [
                    'label' => __('More Styles', 'wpdirectorykit'),
                    'type' => \Elementor\Controls_Manager::HEADING,
                ]
            );

            $items = [
                [
                    'key'=>'files_icon',
                    'label'=> esc_html__('Icon', 'wpdirectorykit'),
                    'selector'=>'{{WRAPPER}} .wdk-field-files .files-row .list-item a .wdk-listing-file-icon',
                    'selector_hover'=>'{{WRAPPER}} .wdk-field-files .files-row .list-item%1$s a .wdk-listing-file-icon',
                    'options'=>  ['margin','background','border','border_radius','padding','shadow','transition','image_size_control','css_filters','hover_animation'],
                ],
                [
                    'key'=>'files_caption',
                    'label'=> esc_html__('Caption', 'wpdirectorykit'),
                    'selector'=>'{{WRAPPER}} .wdk-field-files .files-row .list-item a',
                    'selector_hover'=>'{{WRAPPER}} .wdk-field-files .files-row .list-item a%1$s',
                    'options'=> ['margin','align','typo','color','background','border','border_radius','padding','shadow','transition'],
                ],
            ];

            foreach ($items as $item) {
                $this->add_control(
                    $item['key'].'_header',
                    [
                        'label' => $item['label'],
                        'type' => \Elementor\Controls_Manager::HEADING,
                    ]
                );

                if($item['key'] == 'files_icon')
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
                                    $item['selector'] => 'display: {{VALUE}};',
                                ],
                        ]
                );
        
                $selectors = array(
                    'normal' => $item['selector'],
                    'hover'=>$item['selector_hover'],
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);      
            }

            $this->end_controls_section();
        }
    }

    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-field-files');
        wp_enqueue_style('blueimp-gallery');
        wp_enqueue_script('blueimp-gallery');
        wp_enqueue_script('wdk-blueimp-gallery');
    }
}
