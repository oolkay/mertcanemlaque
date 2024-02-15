<?php
/**
 * The template for Shortcode.
 * This is the template that Shortcode listing field value
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<span class="wdk-shortcode wdk-element <?php echo esc_attr(wmvc_show_data('custom_class', $settings));?>" id="wdk_el_<?php echo esc_attr(wmvc_show_data('id', $settings));?>">
    <div class="wdk-listing-fields-section">
        <h3 class="section-title"><?php echo esc_html($section_label);?></h3>
        <div class="wdk-row">
            <?php if(!empty($section_data['fields']))foreach($section_data['fields'] as $field):?>
            <?php 
                if(wmvc_show_data('is_visible_frontend', $field) != 1) {
                    continue;
                }

                if(wdk_field_value('category_id', $post_id) && wdk_depend_is_hidden_field(wmvc_show_data('idfield', $field), wdk_field_value('category_id', $post_id))) {
                    continue;
                } 

                if(wmvc_show_data('field_type', $field) == "CHECKBOX") {
                    if(wdk_field_value (wmvc_show_data('idfield', $field), $post_id) != 1)
                        if(wmvc_show_data('hide_onempty_checkbox', $settings)) continue;
                } else {
                    if((empty(wdk_field_value (wmvc_show_data('idfield', $field), $post_id)) ||wdk_field_value (wmvc_show_data('idfield', $field), $post_id) == '0.00' ) &&  wmvc_show_data('hide_onempty', $settings))
                        continue;
                } 
            ?>
            <div class="wdk-col 
                <?php if(wmvc_show_data('field_group_icon_enable', $settings) == 'yes' && wmvc_show_data('icon_id', $field, false)):?> icon_group <?php endif;?>  
                <?php if(wmvc_show_data('field_label_hide', $settings, false) == 'none'):?> no_label <?php endif;?>  
                <?php echo esc_html(wmvc_show_data('field_layout', $settings));?> <?php echo esc_html(wmvc_show_data('field_type', $field));?>
                ">
                <?php if(wmvc_show_data('field_group_icon_enable', $settings) == 'yes' && wmvc_show_data('icon_id', $field, false)):?>
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
                            $field_value = wdk_field_value (wmvc_show_data('idfield', $field), $post_id);
                            if(wmvc_show_data('field_type', $field) == "CHECKBOX") {
                                if(wdk_field_value (wmvc_show_data('idfield', $field), $post_id) == 1){
                                    $field_value = '<span class="field_checkbox_success"><span class="dashicons dashicons-yes"></span></span>';
                                } else {
                                    $field_value = '<span class="field_checkbox_unsuccess"><span class="dashicons dashicons-no"></span></span>';
                                } 
                            } else if(wmvc_show_data('field_type', $field) == "INPUTBOX") {
                                $field_value = wdk_field_value (wmvc_show_data('idfield', $field), $post_id);

                                if(strpos($field_value, 'vimeo.com') !== FALSE)
                                {
                                    $field_value = wp_oembed_get($field_value, array("width"=>"800", "height"=>"450"));
                                }
                                elseif(strpos($field_value, 'watch?v=') !== FALSE)
                                {
                                    $embed_code = substr($field_value, strpos($field_value, 'watch?v=')+8);
                                    $field_value =  wp_oembed_get('https://www.youtube.com/watch?v='.$embed_code, array("width"=>"800", "height"=>"455"));
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
                                if(wdk_field_value (wmvc_show_data('idfield', $field), $post_id)){
                                    $this->WMVC->model('category_m');
                                    $tree_data = $this->WMVC->category_m->get(wdk_field_value (wmvc_show_data('idfield', $field), $post_id), TRUE);
                                    $field_value = wmvc_show_data('category_title', $tree_data);
                                }
                            }
                            elseif(wmvc_show_data('idfield', $field) == 'location_id') {
                                if(wdk_field_value (wmvc_show_data('idfield', $field), $post_id)){
                                    $this->WMVC->model('location_m');
                                    $tree_data = $this->WMVC->location_m->get(wdk_field_value (wmvc_show_data('idfield', $field), $post_id), TRUE);
                                    $field_value = wmvc_show_data('location_title', $tree_data);
                                }
                            }
                            elseif(wmvc_show_data('field_type', $field) == 'DATE') {
                                $field_value = wdk_field_value (wmvc_show_data('idfield', $field), $post_id);
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
                            
                            echo wp_kses_post(wdk_filter_decimal($field_value));
                        ?>
                    </span>
                    <span class='suffix'><?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix',wmvc_show_data('suffix', $field), wmvc_show_data('idfield', $field)));?></span>
                </span>
            </div>
            <?php endforeach;?>
        </div>
    </div>
</span>
