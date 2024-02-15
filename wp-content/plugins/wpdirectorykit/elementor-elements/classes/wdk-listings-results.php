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
class WdkListingsResults extends WdkElementorBase {

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
        return 'wdk-listings-results';
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
        return esc_html__('Wdk Listings Results', 'wpdirectorykit');
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
        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        $this->data['listings_count'] = 0;
        $this->data['results'] = array();
        $this->data['pagination_output'] = '';

        $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
        $external_columns = array('location_id', 'category_id', 'post_title');
        $custom_parameters = array();
        $skip_postget = FALSE;

        $custom_parameters['order_by'] = $this->WMVC->db->prefix.'wdk_listings.rank DESC';

        $this->data['custom_order'] =  array();
        if($this->data['settings']['custom_order_list']) {
            foreach($this->data['settings']['custom_order_list'] as $item){
                if(empty($item['title']) || empty($item['key'])) continue;
                $this->data['custom_order'][] = array('title'=>$item['title'],'key'=>$item['key']);
            }
        }

        if(!empty($this->data['settings']['specific_user'])) {
            $custom_parameters['search_agents_ids'] = $this->data['settings']['specific_user'];
        }
        
        if($this->data['settings']['conf_results_type'] == 'results_listings') {
            $controller = 'listing';
            $offset = NULL;

            if($this->data['settings']['get_filters_enable'] != 'yes')
                $skip_postget = TRUE;

            if(!isset($_GET['order_by'])) {
                /* if detected custom field for order */
                if(!empty($this->data['settings']['conf_order_by_custom'])) {
                    $custom_parameters['order_by'] .= ', '.$this->data['settings']['conf_order_by_custom'].' '.$this->data['settings']['conf_order'];
                } else {
                    $custom_parameters['order_by'] .= ', '.$this->data['settings']['conf_order_by'].' '.$this->data['settings']['conf_order'];
                }
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

            if($this->data['settings']['conf_pagination_enable'] == 'yes' && $this->data['settings']['get_filters_enable'] == 'yes') {
                
                wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters, $skip_postget);
                $total_items = $this->WMVC->listing_m->total(array('is_activated' => 1,'is_approved'=>1));
                $current_page = 1;
                if(isset($_GET['wmvc_paged']))
                    $current_page = intval(wmvc_xss_clean($_GET['wmvc_paged']));

                if(empty($this->data['settings']['per_page']))
                    $this->data['settings']['per_page'] = 6;
                    
                $offset = $this->data['settings']['per_page']*($current_page-1);
                
                if(function_exists('wdk_wp_frontend_paginate') && $total_items > $this->data['settings']['per_page'])
                    $this->data['pagination_output'] = wdk_wp_frontend_paginate($total_items, $this->data['settings']['per_page'], 'wmvc_paged', array(), TRUE,
                                                                                FALSE, FALSE, $this->data['settings']['limit_pagination']);

                $this->data['listings_count'] = $total_items;
            } else {
                wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters, $skip_postget);
                $total_items = $this->WMVC->listing_m->total(array('is_activated' => 1,'is_approved'=>1));

                $this->data['listings_count'] = $total_items;
            }

            wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters, $skip_postget);

            $this->data['results'] = $this->WMVC->listing_m->get_pagination($this->data['settings']['per_page'], $offset, array('is_activated' => 1,'is_approved'=>1));

            /*, array('sw_listing.rank DESC')*/

        } else if($this->data['settings']['conf_results_type'] == 'custom_listings') {
            $listings_ids = array();
            foreach($this->data['settings']['conf_custom_results'] as $listing) {
                if(isset($listing['listing_post_id']) && !empty($listing['listing_post_id'])) {
                    $listings_ids [] = $listing['listing_post_id'];
                }
            }
            
            if($this->data['settings']['layout_type'] != 'carousel' && $this->data['settings']['custom_listings_enable_pagination'] == 'yes' && !empty($this->data['settings']['custom_listings_per_page'])) {
                
                $this->data['listings_count'] = $total_items = 0;

                if(!empty($listings_ids))
                {
                    $this->WMVC->db->where( $this->WMVC->db->prefix.'wdk_listings.post_id IN(' . implode(',', $listings_ids) . ')', null, false);
                    $total_items = $this->WMVC->listing_m->total(array('is_activated' => 1,'is_approved'=>1));
                    $this->data['listings_count'] = $total_items;
                }

                $current_page = 1;
                if(isset($_GET['wmvc_paged']))
                    $current_page = intval(wmvc_xss_clean($_GET['wmvc_paged']));

                $limit = $this->data['settings']['custom_listings_per_page'];
                    
                $offset = $limit *($current_page-1);

                if(function_exists('wdk_wp_frontend_paginate'))
                    $this->data['pagination_output'] = wdk_wp_frontend_paginate($total_items, $limit, 'wmvc_paged', array(), TRUE, FALSE, FALSE, 
                                                                                $this->data['settings']['limit_pagination']);

                $this->WMVC->db->limit($limit);
                $this->WMVC->db->offset($offset);
            }

            /* where in */
            if(!empty($listings_ids)){
                $this->WMVC->db->where( $this->WMVC->db->prefix.'wdk_listings.post_id IN(' . implode(',', $listings_ids) . ')', null, false);
                $this->WMVC->db->where(array('is_activated' => 1, 'is_approved'=>1));
                $this->WMVC->db->order_by('FIELD('.$this->WMVC->db->prefix.'wdk_listings.post_id, '. implode(',', $listings_ids) . ')');

                if($this->data['settings']['conf_pagination_enable'] == 'yes' && !empty($this->data['settings']['per_page'])) {


                }
                $this->data['results'] = $this->WMVC->listing_m->get();
            }
            
            /* hide filter header on specific results */
            $this->data['settings']['get_filters_enable'] = '';
        }
        
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

        echo $this->view('wdk-listings-results', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => 'tab_conf',
            ]
        );

        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_ajax_results')){
            $this->add_control(
                'is_ajax_enable',
                [
                    'label' => __( 'Enable Reload content with ajax', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'True', 'wpdirectorykit' ),
                    'label_off' => __( 'False', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                    
                ]
            );
        }

        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_card_elementor_layout')){
            $this->add_control(
                'is_custom_layout_enable',
                [
                    'label' => __( 'Enable Custom Layout', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'True', 'wpdirectorykit' ),
                    'label_off' => __( 'False', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                    
                ]
            );
            $this->add_control(
                'custom_layout_id_grid',
                [
                    'label' => __( 'Layout Grid id', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                    'placeholder' => __( 'put your template id', 'wpdirectorykit' ),
                    'description' => __( 'Create layout here', 'wpdirectorykit' ).' '.wdk_sprintf(__('%1$s here %2$s','wpdirectorykit'),'<a target="_blank" href="'.admin_url('edit.php?post_type=elementor_library#add_new').'">','</a>'),
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'is_custom_layout_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control (
                'custom_layout_id_list',
                [
                    'label' => __( 'Layout List Id', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                    'placeholder' => __( 'put your template id', 'wpdirectorykit' ),
                    'description' => __( 'Create layout here', 'wpdirectorykit' ).' '.wdk_sprintf(__('%1$s here %2$s','wpdirectorykit'),'<a target="_blank" href="'.admin_url('edit.php?post_type=elementor_library#add_new').'">','</a>'),
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'is_custom_layout_enable',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );
        } 


        $this->add_control(
            'specific_user_hr',
            [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
      
        $this->add_control(
			'is_mobile_view_enable',
			[
				'label' => __( 'Horizontal mobile view', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
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

        $this->add_control(
			'get_filters_enable',
			[
				'label' => __( 'Filtering based on URL enable', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => 'yes',
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
			'show_numbers_results_enable',
			[
				'label' => __( 'Show number of results Enable', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => 'yes',
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
			'view_type_enable',
			[
				'label' => __( 'View Type Grid/List Enable', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => 'yes',
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
			'sorting_enable',
			[
				'label' => __( 'Sorting Filter Enable', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => 'yes',
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
            'conf_pagination_enable',
            [
                'label' => __( 'Pagination Enable', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'wpdirectorykit' ),
                'label_off' => __( 'Hide', 'wpdirectorykit' ),
                'return_value' => 'yes',
                'default' => '',
                'description' => esc_html__( 'Filtering based on URL must be enabled for this functionality', 'wpdirectorykit' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'conf_results_type',
                            'operator' => '==',
                            'value' => 'results_listings',
                        ],/*
                        [
                            'name' => 'get_filters_enable',
                            'operator' => '==',
                            'value' => 'yes',
                        ]*/
                    ],
                ]
            ]
        );

        $this->add_control(
            'limit_pagination',
            [
                'label'         => __('Limit Pagination Items', 'wpdirectorykit'),
                'type'          => Controls_Manager::SELECT,
                'label_block'   => true,
                'options'       => [
                    '1'  => __('3 numbers', 'wpdirectorykit'),
                    '2'    => __('5 numbers', 'wpdirectorykit'),
                    '3' => __('7 numbers', 'wpdirectorykit'),
                ],
                'default' => '1',
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
                                            '<br> field_6_min=100&field_6_max=200&field_5=rent&is_featured=on&search_category=3&search_location=4&search_agents_ids=3'.
                                            '<br> Listings from date - field_post_date_min=2023-05-07'.
                                            '<br> Listings from latest 365 days - field_post_date_min=-365 days'.
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

            
            $dbusers =  get_users( array( 'search' => '',
                    'orderby' => 'display_name', 'order' => 'ASC', 'role__in' => [ 'administrator', 'super-admin','wdk_agent','wdk_agency']));

            $users = array('0'=>esc_html__('Not Selected', 'wpdirectorykit'));
            foreach($dbusers as $dbuser) {
                $users[wmvc_show_data('ID', $dbuser)] = '#'.wmvc_show_data('ID', $dbuser).', '.wmvc_show_data('display_name', $dbuser);
            }

            $this->add_control(
                'specific_user',
                [
                'label' => __( 'Filter by Agent', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '0',
                    'label_block' => true,
                    'options' => $users,
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
                    'label'         => __('Default Custom Sort By', 'wpdirectorykit'),
                    'description'         => __('Custom Sort By', 'wpdirectorykit'),
                    'description' => '<span style="word-break: break-all;">'.__( 'Example:', 'wpdirectorykit' ).
                                        '<br> RAND()  - return random results'.
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
            
            $this->add_control(
                'custom_listings_enable_pagination',
                [
                    'label' => __( 'Enable Pagination', 'wpdirectorykit' ),
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
                                'value' => 'custom_listings',
                            ]
                        ],
                    ],
                ]
            );

            $this->add_control(
                'custom_listings_per_page',
                [
                    'label' => __( 'Limit Listings (Per page)', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 50,
                    'step' => 1,
                    'default' => 6,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_results_type',
                                'operator' => '==',
                                'value' => 'custom_listings',
                            ],
                            [
                                'name' => 'custom_listings_enable_pagination',
                                'operator' => '==',
                                'value' => 'yes',
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


        if(wdk_get_option('wdk_sub_listings_enable')) {

            $this->start_controls_section(
                'tab_conf_main_section_subslistings',
                [
                    'label' => esc_html__('Related Listings', 'wpdirectorykit'),
                    'tab' => 'tab_conf',
                ]
            );

            $fields_data = wdk_cached_field_get();
            $fields_list = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
            $order_i = 0;
    
            $fields_list [(++$order_i).'__section'] = esc_html__('-- Section Custom fields --', 'wpdirectorykit');
            $fields_list [(++$order_i).'__idlisting'] = esc_html__('Id listing', 'wpdirectorykit');
            $fields_list [(++$order_i).'__post_id'] = esc_html__('Post Id', 'wpdirectorykit');
            $fields_list [(++$order_i).'__counter_views'] = esc_html__('Views counter', 'wpdirectorykit');
            $fields_list [(++$order_i).'__lat'] = esc_html__('Gps Lat', 'wpdirectorykit');
            $fields_list [(++$order_i).'__lng'] = esc_html__('Gps Lng', 'wpdirectorykit');
            $fields_list [(++$order_i).'__date'] = esc_html__('Date', 'wpdirectorykit');
            $fields_list [(++$order_i).'__date_modified'] = esc_html__('Date Modified', 'wpdirectorykit');
            $fields_list [(++$order_i).'__post_title'] = esc_html__('WP Title', 'wpdirectorykit');
            $fields_list [(++$order_i).'__post_content'] = esc_html__('WP Content', 'wpdirectorykit');
            $fields_list [(++$order_i).'__address'] = esc_html__('Address', 'wpdirectorykit');
            $fields_list [(++$order_i).'__category_id'] = esc_html__('Category', 'wpdirectorykit');
            $fields_list [(++$order_i).'__location_id'] = esc_html__('Location', 'wpdirectorykit');
    
            foreach($fields_data as $field)
            {
                if(wmvc_show_data('field_type', $field) == 'SECTION') {
                    $fields_list [(++$order_i).'section__'.wmvc_show_data('idfield', $field)] = '-- '.esc_html__('Section', 'wpdirectorykit').' '.wmvc_show_data('field_label', $field).' --';
                } else {
                    $fields_list[(++$order_i).'__'.wmvc_show_data('idfield', $field)] = '#'.wmvc_show_data('idfield', $field).' '.wmvc_show_data('field_label', $field).'['.wmvc_show_data('field_type', $field).']';
                }
            }
            
            $this->add_responsive_control(
                'related_fields_header',
                [
                    'label' => esc_html__('Fields', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'related_fields_header_hr',
                [
                        'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $repeater_sublistings_fields = new Repeater();
            $repeater_sublistings_fields->start_controls_tabs( 'related_fields' );
            $repeater_sublistings_fields->add_control(
                'field',
                [
                    'label' => __( 'Field', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $fields_list,
                ]
            );
            
            $repeater_sublistings_fields->add_control(
                'is_stars',
                [
                    'label' => __( 'Show like stars', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                    'description' => esc_html__( 'Numeric values required', 'wpdirectorykit' ),
                ]
            );

            $repeater_sublistings_fields->add_control(
                'is_link',
                [
                    'label' => __( 'Set field like link', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );

            $repeater_sublistings_fields->end_controls_tabs();

            $this->add_control(
                'related_fields_list',
                [
                    'label' => esc_html__('Fields For Query Similar Listings', 'wpdirectorykit'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater_sublistings_fields->get_controls(),
                    'default' => [
                        [
                            'field' => '63',
                            'is_stars' => 'yes',
                            'is_link' => '',
                        ],
                        [
                            'field' => 'post_title',
                            'is_link' => 'yes',
                        ],
                        [
                            'field' => '64',
                            'is_link' => '',
                        ],
                    ],
                    'title_field' => '{{{ field }}}',
                ]
            );

            $this->add_control(
                'related_fields_header_hr2',
                [
                        'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $this->add_responsive_control(
                'related_fields_border',
                [
                    'label' => esc_html__('Line Bottom', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                ]
            );
            
            $this->add_control(
                'related_fields_border_width',
                [
                    'label' => __( 'Speed', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 10,
                    'step' => 1,
                    'default' => 1,
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listing-card .wdk-sublistings-part ul > li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'related_fields_border_color',
                [
                        'label' => esc_html__( 'Color', 'wpdirectorykit' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .wdk-listing-card .wdk-sublistings-part ul > li:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
                        ],
                ]
            );

            $this->add_responsive_control(
                'related_fields_styles',
                [
                    'label' => esc_html__('Styles', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                ]
            );
            
            $this->add_control(
                'related_fields_header_hr3',
                [
                        'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-listing-card .wdk-sublistings-part a, {{WRAPPER}} .wdk-listing-card .wdk-sublistings-part span',
                'hover'=>'{{WRAPPER}} .wdk-listing-card .wdk-sublistings-part a%1$s'
            );
            $this->generate_renders_tabs($selectors, 'related_fields_dynamic', ['color','typo','padding','shadow','transition']);

            $this->add_control(
                'related_fields_part_styles',
                [
                    'label' => esc_html__('Card Part', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                ]
            );
            
            $this->add_control(
                'related_fields_header_hr4',
                [
                        'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $selectors = array(
                'normal' => '{{WRAPPER}} .wdk-listing-card .wdk-sublistings-part',
            );
            $this->generate_renders_tabs($selectors, 'related_fields_part_dynamic', ['margin','background','border','border_radius','padding','shadow','transition']);

            $this->add_control(
                'related_fields_star_styles',
                [
                    'label' => esc_html__('Stars', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                ]
            );
            
            $this->add_control(
                'related_fields_header_hr5',
                [
                        'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );

            $selectors = array(
                'active' => '{{WRAPPER}} .wdk-listing-card .wdk-sublistings-part .stars-lst span i.star-active',
                'deactive' => '{{WRAPPER}} .wdk-listing-card .wdk-sublistings-part .stars-lst span i.innactive',
            );
            $this->generate_renders_tabs($selectors, 'related_fields_star_dynamic', ['margin','padding','font-size', 'color']);

            $this->end_controls_section();  
        }
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

        
        $this->add_control(
            'enable_separed_styles',
            [
                'label' => __( 'Different styles for grid/list', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'true',
                'default' => '',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'layout_type',
                            'operator' => '==',
                            'value' => 'grid',
                        ],
                        [
                            'name' => 'layout_type',
                            'operator' => '==',
                            'value' => 'list',
                        ]
                    ],
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
                        'default' => 'calc(100% / 3)', 
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
                            ],
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
                        'size' => 270,
                        'unit' => 'px',
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

        $this->add_responsive_control(
            'thumbn_slider_h',
            [
                'label' => esc_html__('Thumbnail Slider', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'thumbn_slider_arrow_left',
            [
                'label' => esc_html__('Icon Left', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        $this->add_responsive_control(
            'thumbn_slider_arrow_right',
            [
                'label' => esc_html__('Icon Right', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
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

        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_card_elementor_layout')){
            $this->start_controls_section(
                'sstyles_thmbn_section',
                [
                    'label' => esc_html__('Section Image', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'is_custom_layout_enable',
                                'operator' => '!=',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );
        } else {
            $this->start_controls_section(
                'sstyles_thmbn_section',
                [
                    'label' => esc_html__('Section Image', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
        }

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
                    'label' => esc_html__('Height Grid View', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 50,
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
                        'size' => 350,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listings-results.wdk_size_image_cover .wdk-listing-card:not(.list) .wdk-thumbnail .wdk-image' => 'height: {{SIZE}}{{UNIT}}',
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
                    ]
                ]
            );

            if(false)
            $this->add_responsive_control(
                'styles_thmbn_des_height_list',
                [
                    'label' => esc_html__('Height List view', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 50,
                            'max' => 1500,
                        ],
                        'vw' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'size_units' => [ 'px', 'vw'],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listings-results.wdk_size_image_cover .wdk-listing-card.list .wdk-thumbnail' => 'height: {{SIZE}}{{UNIT}}',
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
            $this->generate_renders_tabs($selectors, 'styles_carousel_arrows_dynamic', ['margin','color','background','border','border_radius','padding','shadow','transition','font-size','hover_animation']);

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
                'hover'=>'{{WRAPPER}} .wdk_results_listings_slider_box .slick-dots li .wdk_lr_dot%1$s',
                'active'=>'{{WRAPPER}} .wdk_results_listings_slider_box .slick-dots li.slick-active .wdk_lr_dot'
            );

            $this->generate_renders_tabs($selectors, 'styles_carousel_dots_dynamic', ['margin','color','background','border','border_radius','padding','shadow','transition','font-size','hover_animation']);

        $this->end_controls_section();
    }

    private function generate_controls_content() {
        
        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_card_elementor_layout')){
            $this->start_controls_section(
                'content_thumbnail_section',
                [
                    'label' => esc_html__('Colors', 'wpdirectorykit'),
                    'tab' => '1',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'is_custom_layout_enable',
                                'operator' => '!=',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );
        } else {
            $this->start_controls_section(
                'content_thumbnail_section',
                [
                    'label' => esc_html__('Colors', 'wpdirectorykit'),
                    'tab' => '1',
                ]
            ); 
        }

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
                'selector'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card',
                'options'=>'full',
            ],
            [
                'key'=>'content_label',
                'label'=> esc_html__('Over Image Top', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card .wdk-thumbnail .wdk-over-image-top span',
                'options'=>'full',
            ],
            [
                'key'=>'content_type',
                'label'=> esc_html__('Over Image Bottom', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card .wdk-thumbnail .wdk-over-image-bottom',
                'is_featured'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card.is_featured .wdk-thumbnail .wdk-over-image-bottom',
                'options'=>'full',
            ],
            [
                'key'=>'content_title',
                'label'=> esc_html__('Title Part', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card .wdk-title .title',
                'options'=>'full',
            ],
            [
                'key'=>'content_description',
                'label'=> esc_html__('Subtitle part', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card .wdk-subtitle-part',
                'options'=>'full',
            ],
            [
                'key'=>'content_items',
                'label'=> esc_html__('Features part', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card .wdk-features-part span',
                'options'=>'full',
            ],
            [
                'key'=>'wdk-divider',
                'label'=> esc_html__('Divider', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card .wdk-divider',
                'options'=>'full',
            ],
            [
                'key'=>'content_price',
                'label'=> esc_html__('Pricing part', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card .wdk-footer .wdk-price',
                'options'=>'full',
            ],
            [
                'key'=>'content_button',
                'label'=> esc_html__('Button Open', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listings-results:not(.enable_separed_styles) .wdk-listing-card .wdk-footer .wdk-btn',
                'options'=>['margin','align','typo','color','background_group','border','border_radius','padding','shadow','transition'],
            ],
        ];


        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_card_elementor_layout')){
            $this->_generate_options_card($items,[
                'terms' => [
                    [
                        'name' => 'enable_separed_styles',
                        'operator' => '!=',
                        'value' => 'true',
                    ], [
                        'name' => 'is_custom_layout_enable',
                        'operator' => '!=',
                        'value' => 'yes',
                    ]
                ],
            ]);
        } else {
            $this->_generate_options_card($items,[
                'terms' => [
                    [
                        'name' => 'enable_separed_styles',
                        'operator' => '!=',
                        'value' => 'true',
                    ]
                ],
            ]);
        }




        $items = [
            [
                'key'=>'content_card_grid',
                'label'=> esc_html__('Card (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid',
                'options'=>'full',
            ],
            [
                'key'=>'content_label_grid',
                'label'=> esc_html__('Over Image Top (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid .wdk-thumbnail .wdk-over-image-top span',
                'options'=>'full',
            ],
            [
                'key'=>'content_type_grid',
                'label'=> esc_html__('Over Image Bottom (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid .wdk-thumbnail .wdk-over-image-bottom',
                'is_featured'=>'.wdk-element .wdk-listing-card.grid.is_featured .wdk-thumbnail .wdk-over-image-bottom',
                'options'=>'full',
            ],
            [
                'key'=>'content_title_grid',
                'label'=> esc_html__('Title Part (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid .wdk-title .title',
                'options'=>'full',
            ],
            [
                'key'=>'content_description_grid',
                'label'=> esc_html__('Subtitle part (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid .wdk-subtitle-part',
                'options'=>'full',
            ],
            [
                'key'=>'content_items_grid',
                'label'=> esc_html__('Features part (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid .wdk-features-part span',
                'options'=>'full',
            ],
            [
                'key'=>'wdk-divider_grid',
                'label'=> esc_html__('Divider (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid .wdk-divider',
                'options'=>'full',
            ],
            [
                'key'=>'content_price_grid',
                'label'=> esc_html__('Pricing part (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid .wdk-footer .wdk-price',
                'options'=>'full',
            ],
            [
                'key'=>'content_button_grid',
                'label'=> esc_html__('Button Open (Grid)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.grid .wdk-footer .wdk-btn',
                'options'=>['margin','align','typo','color','background_group','border','border_radius','padding','shadow','transition'],
            ],
        ];

        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_card_elementor_layout')){
            
            $this->_generate_options_card($items,[
                'terms' => [
                    [
                        'name' => 'enable_separed_styles',
                        'operator' => '==',
                        'value' => 'true',
                    ], [
                        'name' => 'is_custom_layout_enable',
                        'operator' => '!=',
                        'value' => 'yes',
                    ]
                ],
            ],'.grid');
        } else {

            $this->_generate_options_card($items,[
                'terms' => [
                    [
                        'name' => 'enable_separed_styles',
                        'operator' => '==',
                        'value' => 'true',
                    ]
                ],
            ],'.grid');
        }

        $items = [
            [
                'key'=>'content_card_list',
                'label'=> esc_html__('Card (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list',
                'options'=>'full',
            ],
            [
                'key'=>'content_label_list',
                'label'=> esc_html__('Over Image Top (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list .wdk-thumbnail .wdk-over-image-top span',
                'options'=>'full',
            ],
            [
                'key'=>'content_type_list',
                'label'=> esc_html__('Over Image Bottom (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list .wdk-thumbnail .wdk-over-image-bottom',
                'is_featured'=>'.wdk-element .wdk-listing-card.list.is_featured .wdk-thumbnail .wdk-over-image-bottom',
                'options'=>'full',
            ],
            [
                'key'=>'content_title_list',
                'label'=> esc_html__('Title Part (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list .wdk-title .title',
                'options'=>'full',
            ],
            [
                'key'=>'content_description_list',
                'label'=> esc_html__('Subtitle part (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list .wdk-subtitle-part',
                'options'=>'full',
            ],
            [
                'key'=>'content_items_list',
                'label'=> esc_html__('Features part (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list .wdk-features-part span',
                'options'=>'full',
            ],
            [
                'key'=>'wdk-divider_list',
                'label'=> esc_html__('Divider (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list .wdk-divider',
                'options'=>'full',
            ],
            [
                'key'=>'content_price_list',
                'label'=> esc_html__('Pricing part (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list .wdk-footer .wdk-price',
                'options'=>'full',
            ],
            [
                'key'=>'content_button_list',
                'label'=> esc_html__('Button Open (List)', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card.list .wdk-footer .wdk-btn',
                'options'=>['margin','align','typo','color','background_group','border','border_radius','padding','shadow','transition'],
            ],
        ];

        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_card_elementor_layout')){
            $this->_generate_options_card($items,[
                'terms' => [
                    [
                        'name' => 'enable_separed_styles',
                        'operator' => '==',
                        'value' => 'true',
                    ], [
                        'name' => 'is_custom_layout_enable',
                        'operator' => '!=',
                        'value' => 'yes',
                    ]
                ],
            ],'.list');
        } else {
            $this->_generate_options_card($items,[
                'terms' => [
                    [
                        'name' => 'enable_separed_styles',
                        'operator' => '==',
                        'value' => 'true',
                    ]
                ],
            ],'.list');
        }



        $this->start_controls_section(
            'pagination_styles',
            [
                'label' => esc_html__('Pagination Section', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );
        $this->add_responsive_control(
            'pagination_styles_align',
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
                ],
                'render_type' => 'ui',
                'selectors_dictionary' => [
                    'left' => 'justify-content: flex-start;',
                    'center' => 'justify-content: center;',
                    'right' => 'justify-content: flex-end;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-pagination.pagination' => '{{VALUE}};',
                ],
            ]
        );

        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-pagination.pagination',
        );

        $this->generate_renders_tabs($selectors, 'pagination_styles_dynamic', 'block', ['align']);
        
        $this->add_control(
            'pagination_styles_head',
                [
                    'label' => esc_html__('Pagination Links', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );

        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-pagination.pagination .nav-links > *',
            'hover'=>'{{WRAPPER}} .wdk-pagination.pagination .nav-links > *%1$s',
            'active'=>'{{WRAPPER}} .wdk-pagination.pagination .nav-links > *.current'
        );
        $this->generate_renders_tabs($selectors, 'pagination_styles_items_dynamic', ['margin','typo','color','background','border','border_radius','padding','shadow','transition', 'width','height']);
        
        $this->end_controls_section();

        $items = [
            /* header filter */
            [
                'key'=>'filter_section_box',
                'label'=> esc_html__('Filter Box', 'wpdirectorykit'),
                'selector'=>'.wdk-filter-head',
                'options'=> ['margin','background','border','border_radius','padding','shadow'],
            ],
            [
                'key'=>'filter_group_box',
                'label'=> esc_html__('Group Box', 'wpdirectorykit'),
                'selector'=>'.wdk-filter-head .filter-group',
                'options'=>['margin','background','border','border_radius','padding','shadow'],
            ],
            [
                'key'=>'filter_group_box_order_select',
                'label'=> esc_html__('Select Order', 'wpdirectorykit'),
                'selector'=>'.wdk-filter-head .filter-group .wdk-order',
                'options'=>'full',
            ],
            [
                'key'=>'filter_group_box_order',
                'label'=> esc_html__('Group Box View Type', 'wpdirectorykit'),
                'selector'=>'.wdk-filter-head .filter-group.wmvc-view-type',
                'options'=>false,
            ],
            [
                'key'=>'filter_group_box_view',
                'label'=> esc_html__('View Type Icons', 'wpdirectorykit'),
                'selector'=>'.wdk-filter-head .filter-group.wmvc-view-type a',
                'hover_enable' => true,
                'options'=>['align','font-size','color','background','border','border_radius','padding','shadow','transition','margin','width','height'],
            ],
            [
                'key'=>'filter_group_box_filter-status',
                'label'=> esc_html__('Listings Count', 'wpdirectorykit'),
                'selector'=>'.wdk-filter-head .filter-group.filter-status',
                'options'=>'text',
            ],
        ];

        $this->start_controls_section(
            'filter_section',
            [
                'label' =>  esc_html__( 'Filter Header', 'wpdirectorykit' ),
                'tab' => '1',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'get_filters_enable',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );
        foreach ($items as $item) {
            $this->add_control(
                $item['key'].'_head',
                    [
                        'label' => $item['label'],
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
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
            
            if($item['options']){
                $selectors = array(
                    'normal' => '{{WRAPPER}} '.$item['selector']
                );
                if(isset($item['hover_enable']) && $item['hover_enable'])    
                    $selectors['hover'] = '{{WRAPPER}} '.$item['selector'].'%1$s';

                    
                if( $item['key'] == 'filter_group_box_view')    
                    $selectors['active'] = '{{WRAPPER}} '.$item['selector'].'.active';

                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);
            }
            /* special for some elements */
            if($item['key'] == 'filter_group_box_order') {
                $this->add_responsive_control(
                    $item['key'].'_align',
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
                            '{{WRAPPER}} '.$item['selector'] => '{{VALUE}};',
                        ],
                    ]
                );
            }
            if($item['key'] == 'filter_group_box') {
                $this->add_control(
                    $item['key'].'_icon',
                    [
                        'label' => esc_html__('Icon Order', 'wpdirectorykit'),
                        'type' => Controls_Manager::ICONS,
                        'label_block' => true,
                        'default' => [
                            'value' => 'fa fa-filter',
                            'library' => 'solid',
                        ],
                    ]
                );
                $selectors = array(
                    'normal' => '{{WRAPPER}} '.$item['selector'].' i',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_icon_dynamic', ['margin']);
            }
            /* END special for some elements */
        }
        $this->end_controls_section();
    }

    private function _generate_options_card($items, $conditions = NULL, $prefix = ''){
        
        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => '1',
                    'conditions' => $conditions
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

            $this->add_responsive_control(
                $item['key'].'_min-height',
               [
                   'label' => esc_html__('Min Height', 'wpdirectorykit'),
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
                        '{{WRAPPER}} '.$item['selector'] => 'min-height: {{SIZE}}{{UNIT}}',
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
            if (strpos($item['key'], 'content_description') !== FALSE ) {

                $key_option = $item['key'].'content_description_limit';
                if($item['key'] == 'content_description_limit') {
                    $key_option = 'content_description_limit';
                }

                $this->add_control(
                    $key_option,
                    [
                        'label' => __( 'Limit Line (per field)', 'wpdirectorykit' ),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                        'default' => 3, 
                        'selectors' => [
                            '{{WRAPPER}} .wdk-listing-card'.$prefix.' .wdk-subtitle-part span' => '-webkit-line-clamp: {{VALUE}};',
                        ],
                    ]
                );
            }

            if(strpos($item['key'], 'content_button') !== FALSE) {

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
                }

                $selectors = array(
                    'normal' => '{{WRAPPER}} '.$item['selector'].' i',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_icon_dynamic', ['margin']);
            }

            if(strpos($item['key'], 'content_label') !== FALSE) {
                $this->add_control(
                    $item['key'].'_parent_head',
                    [
                        'label' => esc_html__('Parent Box', 'wpdirectorykit'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-element .wdk-listing-card'.$prefix.' .wdk-over-image-top',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_parent_dynamic', ['margin']);
            }

            if(strpos($item['key'], 'content_items') !== FALSE) {
                $this->add_control(
                    $item['key'].'_parent_head',
                    [
                        'label' => esc_html__('Parent Box', 'wpdirectorykit'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-element .wdk-listing-card'.$prefix.' .wdk-features-part',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_parent_dynamic', ['margin','align']);
            }

            if(strpos($item['key'], 'content_label') !== FALSE) {
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
                            '{{WRAPPER}} .wdk-listing-card'.$prefix.' .wdk-thumbnail .wdk-over-image-top' => '{{VALUE}};',
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
                            '{{WRAPPER}} .wdk-listing-card'.$prefix.' .wdk-thumbnail .wdk-over-image-top' => '{{VALUE}};',
                        ],
                    ]
                );
            }

            if(strpos($item['key'], 'content_type') !== FALSE) {
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

        if( wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_ajax_results')) {
            wp_enqueue_script( 'wdk-ajax-loading-listings');
        }
    }
}
