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
    <div class="wdk-last-search">
        <?php if(wmvc_show_data('text_before', $settings, false)):?>
            <span <?php $this->editing_element('text_before', 'basic', array('class'=>'prefix')); ?>><?php echo esc_html(wmvc_show_data('text_before', $settings, false));?></span>
        <?php endif;?>
        <a href="<?php echo esc_url($last_search_url);?>" class="value"><?php echo esc_html($last_search);?></a>
        <?php if(wmvc_show_data('text_after', $settings, false)):?>
            <span <?php $this->editing_element('text_after', 'basic', array('class'=>'suffix')); ?>><?php echo esc_html(wmvc_show_data('text_after', $settings, false));?></span>
        <?php endif;?>
    </div>
</div>

