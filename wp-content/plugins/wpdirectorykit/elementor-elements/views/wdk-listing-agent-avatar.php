<?php
/**
 * The template for Element Listing Agent Avatar.
 * This is the template that elementor element avatar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-listing-agent-avatar">
        <img src="<?php echo esc_url($user_avatar_url);?>" class='wdk-avatar' alt="<?php echo esc_html__('Avatar','wpdirectorykit');?>">
    </div>
</div>
