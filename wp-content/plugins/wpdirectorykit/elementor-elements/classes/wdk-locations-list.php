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
class WdkLocationsList extends WdkElementorBase {

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
        return 'wdk-locations-list';
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
        return esc_html__('Wdk Location List', 'wpdirectorykit');
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
        return 'eicon-bullet-list';
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

        $this->data['results'] = false;
        
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

            static $results = array();
            $key = $order_by.http_build_query($where).$this->data['settings']['conf_limit'];

            if(!isset($results[$key])) {
                $this->data['results'] = $results[$key] = $this->WMVC->{$controller.'_m'}->get_pagination((!empty($this->data['settings']['conf_limit'])) ? $this->data['settings']['conf_limit'] : NULL, NULL, $where, $order_by);
            } else {
                $this->data['results'] = $results[$key];
            }
            
            if(wmvc_show_data('conf_query_params', $this->data['settings'], false) && !empty($this->data['settings']['conf_query_params'])) {
                $custom_parameters = array();
                $qr_string = trim($this->data['settings']['conf_query_params'],'?');
                $string_par = array();
                parse_str($qr_string, $string_par);
                $custom_parameters += array_map('trim', $string_par);

                $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
                $external_columns = array('location_id', 'category_id', 'post_title');
              
                foreach ($this->data['results'] as $key => $item) {
                    $custom_parameters['search_location'] = wmvc_show_data('idlocation', $item, false);
                    wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters, TRUE);
                    $this->data['results'][$key]->listings_counter = $this->WMVC->listing_m->total(array('is_activated' => 1,'is_approved'=>1));
                }

            }
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode']= true;
      
        echo $this->view('wdk-locations-list', $this->data); 
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
            ]
        );
           
        $this->add_control(
            'conf_query_params',
            [
                'label' => __( 'Query params', 'wpdirectorykit' ),
                'description' => __( 'Put addition query params for locations (counting required memory)', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
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
            'suffix',
            [
                'label' => __( 'Title Suffix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'prefix',
            [
                'label' => __( 'Title Prefix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'show_icon',
            [
                'label' => __( 'Enable Location Icons', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'true',
                'default' => '',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Edit Locations <a href="%1$s" target="_blank"> open </a>', 'wpdirectorykit' ), admin_url('admin.php?page=wdk_location')),
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
                'separator' => 'before',
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
                    'idlocation' => __('Location id', 'wpdirectorykit'),
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
                'default'       => 'desc',
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

        if(true){
            $repeater = new Repeater();
            $repeater->start_controls_tabs( 'locations' );
            $repeater->add_control(
                'location_id',
                [
                    'label' => __( 'ID Location', 'wpdirectorykit' ),
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
    }

    private function generate_controls_styles() {
            $this->start_controls_section(
                'sstyles_thmbn_section',
                [
                    'label' => esc_html__('Main', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
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
                        '{{WRAPPER}} .wdk-locations  .wdk-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $this->end_controls_section();

        $items = [
            [
                'key'=>'item_button',
                'label'=> esc_html__('Item Box', 'wpdirectorykit'),
                'selector'=>'.wdk-locations .wdk-link',
                'selector_hover'=>'.wdk-locations .wdk-link%1$s',
                'options'=>['color','background','border','border_radius','padding','shadow','transition'],
            ],
            [
                'key'=>'item_icon',
                'label'=> esc_html__('Item Icon', 'wpdirectorykit'),
                'selector'=>'.wdk-locations .wdk-link i',
                'selector_hover'=>'.wdk-locations .wdk-link%1$s i',
                'options'=>['margin','color','background','border','border_radius','padding','shadow'],
            ],
            [
                'key'=>'item_icon_tree',
                'label'=> esc_html__('Item Icon', 'wpdirectorykit'),
                'selector'=>'.wdk-locations .wdk-link .wdk-icon',
                'selector_hover'=>'.wdk-locations .wdk-link%1$s .wdk-icon',
                'options'=>['margin','background','border','border_radius','padding','shadow','transition','image_size_control', 'css_filters'],
            ],
            [
                'key'=>'item_title',
                'label'=> esc_html__('Item Title', 'wpdirectorykit'),
                'selector'=>'.wdk-locations .wdk-link .wdk-title',
                'selector_hover'=>'.wdk-locations .wdk-link%1$s .wdk-title',
                'options'=>['margin','typo','color','background','border','border_radius','padding'],
            ],
            [
                'key'=>'item_count',
                'label'=> esc_html__('Item Count', 'wpdirectorykit'),
                'selector'=>'.wdk-locations .wdk-link .wdk-count',
                'selector_hover'=>'.wdk-locations .wdk-link%1$s .wdk-count',
                'options'=>['margin','typo','color','background','border','border_radius','padding'],
            ],
        ];

        foreach ($items as $item) {

            if($item['key'] == 'item_icon_tree') {
                $this->start_controls_section(
                    $item['key'].'_section',
                    [
                        'label' => $item['label'],
                        'tab' =>  Controls_Manager::TAB_STYLE,
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'show_icon',
                                    'operator' => '==',
                                    'value' => 'true',
                                ]
                            ],
                        ],
                    ]
                );
            } elseif($item['key'] == 'item_icon') {
                $this->start_controls_section(
                    $item['key'].'_section',
                    [
                        'label' => $item['label'],
                        'tab' =>  Controls_Manager::TAB_STYLE,
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'show_icon',
                                    'operator' => '!=',
                                    'value' => 'true',
                                ]
                            ],
                        ],
                    ]
                );
            } else {
                $this->start_controls_section(
                    $item['key'].'_section',
                    [
                        'label' => $item['label'],
                        'tab' =>  Controls_Manager::TAB_STYLE,
                        ]
                );
            }

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

            /* special for some elements */
            if($item['key'] == 'item_icon') {
                $this->add_control(
                    $item['key'].'_i',
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
                $this->add_responsive_control(
                    $item['key'].'_size',
                    [
                        'label' => __( 'Size', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px'],
                        'range' => [
                            'px' => [
                                'min' => 1,
                                'max' => 60,
                                'step' => 1,
                            ],
                        ],
                        'default' => [
                            'unit' => 'px',
                            'size' => 14,
                        ],
                        'selectors' => [
                            '{{WRAPPER}} '.$item['selector'] => 'font-size: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

            }
            if($item['key'] == 'item_count') {
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
                        
                        ],
                        'render_type' => 'ui',
                        'selectors_dictionary' => [
                            'left' => 'text-align: left;',
                            'center' => 'text-alig: center;',
                            'right' => 'text-align: right;',
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

    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-locations-list');
        
    }
}
