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
    <div class="wdk-field-files <?php if(wmvc_show_data('enable_js_gallery', $settings, false, TRUE, TRUE)):?> wdk_js_gallery <?php endif;?>">
        <?php if(count($images)>0):?>
            <?php $images_opened_tag = false; ?>
            <?php foreach($images as $image):?>
                <?php 
                    if($is_edit_mode) {
                        $src = $image;
                        $image = array('src' => $src,'title'=>esc_html__('Image Title', 'wpdirectorykit'));
                    } else {
                        $src = $image['src'];
                    }
                ?>
                <?php 
                    if(!in_array(wdk_file_extension($image['src']), array('jpg','jpeg','bmp','png','webp')))
                        continue;
                    
                if(!$images_opened_tag):?>
                    <div class="wdk-row">
                <?php $images_opened_tag=true; endif;?>
                        
                <div class="wdk-col">
                    <figure>
                        <a class="wdk-listing-image-card" href="<?php echo esc_url($image['src']);?>">
                            <img src="<?php echo esc_url($src);?>" class="wdk-listing-image <?php if($settings['enable_fixed_height']!='yes'):?> auto_height <?php endif;?>" alt="<?php echo esc_attr(wmvc_show_data('alt',$image,'',TRUE,TRUE));?>">
                        </a>
                        
                        <figcaption>
                            <a class="skip" href="<?php echo esc_url($image['src']);?>" target="_blank">
                                <?php echo esc_html(wmvc_show_data('title',$image,'',TRUE,TRUE));?>
                            </a>
                        </figcaption>
                    </figure>
                </div>
            <?php endforeach;?>
            <?php if($images_opened_tag):?>
                </div>
            <?php endif;?>
        <?php endif;?>

        <?php if(wmvc_show_data('files_section_hide', $settings, '',TRUE,TRUE) !='yes'):?>
            <?php if($is_edit_mode):?>
                <div class="files-row">
                    <ul class="files">
                        <li class="list-item">
                            <a class="file-link" href="<?php echo esc_url(WPDIRECTORYKIT_URL.'public/img/filetype/xlsx.png');?>" target="_blank" title="<?php echo esc_attr__('Excel Worksheet', 'wpdirectorykit');?> ">
                                <img src="<?php echo esc_url(WPDIRECTORYKIT_URL.'public/img/filetype/xlsx.png');?>" class="wdk-listing-file-icon" alt="">
                                <?php echo esc_html__('Excel Worksheet', 'wpdirectorykit');?>                  
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="file-link" href="<?php echo esc_url(WPDIRECTORYKIT_URL.'public/img/filetype/docx.png');?>" target="_blank" title="<?php echo esc_attr__('Document Worksheet', 'wpdirectorykit');?> ">
                                <img src="<?php echo esc_url(WPDIRECTORYKIT_URL.'public/img/filetype/docx.png');?>" class="wdk-listing-file-icon" alt="">
                                <?php echo esc_html__('Document Worksheet', 'wpdirectorykit');?>                  
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="file-link" href="<?php echo esc_url(WPDIRECTORYKIT_URL.'public/img/filetype/pdf.png');?>" target="_blank" title="<?php echo esc_attr__('Pdf Worksheet', 'wpdirectorykit');?>">
                                <img src="<?php echo esc_url(WPDIRECTORYKIT_URL.'public/img/filetype/pdf.png');?>" class="wdk-listing-file-icon" alt="">
                                <?php echo esc_html__('Pdf Worksheet', 'wpdirectorykit');?>                  
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="file-link" href="<?php echo esc_url(WPDIRECTORYKIT_URL.'public/img/filetype/txt.png');?>" target="_blank" title="<?php echo esc_attr__('Txt Worksheet', 'wpdirectorykit');?> ">
                                <img src="<?php echo esc_url(WPDIRECTORYKIT_URL.'public/img/filetype/txt.png');?>" class="wdk-listing-file-icon" alt="">
                                <?php echo esc_html__('Txt Worksheet', 'wpdirectorykit');?>                  
                            </a>
                        </li>
                    </ul>
                </div>
            <?php else:?>
                <?php if(count($images)>0):?>
                    <?php $files_opened_tag = false; ?>
                    <?php foreach($images as $image):?>
                        <?php 
                            if(in_array(wdk_file_extension($image['src']), array('jpg','jpeg','bmp','png','webp')))
                                continue;

                            if(!$files_opened_tag):?>
                            <div class="files-row">
                                <ul class="files">
                            <?php  $files_opened_tag = true; endif;?>
                            
                            <?php
                            if(file_exists(WPDIRECTORYKIT_PATH.'/public/img/filetype/'.wdk_file_extension($image['src']).'.png')) {
                                $src = WPDIRECTORYKIT_URL.'public/img/filetype/'.wdk_file_extension($image['src']).'.png';
                            } else {
                                $src = WPDIRECTORYKIT_URL.'public/img/filetype/_blank.png';
                            }
                        ?>

                        <li class="list-item">
                            <a class="file-link" href="<?php echo esc_url($image['src']);?>" target="_blank" title="<?php echo esc_attr(wmvc_show_data('title',$image,'',TRUE,TRUE));?>">
                                <img src="<?php echo esc_url($src);?>" class="wdk-listing-file-icon" alt="<?php echo esc_attr(wmvc_show_data('alt',$image,'',TRUE,TRUE));?>">
                                <?php echo esc_html(wmvc_show_data('title',$image,'',TRUE,TRUE));?>
                            </a>
                        </li>
                    <?php endforeach;?>
                    <?php if($files_opened_tag):?>
                            </ul>
                        </div>
                    <?php endif;?>
                <?php endif;?>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>

