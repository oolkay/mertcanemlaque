<?php
/**
 * The template for Settings.
 *
 * This is the template that edit form settings
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('Settings', 'wpdirectorykit'); ?></h1>
    <br />
    <div class="wdk-body">
        <form method="post" action="" novalidate="novalidate">
            <?php wp_nonce_field( 'wdk-settings-edit', '_wpnonce'); ?>
            <?php
                $form->messages('class="alert alert-danger"',  __('Successfully saved', 'wpdirectorykit'));
            ?>
            <?php if(!get_option('wdk_results_page')):?>
                <p class="alert alert-info"><?php echo __('Missing results page', 'wpdirectorykit'); ?></p>
            <?php endif;?>

            <div class="wdk-tabs-navs">
                <label for="wdk_tab_general" class="active"><?php echo esc_html__('General','wpdirectorykit');?></label>
                <label for="wdk_tab_apis"><?php echo esc_html__('Api-s','wpdirectorykit');?></label>
                <label for="wdk_tab_templates"><?php echo esc_html__('Templates','wpdirectorykit');?></label>
                <label for="wdk_tab_fields"><?php echo esc_html__('Fields','wpdirectorykit' );?></label>
                <label for="wdk_tab_tools"><?php echo esc_html__('Tools','wpdirectorykit' );?></label>
                <?php if(get_option('wdk_experimental_features')):?>
                    <label for="wdk_tab_experimental"><?php echo esc_html__('Experimental','wpdirectorykit' );?></label>
                <?php endif;?>
            </div>
            <div class="postbox" style="display: block;">
                <div class="inside">
                    <div class="wdk-tabs-panel">
                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_general" checked value="1">
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['general'], $db_data); ?>  
                        </div>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_apis" value="1">
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['apis'], $db_data); ?>  
                        </div>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_templates" value="1">
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['templates'], $db_data); ?>  
                        </div>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_fields" value="1">
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['fields'], $db_data); ?>  
                        </div>
                        
                        <?php if(get_option('wdk_experimental_features')):?>
                            <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_experimental" value="1">
                            <div class="wdk-tab">
                                <?php echo wdk_generate_fields($fields_list_tabs['experimental'], $db_data); ?>  
                            </div>
                        <?php endif;?>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_tools" value="1">
                        <div class="wdk-tab">
                            <div class="wdk-field-edit">
                                <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_settings&function=remove&_wpnonce=".wp_create_nonce( 'remove-data')); ?>" 
                                class="button button-primary event-ajax-indicator confirm" id="reset_data_field_button"><?php echo __('Remove plugin data (Listings, fields, location, categories)','wpdirectorykit'); ?></a>               
                                <span class="wdk-ajax-indicator wdk-infinity-load color-primary dashicons dashicons-update-alt hidden" style="margin-top: 4px;margin-left: 4px;"></span>              
                            </div>
                            <div class="wdk-field-edit">
                                <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_settings&function=import_demo"); ?>" class="button button-primary" id="import_demo_field_button"><?php echo __('Import Demo Data','wpdirectorykit'); ?></a> 
                            </div>
                            <div class="wdk-field-edit">
                                <a href="#" class="button button-primary" id="generate_listings_images_path"><?php echo __('Generate Listings Images Path','wpdirectorykit'); ?></a>               
                            </div>
                            <div class="wdk-field-edit">
                                <a href="#" class="button button-primary" id="optimization_listingfields_table"><?php echo __('Optimization Listing Fields Table','wpdirectorykit'); ?></a>               
                            </div>
                            <div class="wdk-field-edit">
                                <a href="#" class="button button-primary ajax_query" data-function="generated_strings"><?php echo __('Generate translation strings','wpdirectorykit'); ?></a>               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save Changes', 'wpdirectorykit'); ?>">
        </form>
    </div>
    <br/>
    <div class="alert alert-info" role="alert"><a href="//wpdirectorykit.com/documentation/#change_currency" target="_blank"><?php echo __('How to change currency?','wpdirectorykit'); ?></a></div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#reset_data_field_button').on('click', function(e){
        var self = $(this);

        var res = prompt("<?php echo __('Are you sure? All Listings, fields, categories, locations will be completely removed, check url and type remove if you are sure', 'wpdirectorykit')?>", "");

        if(res == "<?php echo __('remove', 'wpdirectorykit')?>") {
            return true;
        } else {
            e.preventDefault();
            e.stopPropagation();
            $(this).parent().find('.wdk-ajax-indicator').addClass('hidden');
            return false;
        }
    })
    $('#generate_listings_images_path').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = $(this);

        if(self.attr('disabled')) {
            return false;
        }

        self.addClass('wdk_btn_load_indicator out');
      
        self.attr('disabled','disabled');
        var ajax_param = {
            "page": 'wdk_backendajax',
            "function": 'generated_listings_images_path',
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-generated_listings_images_path'));?>',
        };
        $.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
            function(data){
                
            if(data.popup_text_success)
                wdk_log_notify(data.popup_text_success);
                
            if(data.popup_text_error)
                wdk_log_notify(data.popup_text_error, 'error');
                
            if(data.success) {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_success out');
            } else {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_error out');
            }
        }).always(function(data) {

        });
        return false;
    })

    $('#optimization_listingfields_table').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = $(this);

        if(self.attr('disabled')) {
            return false;
        }

        self.addClass('wdk_btn_load_indicator out');
      
        self.attr('disabled','disabled');
        var ajax_param = {
            "page": 'wdk_backendajax',
            "function": 'optimization_listingfields_table',
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-optimization_listingfields_table'));?>',
        };
        $.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
            function(data){
                
            if(data.popup_text_success)
                wdk_log_notify(data.popup_text_success);
                
            if(data.popup_text_error)
                wdk_log_notify(data.popup_text_error, 'error');
                
            if(data.success) {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_success out');
            } else {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_error out');
            }
        }).always(function(data) {

        });
        return false;
    })

    $('.ajax_query').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = $(this);

        if(self.attr('disabled')) {
            return false;
        }

        self.addClass('wdk_btn_load_indicator out');
      
        self.attr('disabled','disabled');
        var ajax_param = {
            "page": 'wdk_backendajax',
            "function": self.data('function'),
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-backendajax'));?>',
        };
        $.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
            function(data){
                
            if(data.popup_text_success)
                wdk_log_notify(data.popup_text_success);
                
            if(data.popup_text_error)
                wdk_log_notify(data.popup_text_error, 'error');
                
            if(data.success) {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_success out');
            } else {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_error out');
            }
        }).always(function(data) {

        });
        return false;
    })
})
</script>

<?php $this->view('general/footer', $data); ?>