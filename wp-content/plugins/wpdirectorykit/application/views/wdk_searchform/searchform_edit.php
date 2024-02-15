<?php
/**
 * The template for Edit Search Form.
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

    <h1 class="wp-heading-inline"><?php echo __('Search Form Designer','wpdirectorykit'); ?></h1>
    <br /><br />

    <div class="wdk-body">
        <form method="post" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
            <?php wp_nonce_field( 'wdk-searchform-edit_'.wmvc_show_data('idsearchform', $db_data, 1), '_wpnonce'); ?>
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
                                <th scope="row"><label for="searchform_name"><?php echo __('Name','wpdirectorykit'); ?></label></th>
                                <td><input name="searchform_name" type="text" id="searchform_name" value="<?php echo wmvc_show_data('searchform_name', $db_data, ''); ?>" class="regular-text"></td>
                            </tr>
                            <tr class="hidden">
                                <th scope="row"><label for="searchform_json"><?php echo __('Search Form Json/Structure','wpdirectorykit'); ?></label></th>
                                <td>
                                    <textarea readonly="readonly" name="searchform_json" type="text" id="searchform_json" class="regular-text"><?php echo esc_textarea(wmvc_show_data('searchform_json', $db_data, '')); ?></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="postbox" style="display: block;">
                <div class="postbox-header">
                    <h3><?php echo __('Helper Classes','wpdirectorykit'); ?></h3>
                </div>
                <div class="inside">
                    <div class="" style="border: 1px solid rgb(207, 210, 210); background-color: #eee;padding: 10px 15px;">
                        <dl class="dl-helper">
                            <dt>hidden</dt>
                                <dd style="margin-left: 25px;"><?php echo __('Hide field on all','wpdirectorykit'); ?></dd>
                            <dt>wdk-col-1,wdk-col-2,wdk-col-3,wdk-col-4, wdk-col-n ... 12</dt>
                                <dd style="margin-left: 25px;"><?php echo __('Grid classes from 1 to 12','wpdirectorykit'); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>

            <?php
            $main_fields = array();
            $predefined_fields_array = array(
                'search'=> array(
                    'idfield'=> 'search',
                    'field_type'=> 'SMART_SEARCH',
                    'field_label'=> '',
                ),
                'post_title'=>array(
                    'idfield'=> 'post_title',
                    'field_type'=> 'INPUTBOX',
                    'field_label'=> 'Title',
                ),
                'address'=>array(
                    'idfield'=> 'address',
                    'field_type'=> 'INPUTBOX',
                    'field_label'=> '',
                ),
                'cat'=>array(
                    'idfield'=> 'cat',
                    'field_type'=> 'CATEGORY',
                    'field_label'=> '',
                ),
                'loc'=>array(
                    'idfield'=> 'loc',
                    'field_type'=> 'LOCATION',
                    'field_label'=> '',
                ),
                'more'=>array(
                    'idfield'=> 'more',
                    'field_type'=> 'MORE',
                    'field_label'=> '',
                ),
            );

            if(function_exists('run_wdk_bookings')) {
                $predefined_fields_array['booking_date'] = array(
                        'idfield'=> 'booking_date',
                        'field_type'=> 'BOOKINGS_DATE',
                        'field_label'=> '',
                    );
                $predefined_fields_array['booking_guest'] = array(
                        'idfield'=> 'booking_guest',
                        'field_type'=> 'NUMBER',
                        'field_label'=> __('Max guests','wpdirectorykit'),
                    );
            }

            foreach ($predefined_fields_array as $value) {
                $predefined_field = new stdClass();
                $predefined_field->idfield = $value['idfield'];
                $predefined_field->lang_code = NULL;
                $predefined_field->field_type = $value['field_type'];
                $predefined_field->field_label = $value['field_label'];
                
                $fields [] = $predefined_field ;
                $main_fields [] = $predefined_field;
            }
            ?>

            <div class="postbox" style="display: block;">
                <div class="postbox-header"><h3><?php echo __('Drag & Drop Builder','wpdirectorykit'); ?></h3>
            </div>
            <div class="inside">
                <p class="alert alert-info"><?php echo __('Drag fields from left side to right side, right side represents search form','wpdirectorykit'); ?></p>
                <div class="wdk-builder-container">
                    <div class="wdk-builder-elements-column">
                        <div class="wdk-builder-elements-box">
                            <h3 class="sec-title"><?php echo __('Custom fields','wpdirectorykit'); ?></h3>
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
                                            <p>
                                                <label for="fid_<?php echo esc_attr($field->idfield); ?>-query_type"><?php echo __('Search type:','wpdirectorykit'); ?></label>
                                                <select class="widefat query_type" id="fid_<?php echo esc_attr($field->idfield); ?>-query_type" name="fid_<?php echo esc_attr($field->idfield); ?>-query_type">
                                                    <option value=""><?php echo __('None','wpdirectorykit'); ?></option>
                                                    <option value="exactly"><?php echo __('Exactly','wpdirectorykit'); ?></option>
                                                </select>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php foreach($fields as $key => $field): 
                                    if(isset($used_fields[$field->idfield]))continue; // skip if field is used

                                    if(in_array($field->idfield, array('search','loc','cat','address','post_title','more','booking_date','booking_guest')) !== FALSE)continue; // skip if field is used
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
                                            <p class=" <?php if($field->field_type =='CHECKBOX' || $field->field_type =='MORE' || $field->field_type =='TEXTAREA' || $field->field_type =='TEXTAREA_WYSIWYG' || $field->field_type =='CATEGORY' || $field->field_type =='LOCATION'):?> hidden <?php endif;?>">
                                                <label for="fid_<?php echo esc_attr($field->idfield); ?>-query_type"><?php echo __('Search type:','wpdirectorykit'); ?></label>
                                                <select class="widefat query_type" id="fid_<?php echo esc_attr($field->idfield); ?>-query_type" name="fid_<?php echo esc_attr($field->idfield); ?>-query_type">
                                                    <option value=""><?php echo __('None','wpdirectorykit'); ?></option>
                                                    <option value="exactly"><?php echo __('Exactly','wpdirectorykit'); ?></option>
                                                    <?php if(in_array($field->idfield, array('search','loc','cat','address','post_title')) == FALSE && $field->field_type !='CHECKBOX'):?>
                                                    <option value="min"><?php echo __('Min','wpdirectorykit'); ?></option>
                                                    <option value="max"><?php echo __('Max','wpdirectorykit'); ?></option>
                                                    <option value="min_max"><?php echo __('Min/Max','wpdirectorykit'); ?></option>
                                                    <?php endif;?>
                                                    <?php if(in_array($field->idfield, array('search','loc','cat','address','post_title')) == FALSE && $field->field_type =='NUMBER'):?>
                                                    <option value="slider_range"><?php echo __('Slider Range','wpdirectorykit'); ?></option>
                                                    <?php endif;?>
                                                </select>
                                            </p>
                                            <?php if(in_array($field->idfield, array('search','loc','cat','address','post_title')) == FALSE && $field->field_type =='NUMBER'):?>
                                            <p class="is_slider_range <?php if($field->query_type !='slider_range'):?> hidden <?php endif;?>">
                                                <label for="fid_<?php echo esc_attr($field->idfield); ?>-value_min"><?php echo __('Value Min:','wpdirectorykit'); ?></label>
                                                <input class="widefat value_min" id="fid_<?php echo esc_attr($field->idfield); ?>-value_min" name="fid_<?php echo esc_attr($field->idfield); ?>-value_min" type="number" value="">
                                            </p>
                                            <p class="is_slider_range <?php if($field->query_type !='slider_range'):?> hidden <?php endif;?>">
                                                <label for="fid_<?php echo esc_attr($field->idfield); ?>-value_max"><?php echo __('Value Max:','wpdirectorykit'); ?></label>
                                                <input class="widefat value_max" id="fid_<?php echo esc_attr($field->idfield); ?>-value_max" name="fid_<?php echo esc_attr($field->idfield); ?>-value_max" type="number" value="">
                                            </p>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="drop-container">
                    <?php
                        $WMVC = &wdk_get_instance();
                        $WMVC->model('field_m');
                                        

                    ?>
                    <div id="wdk-drop" class="wdk-builder-selected wdk-drop">
                    <?php if(is_array($used_fields))
                            foreach($used_fields as $used_field): 
                                if(isset($predefined_fields_array[$used_field->field_id])) {
                                    $field = $predefined_fields_array[$used_field->field_id];
                                } else {
                                   $field = $WMVC->field_m->get_fields_data($used_field->field_id);
                                }

                                //if(!isset($used_fields[$field->idfield]))continue; // skip if field is not used
                        ?>
                        <div id="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>" class="widget ui-draggable" rel="<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>">
                            <div class="widget-top">
                                <div class="widget-title-action">
                                    <button type="button" class="widget-action hide-if-no-js" aria-expanded="false">
                                        <span class="toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="widget-action-remove" title="<?php echo esc_attr__('Remove','wpdirectorykit');?>">
                                        <span class="dashicons dashicons-no"></span>
                                    </button>
                                </div>
                                <div class="widget-title ui-draggable-handle"><h3>#<?php echo esc_html(wmvc_show_data('idfield', $field)); ?> [<?php echo esc_html(wmvc_show_data('field_type', $field)); ?>] <?php echo esc_html(wmvc_show_data('field_label', $field)); ?><span class="in-widget-title"></span></h3></div>
                            </div>
                            <div class="widget-inside">
                                <div class="widget-content">
                                    <p>
                                        <label for="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-class"><?php echo __('CSS Class:','wpdirectorykit'); ?></label>
                                        <input class="widefat class" id="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-class" name="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-class" type="text" value="<?php echo wmvc_show_data('class', $used_fields[wmvc_show_data('idfield', $field)], ''); ?>">
                                    </p>
                                    <p>
                                        <label for="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-columns"><?php echo __('Columns/Width:','wpdirectorykit'); ?></label>
                                        <input class="widefat columns" id="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-columns" name="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-columns" type="number" value="<?php echo wmvc_show_data('columns', $used_fields[wmvc_show_data('idfield', $field)], ''); ?>">
                                    </p>
                                    <p class=" <?php if(wmvc_show_data('field_type', $field) =='CHECKBOX' || wmvc_show_data('field_type', $field) =='TEXTAREA' || wmvc_show_data('field_type', $field) =='TEXTAREA_WYSIWYG' || wmvc_show_data('field_type', $field) =='CATEGORY' || wmvc_show_data('field_type', $field) =='LOCATION'):?> hidden <?php endif;?>">
                                        <label for="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-query_type"><?php echo __('Search type:','wpdirectorykit'); ?></label>
                                        <select class="widefat query_type" id="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-query_type" name="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-query_type">
                                            <option value=""><?php echo __('None','wpdirectorykit'); ?></option>
                                            <option value="exactly" <?php if(wmvc_show_data('query_type', $used_fields[wmvc_show_data('idfield', $field)]) == 'exactly'): ?> selected="selected" <?php endif;?>><?php echo __('Exactly','wpdirectorykit'); ?></option>
                                            <?php if(in_array(wmvc_show_data('idfield', $field), array('search','loc','cat','address','post_title')) == FALSE && wmvc_show_data('field_type', $field) !='CHECKBOX'):?>
                                                <option value="min" <?php if(wmvc_show_data('query_type', $used_fields[wmvc_show_data('idfield', $field)]) == 'min'): ?> selected="selected" <?php endif;?>><?php echo __('Min','wpdirectorykit'); ?></option>
                                                <option value="max" <?php if(wmvc_show_data('query_type', $used_fields[wmvc_show_data('idfield', $field)]) == 'max'): ?> selected="selected" <?php endif;?>><?php echo __('Max','wpdirectorykit'); ?></option>
                                                <option value="min_max" <?php if(wmvc_show_data('query_type', $used_fields[wmvc_show_data('idfield', $field)]) == 'min_max'): ?> selected="selected" <?php endif;?>><?php echo __('Min/Max','wpdirectorykit'); ?></option>
                                            <?php endif;?>
                                            <?php if(in_array(wmvc_show_data('idfield', $field), array('search','loc','cat','address','post_title')) == FALSE && wmvc_show_data('field_type', $field) =='NUMBER'):?>
                                                <option value="slider_range" <?php if(wmvc_show_data('query_type', $used_fields[wmvc_show_data('idfield', $field)]) == 'slider_range'): ?> selected="selected" <?php endif;?>><?php echo __('Slider Range','wpdirectorykit'); ?></option>
                                            <?php endif;?>
                                        </select>
                                    </p>
                                    <?php if(in_array(wmvc_show_data('idfield', $field), array('search','loc','cat','address','post_title')) == FALSE && wmvc_show_data('field_type', $field) =='NUMBER'):?>
                                    <p class="is_slider_range <?php if(wmvc_show_data('query_type', $used_fields[wmvc_show_data('idfield', $field)]) !='slider_range'):?> hidden <?php endif;?>">
                                        <label for="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-value_min"><?php echo __('Value Min:','wpdirectorykit'); ?></label>
                                        <input class="widefat value_min" id="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-value_min" name="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-value_min" type="number" value="<?php echo wmvc_show_data('value_min', $used_fields[wmvc_show_data('idfield', $field)], ''); ?>">
                                    </p>
                                    <p class="is_slider_range <?php if(wmvc_show_data('query_type', $used_fields[wmvc_show_data('idfield', $field)]) !='slider_range'):?> hidden <?php endif;?>">
                                        <label for="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-value_max"><?php echo __('Value Max:','wpdirectorykit'); ?></label>
                                        <input class="widefat value_max" id="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-value_max" name="fid_<?php echo esc_attr(wmvc_show_data('idfield', $field)); ?>-value_max" type="number" value="<?php echo wmvc_show_data('value_max', $used_fields[wmvc_show_data('idfield', $field)], ''); ?>">
                                    </p>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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

    $( "#wdk-drag, #wdk-drop" ).sortable({
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

    $('.wdk-builder-container').find('input,select').on('input', save_data);

    $('.wdk-builder-container').find('select.query_type').on('input', function(e){
        if($(this).find('option:selected').attr('value') == 'slider_range') {
            $(this).closest('.widget-content').find('.is_slider_range').removeClass('hidden');
        } else {
            $(this).closest('.widget-content').find('.is_slider_range').addClass('hidden');
        }
    });
    remove();
    function remove() {
        $('#wdk-drop .widget.ui-draggable .widget-action-remove').off().on('click', function(e){
            e.preventDefault();
            $('#wdk-drag').first().append($(this).closest('.widget.ui-draggable').detach())
            save_data();
        })
    }

    function save_data()
    {
        var data_fields_list = [];

        $('#wdk-drop .widget.ui-draggable').each(function( index ) {
            data_fields_list.push({'field_id': $( this ).attr('rel'),
                                'class': $( this ).find('input.class').val(),
                                'query_type': $( this ).find('select.query_type').val(),
                                'value_min': $( this ).find('input.value_min').val(),
                                'value_max': $( this ).find('input.value_max').val(),
                                'columns': $( this ).find('input.columns').val()});
        });

        $('#searchform_json').val(JSON.stringify(data_fields_list));
    }
});

</script>
<?php $this->view('general/footer', $data); ?>