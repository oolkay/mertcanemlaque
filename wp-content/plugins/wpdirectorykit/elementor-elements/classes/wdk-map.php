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
class WdkMap extends WdkElementorBase {

    public function __construct($data = array(), $args = null) {

        \Elementor\Controls_Manager::add_tab(
            'tab_conf',
            esc_html__('Settings', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_layout',
            esc_html__('InfoWindow', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_content',
            esc_html__('Main', 'wpdirectorykit')
        );
        parent::__construct($data, $args);

        if ($this->is_edit_mode_load()) {
            wp_enqueue_style('leaflet');
            wp_enqueue_style('leaflet-cluster-def');
            wp_enqueue_style('leaflet-cluster');
            wp_enqueue_style('leaflet-fullscreen');
            wp_enqueue_script('leaflet');
            wp_enqueue_script('leaflet-fullscreen');
            wp_enqueue_script('leaflet-cluster');
                    
            wp_enqueue_style('wdk-notify');
            wp_enqueue_script('wdk-notify');
            wp_enqueue_style( 'wdk-listings-map' );
            $this->enqueue_styles_scripts();   
        }
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
        return 'wdk-map';
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
        return esc_html__('Wdk Map', 'wpdirectorykit');
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
        return 'eicon-google-maps';
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

       /* default from settings */
        $this->data['lat'] = wdk_get_option('wdk_default_lat', 51.505);
        $this->data['lng'] = wdk_get_option('wdk_default_lng', -0.09);

        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();
        $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
        $controller = 'listing';
        $custom_parameters = array();

        if($this->data['settings']['enable_custom_gps_center'] == 'yes') {
            $this->data['lat'] = $this->data['settings']['conf_custom_map_center_gps_lat'];
            $this->data['lng'] = $this->data['settings']['conf_custom_map_center_gps_lng'];
        }
        
        if(!isset($_GET['order_by'])) {
            $custom_parameters['order_by'] = $this->data['settings']['conf_order_by'].' '.$this->data['settings']['conf_order'];
        }
                        
        if(!isset($_GET['conf_order_by_custom'])) {
            $custom_parameters['order_by'] = $this->data['settings']['conf_order_by_custom'].' '.$this->data['settings']['conf_order'];
        }

        if(!empty($this->data['settings']['conf_query'])) {
            $qr_string = trim($this->data['settings']['conf_query'],'?');
            $string_par = array();
            parse_str($qr_string, $string_par);
            $custom_parameters += array_map('trim', $string_par);
        }
        
        $external_columns = array('location_id', 'category_id', 'post_title');
        wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters);
        $this->data['results'] = $this->WMVC->listing_m->get_pagination($this->data['settings']['conf_limit'], NULL, array('is_activated' => 1,'is_approved'=>1));
        
        $this->data['settings']['content_button_icon'] = '';

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode())
            $this->data['is_edit_mode']= true;
      
        echo $this->view('wdk-map', $this->data); 

    }

    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
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

         /* conf_results_type :: results_listings */
         if(true){
            $this->add_control(
                    'conf_results_type_results_listings_header',
                    [
                        'label' => esc_html__('Results listings', 'wpdirectorykit'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );

            $this->add_control(
                'conf_hide_real_location',
                [
                        'label' => esc_html__( 'Hide real location', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'True', 'wpdirectorykit' ),
                        'block' => esc_html__( 'False', 'wpdirectorykit' ),
                        'render_type' => 'template',
                        'return_value' => 'yes',
                        'default' => '',
                        'separator' => 'after',
                ]
            );

            $this->add_control(
                'conf_limit',
                [
                    'label' => __( 'Limit Results', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 10000,
                    'step' => 1,
                    'default' => 350,
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
                        'category_id' => __('Category', 'wpdirectorykit'),
                    ],
                    'default' => 'post_id',
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


            $fields_data = wdk_cached_field_get();
            $fields_list = array('' => esc_html__('Not Selected', 'wpdirectorykit'));
            $order_i = 0;
    
            $fields_list [(++$order_i).'__section'] = esc_html__('-- Section Custom fields --', 'wpdirectorykit');
            $fields_list [(++$order_i).'__first_image'] = esc_html__('First Image', 'wpdirectorykit');
            $fields_list [(++$order_i).'__counter_views'] = esc_html__('Views counter', 'wpdirectorykit');
            $fields_list [(++$order_i).'__post_title'] = esc_html__('WP Title', 'wpdirectorykit');

            $fields_list [(++$order_i).'__date'] = esc_html__('Date', 'wpdirectorykit');
            $fields_list [(++$order_i).'__address'] = esc_html__('Address', 'wpdirectorykit');
            $fields_list [(++$order_i).'__category_title'] = esc_html__('Category', 'wpdirectorykit');
            $fields_list [(++$order_i).'__location_title'] = esc_html__('Location', 'wpdirectorykit');

            foreach($fields_data as $field)
            {
                if(wmvc_show_data('field_type', $field) == 'SECTION') {
                    $fields_list [(++$order_i).'section__'.wmvc_show_data('idfield', $field)] = '-- '.esc_html__('Section', 'wpdirectorykit').' '.wmvc_show_data('field_label', $field).' --';
                } else {
                    $fields_list[(++$order_i).'__'.wmvc_show_data('idfield', $field)] = '#'.wmvc_show_data('idfield', $field).' '.wmvc_show_data('field_label', $field).'['.wmvc_show_data('field_type', $field).']';
                }
            }
    
            $this->add_control(
                'custom_marker_fields',
                [
                    'label' => __('Show field value instead of marker on map', 'wpdirectorykit'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '',
                    'label_block'   => true,
                    'options' => $fields_list,
                    'separator' => 'after',
                ]
            );

        }

        $this->end_controls_section();

        if(true){
            $this->start_controls_section(
                'conf_custom_map',
                [
                    'label' => esc_html__('Map', 'wpdirectorykit'),
                    'tab' => '1',
                ]
            );

            $this->add_responsive_control(
                    'conf_custom_map_height',
                    [
                        'label' => esc_html__('Height', 'wpdirectorykit'),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                            'px' => [
                                'min' => 100,
                                'max' => 1500,
                            ],
                        ],
                        'render_type' => 'template',
                        'default' => [
                            'size' => 350,
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wdk-element .wdk_map_results' => 'height: {{SIZE}}px !important',
                        ],
                        'separator' => 'after',
                    ]
            ); 

            $this->add_control(
                    'conf_custom_map_zoom_index',
                    [
                        'label' => esc_html__('Zoom Index', 'wpdirectorykit'),
                        'description' => esc_html__( 'Only active if auto centering is disabled', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                            'px' => [
                                'min' => 1,
                                'max' => 18,
                            ],
                        ],
                        'render_type' => 'template',
                        'default' => [
                            'size' => 7,
                        ]
                    ]
            );
            
            $this->add_control(
                'conf_custom_dragging',
                [
                        'label' => esc_html__( 'Dragging For Desktop', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'True', 'wpdirectorykit' ),
                        'block' => esc_html__( 'False', 'wpdirectorykit' ),
                        'render_type' => 'template',
                        'return_value' => 'yes',
                        'default' => 'yes',
                        'separator' => 'before',
                ]
            );
            
            $this->add_control(
                'conf_custom_dragging_mobile',
                [
                        'label' => esc_html__( 'Dragging For Mobile', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'True', 'wpdirectorykit' ),
                        'block' => esc_html__( 'False', 'wpdirectorykit' ),
                        'render_type' => 'template',
                        'return_value' => 'yes',
                        'default' => '',
                        'separator' => 'before',
                ]
            );

            $this->add_control(
                'conf_custom_map_styles_h',
                [
                    'label' => __( 'Map Styles', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'conf_custom_map_style',
                [
                    'label' => esc_html__('Styles List', 'wpdirectorykit'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Default', 'wpdirectorykit'),
                        'custom' => esc_html__('Custom', 'wpdirectorykit'),
                        'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{r}.png' => esc_html__('Light', 'wpdirectorykit'),
                        'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png' => esc_html__('Osmde', 'wpdirectorykit'),
                        'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png' => esc_html__('Osmde Fr', 'wpdirectorykit'),
                        'https://tile.openstreetmap.de/{z}/{x}/{y}.png' => esc_html__('Osmde De', 'wpdirectorykit'),
                        'https://tile.openstreetmap.org/{z}/{x}/{y}.png' => esc_html__('Mapnik', 'wpdirectorykit'),
                        'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png' => esc_html__('OpenTopoMap', 'wpdirectorykit'),
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}' => esc_html__('WorldImagery', 'wpdirectorykit'),
                        'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png' => esc_html__('Carto DarkMatter', 'wpdirectorykit'),
                        'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png' => esc_html__('Carto Voyager', 'wpdirectorykit'),
                        'https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}{r}.png' => esc_html__('Maptiler dark (demo)', 'wpdirectorykit'),
                        'https://{s}.tile.thunderforest.com/mobile-atlas/{z}/{x}/{y}.png' => esc_html__('Thunderforest MobileAtlas', 'wpdirectorykit'),
                        'https://{s}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png' => esc_html__('Thunderforest OpenCycleMap', 'wpdirectorykit'),
                        'https://{s}.tile.thunderforest.com/transport-dark/{z}/{x}/{y}.png' => esc_html__('Thunderforest TransportDark', 'wpdirectorykit'),
                        'https://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png' => esc_html__('Thunderforest Landscape', 'wpdirectorykit'),
                        'https://{s}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png' => esc_html__('Thunderforest Outdoors', 'wpdirectorykit'),
                        'https://{s}.tile.thunderforest.com/pioneer/{z}/{x}/{y}.png' => esc_html__('Thunderforest Pioneer', 'wpdirectorykit'),
                        'https://{s}.tile.thunderforest.com/neighbourhood/{z}/{x}/{y}.png' => esc_html__('Thunderforest Neighbourhood', 'wpdirectorykit'),
                        'https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png' => esc_html__('CyclOSM', 'wpdirectorykit'),
                        'https://{s}.tile.jawg.io/jawg-terrain/{z}/{x}/{y}{r}.png' => esc_html__('Jawg Streets', 'wpdirectorykit'),
                        'https://{s}.tile.jawg.io/jawg-streets/{z}/{x}/{y}{r}.png' => esc_html__('Jawg Sunny', 'wpdirectorykit'),
                        'https://{s}.tile.jawg.io/jawg-dark/{z}/{x}/{y}{r}.png' => esc_html__('Jawg Dark', 'wpdirectorykit'),
                        'https://{s}.tile.jawg.io/jawg-light/{z}/{x}/{y}{r}.png' => esc_html__('Jawg Light', 'wpdirectorykit'),
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}' => esc_html__('World Top oMap', 'wpdirectorykit'),
                        'google_map' => esc_html__('Google Map Layout', 'wpdirectorykit'),
                    ],
                    'default' => '',
                ]
            );

            $this->add_control(
                'jawg_map_key',
                [
                    'label' => __( 'Jawg Map API Key', 'wpdirectorykit' ),
                    'description' => wdk_sprintf(__( 'Please follow link and get API key <a href="%1$s" target="_blank"> here </a>', 'wpdirectorykit' ), 'https://www.jawg.io/en/pricing')
                                    .' '.wdk_sprintf(__( 'or check demo maps <a href="%1$s" target="_blank"> here </a>', 'wpdirectorykit' ), 'https://www.jawg.io/en/maps'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                    'condition' => [
                        'conf_custom_map_style' => [
                            'https://{s}.tile.jawg.io/jawg-terrain/{z}/{x}/{y}{r}.png',
                            'https://{s}.tile.jawg.io/jawg-streets/{z}/{x}/{y}{r}.png',
                            'https://{s}.tile.jawg.io/jawg-dark/{z}/{x}/{y}{r}.png',
                            'https://{s}.tile.jawg.io/jawg-light/{z}/{x}/{y}{r}.png',
                        ],
                    ],
                ]
            );

            $this->add_control(
                'thunderforest_map_key',
                [
                    'label' => __( 'Thunderforest Map API Key', 'wpdirectorykit' ),
                    'description' => wdk_sprintf(__( 'Please follow link and get API key <a href="%1$s" target="_blank"> here </a>', 'wpdirectorykit' ), 'https://www.thunderforest.com/pricing/'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                    'condition' => [
                        'conf_custom_map_style' => [
                            'https://{s}.tile.thunderforest.com/mobile-atlas/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/transport-dark/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/pioneer/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/neighbourhood/{z}/{x}/{y}.png',
                        ],
                    ],
                ]
            );

            $this->add_control(
                'google_map_key',
                [
                    'label' => __( 'Google Map API Key', 'wpdirectorykit' ),
                    'description' => wdk_sprintf(__( 'Please follow link and get API key <a href="%1$s" target="_blank"> here </a>', 'wpdirectorykit' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                    'condition' => [
                        'conf_custom_map_style' => ['google_map'],
                    ],
                ]
            );

            $this->add_control(
                'google_map_default_type',
                [
                    'label' => __( 'Default Type', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'roadmap',
                    'options' => array(
                        'roadmap' => __( 'Roadmap', 'wpdirectorykit' ),
                        'aerial' => __( 'Aerial', 'wpdirectorykit' ),
                        'terrain' => __( 'Terrain', 'wpdirectorykit' ),
                        'hybrid' => __( 'Hybrid', 'wpdirectorykit' ),
                        'satellite' => __( 'Satellite', 'wpdirectorykit' ),
                        'traffic' => __( 'Traffic', 'wpdirectorykit' ),
                        'transit' => __( 'Transit', 'wpdirectorykit' ),
                    ),
                    'condition' => [
                        'conf_custom_map_style' => ['google_map'],
                    ],
                ]
            );

            $this->add_control(
                'conf_custom_map_style_self',
                [
                    'label' => esc_html__('Link to custom Map Style', 'wpdirectorykit'),
                    'description' => esc_html__( 'You can add some custom map by link example https://leaflet-extras.github.io/leaflet-providers/preview/ or create your custom style and put link for example on maps.cloudmade.com/editor', 'wpdirectorykit' ),
                    'type' => Controls_Manager::TEXTAREA,
                    'render_type' => 'template',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'conf_custom_map_style',
                                'operator' => '==',
                                'value' => 'custom',
                            ]
                        ],
                    ]
                ]
            );

        $this->end_controls_section();
    }

    $this->start_controls_section(
        'conf_map_position',
        [
            'label' => esc_html__('Map Positioning', 'wpdirectorykit'),
            'tab' => '1',
        ]
    );

        $this->add_control(
                'conf_custom_map_center_gps_lat',
                [
                    'label' => esc_html__('GPS lat Center', 'wpdirectorykit'),
                    'type' => Controls_Manager::TEXT,
                    'default' => '45.675243',
                ]
        );

        $this->add_control(
                'conf_custom_map_center_gps_lng',
                [
                    'label' => esc_html__('GPS lng Center', 'wpdirectorykit'),
                    'type' => Controls_Manager::TEXT,
                    'default' => '5.907848',
                ]
        );

                    
        $this->add_responsive_control(
            'enable_custom_gps_center',
            [
                    'label' => esc_html__( 'Enable custom GPS', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'True', 'wpdirectorykit' ),
                    'block' => esc_html__( 'False', 'wpdirectorykit' ),
                    'render_type' => 'template',
                    'return_value' => 'yes',
                    'default' => '',
            ]
        );

        $this->end_controls_section();

    }

    private function generate_controls_layout() {

        /* default marker layout */
        $this->generate_controls_layout_default();
    }

    private function generate_controls_styles() {
        /* default marker layout */
        $this->generate_controls_styles_default();

        /* marker */
        $this->start_controls_section(
                'styles_cluster',
                [
                    'label' => esc_html__('Cluster', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        
        $this->add_responsive_control(
            'disable_cluster',
            [
                    'label' => esc_html__( 'Disable Clusters', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__( 'True', 'wpdirectorykit' ),
                    'block' => esc_html__( 'False', 'wpdirectorykit' ),
                    'render_type' => 'template',
                    'return_value' => 'yes',
                    'default' => '',
            ]
        );

        $this->add_control(
            'styles_cluster_color',
            [
                'label' => esc_html__('Cluster Color', 'wpdirectorykit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .marker-cluster div' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'styles_cluster_color_border',
            [
                'label' => esc_html__('Cluster Color Border', 'wpdirectorykit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .marker-cluster div::before' => 'border: 6px solid {{VALUE}}; box-shadow: inset 0 0 0 4px {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'styles_cluster_color_text',
            [
                'label' => esc_html__('Cluster Color Text', 'wpdirectorykit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .marker-cluster div' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                    'name' => 'styles_cluster_color_font',
                    'selector' => '{{WRAPPER}} .marker-cluster div',
            ]
        );

        $this->end_controls_section();

        /* marker */
        $this->start_controls_section(
                'styles_marker_sec',
                [
                    'label' => esc_html__('Marker', 'wpdirectorykit'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );


        $this->add_control(
                'styles_marker_h',
                [
                    'label' => esc_html__('Marker', 'wpdirectorykit'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        
        $this->start_controls_tabs('marker_button_style');

        $this->start_controls_tab(
                'marker',
                [
                    'label' => esc_html__('Normal', 'wpdirectorykit'),
                ]
        );

        $this->add_control(
                'styles_marker_color',
                [
                    'label' => esc_html__('Marker Border Color', 'wpdirectorykit'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wdk-element .wdk-map .wdk_marker-card::before' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .wdk-element .wdk_marker-container.wdk_marker_label' => 'border-color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'styles_marker_color_bckg',
                [
                    'label' => esc_html__('Marker Color', 'wpdirectorykit'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wdk-element .wdk_marker-card:after' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .wdk-element .wdk_marker-container.wdk_marker_label' => 'background-color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'styles_marker_color_text',
                [
                    'label' => esc_html__('Marker Text Color', 'wpdirectorykit'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wdk_face i' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .wdk_marker-container.wdk_marker_label' => 'color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'conf_custom_map_pin_icon',
                [
                    'label' => esc_html__('Icon', 'wpdirectorykit'),
                    'type' => Controls_Manager::ICONS,
                    'label_block' => true,
                    'default' => [
                        'value' => 'fa fa-home',
                        'library' => 'solid',
                    ],
                    'exclude_inline_options' => array('svg'),
                ]
        );

        $this->add_control(
                'conf_custom_map_pin',
                [
                    'label' => esc_html__('Custom Marker Pin Image', 'wpdirectorykit'),
                    'type' => Controls_Manager::MEDIA,
                ]
        );

        $this->end_controls_tab();
    
        $this->start_controls_tab(
                'marker_hover',
                [
                    'label' => esc_html__('Hover', 'wpdirectorykit'),
                ]
        );

        $this->add_control(
                'styles_marker_color_hover',
                [
                    'label' => esc_html__('Marker Border Color', 'wpdirectorykit'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wdk-element .wdk_marker-container:hover .wdk_marker-card:before' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .wdk-element .wdk_marker-container.wdk_marker_label:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'styles_marker_color_hover_bckg',
                [
                    'label' => esc_html__('Marker Color', 'wpdirectorykit'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wdk-element .wdk_marker-container:hover .wdk_marker-card:after' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .wdk-element .wdk_marker-container.wdk_marker_label:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'styles_marker_color_text_hover',
                [
                    'label' => esc_html__('Marker Text Color', 'wpdirectorykit'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wdk_marker-container:hover .wdk_face.back i, .wdk_marker-container:hover .wdk_face i' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .wdk_marker-container.wdk_marker_label:hover' => 'color: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'styles_marker_effect_duration',
                [
                    'label' => esc_html__('Transition Duration', 'wpdirectorykit'),
                    'type' => Controls_Manager::SLIDER,
                    'render_type' => 'template',
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 3000,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wdk_map-marker-container.clicked .wdk_face.front, .wdk_marker-container:hover .wdk_face.front' => 'transition-duration: {{SIZE}}ms',
                        '{{WRAPPER}} .wdk_marker-container.wdk_marker_label' => 'transition-duration: {{SIZE}}ms',
                    ],
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();



    
    }

    private function generate_controls_content() {
        /* default marker layout */
        $this->generate_controls_content_default();
    }

    private function generate_controls_layout_default() {

    }

    private function generate_controls_styles_default() {
        $this->start_controls_section(
            'styles_thmbn_section',
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
                    ''  => __( 'Default Sizes', 'wpdirectorykit' ),
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
                ],
                'render_type' => 'ui',
                'default' => [
                    'size' => 350,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk_size_image_cover .wdk-listing-card .wdk-thumbnail .wdk-image' => 'height: {{SIZE}}px',
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
    }
    
    private function generate_controls_content_default() {
        $this->start_controls_section(
            'content_thumbnail_section',
            [
                'label' => esc_html__('Colors', 'wpdirectorykit'),
                'tab' => 'tab_layout',
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
            [
                'key'=>'content_card',
                'label'=> esc_html__('Card', 'wpdirectorykit'),
                'selector'=>'.wdk-element .wdk-listing-card',
                'options'=>'full',
            ],
        ];

        foreach ($items as $item) {
           
                $this->start_controls_section(
                    $item['key'].'_section',
                    [
                        'label' => $item['label'],
                        'tab' => 'tab_layout'
                    ]
                );
        if($item['key']!='content_card')
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

            if($item['key'] == 'content_button' && FALSE) {
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
                $this->add_responsive_control(
                    $item['key'].'_text',
                    [
                        'label' => __( 'Text of Link', 'wpdirectorykit' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => '',
                    ]
                ); 

                $selectors = array(
                    'normal' => '{{WRAPPER}} '.$item['selector'].' i',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_icon_dynamic', ['margin']);
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
            /* END special for some elements */
        }
    }

    
    public function enqueue_styles_scripts() {
        wp_enqueue_style('leaflet');
        wp_enqueue_style('leaflet-cluster-def');
        wp_enqueue_style('leaflet-cluster');
        wp_enqueue_script('leaflet');
        wp_enqueue_script('leaflet-cluster');
        wp_enqueue_script('leaflet-fullscreen');
        wp_enqueue_style('leaflet-fullscreen');
        
        wp_enqueue_style('wdk-notify');
        wp_enqueue_script('wdk-notify');
		wp_enqueue_style( 'wdk-listings-map' );


        wp_enqueue_style('slick');
        wp_enqueue_style('slick-theme');
        wp_enqueue_style('wdk-hover');
        wp_enqueue_script('slick');
        wp_enqueue_script('leaflet-googlemutant');
        
        wp_enqueue_style('leaflet-draw');
        wp_enqueue_script('wdk-map-rectangle');
        
        if( wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_ajax_results')) {
            wp_enqueue_script( 'wdk-ajax-loading-listings');
        }
    }

}
