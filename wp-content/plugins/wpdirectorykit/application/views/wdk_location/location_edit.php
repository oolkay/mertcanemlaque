<?php
/**
 * The template for Edit Location.
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
    <h1 class="wp-heading-inline"><?php echo __('Location Management','wpdirectorykit'); ?></h1>
    <br /><br />
        <div class="wdk-body">
            <div class="postbox" style="display: block;">
                <div class="postbox-header"><h3><?php echo __('Add/Edit Location','wpdirectorykit'); ?></h3>

                <?php if(function_exists('run_wdk_svg_map') && wmvc_show_data('idlocation', $db_data, false)):?>
                    <a href="<?php echo esc_url(admin_url( 'admin.php?page=wdk_location&function=import_from_svg&id='. wmvc_show_data('idlocation', $db_data, false) )); ?>" class="wdk-mr-5 button button-secondary alignright">
                        <span class="dashicons dashicons-admin-page" style="margin-top: 4px;"></span><?php echo esc_html__('Import sublocations from SVG Map','wpdirectorykit'); ?>
                    </a>
                <?php endif;?>
            </div>
            <div class="inside">

                <form method="post" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
                    <?php wp_nonce_field( 'wdk-location-edit_'.wmvc_show_data('idlocation', $db_data, 0), '_wpnonce'); ?>
                    <?php 
                    $form->messages('class="alert alert-danger"',  __('Successfully saved', 'wpdirectorykit'));
                    ?>

                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="parent_id"><?php echo __('Parent','wpdirectorykit'); ?></label></th>
                                <td>
                                <?php  
                                    echo wmvc_select_option('parent_id', $parents, wmvc_show_data('parent_id', $db_data, ''), NULL, __('Root','wpdirectorykit'), '0'); 
                                ?>

                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="location_title"><?php echo __('Title','wpdirectorykit'); ?></label></th>
                                <td><input name="location_title" type="text" id="location_title" value="<?php echo wmvc_show_data('location_title', $db_data, ''); ?>" class="regular-text"></td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="order_index"><?php echo __('Order Index','wpdirectorykit'); ?></label></th>
                                <td>
                                    <input name="order_index" type="text" id="order_index" value="<?php echo wmvc_show_data('order_index', $db_data, ''); ?>" class="regular-text">
                                    <p class="description" id="order_index-description"><?php echo __('Index for sorting/ordering, you can leave it empty and will be auto added to end of parent list','wpdirectorykit'); ?></p>
                                </td>
                            </tr>
                            <?php if(function_exists('run_wdk_svg_map')):?>
                            <tr>
                                <th scope="row"><label for="related_svg_map"><?php echo __('Related SVG Map','wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php  
                                        echo wmvc_select_option('related_svg_map', $maps_list, wmvc_show_data('related_svg_map', $db_data, ''), "id='related_svg_map'", __('Not Selected','wpdirectorykit'), '0'); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="related_svg_map_location"><?php echo __('Related SVG Map Location','wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php  
                                        echo wmvc_select_option('related_svg_map_location', $map_related_locations, wmvc_show_data('related_svg_map_location', $db_data, ''), "id='related_svg_map_location'", __('Not Selected','wpdirectorykit'), '0'); 
                                    ?>
                                </td>
                            </tr>
                            <?php endif;?>
                            <tr>
                                <th scope="row"><label for="icon_id"><?php echo __('Icon','wpdirectorykit'); ?></label></th>
                                <td>
                                <?php  
                                    echo wmvc_upload_media('icon_id', wmvc_show_data('icon_id', $db_data, '')); 
                                ?>
                                    <p class="description" id="icon_id-description"><?php echo __('Icon used for marker/pin on map or special places on website','wpdirectorykit'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="image_id"><?php echo __('Image','wpdirectorykit'); ?></label></th>
                                <td>
                                <?php  
                                    echo wmvc_upload_media('image_id', wmvc_show_data('image_id', $db_data, '')); 
                                ?>
                                    <p class="description" id="image_id-description"><?php echo __('Image used for widgets or elements where categories are visible','wpdirectorykit'); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save Changes','wpdirectorykit'); ?>">
                </form>
            </div>
        </div>
        
        <div class="alert alert-info" role="alert"><?php echo sprintf(__('%1$s How to add new country in locations? %2$s', 'wpdirectorykit'),'<a href="//wpdirectorykit.com/documentation/#add-new-country" target="_blank">','</a>'); ?></div>
    </div>
</div>

<script>
var jqxhr = null;
jQuery(document).ready(function($) {  
    $('#related_svg_map').on('input', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = $(this);

        jQuery('#related_svg_map_location').closest('tr').find('label').addClass('wdk_btn_load_indicator out');
      
        var ajax_param = {
                "action": 'wdk_svg_map_public_action',
                "page": 'wdk_svg_map_frontendajax',
                "function": 'get_map_data',
        };
        ajax_param['related_svg_map'] = $(this).val();

        // Assign handlers immediately after making the request,
        // and remember the jqxhr object for this request
        if (jqxhr != null)
            jqxhr.abort();

        jqxhr = $.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
            function(data){
                var select_list = '<option value=""> <?php echo esc_js('Not Selected','wpdirectorykit');?> </option>';
                jQuery.each(data.output.locations, function(i,v){
                    select_list += '<option value="'+i+'"> '+v+' </option>';
                });

                jQuery('#related_svg_map_location').html(select_list);
         
        }).always(function(data) {
            jQuery('#related_svg_map_location').closest('tr').find('label').removeClass('wdk_btn_load_indicator out');
        });
        return false;
    })
})

</script>

<?php $this->view('general/footer', $data); ?>