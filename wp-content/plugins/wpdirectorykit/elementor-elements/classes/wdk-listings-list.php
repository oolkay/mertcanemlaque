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
class WdkListingsList extends WdkElementorBase {

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
        return 'wdk-listings-list';
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
        return esc_html__('Wdk Listings List', 'wpdirectorykit');
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
        return 'eicon-section';
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

        $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
        $external_columns = array('location_id', 'category_id', 'post_title');
        $custom_parameters = array();
        
        $this->data['custom_order'] =  array();
        if($this->data['settings']['custom_order_list']) {
            foreach($this->data['settings']['custom_order_list'] as $item){
                if(empty($item['title']) || empty($item['key'])) continue;
                $this->data['custom_order'][] = array('title'=>$item['title'],'key'=>$item['key']);
            }
        }
        
        if($this->data['settings']['conf_results_type'] == 'results_listings') {
            $controller = 'listing';
            $offset = NULL;

            $custom_parameters['order_by'] = $this->data['settings']['conf_order_by'].' '.$this->data['settings']['conf_order'];

            /* if detected custom field for order */
            if(!empty($this->data['settings']['conf_order_by_custom'])) {
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

            wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters, TRUE);
            $this->data['results'] = $this->WMVC->listing_m->get_pagination($this->data['settings']['per_page'], $offset, array('is_activated' => 1,'is_approved'=>1));
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

            /* hide filter header on specific results */
            $this->data['settings']['get_filters_enable'] = '';
        }

        $this->data['is_edit_mode'] = false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode'] = true;

        echo $this->view('wdk-listings-list', $this->data);

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
                'label' => __( 'Results type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'results_listings',
                'options' => [
                    'results_listings'  => __( 'Results Listings', 'wpdirectorykit' ),
                    'custom_listings' => __( 'Specific Listings', 'wpdirectorykit' ),
                ],
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
                'per_page',
                [
                    'label' => __( 'Limit Results (Per page)', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 250,
                    'step' => 1,
                    'default' => 3,
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
                    'label'         => __('Default Listing Order', 'wpdirectorykit'),
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
        $this->add_responsive_control(
                'row_gap',
                [
                    'label' => esc_html__('Rows Gap', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 27,
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

        $this->add_responsive_control(
                'thumbnail_width',
                [
                    'label' => esc_html__('Thumbnail width', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
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
                        '{{WRAPPER}} .wdk-listings-list .listing-item .listing-img-sec' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
                    ],
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
                        'container'  => __( 'Default Sizes', 'wpdirectorykit' ),
                        'cover' => __( 'Image auto crop/resize', 'wpdirectorykit' ),
                    ],
                    'selectors_dictionary' => [
                        'container' => 'object-fit: contain;',
                        'cover' =>  'object-fit: cover;',
                    ],
                    'default' => 'cover',
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listings-list .listing-item .listing-img-sec img' => '{{UNIT}}',
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
                            'min' => 15,
                            'max' => 350,
                        ],
                    ],
                    'size_units' => [ 'px' ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listings-list .listing-item .listing-img-sec img' => 'height: {{SIZE}}{{UNIT}}',
                    ],
                    'separator' => 'after',
                ]
            );
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
                'key'=>'item_box',
                'label'=> esc_html__('Item', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card',
                'options'=>'block',
            ],
            [
                'key'=>'item_image_box',
                'label'=> esc_html__('Image Box', 'wpdirectorykit'),
                'selector'=>'.wdk-listings-list .listing-item .listing-img-sec img',
                'options'=>'block',
            ],
            [
                'key'=>'item_info_box',
                'label'=> esc_html__('Text Box', 'wpdirectorykit'),
                'selector'=>'.wdk-listings-list .listing-item .listing-inf-sec',
                'options'=>'block',
            ],
            [
                'key'=>'item_title',
                'label'=> esc_html__('Title', 'wpdirectorykit'),
                'selector'=>'.wdk-listings-list .listing-item .listing-inf-sec .title',
                'options'=>'full',
            ],
            [
                'key'=>'item_price',
                'label'=> esc_html__('Price', 'wpdirectorykit'),
                'selector'=>'.wdk-listings-list .listing-item .listing-inf-sec .price',
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

            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            $this->end_controls_section();
        }

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-listings-list');
        wp_enqueue_style('wdk-notify');
        wp_enqueue_script('wdk-notify');
    }
}
