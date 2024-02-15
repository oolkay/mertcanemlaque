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
class WdkCategoriesGrid extends WdkElementorBase {

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
        return 'wdk-categories-grid';
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
        return esc_html__('Wdk Categories Grid', 'wpdirectorykit');
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
        return 'eicon-gallery-grid';
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

        $controller = 'category';
        $this->WMVC->model($controller.'_m');
        $this->WMVC->model('listing_m');
        
        $this->data['results'] = array();
        
        if($this->data['settings']['conf_results_type'] == 'custom_categories') {

            $categories_ids = array();
            foreach($this->data['settings']['conf_custom_results'] as $category) {
                if(isset($category['category_id']) && !empty($category['category_id'])) {
                    $categories_ids [] = $category['category_id'];
                }
            }
            
            /* where in */
            if(!empty($categories_ids)){

                $this->WMVC->db->select($this->WMVC->{$controller.'_m'}->_table_name.'.*, COUNT('.$this->WMVC->listing_m->_table_name.'.post_id) AS listings_counter');
                $this->WMVC->db->join($this->WMVC->listing_m->_table_name.' ON '.$this->WMVC->listing_m->_table_name.'.category_id = '.$this->WMVC->{$controller.'_m'}->_table_name.'.idcategory', TRUE, 'LEFT');
                $this->WMVC->db->where($this->WMVC->{$controller.'_m'}->_table_name.'.idcategory IN(' . implode(',', $categories_ids) . ')', null, false);
                $this->WMVC->db->order_by('FIELD('.$this->WMVC->{$controller.'_m'}->_table_name.'.idcategory, '. implode(',', $categories_ids) . ')');
                $this->WMVC->db->group_by($this->WMVC->{$controller.'_m'}->_primary_key);
               
                $this->data['results'] = $this->WMVC->{$controller.'_m'}->get();
            }

        } else {
            $order_by = NULL;
            if(!empty($this->data['settings']['conf_order_by']))
                $order_by = $this->data['settings']['conf_order_by'].' '.$this->data['settings']['conf_order'];

            $where = array();
            if (!empty($this->data['settings']['only_root_enable']) && $this->data['settings']['only_root_enable'] == 'yes') {
                $where['('.$this->WMVC->{$controller.'_m'}->_table_name.'.level = 0)'] = NULL;
            }
            $this->data['results'] = $this->WMVC->{$controller.'_m'}->get_pagination((!empty($this->data['settings']['conf_limit'])) ? $this->data['settings']['conf_limit'] : NULL, NULL,  $where, $order_by);
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode']= true;
      
        echo $this->view('wdk-categories-grid', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
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

        $this->add_control(
            'conf_results_type',
            [
                'label' => __( 'Results type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'results_categories',
                'options' => [
                    'results_categories'  => __( 'All Categories', 'wpdirectorykit' ),
                    'custom_categories' => __( 'Specific', 'wpdirectorykit' ),
                ],
                'separator' => 'after',
            ]
        );

        
        $this->add_control(
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Manage Categories <a href="%1$s" target="_blank"> open </a>', 'wpdirectorykit' ), admin_url('admin.php?page=wdk_category')),
                'content_classes' => 'wdk_elementor_hint',
            ]
        );

        $this->add_responsive_control(
            'only_root_enable',
                [
                    'label' => esc_html__( 'Show only Root', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'No', 'wpdirectorykit' ),
                    'block' => esc_html__( 'Yes', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
        );
        $this->add_control(
            'conf_limit',
            [
                'label' => __( 'Limit Categories', 'wpdirectorykit' ),
                'description' => __( 'Set 0 for unlimit', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => 6,
                'separator' => 'before',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );

        $this->add_control(
            'conf_order_by',
            [
                'label'         => __('Order By Column', 'wpdirectorykit'),
                'type'          => Controls_Manager::SELECT,
                'label_block'   => true,
                'options'       => [
                    ''  => __('None', 'wpdirectorykit'),
                    'category_title' => __('Title', 'wpdirectorykit'),
                    'idcategory' => __('Category id', 'wpdirectorykit'),
                    'order_index' => __('Order index', 'wpdirectorykit'),
                    'listings_counter' => __('Most Listings', 'wpdirectorykit'),
                ],
                'default' => 'order_index',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );

        $this->add_control(
            'conf_order',
            [
                'label'         => __('Order', 'wpdirectorykit'),
                'type'          => Controls_Manager::SELECT,
                'label_block'   => true,
                'options'       => [
                    'asc'           => __('Ascending', 'wpdirectorykit'),
                    'desc'          => __('Descending', 'wpdirectorykit')
                ],
                'default'       => 'desc',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );

        if(true){
            $repeater = new Repeater();
            $repeater->start_controls_tabs( 'categories' );
            $repeater->add_control(
                'category_id',
                [
                    'label' => __( 'ID Category', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'step' => 1,
                ]
            );
            $repeater->end_controls_tabs();

                            
            $this->add_control(
                'conf_custom_results',
                [
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                    ],
                    'title_field' => '{{{ category_id }}}',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_results_type',
                                'operator' => '==',
                                'value' => 'custom_categories',
                            ]
                        ],
                    ],
                ]
            );
        }

        $this->end_controls_section();
    }

    private function generate_controls_layout() {

          /* START Section t_options_slider */
            $this->start_controls_section(
                'tab_options_slider',
                [
                    'label' => esc_html__('Options', 'wpdirectorykit'),
                    'tab' => 'tab_options',
                ]
            );

            
            $this->add_control(
                'layout_carousel_enable',
                [
                    'label' => __( 'Carousel Enable', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                ]
            );


            $this->add_responsive_control(
                'layout_carousel_column_gap',
                [
                    'label' => esc_html__('Columns Gap', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 15,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .slick-slide' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .wdk-categories-grid .wdk-carousel_ini' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'layout_carousel_center',
                [
                    'label' => __( 'Center', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'layout_carousel_variableWidth',
                [
                    'label' => __( 'variableWidth', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'layout_carousel_is_infinite',
                [
                    'label' => __( 'Infinite', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'true',
                    'default' => 'true',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'layout_carousel_is_autoplay',
                [
                    'label' => __( 'Autoplay', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'true',
                    'default' => '',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'layout_carousel_columns',
                [
                    'label' => __( 'Count grid', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'default' => 5,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'layout_carousel_speed',
                [
                    'label' => __( 'Speed', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 100000,
                    'step' => 100,
                    'default' => 500,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );
            $this->add_control(
                'layout_carousel_autoplaySpeed',
                [
                    'label' => __( 'Speed', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 100000,
                    'step' => 100,
                    'default' => 500,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'layout_carousel_animation_style',
                [
                    'label' => __( 'Animation Style', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'fade',
                    'options' => [
                        'slide'  => __( 'Slide', 'wpdirectorykit' ),
                        'fade' => __( 'Fade', 'wpdirectorykit' ),
                        'fade_in_in' => __( 'Fade in', 'wpdirectorykit' ),
                    ],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'layout_carousel_cssease',
                [
                    'label' => __( 'cssEase', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'linear',
                    'options' => [
                        'linear'  => __( 'linear', 'wpdirectorykit' ),
                        'ease' => __( 'ease', 'wpdirectorykit' ),
                        'ease-in' => __( 'ease-in', 'wpdirectorykit' ),
                        'ease-out' => __( 'ease-out', 'wpdirectorykit' ),
                        'ease-in-out' => __( 'ease-in-out', 'wpdirectorykit' ),
                        'step-start' => __( 'step-start', 'wpdirectorykit' ),
                        'step-end' => __( 'step-end', 'wpdirectorykit' ),
                    ],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_carousel_enable',
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
                        'default' => '25%', 
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
            'layout_image_type',
            [
                'label' => __( 'Thumbnail Type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'icon',
                'options' => [
                    'icon' => __( 'Icon', 'wpdirectorykit' ),
                    'image' => __( 'Image', 'wpdirectorykit' ),
                ],
            ]
        );

        $this->add_control(
            'layout_image_design',
            [
                'label' => __( 'Size style thumbnail', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __( 'None', 'wpdirectorykit' ),
                    'contain' => __( 'Contain', 'wpdirectorykit' ),
                    'cover' => __( 'Cover', 'wpdirectorykit' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-categories-card .wdk-thumbnail .wdk-image' => 'object-fit: {{VALUE}}',
                    '{{WRAPPER}} .wdk-categories-card .wdk-thumbnail .wdk-icon' => 'object-fit: {{VALUE}}',
                ],
            ]
        );

        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-categories-card .wdk-thumbnail',
        );
        $this->generate_renders_tabs($selectors, 'layout_image_dynamic', ['padding' ,'background','color']);

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
                'default' => [
                    'size' => 350,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-categories-card .wdk-thumbnail .wdk-image' => 'height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .wdk-categories-card .wdk-thumbnail .wdk-icon' => 'height: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'after',
            ]
        );

        $this->end_controls_section();

        $items = [
            [
                'key'=>'item_title',
                'label'=> esc_html__('Item Title', 'wpdirectorykit'),
                'selector'=>'.wdk-categories-grid .wdk-categories-card .wdk-title',
                'selector_hover'=>'.wdk-categories-grid .wdk-categories-card%1$s .wdk-title',
                'options'=>'full',
            ],
            [
                'key'=>'card',
                'label'=> esc_html__('Card', 'wpdirectorykit'),
                'selector'=>'.wdk-categories-grid .wdk-categories-card',
                'selector_hover'=>'.wdk-categories-grid .wdk-categories-card%1$s',
                'options'=>'block',
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
                'hover'=>'{{WRAPPER}} '.$item['selector_hover'],
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            $this->end_controls_section();
        }
    }

    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
              
        wp_enqueue_style('wdk-categories-grid');
        wp_enqueue_style('slick');
        wp_enqueue_style('slick-theme');
        wp_enqueue_script('slick');
    }
}
