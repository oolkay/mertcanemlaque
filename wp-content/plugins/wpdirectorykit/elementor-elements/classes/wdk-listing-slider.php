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
class WdkListingSlider extends WdkElementorBase {

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
        return 'wdk-listing-slider';
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
        return esc_html__('Wdk Listing Slider', 'wpdirectorykit');
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
        global $wdk_listing_id;

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

        echo $this->view('wdk-listing-slider', $this->data); 
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
            'popup_enable',
            [
                'label' => __( 'Open Images / Video in popup', 'wdk-listing-sliders' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wdk-listing-sliders' ),
                'label_off' => __( 'Off', 'wdk-listing-sliders' ),
                'return_value' => 'yes',
                'default' => '',
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
				'default' => 10,
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
        $this->start_controls_section(
            'tab_content',
            [
                'label' => esc_html__('Basic', 'wpdirectorykit'),
                'tab' => 'tab_layout',
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
                        '{{WRAPPER}} .wdk-col' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .wdk-row, {{WRAPPER}} .wdk_listing_slider_ini ' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
                    ],
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

        $this->end_controls_section();
        
    }

    private function generate_controls_styles() {
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
                    'default' => 'none',
                    'options' => [
                        'none' => __( 'None', 'wpdirectorykit' ),
                        'contain' => __( 'Contain', 'wpdirectorykit' ),
                        'cover' => __( 'Cover', 'wpdirectorykit' ),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk_listing_slider_box .wdk-listing-image' => 'object-fit: {{VALUE}}',
                    ],
                ]
            );

            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk_listing_slider_box .wdk-listing-image',
            );
            $this->generate_renders_tabs($selectors, 'layout_image_dynamic', 'block');

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
                    'default' => [
                        'size' => 350,
                        'unit' => 'px',
                    ],
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
                    'label' => esc_html__('Thumbnail Navs', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
            
            $this->add_responsive_control(
                'styles_thmbn_nav_section_hide',
                [
                        'label' => esc_html__( 'Hide Element', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                        'block' => esc_html__( 'Show', 'wpdirectorykit' ),
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
                    'label' => __( 'Count grid', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'default' => 4,
                ]
            );


            $selectors = array(
                'normal' => '{{WRAPPER}} .banner-thumbs .banner-thumb',
                'hover' => '{{WRAPPER}} .banner-thumbs .banner-thumb%1$s,{{WRAPPER}} .wdk-listing-slider .banner-thumbs-con .banner-thumbs .slick-slide.slick-current.slick-active .banner-thumb',
            );
            $this->generate_renders_tabs($selectors, 'layout_image_nav_dynamic', ['border','background','padding','border_radius']);

            $this->add_responsive_control(
                'styles_thmbn_nav_mask_header',
                [
                    'label' => esc_html__('Mask', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-listing-slider .banner-thumbs-con .banner-thumbs .banner-thumb::before',
            );
            $this->generate_renders_tabs($selectors, 'styles_thmbn_nav_mask', ['background']);

            $this->add_control(
                'styles_thmbn_nav_mask_hr',
                [
                        'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $this->add_responsive_control (
                'styles_thmbn_nav_height',
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
                    'label' => esc_html__('Nav container', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );
            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-listing-slider .banner-thumbs-con',
            );
            $this->generate_renders_tabs($selectors, 'styles_thmbn_nav_box_styles', ['margin']);

            $this->end_controls_section();

            $items = [
                [
                    'key'=>'card',
                    'label'=> esc_html__('Slide', 'wpdirectorykit'),
                    'selector'=>'.wdk_listing_slider_box .wdk-listing-image-card',
                    'selector_hover'=>'.wdk_listing_slider_box .wdk-listing-image-card%1$s',
                    'options'=>'block',
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
        
                $selectors = array(
                    'normal' => '{{WRAPPER}} '.$item['selector'],
                    'hover'=>'{{WRAPPER}} '.$item['selector_hover'],
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

                $this->end_controls_section();
            }


            $this->start_controls_section(
                'styles_carousel_arrows_section',
                [
                    'label' => esc_html__('Carousel Arrows', 'wpdirectorykit'),
                    'tab' => '1',
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
                            '{{WRAPPER}} .wdk-listing-slider .wdk-listing-slider_arrows' => 'display: {{VALUE}};',
                        ],
                ]
            );

            $this->add_responsive_control(
                'styles_carousel_arrows_position',
                [
                    'label' => __( 'Position', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk-listing-slider_arrows_bottom',
                    'options' => [
                        'wdk-listing-slider_arrows_bottom'  => __( 'Bottom', 'wpdirectorykit' ),
                        'wdk-listing-slider_arrows_middle' => __( 'Center', 'wpdirectorykit' ),
                        'wdk-listing-slider_arrows_top' => __( 'Top', 'wpdirectorykit' ),
                    ],
                ]
            );

            $this->add_responsive_control(
                'styles_carousel_arrows_position_style',
                [
                    'label' => __( 'Position Style', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk-listing-slider_arrows_out',
                    'options' => [
                        'wdk-listing-slider_arrows_out' => __( 'Out', 'wpdirectorykit' ),
                        'wdk-listing-slider_arrows_in' => __( 'In', 'wpdirectorykit' ),
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
                        '{{WRAPPER}} .wdk-listing-slider .wdk-listing-slider_arrows' => '{{VALUE}};',
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
                'normal' => '{{WRAPPER}} .wdk-listing-slider .wdk-listing-slider_arrows .wdk-listing-slider_arrow.wdk_prev',
            );
            $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_s_m_left', ['margin']);

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
                'normal' => '{{WRAPPER}} .wdk-listing-slider .wdk-listing-slider_arrows .wdk-listing-slider_arrow.wdk_next',
            );
            $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_s_m_next', ['margin']);

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
                'normal' => '{{WRAPPER}} .wdk-listing-slider .wdk-listing-slider_arrows .wdk-listing-slider_arrow',
                'hover'=>'{{WRAPPER}} .wdk-listing-slider .wdk-listing-slider_arrows .wdk-listing-slider_arrow%1$s'
            );
            $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_dynamic', ['typo','color','background','border','border_radius','padding','shadow','transition']);

            $this->end_controls_section();

            $this->start_controls_section(
                'styles_carousel_dots_section',
                [
                    'label' => esc_html__('Section Dots', 'wpdirectorykit'),
                    'tab' => '1',
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
                                '{{WRAPPER}} .wdk-listing-slider .slick-dots' => 'display: {{VALUE}} !important;',
                            ],
                    ]
            );

            $this->add_responsive_control(
                'styles_carousel_dots_position_style',
                [
                    'label' => __( 'Position Style', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk-listing-slider_dots_out',
                    'options' => [
                        'wdk-listing-slider_dots_out' => __( 'Out', 'wpdirectorykit' ),
                        'wdk-listing-slider_dots_in' => __( 'In', 'wpdirectorykit' ),
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
                        '{{WRAPPER}} .wdk-listing-slider .slick-dots' => '{{VALUE}};',
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
                'normal' => '{{WRAPPER}} .wdk-listing-slider .slick-dots li .wdk_dot',
                'hover'=>'{{WRAPPER}} .wdk-listing-slider .slick-dots li .wdk_dot%1$s'
            );
            $this->generate_renders_tabs($selectors, 'styles_carousel_dots_dynamic', 'full', ['align']);

        $this->end_controls_section();
    }

    private function generate_controls_content() {
     
    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-listing-slider');
        wp_enqueue_style('slick');
        wp_enqueue_style('slick-theme');
        wp_enqueue_script('slick');
        
        wp_enqueue_style('wdk-notify');
        wp_enqueue_script('wdk-notify');

        wp_enqueue_style('blueimp-gallery');
        wp_enqueue_script('blueimp-gallery');
        wp_enqueue_script('wdk-blueimp-gallery');
    }
}
