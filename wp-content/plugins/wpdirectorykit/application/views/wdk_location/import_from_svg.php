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
            </div>
            <div class="inside">

                <form method="post" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
                    <?php wp_nonce_field( 'wdk-svg-import', '_wpnonce'); ?>
                    <?php 
                        $form->messages('class="alert alert-danger"',  __('Locations imported', 'wpdirectorykit'));
                    ?>

                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="related_svg_map"><?php echo __('Related SVG Map','wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php  
                                        echo wmvc_select_option('related_svg_map', $maps_list, '', NULL, __('Not Selected','wpdirectorykit'), '0'); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="related_svg_map_location"><?php echo __('Related SVG Map Location','wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php  
                                        echo wmvc_select_option('related_svg_map_location', $parents, wmvc_show_data('idlocation', $location, ''), NULL, __('Root','wpdirectorykit'), '0'); 
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Import Locations','wpdirectorykit'); ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->view('general/footer', $data); ?>