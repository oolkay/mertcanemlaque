<?php
/**
 * The template for Log Page.
 *
 * This is the template that table list, alerts
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('Remove Plugin Data', 'wpdirectorykit'); ?></h1>
    <br /><br />
    <div class="wdk-body">
        <div class="postbox" style="display: block;">
            <div class="postbox-header">
                <h3><?php echo __('Info', 'wpdirectorykit'); ?></h3>
            </div>
            <div class="inside">
                <?php echo wmvc_xss_clean($data_log);?>     
            </div>
        </div>
        <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wdk_settings'));?>"><?php echo __('Back to Settings', 'wpdirectorykit'); ?></a>
    </div>
</div>

<?php $this->view('general/footer', $data); ?>