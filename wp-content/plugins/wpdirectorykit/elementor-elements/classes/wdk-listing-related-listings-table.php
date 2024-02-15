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
class WdkListingRelatedListingsTable extends WdkElementorBase {

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
        return 'wdk-listing-related-listings-table';
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
        return esc_html__('Wdk Related Listings Table', 'wpdirectorykit');
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

        if(Plugin::$instance->editor->is_edit_mode()) {
            $this->data['results'] = $this->WMVC->listing_m->get_pagination(6, 0, array('is_activated' => 1,'is_approved'=>1));
        } else {
            if(!wdk_get_option('wdk_sub_listings_enable')) {
                return false;
            }
    
            if(empty(wdk_field_value('listing_related_ids', $wdk_listing_id))) {
                return false;
            }
    
            $this->WMVC->db->where( $this->WMVC->db->prefix.'wdk_listings.post_id IN(' . wdk_field_value('listing_related_ids', $wdk_listing_id) . ')', null, false);
            $this->WMVC->db->where(array('is_activated' => 1, 'is_approved'=>1));
            $this->WMVC->db->order_by('FIELD('.$this->WMVC->db->prefix.'wdk_listings.post_id, '. wdk_field_value('listing_related_ids', $wdk_listing_id) . ')');
    
            $this->data['results'] = $this->WMVC->listing_m->get();
        }


        $this->data['is_edit_mode'] = false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode'] = true;


            echo $this->view('wdk-listing-related-listings-table', $this->data); 
    }

    private function generate_controls_conf()
    {

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

        
        $repeater_sublistings_fields->add_control(
            'field_prefix',
            [
                'label' => __( 'Text prefix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater_sublistings_fields->add_control(
            'field_suffix',
            [
                'label' => __( 'Text suffix', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
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
                        'field_suffix' => esc_html__('(persons)', 'wpdirectorykit'),
                    ],
                ],
                'title_field' => '{{{ field }}}',
            ]
        );


        $this->end_controls_section();  

    }

    private function generate_controls_layout()
    {
    }

    private function generate_controls_styles()
    {

        $this->start_controls_section(
            'colors_sections',
            [
                'label' => esc_html__('Styles', 'wpdirectorykit'),
                'tab' => '1'
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => __('Border Color', 'wpdirectorykit'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wdk-listing-related-listings-table' => '--border_color:{{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'border_width',
            [
                'label' => __('Border Width', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-listing-related-listings-table' => '--border_width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        
        $this->add_responsive_control(
            'tr_hover_color',
            [
                    'label' => esc_html__( 'Line Hover Color', 'wpdirectorykit' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listing-related-listings-table table.wdk-table tr:hover' => 'background-color: {{VALUE}};',
                    ],
            ]
        );
        $this->add_control(
            'related_fields_header_hr1_0',
            [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        $this->add_control(
            'related_fields_star_image',
            [
                'label' => esc_html__('Image', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        
        $this->add_control(
            'related_fields_header_hr1',
            [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        $this->add_responsive_control(
            'hide_image',
            [
                    'label' => esc_html__( 'Hide Image', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'Hide', 'wpdirectorykit' ),
                    'block' => esc_html__( 'Show', 'wpdirectorykit' ),
                    'return_value' => 'none',
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .wdk-listing-related-listings-table table.wdk-table td:first-child' => 'display: {{VALUE}};',
                    ],
            ]
        );

        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-listing-related-listings-table .wdk-image',
        );
        $this->generate_renders_tabs($selectors, 'image_dynamic',  ['margin','border','border_radius','padding','shadow','image_size_control','image_fit_control', 'css_filters']);
        

        $this->add_control(
            'related_fields_header_hr2_0',
            [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        $this->add_responsive_control(
            'related_fields_styles',
            [
                'label' => esc_html__('Styles Content', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'related_fields_header_hr2',
            [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-sublistings-part a, {{WRAPPER}} .wdk-sublistings-part span',
            'hover'=>'{{WRAPPER}} .wdk-sublistings-part a%1$s'
        );
        $this->generate_renders_tabs($selectors, 'related_fields_dynamic', ['color','typo','padding','shadow','transition']);


        $this->add_control(
            'related_fields_header_hr5_0',
            [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
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
            'active' => '{{WRAPPER}} .wdk-sublistings-part .stars-lst span i.star-active',
        );
        $this->generate_renders_tabs($selectors, 'related_fields_star_dynamic', ['margin','padding','font-size', 'color']);

        $this->add_control(
            'related_fields_header_hr6_0',
            [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        $this->add_control(
            'related_fields_star_button',
            [
                'label' => esc_html__('Button', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        
        $this->add_control(
            'related_fields_header_hr6',
            [
                    'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-listing-related-listings-table table.wdk-table .wdk-btn',
            'hover'=>'{{WRAPPER}} ..wdk-listing-related-listings-table table.wdk-table .wdk-btn%1$s'
        );
        $this->generate_renders_tabs($selectors, 'button_dynamic',  ['margin','typo','color','background','border','border_radius','padding','shadow','transition','background_group']);

        $this->end_controls_section();

    }

    private function generate_controls_content()
    {
    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-listing-related-listings-table');

        wp_enqueue_style('wdk-notify');
        wp_enqueue_script('wdk-notify');
    }
}
