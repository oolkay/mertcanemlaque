<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://listing-themes.com/
 * @since      1.0.0
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap elementinvader_wrap bg-white">
    <div class="panel panel-default">
        <div class="panel-heading flex">
            <h3 class="panel-title"><?php echo __('Export Template','elementinvader'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="">

            <a target="_blank" href="https://www.facebook.com/groups/2631390953808199/" class="alert alert-warning"><?php echo __('If you have General question please join our Facebook Community','elementinvader'); ?></a>

            <div class="validation-messages">
                <?php
                    $form->messages();
                ?>
            </div>

            <form method="post" action="<?php echo wmvc_current_edit_url(); ?>" class="form-layout" enctype="multipart/form-data" novalidate="novalidate">
                <div class="form-group">
                    <?php
                        $post_data = get_post( $elementor_post_id );
                        
                        $page_title = __('Page Title','elementinvader');
                        if(!empty($post_data->post_title))
                            $page_title = $post_data->post_title;
                    ?>
                    <label for="kit_title"><?php echo __('Template Name','elementinvader'); ?></label>
                    <input name="kit_title" type="text" class="form-control" id="kit_title" value="<?php echo esc_attr(wmvc_show_data('kit_title', $db_data, get_bloginfo('name')));?>" placeholder="<?php echo __('Template Name','elementinvader'); ?>">
                </div>
                <div class="form-group">
                    <label for="kit_page_title"><?php echo __('Page Title','elementinvader'); ?></label>
                    <input name="kit_page_title" type="text" class="form-control" id="kit_page_title" value="<?php echo esc_attr(wmvc_show_data('kit_page_title', $db_data, $page_title));?>" placeholder="<?php echo __('Page Title','elementinvader'); ?>">
                </div>
                <div class="form-group">
                    <label for="kit_page_tags"><?php echo __('Tags','elementinvader'); ?></label>
                    <input name="kit_page_tags" type="text" class="form-control" id="kit_page_tags" value="<?php echo esc_attr(wmvc_show_data('kit_page_tags', $db_data, ''));?>" placeholder="<?php echo __('Page Tags','elementinvader'); ?>">
                </div>
                <div class="form-group">
                    <label for="kit_description"><?php echo __('Description','elementinvader'); ?></label>
                    <textarea name="kit_description" class="form-control" id="kit_description" rows="5"><?php echo esc_attr(wmvc_show_data('kit_description', $db_data, ''));?></textarea>
                </div>
                <div class="form-group">
                    <label for="kit_description"><?php echo __('Screenshot','elementinvader'); ?></label>
                    <?php echo wmvc_upload_media('screenshoot', wmvc_show_data('screenshoot', $db_data, '')); ?>
                    <i class="hint"><?php echo __('Make nice screenshot image with width 640px and max height 2000px','elementinvader'); ?></i>
                </div>
                <div class="form-group">
                    <label for="kit_description"><?php echo __('Screenshot large','elementinvader'); ?></label>
                    <?php echo wmvc_upload_media('screenshoot_large', wmvc_show_data('screenshoot_large', $db_data, '')); ?>
                    <i class="hint"><?php echo __('Make nice screenshot image with width 1280px and max height 4000px','elementinvader'); ?></i>
                </div>

                <div class="form-group">
                    <label><?php echo __('Required plugins','elementinvader'); ?>:</label>

                    <?php
                        $active_plugins=get_option('active_plugins');
                        $all_plugins=get_plugins();
                    ?>
                    <?php foreach ($active_plugins as $plugin_key):?>
                    <?php 
                    if(!isset($all_plugins[$plugin_key])) continue;
                        $plugin_data = $all_plugins[$plugin_key];
                        $plugin_slug = explode('/',$plugin_key)[0];
                    ?>
                    <label class="inline-checkbox">
                    <input
                        <?php
                        if(in_array($plugin_slug, array('elementor','elementinvader','elementinvader-addons-for-elementor')) !== FALSE ){
                            echo 'checked="checked"';
                        }elseif(isset($_POST['required_plugins']) && array_search($plugin_slug, $_POST['required_plugins']) !== FALSE ){
                            echo 'checked="checked"';
                        } elseif(!isset($_POST['required_plugins']) && isset($db_data['required_plugins']) && array_search($plugin_slug, $db_data['required_plugins']) !== FALSE ) {
                            echo 'checked="checked"';
                        }
                        ?> 
                        
                        name="required_plugins[]" type="checkbox" class="form-control" value="<?php echo esc_attr($plugin_slug);?>"><?php echo esc_html($plugin_data['Name']); ?>
                    </label>
                    <?php endforeach;?>
                </div>

                <div class="form-group">
                    <label><?php echo __('Options','elementinvader'); ?>:</label>
                    <label class="inline-checkbox">
                    <input checked="checked" name="save_data" type="checkbox" class="form-control" value="1"><?php echo __('Save Data','elementinvader'); ?>
                    </label>
                </div>
            
                <button type="submit" class="btn btn-primary"><?php echo __('Export','elementinvader'); ?> <img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;" /></button>
            </form>

            </div>
        </div>
    </div>
    
</div>


<?php

?>

<script>


jQuery(document).ready(function($) {

$('#form-layout').on('submit', function()
{
    $(this).find('#ajax-indicator-masking').show();
});

});

</script>

<?php $this->view('general/footer', $data); ?>










