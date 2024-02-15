<?php
/**
 * The template for Element Categories List.
 * This is the template that elementor element list, categories results
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
} 

?>
<?php
$results_page = wmvc_show_data('conf_link', $settings);
if(!is_array($results_page) && !empty($results_page)) {
    $results_page = get_permalink($results_page);
} else {
    $results_page = get_permalink(wdk_get_option('wdk_results_page'));
}

$categories_count = 0;
$more_helper_class = '';
?>

<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-categories-tree">

        <div class="wdk-row">
            <?php if(!empty($categories)):?>
                <div class="wdk-col">
                    <div class="category-block">
                        <?php foreach ($categories as $key => $value):?>
                            <?php if(wmvc_show_data('parent_id', $value) == 0):?>
                            <?php
                            if($categories_count == wmvc_show_data('conf_limit_root', $settings)):
                                

                                if(wmvc_show_data('show_more', $settings) == 'yes'){?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="wdk-col wdk-col-full wdk-col-full-always row-more-toogle">
                                    <a href="#" class="btn-more"><?php echo esc_html__('Show More', 'wpdirectorykit');?></a>
                                </div>
                                <div class="wdk-col">
                                    <div class="category-block more wdk-hidden">
                                <?php 
                                $more_helper_class = ' more wdk-hidden';
                                $categories_count++;
                                $categories_sub_count = 0;
                                } else {
                                    break;
                                }

                            else:

                                $categories_count++;
                                $categories_sub_count = 0;
                            ?>
                            <?php if($key != 0):?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="wdk-col">
                                    <div class="category-block <?php echo esc_attr($more_helper_class);?>">
                                <?php endif;?>
                            <?php endif;?>
                                    <h3 class="title <?php if(wmvc_show_data('show_icon', $settings) == 'yes' && wmvc_show_data('layout_image_type', $settings) == 'image'):?> image_top <?php endif;?>">
                                        <?php if(wmvc_show_data('show_icon', $settings) == 'yes'):?>
                                            <?php if(wmvc_show_data('layout_image_type', $settings) == 'icon'):?>
                                                <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results">
                                                    <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'icon_id', 'icon_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-icon">
                                                </a>
                                            <?php elseif(wmvc_show_data('layout_image_type', $settings) == 'image'):?>
                                                <a class="wdk-d-block" href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results">
                                                    <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'image_id','image_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-image">
                                                </a>
                                            <?php elseif(wmvc_show_data('layout_image_type', $settings) == 'font_icon'):?>
                                                <span class="wdk-font-icon" style="background-color: <?php echo esc_attr(wmvc_show_data('category_color', $value));?>;"><i class="<?php echo esc_attr(wmvc_show_data('font_icon_code', $value));?>"></i></span>
                                            <?php endif;?>
                                        <?php endif;?>
                                        <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results">
                                            <?php echo wmvc_show_data('category_title', $value);?>
                                        </a>
                                    </h3>
                                    <ul class="wdk-categories">
                            <?php else:?>
                                <?php
                                    if($categories_sub_count >= wmvc_show_data('conf_limit_sub', $settings))  {
                                        if(wmvc_show_data('show_more_subs',$settings, false) == 'yes') {
                                            if($categories_sub_count == wmvc_show_data('conf_limit_sub', $settings)):
                                            ?>
                                                <li class="wdk-item wdk-item-toggle">
                                                    <a href="#"  class="wdk-link wdk-link-show-more">
                                                        <?php \Elementor\Icons_Manager::render_icon( $settings['item_icon_i'], [ 'aria-hidden' => 'true' ] );?>
                                                        <span class="wdk-title"><?php echo esc_html__('Show more...', 'wpdirectorykit');?></span>
                                                    </a>
                                                </li>
                                            <?php endif;
                                        } else {
                                            continue;
                                        }
                                    }
                                    $categories_sub_count++;
                                ?>
                                <li class="wdk-item <?php echo ($categories_sub_count > wmvc_show_data('conf_limit_sub', $settings)) ? 'more-sub' : '';?>" <?php echo ($categories_sub_count > wmvc_show_data('conf_limit_sub', $settings)) ? 'style="display: none;"' : '';?>>
                                    <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results"  class="wdk-link">
                                        <?php \Elementor\Icons_Manager::render_icon( $settings['item_icon_i'], [ 'aria-hidden' => 'true' ] );?>
                                        <span class="wdk-title"><?php echo wmvc_show_data('category_title', $value);?></span>
                                        <span class="wdk-count"><?php echo wmvc_show_data('listings_counter', $value, 0);?></span>
                                    </a>
                                </li>
                            <?php endif;?>
                        <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            <?php else:?>
                <div class="wdk-col wdk-col-full wdk-col-full-always">
                    <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Categories not found', 'wpdirectorykit');?></p>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>