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
class WdkFieldIcon extends WdkElementorBase {

    public $field_id = NULL;
    public $fields_list = array();

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
        return 'wdk-field-icon';
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
        return esc_html__('Wdk Field Icon', 'wpdirectorykit');
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
        return 'eicon-image-bold';
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
        global $wdk_listing_id;

        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        $this->data['field_icon'] = '';
        if(!empty($this->data['settings']['field_id'])) {
            if(strpos($this->data['settings']['field_id'],'__') !== FALSE){
                $this->data['settings']['field_id'] = substr($this->data['settings']['field_id'], strpos($this->data['settings']['field_id'],'__')+2);
            }
            if(wdk_field_option ($this->data['settings']['field_id'], 'icon_id'))
                $this->data['field_icon'] = wdk_field_option ($this->data['settings']['field_id'], 'icon_id');
        }
        
        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
        } else {
            /* return false if no content */
            if(wdk_field_option ($this->data['settings']['field_id'], 'icon_id') == '')
                return false;
        }

        echo $this->view('wdk-field-icon', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $fields_data = wdk_cached_field_get();
        $fields_list = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
        $order_i = 0;

        $fields_list [(++$order_i).'__section'] = esc_html__('-- Section Custom fields --', 'wpdirectorykit');
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
        $this->add_control(
            'field_id',
            [
                'label' => __( 'Field id', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $fields_list,
                'separator' => 'after',
            ]
        );

        $this->end_controls_section();

    }

    private function generate_controls_layout() {
    }


    private function generate_controls_styles() {
            $this->start_controls_section(
                'field_group',
                [
                    'label' => esc_html__('Field icon', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control (
                'field_group_icon_max_heigth',
                [
                    'label' => esc_html__('Max Height', 'wpdirectorykit'),
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
                    'default' => [
                        'unit' => 'px',
                        'size' => 18,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-field-icon' => 'max-width: {{SIZE}}{{UNIT}}',
                    ],
                ]
            );

            
            $this->add_responsive_control (
                'field_group_icon_max_width',
                [
                    'label' => esc_html__('Max Width', 'wpdirectorykit'),
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
                    'default' => [
                        'unit' => 'px',
                        'size' => 18,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk-field-icon' => 'max-width: {{SIZE}}{{UNIT}}',
                    ],
                ]
            );

            $this->end_controls_section();
            /* END special for some elements */
    }


    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
      
    }
}
