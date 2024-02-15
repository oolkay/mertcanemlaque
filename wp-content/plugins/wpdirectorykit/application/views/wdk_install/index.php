<?php
/**
 * The template for Demo Import.
 *
 * This is the template that edit form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">

    <h1 class="wp-heading-inline"><?php echo __('Demo Import', 'wpdirectorykit'); ?></h1>
    <div class="wdk-body">
        <?php if($installed):?>
        <div  class="notice notice-success">
            <p>
                <?php echo __('Some data already exists, please remove all data if you want to full import again','wpdirectorykit'); ?>
                <a href="<?php echo esc_url(admin_url("admin.php?page=wdk_settings&function=remove&redirect_url=on_install&multipurpose=".wmvc_show_data('multipurpose', $_GET, '')."&_wpnonce=".wp_create_nonce( 'remove-data' ))); ?>" onclick="return (prompt('<?php echo __('Are you sure? All Listings, fields, categories, locations will be completely removed, check url and type remove if you are sure', 'wpdirectorykit')?>', '') == '<?php echo __('remove', 'wpdirectorykit')?>');"  class="button button-primary" id="reset_data_field_button">
                    <?php echo __('Remove all data','wpdirectorykit'); ?>
                </a>
            </p>
        </div>
        <?php endif;?>
        <?php if($required_plugins):?>
        <?php 
            $tgma_link = '';
            if(file_exists(get_template_directory().'/includes/tgm_pa/class-tgm-plugin-activation.php') || file_exists(get_template_directory().'/tgm_pa/class-tgm-plugin-activation.php')) {
                $tgma_link = get_admin_url() . "themes.php?page=tgmpa-install-plugins";
            } else {
                $tgma_link = get_admin_url() . "plugins.php?page=tgmpa-install-plugins";
            }
        ?>
        <div  class="notice notice-error">
            <p>
                <?php echo __('First please install / activate required plugins','wpdirectorykit'); ?>
                <a href="<?php echo esc_url($tgma_link); ?>" class="button button-primary">
                    <?php echo __('Begin to install / activate','wpdirectorykit'); ?>
                </a>
            </p>
        </div>
        <?php endif;?>
        <form method="post" action="" novalidate="novalidate">
            <?php wp_nonce_field( 'wdk-settings_import', '_wpnonce'); ?>
            <div class="postbox" style="display: block;">
                <div class="postbox-header">
                    <h3><?php echo __('Demo Data Importer Tool', 'wpdirectorykit'); ?></h3>
                </div>
                <div class="inside">
                    <div class="">
                        <?php
                        $max_time = ini_get("max_execution_time");
                        if($max_time < 120):?>
                        <div class="alert alert-danger" role="alert"><?php echo __('For import max_execution_time should be more then 120s, please contact with admin host to increase','wpdirectorykit'); ?></div>
                        <?php endif;?>
                    </div>
                    <?php echo wmvc_xss_clean($info_log_message);?>
                    <?php echo wmvc_xss_clean($import_log);?>
                    <?php echo wdk_generate_fields($fields, $db_data); ?>        
                </div>
            </div>
            <?php if(!$installed || true):?>
                <input type="submit" name="submit" id="submit" class="button button-primary event-ajax-indicator" value="<?php echo __('Import demo data', 'wpdirectorykit'); ?>"> <span class="wdk-ajax-indicator wdk-infinity-load color-primary dashicons dashicons-update-alt hidden" style="margin-top: 4px;margin-left: 4px;"></span>
            <?php endif;?>
            <?php if(!empty($import_log) && stripos($import_log,'alert-succes') !== FALSE && stripos($import_log,'alert-danger') === FALSE):?>
                <a href="<?php echo esc_url(home_url());?>" class="button button-secondary" target="_blank"><?php echo __('Check your results page now', 'wpdirectorykit'); ?></a>
            <?php endif;?>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('form input[type="submit"]').on('click', function(){
        var self = $(this);
        setTimeout(function(){
            self.delay(2000).attr("disabled", true)
        },0)
    })
})
</script>

<?php $this->view('general/footer', $data); ?>