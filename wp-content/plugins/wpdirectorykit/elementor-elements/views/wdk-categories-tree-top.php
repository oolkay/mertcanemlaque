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
?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-categories-tree-top">
        <?php if(!empty($categories_primary)):?>
        <div class="wdk-primary">
            <div class="wdk-row">
                <?php foreach ($categories_primary as $key => $value):?>
                    <div class="wdk-col">
                        <div class="category-card">
                            <div class="body">
                                <h3 class="title"><?php echo wmvc_show_data('category_title', $value);?></h3>
                                <div class="sub">
                                    <?php
                                        echo esc_html(wdk_sprintf(_nx(
                                                '%1$s Listing',
                                                '%1$s Listings',
                                                wmvc_show_data('listings_counter', $value, '0'),
                                                'profile listings count',
                                                'wpdirectorykit'
                                        ), wmvc_show_data('listings_counter', $value, '0')));
                                    ?>
                                </div>
                            </div>
                            <div class="thumbnail">
                                <?php if(wmvc_show_data('primary_layout_image_type', $settings) == 'icon'):?>
                                    <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'icon_id', 'icon_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-icon">
                                <?php elseif(wmvc_show_data('primary_layout_image_type', $settings) == 'image'):?>
                                    <img class="jsplaceholder" onerror="this.src = '<?php echo esc_url(wdk_placeholder_image_src());?>';" src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'image_id','image_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-image">
                                <?php elseif(wmvc_show_data('primary_layout_image_type', $settings) == 'font_icon'):?>
                                    <span class="wdk-font-icon" style="background-color: <?php echo esc_attr(wmvc_show_data('category_color', $value));?>;"><i class="<?php echo esc_attr(wmvc_show_data('font_icon_code', $value));?>"></i></span>
                                <?php endif;?>
                            </div>
                            <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results" class="complete_link"></a>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
        <?php endif;?>
        <?php if(!empty($categories_secondary)):?>
        <div class="wdk-secondary">
            <div class="wdk-row">
                <?php foreach ($categories_secondary as $key => $value):?>
                    <div class="wdk-col">
                        <div class="category-card">
                            <div class="body">
                                <h3 class="title"><?php echo wmvc_show_data('category_title', $value);?></h3>
                                <div class="sub">
                                    <?php
                                        echo esc_html(wdk_sprintf(_nx(
                                                '%1$s Listing',
                                                '%1$s Listings',
                                                wmvc_show_data('listings_counter', $value, '0'),
                                                'profile listings count',
                                                'wpdirectorykit'
                                        ), wmvc_show_data('listings_counter', $value, '0')));
                                    ?>
                                </div>
                            </div>
                            <div class="thumbnail">
                                <?php if(wmvc_show_data('secondary_layout_image_type', $settings) == 'icon'):?>
                                    <img src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'icon_id', 'icon_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-icon">
                                <?php elseif(wmvc_show_data('secondary_layout_image_type', $settings) == 'image'):?>
                                    <img class="jsplaceholder" onerror="this.src = '<?php echo esc_url(wdk_placeholder_image_src());?>';" src="<?php echo esc_url(wdk_image_src($value, 'full',NULL,'image_id','image_path'));?>" alt="<?php echo wmvc_show_data('category_title', $value);?>" class="wdk-image">
                                <?php elseif(wmvc_show_data('secondary_layout_image_type', $settings) == 'font_icon'):?>
                                    <span class="wdk-font-icon" style="background-color: <?php echo esc_attr(wmvc_show_data('category_color', $value));?>;"><i class="<?php echo esc_attr(wmvc_show_data('font_icon_code', $value));?>"></i></span>
                                <?php endif;?>
                            </div>
                            <a href="<?php echo esc_url(wdk_url_suffix($results_page,'search_category='.wmvc_show_data('idcategory', $value)));?>#results" class="complete_link"></a>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
        <?php endif;?>
    </div>
</div>