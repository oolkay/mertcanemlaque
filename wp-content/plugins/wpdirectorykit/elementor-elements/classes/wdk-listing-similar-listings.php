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
class WdkListingSimilarListings extends WdkElementorBase {

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
        return 'wdk-listing-similar-listings';
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
     * Retrieve the widget title.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Wdk Listing Similar Listings', 'wpdirectorykit');
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
        return 'eicon-products';
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

        $this->data['listings_count'] = 0;
        $this->data['results'] = array();
        $this->data['pagination_output'] = '';

        $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
        $external_columns = array('location_id', 'category_id', 'post_title');
        $custom_parameters = array();

        $this->data['custom_order'] =  array();
        if ($this->data['settings']['custom_order_list']) {
            foreach ($this->data['settings']['custom_order_list'] as $item) {
                if (empty($item['title']) || empty($item['key'])) {
                    continue;
                }
                $this->data['custom_order'][] = array('title'=>$item['title'],'key'=>$item['key']);
            }
        }
        
        $controller = 'listing';
        $offset = NULL;
        
        if (isset($wdk_listing_id)) {
            foreach($this->data['settings']['similar_by_fields'] as $similar_field) {

                if(empty($similar_field['field'])) continue;

                if ($similar_field['field_skip_if_empty'] == 'true' && empty(wdk_field_value(str_replace('field','',$similar_field['field']), $wdk_listing_id))) {
                    continue;
                }

                if($similar_field['field'] == 'is_featured') {
                    if (!empty(wdk_field_value(str_replace('field','',$similar_field['field']), $wdk_listing_id))) {
                        $custom_parameters[$similar_field['field']] = 'on';
                    } else {
                        $custom_parameters[$similar_field['field']] = 'off';
                    }
                } else {    
                    $custom_parameters[$similar_field['field']] = wdk_field_value(str_replace('field','',$similar_field['field']), $wdk_listing_id);
                }
            }
        }

        if(wmvc_show_data('featured_query', $this->data['settings']) == 'only_featured') {
            $custom_parameters['is_featured'] = 'on';
        }

        if(wmvc_show_data('featured_query', $this->data['settings']) == 'only_unfeatured') {
            $custom_parameters['is_featured'] = 'off';
        }

        $_GET['order_by'] = $this->data['settings']['conf_order_by'].' '.$this->data['settings']['conf_order'];

        /* if detected custom field for order */
        if(!empty($this->data['settings']['conf_order_by_custom'])) {
            $_GET['order_by'] = $this->data['settings']['conf_order_by_custom'].' '.$this->data['settings']['conf_order'];
        }
            
        wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters, TRUE);

        /* filter same listing */

        if (isset($wdk_listing_id)) {
            global $wpdb;
            $this->WMVC->db->where($wpdb->prefix.'wdk_listings.post_id != '.$wdk_listing_id) ;
        }

        $this->data['results'] = $this->WMVC->listing_m->get_pagination($this->data['settings']['per_page'], $offset, array('is_activated' => 1,'is_approved'=>1));
        
        if(!empty($this->data['results']) && $this->data['listings_count'] == 0)
            $this->data['listings_count'] = wmvc_count($this->data['results']);

        $this->data['settings']['content_button_icon'] = $this->generate_icon($this->data['settings']['content_button_icon']);

        $this->data['is_edit_mode'] = false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode'] = true;

        if(isset($_GET['wmvc_view_type']) && $this->data['settings']['get_filters_enable'] == 'yes') {
            if(wmvc_xss_clean($_GET['wmvc_view_type']) == 'grid')
                $this->data['settings']['layout_type'] = 'grid';
            if(wmvc_xss_clean($_GET['wmvc_view_type']) == 'list')
                $this->data['settings']['layout_type'] = 'list';
        }
        
        echo $this->view('wdk-listing-similar-listings', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => 'tab_conf',
            ]
        );

        if(true){
            $this->add_control(
                'per_page',
                [
                    'label' => __( 'Limit Results (Per page)', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 250,
                    'step' => 1,
                    'default' => 6,
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
                ]
            );

            $this->add_control(
                'conf_order_by_custom',
                [
                    'label'         => __('Default Custom Sort By (小olumn', 'wpdirectorykit'),
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
                ]
            );

            $this->add_control(
                'conf_order',
                [
                    'label'         => __('Default Listing Order', 'wpdirectorykit'),
                    'type'          => Controls_Manager::SELECT,
                    'label_block'   => true,
                    'options'       => [
                        'asc'           => __('Ascending', 'wpdirectorykit'),
                        'desc'          => __('Descending', 'wpdirectorykit')
                    ],
                    'default'       => 'desc',
                ]
            );

        }

        $this->add_control (
            'disable_not_found_alert',
            [
                'label' => __( 'Disable not found listings alert', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'featured_query',
            [
                'label' => __( 'Featured Listings', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'any',
                'options' => [
                    'any' => __( 'Show Featured and Not Featured', 'wpdirectorykit' ),
                    'only_featured' => __( 'Show Only Featured', 'wpdirectorykit' ),
                    'only_unfeatured' => __( 'Show Only Not Featured', 'wpdirectorykit' ),
                ],
            ]
        );

        $WMVC = &wdk_get_instance();
        $WMVC->model('field_m'); 
		$fields_data = $WMVC->field_m->get_fields_data();
        $fields_list = array('0' => esc_html__('Not Selected', 'wpdirectorykit'));
        $fields_list ['category_id'] = esc_html__('Category', 'wpdirectorykit');
        $fields_list ['location_id'] = esc_html__('Location', 'wpdirectorykit');
        $fields_list ['is_featured'] = esc_html__('Featured', 'wpdirectorykit');
        
        foreach($fields_data as $field)
        {
            $fields_list['field_'.wmvc_show_data('idfield', $field)] = '#'.wmvc_show_data('idfield', $field).' '.wmvc_show_data('field_label', $field).'['.wmvc_show_data('field_type', $field).']';
        }

        $repeater_similar_by_fields = new Repeater();
        $repeater_similar_by_fields->start_controls_tabs( 'repeater_similar_by_fields' );

        $repeater_similar_by_fields->add_control(
            'field',
            [
                'label' => __( 'Field', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $fields_list,
            ]
        );

        $repeater_similar_by_fields->add_control (
            'field_skip_if_empty',
            [
                'label' => __( 'Skip from query if empty value', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'true',
                'default' => '',
            ]
        );

        $repeater_similar_by_fields->end_controls_tabs();

        $this->add_control(
            'similar_by_fields',
            [
                'label' => esc_html__('Fields For Query Similar Listings', 'wpdirectorykit'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater_similar_by_fields->get_controls(),
                'default' => [
                    [
                        'field' => 'category_id',
                    ],
                ],
                'title_field' => '{{{ field }}}',
            ]
        );
                    
        $this->add_control(
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Edit Result Card Designer <a href="%1$s" target="_blank"> open </a>', 'wpdirectorykit' ), admin_url('admin.php?page=wdk_resultitem')),
                'content_classes' => 'wdk_elementor_hint',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'tab_conf_main_section_order',
            [
                'label' => esc_html__('Custom Sort', 'wpdirectorykit'),
                'tab' => 'tab_conf',
            ]
        );

        $repeater_order = new Repeater();
        $repeater_order->start_controls_tabs( 'orders' );
        $repeater_order->add_control(
            'key',
            [
                'label' => __( 'Sort Key', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'post_id ASC',
                'description' => '<span style="word-break: break-all;">'.__( 'Custom Fields Order Example:', 'wpdirectorykit' ).
                                        '<br> field_13_NUMBER ASC - where 13 is field id, NUMBER - field type'.
                                        '<br> field_4_NUMBER DESC - where 4 is field id, NUMBER - field type'.
                                        '<br> field_6_DROPDOWN ASC - where 6 is field id, DROPDOWN - field type'.
                                        '<br> category_title  - Category Title'.
                                        '<br> location_title  - Location Title'.
                                '</span>',
            ]
        );
        $repeater_order->add_control(
            'title',
            [
                'label' => __( 'Title', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'Title', 'wpdirectorykit' ),
            ]
        );
        $repeater_order->end_controls_tabs();

        $this->add_control(
            'custom_order_list',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater_order->get_controls(),
                'default' => [
                ],
                'title_field' => '{{{ title }}}',
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

        $this->add_control(
            'layout_type',
            [
                'label' => __( 'Layout Type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => __( 'Grid', 'wpdirectorykit' ),
                    'list' => __( 'List', 'wpdirectorykit' ),
                    'carousel' => __( 'Carousel', 'wpdirectorykit' ),
                ],
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
                    'default' => '', 
                    'separator' => 'before',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_type',
                                'operator' => '==',
                                'value' => 'grid',
                            ]
                        ],
                    ],
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
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_type',
                                'operator' => '==',
                                'value' => 'grid',
                            ]
                        ],
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
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_type',
                                'operator' => '!=',
                                'value' => 'carousel',
                            ]
                        ],
                    ],
                ]
        );

        $this->add_responsive_control(
                'thumbnail_width',
                [
                    'label' => esc_html__('Thumbnail width (For List Version)', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 35,
                        'unit' => '%',
                    ],
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 600,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-element .wdk-listing-card.list .wdk-thumbnail' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_type',
                                'operator' => '==',
                                'value' => 'list',
                            ]
                        ],
                    ],
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
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'layout_type',
                                    'operator' => '==',
                                    'value' => 'carousel',
                                ]
                            ],
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
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'layout_type',
                                    'operator' => '==',
                                    'value' => 'carousel',
                                ]
                            ],
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
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'layout_type',
                                    'operator' => '==',
                                    'value' => 'carousel',
                                ]
                            ],
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
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_type',
                                'operator' => '==',
                                'value' => 'carousel',
                            ]
                        ],
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
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'layout_type',
                            'operator' => '==',
                            'value' => 'carousel',
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
                            'min' => 50,
                            'max' => 1500,
                        ],
                        'vw' => [
                            'min' => 0,
                            'max' => 100,
                        ]
                    ],
                    'size_units' => [ 'px', 'vw' ],
                    'default' => [
                        'size' => 220,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk_size_image_cover .wdk-listing-card .wdk-thumbnail .wdk-image' => 'height: {{SIZE}}{{UNIT}}',
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
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_type',
                                'operator' => '==',
                                'value' => 'carousel',
                            ]
                        ],
                    ],
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

            if(false)
            $this->add_responsive_control(
                'styles_carousel_arrows_position_style',
                [
                    'label' => __( 'Position Style', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'wdk_slider_arrows_out',
                    'options' => [
                        'wdk_slider_arrows_out' => __( 'Out', 'wpdirectorykit' ),
                        'wdk_slider_arrows_in' => __( 'In', 'wpdirectorykit' ),
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
                        'terms' => [
                            [
                                'name' => 'styles_carousel_arrows_position',
                                'operator' => '!=',
                                'value' => 'wdk_slider_arrows_middle',
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
            $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_dynamic', ['typo','color','background','border','border_radius','padding','shadow','transition']);

            $this->end_controls_section();

            $this->start_controls_section(
                'styles_carousel_dots_section',
                [
                    'label' => esc_html__('Section Dots', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'layout_type',
                                'operator' => '==',
                                'value' => 'carousel',
                            ]
                        ],
                    ],
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
                'hover'=>'{{WRAPPER}} .wdk_results_listings_slider_box .slick-dots li .wdk_lr_dot%1$s'
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
                $this->generate_renders_tabs($selectors, $item['key'].'_parent_dynamic', ['margin']);
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
    }
}
