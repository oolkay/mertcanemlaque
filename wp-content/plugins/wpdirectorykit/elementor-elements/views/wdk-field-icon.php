<?php
/**
 * The template for Element Listing Field Icon.
 * This is the template that elementor element icon, img
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-field-icon">
        <img src="<?php echo esc_url(wdk_image_src(array('icon_id' => $field_icon), 'full', NULL, 'icon_id'));?>" alt="<?php echo wdk_image_alt($field_icon);?>">
    </div>
</div>

