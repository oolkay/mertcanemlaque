<?php
/**
 * The template for Element Listing Agent fields.
 * This is the template that elementor element meta
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-listing-agent-field">
        <?php echo esc_html($field_prefix).esc_html($field_value).esc_html($field_suffix); ?>
    </div>
</div>

