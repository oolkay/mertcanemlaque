<?php
/**
 * The template for Element Listing Section with Fields.
 * This is the template that elementor element, fields, images, list
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-listing-fields-section">
        <h3 class="section-title"><?php echo esc_html($section_label);?></h3>
        <div class="wdk-row">
            <?php if(!empty($section_data['fields']))foreach($section_data['fields'] as $field):?>
            <?php 
            if(!$is_edit_mode && !empty($wdk_listing_id)) {
                if(wmvc_show_data('is_visible_frontend', $field) != 1) {
                    continue;
                }

                if(wdk_field_value('category_id', $wdk_listing_id) && wdk_depend_is_hidden_field(wmvc_show_data('idfield', $field), wdk_field_value('category_id', $wdk_listing_id))) {
                    continue;
                } 

                if(wmvc_show_data('field_type', $field) == "CHECKBOX") {
                    if(wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id) != 1)
                        if(wmvc_show_data('hide_onempty_checkbox', $settings)) continue;
                } else {
                    if((empty(wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id)) ||wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id) == '0.00' ) &&  wmvc_show_data('hide_onempty', $settings))
                        continue;
                } 
            }
            ?>
            <div class="wdk-col 
                <?php if(wmvc_show_data('field_group_icon_enable', $settings, false) && wmvc_show_data('icon_id', $field, false)):?> icon_group <?php endif;?>  
                <?php if(wmvc_show_data('field_label_hide', $settings, false) == 'none'):?> no_label <?php endif;?>  
                <?php echo esc_html(wmvc_show_data('field_layout', $settings));?> <?php echo esc_html(wmvc_show_data('field_type', $field));?>
                ">
                <div class="field-group">
                    <?php if(wmvc_show_data('field_group_icon_enable', $settings, false) && wmvc_show_data('icon_id', $field, false)):?>
                        <span class="field_icon"> 
                            <img src="<?php echo esc_url(wdk_image_src($field, 'full',NULL,'icon_id'));?>" alt="<?php echo wmvc_show_data('field_label', $value);?>" class="wdk-icon">
                        </span>
                    <?php endif;?>
                    <span class="field_label"> 
                        <?php echo esc_html(wmvc_show_data('label_prefix', $settings));?><?php echo esc_html(wmvc_show_data('field_label', $field));?><?php echo esc_html(wmvc_show_data('label_suffix', $settings));?>
                    </span>
                    <span class="field_value">
                        <span class='prefix'><?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix',wmvc_show_data('prefix', $field), wmvc_show_data('idfield', $field)));?></span>
                        <span class='value'>
                            <?php
                                if(!$is_edit_mode && !empty($wdk_listing_id)) {
                                    $field_value = wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id);

                                    if(wmvc_show_data('field_type', $field) == "CHECKBOX") {
                                        if(wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id) == 1){
                                            $field_value = '<span class="field_checkbox_success">'.$this->generate_icon($settings['field_checkbox_icon_success']).'</span>';
                                        } else {
                                            $field_value = '<span class="field_checkbox_unsuccess">'.$this->generate_icon($settings['field_checkbox_icon_unsuccess']).'</span>';
                                        } 
                                    } 
                                    else if($field_value == '') {
                                        $field_value = '-';
                                    } 
                                    else if(wmvc_show_data('field_type', $field) == "INPUTBOX") {
                                        $field_value = wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id);

                                        if(strpos($field_value, 'vimeo.com') !== FALSE)
                                        {
                                            $field_value = wp_oembed_get($field_value, array("width"=>"800", "height"=>"450"));
                                        }
                                        elseif(strpos($field_value, 'watch?v=') !== FALSE)
                                        {
                                            $embed_code = substr($field_value, strpos($field_value, 'watch?v=')+8);
                                            $field_value =  wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code, array("width"=>"800", "height"=>"800"));
                                        }
                                        elseif(strpos($field_value, 'youtube.com/shorts/') !== FALSE)
                                        {
                                            $embed_code = substr($field_value, strpos($field_value, 'shorts')+7);
                                            $field_value = wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code,array("width"=>"800", "height"=>"455"));
                                        }
                                        elseif(strpos($field_value, 'youtu.be/') !== FALSE)
                                        {
                                            $embed_code = substr($field_value, strpos($field_value, 'youtu.be/')+9);
                                            $field_value = wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code, array("width"=>"800", "height"=>"455"));
                                        }
                                        elseif(filter_var($field_value, FILTER_VALIDATE_URL) !== FALSE && preg_match('/\.(mp4|flv|wmw|ogv|webm|ogg)$/i', $field_value))
                                        {
                                            $field_value  = '<video src="'.$field_value.'" controls></video> ';
                                        }
                                        elseif(filter_var($field_value , FILTER_VALIDATE_URL) !== FALSE) {
                                            $field_value  = '<a href="'.$field_value .'">'.$field_value .'</a>';
                                        }
                                        elseif(filter_var($field_value , FILTER_VALIDATE_EMAIL) !== FALSE) {
                                            $field_value  = '<a href="mailto:'.$field_value .'">'.$field_value .'</a>';
                                        }
                                        elseif(wdk_is_phone($field_value)) {
                                            $field_value  = '<a href="tel:'.wdk_filter_phone($field_value) .'">'.$field_value .'</a>';
                                        }
                                    }
                                    elseif(wmvc_show_data('idfield', $field) == 'category_id') {
                                        if(wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id)){
                                            $this->WMVC->model('category_m');
                                            $tree_data = $this->WMVC->category_m->get(wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id), TRUE);
                                            $field_value = wmvc_show_data('category_title', $tree_data);
                                        }
                                    }
                                    elseif(wmvc_show_data('idfield', $field) == 'location_id') {
                                        if(wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id)){
                                            $this->WMVC->model('location_m');
                                            $tree_data = $this->WMVC->location_m->get(wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id), TRUE);
                                            $field_value = wmvc_show_data('location_title', $tree_data);
                                        }
                                    }
                                    elseif(wmvc_show_data('field_type', $field) == 'DATE') {
                                        $field_value = wdk_field_value (wmvc_show_data('idfield', $field), $wdk_listing_id);
                                        $field_value = wdk_get_date($field_value );
                                    } elseif(wmvc_show_data('field_type', $field) == 'NUMBER') {

                                        /* price format implement */
                                        if(function_exists('run_wdk_currency_conversion') && wdk_currencies_is_price_field(wmvc_show_data('idfield', $field))) {
                                            /* if currency_conversion and field is price */
                                            $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal($field_value), wmvc_show_data('idfield', $field), FALSE));
                                            $field_value = esc_html(wdk_number_format_i18n($value));
                                        } elseif(wdk_field_option(wmvc_show_data('idfield', $field), 'is_price_format')) {
                                            /* if field enabled is_price_format and field type is number*/
                                            $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal($field_value), wmvc_show_data('idfield', $field), FALSE));
                                            $field_value = esc_html(wdk_number_format_i18n($value));
                                        } else {
                                            /* without number format */
                                            $field_value = apply_filters( 'wpdirectorykit/listing/field/value', $field_value, wmvc_show_data('idfield', $field));
                                        }
                                        
                                    } else {
                                        $field_value = apply_filters( 'wpdirectorykit/listing/field/value', $field_value, wmvc_show_data('idfield', $field));
                                    } 
                                } else {
                                    if(wmvc_show_data('field_type', $field) == "CHECKBOX"){
                                        $field_value = '<span class="field_checkbox_success">'.$this->generate_icon($settings['field_checkbox_icon_success']).'</span>';
                                    } elseif(wmvc_show_data('idfield', $field) == 'post_title') {
                                        $field_value = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Title', 'wpdirectorykit');
                                    } elseif(wmvc_show_data('idfield', $field) == 'address') {
                                        $field_value = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Address', 'wpdirectorykit');
                                    } elseif(wmvc_show_data('idfield', $field) == 'post_content') {
                                        $field_value = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Content', 'wpdirectorykit');
                                    } elseif(wmvc_show_data('idfield', $field) == 'post_title') {
                                        $field_value = esc_html__('Example', 'wpdirectorykit') .' '.esc_html__('Title', 'wpdirectorykit');
                                    }
                                    else{
                                        $field_value = esc_html__('Example', 'wpdirectorykit') .' '. wdk_field_label(wmvc_show_data('idfield', $field));
                                    }
                                    
                                    if(wmvc_show_data('is_visible_frontend', $field) != 1) {
                                        $field_value .= ' <span class="dashicons dashicons-hidden" style="color:red"></span>';
                                    }
                                    
                                }  
                                
                                
                                echo wp_kses_post(wdk_filter_decimal($field_value));
                            ?>
                        </span>
                        <span class='suffix'><?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix',wmvc_show_data('suffix', $field), wmvc_show_data('idfield', $field)));?></span>
                    </span>
                </div>
            </div>
            <?php endforeach;?>
        </div>

    </div>
</div>

