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
<?php if($settings['enable_html'] == true):?>
    <span class="wdk-shortcode wdk-element <?php echo esc_attr(wmvc_show_data('custom_class', $settings));?>" id="wdk_el_<?php echo esc_attr(wmvc_show_data('id', $settings));?>">
        <span class='prefix'><?php echo esc_html($field_prefix);?></span>
        <span class='value'><?php wdk_viewe(wdk_filter_decimal($field_value));?></span>
        <span class='suffix'><?php echo esc_html($field_suffix);?></span>
    </span>
<?php else:?>
    <?php wdk_viewe(wdk_filter_decimal($field_value));?>
<?php endif;?>
