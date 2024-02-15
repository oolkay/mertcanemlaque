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
class WdkLastSearch extends WdkElementorBase {

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

        add_action( 'elementor/editor/after_enqueue_styles', function()
        {
            wp_add_inline_style( 'elementor-editor', '.elementor-control-content select option[value*="section"]{color:#fff;background:#000}');
        } ); 
        
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
		return [ 'wdk-elementor-listing-preview' ];
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
        return 'wdk-last-search';
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
        return esc_html__('Wdk Last Search', 'wpdirectorykit');
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
        return 'eicon-post-title';
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

        $this->data['last_search'] = wmvc_show_data('text_default_search', $this->data['settings'], esc_html__('Last Search', 'wpdirectorykit'));
        $this->data['last_search_url'] = wmvc_show_data('url_link_default_onempty', $this->data['settings'],'#');

        if(isset($_COOKIE['wdk_last_search'])) {
            $this->data['last_search_url'] = wdk_url_suffix(get_permalink(get_option('wdk_results_page')), $_COOKIE['wdk_last_search']);

            $custom_parameters = array();
            $qr_string = trim($_COOKIE['wdk_last_search']);
            $qr_string = 'search_location%5B%5D=221&search_location55=1&search_category%5B%5D=0&search_category=2&wmvc_view_type=grid&order_by=post_id%20DESC';
            parse_str($qr_string, $custom_parameters);

            if(isset($custom_parameters['search_location'])) {
                if(is_array($custom_parameters['search_location'])) {
                    if(isset($custom_parameters['search_location'][0]) && !empty($custom_parameters['search_location'][0]))
                        $this->data['last_search'] = wdk_get_location_title($custom_parameters['search_location'][0], $this->data['last_search']);
                } else {
                    $this->data['last_search'] = wdk_get_location_title($custom_parameters['search_location'], $this->data['last_search']);
                }
            }

        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
        } else {
            if($this->data['last_search_url'] == '#' || empty($this->data['last_search_url']) ) {
                return false;
            }
        }

        echo $this->view('wdk-last-search', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_control(
            'text_before',
            [
                'label' => __( 'Text before', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'render_type' => 'template'
            ]
        );

        $this->add_control(
            'text_after',
            [
                'label' => __( 'Text after', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'render_type' => 'template'
            ]
        );

        $this->add_control(
            'text_default_search',
            [
                'label' => __( 'Text default', 'wpdirectorykit' ),
                'default' => __( 'Last Search', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'render_type' => 'template'
            ]
        );

        $this->add_control(
            'url_link_default_onempty',
            [
                'label' => __( 'Link if missing latest search', 'wpdirectorykit' ),
                'default' =>'',
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->end_controls_section();

    }

    private function generate_controls_layout() {
    }


    private function generate_controls_styles() {
        $items = [
            [
                'key'=>'text',
                'label'=> esc_html__('Styles', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-last-search',
                'options'=>['align','typo','color'],
            ],
            [
                'key'=>'text_search',
                'label'=> esc_html__('Special For Link', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-last-search a',
                'selector_hover'=>'{{WRAPPER}} .wdk-last-search a%1$s',
                'options'=>['align','typo','color'],
            ],
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => 'tab_form_styles',
                ]
            );

            if(!empty($item['selector_hide'])) {
                $this->add_responsive_control(
                    $item['key'].'_hide',
                    [
                        'label' => esc_html__( 'Hide Element', 'wdk-svg-map' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'Hide', 'wdk-svg-map' ),
                        'block' => esc_html__( 'Show', 'wdk-svg-map' ),
                        'return_value' =>  'none',
                        'default' => ($item['key'] == 'field_button_reset' ) ? 'none':'',
                        'selectors' => [
                            $item['selector_hide'] => 'display: {{VALUE}};',
                        ],
                    ]
                );
            }

            $selectors = array();
            if(!empty($item['selector']))
                $selectors['normal'] = $item['selector'];

            if(!empty($item['selector_hover']))
                $selectors['hover'] = $item['selector_hover'];

            if(!empty($item['selector_focus']))
                $selectors['focus'] = $item['selector_hover'];
                
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            $this->end_controls_section();
            /* END special for some elements */
        }
    }


    private function generate_controls_content() {

    } 
            
    public function enqueue_styles_scripts() {
      
    }
}
