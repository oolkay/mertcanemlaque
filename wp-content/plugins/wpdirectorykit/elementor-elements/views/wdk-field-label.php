<?php
/**
 * The template for Element Listing Field Label.
 * This is the template that elementor element
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-field-label">
        <?php echo esc_html($field_prefix).wp_kses_post($field_label).esc_html($field_suffix); ?>
    </div>
</div>

