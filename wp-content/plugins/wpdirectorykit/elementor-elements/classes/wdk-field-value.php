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
class WdkFieldValue extends WdkElementorBase {

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
        return 'wdk-field-value';
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
        return esc_html__('Wdk Field Value', 'wpdirectorykit');
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
        return 'eicon-product-meta';
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

        $this->data['field_value'] = 'Example Value';
        $this->data['field_prefix'] = 'prefix';
        $this->data['field_suffix'] = 'suffix';
        if(!empty($this->data['settings']['field_id'])){
            if(strpos($this->data['settings']['field_id'],'__') !== FALSE){
                $this->data['settings']['field_id'] = substr($this->data['settings']['field_id'], strpos($this->data['settings']['field_id'],'__')+2);
            }

            if(!Plugin::$instance->editor->is_edit_mode() && !empty($wdk_listing_id)) {
                if(wdk_field_option($this->data['settings']['field_id'], 'field_type') == "CHECKBOX") {
                    if(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id) == 1){
                        $this->data['field_value'] = '<span class="field_checkbox_success">'.$this->generate_icon($this->data['settings']['field_checkbox_icon_success']).'</span>';
                    } else {
                        $this->data['field_value'] = '<span class="field_checkbox_unsuccess">'.$this->generate_icon($this->data['settings']['field_checkbox_icon_unsuccess']).'</span>';
                    } 
                } else if(wdk_field_option($this->data['settings']['field_id'],'field_type') == "INPUTBOX") {
                    $this->data['field_value'] =__(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id), 'wpdirectorykit');

                    if(strpos($this->data['field_value'], 'vimeo.com') !== FALSE)
                    {
                         $this->data['field_value'] = wp_oembed_get($this->data['field_value'], array("width"=>"800", "height"=>"450"));
                    }
                    elseif(strpos($this->data['field_value'], 'watch?v=') !== FALSE)
                    {
                        $embed_code = substr($this->data['field_value'], strpos($this->data['field_value'], 'watch?v=')+8);
                        $this->data['field_value'] =  wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code, array("width"=>"800", "height"=>"455"));
                    }
                    elseif(strpos($this->data['field_value'], 'youtube.com/shorts/') !== FALSE)
                    {
                        $embed_code = substr($this->data['field_value'], strpos($this->data['field_value'], 'shorts')+7);
                        $this->data['field_value'] = wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code,array("width"=>"800", "height"=>"455"));
                    }
                    elseif(strpos($this->data['field_value'], 'youtu.be/') !== FALSE)
                    {
                        $embed_code = substr($this->data['field_value'], strpos($this->data['field_value'], 'youtu.be/')+9);
                        $this->data['field_value'] = wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code, array("width"=>"800", "height"=>"455"));
                    } 
                    elseif(filter_var($this->data['field_value'], FILTER_VALIDATE_URL) !== FALSE && preg_match('/\.(mp4|flv|wmw|ogv|webm|ogg)$/i', $this->data['field_value']))
                    {
                        $this->data['field_value']  = '<video src="'.$this->data['field_value'].'" controls></video> ';
                    }
                    elseif(filter_var($this->data['field_value'] , FILTER_VALIDATE_EMAIL) !== FALSE) {
                        if(wmvc_show_data('link_value',$this->data['settings']) == 'value') {
                            $this->data['field_value']  = '<a href="mailto:'.$this->data['field_value'] .'">'.$this->data['field_value'] .'</a>';
                        } else if(wmvc_show_data('link_value',$this->data['settings']) == 'label') {
                            $this->data['field_value']  = '<a href="mailto:'.$this->data['field_value'] .'">'.wdk_field_label($this->data['settings']['field_id']) .'</a>';
                        } else if(wmvc_show_data('link_value',$this->data['settings']) == 'icon' && wdk_field_option ($this->data['settings']['field_id'], 'icon_id')) {
                            $this->data['field_value']  = '<a href="mailto:'.$this->data['field_value'] .'"><img src="'.esc_url(wdk_image_src(array('icon_id' =>wdk_field_option ($this->data['settings']['field_id'], 'icon_id')), 'full', NULL, 'icon_id')).'"></a>';
                        } else {
                            $this->data['field_value']  = '<a href="mailto:'.$this->data['field_value'] .'">'.wdk_field_label($this->data['settings']['field_id']) .'</a>';
                        }
                    }
                    elseif(filter_var($this->data['field_value'] , FILTER_VALIDATE_URL) !== FALSE) {
                        if(wmvc_show_data('link_value',$this->data['settings']) == 'value') {
                            $this->data['field_value']  = '<a href="'.$this->data['field_value'] .'">'.$this->data['field_value'] .'</a>';
                        } else if(wmvc_show_data('link_value',$this->data['settings']) == 'label') {
                            $this->data['field_value']  = '<a href="'.$this->data['field_value'] .'">'.wdk_field_label($this->data['settings']['field_id']) .'</a>';
                        } else if(wmvc_show_data('link_value',$this->data['settings']) == 'icon' && wdk_field_option ($this->data['settings']['field_id'], 'icon_id')) {
                            $this->data['field_value']  = '<a href="'.$this->data['field_value'] .'"><img src="'.esc_url(wdk_image_src(array('icon_id' =>wdk_field_option ($this->data['settings']['field_id'], 'icon_id')), 'full', NULL, 'icon_id')).'"></a>';
                        } else {
                            $this->data['field_value']  = '<a href="'.$this->data['field_value'] .'">'.wdk_field_label($this->data['settings']['field_id']) .'</a>';
                        }
                    }
                }
                elseif($this->data['settings']['field_id'] == 'category_id') {
                    if(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id)){
                        $this->WMVC->model('category_m');
                        $tree_data = $this->WMVC->category_m->get(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id), TRUE);
                        $this->data['field_value'] = wmvc_show_data('category_title', $tree_data);

                        if(get_option('wdk_multi_categories_other_enable', FALSE))
                            if(wdk_field_value ('categories_list', $wdk_listing_id)){
                                $other_categories = wdk_generate_other_categories_fast(wdk_field_value ('categories_list', $wdk_listing_id));
                                if(!empty($other_categories))
                                $this->data['field_value'] .=', '.join(', ',$other_categories);
                            }

                        $this->data['field_value'] =__($this->data['field_value'], 'wpdirectorykit');
                    }
                }
                elseif($this->data['settings']['field_id'] == 'location_id') {
                    if(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id)){
                        $this->WMVC->model('location_m');
                        $tree_data = $this->WMVC->location_m->get(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id), TRUE);
                        $this->data['field_value'] = wmvc_show_data('location_title', $tree_data);
                        
                        if(get_option('wdk_multi_locations_other_enable', FALSE))
                            if(wdk_field_value ('locations_list', $wdk_listing_id)){
                                $other_locations = wdk_generate_other_locations_fast(wdk_field_value ('locations_list', $wdk_listing_id));
                                if(!empty($other_locations))
                                $this->data['field_value'] .=', '.join(', ',$other_locations);
                            }

                        $this->data['field_value'] =__($this->data['field_value'], 'wpdirectorykit');
                    }
                    
                }
                elseif(wdk_field_option($this->data['settings']['field_id'], 'field_type') == "DATE") {
                    $this->data['field_value'] = __(wdk_get_date(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id)), 'wpdirectorykit') ;
                }
                elseif(strpos($this->data['settings']['field_id'],'date') !== FALSE) {
                    $this->data['field_value'] =__(wdk_get_date(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id)), 'wpdirectorykit') ;
                }
                elseif(
                    wdk_field_option($this->data['settings']['field_id'], 'field_type') == "TEXTAREA" ||
                    $this->data['settings']['field_id'] == 'post_content'
                ) {
                    global $wp_embed;
                    $this->data['field_value'] = wpautop(__(wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id), 'wpdirectorykit' ));
                    $this->data['field_value'] = html_entity_decode($wp_embed->autoembed($this->data['field_value'] ));
                }
                else {
                    $this->data['field_value'] =__( wdk_field_value ($this->data['settings']['field_id'], $wdk_listing_id), 'wpdirectorykit');
                }
            } else {
                if(wdk_field_option($this->data['settings']['field_id'],'field_type') == "CHECKBOX"){
                    $this->data['field_value'] = '<span class="field_checkbox_success">'.$this->generate_icon($this->data['settings']['field_checkbox_icon_success']).'</span>';
                } elseif($this->data['settings']['field_id'] == 'post_title') {
                    $this->data['field_value'] = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Title', 'wpdirectorykit');
                } elseif($this->data['settings']['field_id'] == 'address') {
                    $this->data['field_value'] = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Address', 'wpdirectorykit');
                } elseif($this->data['settings']['field_id'] == 'post_content') {
                    $this->data['field_value'] = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Content', 'wpdirectorykit');
                } elseif($this->data['settings']['field_id'] == 'post_title') {
                    $this->data['field_value'] = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Title', 'wpdirectorykit');
                } elseif($this->data['settings']['field_id'] == 'counter_views') {
                    $this->data['field_label'] = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Views counter', 'wpdirectorykit');
                }
                else{
                    $this->data['field_value'] = esc_html__('Example', 'wpdirectorykit') .' '. wdk_field_label($this->data['settings']['field_id']);
                }
            }     
            $this->data['field_prefix'] = wdk_field_option ($this->data['settings']['field_id'], 'prefix');
            $this->data['field_suffix'] = wdk_field_option ($this->data['settings']['field_id'], 'suffix');
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode() || empty($wdk_listing_id)) {
            $this->data['is_edit_mode']= true;
            if(is_intval($this->data['settings']['field_id']) && wdk_field_option($this->data['settings']['field_id'], 'is_visible_frontend') != 1) {
                $this->data['field_value'] .= ' <span class="dashicons dashicons-hidden" style="color:red"></span>';
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
                
            $this->data['field_prefix'] = apply_filters( 'wpdirectorykit/listing/field/prefix', $this->data['field_prefix'], $this->data['settings']['field_id']);
            $this->data['field_suffix'] = apply_filters( 'wpdirectorykit/listing/field/suffix',$this->data['field_suffix'], $this->data['settings']['field_id']);
         
            /* price format implement */
            if(function_exists('run_wdk_currency_conversion') && wdk_currencies_is_price_field($this->data['settings']['field_id'])) {
                /* if currency_conversion and field is price */
                $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal($this->data['field_value']), $this->data['settings']['field_id'], FALSE));
                $this->data['field_value'] = esc_html(wdk_number_format_i18n($value));
            } elseif(wdk_field_option($this->data['settings']['field_id'], 'is_price_format') && wdk_field_option($this->data['settings']['field_id'], 'field_type') == 'NUMBER' && !empty($this->data['field_value'])) {
                /* if field enabled is_price_format and field type is number*/
                $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal($this->data['field_value']), $this->data['settings']['field_id'], FALSE));
                $this->data['field_value'] = esc_html(wdk_number_format_i18n($value));
            } else {
                /* without number format */
                $this->data['field_value'] = apply_filters( 'wpdirectorykit/listing/field/value', $this->data['field_value'], $this->data['settings']['field_id']);
            }
        }
      
        echo $this->view('wdk-field-value', $this->data); 
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
            'html_tag',
            [
                'label' => __( 'HTML Tag', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'span',
                'options' => array(
                    ''=>__( 'Default', 'wpdirectorykit' ),
                    'h1'=>'H1',
                    'h2'=>'H2',
                    'h3'=>'H3',
                    'h4'=>'H4',
                    'h5'=>'H5',
                    'h6'=>'H6',
                    'span'=>'span',
                    'address'=>'address',
                    'strong'=>'strong',
                    'div'=>'div',
                    'p'=>'p',
                    'q'=>'q',
                    'time'=>'time',
                ),
                'separator' => 'after',
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

        $this->add_control(
            'link_value',
            [
                'label' => esc_html__('in case of link detected', 'wpdirectorykit'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__('Default', 'wpdirectorykit'),
                    'value' => esc_html__('show value', 'wpdirectorykit'),
                    'label' => esc_html__('show field label', 'wpdirectorykit'),
                    'icon' => esc_html__('show field icon', 'wpdirectorykit'),
                ],
            ]
        );

        $this->end_controls_section();

    }

    private function generate_controls_layout() {
    }


    private function generate_controls_styles() {
        $items = [
            [
                'key'=>'field_value',
                'label'=> esc_html__('Field Value', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-field-value .value, {{WRAPPER}} .wdk-field-value .value p',
                'selector_hover'=>'{{WRAPPER}} .wdk-field-value .value%1$s, {{WRAPPER}} .wdk-field-value .value%1$s p',
                'options'=>'full',
            ],
            [
                'key'=>'field_prefix',
                'label'=> esc_html__('Field Prefix', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-field-value .prefix',
                'selector_hover'=>'{{WRAPPER}} .wdk-field-value .prefix%1$s',
                'options'=>'full',
            ],
            [
                'key'=>'field_suffix',
                'label'=> esc_html__('Field Suffix', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-field-value .suffix',
                'selector_hover'=>'{{WRAPPER}} .wdk-field-value .suffix%1$s',
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

            if( $item ['key'] == 'field_value'){
                $selectors = array(
                    'normal' => '{{WRAPPER}} .wdk-field-value',
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic_align', ['align']);
            }

            $selectors = array();
            if(!empty($item['selector']))
                $selectors['normal'] = $item['selector'];

            if(!empty($item['selector_hover']))
                $selectors['hover'] = $item['selector_hover'];

            if(!empty($item['selector_focus']))
                $selectors['focus'] = $item['selector_hover'];
                
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options'], ['align']);

            $this->end_controls_section();
            /* END special for some elements */
        }


        $this->start_controls_section(
            'field_checkbox_icon',
            [
                'label' => esc_html__('Checkbox', 'wpdirectorykit'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
        $this->add_responsive_control(
            'field_checkbox_icon_success_header',
            [
                'label' => esc_html__('Success checkbox', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
    
        $this->add_control(
            'field_checkbox_icon_success',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-check',
                    'library' => 'solid',
                ],
            ] 
        );
    
        $selectors = array(
            'normal' => '{{WRAPPER}} .field_checkbox_success',
        );
        $this->generate_renders_tabs($selectors, 'field_checkbox_icon_success_dynamic', 'full');
    
            
        $this->add_responsive_control(
            'field_checkbox_icon_unsuccess_header',
            [
                'label' => esc_html__('Unsuccess checkbox', 'wpdirectorykit'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
    
        $this->add_control(
            'field_checkbox_icon_unsuccess',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-close',
                    'library' => 'solid',
                ],
            ] 
        );
    
        $selectors = array(
            'normal' => '{{WRAPPER}} .field_checkbox_unsuccess',
        );
        $this->generate_renders_tabs($selectors, 'field_checkbox_icon_unsuccess_dynamic', 'full');
    
        $this->end_controls_section();
    }

    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
      
    }
}
