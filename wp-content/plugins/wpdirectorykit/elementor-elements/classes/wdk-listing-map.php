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
class WdkListingMap extends WdkElementorBase {

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
        return 'wdk-listing-map';
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
        return esc_html__('Wdk Listing Map', 'wpdirectorykit');
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
        global $wdk_listing_id;

        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        /* default from settings */
        $this->data['lat'] = wdk_get_option('wdk_default_lat', 51.505);
        $this->data['lng'] = wdk_get_option('wdk_default_lng', -0.09);
        $this->data['wdk_listing_id'] = $wdk_listing_id;

        if(!Plugin::$instance->editor->is_edit_mode() && !empty($wdk_listing_id)) {
            $this->data['lat'] = $this->data['lng'] = NULL;
            if(!empty($this->data['settings']['field_id'])) {
                if(strpos($this->data['settings']['field_id'],'__') !== FALSE){
                    $this->data['settings']['field_id'] = substr($this->data['settings']['field_id'], strpos($this->data['settings']['field_id'],'__')+2);
                }

                $gps = wdk_get_gps(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id));
                if($gps) {
                    $this->data['lat'] =  $gps['lat'];
                    $this->data['lng'] =  $gps['lng'];
                }
            } else {
                $this->data['lat'] =  wdk_field_value ('lat', $wdk_listing_id);
                $this->data['lng'] =  wdk_field_value ('lng', $wdk_listing_id);
            }
        } else {
            
        }     

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode() || empty($wdk_listing_id)) {
            $this->data['is_edit_mode']= true;
        }
      
        echo $this->view('wdk-listing-map', $this->data); 
    }

    private function generate_controls_conf() {
        if(true){

                $this->start_controls_section(
                    'conf_custom_map',
                    [
                        'label' => esc_html__('Map', 'wpdirectorykit'),
                        'tab' => '1',
                    ]
                );

                                         
            $this->add_control(
                'enable_router_suggest',
                [
                        'label' => esc_html__( 'Enable Router Suggestion', 'wpdirectorykit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'True', 'wpdirectorykit' ),
                        'block' => esc_html__( 'False', 'wpdirectorykit' ),
                        'render_type' => 'template',
                        'return_value' => 'yes',
                        'default' => '',
                ]
            );

            $this->add_control(
                'text_suggestion_route_placeholder',
                [
                    'label' => __( 'Suggestion route placeholder', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'rows' => 5,
                    'default' => __( 'Type your address', 'wpdirectorykit' ),
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'enable_router_suggest',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );
            
            $this->add_control(
                'text_suggestion_route',
                [
                    'label' => __( 'Suggestion route button', 'wpdirectorykit' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'rows' => 5,
                    'default' => __( 'Suggest Route', 'wpdirectorykit' ),
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'enable_router_suggest',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
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
                        'label' => __( 'Marker position based on field with address (Detected gps by api)', 'wpdirectorykit' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => '',
                        'options' => $fields_list,
                        'separator' => 'after',
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
                                '{{WRAPPER}} .wdk-element #wdk_map_results' => 'height: {{SIZE}}px !important',
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
                            ],
                        ]
                );
                
                $this->add_responsive_control(
                    'conf_custom_dragging',
                    [
                            'label' => esc_html__( 'Dragging', 'wpdirectorykit' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'True', 'wpdirectorykit' ),
                            'block' => esc_html__( 'False', 'wpdirectorykit' ),
                            'render_type' => 'template',
                            'return_value' => 'yes',
                            'default' => 'yes',
                            'separator' => 'before',
                    ]
                );

                                
                $this->add_responsive_control(
                    'conf_custom_popup_enable',
                    [
                            'label' => esc_html__( 'Enable Infobox', 'wpdirectorykit' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'True', 'wpdirectorykit' ),
                            'block' => esc_html__( 'False', 'wpdirectorykit' ),
                            'render_type' => 'template',
                            'return_value' => 'yes',
                            'default' => 'yes',
                            'separator' => 'before',
                    ]
                );
                                
                $this->add_responsive_control(
                    'puopup_custom_content',
                    [
                            'label' => esc_html__( 'Custom Fields For Infobox', 'wpdirectorykit' ),
                            'type' => Controls_Manager::SWITCHER,
                            'none' => esc_html__( 'True', 'wpdirectorykit' ),
                            'block' => esc_html__( 'False', 'wpdirectorykit' ),
                            'render_type' => 'template',
                            'return_value' => 'yes',
                            'default' => '',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'conf_custom_popup_enable',
                                        'operator' => '==',
                                        'value' => 'yes',
                                    ]
                                ],
                            ]
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
            'tab_conf_main_section',
            [
                'label' => esc_html__('Custom Popup Fields', 'wpdirectorykit'),
                'tab' => '1',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'puopup_custom_content',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ]
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
            'title_field_id',
            [
                'label' => __( 'Title Field', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $fields_list,
            ]
        );

        $this->add_control(
            'content_field_id',
            [
                'label' => __( 'Content Field', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $fields_list,
            ]
        );

        $this->end_controls_section();

    }

    private function generate_controls_layout() {

        /* default marker layout */
        $this->generate_controls_layout_default();
    }

    private function generate_controls_styles() {
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
                    ],
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();

        $this->start_controls_section(
            'section_form_suggestion',
            [
                'label' => __( 'Form Suggestion Route', 'wpdirectorykit' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'enable_router_suggest',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );

        
        $this->add_responsive_control (
            'form_suggestion_style_align',
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
                        'title' => esc_html__( 'Default', 'wpdirectorykit' ),
                        'icon' => 'eicon-text-align-justify',
                ]   ,
                ],
                'render_type' => 'ui',
                'selectors_dictionary' => [
                    'left' => 'flex-start',
                    'center' => 'center',
                    'right' => 'flex-end',
                    'justify' => 'space-between',
                ],
                'selectors' => [
                    '{{WRAPPER}} .route_suggestion' => 'justify-content:{{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_suggestion_column_direct',
            [
                    'label' => __( 'Direction', 'wpdirectorykit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__('Default', 'wpdirectorykit'),
                        'row' => esc_html__('Row', 'wpdirectorykit'),
                        'row-reverse' => esc_html__('Row reverse', 'wpdirectorykit'),
                        'column' => esc_html__('Column', 'wpdirectorykit'),
                        'column-reverse' => esc_html__('Column reverse', 'wpdirectorykit'),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .route_suggestion' => 'flex-direction: {{UNIT}}',
                    ],
                    'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
                'form_suggestion_column_gap',
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
                        '{{WRAPPER}} .route_suggestion > *' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};;',
                        '{{WRAPPER}} .route_suggestion' => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_responsive_control(
                'form_suggestion_row_gap',
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
                        '{{WRAPPER}} .route_suggestion > *' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .route_suggestion ' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        
        $this->add_responsive_control(
            'form_suggestion_space_top',
            [
                'label' => esc_html__('Space Top', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 25,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .route_suggestion' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
    );

        $this->add_control(
			'form_suggestion_header_1',
			[
				'label' => __( 'Input', 'wpdirectorykit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $selectors = array(
            'normal' => '{{WRAPPER}} .route_suggestion .input_text', 
        );

        $this->generate_renders_tabs($selectors, 'form_suggestion_input_dynamic', ['typo','color','background','border','border_radius','padding','shadow','transition', 'height', 'width']);

        
        $this->add_control(
			'form_suggestion_header_2',
			[
				'label' => __( 'Placeholder', 'wpdirectorykit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $selectors = array(
            'normal' => '{{WRAPPER}} .route_suggestion .input_text::placeholder',
        );
     
        $this->generate_renders_tabs($selectors, 'form_suggestion_input_pl_dynamic', ['align','typo','color']);
        
        $this->add_control(
			'form_suggestion_header_3',
			[
				'label' => __( 'Submit Button', 'wpdirectorykit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $selectors = array(
            'normal' => '{{WRAPPER}} .route_suggestion .wdk-btn',
            'hover' => '{{WRAPPER}} .route_suggestion .wdk-btn%1$s',
        );
     
        $this->generate_renders_tabs($selectors, 'form_suggestion_btn_dynamic', ['typo', 'color', 'border', 'border_radius', 'shadow', 'transition', 'background_group','padding']);

        $this->end_controls_section();
    }

    private function generate_controls_content() {
    }

    private function generate_controls_layout_default() {

    }

    private function generate_controls_styles_default() {
      
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
    }
}
