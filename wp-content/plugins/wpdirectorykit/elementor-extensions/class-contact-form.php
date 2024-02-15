<?php

namespace Wdk\Elementor\Extensions;
        

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
class WdkContactFormExt extends \ElementinvaderAddonsForElementor\Widgets\EliContact_Form {
    public $field_types = array();

    public function __construct($data = array(), $args = null) {
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
        return 'wdk-class-contact-form';
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
        return esc_html__('Wdk Contact Form', 'wpdirectorykit');
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
        return 'eicon-mail';
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
        //$this->field_types ['user_id'] = esc_html__('Hidden Field Agent ID', 'wpdirectorykit');
        parent::register_controls();

        $items = [
            [
                'key'=>'button_booking',
                'label'=> esc_html__('Button Booking', 'wpdirectorykit'),
                'selector'=>'.elementinvader_contact_form .wdk-booking',
                'options'=>'full',
            ],
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                $item['key'].'_hide',
                [
                        'label' => esc_html__('Hide Element', 'wpdirectorykit'),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__('Hide', 'wpdirectorykit'),
                        'block' => esc_html__('Show', 'wpdirectorykit'),
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

            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            $this->end_controls_section();
        }
        
        $this->start_controls_section(
            'booking_section',
            [
                'label' => esc_html__('Booking', 'wpdirectorykit'),
                'tab' => 'fields_tab',
            ]
        );

        $this->add_control(
            'booking_hide_if_сhildrens_allowed_field',
            [
                    'label' => esc_html__('Childrens allowed field hide', 'wpdirectorykit'),
                    'type' => Controls_Manager::HIDDEN,
                    'none' => esc_html__('Yes', 'wpdirectorykit'),
                    'block' => esc_html__('No', 'wpdirectorykit'),
                    'return_value' => 'yes',
                    'default' => '',
            ]
        );

        $this->add_control(
            'booking_hide_if_pets_allowed_field',
            [
                    'label' => esc_html__('Pets allowed field hide', 'wpdirectorykit'),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__('Yes', 'wpdirectorykit'),
                    'block' => esc_html__('No', 'wpdirectorykit'),
                    'return_value' => 'yes',
                    'default' => '',
            ]
        );

        $this->add_control(
            'booking_hide_count_childs_field',
            [
                    'label' => esc_html__('Hide booking field count childs', 'wpdirectorykit'),
                    'type' => Controls_Manager::SWITCHER,
                    'none' => esc_html__('Yes', 'wpdirectorykit'),
                    'block' => esc_html__('No', 'wpdirectorykit'),
                    'return_value' => 'yes',
                    'default' => '',
            ]
        );
        $this->end_controls_section();
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
        $settings = $this->get_settings();
        global $wdk_listing_id;

        $validation_form_email = false;
        $validation_form_message = false;

        foreach ($settings['form_fields'] as $field) {

            $field_name = '';

            if(empty($field['field_id'])) {
                $field_name = $field['field_id'];
            }

            if(empty($field_name)) {
                $field_name = $field['field_label'];
            } 
            
            if(empty($field_name)) {
                $field_name = $field['placeholder'];
            } 
            
            if(strtolower($field_name) == 'email') {
                $validation_form_email = true;
            }

            if(strtolower($field_name) == 'message') {
                $validation_form_message = true;
            }
        }

        if(!$validation_form_email || !$validation_form_message) {
            $this->content ['wlisting_fields'] .= '<div class="elementinvader_addons_for_elementor_f_group elementinvader_addons_for_elementor_f_group_el_guests" style="width: 100%;-webkit-flex: 0 0 100%;flex: 0 0 100%;">';
            $this->content ['wlisting_fields'] .= '<div role="alert" class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger">'.esc_html__('Field with ID "email" and "message" missing, please add fields with this ID-s or form can\'t work properly','wpdirectorykit').'</div>';
            $this->content ['wlisting_fields'] .= '</div>';
        }


        $post_id = get_the_ID();
        if(isset($wdk_listing_id)){
            $this->content ['wlisting_fields'] .= '<input name="listing_id" type="hidden"  value="'.$wdk_listing_id.'" >';
            $this->content ['wlisting_fields'] .= '<input name="listing_link" type="hidden"  value="'.get_permalink(wmvc_show_data('post_id', $post_id)) .'" >';
        }
        wp_enqueue_script( 'wdk-booking-calculator-price' );
        if(function_exists('run_wdk_bookings')) {
            global $Winter_MVC_wdk_bookings;
         
            $Winter_MVC_wdk_bookings->model('calendar_m');
            $Winter_MVC_wdk_bookings->model('reservation_m');
            $calendar = $Winter_MVC_wdk_bookings->calendar_m->get_pagination(NULL, NULL, array('post_id' => $wdk_listing_id, 'is_activated' => 1));

            $dates = $Winter_MVC_wdk_bookings->reservation_m->get_enabled_dates($wdk_listing_id);
            $dates = (str_replace("'",'',join(' ',$dates)));

            /* booking price selector */
            $this->content ['wlisting_fields'] ='<div class="elementinvader_addons_for_elementor_f_group elementinvader_addons_for_elementor_f_group_el_guests" style="width: 100%;-webkit-flex: 0 0 100%;flex: 0 0 100%;">
                                                    <div class="wdk_booking_auto_calculate_price"></div>
                                                </div>'. $this->content ['wlisting_fields'];

            if(wdk_get_option('wdk_bookings_enable_woocommerce_payments') && wc_get_cart_url() == home_url()) {
                $this->content ['wlisting_fields'] .= '<div class="elementinvader_addons_for_elementor_f_group elementinvader_addons_for_elementor_f_group_el_guests" style="width: 100%;-webkit-flex: 0 0 100%;flex: 0 0 100%;">
                <div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger">'.esc_html__('Missing cart page in WooCommerce, please configure by guide','wpdirectorykit').'</div>
                </div>';
            }


            if( $calendar && isset($calendar[0]) )
            if( !is_user_logged_in() && ((isset($settings['booking_hide_if_not_login']) && !$settings['booking_hide_if_not_login'] == 'yes') || wmvc_show_data('is_disable_for_not_login',$calendar[0],false, TRUE, TRUE) || wdk_get_option('wdk_booking_disable_for_not_login'))){
                $this->content ['wlisting_fields'] .= '<div class="elementinvader_addons_for_elementor_f_group elementinvader_addons_for_elementor_f_group_el_guests" style="width: 100%;-webkit-flex: 0 0 100%;flex: 0 0 100%;">
                                                        <a href="'.wdk_login_url(wdk_current_url()).'" class="wdk-booking">
                                                        '.esc_html__('For Booking please login','wpdirectorykit').'
                                                        </a></div>';
            } else {
                $calendar = $calendar[0];
                
                if(intval(wdk_get_option('wdk_bookings_max_guests')) > 0) {
                    $options = range(1, intval(wdk_get_option('wdk_bookings_max_guests')));
                } else {
                    $options = range(1, 10);
                }

                if(empty($calendar->is_guests_disabled)) {

                    if($settings['booking_hide_count_childs_field'] != 'yes') {
                        $output ='<div class="elementinvader_addons_for_elementor_f_group elementinvader_addons_for_elementor_f_group_el_guests" style="width: 50%;-webkit-flex: 0 0 50%;flex: 0 0 50%;">';
                    } else {
                        $output ='<div class="elementinvader_addons_for_elementor_f_group elementinvader_addons_for_elementor_f_group_el_guests" style="width: 100%;-webkit-flex: 0 0 100%;flex: 0 0 100%;">';
                    }

                    $output .='<select name="guests_number_adults" id="guests_number_adults" type="select" class="elementinvader_addons_for_elementor_f_field" value="'.wmvc_show_data('guests_number', $_GET).'">';
                    $output .= '<option value="">'.esc_html__('Adults','wpdirectorykit').'</option>';
                    foreach ($options as $key => $option){
                        if(!next($options)) {
                            $output .= '<option value="'.esc_attr($option).'+">'.esc_html($option).'+</option>';
                            }else {
                                $output .= '<option value="'.esc_attr($option).'">'.esc_html($option).'</option>';
                            }
                        }
                        $output .='</select>
                    </div>';

                    if($settings['booking_hide_count_childs_field'] != 'yes') {
                        $output .='<div class="elementinvader_addons_for_elementor_f_group elementinvader_addons_for_elementor_f_group_el_guests" style="width: 50%;-webkit-flex: 0 0 50%;flex: 0 0 50%;">';
                            $output .='<select name="guests_number_childs" id="guests_number_childs" type="select" class="elementinvader_addons_for_elementor_f_field" value="'.wmvc_show_data('guests_number', $_GET).'">';
                            $output .= '<option value="">'.esc_html__('Childs','wpdirectorykit').'</option>';
                            foreach ($options as $key => $option){
                                if(!next($options)) {
                                    $output .= '<option value="'.esc_attr($option).'+">'.esc_html($option).'+</option>';
                                    } else {
                                        $output .= '<option value="'.esc_attr($option).'">'.esc_html($option).'</option>';
                                    }
                                }
                            $output .='</select>
                        </div>';
                    }
                    
                    $this->content ['wlisting_fields'] .= $output;
                }


                $attr = '';
                if(get_option('wdk_bookings_calendar_single') && !wmvc_show_data('is_hour_enabled',$calendar, false)) {
                    $attr = "data-wdksingle = 'true'";
                } else {
                                    
                    $custom_css = '.daterangepicker .drp-calendar.left .calendar-table::after {
                        content: "'.__('Please select from date/time','wpdirectorykit').'";
                    }';
                                    
                    $custom_css .= '.daterangepicker .drp-calendar.right .calendar-table::after {
                        content: "'.__('Please select to date/time','wpdirectorykit').'";
                    }';

                    wp_add_inline_style( 'daterangepicker', $custom_css);
                }

                if(!wmvc_show_data('is_hour_enabled',$calendar, false)){
                    $this->content ['wlisting_fields'] .= ' <div class="elementinvader_addons_for_elementor_f_group text elementinvader_addons_for_elementor_f_group_el_1" style="width: 50%;-webkit-flex: 0 0 50%;flex: 0 0 50%;">
                                                                <input '.$attr.' data-allowdates="'.$dates.'" name="date_from" id="date_from" type="text" class="date_from wdk-fielddate_range elementinvader_addons_for_elementor_f_field" placeholder="'.esc_html__('Date Range','wpdirectorykit').'">
                                                            </div>
                                                            ';
                    $this->content ['wlisting_fields'] .= ' <div class="elementinvader_addons_for_elementor_f_group text elementinvader_addons_for_elementor_f_group_el_1" style="width: 50%;-webkit-flex: 0 0 50%;flex: 0 0 50%;">
                                                                <input '.$attr.' data-allowdates="'.$dates.'" name="date_to" id="date_to" type="text" class="date_to wdk-fielddate_range elementinvader_addons_for_elementor_f_field" placeholder="'.esc_html__('Date Range','wpdirectorykit').'">
                                                            </div>
                                                            ';
                } else {
                    $this->content ['wlisting_fields'] .= ' <div class="elementinvader_addons_for_elementor_f_group text elementinvader_addons_for_elementor_f_group_el_1" style="width: 50%;-webkit-flex: 0 0 50%;flex: 0 0 50%;">
                                                                <input '.$attr.' data-allowdates="'.$dates.'" name="date_from" id="date_from" type="text" class="date_from wdk-fielddatetime_range elementinvader_addons_for_elementor_f_field" placeholder="'.esc_html__('Date Range','wpdirectorykit').'">
                                                            </div>
                                                            ';
                    $this->content ['wlisting_fields'] .= ' <div class="elementinvader_addons_for_elementor_f_group text elementinvader_addons_for_elementor_f_group_el_1" style="width: 50%;-webkit-flex: 0 0 50%;flex: 0 0 50%;">
                                                                <input '.$attr.' data-allowdates="'.$dates.'" name="date_to" id="date_to" type="text" class="date_to wdk-fielddatetime_range elementinvader_addons_for_elementor_f_field" placeholder="'.esc_html__('Date Range','wpdirectorykit').'">
                                                            </div>
                                                            ';
                }

                if(true && function_exists('wdk_booking_currency_symbol')){
                    if(!empty($calendar->json_data_fees))
                        $calendar_fees = json_decode($calendar->json_data_fees );
                  
                    if(!empty($calendar_fees)) {

                        foreach ($calendar_fees as $fee) {
                            if(!wmvc_show_data('is_activated', $fee, false,TRUE,TRUE)) continue;

                            $field = wdk_generate_slug(strtolower(esc_html(wmvc_show_data('title', $fee,'-',TRUE,TRUE)))); 

                            $readonly = '';
                            if(wmvc_show_data('is_required', $fee, false,TRUE,TRUE)) {
                                $readonly = 'readonly disabled';
                            }

                            $this->content ['wlisting_fields'] .='<div class="elementinvader_addons_for_elementor_f_group checkbox fee_group" style="width: 100%;-webkit-flex: 0 0 100%;flex: 0 0 100%;">
                                                                        <label for="'.esc_attr($field).'">
                                                                            <input name="fee_'.esc_attr($field).'" checked="checked" '.esc_attr($readonly).' id="'.esc_attr($field).'" type="checkbox" class="elementinvader_addons_for_elementor_f_field_checkbox fees_checkbox" value="yes" placeholder="'.esc_attr(wmvc_show_data('title', $fee,'-',TRUE,TRUE)).'" >
                                                                            '.esc_html(wmvc_show_data('title', $fee,'-',TRUE,TRUE)).'<span class="fee_price"> ('.esc_html(wmvc_show_data('value', $fee,'-',TRUE,TRUE)).esc_html(apply_filters( 'wdk-currency-conversion/convert/symbol', wdk_booking_currency_symbol())).'/'.
                                                                                ((isset($Winter_MVC_wdk_bookings->calendar_m->calculation_base[wmvc_show_data('calculation_base', $fee,'-',TRUE,TRUE)])) ? $Winter_MVC_wdk_bookings->calendar_m->calculation_base[wmvc_show_data('calculation_base', $fee,'-',TRUE,TRUE)] : 'per stay').')</span>
                                                                        </label>
                                                                    </div>';
                        }
                    }
                }

                if ($settings['booking_hide_if_pets_allowed_field'] != 'yes') {
                    $this->content ['wlisting_fields'] .='<div class="elementinvader_addons_for_elementor_f_group checkbox elementinvader_addons_for_elementor_f_group_el_pets" style="width: 100%;-webkit-flex: 0 0 100%;flex: 0 0 100%;">
                        <label for="pets_allowed">
                            <input name="pets_allowed" id="pets_allowed" type="checkbox" class="elementinvader_addons_for_elementor_f_field_checkbox" value="yes" placeholder="'.esc_attr__('Pets allowed', 'wpdirectorykit').'" >
                            '.esc_html__('Pets', 'wpdirectorykit').'
                        </label>
                    </div>';
                }

                wp_enqueue_script( 'daterangepicker-moment' );
              
                wp_enqueue_script( 'daterangepicker' );
                wp_enqueue_style('daterangepicker');
                

            }
        }

        if( is_user_logged_in()) {
            wp_enqueue_script( 'wdk-notify' );
            $data_user = get_userdata(get_current_user_id());
            $custom_js ="
                jQuery('document').ready(function($){
                    $('#elementinvader_addons_for_elementor_".esc_html($this->get_id_int())." #namename').val('".esc_js(wdk_show_data('display_name', $data_user, '' , TRUE, TRUE))."').attr('data-default','".esc_js(wdk_show_data('display_name', $data_user, '' , TRUE, TRUE))."');
                    $('#elementinvader_addons_for_elementor_".esc_html($this->get_id_int())." #emailemail').val('".esc_js(wdk_show_data('user_email', $data_user, '' , TRUE, TRUE))."').attr('data-default','".esc_js(wdk_show_data('user_email', $data_user, '' , TRUE, TRUE))."');
                    $('#elementinvader_addons_for_elementor_".esc_html($this->get_id_int())." [name=\"Phone\"]').val('".esc_js(wdk_show_data('wdk_phone', $data_user, '' , TRUE, TRUE))."').attr('data-default','".esc_js(wdk_show_data('wdk_phone', $data_user, '' , TRUE, TRUE))."');
                    $('#elementinvader_addons_for_elementor_".esc_html($this->get_id_int())." #messagemessage').val('".esc_js(esc_html__('Interested for listing', 'wpdirectorykit'))."').attr('data-default','".esc_js(esc_html__('Interested for listing', 'wpdirectorykit'))."');
                })
            ";
            wp_add_inline_script( 'wdk-notify', $custom_js );
        }
    
        
        parent::render();
    }

}

