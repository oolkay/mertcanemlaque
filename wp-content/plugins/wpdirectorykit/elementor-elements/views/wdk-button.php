<?php
/**
 * The template for Element Button.
 * This is the template that elementor element button, link
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <a href="<?php echo esc_attr(wmvc_show_data('link_url', $settings));?>" id="<?php echo esc_attr(wmvc_show_data('link_id', $settings));?>" class="wdk-element-button">
        <?php if(wmvc_show_data('link_icon_position', $settings) == 'left') :?>
            <?php \Elementor\Icons_Manager::render_icon( $settings['link_icon'], [ 'aria-hidden' => 'true' ] ); ?>
        <?php endif;?>
        <?php echo esc_html(wmvc_show_data('link_text', $settings));?>
        <?php if(wmvc_show_data('link_icon_position', $settings) == 'right') :?>
            <?php \Elementor\Icons_Manager::render_icon( $settings['link_icon'], [ 'aria-hidden' => 'true' ] ); ?>
        <?php endif;?>
    </a>
</div>

