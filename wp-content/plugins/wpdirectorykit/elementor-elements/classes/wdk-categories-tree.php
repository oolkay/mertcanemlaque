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
class WdkCategoriesTree extends WdkElementorBase {

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
        return 'wdk-categories-tree';
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
        return esc_html__('Wdk Categories Tree', 'wpdirectorykit');
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
                $where = array();
                $where[$this->WMVC->{$controller.'_m'}->_table_name.'.level_0_id IN (' . implode(',', $categories_ids) . ') OR '.$this->WMVC->{$controller.'_m'}->_table_name.'.idcategory IN (' . implode(',', $categories_ids) . ')'] = NULL;
                $this->data['categories'] = $this->WMVC->category_m->get_pagination(NULL, NULL, $where);
            }
        } else {
            $this->data['categories'] = $this->WMVC->category_m->get_pagination(NULL, NULL, array());
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode']= true;
      
        echo $this->view('wdk-categories-tree', $this->data); 
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
                'default' => 'results_categories',
                'options' => [
                    'results_categories'  => __( 'All Categorys', 'wpdirectorykit' ),
                    'custom_categories' => __( 'Specific', 'wpdirectorykit' ),
                ],
               
            ]
        );

        $this->add_control(
            'conf_limit_root',
            [
                'label' => __( 'Limit Main Categories', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 9,
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
            'conf_limit_sub',
            [
                'label' => __( 'Limit Sub Categories', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 9,
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
            'show_more_subs',
            [
                'label' => __( 'Show Button Open More SubCategories', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'yes',
                'default' => '',
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
            'show_more',
            [
                'label' => __( 'Show Button Open More Categories', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'yes',
                'default' => '',
                'separator' => 'after',
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

            $WMVC = &wdk_get_instance();
            $WMVC->model('category_m');
            $categories_root = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
            $categories = $WMVC->category_m->get_pagination(NULL, NULL, array('('.$WMVC->category_m->_table_name.'.level = 0)'=>NULL));
            foreach ($categories as $key => $category) {
                $categories_root[$category->idcategory] = $category->category_title;
            }

            $repeater->add_control(
                'category_id',
                [
                    'label' => __( 'Categories', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '',
                    'options' => $categories_root,
                    'separator' => 'after',
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
       
        $this->add_control(
            'show_icon',
            [
                'label' => __( 'Enable Category Icons', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'On', 'wpdirectorykit' ),
                'label_off' => __( 'Off', 'wpdirectorykit' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'layout_image_type',
            [
                'label' => __( 'Thumbnail Type', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'font_icon',
                'options' => [
                    'icon' => __( 'Icon', 'wpdirectorykit' ),
                    'image' => __( 'Image', 'wpdirectorykit' ),
                    'font_icon' => __( 'Font Icon', 'wpdirectorykit' ),
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_icon',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
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
                        'auto' => '-webkit-flex:0 0 auto;flex:0 0 auto',
                        '100%' =>  '-webkit-flex:0 0 100%;flex:0 0 100%',
                        '50%' =>  '-webkit-flex:0 0 50%;flex:0 0 50%',
                        'calc(100% / 3)' =>  '-webkit-flex:0 0 calc(100% / 3);flex:0 0 calc(100% / 3)',
                        '25%' =>  '-webkit-flex:0 0 25%;flex:0 0 25%',
                        '20%' =>  '-webkit-flex:0 0 20%;flex:0 0 20%',
                        'auto' =>  '-webkit-flex:0 0 auto;flex:0 0 auto',
                        'auto_flexible' =>  '-webkit-flex:0 0 auto;flex:0 0 auto',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-row .wdk-col' => '{{UNIT}}',
                    ],
                    'default' => 'calc(100% / 3)', 
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
                'list_gap',
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
                        '{{WRAPPER}} .wdk-categories  .wdk-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $this->end_controls_section();

        $items = [
            [
                'key'=>'title',
                'label'=> esc_html__('Title', 'wpdirectorykit'),
                'selector'=>'.title',
                'selector_hover'=>'.title%1$s',
                'options'=>['color','background','border','border_radius','padding','shadow','transition','margin','padding'],
            ],
            [
                'key'=>'item_button',
                'label'=> esc_html__('Item Box', 'wpdirectorykit'),
                'selector'=>'.wdk-categories .wdk-link',
                'selector_hover'=>'.wdk-categories .wdk-link%1$s',
                'options'=>['color','background','border','border_radius','padding','shadow','transition'],
            ],
            [
                'key'=>'item_icon',
                'label'=> esc_html__('Item Icon', 'wpdirectorykit'),
                'selector'=>'.wdk-categories .wdk-link i',
                'selector_hover'=>'.wdk-categories .wdk-link%1$s i',
                'options'=>['margin','color','background','border','border_radius','padding','shadow'],
            ],
            [
                'key'=>'category_icon',
                'label'=> esc_html__('Category Icon', 'wpdirectorykit'),
                'selector'=>'.title .wdk-icon',
                'selector_hover'=>'.title%1$s .wdk-icon',
                'options'=>['margin','color','background','border','border_radius','padding','shadow','image_size_control','image_fit_control'],
            ],
            [
                'key'=>'category_image',
                'label'=> esc_html__('Category Image', 'wpdirectorykit'),
                'selector'=>'.title .wdk-image',
                'selector_hover'=>'.title%1$s .wdk-image',
                'options'=>['margin','background','border','border_radius','padding','shadow','transition','image_size_control', 'css_filters','image_fit_control'],
            ],
            [
                'key'=>'category_font_icon',
                'label'=> esc_html__('Category Font Icon', 'wpdirectorykit'),
                'selector'=>'.title .wdk-font-icon',
                'selector_hover'=>'.title%1$s .wdk-font-icon',
                'options'=>['margin','background','border','border_radius','shadow','color','font-size','height','width'],
            ],
            [
                'key'=>'item_title',
                'label'=> esc_html__('Item Title', 'wpdirectorykit'),
                'selector'=>'.wdk-categories .wdk-link .wdk-title',
                'selector_hover'=>'.wdk-categories .wdk-link%1$s .wdk-title',
                'options'=>['margin','typo','color','background','border','border_radius','padding'],
            ],
            [
                'key'=>'item_count',
                'label'=> esc_html__('Item Count', 'wpdirectorykit'),
                'selector'=>'.wdk-categories .wdk-link .wdk-count',
                'selector_hover'=>'.wdk-categories .wdk-link%1$s .wdk-count',
                'options'=>['margin','typo','color','background','border','border_radius','padding'],
            ],
            [
                'key'=>'btn_more',
                'label'=> esc_html__('Btn More', 'wpdirectorykit'),
                'selector'=>'.wdk-categories-tree .btn-more',
                'selector_hover'=>'.wdk-categories-tree .btn-more%1$s',
                'options'=>['margin','typo','color','background','border','border_radius','padding'],
            ],
        ];

        foreach ($items as $item) {
            if($item['key'] == 'category_icon') {
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
                                    'value' => 'yes',
                                ],
                                [
                                    'name' => 'layout_image_type',
                                    'operator' => '==',
                                    'value' => 'icon',
                                ]
                            ],
                        ],
                    ]
                );
            } elseif($item['key'] == 'category_image') {
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
                                    'value' => 'yes',
                                ],
                                [
                                    'name' => 'layout_image_type',
                                    'operator' => '==',
                                    'value' => 'image',
                                ]
                            ],
                        ],
                    ]
                );
            } elseif($item['key'] == 'category_font_icon') {
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
                                    'value' => 'yes',
                                ],
                                [
                                    'name' => 'layout_image_type',
                                    'operator' => '==',
                                    'value' => 'font_icon',
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
        wp_enqueue_style('wdk-categories-tree');
    }
}
