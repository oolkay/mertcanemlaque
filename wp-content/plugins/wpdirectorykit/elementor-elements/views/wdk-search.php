<?php
/**
 * The template for Element Listings Search Form.
 * This is the template that elementor element, fields, search form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php
global $wdk_button_search_defined;
$wdk_button_search_defined=false;

global $wdk_enable_search_fields_toggle;
$wdk_enable_search_fields_toggle = true;

global $wdk_text_search_button;
$wdk_text_search_button = esc_html(wmvc_show_data('text_search_button', $settings));

global $wdk_text_reset_button;
$wdk_text_reset_button = esc_html(wmvc_show_data('text_reset_button', $settings));

global $wdk_text_more_button;
$wdk_text_more_button = esc_html(wmvc_show_data('text_more_button', $settings));

$current_url = '';

$results_page = wmvc_show_data('conf_link', $settings);
if(!is_array($results_page) && !empty($results_page)) {
    $results_page = get_permalink($results_page);
} else {
    $results_page = get_permalink(wdk_get_option('wdk_results_page'));

    if (!wdk_get_option('wdk_is_results_page_require')) {
        $obj_id = get_queried_object_id();
        $current_url = get_permalink( $obj_id );
    }
}

$form_opened = '';
if(isset($_GET['wdk_search_additional_opened']) && wmvc_xss_clean($_GET['wdk_search_additional_opened']) == 1) {
    $form_opened = 'open-form';
}
?>

<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-search layout_<?php echo esc_attr(wmvc_show_data('design_layout', $settings));?>
        <?php if
            (
                wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_ajax_results') &&
                isset($settings['is_ajax_enable']) && $settings['is_ajax_enable'] == 'yes'
            ):?>
                ajax_results_enabled
        <?php endif;?>
    ">
        <form data-current-link="<?php echo esc_url($current_url);?>" data-scrollto="<?php echo esc_attr(wmvc_show_data('search_scroll', $settings));?>" class="wdk-search-form wdk-skip-empty
         <?php if(wmvc_show_data('auto_search_enable', $settings) == 'yes'):?> auto_search <?php endif;?>
        <?php echo esc_html($form_opened);?> <?php if(!wdk_get_option('wdk_results_page')):?> wdk-result-page-notdefined <?php endif;?>" action="<?php echo esc_url($results_page);?>">
            <input name="rectangle_ne" type="hidden" class="wdk-hidden" value="<?php echo isset($_GET['rectangle_ne']) ? esc_attr(sanitize_text_field($_GET['rectangle_ne'])) : '';?>"/>
            <input name="rectangle_sw" type="hidden" class="wdk-hidden" value="<?php echo isset($_GET['rectangle_sw']) ? esc_attr(sanitize_text_field($_GET['rectangle_sw'])) : '';?>"/>
            <?php
                $field_id = $tab_field ;
                $field_values = wdk_field_option ($field_id, 'values_list');
                $values = array();
                if(!empty($field_values)){
                    $values = explode(',', $field_values);
                }
                $field_value = false;
                if(isset($_GET['field_'.$field_id])) {
                    $field_value = sanitize_text_field($_GET['field_'.$field_id]);
                }

            ?>
            <?php if(!empty( $values )):?>
            <div class="wdk-search-tabs">
                <input type="radio" name="field_<?php echo esc_attr($field_id);?>" id="wdk_tab_field_any" value="" <?php if(empty($field_value)):?>checked="checked"<?php endif;?>>
                <label for="wdk_tab_field_any">
                    <?php echo esc_html__('Any', 'wpdirectorykit');?>
                </label>
                <?php foreach ($values as $key => $value):?>
                <?php if(empty($value)) continue;?>
                <input type="radio" name="field_<?php echo esc_attr($field_id);?>" id="<?php echo esc_html($id_element);?>_wdk_tab_field_<?php echo esc_attr($key);?>" value="<?php echo esc_attr($value);?>" <?php if($field_value == $value):?>checked="checked"<?php endif;?>>
                <label for="<?php echo esc_html($id_element);?>_wdk_tab_field_<?php echo esc_attr($key);?>"><?php echo esc_html($value);?> 
                    <?php if(wmvc_show_data('tabs_count', $settings) == 'yes'):?>
                        <span class="tab_count"><?php echo esc_html(wmvc_show_data($value, $this->data['counts'][$field_id], 0));?></span>
                    <?php endif;?>
                </label>
                <?php endforeach;?>
            </div>
            <script>
                jQuery(document).ready(function(){
                    jQuery('.wdk_search_field_<?php echo esc_attr($field_id);?>').remove();
                });
            </script>
            <?php endif;?>

            <?php if(wmvc_user_in_role('administrator') || current_user_can('wdk_listings_manage')):?>
                <div class="section-widget-control">
                    <a class="wdk-c-btn wdk-c-edit" href="<?php echo esc_url(admin_url('admin.php?page=wdk_searchform'));?>" title="<?php echo esc_attr_e('Edit search form', 'selio'); ?>" target="_blank"><span class="dashicons dashicons-edit"></span></a>
                </div>
            <?php endif;?>
            <div class="wdk-row">
                <?php if(wmvc_show_data('enable_custom_fields', $settings) == 'yes'):?>
                    <?php wdk_generate_search_form_fields_elementor(wmvc_show_data('custom_fields', $settings), '', TRUE, $predefields_query); ?>
                <?php else:?>
                    <?php wdk_generate_search_form(1, '', TRUE, $predefields_query); ?>
                <?php endif;?>
                
                <?php if(!$wdk_button_search_defined): ?>
                            <div class="wdk-col wdk-col-btns">
                                <div class="wdk-field wdk-field-btn">
                                    <?php if(wmvc_show_data('enable_custom_fields', $settings) != 'yes'):?>
                                        <div class="wdk-field-group wdk-field-group-additional">
                                            <button id="wdk-search-additional" type="button" class="wdk-search-additional-btn"><?php echo esc_html($wdk_text_more_button); ?><i class="wdk-toggle-icon"></i></button>
                                            <input type='checkbox' style="display: none !important" value='1' name='wdk_search_additional_opened' />   
                                        </div>
                                    <?php endif;?>
                                    <div class="wdk-field-group wdk-field-group-reset">
                                        <button id="wdk-start-primary" type="reset" class="wdk-search-start wdk-search-reset wdk-click-load-animation">&nbsp;&nbsp;<?php echo esc_html(wmvc_show_data('text_reset_button', $settings)); ?>&nbsp;<i class="fa fa-spinner fa-spin fa-ajax-indicator" style="display: none;"></i>&nbsp;</button>
                                    </div>
                                    <div class="wdk-field-group wdk-field-group-search">
                                        <button id="wdk-start-primary" type="submit" class="wdk-search-start wdk-click-load-animation">
                                            <?php if(wmvc_show_data('field_button_icon_position', $settings) == 'left') :?>
                                                <?php \Elementor\Icons_Manager::render_icon( $settings['field_button_icon'], [ 'aria-hidden' => 'true', "class"=>'icon_search' ] ); ?>
                                            <?php endif;?>
                                            &nbsp;&nbsp;<?php echo esc_html(wmvc_show_data('text_search_button', $settings)); ?>
                                            <?php if(wmvc_show_data('field_button_icon_position', $settings) == 'right') :?>
                                                <?php \Elementor\Icons_Manager::render_icon( $settings['field_button_icon'], [ 'aria-hidden' => 'true', "class"=>'icon_search' ] ); ?>
                                            <?php endif;?>
                                            &nbsp;<i class="fa fa-spinner fa-spin fa-ajax-indicator" style="display: none;"></i>&nbsp;
                                        </button>

                                        <?php if(function_exists('run_wdk_save_search') && wdk_get_option('wdk_save_search_show_on_searchform')):?>
                                        <div class="section-widget-control right">
                                            <a class="wdk-c-btn wdk-c-edit wdk-save-search-button" href="#" data-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" title="<?php echo esc_attr_e('Save Search', 'selio'); ?>" target="_blank">
                                                <i class="fas fa-save" aria-hidden="true"></i>
                                                <i class="fa fa-spinner fa-spin fa-ajax-indicator"></i>
                                            </a>
                                        </div>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                        </div>
                    </div>
                    <?php endif; ?>
            </div>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#wdk_el_<?php echo esc_html($id_element);?> #wdk-search-additional').on('click', function (e) {
            e.preventDefault();
            if ($('#wdk_el_<?php echo esc_html($id_element);?> #wdk-form-additional').length) {
                var addition = $('#wdk_el_<?php echo esc_html($id_element);?> #wdk-form-additional');
                var form = $(this).closest('.wdk-search-form ');
                form.toggleClass('open-form');
                if (form.hasClass('open-form')) {
                    form.find("[name='wdk_search_additional_opened']").prop('checked', 'checked');
                    addition.slideDown();
                } else {
                    addition.slideUp();
                    form.find("[name='wdk_search_additional_opened']").prop('checked', false);
                }
            }
        })

        $("form.wdk-result-page-notdefined").on('submit', function() {
            wdk_log_notify('<?php echo esc_js(__('Results page not found, please configure results page', 'wpdirectorykit')); ?>', 'error');
            return false;
        });

        const search_highlight = (elem) =>
        {
            if(elem.is('select'))
            {   
                if(elem.val() == '' || elem.val() == 0 || elem.val() == null)
                {
                    // remove selector class
                    elem.closest('.select-item').removeClass('sel_class');
                    elem.parent().removeClass('sel_class');
                    elem.removeClass('sel_class');
                }
                else
                {                
                    // add selector class
                    elem.closest('.select-item').addClass('sel_class');
                    elem.parent().addClass('sel_class');
                    elem.addClass('sel_class');
                }
            }
            else if(elem.attr('type') == 'text')
            {
                if(elem.parent().find('.wdk_dropdown_tree').length > 0) // For treefield
                {
                    if(elem.val() != '' && elem.val() != null)
                    {
                        // add selector class
                        elem.closest('.wdk_dropdown_tree_style').find('.wdk_dropdown_tree').addClass('sel_class');
                        elem.parent().find('.btn-group:first-child').addClass('sel_class');
                    }
                    else
                    {
                        // remove selector class
                        elem.closest('.wdk_dropdown_tree_style').find('.wdk_dropdown_tree').removeClass('sel_class');
                        elem.parent().find('.btn-group:first-child').removeClass('sel_class');
                    }
                }
                else  // For basic input
                {
                    if(elem.val() != '' && elem.val() != null)
                    {
                        // add selector class
                        elem.addClass('sel_class');
                    }
                    else
                    {
                        // remove selector class
                        elem.removeClass('sel_class');
                    }
                }
            }
        }

        // On change value, change field style
        $('#wdk_el_<?php echo esc_html($id_element);?>').find('input, select').each(function(i)
        {
            $(this).on('change', function(){search_highlight($(this))});
            search_highlight($(this));
        })
        <?php if($is_edit_mode):?>
            wdk_select_init();

            if(typeof $.fn.fieldSliderRange == 'function' && typeof $.fn.ionRangeSlider == 'function') {
                $('.wdk-slider-range-field').fieldSliderRange();
            }
        <?php endif;?>
    });
    </script>
</div>
