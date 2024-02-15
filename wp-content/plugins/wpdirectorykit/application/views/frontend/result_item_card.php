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

$infobox = false;
if(isset($settings['infobox'])) {
    $infobox = true;
}

$content_button_icon = '<i aria-hidden="true" class="fa fa-angle-right"></i>';
if(isset($settings['content_button_icon'])) {
    $content_button_icon = $settings['content_button_icon'];
}

$url = get_permalink($listing);
$title_part =  wdk_resultitem_fields_section_value(1, 2, $listing);
$subtitle_part = wdk_resultitem_fields_section_value(1, 3, $listing);
$over_image_bottom =  wdk_resultitem_fields_section_value(1, 1, $listing);
$over_image_top = wdk_resultitem_fields_section_value(1, 0, $listing);
$features_part = wdk_resultitem_fields_section_value(1, 4, $listing);
$price_part = wdk_resultitem_fields_section_value(1, 5, $listing);
$resul_item_config = wdk_resultitem();
/* true if thumbnail printed */
$thumbnail_printed = false;

$slides_count_limit = 5;

global $wdk_listing_result_id;
$wdk_listing_result_id = wmvc_show_data('post_id', $listing);

?>
<div class="wdk-listing-card <?php echo esc_attr($layout_type);?> <?php if($layout_type == 'carousel'):?> grid <?php endif;?> <?php if(wdk_get_option('wdk_is_featured_enabled', FALSE) && wmvc_show_data('is_featured', $listing, '') == 1):?> is_featured <?php endif;?> <?php if(!$infobox && wmvc_show_data('is_multiline_enabled', $resul_item_config, '') == 1):?> is_multiline_enabled <?php endif;?>">
    <div class="wdk-thumbnail">

        <?php if(wdk_get_option('wdk_card_slider_enable') && !$infobox):?>
            <?php
                $images = wdk_listing_images_fast_access ($listing, 'full');
            ?>
            <?php if(!empty($images)):?>
                <div class="wdk_js_gallery_slider_box">
                    <div class="wdk_js_gallery_slider">
                        <?php if(wdk_get_option('wdk_card_video_field')):?>
                            <?php $media = wdk_generate_media_field(wdk_field_value (wdk_get_option('wdk_card_video_field'), $listing));?>
                            <?php if($media):?>
                                <div class="wdk-image media">
                                    <?php echo wp_kses_post($media);?>
                                </div>
                            <?php endif;?>
                        <?php endif;?>
                        <?php foreach ($images as $key=>$image_src):?>
                            <?php if($key >= $slides_count_limit) break;?>
                            <div>
                                <img src="<?php echo esc_url($image_src);?>" alt=""  class="wdk-image">
                                <a href="<?php echo esc_url($url);?>" class="wdk-thumbnail_link" title="<?php echo esc_attr(wmvc_show_data('post_title', $listing)) ;?>"></a>
                            </div>
                        <?php endforeach;?>
                    </div>
                    <div class="wdk_js_gallery_slider-carousel_arrows">
                        <a href="#prev" class="wdk-slider-prev">
                            <?php if(!empty($settings['thumbn_slider_arrow_left']['value'])):?>
                                <?php \Elementor\Icons_Manager::render_icon( $settings['thumbn_slider_arrow_left'], [ 'aria-hidden' => 'true' ] ); ?>
                            <?php else:?>
                                <span class="dashicons dashicons-arrow-left-alt2"></span>
                            <?php endif;?>
                        </a>
                        <a href="#next" class="wdk-slider-next">
                            <?php if(!empty($settings['thumbn_slider_arrow_right']['value'])):?>
                                <?php \Elementor\Icons_Manager::render_icon( $settings['thumbn_slider_arrow_right'], [ 'aria-hidden' => 'true' ] ); ?>
                            <?php else:?>
                                <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php endif;?>
                        </a>
                    </div>
                </div>
                <?php $thumbnail_printed = true;?>
            <?php else:?>
                <?php if(wdk_get_option('wdk_card_video_field')):?>
                    <?php $media = wdk_generate_media_field(wdk_field_value (wdk_get_option('wdk_card_video_field'), $listing));?>
                    <?php if($media):?>
                        <div class="wdk-image media">
                            <?php echo wp_kses_post($media);?>
                        </div>
                        <?php $thumbnail_printed = true;?>
                    <?php endif;?>
                <?php endif;?>
            <?php endif;?>

            <?php if(false):?>
            <div class="wdk_js_gallery_slider">
                <div class="links wdk-hidden">
                    <?php foreach ($images as $image_src):?>
                        <a href="<?php echo esc_url($image_src);?>" title="">
                            <img src="<?php echo esc_url($image_src);?>" alt="" />
                        </a>
                    <?php endforeach;?>
                </div>
                <div
                    id="blueimp-image-carousel"
                    class="blueimp-gallery blueimp-gallery-carousel"
                    aria-label="image carousel"
                    >
                    <div class="slides" aria-live="off">
                    </div>
                    <a
                        class="prev"
                        aria-controls="blueimp-image-carousel"
                        aria-label="previous slide"
                    ></a>
                    <a
                        class="next"
                        aria-controls="blueimp-image-carousel"
                        aria-label="next slide"
                    ></a>
                    <a
                        class="play-pause"
                        aria-controls="blueimp-image-carousel"
                        aria-label="play slideshow"
                        aria-pressed="true"
                        role="button"
                    ></a>
                </div>
            </div>
            <?php endif;?>
        <?php endif;?>

        <?php if(!$thumbnail_printed && wdk_get_option('wdk_card_video_field') && !$infobox):?>
            <?php $media = wdk_generate_media_field(wdk_field_value (wdk_get_option('wdk_card_video_field'), $listing));?>
            <?php if($media):?>
                <div class="wdk-image media">
                    <?php echo wp_kses_post($media);?>
                </div>
                <?php $thumbnail_printed = true;?>
            <?php endif;?>
        <?php endif;?>

        <?php if(!$thumbnail_printed):?>
            <?php if(wdk_get_option('wdk_card_video_enable')):?>
                <?php
                    $image_src = wdk_listing_media_src($listing);
                ?>
                <?php if(wdk_file_extension_type($image_src) == 'video'):?>
                    <div class="wdk-image media">
                        <video controls src="<?php echo esc_url($image_src);?>" alt="<?php echo esc_attr(wmvc_show_data('post_title', $listing));?>"></video>
                    </div>
                <?php elseif(wdk_file_extension_type($image_src)  == 'image'):?>
                    <img src="<?php echo esc_url($image_src);?>" class="wdk-image" alt="<?php echo esc_attr(wmvc_show_data('post_title', $listing));?>">
                <?php endif;?>
            <?php else:?>
                <img src="<?php echo esc_url(wdk_image_src($listing, 'full'));?>" alt="<?php echo esc_attr(wmvc_show_data('post_title', $listing)) ;?>" class="wdk-image">
            <?php endif;?>
        <?php endif;?>

        <a href="<?php echo esc_url($url);?>" class="wdk-thumbnail_link" title="<?php echo esc_attr(wmvc_show_data('post_title', $listing)) ;?>"></a>
        <?php if(!empty($over_image_top)):?>
            <div class="wdk-over-image-top">
            <?php foreach ($over_image_top as $key => $field):?>
                <?php if(wmvc_show_data('field_id', $field) == 'agent_image'):?>
                    <?php $user = wdk_get_user_data(wmvc_show_data('user_id_editor', $listing));?>
                    <?php if(!empty($user)): ?>
                        <?php if(!empty($user['profile_url'])) :?>
                            <a href="<?php echo esc_url($user['profile_url']);?>" class="agent_logo_link"><img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>"></a>
                        <?php else:?>
                            <img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>">
                        <?php endif;?>
                    <?php endif;?>
                <?php continue; endif;?>
                <span class='wdk-field-<?php echo esc_attr(wmvc_show_data('field_id', $field, ''));?>'>
                <?php 
                    echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)));

                    if(wdk_field_option(wmvc_show_data('field_id', $field), 'is_price_format') && wdk_field_option(wmvc_show_data('field_id', $field), 'field_type') == 'NUMBER') {
                        $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field), FALSE));
                        echo esc_html(wdk_number_format_i18n($value));
                    } else {
                        echo esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', do_shortcode(wdk_filter_decimal(wmvc_show_data('value', $field))), wmvc_show_data('field_id', $field))));
                    }
                    
                    echo esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)));
                ?>
                </span>
            <?php endforeach;?>
            </div>
        <?php endif;?>
        <?php if(!empty($over_image_bottom) || function_exists('run_wdk_favorites')):?>
            <div class="wdk-over-image-bottom">
            <?php  if(!empty($over_image_bottom)) foreach ($over_image_bottom as $key => $field):?>
                <?php if(wmvc_show_data('field_id', $field) == 'agent_image'):?>
                    <?php $user = wdk_get_user_data(wmvc_show_data('user_id_editor', $listing));?>
                    <?php if(!empty($user)): ?>
                        <?php if(!empty($user['profile_url'])) :?>
                            <a href="<?php echo esc_url($user['profile_url']);?>" class="agent_logo_link"><img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>"></a>
                        <?php else:?>
                            <img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>">
                        <?php endif;?>
                    <?php endif;?>
                <?php continue; endif;?>
                <span class='wdk-item wdk-field-<?php echo esc_attr(wmvc_show_data('field_id', $field, ''));?>'>
                <?php 
                    echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)));

                    if(wdk_field_option(wmvc_show_data('field_id', $field), 'is_price_format') && wdk_field_option(wmvc_show_data('field_id', $field), 'field_type') == 'NUMBER') {
                        $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field), FALSE));
                        echo esc_html(wdk_number_format_i18n($value));
                    } else {
                        echo esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', do_shortcode(wdk_filter_decimal(wmvc_show_data('value', $field))), wmvc_show_data('field_id', $field))));
                    }
                    
                    echo esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)));
                ?>
                </span> 
            <?php endforeach;?>

            
            <?php if(function_exists('run_wdk_favorites')): ?>
                <span class="wdk-favorites-actions">
                    <a href="#" data-post_type="wdk-listing" data-post_id="<?php echo esc_attr(wmvc_show_data('post_id', $listing));?>" class="wdk-add-favorites-action <?php echo (esc_attr($favorite_added))?'wdk-hidden':''; ?>"  data-ajax="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>">
                        <i class="fa fa-heart-o"></i>
                    </a>
                    <a href="#" data-post_type="wdk-listing" data-post_id="<?php echo esc_attr(wmvc_show_data('post_id', $listing));?>" class="wdk-remove-favorites-action <?php echo (!esc_attr($favorite_added))?'wdk-hidden':''; ?>" data-ajax="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>">
                        <i class="fa fa-heart"></i>
                    </a>
                    <i class="fa fa-spinner fa-spin fa-custom-ajax-indicator"></i>
                </span>
            <?php endif; ?>

            <?php if(shortcode_exists('wdk-compare-listing-button') && !get_option('wdk_compare_disable_on_result_items')):?>
                <?php echo do_shortcode('[wdk-compare-listing-button wdk_listing_id="'.esc_attr(wmvc_show_data('post_id', $listing)).'" ]');?>
            <?php endif;?>
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
                    <a href="<?php echo esc_url($url);?>" title="<?php echo esc_attr(wmvc_show_data('post_title', $listing)) ;?>">
                        <?php foreach ($title_part as $key => $field):?>
                            <?php if(wmvc_show_data('field_id', $field) == 'agent_image'):?>
                                <?php $user = wdk_get_user_data(wmvc_show_data('user_id_editor', $listing));?>
                                <?php if(!empty($user)): ?>
                                    <?php if(!empty($user['profile_url'])) :?>
                                        <a href="<?php echo esc_url($user['profile_url']);?>" class="agent_logo_link"><img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>"></a>
                                    <?php else:?>
                                        <img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>">
                                    <?php endif;?>
                                <?php endif;?>
                            <?php continue; endif;?>
                            <span class='wdk-field-<?php echo esc_attr(wmvc_show_data('field_id', $field, ''));?>'>
                            <?php 
                                echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)));

                                if(wdk_field_option(wmvc_show_data('field_id', $field), 'is_price_format') && wdk_field_option(wmvc_show_data('field_id', $field), 'field_type') == 'NUMBER') {
                                    $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field), FALSE));
                                    echo esc_html(wdk_number_format_i18n($value));
                                } else {
                                    echo esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', do_shortcode(wdk_filter_decimal(wmvc_show_data('value', $field))), wmvc_show_data('field_id', $field))));
                                }
                                
                                echo esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)));
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
                    <?php if(wmvc_show_data('field_id', $field) == 'agent_image'):?>
                        <?php $user = wdk_get_user_data(wmvc_show_data('user_id_editor', $listing));?>
                        <?php if(!empty($user)): ?>
                            <?php if(!empty($user['profile_url'])) :?>
                                <a href="<?php echo esc_url($user['profile_url']);?>" class="agent_logo_link"><img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>"></a>
                            <?php else:?>
                                <img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>">
                            <?php endif;?>
                        <?php endif;?>
                    <?php continue; endif;?>
                    <span class="wdk-field-<?php echo esc_attr(wmvc_show_data('field_id', $field, ''));?>">
                    <?php 
                        echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)));

                        if(wdk_field_option(wmvc_show_data('field_id', $field), 'is_price_format') && wdk_field_option(wmvc_show_data('field_id', $field), 'field_type') == 'NUMBER') {
                            $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field), FALSE));
                            echo esc_html(wdk_number_format_i18n($value));
                        } else {
                            echo esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', do_shortcode(wdk_filter_decimal(wmvc_show_data('value', $field))), wmvc_show_data('field_id', $field))));
                        }
                        
                        echo esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)));
                    ?>
                    </span> 
                <?php endforeach;?>
            </div>
        <?php endif;?>
        <?php if(wdk_get_option('wdk_sub_listings_enable') && isset($settings['related_fields_list'])): ?>
            <div class="wdk-sublistings-part">
                <?php if(!empty(wmvc_show_data('listing_related_ids', $listing))):?>
                    <ul>
                    <?php foreach (explode(',',wmvc_show_data('listing_related_ids', $listing, '')) as $key => $child_idlisting):?>
                        <?php if(!wdk_field_value('is_activated', $child_idlisting) || !wdk_field_value('is_approved', $child_idlisting)) continue; ?>
                        <li>
                            <?php foreach($settings['related_fields_list'] as $field):?>
                                <?php
                                if(strpos($field['field'],'__') !== FALSE){
                                    $field['field'] = substr($field['field'], strpos($field['field'],'__')+2);
                                }
                                ?>

                                <?php if($field['is_link'] == 'yes'):?>
                                    <a href="<?php echo get_permalink($child_idlisting); ?>" title="<?php echo esc_attr__('View','wpdirectorykit');?>" target="blank">
                                <?php else:?>
                                    <span>
                                <?php endif;?>

                                <?php if($field['is_stars'] == 'yes'):?>
                                    <?php $stars = round(floatval(wdk_field_value( $field['field'], $child_idlisting)),2);?>
                                    <span class="stars-lst">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <=$stars): ?>
                                            <span><i class="fas fa-star star-active"></i></span>
                                        <?php elseif( abs($stars - $i) < 1): ?>
                                            <span><i class="fas fa-star-half-alt"></i></span>
                                        <?php else: ?>
                                            <span><i class="far fa-star innactive"></i></span>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    </span>
                                <?php else:?>
                                    <?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wdk_field_option($field['field'],'prefix'), $field['field']));?>
                                    <?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/value', wdk_field_value_on_type($field['field'], $child_idlisting, '-'), $field['field']));?>
                                    <?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/suffix', wdk_field_option($field['field'],'suffix'), $field['field']));?>
                                <?php endif;?>

                                <?php if($field['is_link'] == 'yes'):?>
                                    </a> 
                                <?php else:?>
                                    </span>
                                <?php endif;?>

                            <?php endforeach;?>
                        </li>
                    <?php endforeach;?>
                    </ul>
                <?php endif;?>
            </div>
        <?php endif;?>
        <?php if(!empty($features_part)):?>
            <div class="wdk-features-part">
                <?php foreach ($features_part as $key => $field):?><?php if(wmvc_show_data('field_id', $field) == 'agent_image'):?>
                                <?php $user = wdk_get_user_data(wmvc_show_data('user_id_editor', $listing));?>
                                <?php if(!empty($user)): ?>
                                    <?php if(!empty($user['profile_url'])) :?>
                                        <a href="<?php echo esc_url($user['profile_url']);?>" class="agent_logo_link"><img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>"></a>
                                    <?php else:?>
                                        <img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>">
                                    <?php endif;?>
                                <?php endif;?>
                            <?php continue; endif;?><?php if(wmvc_show_data('field_type', $field) == 'CHECKBOX'):?>
                        <span class="wdk-field-<?php echo esc_attr(wmvc_show_data('field_id', $field, ''));?>"><?php echo esc_html(esc_html(wmvc_show_data('field_label', $field, '')));?></span>
                    <?php else:?> 
                        <?php if(!wdk_filter_decimal(wmvc_show_data('value', $field))) continue;?>
                        <span class="wdk-field-item wdk-field-<?php echo esc_attr(wmvc_show_data('field_id', $field, ''));?>">
                            <?php if(wmvc_show_data('icon_id', $field, false)):?>
                                <img src="<?php echo esc_url(wdk_image_src($field, 'full',NULL,'icon_id'));?>" alt="<?php echo esc_attr(esc_html__(wmvc_show_data('field_label', $field, ''),'wpdirectorykit'));?>" class="wdk-icon">
                            <?php elseif(wmvc_show_data('is_label_disable', $resul_item_config, false) == 1):?>
                                <?php echo '<span class="wdk-rc-field-label">'.esc_html__(wmvc_show_data('field_label', $field, ''),'wpdirectorykit').':</span>';?> 
                            <?php endif;?>

                            <?php if(wmvc_show_data('is_label_disable', $resul_item_config, false) != 1):?>
                                <?php echo '<span class="wdk-rc-field-label">'.esc_html__(wmvc_show_data('field_label', $field, ''),'wpdirectorykit').':</span>';?> 
                            <?php endif;?>

                            <?php 
                                echo '<span class="wdk-rc-field-prefix">'.esc_html__(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)),'wpdirectorykit').'</span>';

                                if(wdk_field_option(wmvc_show_data('field_id', $field), 'is_price_format') && wdk_field_option(wmvc_show_data('field_id', $field), 'field_type') == 'NUMBER') {
                                    $value = strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', wdk_filter_decimal(wmvc_show_data('value', $field)), wmvc_show_data('field_id', $field), FALSE));
                                    echo '<span class="wdk-rc-field-value">'.esc_html__(wdk_number_format_i18n($value),'wpdirectorykit').'</span>';
                                } else {
                                    echo '<span class="wdk-rc-field-value">'.esc_html__(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', do_shortcode(wdk_filter_decimal(wmvc_show_data('value', $field))), wmvc_show_data('field_id', $field))),'wpdirectorykit').'</span>';
                                }
                                
                                echo '<span class="wdk-rc-field-suffix">'.esc_html__(apply_filters( 'wpdirectorykit/listing/field/suffix', wmvc_show_data('suffix', $field), wmvc_show_data('field_id', $field)),'wpdirectorykit').'</span>';
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
                        <?php if(wmvc_show_data('field_id', $field) == 'agent_image'):?>
                            <?php $user = wdk_get_user_data(wmvc_show_data('user_id_editor', $listing));?>
                            <?php if(!empty($user)): ?>
                                <?php if(!empty($user['profile_url'])) :?>
                                    <a href="<?php echo esc_url($user['profile_url']);?>" class="agent_logo_link"><img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>"></a>
                                <?php else:?>
                                    <img class="agent_logo" src="<?php echo esc_url(wmvc_show_data('avatar', $user));?>" alt="<?php echo esc_attr(wmvc_show_data('display_name', $user['userdata']));?>">
                                <?php endif;?>
                            <?php endif;?>
                        <?php continue; endif;?>
                        <span class="wdk-field-<?php echo esc_attr(wmvc_show_data('field_id', $field, ''));?>">
                            <?php echo esc_html(apply_filters( 'wpdirectorykit/listing/field/prefix', wmvc_show_data('prefix', $field), wmvc_show_data('field_id', $field)));?>
                            <?php if(function_exists('run_wdk_currency_conversion') && wdk_field_option(wmvc_show_data('field_id', $field), 'field_type') == 'NUMBER'):?>
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
                                    <?php echo esc_html(strip_tags(apply_filters( 'wpdirectorykit/listing/field/value', (do_shortcode(wdk_filter_decimal(wmvc_show_data('value', $field)))), wmvc_show_data('field_id', $field))));?>
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

<?php
$wdk_listing_result_id = null;
?>