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
$wdk_button_search_defined=true;

global $wdk_enable_search_fields_toggle;
$wdk_enable_search_fields_toggle = false;


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

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-search-popup">
        <button type="button" class="toggle-btn elementor-clickable" 
        data-wdk-toggle="modal" data-wdk-target="#wdk_search_popup_modal_<?php echo esc_html($id_element); ?>" >
            <?php if(wmvc_show_data('text_toggle_button_icon_position', $settings) == 'left') :?>
                <?php \Elementor\Icons_Manager::render_icon( $settings['text_toggle_button_icon'], [ 'aria-hidden' => 'true', "class"=>'icon_popup' ] ); ?>
            <?php endif;?>
            &nbsp;&nbsp;<?php echo esc_html(wmvc_show_data('text_toggle_button', $settings));?>&nbsp;&nbsp;
            <?php if(wmvc_show_data('text_toggle_button_icon_position', $settings) == 'right') :?>
                <?php \Elementor\Icons_Manager::render_icon( $settings['text_toggle_button_icon'], [ 'aria-hidden' => 'true', "class"=>'icon_popup' ] ); ?>
            <?php endif;?>
        </button>
    </div>
                    
    <div class="wdk-modal wkd-fade wdk-search-popup-modal elementor-clickable nodetach" id="wdk_search_popup_modal_<?php echo esc_html($id_element); ?>" tabindex="-1" role="dialog"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-notice">
            <form data-current-link="<?php echo esc_url($current_url);?>" data-scrollto="<?php echo esc_attr(wmvc_show_data('search_scroll', $settings));?>" class="wdk-search-form wdk-skip-empty
                <?php if(wmvc_show_data('auto_search_enable', $settings) == 'yes'):?> auto_search <?php endif;?>
                <?php if(!wdk_get_option('wdk_results_page')):?> wdk-result-page-notdefined <?php endif;?>" action="<?php echo esc_url($results_page);?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 <?php $this->editing_element('text_popup', 'basic', array('class'=>'modal-title')); ?>><?php echo esc_html(wmvc_show_data('text_popup', $settings));?></h5>
                        <button type="button" class="close" data-wdk-dismiss="modal" aria-hidden="true" >
                            <span class="dashicons dashicons-no-alt"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="wdk-row wdk-fields-list">
                            <?php wdk_generate_search_form_fields_elementor(wmvc_show_data('custom_fields', $settings), '', TRUE, $predefields_query); ?>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <?php foreach (wmvc_show_data('custom_buttons', $settings) as $key => $custom_button):?>
                            <?php 
                        switch (wmvc_show_data('action_list_field_id', $custom_button)):
                            case 'button_reset':
                                echo '<button type="button" class="wdk-btn wdk-button-search-reset">'.(!empty(wmvc_show_data('placeholder', $custom_button)) ? wmvc_show_data('placeholder', $custom_button) : esc_html__('Reset', 'wpdirectorykit')).'</button>';
                                break;
                                case 'button_search':
                                    echo '<button type="submit" class="wdk-btn wdk-button-search-start wdk-click-load-animation">'.(!empty(wmvc_show_data('placeholder', $custom_button)) ? wmvc_show_data('placeholder', $custom_button) : esc_html__('Search', 'wpdirectorykit')).'</button>';
                                    break;
                                    case 'button_close':
                                        echo '<button type="button" class="wdk-btn wdk-button-close" data-wdk-dismiss="modal">'.(!empty(wmvc_show_data('placeholder', $custom_button)) ? wmvc_show_data('placeholder', $custom_button) : esc_html__('Close', 'wpdirectorykit')).'</button>';
                                        break;
                                        
                                        default:
                                        # code...
                                        break;
                                    endswitch;
                                    ?>
                        <?php endforeach;?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
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

            wdk_log_modal();
        <?php endif;?>
    });
    </script>
</div>
