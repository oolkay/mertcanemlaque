<?php
/**
 * The template for Edit Result Card.
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

<h1 class="wp-heading-inline"><?php echo __('Result Card Designer','wpdirectorykit'); ?></h1>
<br /><br />

    <div class="wdk-body">
        <form method="post" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
        
            <?php wp_nonce_field( 'wdk-resultitem-edit_'.wmvc_show_data('idresultitem', $db_data, 1), '_wpnonce'); ?>

            <div class="postbox" style="display: block;">
                <div class="postbox-header">
                    <h3><?php echo __('Main info','wpdirectorykit'); ?></h3>
                </div>
                <div class="inside">
                    <?php 
                        $form->messages('class="alert alert-danger"',  __('Successfully saved', 'wpdirectorykit'));
                    ?>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="resultitem_name"><?php echo __('Name','wpdirectorykit'); ?></label></th>
                                <td><input name="resultitem_name" type="text" id="resultitem_name" value="<?php echo esc_attr(wmvc_show_data('resultitem_name', $db_data, '')); ?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="is_multiline_enabled"><?php echo __('Multiline enabled','wpdirectorykit'); ?></label></th>
                                <td>
                                    <input name="is_multiline_enabled" value="1" type="checkbox" id="is_multiline_enabled" class="regular-text" <?php echo !empty(wmvc_show_data('is_multiline_enabled', $db_data, ''))?'checked':''; ?>>
                                    <p class="wdk-hint">
                                    <?php echo esc_html__('Enable multilines on features part','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="is_label_disable"><?php echo __('Show only icons','wpdirectorykit'); ?></label></th>
                                <td>
                                    <input name="is_label_disable" value="1" type="checkbox" id="is_label_disable" class="regular-text" <?php echo !empty(wmvc_show_data('is_label_disable', $db_data, ''))?'checked':''; ?>>
                                    <p class="wdk-hint">
                                    <?php echo esc_html__('Show only icons instead of field label when available','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr class="hidden">
                                <th scope="row"><label for="resultitem_json"><?php echo __('Result Card Json/Structure','wpdirectorykit'); ?></label></th>
                                <td>
                                    <textarea name="resultitem_json" type="text" id="resultitem_json" class="regular-text"><?php echo esc_textarea(wmvc_show_data('resultitem_json', $db_data, '')); ?></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
            /* Post title */
            $main_fields = array();
            $predefined_fields_array = array(
                array(
                    'idfield'=> 'post_id',
                    'field_type'=> 'WP',
                    'field_label'=> 'ID',
                ),
                array(
                    'idfield'=> 'date',
                    'field_type'=> 'WP',
                    'field_label'=> 'Date',
                ),
                array(
                    'idfield'=> 'date_modified',
                    'field_type'=> 'WP',
                    'field_label'=> 'Date Modified',
                ),
                array(
                    'idfield'=> 'post_title',
                    'field_type'=> 'WP',
                    'field_label'=> 'Title',
                ),
                array(
                    'idfield'=> 'post_content',
                    'field_type'=> 'WP',
                    'field_label'=> 'Content',
                ),
                array(
                    'idfield'=> 'address',
                    'field_type'=> 'INPUTBOX',
                    'field_label'=> '',
                ),
                array(
                    'idfield'=> 'category_id',
                    'field_type'=> 'CATEGORY',
                    'field_label'=> '',
                ),
                array(
                    'idfield'=> 'location_id',
                    'field_type'=> 'LOCATION',
                    'field_label'=> '',
                ),
                array(
                    'idfield'=> 'counter_views',
                    'field_type'=> 'Views',
                    'field_label'=> '',
                ),
                array(
                    'idfield'=> 'agent_image',
                    'field_type'=> 'IMAGE',
                    'field_label'=> '',
                ),
            );

            foreach ($predefined_fields_array as $key => $value) {
                $predefined_field = new stdClass();
                $predefined_field->idfield = $value['idfield'];
                $predefined_field->lang_code = NULL;
                $predefined_field->field_type = $value['field_type'];
                $predefined_field->field_label = $value['field_label'];
                
                $fields [] = $predefined_field;
                $main_fields [] = $predefined_field;
            }

            ?>

            <div class="postbox" style="display: block;">
                <div class="postbox-header"><h3><?php echo __('Drag & Drop Builder','wpdirectorykit'); ?></h3>
            </div>
            <div class="inside">
                <p class="alert alert-info"><?php echo __('Drag fields from left side to right side, right side represents result card','wpdirectorykit'); ?></p>
                <div class="wdk-builder-container">
                    <div class="wdk-builder-elements-column">
                        <div class="wdk-builder-elements-box">
                            <h3 class="sec-title"><?php echo __('General fields','wpdirectorykit'); ?></h3>
                            <div id="wdk-drag" class="wdk-builder-elements wdk-drop section_fields">
                        <?php foreach($main_fields as $key => $field): 
                                if(isset($used_fields[$field->idfield]))continue; // skip if field is used
                            ?>
                            <div id="fid_<?php echo esc_attr($field->idfield); ?>" class="widget ui-draggable" rel="<?php echo esc_attr($field->idfield); ?>">
                                <div class="widget-top">
                                    <div class="widget-title-action">
                                        <button type="button" class="widget-action hide-if-no-js" aria-expanded="false">
                                            <span class="toggle-indicator" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                    <div class="widget-title ui-draggable-handle"><h3>#<?php echo esc_html($field->idfield); ?> [<?php echo esc_html($field->field_type); ?>] <?php echo esc_html($field->field_label); ?><span class="in-widget-title"></span></h3></div>
                                </div>
                                <div class="widget-inside">
                                    <div class="widget-content">
                                        <p>
                                            <label for="fid_<?php echo esc_attr($field->idfield); ?>-class"><?php echo __('CSS Class:','wpdirectorykit'); ?></label>
                                            <input class="widefat class" id="fid_<?php echo esc_attr($field->idfield); ?>-class" name="fid_<?php echo esc_attr($field->idfield); ?>-class" type="text" value="">
                                        </p>
                                        <p>
                                            <label for="fid_<?php echo esc_attr($field->idfield); ?>-columns"><?php echo __('Columns/Width:','wpdirectorykit'); ?></label>
                                            <input class="widefat columns" id="fid_<?php echo esc_attr($field->idfield); ?>-columns" name="fid_<?php echo esc_attr($field->idfield); ?>-columns" type="number" value="">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php foreach($fields as $key => $field): 
                                if(isset($used_fields[$field->idfield]))continue; // skip if field is used
                                
                                if(in_array($field->idfield, array('search','loc','cat','address','post_title','more','booking_date','location_id','category_id','agent_image','post_content','date_modified','date','counter_views')) !== FALSE)continue; // skip if field is used
                            ?>
                            <?php if($field->field_type=='SECTION'):?>
                                </div>
                                </div>
                                <div class="wdk-builder-elements-box">
                                    <h3 class="sec-title"><?php echo esc_html($field->field_label); ?></h3>
                                    <div id="wdk-drag" class="wdk-builder-elements wdk-drop section_fields" data-name="<?php echo esc_html($field->field_label); ?>">
                            <?php continue; endif;?>

                            <div id="fid_<?php echo esc_attr($field->idfield); ?>" class="widget ui-draggable" rel="<?php echo esc_attr($field->idfield); ?>">
                                <div class="widget-top">
                                    <div class="widget-title-action">
                                        <button type="button" class="widget-action hide-if-no-js" aria-expanded="false">
                                            <span class="toggle-indicator" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                    <div class="widget-title ui-draggable-handle"><h3>#<?php echo esc_html($field->idfield); ?> [<?php echo esc_html($field->field_type); ?>] <?php echo esc_html($field->field_label); ?><span class="in-widget-title"></span></h3></div>
                                </div>
                                <div class="widget-inside">
                                    <div class="widget-content">
                                        <p>
                                            <label for="fid_<?php echo esc_attr($field->idfield); ?>-class"><?php echo __('CSS Class:','wpdirectorykit'); ?></label>
                                            <input class="widefat class" id="fid_<?php echo esc_attr($field->idfield); ?>-class" name="fid_<?php echo esc_attr($field->idfield); ?>-class" type="text" value="">
                                        </p>
                                        <p>
                                            <label for="fid_<?php echo esc_attr($field->idfield); ?>-columns"><?php echo __('Columns/Width:','wpdirectorykit'); ?></label>
                                            <input class="widefat columns" id="fid_<?php echo esc_attr($field->idfield); ?>-columns" name="fid_<?php echo esc_attr($field->idfield); ?>-columns" type="number" value="">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="drop-container">
                <?php 
                $titles = array(__('Over Image Top','wpdirectorykit'),
                                __('Over Image Bottom','wpdirectorykit'),
                                __('Title Part','wpdirectorykit'),
                                __('Subtitle part','wpdirectorykit'),
                                __('Features part','wpdirectorykit'),
                                __('Pricing part','wpdirectorykit'),);
                
                for($i=1;$i<=count($titles);$i++): 
                ?>
                <h3><?php echo esc_html($titles[$i-1]); ?></h3>
                <div id="wdk-drop-<?php echo esc_attr($i); ?>" class="wdk-builder-selected wdk-drop">
                <?php if(isset($used_fields_sub[$i]) && is_array($used_fields_sub[$i]))
                        foreach($fields as $field): 
                            if(!isset($used_fields_sub[$i][$field->idfield]))continue; // skip if field is not used
                    ?>
                    <div id="fid_<?php echo esc_attr($field->idfield); ?>" class="widget ui-draggable" rel="<?php echo esc_attr($field->idfield); ?>">
                        <div class="widget-top">
                            <div class="widget-title-action">
                                <button type="button" class="widget-action hide-if-no-js" aria-expanded="false">
                                    <span class="toggle-indicator" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="widget-action-remove" title="<?php echo esc_attr__('Remove','wpdirectorykit');?>">
                                    <span class="dashicons dashicons-no"></span>
                                </button>
                            </div>
                            <div class="widget-title ui-draggable-handle"><h3>#<?php echo esc_html($field->idfield); ?> [<?php echo esc_html($field->field_type); ?>] <?php echo esc_html($field->field_label); ?><span class="in-widget-title"></span></h3></div>
                        </div>
                        <div class="widget-inside">
                            <div class="widget-content">
                                <p>
                                    <label for="fid_<?php echo esc_attr($field->idfield); ?>-class"><?php echo __('CSS Class:','wpdirectorykit'); ?></label>
                                    <input class="widefat class" id="fid_<?php echo esc_attr($field->idfield); ?>-class" name="fid_<?php echo esc_attr($field->idfield); ?>-class" type="text" value="<?php echo wmvc_show_data('class', $used_fields[$field->idfield], ''); ?>">
                                </p>
                                <p>
                                    <label for="fid_<?php echo esc_attr($field->idfield); ?>-columns"><?php echo __('Columns/Width:','wpdirectorykit'); ?></label>
                                    <input class="widefat columns" id="fid_<?php echo esc_attr($field->idfield); ?>-columns" name="fid_<?php echo esc_attr($field->idfield); ?>-columns" type="number" value="<?php echo wmvc_show_data('columns', $used_fields[$field->idfield], ''); ?>">
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <br style="clear:both;" />
                <?php endfor; ?>
                </div>
                <br style="clear:both;" />
            </div>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save Changes','wpdirectorykit'); ?>">
            </div>
            </div>
        </form>
    </div>
</div>

<?php
wp_enqueue_script( 'jquery-ui-core', false, array('jquery') );
wp_enqueue_script( 'jquery-ui-sortable', false, array('jquery') );
?>

<script>
    // Generate table
    jQuery(document).ready(function($) {

        $( "#wdk-drag, #wdk-drop-1, #wdk-drop-2, #wdk-drop-3, #wdk-drop-4, #wdk-drop-5, #wdk-drop-6" ).sortable({
                connectWith: ".wdk-drop",
                placeholder: "ui-sortable-placeholder widget-placeholder",
                update: function(event, ui) {
                    if(!$(ui.item).find('.widget-action-remove').length){
                        $(ui.item).find('.widget-title-action').append('<button type="button" class="widget-action-remove" title="<?php echo esc_js(__('Remove','wpdirectorykit'));?>"><span class="dashicons dashicons-no"></span></button>');
                    }
                    save_data();
                    remove();
                }
            }).disableSelection();

            $('button.widget-action').on('click', function(){
                if($(this).attr("aria-expanded") == 'true')
                {
                    $(this).attr("aria-expanded", 'false');
                    $(this).parent().parent().parent().find('.widget-inside').hide();
                }
                else
                {
                    $(this).attr("aria-expanded", 'true');
                    $(this).parent().parent().parent().find('.widget-inside').show();
                }
            });

            $('.wdk-builder-container input').on('input', save_data);

            remove();
            function remove() {
                $('.drop-container .wdk-drop .widget.ui-draggable .widget-action-remove').off().on('click', function(e){
                    e.preventDefault();
                    $('#wdk-drag').first().append($(this).closest('.widget.ui-draggable').detach())
                    save_data();
                })
            }

            function save_data()
            {
                var data_fields_list = [];
                var data_fields_sublist = [];

                $('.drop-container .wdk-drop').each(function( index ) {
                    data_fields_sublist = [];

                    $(this).find('.widget.ui-draggable').each(function( index ) {
                        data_fields_sublist.push({'field_id': $( this ).attr('rel'),
                                        'class': $( this ).find('input.class').val(),
                                        'columns': $( this ).find('input.columns').val()});
                    })


                    data_fields_list.push(data_fields_sublist);
                });

                $('#resultitem_json').val(JSON.stringify(data_fields_list));
            }
    });
</script>

<?php $this->view('general/footer', $data); ?>
