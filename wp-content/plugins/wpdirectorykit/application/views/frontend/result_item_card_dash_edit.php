<?php
/**
 * The template for Result Item.
 *
 * This is the template that for result listing of listings preview
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php
$layout_type = 'grid';

if(isset($settings['layout_type'])) {
    $layout_type = $settings['layout_type'];
}

$content_button_icon = '<span class="dashicons dashicons-edit"></span>';
if(isset($settings['content_button_icon'])) {
    $content_button_icon = $settings['content_button_icon'];
}

$url = '#';

if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')) {
    if(function_exists('wdk_dash_url') && wdk_dash_url('dash_page=listings&function=edit') && current_user_can('edit_own_listings') && wdk_field_value('user_edit_listing', $listing)) {
        $url =  esc_url(wdk_dash_url('dash_page=listings&function=edit&id=' . wdk_field_value('post_id', $listing)));
    }
} else{
    $url = esc_url(admin_url('admin.php?page=wdk_listing&id='.wdk_field_value('post_id', $listing)));
}

$url_preview = get_permalink($listing);

$title_part =  wdk_resultitem_fields_section_value(1, 2, $listing);
$subtitle_part = wdk_resultitem_fields_section_value(1, 3, $listing);
$over_image_bottom =  wdk_resultitem_fields_section_value(1, 1, $listing);
$over_image_top = wdk_resultitem_fields_section_value(1, 0, $listing);
$features_part = wdk_resultitem_fields_section_value(1, 4, $listing);
$price_part = wdk_resultitem_fields_section_value(1, 5, $listing);
$resul_item_config = wdk_resultitem();
?>
<div class="wdk-listing-card <?php echo esc_attr($layout_type);?> <?php if(wdk_get_option('wdk_is_featured_enabled', FALSE) && wmvc_show_data('is_featured', $listing, '') == 1):?> is_featured <?php endif;?> <?php if(wmvc_show_data('is_multiline_enabled', $resul_item_config, '') == 1):?> is_multiline_enabled <?php endif;?>">
    <div class="wdk-thumbnail">
        <img src="<?php echo esc_url(wdk_image_src($listing, 'full'));?>" alt="<?php echo esc_attr(wmvc_show_data('post_title', $listing)) ;?>" class="wdk-image">
        <a href="<?php echo esc_url($url_preview);?>" target="_blank" class="wdk-thumbnail_link" title="<?php echo esc_attr(wmvc_show_data('post_title', $listing)) ;?>"></a>
        <?php if(!empty($over_image_top)):?>
            <div class="wdk-over-image-top">
            <?php foreach ($over_image_top as $key => $field):?>
                <span>
                <?php 
                    echo esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field))))
                        .esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field))))
                        .esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field))));
                ?>
                </span>
            <?php endforeach;?>
            </div>
        <?php endif;?>
        <?php if(!empty($over_image_bottom) || function_exists('run_wdk_favorites')):?>
            <div class="wdk-over-image-bottom">
                <?php  if(!empty($over_image_bottom)) foreach ($over_image_bottom as $key => $field):?>
                    <span class='wdk-item'>
                    <?php 
                        echo esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field))))
                            .esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field))))
                            .esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field))));
                    ?>
                    </span> 
                <?php endforeach;?>
            </div>
        <?php endif;?>
        <div class="overlay"></div>
    </div>
    <?php if($layout_type == 'list'):?>
    <div class="wdk-content">
    <?php endif;?>
        <?php if(!empty($title_part)):?>
            <div class="wdk-title">
                <h2 class="title">
                    <a href="<?php echo esc_url($url_preview);?>" target="_blank" title="<?php echo esc_attr(wmvc_show_data('post_title', $listing)) ;?>">
                        <?php foreach ($title_part as $key => $field):?>
                            <span>
                            <?php 
                                echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)))
                                    .esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field))))
                                    .esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)));
                            ?>
                            </span> 
                        <?php endforeach;?>
                    </a>
                </h2>
            </div>
        <?php endif;?>
        <?php if(!empty($subtitle_part)):?>
            <div class="wdk-subtitle-part">
                <?php foreach ($subtitle_part as $key => $field):?>
                    <span>
                    <?php 
                        echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)))
                            .esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field))))
                            .esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)));
                    ?>
                    </span> 
                <?php endforeach;?>
            </div>
        <?php endif;?>
        <?php if(!empty($features_part)):?>
            <div class="wdk-features-part">
                <?php foreach ($features_part as $key => $field):?><?php if(wmvc_show_data('field_type', $field) == 'CHECKBOX'):?>
                        <span><?php echo esc_html(esc_html(wmvc_show_data('field_label', $field, '')));?></span>
                    <?php else:?> 
                        <?php if(!wdk_filter_decimal(wmvc_show_data('value', $field))) continue;?>
                        <span>
                            <?php if(wmvc_show_data('icon_id', $field, false)):?>
                                <img src="<?php echo esc_url(wdk_image_src($field, 'full',NULL,'icon_id'));?>" alt="<?php echo esc_attr(esc_html(wmvc_show_data('field_label', $field, '')));?>" class="wdk-icon">
                            <?php endif;?>

                            <?php if(wmvc_show_data('is_label_disable', $resul_item_config, false) != 1):?>
                                <?php echo esc_html(esc_html(wmvc_show_data('field_label', $field, '')));?>: 
                            <?php endif;?>

                            <?php 
                                echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)))
                                    .esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field))))
                                    .esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)));
                            ?>
                        </span>
                    <?php endif;?> 
                <?php endforeach;?>
            </div>
        <?php endif;?>
        <div class="wdk-divider"></div>
        <div class="wdk-footer">
            <div class="wdk-left">
                <div class="wdk-price">
                <?php if(!empty($price_part)):?>
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
                    <?php endif;?>
                </div>
            </div>
            <div class="wdk-right">
                <a href="<?php echo esc_url($url);?>" class="wdk-btn"><?php echo wmvc_show_data('content_button_text', $settings, '');?><?php wdk_viewe($content_button_icon); ?></a>
            </div>
        </div>
    <?php if($layout_type == 'list'):?>
    </div>
    <?php endif;?>
</div>