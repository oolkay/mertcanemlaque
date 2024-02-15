<?php
/**
 * The template for Element Listing Field Value.
 * This is the template that elementor element, link, iframe, images
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-field-value">
    <?php echo empty(wmvc_show_data('html_tag', $settings, 'span')) ? '<span>' : '<'.wmvc_show_data('html_tag', $settings, 'span').'>'; ?>
        <span class='prefix'><?php echo esc_html($field_prefix);?></span>
        <span class="value"><?php echo wp_kses_post(wdk_filter_decimal($field_value));?></span>
        <span class='suffix'><?php echo esc_html($field_suffix);?></span>
        <?php echo empty(wmvc_show_data('html_tag', $settings, 'span')) ? '</span>' : '</'.wmvc_show_data('html_tag', $settings, 'span').'>'; ?>
    </div>
</div>

