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
class WdkLocationsCarousel extends WdkElementorBase {

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
        return 'wdk-locations-carousel';
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
        return esc_html__('Wdk Locations Carousel', 'wpdirectorykit');
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
        return 'eicon-banner';
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

        $controller = 'location';
        $this->WMVC->model($controller.'_m');
        $this->WMVC->model('listing_m');
        $this->data['results'] = array();
        
        if($this->data['settings']['conf_results_type'] == 'custom_locations') {

            $locations_ids = array();
            foreach($this->data['settings']['conf_custom_results'] as $location) {
                if(isset($location['location_id']) && !empty($location['location_id'])) {
                    $locations_ids [] = $location['location_id'];
                }
            }
            
            /* where in */
            if(!empty($locations_ids)){

                $this->WMVC->db->select($this->WMVC->{$controller.'_m'}->_table_name.'.*, COUNT('.$this->WMVC->listing_m->_table_name.'.post_id) AS listings_counter');
                $this->WMVC->db->join($this->WMVC->listing_m->_table_name.' ON '.$this->WMVC->listing_m->_table_name.'.location_id = '.$this->WMVC->{$controller.'_m'}->_table_name.'.idlocation', TRUE, 'LEFT');
                $this->WMVC->db->where($this->WMVC->{$controller.'_m'}->_table_name.'.idlocation IN(' . implode(',', $locations_ids) . ')', null, false);
                $this->WMVC->db->order_by('FIELD('.$this->WMVC->{$controller.'_m'}->_table_name.'.idlocation, '. implode(',', $locations_ids) . ')');
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
            $this->data['results'] = $this->WMVC->{$controller.'_m'}->get_pagination((!empty($this->data['settings']['conf_limit'])) ? $this->data['settings']['conf_limit'] : NULL, NULL, $where, $order_by);
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode']= true;
    
        echo $this->view('wdk-locations-carousel', $this->data); 
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
                'label' => __( 'Show type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'results_locations',
                'options' => [
                    'results_locations'  => __( 'All Locations', 'wpdirectorykit' ),
                    'custom_locations' => __( 'Specific', 'wpdirectorykit' ),
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Manage Locations <a href="%1$s" target="_blank"> open </a>', 'wpdirectorykit' ), admin_url('admin.php?page=wdk_location')),
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
                'label' => __( 'Limit Locations', 'wpdirectorykit' ),
                'description' => __( 'Set 0 for unlimit', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => 6,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'conf_results_type',
                            'operator' => '==',
                            'value' => 'results_locations',
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
                    'location_title' => __('Title', 'wpdirectorykit'),
                    'idlocation' => __('Category id', 'wpdirectorykit'),
                    'order_index' => __('Order index', 'wpdirectorykit'),
                    'listings_counter' => __('Most Listings', 'wpdirectorykit'),
                ],
                'default' => 'order_index',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'conf_results_type',
                            'operator' => '==',
                            'value' => 'results_locations',
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
                'default'       => 'asc',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'conf_results_type',
                            'operator' => '==',
                            'value' => 'results_locations',
                        ]
                    ],
                ],
            ]
        );


        $this->add_control(
            'complete_link_enable',
            [
                'label' => __( 'Complete Link', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        if(true){
            $repeater = new Repeater();
            $repeater->start_controls_tabs( 'locations' );
            $repeater->add_control(
                'location_id',
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
                    'title_field' => '{{{ location_id }}}',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_results_type',
                                'operator' => '==',
                                'value' => 'custom_locations',
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
          if(true){
            $this->start_controls_section(
                'tab_options_slider',
                [
                    'label' => esc_html__('Slider options', 'wpdirectorykit'),
                    'tab' => 'tab_options',
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
                        '{{WRAPPER}} .slick-slide' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};;',
                    ],
                ]
            );

            $this->add_responsive_control(
                'custom_width',
                [
                    'label' => esc_html__('Columns Custom Width', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1200,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .slick-slide .wdk-slider-item' => 'width: {{SIZE}}{{UNIT}} !important;',
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
                    'default' => 1,
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
                ]
            );

            $this->end_controls_section();
        }

        /* START Section t_options_slider */
        if(true){
            $this->start_controls_section(
                'tab_styles_image',
                [
                    'label' => esc_html__('Section Image', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                't_styles_img_des_type',
                [
                    'label' => __( 'Design type', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk-image_cover',
                    'options' => [
                        ''  => __( 'Default Sizes', 'wpdirectorykit' ),
                        'wdk-image_size_cover' => __( 'Image auto crop/resize', 'wpdirectorykit' ),
                        'wdk-image_cover' => __( 'Image cover (like background)', 'wpdirectorykit' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                't_styles_img_des_height',
                [
                    'label' => esc_html__('Height', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 300,
                            'max' => 1500,
                        ],
                    ],
                    'render_type' => 'template',
                    'default' => [
                        'size' => 350,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini.wdk-image_cover .slick-list,
                         {{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini.wdk-image_size_cover .wdk-slider-item .wdk-slider-item_thumbnail' => 'height: {{SIZE}}px',
                    ],
                    'separator' => 'after',
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'name' => 't_styles_img_des_type',
                                'operator' => '==',
                                'value' => 'wdk-image_size_cover',
                            ],
                            [
                                'name' => 't_styles_img_des_type',
                                'operator' => '==',
                                'value' => 'wdk-image_cover',
                            ]
                        ],
                    ]
                ]
            );
            $this->end_controls_section();

            $this->start_controls_section(
                'tab_styles_arrows_section',
                [
                    'label' => esc_html__('Section Arrows', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                't_styles_arrows_hide',
                [
                        'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                        'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                        'return_value' => 'none',
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_arrows' => 'display: {{VALUE}};',
                        ],
                ]
        );

        
            $this->add_responsive_control(
                't_styles_arrows_position',
                [
                    'label' => __( 'Position', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk-locations-carousel_arrows_bottom',
                    'options' => [
                        'wdk-locations-carousel_arrows_bottom'  => __( 'Bottom', 'wpdirectorykit' ),
                        'wdk-locations-carousel_arrows_middle' => __( 'Center', 'wpdirectorykit' ),
                        'wdk-locations-carousel_arrows_top' => __( 'Top', 'wpdirectorykit' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                't_styles_arrows_position_style',
                [
                    'label' => __( 'Position Style', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk-locations-carousel_arrows_out',
                    'options' => [
                        'wdk-locations-carousel_arrows_out' => __( 'Out', 'wpdirectorykit' ),
                        'wdk-locations-carousel_arrows_in' => __( 'In', 'wpdirectorykit' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                't_styles_arrows_align',
                [
                    'label' => __( 'Align', 'wpdirectorykit' ),
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
                        '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_arrows' => '{{VALUE}};',
                    ],
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'name' => 't_styles_img_des_type',
                                'operator' => '==',
                                'value' => 'wdk-image_size_cover',
                            ],
                            [
                                'name' => 't_styles_img_des_type',
                                'operator' => '==',
                                'value' => 'wdk-image_cover',
                            ]
                        ],
                    ],
                ]
            );
            
            $this->add_responsive_control(
                'styles_carousel_arrows_icon_left_h',
                [
                    'label' => esc_html__('Arrow left', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_arrows .wdk-locations-carousel_arrow.wdk-slider-prev',
            );
            $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_icon_left', ['margin']);

            $this->add_responsive_control(
                'styles_carousel_arrows_icon_left',
                [
                    'label' => esc_html__('Icon', 'wpdirectorykit'),
                    'type' => Controls_Manager::ICONS,
                    'label_block' => true,
                    'default' => [
                        'value' => 'fa fa-angle-left',
                        'library' => 'solid',
                    ],
                ]
            );
                                
            $this->add_responsive_control(
                'styles_carousel_arrows_icon_right_h',
                [
                    'label' => esc_html__('Arrow right', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_arrows .wdk-locations-carousel_arrow.wdk-slider-next',
            );
            $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_icon_right', ['margin']);

            $this->add_responsive_control(
                'styles_carousel_arrows_icon_right',
                [
                    'label' => esc_html__('Icon', 'wpdirectorykit'),
                    'type' => Controls_Manager::ICONS,
                    'label_block' => true,
                    'default' => [
                        'value' => 'fa fa-angle-right',
                        'library' => 'solid',
                    ],
                ]
            );
            
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_arrows .wdk-locations-carousel_arrow',
                'hover'=>'{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_arrows .wdk-locations-carousel_arrow%1$s'
            );
            $this->generate_renders_tabs($selectors, 't_styles_arrows_s', ['typo','color','background','border','border_radius','padding','shadow','transition']);

            $this->end_controls_section();

            $this->start_controls_section(
                'tab_styles_dots_section',
                [
                    'label' => esc_html__('Section Dots', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                    't_styles_dots_hide',
                    [
                            'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                            'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                            'return_value' => 'none',
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .wdk-locations-carousel .slick-dots' => 'display: {{VALUE}} !important;',
                            ],
                    ]
            );

            $this->add_responsive_control(
                't_styles_dots_position_style',
                [
                    'label' => __( 'Position Style', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk-locations-carousel_dots_out',
                    'options' => [
                        'wdk-locations-carousel_dots_out' => __( 'Out', 'wpdirectorykit' ),
                        'wdk-locations-carousel_dots_in' => __( 'In', 'wpdirectorykit' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                't_styles_dots_align',
                [
                    'label' => __( 'Position', 'wpdirectorykit' ),
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
                        '{{WRAPPER}} .wdk-locations-carousel .slick-dots' => '{{VALUE}};',
                    ],
                ]
            );
            
            $this->add_responsive_control(
                'styles_carousel_dots_position_style',
                [
                    'label' => esc_html__('Icon', 'wpdirectorykit'),
                    'type' => Controls_Manager::ICONS,
                    'label_block' => true,
                    'default' => [
                        'value' => 'fas fa-circle',
                        'library' => 'solid',
                    ],
                ]
            );

            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-locations-carousel .slick-dots li .wdk-dot',
                'hover'=>'{{WRAPPER}} .wdk-locations-carousel .slick-dots li .wdk-dot%1$s'
            );
            $this->generate_renders_tabs($selectors, 't_styles_dots__s', 'full', ['align']);

            $this->end_controls_section();

        }

    }

    private function generate_controls_styles() {
       
    }

    private function generate_controls_content() {
   
        if(true){
            $this->start_controls_section(
                'tab_content',
                [
                    'label' => esc_html__('Basic', 'wpdirectorykit'),
                    'tab' => 'tab_content',
                ]
            );

            $this->add_responsive_control(
                't_content_basic_position_y',
                [
                    'label' => __( 'Position Y', 'wpdirectorykit' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'top' => [
                                'title' => esc_html__( 'Top', 'wpdirectorykit' ),
                                'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                                'title' => esc_html__( 'Center', 'wpdirectorykit' ),
                                'icon' => 'eicon-text-align-center',
                        ],
                        'bottom' => [
                                'title' => esc_html__( 'Bottom', 'wpdirectorykit' ),
                                'icon' => 'eicon-text-align-right',
                        ],
                        'justify' => [
                                'title' => esc_html__( 'Default', 'wpdirectorykit' ),
                                'icon' => 'eicon-text-align-justify',
                        ],
                    ],
                    'default' => 'left',
                    'render_type' => 'template',
                    'selectors_dictionary' => [
                        'top' => 'justify-content: flex-start;',
                        'center' => 'justify-content: center;',
                        'bottom' => 'justify-content: flex-end;',
                        'justify' => 'justify-content: space-between;',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item' => '{{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                't_content_basic_position_x',
                [
                    'label' => __( 'Position X', 'wpdirectorykit' ),
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
                    'default' => 'left',
                    'render_type' => 'template',
                    'selectors_dictionary' => [
                        'left' => 'align-items: flex-start;',
                        'center' => 'align-items: center;',
                        'right' => 'align-items: flex-end;',
                        'justify' => 'align-items: stretch;',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item' => '{{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                't_content_basic_link_text',
                [
                    'label' => __( 'Text of Link', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => __( 'View', 'wpdirectorykit' ),
                ]
            ); 

            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-locations-carousel_mask',
            );
            $this->generate_renders_tabs($selectors, 't_content_basic_s', ['background']);

            $this->end_controls_section();

            $this->start_controls_section(
                'tab_content_title',
                [
                    'label' => esc_html__('Title', 'wpdirectorykit'),
                    'tab' => 'tab_content',
                ]
            );
            $this->add_responsive_control(
                    'tab_content_title_hide',
                    [
                            'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                            'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                            'return_value' => 'none',
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_title' => 'display: {{VALUE}};',
                            ],
                    ]
            );
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_title',
                'hover'=>'{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_title%1$s'
            );
            $this->generate_renders_tabs($selectors, 'tab_content_title_s', 'full');
            $this->end_controls_section();

            $this->start_controls_section(
                'tab_content_content',
                [
                    'label' => esc_html__('Content', 'wpdirectorykit'),
                    'tab' => 'tab_content',
                ]
            );
            $this->add_responsive_control(
                    'tab_content_content_hide',
                    [
                            'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                            'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                            'return_value' => 'none',
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_content' => 'display: {{VALUE}};',
                            ],
                    ]
            );
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_content',
                'hover'=>'{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_content%1$s'
            );
            $this->generate_renders_tabs($selectors, 'tab_content_content_s', 'full');
            $this->end_controls_section();

            $this->start_controls_section(
                'tab_content_link',
                [
                    'label' => esc_html__('Link', 'wpdirectorykit'),
                    'tab' => 'tab_content',
                ]
            );
            $this->add_responsive_control(
                    'tab_content_link_hide',
                    [
                            'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                            'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                            'return_value' => 'none',
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_link' => 'display: {{VALUE}};',
                            ],
                    ]
            );
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_link',
                'hover'=>'{{WRAPPER}} .wdk-locations-carousel .wdk-locations-carousel_ini .wdk-slider-item_box_line .wdk-slider-item_box_link%1$s'
            );
            $this->generate_renders_tabs($selectors, 'tab_content_link_s', 'full');
            $this->end_controls_section();

        }
    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('slick');
        wp_enqueue_style('slick-theme');
        wp_enqueue_script('slick');
        wp_enqueue_style('wdk-locations-carousel');
    }

}
