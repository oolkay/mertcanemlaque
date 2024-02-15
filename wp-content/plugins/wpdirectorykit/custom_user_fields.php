<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$wdk_user_fields_list = array(
    'wdk_phone' => __('Phone', 'wpdirectorykit'),
);

add_action( 'show_user_profile', function($user) use ($wdk_user_fields_list) {wdk_extra_user_profile_fields($user,$wdk_user_fields_list);} );
add_action( 'edit_user_profile', function($user) use ($wdk_user_fields_list) {wdk_extra_user_profile_fields($user,$wdk_user_fields_list);} );

function wdk_extra_user_profile_fields( $user,$wdk_user_fields_list ) {
    ?>
    <div class="wdk_postbox" style="display: block;">
        <div class="wdk_postbox-header">
            <h3><?php _e("Directory Profile Info", "wpdirectorykit"); ?></h3>
        </div>
        <div class="wdk_inside">
            <table class="form-table">
                <?php foreach($wdk_user_fields_list as $field_id => $field_name):?>
                <tr>
                    <th><label for="user_field_<?php echo esc_html($field_id);?>"><?php echo esc_html__($field_name, 'wpdirectorykit'); ?></label></th>
                    <td>
                        <input type="text" name="<?php echo esc_html($field_id);?>" id="user_field_<?php echo esc_html($field_id);?>" value="<?php echo esc_attr( get_the_author_meta( $field_id, $user->ID ) ); ?>" class="regular-text" /><br />
                        <span class="description"><?php _e("Please enter your",'wpdirectorykit'); ?> <?php echo esc_html__($field_name, 'wpdirectorykit'); ?>.</span>
                    </td>
                </tr>
                <?php endforeach;?>
            </table>
        </div>
    </div>
    <?php 
}

add_action( 'personal_options_update', function($user) use ($wdk_user_fields_list) {wdk_save_extra_user_profile_fields($user,$wdk_user_fields_list);} );
add_action( 'edit_user_profile_update', function($user) use ($wdk_user_fields_list) {wdk_save_extra_user_profile_fields($user,$wdk_user_fields_list);} );

function wdk_save_extra_user_profile_fields( $user,$wdk_user_fields_list ) {
    $user_id = wmvc_show_data('ID', $user);
    if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
        return;
    }
    
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }

    foreach ($wdk_user_fields_list as  $field_id => $field_name) {

        if(isset($_POST[$field_id]))
            update_user_meta( $user_id, $field_id, sanitize_text_field($_POST[$field_id]) );
    }
}