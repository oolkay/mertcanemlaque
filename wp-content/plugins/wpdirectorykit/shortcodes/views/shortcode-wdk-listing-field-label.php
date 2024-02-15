<?php
/**
 * The template for Shortcode Listing Field Label.
 * This is the template that Shortcode listing field label
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php if($settings['enable_html'] == true):?>
    <span class="wdk-shortcode wdk-element <?php echo esc_attr(wmvc_show_data('custom_class', $settings));?>" id="wdk_el_<?php echo esc_attr(wmvc_show_data('id', $settings));?>">
        <?php echo esc_html($field_label); ?>
    </span>
<?php else:?>
    <?php echo esc_html($field_label); ?>
<?php endif;?>
