<?php
/**
 * The template for Element Listing Images.
 * This is the template that elementor element images, results
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-field-images <?php if(wmvc_show_data('enable_js_gallery', $settings, false, TRUE, TRUE)):?> wdk_js_gallery <?php endif;?>">
        <div class="wdk-row">
            <?php if(count($images)>0):?>
                <?php foreach($images as $image):?>
                    <?php if(!wmvc_show_data('wdk_listing_video_disabled',$settings, false) && wdk_file_extension_type(wmvc_show_data('src',$image)) == 'video'):?>
                        <div class="wdk-col">
                            <?php if(wmvc_show_data('enable_js_gallery', $settings, false, TRUE, TRUE)):?> 
                                <a class="wdk-listing-image-card" href="<?php echo esc_url($image);?>">
                            <?php else:?>
                                <div class="wdk-listing-image-card">
                            <?php endif;?>
                                <video controls src="<?php echo esc_url(wmvc_show_data('src',$image));?>"  alt="<?php echo esc_attr(wmvc_show_data('alt',$image));?>" class="wdk-listing-image-card wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>"></video>
                            <?php if(wmvc_show_data('enable_js_gallery', $settings, false, TRUE, TRUE)):?> 
                                </a>
                            <?php else:?>
                                </div>
                            <?php endif;?>
                        </div>
                    <?php elseif(wdk_file_extension_type(wmvc_show_data('src',$image)) == 'image'):?>
                        <div class="wdk-col">
                            <?php if(wmvc_show_data('enable_js_gallery', $settings, false, TRUE, TRUE)):?> 
                                <a class="wdk-listing-image-card" href="<?php echo esc_url($image);?>">
                            <?php else:?>
                                <div class="wdk-listing-image-card">
                            <?php endif;?>
                                <img src="<?php echo esc_url($image);?>" class="wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>" alt="<?php echo esc_attr__('thumb', 'wpdirectorykit');?>">
                            <?php if(wmvc_show_data('enable_js_gallery', $settings, false, TRUE, TRUE)):?> 
                                </a>
                            <?php else:?>
                                </div>
                            <?php endif;?>
                        </div>
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        </div>
    </div>
</div>

