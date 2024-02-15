<?php
/**
 * The template for Edit Messages.
 *
 * This is the template that form edit
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('View/Edit Messages', 'wpdirectorykit'); ?></h1>
    <br /><br />
    <div class="wdk-body">
        <form method="post" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" enctype="multipart/form-data" novalidate="novalidate">
            <?php wp_nonce_field( 'wdk-messages-edit_'.wmvc_show_data('idmessage', $db_data, 0), '_wpnonce'); ?>
            <div class="postbox" style="display: block;">
                <div class="postbox-header">
                    <h3><?php echo __('Main Data', 'wpdirectorykit'); ?></h3>
                </div>
                <div class="inside">
                    <?php
                    $form->messages('class="alert alert-danger"',  __('Successfully saved', 'wpdirectorykit'));
                    ?>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="input_post_id"><?php echo __('Listing id', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <input name="post_id" type="text" id="input_post_id" value="<?php echo wmvc_show_data('post_id', $db_data, ''); ?>" placeholder="<?php echo esc_html__('Post Id', 'wpdirectorykit');?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="input_date"><?php echo __('Date', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <input name="date" type="text" id="input_date" value="<?php echo wmvc_show_data('date', $db_data, ''); ?>" placeholder="<?php echo esc_html__('Address', 'wpdirectorykit');?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="message"><?php echo __('Message', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php wp_editor(wmvc_show_data('message', $db_data, ''), 'message', array('media_buttons' => FALSE)); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="is_readed"><?php echo __('Make as read', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <input name="is_readed" type="checkbox" id="is_featured" value="1" <?php echo !empty(wmvc_show_data('is_readed', $db_data, ''))?'checked':''; ?>> <?php echo __('Mark it as readed','wpdirectorykit'); ?>
                                </td>
                            </tr>
                            <?php 
                                $json_data = wmvc_show_data('json_object', $db_data, '');
                                if(!empty($json_data)) :
                                    $json_data = json_decode($json_data);
                            ?>
                            <tr>
                                <th scope="row"><label for="is_readed"><?php echo __('Full Message Data', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <span class="regular-span" style="">
                                      
                                          <?php foreach ($json_data as $key => $value) : ?>
                                            <?php if($key == 'element_id' || $key == 'eli_id' || $key == 'action') continue;?>
                                            <?php if (!empty($value)) : ?>
                                                <p>
                                                    <?php if(filter_var($value, FILTER_VALIDATE_URL ) || strpos( $value, 'http' ) !== FALSE):?>
                                                        <strong><?php echo esc_html(ucfirst(str_replace('_', ' ', $key))); ?>:</strong> <a href="<?php echo esc_url($value);?>"><?php echo wp_kses_post($value); ?></a><br />
                                                    <?php else : ?>
                                                        <strong><?php echo esc_html(ucfirst(str_replace('_', ' ', $key))); ?>:</strong> <?php echo wp_kses_post($value); ?><br />
                                                    <?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save Changes','wpdirectorykit'); ?>">
        </form>
    </div>
</div>
<?php $this->view('general/footer', $data); ?>