<?php
/**
 * The template for Directory Fields Management.
 *
 * This is the template that table, search layout
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('Directory Fields Management','wpdirectorykit'); ?> <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_fields&function=field_edit"); ?>" class="button button-primary" id="add_field_button"><?php echo __('Add Field','wpdirectorykit'); ?></a></h1>
    <br /><br />
        <div class="wdk-body ">
            <div class="row fields_list">
            <?php if(count($fields) == 0): ?>
                <?php if(!function_exists('eli_installer') || !function_exists('run_elementinvader') || !is_plugin_active('elementor/elementor.php')):?>
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
                        <?php echo __('First please install / activate required plugins, then you can import demo data','wpdirectorykit'); ?>
                        <a href="<?php echo esc_url($tgma_link); ?>" class="button button-primary">
                            <?php echo __('Begin to install / activate','wpdirectorykit'); ?>
                        </a>
                    </p>
                </div>
                <?php else:?>
                    <?php 
                        if(file_exists(WPDIRECTORYKIT_PATH.'demo-data/'))
                        {
                            $files = array();
                            $dir = WPDIRECTORYKIT_PATH.'demo-data/';
                            if (is_dir($dir)) {
                                if ($dh = opendir($dir)) {
                                    while (($file = readdir($dh)) !== false) {
                                        if($file  == 'locations.xml') continue;
                                        if(strpos($file, '.xml') !== false && strpos($file, '~') === false)
                                            $multipurpose_values[$file] = ucfirst(str_replace(array('_', '-', '.xml'), ' ', $file));
                                    }
                                    closedir($dh);
                                }
                            }
                        }
                        ksort($multipurpose_values); 

                                                    
                        if(has_filter('wdk/settings/import/multipurpose_values'))
                            $multipurpose_values = apply_filters('wdk/settings/import/multipurpose_values', $multipurpose_values);
                    ?>
                    <div  class="notice notice-success">
                        <p>
                            <?php echo __('You don\'t have any fields, import demo data for','wpdirectorykit'); ?>  
                            <select name="multipurpose" class="field_purpose align-top text-capitalize">
                                <?php foreach ($multipurpose_values as $purpose_key => $purpose) :?>
                                    <?php 
                                        $selected = '';
                                        if($purpose_key=='real-estate.xml')
                                            $selected = "selected ='selected'";
                                    ?>
                                    <option value="<?php echo esc_attr($purpose_key);?>" <?php echo esc_html($selected);?>><?php echo esc_html(ucwords($purpose));?></option>
                                <?php endforeach;?>
                            </select>
                            <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_settings&function=run&multipurpose=real-estate.xml&redirect_url=admin.php?page=wdk_fields&_wpnonce=".wp_create_nonce( 'wdk-import-data-run')); ?>" class="button button-primary event-ajax-indicator" id="import_demo_field_button">
                                <?php echo __('Click here to import now','wpdirectorykit'); ?>
                            </a>
                            <span class="wdk-ajax-indicator wdk-infinity-load color-primary dashicons dashicons-update-alt hidden" style="margin-top: 6px;margin-left: 4px;"></span>
                        </p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php
                if (isset($_GET['message'])) {
                    echo '<p class="alert alert-success">' .str_replace("+", ' ', wp_kses_post(urldecode($_GET['message']))) . '</p><br/>';
                }
            ?>

            <div id="wdk-sortable-fields" class="meta-box-sortables ui-sortable wdk-row">
                <?php foreach($fields as $field): ?>
                    <div id="fid_<?php echo esc_attr($field->idfield); ?>" class=" wdk_field wdk-col-md-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->field_type); ?>" rel="<?php echo esc_attr($field->idfield); ?>">
                        <div class="postbox-header postbox ">
                            <h3 class="hndle ui-sortable-handle"> <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_fields&function=field_edit&id=".esc_attr($field->idfield)); ?>">#<?php echo esc_html($field->idfield); ?> <?php echo esc_html($field->field_label); ?>  <?php echo !empty(wmvc_show_data('is_required', $field, ''))?'*':''; ?> [<?php echo esc_html($field->field_type); ?>]</a> 
                                <a class="question_sure pull_right" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_fields&function=delete&id=".esc_attr($field->idfield)); ?>&_wpnonce=<?php echo wp_create_nonce( 'wdk-fields-delete_'.esc_attr($field->idfield));?>"  title="<?php echo esc_attr__('Remove','wpdirectorykit');?>"><span class="dashicons dashicons-no"></span></a>
                                <a class="pull_right" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_fields&function=field_edit&id=".esc_attr($field->idfield)); ?>"  title="<?php echo esc_attr__('Edit','wpdirectorykit');?>"><span class="dashicons dashicons-edit"></span></a>
                            </h3>
                            <?php if(false): ?>
                            <div class="handle-actions hide-if-no-js">
                                <button type="button" class="handle-order-higher" aria-disabled="false" aria-describedby="postimagediv-handle-order-higher-description">
                                    <span class="screen-reader-text">Move up</span>
                                    <span class="order-higher-indicator" aria-hidden="true"></span>
                                </button>
                                <span class="hidden" id="postimagediv-handle-order-higher-description">Move Featured image box up</span>
                                <button type="button" class="handle-order-lower" aria-disabled="false" aria-describedby="postimagediv-handle-order-lower-description">
                                    <span class="screen-reader-text">Move down</span><span class="order-lower-indicator" aria-hidden="true"></span>
                                </button>
                                <span class="hidden" id="postimagediv-handle-order-lower-description">Move Featured image box down</span>
                                <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Featured image</span>
                                    <span class="toggle-indicator" aria-hidden="true"></span>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if(false): ?>
                        <div class="inside">
                            <p class="hide-if-no-js"></p>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
                    
            <div class="alert alert-info" style="margin-bottom:20px" role="alert"><?php echo sprintf(__('%1$s This fields and section can be restructured, replaced/moved with drag & drop, resized etc. How to add new fields?%2$s', 'wpdirectorykit'),'<a href="//wpdirectorykit.com/documentation/#!/custom_fields" target="_blank">','</a>'); ?></div>
            <iframe width="560" height="315" src="//www.youtube.com/embed/cewZBOGzbPg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<?php

wp_enqueue_script( 'jquery-ui-core', false, array('jquery') );
wp_enqueue_script( 'jquery-ui-sortable', false, array('jquery') );

?>


<script>
    // Generate table
    jQuery(document).ready(function($) {

        $('.field_purpose').on('change', function(){
            $('#import_demo_field_button').attr('href', '<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_settings&function=run&redirect_url=admin.php?page=wdk_fields&_wpnonce=".wp_create_nonce( 'wdk-import-data-run')); ?>&multipurpose='+$(this).find(":selected").attr('value'))
        });

        $( "#wdk-sortable-fields" ).sortable({
            update: function(event, ui) {

                var data_fields_list = '';

                $('#wdk-sortable-fields .wdk_field').each(function( index ) {
                    data_fields_list+=$( this ).attr('rel')+';';
                });

                //console.log(data_fields_list);

                var data = {
                    'page': 'wdk_fields',
                    'function': 'ajax_save_order',
                    'action': 'wdk_admin_action',
                    'data_fields_list' : data_fields_list
                };

                $.post( "<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>", data)
                .done(function( data ) {
                    //alert( "Data Loaded: " + data );
                });
            }
        });

        $('.question_sure').on('click', function(){
            return confirm("<?php echo esc_js(__('Are you sure? Selected item will be completely removed!','wpdirectorykit')); ?>");
        });
    });

</script>

<?php $this->view('general/footer', $data); ?>