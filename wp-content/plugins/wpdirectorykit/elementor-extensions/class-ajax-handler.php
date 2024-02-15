<?php
namespace Wdk\Elementor\Extensions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AjaxHandler {
    /**
     * data array
     *
     * @var array
     */
    protected $data = array();
    public $_translate_strings = array();
    protected $WMVC = NULL;


    public function __construct($data = array(), $args = null) {
        add_action( 'eli/ajax-handler/after', array( $this, 'after' ) );
        add_filter( 'eli/ajax-handler/filter_from_data', array( $this, 'filter_from_data' ) );

        $this->_translate_strings = array(
            __('Guests number', 'wpdirectorykit'),
            __('Listing link', 'wpdirectorykit'),
            __('Name', 'wpdirectorykit'),
            __('Date from', 'wpdirectorykit'),
            __('Date to', 'wpdirectorykit'),
        );
    }

    public function filter_output ($filter_output = array()) {
        $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-primary" role="alert">The form was sent successfully.</div>';
        return $filter_output;
    }

    public function filter_from_data ($form_data = array()) {
        if (isset($_POST['date_from']) && isset($_POST['date_to'])) {
            /* disable double sent to admin mail */
            $form_data['settings']['disable_mail_send'] = 1;
        }
        if (isset($_POST['listing_id']) && !empty($_POST['listing_id'])) {
            /* disable double sent to admin mail */
            $form_data['settings']['disable_mail_send'] = 1;
        }
        return $form_data;
    }
    
    public function after ($form_data = array()) {

        $this->WMVC = &wdk_get_instance();
        $this->WMVC->model('messages_m');
        
        $message_skip = false;
     
        $email = '';
        $message = '';
        $post_id = '';
        $wdk_widget = '';
        foreach ($_POST as $key => $value) {
            if (stripos($key, 'mail') !== false || filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $email = sanitize_email($value);
            }
            if (stripos($key, 'message') !== false) {
                $message = sanitize_text_field($value);
            }
            if (stripos($key, 'wdk_widget') !== false) {
                $wdk_widget = sanitize_text_field($value);
            }
            if (stripos($key, 'listing_id') !== false) {
                $post_id = sanitize_text_field($value);
            }
        }
        
        if(function_exists('run_wdk_bookings') && (
            is_user_logged_in() || 
            $wdk_widget == 'wdk-booking-quick-submission' || 
            wdk_get_option('wdk_booking_disable_for_not_login') || 
            (!wdk_get_option('wdk_booking_disable_for_not_login') && wdk_get_option('wdk_bookings_enable_woocommerce_payments'))
            )
            ) {
            if(isset($_POST['date_from']) && isset($_POST['date_to']) && !empty($_POST['date_from']) && !empty($_POST['date_to'])) {
                $date_from = sanitize_text_field($_POST['date_from']);
                $date_to = sanitize_text_field($_POST['date_to']);
                $errors = '';

                if(wdk_is_date($date_from))
                {
                    $date_from = wdk_normalize_date_db($date_from);
                }
                else
                {
                   return false;
                }
                
                if(wdk_is_date($date_to))
                {
                    $date_to = wdk_normalize_date_db($date_to);
                }
                else
                {
                   return false;
                }

                global $Winter_MVC_wdk_bookings;
                $Winter_MVC_wdk_bookings->model('reservation_m');
                $Winter_MVC_wdk_bookings->model('calendar_m');
                $Winter_MVC_wdk_bookings->model('price_m');
               
                /* message data */
                $price = $Winter_MVC_wdk_bookings->reservation_m->calculate_price($post_id, $date_from, $date_to);
                $calendar = $Winter_MVC_wdk_bookings->calendar_m->get_by(array('post_id'=>$post_id), TRUE); // date_package_expire package_id
                $calendar_fees = array();
                if($calendar && !empty($calendar->json_data_fees))
                    $calendar_fees = json_decode($calendar->json_data_fees );

                if($price) {
                    $guests_number = intval(wmvc_show_data('guests_number_adults', $_POST, 0)) + intval(wmvc_show_data('guests_number_childs', $_POST, 0));
                    $nights = (int)abs(strtotime($date_to) - strtotime($date_from))/(60*60*24);
                    foreach ($calendar_fees as $fee) {
                        if(!wmvc_show_data('is_activated', $fee, false,TRUE,TRUE)) continue;
                        if(is_intval(wmvc_show_data('value', $fee,'',TRUE,TRUE))) {
                            $field = wdk_generate_slug(strtolower(esc_html(wmvc_show_data('title', $fee,'-',TRUE,TRUE)))); 
                            if(!wmvc_show_data('is_required', $fee, false,TRUE,TRUE) && !isset($_POST['fee_'.$field])) {
							
                            } else {
                                if(wmvc_show_data('calculation_base', $fee,'',TRUE,TRUE) == 'per_night') {
                                    $price['price'] += intval(wmvc_show_data('value', $fee,'',TRUE,TRUE)) * $nights;
                                } else if(wmvc_show_data('calculation_base', $fee,'',TRUE,TRUE) == 'per_person') {
                                    $price['price'] += intval(wmvc_show_data('value', $fee,'',TRUE,TRUE)) * $guests_number;
                                } else {
                                    $price['price'] += intval(wmvc_show_data('value', $fee,'',TRUE,TRUE));
                                }
                            }
                        }
                    }
                }

                $note = '<br>';
                if(empty($calendar->is_guests_disabled)){
                    if(wmvc_show_data ('guests_number_adults', $_POST, false))
                        $note .= '<b>'.__('Guests Adult', 'wpdirectorykit').':</b> '.sanitize_text_field(wmvc_show_data('guests_number_adults', $_POST)).'<br>'.PHP_EOL;
                    if(wmvc_show_data ('guests_number_childs', $_POST, false))
                        $note .= '<b>'.__('Guests Childs', 'wpdirectorykit').':</b> '.sanitize_text_field(wmvc_show_data('guests_number_childs', $_POST)).'<br>'.PHP_EOL;
                }

                if(wmvc_show_data ('сhildrens_allowed', $_POST, false) || intval(wmvc_show_data ('guests_number_childs', $_POST, false)) > 0)
                    $note .= '<b>'.__('Childrens', 'wpdirectorykit').':</b> yes'.'<br>'.PHP_EOL;

                if(wmvc_show_data ('pets_allowed', $_POST, false))
                    $note .= '<b>'.__('Pets', 'wpdirectorykit').':</b> yes'.'<br>'.PHP_EOL;

                if(wmvc_show_data ('Name', $_POST, false))
                    $note .= '<b>'.__('Name', 'wpdirectorykit').':</b> '.sanitize_text_field(wmvc_show_data ('Name', $_POST)).'<br>'.PHP_EOL;

                if(wmvc_show_data ('Email', $_POST, false))
                    $note .= '<b>'.__('Email', 'wpdirectorykit').':</b> '.sanitize_text_field(wmvc_show_data ('Email', $_POST)).'<br>'.PHP_EOL;

                if(wmvc_show_data ('Message', $_POST, false))
                    $note .= '<b>'.__('Message', 'wpdirectorykit').':</b> '.sanitize_text_field(wmvc_show_data ('Message', $_POST)).'<br>'.PHP_EOL;

                if(wmvc_show_data ('Phone', $_POST, false))
                    $note .= '<b>'.__('Phone', 'wpdirectorykit').':</b> '.sanitize_text_field(wmvc_show_data ('Phone', $_POST)).'<br>'.PHP_EOL;

                if(true)
                    foreach($_POST as $key => $value) {
                        if($key=='element_id') continue;
                        if(in_array($key, array('eli_page_id', 'g-recaptcha-response','eli_id', 'eli_type','ID','filter','action','send_action_type','Phone','Message','Email','Name','pets_allowed','сhildrens_allowed','guests_number','guests_number_childs','guests_number_adults'))) continue;
                        if(in_array($key, array('listing_link','date_from','date_to','listing_id','wdk_widget', 'function','page'))) continue;

                        if(filter_var($value, FILTER_VALIDATE_URL ) || strpos( $value, 'http' ) !== FALSE) {
                            $note .= '<b> '.__(str_replace('_',' ', ucfirst($key)), 'wpdirectorykit').':</b> <a href="'.esc_url($value).'">'.$value.'</a><br>';
                        } else {
                            $note .= '<b> '.__(str_replace('_',' ', ucfirst($key)), 'wpdirectorykit').':</b> '.$value.'<br>';
                        }
                    }

                
                if( !is_user_logged_in()) {

                    if(
                        $wdk_widget == 'wdk-booking-quick-submission' || 
                        (!wdk_get_option('wdk_booking_disable_for_not_login') && wdk_get_option('wdk_bookings_enable_woocommerce_payments'))
                        ) {
                        
                        /* if not disable for not login users, try create user */
                        $userlogin = $email;
                        $username = '';
                        $email_address = $email;
                        $password = wp_generate_password();
                        
                        if(wmvc_show_data('name', $_POST, false))
                            $username = sanitize_text_field(wmvc_show_data('name', $_POST, false));
                            
                        $user_id = wp_create_user( $userlogin, $password, $email_address );

                        if(is_intval($user_id)) {
                            // Set the nickname
                            wp_update_user(
                                array(
                                    'ID'          =>    $user_id,
                                    'nickname'    =>    $email_address
                                )
                            );
                
                            $user = new \WP_User( $user_id );
                            $user->set_role('wdk_visitor');
                
                            wp_update_user( array ('ID' => $user_id, 'display_name' => esc_html( $username ) ) );
                
                            $userdata = get_userdata($user_id);
                
                            $subject = __('New user on our website!', 'wdk-membership');
                            $data_message = array();
                            $data_message['login'] = $userlogin;
                            $data_message['password'] = $password;
                            $data_message['user'] = $userdata;
                            $data_message['data'] = '';
                
                            update_user_meta( $user_id, 'first_name', $username);
                
                            wdk_mail($email_address, $subject, $data_message, 'new_user_auto_created');

                            /* auto login */
                            wp_set_current_user($user_id, $userdata->user_login );
                            wp_set_auth_cookie($user_id);
                            
                        } elseif($user = get_user_by( 'email', $email_address)) {
                            $user_id = $user->ID;
                            $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.wdk_sprintf(__( 'For Booking required login, user with email %1$s already exists, please first try login', 'wpdirectorykit' ), $email_address).' <a href="'.wdk_login_url().'"  target="_blank" style="text-decoration: underline;">'.esc_html__('here', 'wpdirectorykit').'</a></div>';
                        } else {
                            $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('For Booking please login', 'wpdirectorykit').' <a href="'.wdk_login_url().'"  target="_blank" style="text-decoration: underline;">'.esc_html__('here', 'wpdirectorykit').'</div>';
                        }
                    } else {
                        $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('For Booking please login', 'wpdirectorykit').' <a href="'.wdk_login_url().'"  target="_blank" style="text-decoration: underline;">'.esc_html__('here', 'wpdirectorykit').'</div>';
                    }
                }

                $data = array(
                    'post_id' => ($post_id),
                    'date_from' =>  ($date_from),
                    'date_to' =>  ($date_to),
                    'notes' =>   $message,
                    'user_id' =>  get_current_user_id(),
                    'price' =>  wmvc_show_data('price', $price),
                    'currency_code' =>  wdk_booking_currency_symbol(),
                    'calendar_id' =>  wmvc_show_data('idcalendar', $calendar),
                    'notes' => $note,
                );

                if(wmvc_show_data('is_enable_noapprovements', $calendar, false))
                    $data['is_approved'] = 1;

                $results = $Winter_MVC_wdk_bookings->reservation_m->is_booked($post_id, $date_from, $date_to);
               

                $Winter_MVC_wdk_bookings->db->where("date_from <='".$date_from."'");
                $Winter_MVC_wdk_bookings->db->where("date_to >= '".$date_to."'");
                $price_reservation = $Winter_MVC_wdk_bookings->price_m->get_by(array('post_id'=>$post_id), TRUE);
                /* manual validation */
                if( !$results ){
                    if($date_from == $date_to) {
                        $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Please set different dates', 'wpdirectorykit').'</div>';
                    }

                    if(empty($calendar->is_guests_disabled)){
                        $guests_number = intval(wmvc_show_data('guests_number_adults', $_POST, 0)) + intval(wmvc_show_data('guests_number_childs', $_POST, 0));
                        if(!empty(wmvc_show_data('guests', $calendar, 0, TRUE, TRUE)) && $guests_number > wmvc_show_data('guests', $calendar, 0, TRUE, TRUE)) {
                            $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Possible max', 'wpdirectorykit').' '.esc_html(wmvc_show_data('guests', $calendar, 0, TRUE, TRUE)).' '.esc_html__('guest(s)', 'wpdirectorykit').'</div>';
                        }
                    }

                    if((wmvc_show_data('сhildrens_allowed', $_POST, false) || wmvc_show_data('guests_number_childs', $_POST, 0) > 0) && !wmvc_show_data('is_children_acceptable', $calendar, false, TRUE, TRUE)) {
                        $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Only without children\'s', 'wpdirectorykit').'</div>';
                    }

                    if(wmvc_show_data('pets_allowed', $_POST, false) && !wmvc_show_data('is_pets_acceptable', $calendar, false, TRUE, TRUE)) {
                        $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Only without pet\'s', 'wpdirectorykit').'</div>';
                    }

                    $days = array(
                        '1'=>__('Monday', 'wpdirectorykit'),
                        '2'=>__('Tuesday', 'wpdirectorykit'),
                        '3'=>__('Wednesday', 'wpdirectorykit'),
                        '4'=>__('Thursday', 'wpdirectorykit'),
                        '5'=>__('Friday', 'wpdirectorykit'),
                        '6'=>__('Saturday', 'wpdirectorykit'),
                        '7'=>__('Sunday', 'wpdirectorykit'),
                    );
                
                    $day_num_from = date('N', strtotime($date_from));
                    $day_num_to = date('N', strtotime($date_to));

                    if(wmvc_show_data('changeover_day', $price_reservation, false, TRUE, TRUE) && (
                        wmvc_show_data('changeover_day', $price_reservation, false, TRUE, TRUE) != $day_num_from
                        || wmvc_show_data('changeover_day', $price_reservation, false, TRUE, TRUE) != $day_num_to
                        )
                    ) {
                        $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Changeover day is', 'wpdirectorykit').' '.esc_html($days[wmvc_show_data('changeover_day', $price_reservation, false, TRUE, TRUE)]).'</div>';
                    }

                    $hours_reservation = abs(strtotime($date_to) - strtotime($date_from))/(60*60);
                
                    if(wmvc_show_data('min_hours', $price_reservation, false, TRUE, TRUE) && wmvc_show_data('min_hours', $price_reservation, false, TRUE, TRUE) > $hours_reservation) {
                        $period = '';
                        $min_hours = intval(wmvc_show_data('min_hours', $price_reservation, false, TRUE, TRUE));
                        if((int)($min_hours/24) >= 1) {
                            $days = (int)($min_hours/24);
                            $period .= esc_html(sprintf(_nx(
                                                            '%1$s day',
                                                            '%1$s days',
                                                            $days,
                                                            'days',
                                                            'wpdirectorykit'
                                                    ), $days));

                            $min_hours -= $days * 24;
                        }
                        
                        if(!empty($min_hours)) {
                            $period .= ' '.esc_html(sprintf(_nx(
                                            '%1$s hour',
                                            '%1$s hours',
                                            (int)($min_hours),
                                            'hours',
                                            'wpdirectorykit'
                                    ), (int)($min_hours)));
                        }

                        $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Min', 'wpdirectorykit').' '.esc_html($period).'</div>';
                    }

                    if(wmvc_show_data('max_hours', $price_reservation, false, TRUE, TRUE) && wmvc_show_data('max_hours', $price_reservation, false, TRUE, TRUE) < $hours_reservation) {
                        $period = '';
                        $max_hours = intval(wmvc_show_data('max_hours', $price_reservation, false, TRUE, TRUE));
                        if((int)($max_hours/24) >= 1) {
                            $days = (int)($max_hours/24);
                            $period .= esc_html(sprintf(_nx(
                                                            '%1$s day',
                                                            '%1$s days',
                                                            $days,
                                                            'days',
                                                            'wpdirectorykit'
                                                    ), $days));

                            $max_hours -= $days * 24;
                        }
                        
                        if(!empty($max_hours)) {
                            $period .= ' '.esc_html(sprintf(_nx(
                                            '%1$s hour',
                                            '%1$s hours',
                                            (int)($max_hours),
                                            'hours',
                                            'wpdirectorykit'
                                    ), (int)($max_hours)));
                        }

                        $errors .= '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Max', 'wpdirectorykit').' '.esc_html($period).'</div>';
                    }
                }

                if( $results ) {
                    add_filter( 'eli/ajax-handler/filter_output', function($filter_output){
                        $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Date not available', 'wpdirectorykit').'</div>';
                        $filter_output['no_clear_from'] = true;
                        $filter_output['success'] = false;
                        return $filter_output;
                    } );
                } elseif( !empty($errors) ) {
                    add_filter( 'eli/ajax-handler/filter_output', function($filter_output) use ($errors) {
                        $filter_output['message'] = $errors;
                        $filter_output['success'] = false;
                        $filter_output['no_clear_from'] = true;
                        return $filter_output;
                    } );
                } else {
                    
                    if(wmvc_show_data('is_hour_enabled', $calendar, false) && !empty($data['date_from']) && !empty($data['date_to'])) {
                        $data['date_from'] = date('Y-m-d 00:00:00', strtotime($data['date_from']));
                        $data['date_to'] = date('Y-m-d 00:00:00', strtotime($data['date_to']));
                    }
                    $insert_id = $Winter_MVC_wdk_bookings->reservation_m->insert($data, NULL);

                    /* auto is_enable_noapprovements enabled */
                    if(wmvc_show_data('is_enable_noapprovements', $calendar, false)) {
                        /* if woo payment enabled */
                        if(wdk_get_option('wdk_bookings_enable_woocommerce_payments')) {
                            global $Winter_MVC_WDK;
                            $Winter_MVC_WDK->model('listingusers_m');
                            $Winter_MVC_WDK->model('listing_m');
                            $Winter_MVC_WDK->load_helper('listing');
                            $listing = $Winter_MVC_WDK->listing_m->get($post_id, TRUE);
                            $user_owner_id = NULL;
                            $user_data_owner = NULL;

                            if( wmvc_show_data('user_id_editor', $listing, '', TRUE, TRUE )) {
                                $user_owner_id = wmvc_show_data('user_id_editor', $listing, '', TRUE, TRUE );
                                if(wmvc_show_data('user_id', $user_listing, false, TRUE, TRUE )) {
                                    $user_data_owner = get_userdata( wmvc_show_data('user_id', $user_listing, false, TRUE, TRUE ) );
                                }
                            }
                                
                            if(empty($user_data_owner)) {
                                $user_data_owner = array(
                                    'display_name' => __('Administrator', 'wpdirectorykit'),
                                    'user_email' => get_bloginfo('admin_email'),
                                );
                            }
                            
                            /* auto create woo item and redirect to order */
                            $url_pay = '';
                         
                            if(class_exists( 'WooCommerce' )) {

                                $title = __('Reservation for', 'wpdirectorykit').' '.wdk_field_value ('post_title', $post_id).' #'.$post_id;
                                if($user_owner_id) {
                                    $title .= ' A'.$user_owner_id;
                                }
                                $title .= ' '.wdk_get_date($date_from).' - '.wdk_get_date($date_to); // The product's Title
                                
                                $post_args = array (
                                    'post_author' => $user_owner_id, // The user's ID
                                    'post_title' => $title, // The product's Title
                                    'post_content' => str_replace(PHP_EOL,'<br style="line-height: 0;">', $note), // The product's Title
                                    'post_type' => 'product',
                                    'post_status' => 'publish' // This could also be $data['status'];
                                );
                            
                                $woo_id = wp_insert_post( $post_args );
                                // If the post was created okay, let's try update the WooCommerce values.
                                if ( ! empty( $woo_id ) && function_exists( 'wc_get_product' ) ) {
                                    $product = wc_get_product( $woo_id );
                                    $product->set_virtual(true);
                                    $product->set_sold_individually(true);
                                    $product->set_sku( 'wdk-booking-' . $woo_id ); // Generate a SKU with a prefix. (i.e. 'pre-123') 
                                    $product->set_regular_price(wmvc_show_data('price', $price)); // Be sure to use the correct decimal price.
                                    $product->save(); // Save/update the WooCommerce order object.

                                    $terms = array( 'exclude-from-catalog', 'exclude-from-search' );
                                    wp_set_object_terms( $woo_id, $terms, 'product_visibility' );

                                    update_post_meta($woo_id, '_manage_stock', 'yes');
                                    update_post_meta($woo_id, '_stock', 1);

                                    $wdk_attach_id = NULL;
                                    $image_ids = explode(',', trim(wdk_show_data('listing_images' , $listing, '', TRUE, TRUE), ','));
    
                                    if(is_array($image_ids))
                                        $wdk_attach_id = $image_ids[0];
                            
                                    set_post_thumbnail( $woo_id, $wdk_attach_id );

                                    /* set woo product id for reservation */
                                    $update_data = array('woocommerce_product_id'=>$woo_id);
                                    $Winter_MVC_wdk_bookings->reservation_m->insert($update_data, $insert_id);

                                    if(function_exists('wc_get_cart_url')) {
                                        $url_pay = wdk_url_suffix(wc_get_cart_url(), 'add-to-cart='.esc_attr($woo_id).'&reservation_id='.esc_attr($insert_id));
                                    
                                        add_filter( 'eli/ajax-handler/filter_output', function($filter_output) use ($url_pay){
                                            $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-success" role="alert">'.esc_html__('Reservation created, auto redirect to order page', 'wpdirectorykit').'</div>';
                                            $filter_output['redirect'] = $url_pay;
                                            return $filter_output;
                                        } );
                                    }
                                }
                            } else {

                            }
                            /* message */
                            $data_message = array();
                            $data_message['user'] = get_userdata(get_current_user_id());
                            $data_message['user_owner'] = get_userdata(get_current_user_id());
                            $data_message['listing'] = $listing;
                            $data_message['user_owner'] = $user_data_owner;
                            $data_message['pay_link'] = $url_pay;
                            $data_message['hours_for_payment'] = false;
                            $data_message['reservation'] = $data;
                            $data_message['reservation_id'] = $insert_id;

                            if(wdk_get_option(('wdk_bookings_time_for_payment'))) {
                                $data_message['hours_for_payment'] = intval(wdk_get_option(('wdk_bookings_time_for_payment')));
                                
                                /* set date_expire for reservation */
                                $update_data = array('date_expire'=>esc_sql(date("Y-m-d H:i:s", strtotime("+".intval(wdk_get_option(('wdk_bookings_time_for_payment')))." hours"))));
                                $Winter_MVC_wdk_bookings->reservation_m->insert($update_data, $insert_id);
                            }

                            wdk_mail(wdk_show_data('user_email', $data_message['user'], '' , TRUE, TRUE), __('Reservation waiting for payment', 'wpdirectorykit'), $data_message, 'reservation_user_payment_notify');
                        } else {
                            /* manual pay */
                            global $Winter_MVC_WDK;
                            $user_owner_id = NULL;
                            $Winter_MVC_WDK->model('listingusers_m');
                            $Winter_MVC_WDK->model('listing_m');
                            $Winter_MVC_WDK->load_helper('listing');
                            $listing = $Winter_MVC_WDK->listing_m->get($post_id, TRUE);
                            $user = get_userdata( wmvc_show_data('user_id', $data) );

                            $user_owner_id = NULL;
                            $user_data_owner = NULL;
                            if(wmvc_show_data('user_id_editor', $listing, '', TRUE, TRUE )) {
                                $user_owner_id = wmvc_show_data('user_id_editor', $listing, '', TRUE, TRUE );
                                $user_data_owner = get_userdata( wmvc_show_data('user_id_editor', $listing, false, TRUE, TRUE ) );
                            }
                                
                            if(empty($user_data_owner)) {
                                $user_data_owner = array(
                                    'display_name' => __('Administrator', 'wpdirectorykit'),
                                    'user_email' => get_bloginfo('admin_email'),
                                );
                            }

                            /* message */
                            $data_message = array();
                            $data_message['user'] = $user;
                            $data_message['user_owner'] = $user_data_owner;
                            $data_message['listing'] = $listing;
                            $data_message['reservation'] = $data;
                            $data_message['reservation_id'] = $insert_id;
                            $data_message['data'] = array(
                                __('Reservation ID', 'wpdirectorykit')=> $insert_id,
                                __('Date From', 'wpdirectorykit')=>  wdk_get_date($date_from),
                                __('Date To', 'wpdirectorykit')=>  wdk_get_date($date_to),
                            );
                            
                            $nights = (int)abs(strtotime($date_to) - strtotime($date_from))/(60*60*24);
                            $guests_number = intval(wmvc_show_data('guests_number_adults', $_POST, 0)) + intval(wmvc_show_data('guests_number_childs', $_POST, 0));
                            foreach ($calendar_fees as $fee) {
                                if(!wmvc_show_data('is_activated', $fee, false,TRUE,TRUE)) continue;

                                $field = wdk_generate_slug(strtolower(esc_html(wmvc_show_data('title', $fee,'-',TRUE,TRUE)))); 
                                if(!wmvc_show_data('is_required', $fee, false,TRUE,TRUE) && !isset($_POST['fee_'.$field])) {
                                
                                } else {
                                    if(wmvc_show_data('calculation_base', $fee,'',TRUE,TRUE) == 'per_night') {
                                        $data_message['data'][__('Price', 'wpdirectorykit').' '.wmvc_show_data('title', $fee, '-', TRUE, TRUE)] = intval(wmvc_show_data('value', $fee,'',TRUE,TRUE)) * $nights;
                                    } else if(wmvc_show_data('calculation_base', $fee,'',TRUE,TRUE) == 'per_person') {
                                        $data_message['data'][__('Price', 'wpdirectorykit').' '.wmvc_show_data('title', $fee, '-', TRUE, TRUE)] = intval(wmvc_show_data('value', $fee,'',TRUE,TRUE)) * $guests_number;
                                    } else {
                                        $data_message['data'][__('Price', 'wpdirectorykit').' '.wmvc_show_data('title', $fee, '-', TRUE, TRUE)] = intval(wmvc_show_data('value', $fee,'',TRUE,TRUE));
                                    }
                                }
                            }

                            $data_message['data'][__('Total Price', 'wpdirectorykit')] = wmvc_show_data('price', $price).wdk_booking_currency_symbol();

                            $data_message['notes'] = $note;
                            if(wmvc_show_data('payment_info', $calendar, false, TRUE, TRUE)) {
                                $data_message['data'][__('Payment info', 'wpdirectorykit')] = str_replace(PHP_EOL,'<br style="line-height: 0;">', wmvc_show_data('payment_info', $calendar, false, TRUE, TRUE));
                            }
                          
                            $ret = wdk_mail(wdk_show_data('user_email', $data_message['user'], '' , TRUE, TRUE), __('Reservation approved, waiting for payment', 'wpdirectorykit'), $data_message, 'reservation_approved_visitor');
                          
                            $ret = wdk_mail(wdk_show_data('user_email', $data_message['user_owner'], '' , TRUE, TRUE), __('New Reservation approved, please confirm as paid when you receive payment', 'wpdirectorykit'), $data_message, 'reservation_approved_owner');
                    
                            if( $ret) {
                                add_filter( 'eli/ajax-handler/filter_output', function($filter_output) {
                                    $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-success" role="alert">'.esc_html__('Reservation sent, please check your email with payment details', 'wpdirectorykit').'</div>';
                                    return $filter_output;
                                } );
                            }
                            else
                            {
                                add_filter( 'eli/ajax-handler/filter_output', function($filter_output) {
                                    $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Server can\'t send emails, please use SMTP mail configuration.', 'wpdirectorykit').'</div>';
                                    $filter_output['no_clear_from'] = true;
                                    return $filter_output;
                                } );
                            }
                        }

                    } else {
                    /* auto is_enable_noapprovements disabled */
                        global $Winter_MVC_WDK;
                        $Winter_MVC_WDK->model('listingusers_m');
                        $Winter_MVC_WDK->model('listing_m');
                        $Winter_MVC_WDK->load_helper('listing');
                        
                        $data_listing = $Winter_MVC_WDK->listing_m->get($post_id, TRUE);
                        $user_client = get_userdata( wmvc_show_data('user_id', $data) );
                        /* owner data */
                        $user_owner = NULL;
                        $owner_email = get_bloginfo('admin_email');
                        
                        if(wmvc_show_data('user_id_editor', $data_listing, false, TRUE, TRUE )) {
                            $user_owner = get_userdata( wmvc_show_data('user_id_editor', $data_listing, false, TRUE, TRUE ) );
                            if($user_owner)
                                $owner_email = $user_owner->user_email;
                        }

                        /* waiting approve owner */
                        $data_message = array();
                        $data_message['user_owner'] = $user_owner;
                        $data_message['user_client'] = $user_client;
                        $data_message['reservation_id'] = $insert_id;
                        $data_message['reservation'] = $data; /* reservation data */
                        $data_message['listing'] = $data_listing;
                        $data_message['data'] = array(
                            __('Reservation ID', 'wpdirectorykit')=> $insert_id,
                            __('Date From', 'wpdirectorykit')=>  wdk_get_date($date_from),
                            __('Date To', 'wpdirectorykit')=>  wdk_get_date($date_to),
                        );
                        $nights = (int)abs(strtotime($date_to) - strtotime($date_from))/(60*60*24);
                        $guests_number = intval(wmvc_show_data('guests_number_adults', $_POST, 0)) + intval(wmvc_show_data('guests_number_childs', $_POST, 0));
                        foreach ($calendar_fees as $fee) {
                            if(!wmvc_show_data('is_activated', $fee, false,TRUE,TRUE)) continue;

                            $field = wdk_generate_slug(strtolower(esc_html(wmvc_show_data('title', $fee,'-',TRUE,TRUE)))); 
                            if(!wmvc_show_data('is_required', $fee, false,TRUE,TRUE) && !isset($_POST['fee_'.$field])) {
                            
                            } else {
                                if(wmvc_show_data('calculation_base', $fee,'',TRUE,TRUE) == 'per_night') {
                                    $data_message['data'][__('Price', 'wpdirectorykit').' '.wmvc_show_data('title', $fee, '-', TRUE, TRUE)] = intval(wmvc_show_data('value', $fee,'',TRUE,TRUE)) * $nights;
                                } else if(wmvc_show_data('calculation_base', $fee,'',TRUE,TRUE) == 'per_person') {
                                    $data_message['data'][__('Price', 'wpdirectorykit').' '.wmvc_show_data('title', $fee, '-', TRUE, TRUE)] = intval(wmvc_show_data('value', $fee,'',TRUE,TRUE)) * $guests_number;
                                } else {
                                    $data_message['data'][__('Price', 'wpdirectorykit').' '.wmvc_show_data('title', $fee, '-', TRUE, TRUE)] = intval(wmvc_show_data('value', $fee,'',TRUE,TRUE));
                                }
                            }
                        }

                        $data_message['data'][__('Total Price', 'wpdirectorykit')] = wmvc_show_data('price', $price).wdk_booking_currency_symbol();

                        $data_message['notes'] = $note;

                        if(wmvc_show_data ('сhildrens_allowed', $_POST, false))
                            $data_message['data'][__('Childrens', 'wpdirectorykit')] = 'yes';
                            
                        if(wmvc_show_data ('pets_allowed', $_POST, false))
                            $data_message['data'][__('Pets', 'wpdirectorykit')] = 'yes';
                            
                        if(empty($calendar->is_guests_disabled)){
                            if(wmvc_show_data ('guests_number_adults', $_POST, false))
                                $data_message['data'][__('Guests Adult', 'wpdirectorykit')] = sanitize_text_field(wmvc_show_data ('guests_number_adults', $_POST));
                            if(wmvc_show_data ('guests_number_childs', $_POST, false))
                                $data_message['data'][__('Guests Childs', 'wpdirectorykit')] = sanitize_text_field(wmvc_show_data ('guests_number_childs', $_POST));
                        }  
                        
                        if(wmvc_show_data ('Name', $_POST, false))
                            $data_message['data'][__('Name', 'wpdirectorykit')] = sanitize_text_field(wmvc_show_data ('Name', $_POST));
        
                        if(wmvc_show_data ('Email', $_POST, false))
                            $data_message['data'][__('Email', 'wpdirectorykit')] = sanitize_text_field(wmvc_show_data ('Email', $_POST));
        
                        if(wmvc_show_data ('Message', $_POST, false))
                            $data_message['data'][__('Message', 'wpdirectorykit')] = sanitize_text_field(wmvc_show_data ('Message', $_POST));
        
                        $ret =  wdk_mail( $owner_email, __('New Reservation Waiting For Approvement', 'wpdirectorykit'), $data_message, 'reservation_waiting_for_approve_owner');
                        
                        /* waiting approve to client */
                        $ret =  wdk_mail(wdk_show_data('user_email', $user_client, '' , TRUE, TRUE), __('Your reservation waiting approvement by owner', 'wpdirectorykit'), $data_message, 'reservation_waiting_for_approve_visitor');

                        if( $ret) {
                            add_filter( 'eli/ajax-handler/filter_output', function($filter_output){
                                $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-success" role="alert">'.esc_html__('Thanks on reservation, after owner approvement you will receive details for payment', 'wpdirectorykit').'</div>';
                                return $filter_output;
                            } );
                        }
                        else
                        {
                            add_filter( 'eli/ajax-handler/filter_output', function($filter_output) {
                                $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Server can\'t send emails, please use SMTP mail configuration.', 'wpdirectorykit').'</div>';
                                $filter_output['no_clear_from'] = true;
                                return $filter_output;
                            } );
                        }
                    }

                    
                }
                
                /* skip default message */
                $message_skip = true;
            }
        } 
      
        if(!$message_skip) {
            /* message data */
            if (!empty($post_id) && !empty($email)) {
                $data = array(
                    'post_id' => ($post_id),
                    'email_sender' =>  ($email),
                    'message' =>  ($message),
                    'json_object' => json_encode($_POST),
                    'user_id_sender' => get_current_user_id()
                );

                $insert_idmessage = $this->WMVC->messages_m->insert($data, null);
                if ($insert_idmessage && isset($_POST['listing_id'])) {
                    $data_mess= array();

                    foreach($_POST as $key => $value){
                        if($key=='element_id') continue;
                        if(in_array($key, array('eli_id','eli_page_id', 'eli_type','ID','filter','action','send_action_type', 'page_link', 'g-recaptcha-response'))) continue;

                        if(empty($value)) continue;
          
                        if(filter_var($value, FILTER_VALIDATE_URL ) || strpos( $value, 'http' ) !== FALSE) {
                            $data_mess []= '<p><strong>'.__(str_replace('_',' ', ucfirst($key)), 'wpdirectorykit').':</strong> <a href="'.esc_url($value).'">'.$value.'</a></p>';
                        } else {
                            $data_mess []= '<p><strong>'.__(str_replace('_',' ', ucfirst($key)), 'wpdirectorykit').':</strong> '.$value.'</p>';
                        }
                    }

                    global $Winter_MVC_WDK;
                    $Winter_MVC_WDK->model('listingusers_m');
                    $Winter_MVC_WDK->model('listing_m');
                    $Winter_MVC_WDK->load_helper('listing');
                    $data_listing = $Winter_MVC_WDK->listing_m->get($post_id, TRUE);

                    /* message for user */
                    if (!empty($email)) {
                        $data_message = array();
                        $data_message['email_data'] = $data;
                        $data_message['listing'] = $data;
                        $data_message['message'] =  '';

                        if(function_exists('run_wdk_bookings')) {
                            if(isset($_POST['date_from']) && isset($_POST['date_to']) && !empty($_POST['date_from']) && !empty($_POST['date_to'])) {
                                $data_message['message'] .= '<h2>'.__('Date reservation change to', 'wpdirectorykit').'</h2>';
                                $data_message['message'] .= '<p>'.__('Details from contact form', 'wpdirectorykit').'</p>';
                            }
                        }

                        $data_message['message'] .= '<p>'.__('Contact message related to listing', 'wpdirectorykit').' <a href="'.esc_url(get_permalink($data_listing)).'">'.esc_html(wmvc_show_data('post_title', $data_listing, '', TRUE, TRUE)).'</a></p>';

                        $data_message['message'] .= implode('', $data_mess);

                        $ret = wdk_mail($email, __('Your messages sent on', 'wpdirectorykit').' '.html_entity_decode(get_bloginfo('name')), $data_message, 'new_message');
                    }
                
                    /* message for admin */
                    $data_message = array();
                    $data_message['email_data'] = $data;
                    $data_message['message'] =  '';

                  
                    $message_title = __('New Message waiting', 'wpdirectorykit');

                    if(function_exists('run_wdk_bookings')) {

                        if(isset($_POST['date_from']) && isset($_POST['date_to']) && !empty($_POST['date_from']) && !empty($_POST['date_to'])) {
                            $data_message['message'] .= '<h2>'.__('Date reservation change to', 'wpdirectorykit').'</h2>';
                            $data_message['message'] .= '<p>'.__('Details from contact form', 'wpdirectorykit').'</p>';

                            $message_title = __('Contact message related to reservation on page', 'wpdirectorykit').' '.get_bloginfo('name');
                        }
                    }

                    $data_message['message'] .= '<p>'.__('Contact message related to listing', 'wpdirectorykit').' <a href="'.esc_url(get_permalink($data_listing)).'">'.esc_html(wmvc_show_data('post_title', $data_listing, '', TRUE, TRUE)).'</a></p>';
                    $data_message['message'] .= implode('', $data_mess);

                    /* owner data */
                    $user_owner = NULL;
                    $owner_email = get_bloginfo('admin_email');
                    if(wmvc_show_data('user_id_editor', $data_listing, false, TRUE, TRUE )) {
                        $user_owner = get_userdata( wmvc_show_data('user_id_editor', $data_listing, false, TRUE, TRUE ) );
                        if($user_owner)
                            $owner_email = $user_owner->user_email;
                    }

                    $ret = wdk_mail($owner_email, $message_title, $data_message, 'new_message', '', NULL, $email);
                    if( $ret) {
                        add_filter( 'eli/ajax-handler/filter_output', function($filter_output){
                            $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-success" role="alert">'.esc_html__('Thanks on message', 'wpdirectorykit').'</div>';
                            return $filter_output;
                        } );
                        add_filter( 'eli/ajax-handler/filter_from_data', function ($form_data = array()) {
                                /* disable double sent to admin mail */
                                $form_data['settings']['disable_mail_send'] = 1;
                                return $form_data;
                            }
                        );
                    }
                    else
                    {
                        add_filter( 'eli/ajax-handler/filter_output', function($filter_output) {
                            $filter_output['message'] = '<div class="elementinvader_addons_for_elementor_alert elementinvader_addons_for_elementor_alert-danger" role="alert">'.esc_html__('Server can\'t send emails, please use SMTP mail configuration.', 'wpdirectorykit').'</div>';
                            $filter_output['success'] = false;
                            $filter_output['no_clear_from'] = true;
                            return $filter_output;
                        } );
                    }


                    
                    
                }
            }
        }


    }
	
}
