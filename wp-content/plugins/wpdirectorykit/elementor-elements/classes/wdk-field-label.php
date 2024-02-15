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
class WdkFieldLabel extends WdkElementorBase {

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
        return 'wdk-field-label';
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
        return esc_html__('Wdk Field Label', 'wpdirectorykit');
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
        global $wdk_listing_id;

        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        $this->data['field_label'] = 'Example Label';
        $this->data['field_prefix'] = $this->data['settings']['field_prefix'];
        $this->data['field_suffix'] = $this->data['settings']['field_suffix'];
        if(!empty($this->data['settings']['field_id'])) {
            if(strpos($this->data['settings']['field_id'],'__') !== FALSE){
                $this->data['settings']['field_id'] = substr($this->data['settings']['field_id'], strpos($this->data['settings']['field_id'],'__')+2);
            }
            $this->data['field_label'] = wdk_field_label($this->data['settings']['field_id']);
    
            if(Plugin::$instance->editor->is_edit_mode()) {
                if($this->data['settings']['field_id'] == 'post_title') {
                    $this->data['field_label'] = esc_html__('Field', 'wpdirectorykit') .' '.esc_html__('Title', 'wpdirectorykit');
                } elseif($this->data['settings']['field_id'] == 'address') {
                    $this->data['field_label'] = esc_html__('Field', 'wpdirectorykit') .' '.esc_html__('Address', 'wpdirectorykit');
                } elseif($this->data['settings']['field_id'] == 'post_content') {
                    $this->data['field_label'] = esc_html__('Field', 'wpdirectorykit') .' '.esc_html__('Content', 'wpdirectorykit');
                } elseif($this->data['settings']['field_id'] == 'counter_views') {
                    $this->data['field_label'] = esc_html__('Field', 'wpdirectorykit') .' '.esc_html__('Views counter', 'wpdirectorykit');
                }
            }     
        }
            
        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
            if(is_intval($this->data['settings']['field_id']) && wdk_field_option($this->data['settings']['field_id'], 'is_visible_frontend') != 1) {
                $this->data['field_label'] .= ' <span class="dashicons dashicons-hidden" style="color:red"></span>';
            }
        } else {
            if(is_intval($this->data['settings']['field_id']) && wdk_field_option($this->data['settings']['field_id'], 'is_visible_frontend') != 1) {
                return false;
            }

            if(wdk_field_value('category_id', $wdk_listing_id) && wdk_depend_is_hidden_field($this->data['settings']['field_id'], wdk_field_value('category_id', $wdk_listing_id))) {
                return false;
            } 

            /* return false if no content */
            if($this->data['settings']['hide_onempty'] == 'yes' && wdk_field_value($this->data['settings']['field_id'], $wdk_listing_id) == '')
                return false;
        }

        echo $this->view('wdk-field-label', $this->data); 
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

        $this->add_control(
            'field_suffix',
            [
                'label' => __( 'Label suffix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'field_prefix',
            [
                'label' => __( 'Label prefix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );
        
        $this->add_control(
			'hide_onempty',
			[
				'label' => __( 'Hide if empty', 'wpdirectorykit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'True', 'wpdirectorykit' ),
				'label_off' => __( 'False', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

        $this->end_controls_section();

    }

    private function generate_controls_layout() {
    }


    private function generate_controls_styles() {
        $items = [
            [
                'key'=>'field_label',
                'label'=> esc_html__('Field Label', 'wpdirectorykit'),
                'selector'=>'.wdk-field-label',
                'options'=>'full',
            ]
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => 'tab_layout'
                ]
            );

            $selectors = array(
                'normal' => '{{WRAPPER}} '.$item['selector'],
                'hover'=>'{{WRAPPER}} '.$item['selector'].'%1$s'
            );
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
