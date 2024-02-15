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
class WdkCategoriesTreeTop extends WdkElementorBase {

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
        return 'wdk-categories-tree-top';
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
        return esc_html__('Wdk Categories Tree Top', 'wpdirectorykit');
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
        return 'eicon-sitemap';
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
        $this->WMVC->model('category_m');
        
        $this->data['categories_primary'] = array();
        $this->data['categories_secondary'] = array();
        
        /* primary */
        if($this->data['settings']['primary_conf_results_type'] == 'custom_categories') {
            $categories_ids = array();
            foreach($this->data['settings']['primary_conf_custom_results'] as $category) {
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
                $this->data['categories_primary'] = $this->WMVC->{$controller.'_m'}->get();
            }
        } else {
            $where = array();
            if ($this->data['settings']['primary_root_enable'] == 'yes' && $this->data['settings']['primary_sub_enable'] != 'yes') {
                $where['('.$this->WMVC->{$controller.'_m'}->_table_name.'.level = 0)'] = NULL;
            } elseif ($this->data['settings']['primary_root_enable'] != 'yes' && $this->data['settings']['primary_sub_enable'] == 'yes') {
                $where['('.$this->WMVC->{$controller.'_m'}->_table_name.'.level != 0)'] = NULL;
            }

            $order_by = NULL;
            if($this->data['settings']['primary_conf_order_by'] == 'order_most') {
                /* get category with most listings */
                $order_by = 'listings_counter '.$this->data['settings']['primary_conf_order'];
            } else if (!empty($this->data['settings']['primary_conf_order_by'])) {
                $order_by = $this->data['settings']['primary_conf_order_by'].' '.$this->data['settings']['primary_conf_order'];
            }

            $this->data['categories_primary'] = $this->WMVC->{$controller.'_m'}->get_pagination((!empty($this->data['settings']['primary_conf_limit'])) ? $this->data['settings']['primary_conf_limit'] : NULL, $this->data['settings']['primary_conf_offset'], $where, $order_by);
        }
        
        /* secondary */
        if($this->data['settings']['secondary_categories_enable'] == 'yes')
            if($this->data['settings']['secondary_conf_results_type'] == 'custom_categories') {
                $categories_ids = array();
                foreach($this->data['settings']['secondary_conf_custom_results'] as $category) {
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
                    $this->WMVC->db->group_by($this->WMVC->{$controller.'_m'}->_secondary_key);
                    $this->data['categories_secondary'] = $this->WMVC->{$controller.'_m'}->get();
                }
            } else {
                $where = array();
                if ($this->data['settings']['secondary_root_enable'] == 'yes' && $this->data['settings']['secondary_sub_enable'] != 'yes') {
                    $where['('.$this->WMVC->{$controller.'_m'}->_table_name.'.level = 0)'] = NULL;
                } elseif ($this->data['settings']['secondary_root_enable'] != 'yes' && $this->data['settings']['secondary_sub_enable'] == 'yes') {
                    $where['('.$this->WMVC->{$controller.'_m'}->_table_name.'.level != 0)'] = NULL;
                }

                $order_by = NULL;
                if($this->data['settings']['secondary_conf_order_by'] == 'order_most') {
                    /* get category with most listings */
                    $order_by = 'listings_counter '.$this->data['settings']['secondary_conf_order'];
                } else if (!empty($this->data['settings']['secondary_conf_order_by'])) {
                    $order_by = $this->data['settings']['secondary_conf_order_by'].' '.$this->data['settings']['secondary_conf_order'];
                }

                $this->data['categories_secondary'] = $this->WMVC->{$controller.'_m'}->get_pagination((!empty($this->data['settings']['secondary_conf_limit'])) ? $this->data['settings']['secondary_conf_limit'] : NULL, $this->data['settings']['secondary_conf_offset'], $where, $order_by);
            }

        
        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode']= true;
      
        echo $this->view('wdk-categories-tree-top', $this->data); 
    }


    private function generate_controls_conf() {

        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main Options', 'wpdirectorykit'),
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
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( 'Edit Categorys <a href="%1$s" target="_blank"> open </a>', 'wpdirectorykit' ), admin_url('admin.php?page=wdk_category')),
                'content_classes' => 'wdk_elementor_hint',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'tab_conf_primary_section',
            [
                'label' => esc_html__('Top section Categories', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_control(
            'primary_conf_results_type',
            [
                'label' => __( 'Show type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'results_categories',
                'options' => [
                    'results_categories'  => __( 'All Categorys', 'wpdirectorykit' ),
                    'custom_categories' => __( 'Specific', 'wpdirectorykit' ),
                ],
               
            ]
        );

        $this->add_responsive_control (
            'primary_thumbnail_position',
            [
                    'label' => __( 'Thumbnail/icon position', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'right' => esc_html__('Rigth', 'wpdirectorykit'),
                        'left' => esc_html__('Left', 'wpdirectorykit'),
                        'top' => esc_html__('Top', 'wpdirectorykit'),
                        'bottom' => esc_html__('Bottom', 'wpdirectorykit'),
                    ],
                    'selectors_dictionary' => [
                        'right' => '',
                        'left' => 'flex-direction:row-reverse;text-align:right;',
                        'top' => 'flex-direction:column-reverse;text-align:center;',
                        'bottom' => 'flex-direction:column;text-align:center;',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-categories-tree-top .wdk-primary .category-card' => '{{UNIT}}',
                    ],
                    'default' => 'right', 
                    'separator' => 'after',
            ]
        );

        $this->add_responsive_control(
            'primary_root_enable',
                [
                    'label' => esc_html__( 'Show Main Categories', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'No', 'wpdirectorykit' ),
                    'block' => esc_html__( 'Yes', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
        );

        $this->add_responsive_control(
            'primary_sub_enable',
                [
                    'label' => esc_html__( 'Show Sub Categories', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'No', 'wpdirectorykit' ),
                    'block' => esc_html__( 'Yes', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                ]
        );
        
        $this->add_control(
            'primary_layout_image_type',
            [
                'label' => __( 'Thumbnail Type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'font_icon',
                'options' => [
                    'icon' => __( 'Icon', 'wpdirectorykit' ),
                    'image' => __( 'Image', 'wpdirectorykit' ),
                    'font_icon' => __( 'Font Icon', 'wpdirectorykit' ),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'primary_conf_offset',
            [
                'label' => __( 'Offset Categories', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 0,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'primary_conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );
        
        $this->add_control(
            'primary_conf_limit',
            [
                'label' => __( 'Limit Categories', 'wpdirectorykit' ),
                'description' => __( 'Set 0 for unlimit', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => 3,
                'separator' => 'before',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'primary_conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );

        $this->add_control(
            'primary_conf_order_by',
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
                    'rand()' => __('Random', 'wpdirectorykit'),
                ],
                'default' => 'order_index',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'primary_conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );

        $this->add_control(
            'primary_conf_order',
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
                            'name' => 'primary_conf_results_type',
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
                    'label' => __( 'ID category', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'step' => 1,
                ]
            );
            $repeater->end_controls_tabs();
      
            $this->add_control(
                'primary_conf_custom_results',
                [
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                    ],
                    'title_field' => '{{{ category_id }}}',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'primary_conf_results_type',
                                'operator' => '==',
                                'value' => 'custom_categories',
                            ]
                        ],
                    ],
                ]
            );
        }

        $this->end_controls_section();


        $this->start_controls_section(
            'tab_conf_secondary_section',
            [
                'label' => esc_html__('Bottom section Categories', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_control(
            'secondary_categories_enable',
            [
                'label' => esc_html__( 'Show Bottom Categories', 'wpdirectorykit' ),
                'type' => Controls_Manager::SWITCHER,
                'none' => esc_html__( 'No', 'wpdirectorykit' ),
                'block' => esc_html__( 'Yes', 'wpdirectorykit' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'secondary_conf_results_type',
            [
                'label' => __( 'Show type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'results_categories',
                'options' => [
                    'results_categories'  => __( 'All Categorys', 'wpdirectorykit' ),
                    'custom_categories' => __( 'Specific', 'wpdirectorykit' ),
                ],
            ]
        );

        $this->add_responsive_control (
            'secondary_thumbnail_position',
            [
                    'label' => __( 'Thumbnail/icon position', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'right' => esc_html__('Rigth', 'wpdirectorykit'),
                        'left' => esc_html__('Left', 'wpdirectorykit'),
                        'top' => esc_html__('Top', 'wpdirectorykit'),
                        'bottom' => esc_html__('Bottom', 'wpdirectorykit'),
                    ],
                    'selectors_dictionary' => [
                        'right' => '',
                        'left' => 'flex-direction:row-reverse;text-align:right;',
                        'top' => 'flex-direction:column-reverse;text-align:center;',
                        'bottom' => 'flex-direction:column;text-align:center;',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-categories-tree-top .wdk-secondary .category-card' => '{{UNIT}}',
                    ],
                    'default' => 'top', 
                    'separator' => 'after',
            ]
        );

        $this->add_responsive_control(
            'secondary_root_enable',
                [
                    'label' => esc_html__( 'Show Main Categories', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'No', 'wpdirectorykit' ),
                    'block' => esc_html__( 'Yes', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => '',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'secondary_conf_results_type',
                                'operator' => '==',
                                'value' => 'results_categories',
                            ]
                        ],
                    ],
                ]
        );

        $this->add_responsive_control(
            'secondary_sub_enable',
                [
                    'label' => esc_html__( 'Show Sub Categories', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'No', 'wpdirectorykit' ),
                    'block' => esc_html__( 'Yes', 'wpdirectorykit' ),
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'secondary_conf_results_type',
                                'operator' => '==',
                                'value' => 'results_categories',
                            ]
                        ],
                    ],
                ]
        );
                
        $this->add_control(
            'secondary_layout_image_type',
            [
                'label' => __( 'Thumbnail Type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'font_icon',
                'options' => [
                    'icon' => __( 'Icon', 'wpdirectorykit' ),
                    'image' => __( 'Image', 'wpdirectorykit' ),
                    'font_icon' => __( 'Font Icon', 'wpdirectorykit' ),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'secondary_conf_offset',
            [
                'label' => __( 'Offset Categories', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 0,
                'separator' => 'before',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'secondary_conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );

        $this->add_control(
            'secondary_conf_limit',
            [
                'label' => __( 'Limit Categories', 'wpdirectorykit' ),
                'description' => __( 'Set 0 for unlimit', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => 16,
                'separator' => 'after',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'secondary_conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );

        $this->add_control(
            'secondary_conf_order_by',
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
                    'rand()' => __('Random', 'wpdirectorykit'),
                ],
                'default' => 'order_index',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'secondary_conf_results_type',
                            'operator' => '==',
                            'value' => 'results_categories',
                        ]
                    ],
                ],
            ]
        );

        $this->add_control(
            'secondary_conf_order',
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
                            'name' => 'secondary_conf_results_type',
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
                    'label' => __( 'ID category', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'step' => 1,
                ]
            );
            $repeater->end_controls_tabs();
      
            $this->add_control(
                'secondary_conf_custom_results',
                [
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                    ],
                    'title_field' => '{{{ category_id }}}',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'secondary_conf_results_type',
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
    }

    private function generate_controls_styles() {

      $this->section_cards('wdk-primary');
      $this->section_cards('wdk-secondary', '25%');
    }

    private function section_cards($prefix = '', $grid_default = 'calc(100% / 3)') {

        $this->start_controls_section(
            $prefix.'styles_primary_section',
            [
                'label' => esc_html__($prefix, 'wpdirectorykit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            $prefix.'primary_row_gap_col',
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
                        'auto' => '-webkit-flex:1 2 auto;flex:1 2 auto',
                        '100%' =>  '-webkit-flex:1 2 100%;flex:1 2 100%',
                        '50%' =>  '-webkit-flex:1 2 50%;flex:1 2 50%',
                        'calc(100% / 3)' =>  '-webkit-flex:1 2 calc(100% / 3);flex:1 2 calc(100% / 3)',
                        '25%' =>  '-webkit-flex:1 2 25%;flex:1 2 25%',
                        '20%' =>  '-webkit-flex:1 2 20%;flex:1 2 20%',
                        'auto' =>  '-webkit-flex:1 2 auto;flex:1 2 auto',
                        'auto_flexible' =>  '-webkit-flex:1 2 auto;flex:1 2 auto',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .'.$prefix.' .wdk-row .wdk-col' => '{{UNIT}}',
                    ],
                    'default' => $grid_default, 
                    'separator' => 'before',
            ]
    );

    $this->add_responsive_control(
            $prefix.'primary_column_gap',
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
                    '{{WRAPPER}} .'.$prefix.' .wdk-row .wdk-col' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};;',
                    '{{WRAPPER}} .'.$prefix.' .wdk-row' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
                ],
            ]
    );

    $this->add_responsive_control(
            $prefix.'primary_row_gap',
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
                        '{{WRAPPER}} .'.$prefix.' .wdk-row  .wdk-col' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .'.$prefix.' .wdk-row' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $items = [
            [
                'key'=>$prefix.'card',
                'label'=> esc_html__('Card', 'wpdirectorykit'),
                'selectors'=> array(
                    'normal'=>'{{WRAPPER}} .'.$prefix.' .category-card',
                    'hover'=>'{{WRAPPER}} .'.$prefix.' .category-card%1$s',
                ),
                'options'=>['background','border','border_radius','padding','shadow','transition','padding', 'outline'],
            ],
            [
                'key'=>$prefix.'title',
                'label'=> esc_html__('Title', 'wpdirectorykit'),
                'selectors'=> array(
                    'normal'=>'{{WRAPPER}} .'.$prefix.' .category-card .body .title',
                    'hover'=>'{{WRAPPER}} .'.$prefix.' .category-card%1$s .body .title',
                ),
                'options'=>['color','background','border','border_radius','padding','shadow','transition','margin','padding'],
            ],
            [
                'key'=>$prefix.'sub',
                'label'=> esc_html__('Subtitle (count)', 'wpdirectorykit'),
                'selectors'=> array(
                    'normal'=>'{{WRAPPER}} .'.$prefix.' .category-card .body .sub',
                    'hover'=>'{{WRAPPER}} .'.$prefix.' .category-card%1$s .body .sub',
                ),
                'selector_hider'=>'{{WRAPPER}} .'.$prefix.' .category-card .body .sub',
                'options'=>['color','background','border','border_radius','padding','shadow','transition','margin','padding'],
            ],
            [
                'key'=>$prefix.'thumbnail',
                'label'=> esc_html__('Thumbnail', 'wpdirectorykit'),
                'selectors'=> array(
                    'normal'=>'{{WRAPPER}} .'.$prefix.' .category-card .thumbnail>*',
                ),
                'selector_hider'=>'{{WRAPPER}} .'.$prefix.' .category-card .thumbnail',
                'options'=>['border','border_radius','padding','shadow','transition','image_size_control','image_fit_control','font-size','css_filters'],
            ],
        ];

        foreach ($items as $item) {
            $this->add_control(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'type' => \Elementor\Controls_Manager::HEADING,
                ]
            );
    
            if(isset($item['selector_hidder'])) {
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
                                $item['selector_hidder'] => 'display: {{VALUE}};',
                            ],
                        ]
                );
            }

            $selectors = array();
            if(isset($item['selectors'])) {
                $selectors = $item['selectors'];
            }

            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);
                    
            $this->add_control(
                $item['key'].'_hr',
                [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                ]
            );
        
        }

        $this->end_controls_section();
    }

    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-categories-tree-top');
    }
}
