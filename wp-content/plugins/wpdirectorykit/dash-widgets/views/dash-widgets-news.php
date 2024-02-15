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
    <div class="wdk-news wdk-wrap">
        <table class="table table-bordered footable">
            <tbody id="wdk_script_news_table">
                <tr>
                    <td colspan="5"><?php echo __('Loading in progress', 'wpdirectorykit');?></td>
                </tr>      
            </tbody>
        </table>
        <div class="footer">
            <a href="https://wpdirectorykit.com/wp/blog/" title="<?php echo esc_attr__('Show More', 'wpdirectorykit'); ?>" target="_blank" class="button">
                <?php echo esc_html__('Show More', 'wpdirectorykit'); ?>
            </a>
        </div>
    </div>
</div>

