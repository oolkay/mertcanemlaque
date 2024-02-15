<?php
/**
 * The template for Element  Button Login.
 * This is the template that elementor element login, logout, link
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="wdk-element wdk-button-login" id="wdk_el_<?php echo esc_html($id_element);?>">
    <?php if(!is_user_logged_in()):?>
    <a href="<?php echo esc_attr(wmvc_show_data('link_not_login_url', $settings));?>" id="<?php echo esc_attr(wmvc_show_data('link_not_login_id', $settings));?>" class="wdk-element-button login">
        <?php if(wmvc_show_data('link_not_login_icon_position', $settings) == 'left') :?>
            <?php \Elementor\Icons_Manager::render_icon( $settings['link_not_login_icon'], [ 'aria-hidden' => 'true' ] ); ?>
        <?php endif;?>
        <?php echo esc_html(wmvc_show_data('link_not_login_text', $settings));?>
        <?php if(wmvc_show_data('link_not_login_icon_position', $settings) == 'right') :?>
            <?php \Elementor\Icons_Manager::render_icon( $settings['link_not_login_icon'], [ 'aria-hidden' => 'true' ] ); ?>
        <?php endif;?>
    </a>
    <?php endif;?>

    <?php if(is_user_logged_in()):?>
    <a href="<?php echo esc_attr(wmvc_show_data('link_login_url', $settings));?>" id="<?php echo esc_attr(wmvc_show_data('link_login_id', $settings));?>" 
        title="<?php echo esc_attr(wmvc_show_data('link_login_text_attr', $settings));?>" 
        class="wdk-element-button logout">
        <?php if(wmvc_show_data('link_login_icon_position', $settings) == 'left') :?>
            <?php \Elementor\Icons_Manager::render_icon( $settings['link_login_icon'], [ 'aria-hidden' => 'true' ] ); ?>
        <?php endif;?>
        <?php echo esc_html(wmvc_show_data('link_login_text', $settings));?>
        <?php if(wmvc_show_data('link_login_icon_position', $settings) == 'right') :?>
            <?php \Elementor\Icons_Manager::render_icon( $settings['link_login_icon'], [ 'aria-hidden' => 'true' ] ); ?>
        <?php endif;?>
    </a>
    <span class="dash-span">
        <a href="<?php echo esc_attr(wmvc_show_data('link_dash_url', $settings));?>" 
            title="<?php echo esc_attr(wmvc_show_data('link_dash_text_attr', $settings));?>"
            id="<?php echo esc_attr(wmvc_show_data('link_dash_id', $settings));?>" class="wdk-element-button dash logout">
            <?php if(wmvc_show_data('link_dash_icon_position', $settings) == 'left') :?>
                <?php \Elementor\Icons_Manager::render_icon( $settings['link_dash_icon'], [ 'aria-hidden' => 'true' ] ); ?>
            <?php endif;?>
            <?php echo esc_html(wmvc_show_data('link_dash_text', $settings));?>
            <?php if(wmvc_show_data('link_dash_icon_position', $settings) == 'right') :?>
                <?php \Elementor\Icons_Manager::render_icon( $settings['link_dash_icon'], [ 'aria-hidden' => 'true' ] ); ?>
            <?php endif;?>
        </a>
        <?php if(function_exists('run_wdk_messages_chat') && function_exists('wdk_dash_url')):?>
            <?php
            global $Winter_MVC_wdk_messages_chat;
            $Winter_MVC_wdk_messages_chat->model('messageschat_m');
            $total_new_message = $Winter_MVC_wdk_messages_chat->messageschat_m->total_relationship_user(get_current_user_id(), array('chat_is_readed IS NULL'=>NULL));
            if($total_new_message):
            ?>
                <a href="<?php echo esc_url(wdk_dash_url('dash_page=messages&function=chat'));?>" 
                    title="<?php echo esc_html__('New messages', 'wpdirectorykit');?>" class="count_messages">
                    <?php echo esc_html($total_new_message);?>
                </a>
            <?php endif;?>
        <?php endif;?>
    </span>
    <?php endif;?>
</div>

