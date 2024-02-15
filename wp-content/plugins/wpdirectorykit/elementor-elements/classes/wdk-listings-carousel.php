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
class WdkListingsCarousel extends WdkElementorBase {

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
        return 'wdk-listings-carousel';
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
        return esc_html__('Wdk Listings Carousel', 'wpdirectorykit');
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
        return 'eicon-post-navigation';
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
        
        $this->insert_pro_message('tab_conf');
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

        $this->data['listings_count'] = 0;
        $this->data['results'] = array();
        $this->data['pagination_output'] = '';
        $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by', 'is_featured', 'address');
        $custom_parameters = array();
        
        if($this->data['settings']['conf_results_type'] == 'results_listings') {
            $controller = 'listing';
            $offset = NULL;
                
            if(!isset($custom_parameters['order_by'])) {
                $custom_parameters['order_by'] = $this->data['settings']['conf_order_by'].' '.$this->data['settings']['conf_order'];
            }
                            
            if(!isset($custom_parameters['conf_order_by_custom'])) {
                $custom_parameters['order_by'] = $this->data['settings']['conf_order_by_custom'].' '.$this->data['settings']['conf_order'];
            }

            if(!empty($this->data['settings']['conf_query'])) {
                $qr_string = trim($this->data['settings']['conf_query'],'?');
                $string_par = array();
                parse_str($qr_string, $string_par);
                $custom_parameters += array_map('trim', $string_par);
            }

            if($this->data['settings']['only_is_featured'] == 'yes') {
                $custom_parameters['is_featured'] = 'on';
            }
            $external_columns = array('location_id', 'category_id', 'post_title', 'is_featured');

            wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters, TRUE);
            $this->data['results'] = $this->WMVC->listing_m->get_pagination($this->data['settings']['conf_limit'], $offset, array('is_activated' => 1,'is_approved'=>1));
            
        } else if($this->data['settings']['conf_results_type'] == 'custom_listings') {
            $listings_ids = array();
            foreach($this->data['settings']['conf_custom_results'] as $listing) {
                if(isset($listing['listing_post_id']) && !empty($listing['listing_post_id'])) {
                    $listings_ids [] = $listing['listing_post_id'];
                }
            }
            /* where in */
            if(!empty($listings_ids)){
                $this->WMVC->db->where( $this->WMVC->db->prefix.'wdk_listings.post_id IN(' . implode(',', $listings_ids) . ')', null, false);
                $this->WMVC->db->where(array('is_activated' => 1));
                $this->WMVC->db->order_by('FIELD('.$this->WMVC->db->prefix.'wdk_listings.post_id, '. implode(',', $listings_ids) . ')');
                $this->data['results'] = $this->WMVC->listing_m->get();
            }
        }

        if(!empty($this->data['results']) && $this->data['listings_count'] == 0)
            $this->data['listings_count'] = wmvc_count($this->data['results']);

        $this->data['settings']['content_button_icon'] = $this->generate_icon($this->data['settings']['content_button_icon']);
        $this->data['is_edit_mode'] = false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode'] = true;

        echo $this->view('wdk-listings-carousel', $this->data); 
      
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => 'tab_conf',
            ]
        );

        $this->add_control(
            'conf_results_type',
            [
                'label' => __( 'Carousel type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'results_listings',
                'options' => [
                    'results_listings'  => __( 'Results Listings', 'wpdirectorykit' ),
                    'custom_listings' => __( 'Specific Listings', 'wpdirectorykit' ),
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Edit Result Card Designer <a href="%1$s" target="_blank"> open </a>', 'wpdirectorykit' ), admin_url('admin.php?page=wdk_resultitem')),
                'content_classes' => 'wdk_elementor_hint',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'important_note2',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Manage Listings <a href="%1$s" target="_blank"> open </a>', 'wpdirectorykit' ), admin_url('admin.php?page=wdk')),
                'content_classes' => 'wdk_elementor_hint',
                'separator' => 'after',
            ]
        );

         /* conf_results_type :: results_listings */
         if(true){
            $this->add_control(
                    'conf_results_type_results_listings_header',
                    [
                        'label' => esc_html__('Results listings', 'wpdirectorykit'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'conf_results_type',
                                    'operator' => '==',
                                    'value' => 'results_listings',
                                ]
                            ],
                        ],
                    ]
            );

            $this->add_control(
                'conf_limit',
                [
                    'label' => __( 'Limit Results', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 250,
                    'step' => 1,
                    'default' => 6,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_results_type',
                                'operator' => '==',
                                'value' => 'results_listings',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'only_is_featured',
                [
                    'label' => __( 'Only show featured', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'True', 'wpdirectorykit' ),
                    'label_off' => __( 'False', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                ]
            );

            $this->add_control(
                'conf_query',
                [
                    'label' => __( 'Query', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXTAREA,
                    'rows' => 5,
                    'default' => '',
                    'placeholder' => __( 'Type your query here, example xxx', 'wpdirectorykit' ),
                    'description' => '<span style="word-break: break-all;">'.__( 'Example (same like on url):', 'wpdirectorykit' ).
                                    ' field_6_min=100&field_6_max=200&field_5=rent&is_featured=on&search_category=3&search_location=4&search_agents_ids=3'.
                                    '</span>',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_results_type',
                                'operator' => '==',
                                'value' => 'results_listings',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'conf_order_by',
                [
                    'label'         => __('Default Sort By Column', 'wpdirectorykit'),
                    'type'          => Controls_Manager::SELECT,
                    'label_block'   => true,
                    'options'       => [
                        'none'  => __('None', 'wpdirectorykit'),
                        'post_id'    => __('ID', 'wpdirectorykit'),
                        'post_title' => __('Title', 'wpdirectorykit'),
                    ],
                    'default' => 'post_id',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_results_type',
                                'operator' => '==',
                                'value' => 'results_listings',
                            ]
                        ],
                    ],
                ]
            );
                        
            $this->add_control(
                'conf_order_by_custom',
                [
                    'label'         => __('Default Custom Sort By (Column)', 'wpdirectorykit'),
                    'description' => '<span style="word-break: break-all;">'.__( 'Example:', 'wpdirectorykit' ).
                                        '<br> field_13_NUMBER  - where 13 is field id, NUMBER - field type'.
                                        '<br> field_4_NUMBER  - where 4 is field id, NUMBER - field type'.
                                        '<br> field_6_DROPDOWN  - where 6 is field id, DROPDOWN - field type'.
                                        '<br> category_title  - Category Title'.
                                        '<br> location_title  - Location Title'.
                                    '</span>',
                    'type'          => Controls_Manager::TEXT,
                    'label_block'   => true,
                    'default' => 'post_id',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_results_type',
                                'operator' => '==',
                                'value' => 'results_listings',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'conf_order',
                [
                    'label'         => __('Default Post Order', 'wpdirectorykit'),
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
                                'value' => 'results_listings',
                            ]
                        ],
                    ],
                ]
            );


        }

        if(true) {
            $this->add_control(
                'conf_results_type_custom_listings_header',
                [
                    'label' => esc_html__('Custom listings', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_results_type',
                                'operator' => '==',
                                'value' => 'custom_listings',
                            ]
                        ],
                    ],
                ]
            );

            
            if(true){
                $repeater = new Repeater();
                $repeater->start_controls_tabs( 'listings' );
                $repeater->add_control(
                    'listing_post_id',
                    [
                        'label' => __( 'ID Post Listing', 'wpdirectorykit' ),
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
                        'title_field' => '{{{ listing_post_id }}}',
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'conf_results_type',
                                    'operator' => '==',
                                    'value' => 'custom_listings',
                                ]
                            ],
                        ],
                    ]
                );

            }
        }
                    
        $this->end_controls_section();

    }

    private function generate_controls_layout() {
        $this->start_controls_section(
            'tab_content',
            [
                'label' => esc_html__('Basic', 'wpdirectorykit'),
                'tab' => 'tab_layout',
            ]
        );

        /* Carousel Grid Config */
        if(true) {
            $this->add_responsive_control(
                    'carousel_column_gap_carousel',
                    [
                        'label' => esc_html__('Slider Gap', 'wpdirectorykit'),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 60,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .slick-slider.wdk_results_listings_slider_ini ' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
                        ],
                    ]
            );

            $this->add_responsive_control (
                    'carousel_column_gap',
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
                            '{{WRAPPER}} .slick-slider.wdk_results_listings_slider_ini .wdk-col' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .slick-slider.wdk_results_listings_slider_ini' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
                        ],
                    ]
            );

            $this->add_responsive_control(
                    'carousel_column_gap_top',
                    [
                        'label' => esc_html__('Columns Gap Top', 'wpdirectorykit'),
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
                            '{{WRAPPER}} .wdk_results_listings_slider_box' => 'padding-top: {{SIZE}}{{UNIT}};',
                        ],
                    ]
            );

            $this->add_responsive_control(
                'carousel_column_gap_bottom',
                [
                    'label' => esc_html__('Columns Gap Bottom', 'wpdirectorykit'),
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
                        '{{WRAPPER}} .wdk_results_listings_slider_box' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
        }


        $this->add_control(
            'basic_el_header_1',
            [
                'label' => esc_html__('Text', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'content_button_text',
            [
                'label' => __( 'Button Open Text', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        ); 
        
        $this->end_controls_section();
        
        $this->start_controls_section(
            'layout_carousel_sec',
            [
                'label' => esc_html__('Carousel Options', 'wpdirectorykit'),
                'tab' => 'tab_layout',
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

        $this->add_responsive_control(
            'layout_carousel_columns',
            [
                'label' => __( 'Count grid', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 3,
            ]
        );

        $this->end_controls_section();
        
    }

    private function generate_controls_styles() {
            $this->start_controls_section(
                'sstyles_thmbn_section',
                [
                    'label' => esc_html__('Section Image', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                'styles_thmbn_des_type',
                [
                    'label' => __( 'Design type', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk_size_image_cover',
                    'options' => [
                        'wdk_size_image_ori'  => __( 'Default Sizes', 'wpdirectorykit' ),
                        'wdk_size_image_cover' => __( 'Image auto crop/resize', 'wpdirectorykit' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                'styles_thmbn_des_height',
                [
                    'label' => esc_html__('Height', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 300,
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
                        '{{WRAPPER}} .wdk-listings-results.wdk_size_image_cover .wdk-listing-card .wdk-thumbnail .wdk-image' => 'height: {{SIZE}}{{UNIT}}',
                    ],
                    'separator' => 'after',
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'name' => 'styles_thmbn_des_type',
                                'operator' => '==',
                                'value' => 'wdk_size_image_cover',
                            ],
                            [
                                'name' => 'styles_thmbn_des_type',
                                'operator' => '==',
                                'value' => 'wdk_image_cover',
                            ]
                        ],
                    ]
                ]
            );
            $this->end_controls_section();

            $this->start_controls_section(
                'styles_carousel_arrows_section',
                [
                    'label' => esc_html__('Carousel Arrows', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                'styles_carousel_arrows_hide',
                [
                        'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                        'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                        'return_value' => 'none',
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .wdk_results_listings_slider_box .wdk_slider_arrows' => 'display: {{VALUE}};',
                        ],
                ]
            );

            $this->add_responsive_control(
                'styles_carousel_arrows_position',
                [
                    'label' => __( 'Position', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk_slider_arrows_bottom',
                    'options' => [
                        'wdk_slider_arrows_bottom'  => __( 'Bottom', 'wpdirectorykit' ),
                        'wdk_slider_arrows_middle' => __( 'Center', 'wpdirectorykit' ),
                        'wdk_slider_arrows_top' => __( 'Top', 'wpdirectorykit' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                'styles_carousel_arrows_align',
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
                    'render_type' => 'ui',
                    'selectors_dictionary' => [
                        'left' => 'justify-content: flex-start;',
                        'center' => 'justify-content: center;',
                        'right' => 'justify-content: flex-end;',
                        'justify' => 'justify-content: space-between;',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk_results_listings_slider_box .wdk_slider_arrows' => '{{VALUE}};',
                    ],
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'name' => 'styles_thmbn_des_type',
                                'operator' => '==',
                                'value' => 'wdk_size_image_cover',
                            ],
                            [
                                'name' => 'styles_thmbn_des_type',
                                'operator' => '==',
                                'value' => 'wdk_image_cover',
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
            $this->add_responsive_control(
                'styles_carousel_arrows_s_m_left_margin',
                [
                        'label' => esc_html__( 'Margin', 'wpdirectorykit' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'allowed_dimensions' => 'horizontal',
                        'selectors' => [
                            '{{WRAPPER}} .wdk_results_listings_slider_box .wdk_slider_arrows .wdk_lr_slider_arrow.wdk-slider-prev' => 'margin-right:{{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};',
                        ],
                ]
            );

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

            $this->add_responsive_control(
                'styles_carousel_arrows_s_m_right_margin',
                [
                        'label' => esc_html__( 'Margin', 'wpdirectorykit' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', 'em', '%' ],
                        'allowed_dimensions' => 'horizontal',
                        'selectors' => [
                            '{{WRAPPER}} .wdk_results_listings_slider_box .wdk_slider_arrows .wdk_lr_slider_arrow.wdk-slider-next' => 'margin-right:{{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};',
                        ],
                ]
            );

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
                'normal' => '{{WRAPPER}} .wdk_results_listings_slider_box .wdk_slider_arrows .wdk_lr_slider_arrow',
                'hover'=>'{{WRAPPER}} .wdk_results_listings_slider_box .wdk_slider_arrows .wdk_lr_slider_arrow%1$s'
            );
            $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_dynamic', ['margin','color','background','border','border_radius','padding','shadow','transition','font-size','hover_animation']);

            $this->end_controls_section();

            $this->start_controls_section(
                'styles_carousel_dots_section',
                [
                    'label' => esc_html__('Section Dots', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                    'styles_carousel_dots_hide',
                    [
                            'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                            'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                            'return_value' => 'none',
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .wdk_results_listings_slider_box .slick-dots' => 'display: {{VALUE}} !important;',
                            ],
                    ]
            );

            $this->add_responsive_control(
                'styles_carousel_dots_position_style',
                [
                    'label' => __( 'Position Style', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk_slider_dots_out',
                    'options' => [
                        'wdk_slider_dots_out' => __( 'Out', 'wpdirectorykit' ),
                        'wdk_slider_dots_in' => __( 'In', 'wpdirectorykit' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                'styles_carousel_dots_align',
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
                    'render_type' => 'ui',
                    'selectors_dictionary' => [
                        'left' => 'justify-content: flex-start;',
                        'center' => 'justify-content: center;',
                        'right' => 'justify-content: flex-end;',
                        'justify' => 'justify-content: space-between;',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk_results_listings_slider_box .slick-dots' => '{{VALUE}};',
                    ],
                ]
            );
            
            $this->add_responsive_control(
                'styles_carousel_dots_icon',
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
                'normal' => '{{WRAPPER}} .wdk_results_listings_slider_box .slick-dots li .wdk_lr_dot',
                'hover'=>'{{WRAPPER}} .wdk_results_listings_slider_box .slick-dots li .wdk_lr_dot%1$s',
                'active'=>'{{WRAPPER}} .wdk_results_listings_slider_box .slick-dots li.slick-active .wdk_lr_dot'
            );

            $this->generate_renders_tabs($selectors, 'styles_carousel_dots_dynamic', ['margin','color','background','border','border_radius','padding','shadow','transition','font-size','hover_animation']);
        $this->end_controls_section();
    }

    private function generate_controls_content() {
        $this->start_controls_section(
            'content_thumbnail_section',
            [
                'label' => esc_html__('Colors', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_control(
            'content_thumbnail_section_header',
            [
                'label' => esc_html__('Color Hover Thumbnail', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_responsive_control(
            'content_thumbnail_section_d_background',
            [
                'label' => esc_html__( 'Color', 'wpdirectorykit' ),
                'description' => esc_html__( 'Set some opacity for color', 'wpdirectorykit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wdk-listing-card .wdk-thumbnail::before, {{WRAPPER}} .wdk-listing-card .wdk-thumbnail::after,{{WRAPPER}}  .wdk-listing-card .overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_thumbnail_section_header_f',
            [
                'label' => esc_html__('Shadow around Card, for Featured Listings', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                        'name' => 'content_thumbnail_section_d_featured',
                        'exclude' => [
                                'field_shadow_position',
                        ],
                        'selector' => '{{WRAPPER}} .wdk-listing-card.is_featured',
                ]
        );
        
        $this->end_controls_section();


        $items = [
            [
                'key'=>'content_card',
                'label'=> esc_html__('Card', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card',
                'options'=>'full',
            ],
            [
                'key'=>'content_label',
                'label'=> esc_html__('Over Image Top', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-thumbnail .wdk-over-image-top span',
                'options'=>'full',
            ],
            [
                'key'=>'content_type',
                'label'=> esc_html__('Over Image Bottom', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-thumbnail .wdk-over-image-bottom',
                'is_featured'=>'.wdk-element .wdk-listing-card.is_featured .wdk-thumbnail .wdk-over-image-bottom',
                'options'=>'full',
            ],
            [
                'key'=>'content_title',
                'label'=> esc_html__('Title Part', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-title .title',
                'options'=>'full',
            ],
            [
                'key'=>'content_description',
                'label'=> esc_html__('Subtitle part', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-subtitle-part',
                'options'=>'full',
            ],
            [
                'key'=>'content_items',
                'label'=> esc_html__('Features part', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-features-part span',
                'options'=>'full',
            ],
            [
                'key'=>'wdk-divider',
                'label'=> esc_html__('Divider', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-divider',
                'options'=>'full',
            ],
            [
                'key'=>'content_price',
                'label'=> esc_html__('Pricing part', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-footer .wdk-price',
                'options'=>'full',
            ],
            [
                'key'=>'content_button',
                'label'=> esc_html__('Button Open', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-footer .wdk-btn',
                'options'=>'full',
            ],
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => '1',
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
            
            if(isset($item['is_featured'])) {
                $selectors['featured'] = '{{WRAPPER}} '.$item['is_featured'];
            }
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            /* special for some elements */
            if ($item['key'] == 'content_description') {
            
                $this->add_control(
                    'content_description_limit',
                    [
                        'label' => __( 'Limit Line (per field)', 'wpdirectorykit' ),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                        'default' => 3, 
                        'selectors' => [
                            '{{WRAPPER}} .wdk-listing-card .wdk-subtitle-part span' => '-webkit-line-clamp: {{VALUE}};',
                        ],
                    ]
                );

            }
            if($item['key'] == 'content_button') {
                $this->add_control(
                    $item['key'].'_icon',
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
                    'normal' => '{{WRAPPER}} '.$item['selector'].' i',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_icon_dynamic', ['margin']);
            }

            if($item['key'] == 'content_items') {
                $this->add_control(
                    $item['key'].'_parent_head',
                    [
                        'label' => esc_html__('Parent Box', 'wpdirectorykit'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-element .wdk-listing-card .wdk-features-part',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_parent_dynamic', ['margin']);
            }

            if($item['key'] == 'content_label') {
                $this->add_control(
                    $item['key'].'_parent_head',
                    [
                        'label' => esc_html__('Parent Box', 'wpdirectorykit'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-element .wdk-listing-card .wdk-over-image-top',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_parent_dynamic', ['margin','align']);
            }


            if($item['key'] == 'content_label') {
                $this->add_responsive_control(
                    $item['key'] .'content_label_positions_y',
                    [
                        'label' => __( 'Position Y', 'wpdirectorykit' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'top' => [
                                    'title' => esc_html__( 'Top', 'wpdirectorykit' ),
                                    'icon' => 'eicon-text-align-top',
                            ],
                            'center' => [
                                    'title' => esc_html__( 'Center', 'wpdirectorykit' ),
                                    'icon' => 'eicon-text-align-center',
                            ],
                            'bottom' => [
                                    'title' => esc_html__( 'Bottom', 'wpdirectorykit' ),
                                    'icon' => 'eicon-text-align-bottom',
                            ],
                        ],
                        'default' => 'left',
                        'render_type' => 'ui',
                        'selectors_dictionary' => [
                            'top' => 'top:0;bottom:initial',
                            'center' => 'top:50%;transform: translateY(-50%)',
                            'bottom' => 'top:initial;bottom:0',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wdk-listing-card .wdk-thumbnail .wdk-over-image-top' => '{{VALUE}};',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    $item['key'] .'content_label_positions_x',
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
                        ],
                        'default' => 'left',
                        'render_type' => 'ui',
                        'selectors_dictionary' => [
                            'left' => 'left:0',
                            'center' => 'left:50%;transform: translateX(-50%)',
                            'right' => 'left:initial; right:0',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wdk-listing-card .wdk-thumbnail .wdk-over-image-top' => '{{VALUE}};',
                        ],
                    ]
                );
            }

            if($item['key'] == 'content_type') {
                $this->add_responsive_control(
                    $item['key'] .'content_label_positions_y',
                    [
                        'label' => __( 'Position Y', 'wpdirectorykit' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'top' => [
                                    'title' => esc_html__( 'Top', 'wpdirectorykit' ),
                                    'icon' => 'eicon-text-align-top',
                            ],
                            'center' => [
                                    'title' => esc_html__( 'Center', 'wpdirectorykit' ),
                                    'icon' => 'eicon-text-align-center',
                            ],
                            'bottom' => [
                                    'title' => esc_html__( 'Bottom', 'wpdirectorykit' ),
                                    'icon' => 'eicon-text-align-bottom',
                            ],
                        ],
                        'default' => 'left',
                        'render_type' => 'ui',
                        'selectors_dictionary' => [
                            'top' => 'top:0;bottom:initial',
                            'center' => 'top:50%;transform: translateY(-50%)',
                            'bottom' => 'top:initial;bottom:0',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} '.$item['selector'] => '{{VALUE}};',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    $item['key'] .'content_label_positions_x',
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
                        'render_type' => 'ui',
                        'selectors_dictionary' => [
                            'top' => 'justify-content: flex-start;',
                            'center' => 'justify-content: center;',
                            'bottom' => 'justify-content: flex-end;',
                            'justify' => 'justify-content: stretch;',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} '.$item['selector'] => '{{VALUE}};',
                        ],
                    ]
                );
            }
            $this->end_controls_section();
        }
    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('slick');
        wp_enqueue_style('slick-theme');
        wp_enqueue_script('slick');
        
        wp_enqueue_style('wdk-notify');
        wp_enqueue_script('wdk-notify');
        wp_enqueue_style('wdk-listings-carousel');
    }
}
