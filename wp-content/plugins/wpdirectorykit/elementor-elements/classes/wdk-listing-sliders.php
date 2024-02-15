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
use Elementor\Group_Control_Css_Filter;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * @since 1.1.0
 */
class WdkListingSliders extends WdkElementorBase {
    
        public function __construct($data = array(), $args = null) {
    
            \Elementor\Controls_Manager::add_tab(
                'tab_conf',
                esc_html__('Settings', 'wdk-listing-sliders')
            );
    
            \Elementor\Controls_Manager::add_tab(
                'tab_slider_main',
                esc_html__('Slider Main', 'wdk-listing-sliders')
            );
    
            \Elementor\Controls_Manager::add_tab(
                'tab_slider_nav',
                esc_html__('Slider Nav', 'wdk-listing-sliders')
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
            return [ 'wdk-elementor-listing-preview-sliders' ];
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
            return 'wdk-listing-sliders';
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
            return esc_html__('Wdk Listing Slider + Carousel', 'wdk-listing-sliders');
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
            return 'eicon-featured-image';
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
            global $wdk_listing_id;
            /* test data */ 
            $this->data['id_element'] = $this->get_id();
            $this->data['settings'] = $this->get_settings();
    
            $this->data['images'] = array();
            if(!Plugin::$instance->editor->is_edit_mode()) {
                $this->data['images'] = wdk_listing_images_data (array('listing_images'=>wdk_field_value ('listing_images', $wdk_listing_id)), 'full');
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

            echo $this->view('wdk-listing-sliders', $this->data); 
        }
    
    
        private function generate_controls_conf() {
            $this->start_controls_section(
                'tab_conf_main_section',
                [
                    'label' => esc_html__('Main', 'wdk-listing-sliders'),
                    'tab' => 'tab_conf',
                ]
            );
                    
            $this->add_control(
                'limit_images',
                [
                    'label' => __( 'Limit Images', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'default' => 10,
                ]
            );
    
            $this->add_control(
                'offset_images',
                [
                    'label' => __( 'Offset Images', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'default' => 1,
                ]
            );

                
            $this->add_control(
                'wdk_listing_video_disabled',
                [
                    'label' => __( 'Disable Video', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'True', 'wpdirectorykit' ),
                    'label_off' => __( 'False', 'wpdirectorykit' ),
                    'return_value' => '1',
                    'default' => '',
                ]
            );
    
            $this->end_controls_section();
    
        }
    
        private function generate_controls_layout() {
            if(false) {

                $this->start_controls_section(
                    'tab_slider_nav',
                    [
                        'label' => esc_html__('Basic', 'wdk-listing-sliders'),
                        'tab' => 'tab_slider_main',
                    ]
                );
                    
                $this->end_controls_section();
            }
            
            $this->start_controls_section(
                'layout_carousel_sec',
                [
                    'label' => esc_html__('Carousel Options', 'wdk-listing-sliders'),
                    'tab' => 'tab_slider_main',
                ]
            );
    
            $this->add_control(
                'layout_carousel_is_infinite',
                [
                    'label' => __( 'Infinite', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wdk-listing-sliders' ),
                    'label_off' => __( 'Off', 'wdk-listing-sliders' ),
                    'return_value' => 'true',
                    'default' => 'true',
                ]
            );
    
            $this->add_control(
                'layout_carousel_is_autoplay',
                [
                    'label' => __( 'Autoplay', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wdk-listing-sliders' ),
                    'label_off' => __( 'Off', 'wdk-listing-sliders' ),
                    'return_value' => 'true',
                    'default' => '',
                ]
            );
    
            $this->add_control(
                'layout_carousel_speed',
                [
                    'label' => __( 'Speed', 'wdk-listing-sliders' ),
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
                    'label' => __( 'Animation Style', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'fade_in',
                    'options' => [
                        'slide'  => __( 'Slide', 'wdk-listing-sliders' ),
                        'fade' => __( 'Fade', 'wdk-listing-sliders' ),
                        'fade_in' => __( 'Fade in', 'wdk-listing-sliders' ),
                    ],
                ]
            );
    
            $this->add_control(
                'layout_carousel_cssease',
                [
                    'label' => __( 'cssEase ', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'linear',
                    'options' => [
                        'linear'  => __( 'linear', 'wdk-listing-sliders' ),
                        'ease' => __( 'ease', 'wdk-listing-sliders' ),
                        'ease-in' => __( 'ease-in', 'wdk-listing-sliders' ),
                        'ease-out' => __( 'ease-out', 'wdk-listing-sliders' ),
                        'ease-in-out' => __( 'ease-in-out', 'wdk-listing-sliders' ),
                        'step-start' => __( 'step-start', 'wdk-listing-sliders' ),
                        'step-end' => __( 'step-end', 'wdk-listing-sliders' ),
                    ],
                ]
            );
    
            $this->end_controls_section();


            $this->start_controls_section(
                'layout_carousel_nav_sec',
                [
                    'label' => esc_html__('Carousel Nav Options', 'wdk-listing-sliders'),
                    'tab' => '1',
                ]
            );

            $this->add_responsive_control(
                'styles_thmbn_nav_section_hide',
                [
                        'label' => esc_html__( 'Hide Element', 'wdk-listing-sliders' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'Hide', 'wdk-listing-sliders' ),
                        'block' => esc_html__( 'Show', 'wdk-listing-sliders' ),
                        'return_value' => 'none',
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .banner-thumbs' => 'display: {{VALUE}};',
                        ],
                ]
            );

            $this->add_responsive_control(
                'styles_thmbn_nav_columns',
                [
                    'label' => __( 'Count grid', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'default' => 4,
                ]
            );

            $this->add_control(
                'layout_carousel_nav_is_infinite',
                [
                    'label' => __( 'Infinite', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wdk-listing-sliders' ),
                    'label_off' => __( 'Off', 'wdk-listing-sliders' ),
                    'return_value' => 'true',
                    'default' => 'true',
                ]
            );

            $this->add_control(
                'layout_carousel_nav_variableWidth',
                [
                    'label' => __( 'Variable width slides', 'wdk-listing-sliders' ),
                    'description' => __( 'Ignore columns', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wdk-listing-sliders' ),
                    'label_off' => __( 'Off', 'wdk-listing-sliders' ),
                    'return_value' => 'true',
                    'default' => 'true',
                ]
            );

            $this->add_control(
                'layout_carousel_nav_is_center',
                [
                    'label' => __( 'Center Mode', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wdk-listing-sliders' ),
                    'label_off' => __( 'Off', 'wdk-listing-sliders' ),
                    'return_value' => 'true',
                    'default' => '',
                ]
            );
        
            $this->add_control(
                'layout_carousel_nav_speed',
                [
                    'label' => __( 'Animations Speed', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 100000,
                    'step' => 100,
                    'default' => 500,
                ]
            );
    
            
            $this->add_control(
                'layout_carousel_nav_is_autoplay',
                [
                    'label' => __( 'Autoplay', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wdk-listing-sliders' ),
                    'label_off' => __( 'Off', 'wdk-listing-sliders' ),
                    'return_value' => 'true',
                    'default' => '',
                ]
            );

            $this->add_control(
                'layout_carousel_nav_autoplay_speed',
                [
                    'label' => __( 'Autoplay Speed', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 100000,
                    'step' => 100,
                    'default' => 500,
                ]
            );
    
            $this->add_control(
                'layout_carousel_nav_animation_style',
                [
                    'label' => __( 'Animation Style', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'fade_in',
                    'options' => [
                        'slide'  => __( 'Slide', 'wdk-listing-sliders' ),
                        'fade' => __( 'Fade', 'wdk-listing-sliders' ),
                        'fade_in' => __( 'Fade in', 'wdk-listing-sliders' ),
                    ],
                ]
            );
    
            $this->add_control(
                'layout_carousel_nav_cssease',
                [
                    'label' => __( 'cssEase ', 'wdk-listing-sliders' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'linear',
                    'options' => [
                        'linear'  => __( 'linear', 'wdk-listing-sliders' ),
                        'ease' => __( 'ease', 'wdk-listing-sliders' ),
                        'ease-in' => __( 'ease-in', 'wdk-listing-sliders' ),
                        'ease-out' => __( 'ease-out', 'wdk-listing-sliders' ),
                        'ease-in-out' => __( 'ease-in-out', 'wdk-listing-sliders' ),
                        'step-start' => __( 'step-start', 'wdk-listing-sliders' ),
                        'step-end' => __( 'step-end', 'wdk-listing-sliders' ),
                    ],
                ]
            );
    
            $this->end_controls_section();
            
        }
    
        private function generate_controls_styles() {

                $items = [
                    [
                        'key'=>'card',
                        'label'=> esc_html__('Slider', 'wdk-listing-sliders'),
                        'selector'=>'.wdk_listing_slider_box .wdk-listing-image-card',
                        'selector_hover'=>'.wdk_listing_slider_box .wdk-listing-image-card%1$s',
                        'options'=>['background','border','border_radius','padding','shadow','transition'],
                    ],
                ];

                foreach ($items as $item) {
                    $this->start_controls_section(
                        $item['key'].'_section',
                        [
                            'label' => $item['label'],
                            'tab' => 'tab_slider_main',
                        ]
                    );
            
                    $selectors = array(
                        'normal' => '{{WRAPPER}} '.$item['selector'],
                        'hover'=>'{{WRAPPER}} '.$item['selector_hover'],
                    );
                    $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

                    $this->end_controls_section();
                }

                $this->start_controls_section(
                    'styles_thmbn_type',
                    [
                        'label' => esc_html__('Image Slide', 'wdk-listing-sliders'),
                        'tab' => 'tab_slider_main',
                    ]
                );
    
                $this->add_control(
                    'layout_image_design',
                    [
                        'label' => __( 'Size style thumbnail', 'wdk-listing-sliders' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            '' => __( 'Default', 'wdk-listing-sliders' ),
                            'none' => __( 'None', 'wdk-listing-sliders' ),
                            'contain' => __( 'Contain', 'wdk-listing-sliders' ),
                            'cover' => __( 'Cover', 'wdk-listing-sliders' ),
                            'fill' => __( 'Fill', 'wdk-listing-sliders' ),
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wdk_listing_slider_box .wdk-listing-image' => 'object-fit: {{VALUE}}',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'layout_image_mask_header',
                    [
                        'label' => esc_html__('Mask', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk_listing_slider_box .wdk-listing-image-card:after',
                );
                $this->generate_renders_tabs($selectors, 'layout_image_mask_styles', ['background_group']);
        
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk_listing_slider_box .wdk-listing-image',
                );

                $this->generate_renders_tabs($selectors, 'layout_image_dynamic', ['background','border','border_radius','padding','shadow','css_filters','transition']);
    
                $this->add_control(
                    'enable_fixed_height',
                    [
                        'label' => __( 'Fixed Height', 'wdk-listing-sliders' ),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => __( 'True', 'wdk-listing-sliders' ),
                        'label_off' => __( 'False', 'wdk-listing-sliders' ),
                        'return_value' => 'yes',
                        'default' => 'yes',
                    ]
                );
    
                $this->add_responsive_control (
                    'styles_thmbn_des_height',
                    [
                        'label' => esc_html__('Height', 'wdk-listing-sliders'),
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
                            'vh' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'size_units' => [ 'px', 'vw', 'vh' ],
                        'selectors' => [
                            '{{WRAPPER}} .wdk_listing_slider_box .wdk-listing-image' => 'height: {{SIZE}}{{UNIT}}',
                        ],
                        'separator' => 'after',
                    ]
                );
    
                $this->end_controls_section();
    
                $this->start_controls_section(
                    'styles_thmbn_nav_section',
                    [
                        'label' => esc_html__('Thumbnail Navs', 'wdk-listing-sliders'),
                        'tab' => '1',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .banner-thumbs .banner-thumb',
                    'hover' => '{{WRAPPER}} .banner-thumbs .banner-thumb%1$s',
                    'active' => '{{WRAPPER}} .wdk-listing-sliders .wdk-cls-banner-thumbs .banner-thumbs .slick-slide.slick-current.slick-active .banner-thumb',
                );
                $this->generate_renders_tabs($selectors, 'layout_image_nav_dynamic', ['background','border','border_radius','padding','margin','shadow','transition']);

                $this->add_responsive_control(
                    'layout_image_nav_image_header',
                    [
                        'label' => esc_html__('Mask', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-sliders .wdk-cls-banner-thumbs .banner-thumbs .banner-thumb img',
                    'hover' => '{{WRAPPER}} .wdk-listing-sliders .wdk-cls-banner-thumbs .banner-thumbs .banner-thumb%1$s img',
                    'active' => '{{WRAPPER}} .wdk-listing-sliders .wdk-cls-banner-thumbs .banner-thumbs .slick-slide.slick-current.slick-active .banner-thumb img',
                );
                $this->generate_renders_tabs($selectors, 'layout_image_nav_image_styles', ['css_filters','transition']);

                $this->add_responsive_control(
                    'layout_image_nav_mask_header',
                    [
                        'label' => esc_html__('Mask', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-sliders .wdk-cls-banner-thumbs .banner-thumbs .banner-thumb:before',
                    'active' => '{{WRAPPER}} .wdk-listing-sliders .wdk-cls-banner-thumbs .banner-thumbs .slick-slide.slick-current.slick-active .banner-thumb:before',
                );
                $this->generate_renders_tabs($selectors, 'layout_image_nav_mask_styles', ['background_group']);

    
                $this->add_responsive_control (
                    'styles_thmbn_nav_height',
                    [
                        'label' => esc_html__('Height', 'wdk-listing-sliders'),
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
                            'size' => 80,
                            'unit' => 'px',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .banner-thumbs .banner-thumb' => 'height: {{SIZE}}{{UNIT}}',
                        ],
                        'separator' => 'after',
                    ]
                );
    
                $this->add_responsive_control(
                    'styles_thmbn_nav_box',
                    [
                        'label' => esc_html__('Nav container', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-sliders .wdk-cls-banner-thumbs',
                );
                $this->generate_renders_tabs($selectors, 'styles_thmbn_nav_box_styles', ['margin']);
    
                $this->end_controls_section();
     
    
                $this->start_controls_section(
                    'styles_carousel_arrows_section',
                    [
                        'label' => esc_html__('Carousel Arrows', 'wdk-listing-sliders'),
                        'tab' => 'tab_slider_main',
                    ]
                );
    
                $this->add_responsive_control(
                    'styles_carousel_arrows_hide',
                    [
                            'label' => esc_html__( 'Hide Element', 'wdk-listing-sliders' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'Hide', 'wdk-listing-sliders' ),
                            'block' => esc_html__( 'Show', 'wdk-listing-sliders' ),
                            'return_value' => 'none',
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .wdk-listing-sliders .wdk-listing-sliders_arrows' => 'display: {{VALUE}};',
                            ],
                    ]
                );
    
                $this->add_responsive_control(
                    'styles_carousel_arrows_position',
                    [
                        'label' => __( 'Position', 'wdk-listing-sliders' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'wdk-listing-sliders_arrows_middle',
                        'options' => [
                            ''  => __( 'Default', 'wdk-listing-sliders' ),
                            'wdk-listing-sliders_arrows_bottom'  => __( 'Bottom', 'wdk-listing-sliders' ),
                            'wdk-listing-sliders_arrows_middle' => __( 'Center', 'wdk-listing-sliders' ),
                            'wdk-listing-sliders_arrows_top' => __( 'Top', 'wdk-listing-sliders' ),
                        ],
                    ]
                );
    
                $this->add_responsive_control(
                    'styles_carousel_arrows_position_style',
                    [
                        'label' => __( 'Position Style', 'wdk-listing-sliders' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'wdk-listing-sliders_arrows_in',
                        'options' => [
                            '' => __( 'Default', 'wdk-listing-sliders' ),
                            'wdk-listing-sliders_arrows_out' => __( 'Out', 'wdk-listing-sliders' ),
                            'wdk-listing-sliders_arrows_in' => __( 'In', 'wdk-listing-sliders' ),
                        ],
                    ]
                );
    
                $this->add_responsive_control(
                    'styles_carousel_arrows_align',
                    [
                        'label' => __( 'Align', 'wdk-listing-sliders' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                    'title' => esc_html__( 'Left', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-left',
                            ],
                            'center' => [
                                    'title' => esc_html__( 'Center', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-center',
                            ],
                            'right' => [
                                    'title' => esc_html__( 'Right', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-right',
                            ],
                            'justify' => [
                                    'title' => esc_html__( 'Justified', 'wdk-listing-sliders' ),
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
                            '{{WRAPPER}} .wdk-listing-sliders .wdk-listing-sliders_arrows' => '{{VALUE}};',
                        ],
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'styles_carousel_arrows_position',
                                    'operator' => '!=',
                                    'value' => 'wdk-listing-sliders_arrows_middle',
                                ]
                            ],
                        ],
                    ]
                );
                
                $this->add_responsive_control(
                    'styles_carousel_arrows_icon_left_h',
                    [
                        'label' => esc_html__('Arrow left', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-sliders .wdk-listing-sliders_arrows .wdk-listing-sliders_arrow.wdk-slider-prev',
                );
                $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_s_m_left', ['margin']);
    
                $this->add_responsive_control(
                    'styles_carousel_arrows_icon_left',
                    [
                        'label' => esc_html__('Icon', 'wdk-listing-sliders'),
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
                        'label' => esc_html__('Arrow right', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-sliders .wdk-listing-sliders_arrows .wdk-listing-sliders_arrow.wdk-slider-next',
                );
                $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_s_m_next', ['margin']);
    
                $this->add_responsive_control(
                    'styles_carousel_arrows_icon_right',
                    [
                        'label' => esc_html__('Icon', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::ICONS,
                        'label_block' => true,
                        'default' => [
                            'value' => 'fa fa-angle-right',
                            'library' => 'solid',
                        ],
                    ]
                );
                
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-sliders .wdk-listing-sliders_arrows .wdk-listing-sliders_arrow',
                    'hover'=>'{{WRAPPER}} .wdk-listing-sliders .wdk-listing-sliders_arrows .wdk-listing-sliders_arrow%1$s'
                );
                $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_dynamic', ['font-size','color','background','border','border_radius','padding','shadow','transition']);
    
                $this->end_controls_section();
    
                $this->start_controls_section(
                    'styles_carousel_dots_section',
                    [
                        'label' => esc_html__('Section Dots', 'wdk-listing-sliders'),
                        'tab' => 'tab_slider_main',
                    ]
                );
    
                $this->add_responsive_control(
                        'styles_carousel_dots_hide',
                        [
                                'label' => esc_html__( 'Hide Element', 'wdk-listing-sliders' ),
                                'type' => Controls_Manager::SWITCHER,
                                'none' => esc_html__( 'Hide', 'wdk-listing-sliders' ),
                                'block' => esc_html__( 'Show', 'wdk-listing-sliders' ),
                                'return_value' => 'none',
                                'default' => '',
                                'selectors' => [
                                    '{{WRAPPER}} .wdk-listing-sliders .slick-dots' => 'display: {{VALUE}} !important;',
                                ],
                        ]
                );
    
                $this->add_responsive_control(
                    'styles_carousel_dots_position_style',
                    [
                        'label' => __( 'Position Style', 'wdk-listing-sliders' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'wdk-listing-sliders_dots_in',
                        'options' => [
                            '' => __( 'Default', 'wdk-listing-sliders' ),
                            'wdk-listing-sliders_dots_out' => __( 'Out', 'wdk-listing-sliders' ),
                            'wdk-listing-sliders_dots_in' => __( 'In', 'wdk-listing-sliders' ),
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'styles_carousel_dots_direction',
                    [
                            'label' => __( 'Direction Dots', 'wdk-listing-sliders' ),
                            'type' => Controls_Manager::SELECT,
                            'options' => [
                                '' => esc_html__('Default', 'wdk-listing-sliders'),
                                'column' => esc_html__('Column', 'wdk-listing-sliders'),
                                'column-reverse' => esc_html__('Column Reverse', 'wdk-listing-sliders'),
                                'row' => esc_html__('Row', 'wdk-listing-sliders'),
                                'row-reverse' => esc_html__('Row Reverse', 'wdk-listing-sliders'),
                            ],
                            'selectors_dictionary' => [
                                'column' => 'display: flex !important; flex-direction: column;',
                                'column-reverse' =>  'display: flex !important; flex-direction: column-reverse;',
                                'row' =>  'display: flex !important; flex-direction: row;',
                                'row-reverse' =>  'display: flex !important; flex-direction: row-reverse;',
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .wdk-listing-sliders .slick-dots' => '{{UNIT}}',
                            ],
                            'default' => '100%', 
                            'separator' => 'before',
                    ]
                );
    
                $this->add_responsive_control(
                    'styles_carousel_dots_align',
                    [
                        'label' => __( 'Position', 'wdk-listing-sliders' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                    'title' => esc_html__( 'Left', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-left',
                            ],
                            'center' => [
                                    'title' => esc_html__( 'Center', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-center',
                            ],
                            'right' => [
                                    'title' => esc_html__( 'Right', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-right',
                            ],
                            'justify' => [
                                    'title' => esc_html__( 'Justified', 'wdk-listing-sliders' ),
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
                            '{{WRAPPER}} .wdk-listing-sliders .slick-dots' => '{{VALUE}};',
                        ],
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'styles_carousel_dots_position_style',
                                    'operator' => '!=',
                                    'value' => 'wdk-listing-sliders_dots_in',
                                ]
                            ],
                        ],
                    ]
                );
    
                $this->add_responsive_control(
                    'styles_carousel_dots_in_align',
                    [
                        'label' => __( 'Position', 'wdk-listing-sliders' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                    'title' => esc_html__( 'Left', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-left',
                            ],
                            'center' => [
                                    'title' => esc_html__( 'Center', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-center',
                            ],
                            'right' => [
                                    'title' => esc_html__( 'Right', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-right',
                            ],
                        ],
                        'default' => 'center',
                        'render_type' => 'ui',
                        'selectors_dictionary' => [
                            'left' => 'left:0; right: initial',
                            'center' => 'left:50%; right: initial; transform:translateX(-50%)',
                            'right' => 'right:0; left: initial',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wdk-listing-sliders .slick-dots' => '{{VALUE}};',
                        ],
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'styles_carousel_dots_position_style',
                                    'operator' => '=',
                                    'value' => 'wdk-listing-sliders_dots_in',
                                ]
                            ],
                        ],
                    ]
                );
    
                $this->add_responsive_control(
                    'styles_carousel_dots_in_align_y',
                    [
                        'label' => __( 'Position Vertical', 'wdk-listing-sliders' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'top' => [
                                    'title' => esc_html__( 'Top', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-justify-start-v',
                            ],
                            'center' => [
                                    'title' => esc_html__( 'Center', 'wdk-listing-sliders' ),
                                    'icon' => 'eicon-text-align-center',
                            ],
                            'bottom' => [
                                    'title' => esc_html__( 'Bottom', 'wdk-listing-sliders' ),
                                    'icon' => ' eicon-justify-end-v',
                            ],
                        ],
                        'render_type' => 'ui',
                        'selectors_dictionary' => [
                            'top' => 'top:0; bottom: initial',
                            'center' => 'top:50%; bottom: initial; transform:translateY(-50%)',
                            'bottom' => 'bottom:0; top: initial',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wdk-listing-sliders .slick-dots' => '{{VALUE}};',
                        ],
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'styles_carousel_dots_position_style',
                                    'operator' => '=',
                                    'value' => 'wdk-listing-sliders_dots_in',
                                ]
                            ],
                        ],
                    ]
                );
                
                $this->add_responsive_control(
                    'styles_carousel_dots_icon',
                    [
                        'label' => esc_html__('Icon', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::ICONS,
                        'label_block' => true,
                        'default' => [
                            'value' => 'fas fa-circle',
                            'library' => 'solid',
                        ],
                    ]
                );
    
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-sliders .slick-dots li .wdk_dot',
                    'hover'=>'{{WRAPPER}} .wdk-listing-sliders .slick-dots li .wdk_dot%1$s',
                    'active'=>'{{WRAPPER}} .wdk-listing-sliders .slick-dots li.slick-active .wdk_dot'
                ); 

                $this->generate_renders_tabs($selectors, 'styles_carousel_dots_dynamic', ['background','border','border_radius','padding','margin','shadow','font-size', 'color','transition']);
    
                $this->add_responsive_control(
                    'styles_carousel_dots_hover_header',
                    [
                        'label' => esc_html__('Hover Animation', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'hover'=>'{{WRAPPER}} .wdk-listing-sliders .slick-dots li .wdk_dot%1$s i, {{WRAPPER}} .wdk-listing-sliders .slick-dots li .wdk_dot%1$s svg',
                ); 

                $this->generate_renders_tabs($selectors, 'styles_carousel_dots_hover_dynamic', ['hover_animation']);

                $this->add_responsive_control(
                    'styles_carousel_dots_box_header',
                    [
                        'label' => esc_html__('Dots container', 'wdk-listing-sliders'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-listing-sliders .slick-dots',
                ); 

                $this->generate_renders_tabs($selectors, 'styles_carousel_dots_box_dynamic', ['background','padding','margin','shadow', 'transition']);
    
            $this->end_controls_section();
        }
    
        private function generate_controls_content() {
         
        }
                
        public function enqueue_styles_scripts() {
            wp_enqueue_style('wdk-listing-sliders');
            wp_enqueue_style('slick');
            wp_enqueue_style('slick-theme');
            wp_enqueue_style('wdk-hover');
            
            wp_enqueue_script('slick');
            
            wp_enqueue_style('wdk-notify');
            wp_enqueue_script('wdk-notify');
        }
    }
    