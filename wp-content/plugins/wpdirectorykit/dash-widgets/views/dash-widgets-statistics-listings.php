<?php
/**
 * The template for Shortcode Listings list
 * This is the template that Shortcode listings list
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?> 
<div class="wdk-dashwidget-element" id="wdk_dashwidget_<?php echo esc_attr($id_element);?>">
    <div class="dash-widgets-statistics-usage wdk-wrap">

        <?php if(isset($stats)) foreach ($stats as $stat):?>
            <?php if(wmvc_show_data('link', $stat, false)):?>
                <a href="<?php echo esc_attr(wmvc_show_data('link', $stat,''));?>" title="<?php echo esc_html(wmvc_show_data('title', $stat,''));?>" class="stat-card <?php echo esc_attr(wmvc_show_data('class', $stat,''));?>">
            <?php else:?>
                <div class="stat-card <?php echo esc_attr(wmvc_show_data('class', $stat,''));?>">
            <?php endif;?>

                <div class="body">
                    <div class="count"><?php echo esc_html(wmvc_show_data('value', $stat,''));?></div>
                    <div class="title"><?php echo esc_html(wmvc_show_data('title', $stat,''));?></div>
                </div>
                <div class="side"><span class=" <?php echo esc_attr((wdk_show_data('icon', $stat,false)? wdk_show_data('icon', $stat) : 'dashicons dashicons-chart-bar'));?>"></span></div>

            <?php if(wmvc_show_data('link', $stat, false)):?>
                </a>
            <?php else:?>
                </div>
            <?php endif;?>
        <?php endforeach;?>
    </div>
</div>

