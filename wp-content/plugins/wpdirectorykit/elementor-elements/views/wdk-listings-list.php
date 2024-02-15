<?php
/**
 * The template for Element Listings list.
 * This is the template that elementor element listings, list
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-listings-list">
        <div class="wdk-row">
            <?php if(!empty($results)):?>
                <?php foreach($results as $listing):?>
                    <?php
                        $url = get_permalink($listing);
                        $title_post =  wdk_resultitem_fields_section_value(1, 2, $listing);
                        $title_part =  wdk_resultitem_fields_section_value(1, 2, $listing);
                        $price_part = wdk_resultitem_fields_section_value(1, 5, $listing);
                    ?>
                    <div class="wdk-col">
                        <div class="listing-item">
                            <div class="listing-img-sec">
                                <a href="<?php echo esc_url($url);?>" title="<?php echo esc_attr(wdk_show_data('post_title', $listing, '', TRUE, TRUE));?>">
                                    <img src="<?php echo esc_url(wdk_image_src($listing, 'full'));?>" alt="<?php echo esc_attr(wdk_show_data('post_title', $listing, '', TRUE, TRUE));?>">
                                </a>
                            </div><!--prop-img-sec enf-->
                            <div class="listing-inf-sec">
                                <?php if(!empty($title_part)):?>
                                    <h3 class="title">
                                        <a href="<?php echo esc_url($url);?>">
                                            <?php foreach ($title_part as $key => $field):?>
                                                <span>
                                                    <?php echo wmvc_show_data('prefix', $field).wdk_filter_decimal(wmvc_show_data('value', $field)).wmvc_show_data('suffix', $field);?>
                                                </span> 
                                            <?php endforeach;?>
                                        </a>
                                    </h3>
                                <?php endif;?>
                                <?php if(!empty($price_part)):?>
                                    <div class="price">
                                        <?php foreach ($price_part as $key => $field):?>
                                            <span>
                                                <?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)));?>
                                                <?php if(function_exists('run_wdk_currency_conversion')):?>
                                                        <?php  
                                                            $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field), FALSE));
                                                            echo esc_html(wdk_number_format_i18n($value));
                                                        ?>
                                                <?php else:?>
                                                    <?php if(wdk_field_option(wmvc_show_data('field_id', $field), 'is_price_format') && wdk_field_option(wmvc_show_data('field_id', $field), 'field_type') == 'NUMBER'):?>
                                                        <?php  
                                                            $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field), FALSE));
                                                            echo esc_html(wdk_number_format_i18n($value));
                                                        ?>
                                                    <?php else:?>
                                                        <?php echo esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', (wdk_filter_decimal(wmvc_show_data('value', $field))), wmvc_show_data('field_id', $field))));?>
                                                    <?php endif;?>
                                                <?php endif;?>
                                                <?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)));?>
                                            </span> 
                                        <?php endforeach;?>
                                    </div>
                                <?php endif;?>
                            </div><!--prop-inf-sec enf-->
                        </div>
                    </div>
                <?php endforeach;?> 
            <?php else:?>
                <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Results not found', 'wpdirectorykit');?></p>
            <?php endif;?>
        </div>
    </div>
</div>

