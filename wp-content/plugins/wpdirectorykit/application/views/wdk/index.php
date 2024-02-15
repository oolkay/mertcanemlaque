<?php
/**
 * The template for Listings Management.
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
    <h1 class="wp-heading-inline"><?php echo __('Listings Management', 'wpdirectorykit'); ?> <a href="<?php echo get_admin_url() . "admin.php?page=wdk_listing"; ?>" class="button button-primary" title="<?php echo esc_attr__('Add Listing','wpdirectorykit');?>" id="add_listing_button"><?php echo __('Add Listing', 'wpdirectorykit'); ?></a></h1>
    <?php
    if(!get_option('wdk_listing_page') || get_post_status(get_option('wdk_listing_page')) !='publish'
        || !get_option('wdk_results_page') || get_post_status(get_option('wdk_results_page')) !='publish'
        ):?>
        <div  class="notice notice-success">
            <p>
                <?php echo __('Listing Preview page or Listings Result page missing','wpdirectorykit'); ?>
                <a href="<?php echo esc_url(get_admin_url()) . "admin.php?page=wdk_settings&function=import_demo"; ?>" class="button button-primary" id="reset_data_field_button">
                    <?php echo __('Import Demo Data','wpdirectorykit'); ?>
                </a>
            </p>
        </div>
    <?php endif;?>

    <?php
        if(class_exists('Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Listings')) {
            $widget = new Wdk\DashWidgets\Widgets\WDK_Dashboard_Widget_Stats_Listings();
            $widget -> widget();
        }
    ?>

    <form method="GET" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate" class="filters-form">
        <div class="tablenav top">
            <div class="alignleft actions">
                <input type="hidden" name="page" value="wdk" />


                <?php if(get_option('wdk_is_location_enabled', FALSE)): ?>
                    <label class="screen-reader-text" for="location_id"><?php echo __('Filter by location', 'wpdirectorykit'); ?></label>
                    <?php echo wmvc_select_option('location_id', $locations, wmvc_show_data('location_id', $db_data, ''), NULL, __('Location', 'wpdirectorykit')); ?>
                <?php endif;?>

                <?php if(get_option('wdk_is_category_enabled', FALSE)): ?>
                    <label class="screen-reader-text" for="category_id"><?php echo __('Filter by category', 'wpdirectorykit'); ?></label>
                    <?php echo wmvc_select_option('category_id', $categories, wmvc_show_data('category_id', $db_data, ''), NULL, __('Category', 'wpdirectorykit')); ?>
                <?php endif;?>

                <label class="screen-reader-text" for="user_id_editor"><?php echo esc_html__('Filter by user', 'wpdirectorykit'); ?></label>
                <?php echo wmvc_select_option('user_id_editor', $users, wmvc_show_data('user_id_editor', $db_data, ''), NULL, __('User', 'wpdirectorykit')); ?>

                <label class="screen-reader-text" for="search"><?php echo __('Filter by keyword', 'wpdirectorykit'); ?></label>
                <input type="text" name="search" id="search" class="postform left" value="<?php echo esc_attr(wmvc_show_data('search', $db_data, '')); ?>" placeholder="<?php echo __('Filter by keyword', 'wpdirectorykit'); ?>" />

                <label class="screen-reader-text" for="order_by"><?php echo __('Order By', 'wpdirectorykit'); ?></label>
                <?php echo wmvc_select_option('order_by', $order_by, wmvc_show_data('order_by', $db_data, ''), NULL, __('Order by', 'wpdirectorykit')); ?>


                <?php
                    $custom_field_init = false;
                    foreach ($_GET as $key => $value) {
                        if(stripos($key, 'c_field_') !== FALSE && stripos($key, '_field',8) !== FALSE) {
                            $field_id = substr($key, 8, (stripos($key, '_',8) - 8) );
                            ?>
                                <span class="custom_parameter" data-key="<?php echo esc_attr($field_id);?>">
                                    <?php echo wmvc_select_option('c_field_'.$field_id.'_field', $fields_list, wmvc_show_data('c_field_'.$field_id.'_field', $_GET, ''), 'class="cus_p_field"', __('Field', 'wpdirectorykit')); ?>
                                    <?php echo wmvc_select_option('c_field_'.$field_id.'_like', array('=='=>'=','>'=>'>','<'=>'<'), wmvc_show_data('c_field_'.$field_id.'_like', $_GET, ''), 'class="cus_p_like"'); ?>
                                    <input type="text" name="c_field_<?php echo esc_attr($field_id);?>_value" value="<?php echo wmvc_show_data('c_field_'.$field_id.'_value', $_GET, ''); ?>" class="cus_p_value" placeholder="<?php echo __('Value', 'wpdirectorykit'); ?>" />
                                </span>
                            <?php
                            $custom_field_init = true;
                        }
                    };
                ?>
                <?php if(!$custom_field_init):?>
                    <span class="custom_parameter">
                        <?php echo wmvc_select_option('c_field_1_field', $fields_list, wmvc_show_data('c_field_1_field', $db_data, ''), 'class="cus_p_field"', __('Field', 'wpdirectorykit')); ?>
                        <?php echo wmvc_select_option('c_field_1_like', array('=='=>'=','>'=>'>','<'=>'<'), wmvc_show_data('c_field_1_like', $db_data, ''), 'class="cus_p_like"'); ?>
                        <input type="text" name="c_field_1_value" value="<?php echo wmvc_show_data('c_field_1_value', $db_data, ''); ?>" class="cus_p_value" placeholder="<?php echo __('Value', 'wpdirectorykit'); ?>" />
                    </span>
                <?php endif;?>

                <a href="" class="btn_parameter remove"><span class="dashicons dashicons-minus"></span></a>
                <a href="" class="btn_parameter add"><span class="dashicons dashicons-plus-alt2"></span></a>

                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="<?php echo __('Filter', 'wpdirectorykit'); ?>">
            
                <input type="hidden" name="is_featured" value="<?php echo esc_attr(wmvc_show_data('is_featured', $db_data, ''));?>">
                <input type="hidden" name="is_activated" value="<?php echo esc_attr(wmvc_show_data('is_activated', $db_data, ''));?>">
            </div>
            <?php echo wmvc_xss_clean($pagination_output); ?>
            <br class="clear">
        </div>
    </form>

    <?php 
    if (function_exists('PLL')){
        $pll_langs = pll_the_languages( array( 'raw' => 1 ) );
    } 
    ?>

    <form method="GET" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
        <table class="wp-list-table widefat fixed striped table-view-list pages">
            <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php echo __('Select All', 'wpdirectorykit'); ?></label><input id="cb-select-all-1" type="checkbox"></td>
                    <th style="width:50px;"><?php echo __('#ID', 'wpdirectorykit'); ?></th>
                    <th><?php echo __('Title', 'wpdirectorykit'); ?></th>
                    <?php if(get_option('wdk_is_category_enabled', FALSE)): ?>
                    <th><?php echo __('Category', 'wpdirectorykit'); ?></th>
                    <?php endif; ?>
                    <th style="text-align: center;"><?php echo __('Image', 'wpdirectorykit'); ?></th>
                    <th><?php echo __('Post Date', 'wpdirectorykit'); ?></th>
                    <?php if (function_exists('PLL')): ?>
                    <?php $pll_langs = pll_the_languages( array( 'raw' => 1 ) );
                          foreach($pll_langs as $pll_lang): ?>
                    <th class="manage-column column-language_<?php echo esc_attr($pll_lang['slug']); ?>"><img src="<?php echo esc_html($pll_lang['flag']); ?>" /></th>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <th class="actions_column"><?php echo __('Actions', 'wpdirectorykit'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($listings) == 0) : ?>
                    <tr class="no-items">
                        <td class="colspanchange" colspan="7"><?php echo __('No Listings found.', 'wpdirectorykit'); ?></td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($listings as $listing) : ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <input id="cb-select-<?php echo wmvc_show_data('ID', $listing, '-'); ?>" type="checkbox" name="post[]" value="<?php echo wmvc_show_data('ID', $listing, '-'); ?>">
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text"><?php echo __('Is Locked', 'wpdirectorykit'); ?></span>
                            </div>
                        </th>
                        <td>
                            <?php echo wmvc_show_data('ID', $listing, '-'); ?>
                        </td>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong>
                                <a class="row-title" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_listing&id=" . wmvc_show_data('ID', $listing, '-')); ?>"><?php echo wmvc_show_data('post_title', $listing, '-'); ?></a>
                                <?php if(!wmvc_show_data('is_activated', $listing, 0)): ?>
                                <span class="label label-danger"><?php echo __('Not activated', 'wpdirectorykit'); ?></span>
                                <?php endif; ?>
                                <?php if(!wmvc_show_data('is_approved', $listing, 0) && function_exists('run_wdk_membership')): ?>
                                <span class="label label-danger"><?php echo esc_html__('Not approved', 'wpdirectorykit'); ?></span>
                                <?php endif; ?>

                                <?php if(wdk_get_option('wdk_is_featured_enabled', FALSE)): ?>
                                    <?php if(wmvc_show_data('is_featured', $listing, 0)): ?>
                                    <span class="label label-info"><?php echo esc_html__('featured', 'wpdirectorykit'); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </strong>
                            <div class="row-actions">
                                <span class="edit"><a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_listing&id=" . wmvc_show_data('ID', $listing, '-')); ?>"><?php echo __('Edit', 'wpdirectorykit'); ?></a> | </span>
                                <span class="trash "><a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk&function=delete&paged=".esc_attr($paged)."&id=" . wmvc_show_data('ID', $listing, '-')); ?>&_wpnonce=<?php echo wp_create_nonce( 'wdk-listing-delete_'.wmvc_show_data('ID', $listing, '-'));?>" class="submitdelete question_sure"><?php echo __('Delete', 'wpdirectorykit'); ?></a> | </span>
                                <span class="view"><a href="<?php echo get_permalink($listing); ?>" target="blank"><?php echo __('View', 'wpdirectorykit'); ?></a></span>
                            </div>


                            <?php if(get_option('wdk_sub_listings_enable')): ?>
                                <?php if(!empty(wmvc_show_data('listing_related_ids', $listing))):?>
                                <div class="">
                                    <a href="#" class="laoding_sublistings" data-listing_id = '<?php echo esc_attr(wmvc_show_data('ID', $listing, '-'));?>'><?php echo esc_html__('Show Related','wpdirectorykit');?></a>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>

                        <?php if(get_option('wdk_is_category_enabled', FALSE)): ?>
                        <td>
                        <?php echo wmvc_show_data($listing->category_id, $categories, '-'); ?>
                            <?php 
                                $other_categories = wdk_generate_other_categories_fast($listing->categories_list);

                                if(!empty($other_categories)):?>
                                    <br>
                                    <span style="display: inline-block;padding-top: 10px;" ><?php echo esc_html(join(', ',$other_categories));?></span>
                                <?php endif;?>
                        </td>
                        <?php endif; ?>
                        
                        <td style="text-align: center;">
                            <a class="img-link" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_listing&id=" . wmvc_show_data('ID', $listing, '-')); ?>">
                                <img src="<?php echo esc_url(wdk_image_src($listing));?>" alt="thumb" style="height:70px;width:110px;object-fit:cover;text-align: center;"/>
                            </a>
                        </td>
                        <td>
                            <?php echo wdk_get_date($listing->post_date, false); ?>
                        </td>
                        <?php if (function_exists('PLL')): ?>
                        <?php foreach($pll_langs as $pll_lang): ?>
                        <?php if($pll_lang['slug'] == pll_get_post_language($listing->post_id, 'slug' )): ?>
                        <td><img src="<?php echo esc_html($pll_lang['flag']); ?>" /></td>
                        <?php else: ?>
                        <td><a class="pll_icon_edit translation_<?php echo esc_attr($listing->post_id); ?>" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_listing&id=" . pll_get_post( $listing->post_id, $pll_lang['slug'] )); ?>"></a></td>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <td class="actions_column">
                            <a href="<?php echo get_permalink($listing); ?>" title="<?php echo esc_attr__('View','wpdirectorykit');?>" target="blank"><span class="dashicons dashicons-visibility"></span></a>
                            <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_listing&id=" . wmvc_show_data('ID', $listing, '-')); ?>" title="<?php echo esc_attr__('Edit','wpdirectorykit');?>"><span class="dashicons dashicons-edit"></span></a>
                            <a class="question_sure" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk&function=delete&paged=".esc_attr($paged)."&id=" . wmvc_show_data('ID', $listing, '-')."&_wpnonce=".wp_create_nonce( 'wdk-listing-delete_'.wmvc_show_data('ID', $listing, '-'))); ?>"  title="<?php echo esc_attr__('Remove','wpdirectorykit');?>"><span class="dashicons dashicons-no"></span></a>
                        </td>
                    </tr>

                    <?php if(get_option('wdk_sub_listings_enable')): ?>
                        <?php if(!empty(wmvc_show_data('listing_related_ids', $listing))):?>
                            <?php if(false)foreach (explode(',',wmvc_show_data('listing_related_ids', $listing, '')) as $key => $child_idlisting):?>
                            <tr class="child">
                                <th scope="row"></th>
                                <td scope="row"></td>
                                <td colspan="1">
                                    <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&id='.$child_idlisting));?>"><?php echo esc_html('#'.$child_idlisting.', '.wdk_field_value('post_title', $child_idlisting));?></a> 
                                </td>
                                <td>
                                    <?php echo wdk_field_value('category_id', $categories); ?>
                                </td>
                                <td style="text-align: center;">
                                    <a class="img-link" href="<?php echo get_admin_url() . "admin.php?page=wdk_listing&id=" . $child_idlisting; ?>">
                                        <img src="<?php echo esc_url(wdk_image_src(array('listing_images'=>wdk_field_value('listing_images', $child_idlisting))));?>" alt="thumb" style="height:50px;width:65px;object-fit:cover;text-align: center;"/>
                                    </a>
                                </td>
                                <td>
                                    <?php echo wdk_get_date(wdk_field_value('date', $child_idlisting), false); ?>
                                </td>
                                <td class="actions_column">
                                    <a href="<?php echo get_permalink($child_idlisting); ?>" title="<?php echo esc_attr__('View','wpdirectorykit');?>" target="blank"><span class="dashicons dashicons-visibility"></span></a>
                                    <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_listing&id=" . $child_idlisting); ?>" title="<?php echo esc_attr__('Edit','wpdirectorykit');?>"><span class="dashicons dashicons-edit"></span></a>
                                    <a class="question_sure" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk&function=delete&paged=".esc_attr($paged)."&id=" . $child_idlisting."&_wpnonce=".wp_create_nonce( 'wdk-listing-delete_'.wmvc_show_data('ID', $listing, '-'))); ?>"  title="<?php echo esc_attr__('Remove','wpdirectorykit');?>"><span class="dashicons dashicons-no"></span></a>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        <?php endif;?>
                    <?php endif;?>
            <?php endforeach;?>
            </tbody>    
            <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2"><?php echo __('Select All', 'wpdirectorykit'); ?></label><input id="cb-select-all-2" type="checkbox"></td>
                    <th style="width:50px;"><?php echo __('#ID', 'wpdirectorykit'); ?></th>
                    <th><?php echo __('Title', 'wpdirectorykit'); ?></th>
                    <?php if(get_option('wdk_is_category_enabled', FALSE)): ?>
                    <th><?php echo __('Category', 'wpdirectorykit'); ?></th>
                    <?php endif; ?>
                    <th style="text-align: center;"><?php echo __('Image', 'wpdirectorykit'); ?></th>
                    <th><?php echo __('Post Date', 'wpdirectorykit'); ?></th>
                    <?php if (function_exists('PLL')): ?>
                    <?php foreach($pll_langs as $pll_lang): ?>
                    <th><img src="<?php echo esc_html($pll_lang['flag']); ?>" /></th>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <th class="actions_column"><?php echo __('Actions', 'wpdirectorykit'); ?></th>
                </tr>
            </tfoot>
        </table>
        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <?php wp_nonce_field( 'wdk-listing-bulk', '_wpnonce'); ?>
                <label for="bulk-action-selector-bottom" class="screen-reader-text"><?php echo __('Select bulk action', 'wpdirectorykit'); ?></label>
                <select name="action" id="bulk-action-selector-bottom">
                    <option value="-1"><?php echo __('Bulk actions', 'wpdirectorykit'); ?></option>
                    <option value="delete" class="hide-if-no-js"><?php echo __('Delete', 'wpdirectorykit'); ?></option>
                    <option value="deactivate" class="hide-if-no-js"><?php echo __('Deactivate', 'wpdirectorykit'); ?></option>
                    <option value="activate" class="hide-if-no-js"><?php echo __('Activate', 'wpdirectorykit'); ?></option>
                    <?php if(function_exists('run_wdk_membership')):?>
                        <option value="deapprove" class="hide-if-no-js"><?php echo __('Deapprove', 'wpdirectorykit'); ?></option>
                        <option value="approve" class="hide-if-no-js"><?php echo __('Approve', 'wpdirectorykit'); ?></option>
                    <?php endif;?>
                </select>
                <input type="hidden" name="page" value="wdk" />
                <input type="submit" id="table_action" class="button action" name="table_action" value="<?php echo esc_attr__('Apply', 'wpdirectorykit'); ?>">
            </div>

            <?php echo wmvc_xss_clean($pagination_output); ?>
            <br class="clear">
        </div>
    </form>
</div>
<?php
    wp_enqueue_style('wdk-notify');
    wp_enqueue_script('wdk-notify');
?>
<script>
    // Generate table
    jQuery(document).ready(function($) {

        $('.question_sure').on('click', function() {
            return confirm("<?php echo esc_js(__('Are you sure? Selected item will be completely removed!', 'wpdirectorykit')); ?>");
        });
        wdk_loading_sublistings();
        wdk_custom_search_parameters();
    });

    const wdk_loading_sublistings = ($selector_btn = '.laoding_sublistings') => {
        var init,action,event, eventRemove;

        event = (elem) => {
            var listing_id = jQuery(elem).attr('data-listing_id');
            var self = jQuery(elem);

            if(self.attr('disabled')) {
                return false;
            }

            self.addClass('wdk_btn_load_indicator out');
            self.attr('disabled','disabled');

            var ajax_param = {
                "page": 'wdk_backendajax',
                "function": 'loading_sublistings',
                "action": 'wdk_public_action',
                "listing_id": listing_id,
                "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-backendajax'));?>',
            };
            
            jQuery.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
                function(data){
                    
                if(data.popup_text_success)
                    wdk_log_notify(data.popup_text_success);
                    
                if(data.popup_text_error)
                    wdk_log_notify(data.popup_text_error, 'error');
                    
                self.removeClass('wdk_btn_load_indicator out');

                data.results
                var html = '';
                jQuery.each(data.results, function(index, value){
                    html += '<tr class="child">\n\
                        <th scope="row"></th>\n\
                        <td scope="row">'+value.post_id+'</td>\n\
                        <td colspan="1">\n\
                            <a target="_blank" href="'+value.listing_edit_url+'">'+value.post_title+'</a>\n\
                        </td>\n\
                        <td>\n\
                            '+value.category+'\n\
                        </td>\n\
                        <td style="text-align: center;">\n\
                            <a class="img-link" href="'+value.listing_edit_url+'">\n\
                                <img src="'+value.image_src+'" alt="thumb" style="height:50px;width:65px;object-fit:cover;text-align: center;"/>\n\
                            </a>\n\
                        </td>\n\
                        <td>\n\
                            '+value.date+'\n\
                        </td>\n\
                        <td class="actions_column">\n\
                            <a href="'+value.listing_view_url+'" title="<?php echo esc_attr__('View','wpdirectorykit');?>" target="blank"><span class="dashicons dashicons-visibility"></span></a>\n\
                            <a href="'+value.listing_edit_url+'" title="<?php echo esc_attr__('Edit','wpdirectorykit');?>"><span class="dashicons dashicons-edit"></span></a>\n\
                            <a class="question_sure remove_event" href="'+value.listing_remove_url+'" data-listing_id ="'+value.post_id+'" title="<?php echo esc_attr__('Remove','wpdirectorykit');?>"><span class="dashicons dashicons-no"></span></a>\n\
                        </td>\n\
                    </tr>'
                });

                jQuery( html ).insertAfter( self.closest('tr') );
                jQuery(self).remove();

            }).always(function(data) {
                if(true) {
                    eventRemove();
                } else {
                    jQuery('.child .question_sure').off().on('click', function() {
                        return confirm("<?php echo esc_js(__('Are you sure? Selected item will be completely removed!', 'wpdirectorykit')); ?>");
                    });
                }
            });

            return false;
        };

        eventRemove = () => {
            jQuery('.child .remove_event').off().on('click', function(e) {
                e.preventDefault();
                if(!confirm("<?php echo esc_js(__('Are you sure? Selected item will be completely removed!', 'wpdirectorykit')); ?>")) {
                    return false;
                }

                var self = jQuery(this);
                var listing_id = self.attr('data-listing_id');

                var ajax_param = {
                    "page": 'wdk_backendajax',
                    "function": 'remove_listing',
                    "action": 'wdk_public_action',
                    "listing_id": listing_id,
                    "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-backendajax'));?>',
                };

                var tr = self.closest('tr');

                self.addClass('wdk_btn_load_indicator out');
                
                jQuery.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
                function(data){
                    
                    if(data.popup_text_success)
                    wdk_log_notify(data.popup_text_success);
                    
                    if(data.popup_text_error)
                    wdk_log_notify(data.popup_text_error, 'error');
                    
                    if(data.success) {
                        
                        setTimeout(function() {
                            tr.animate({ opacity: 1/2 }, 500, function(){tr.remove();});
                        }, 200);
                        
                    } else {
                        self.removeClass('loading_removing').addClass('loading_removed_error');
                    }
                    
                }).always(function(data) {
                    self.removeClass('wdk_btn_load_indicator out').addClass('wdk_btn_load_success out');
                });
            })
        };

        action = () => {
            /*
            jQuery($selector_btn).on('click', function(e){
                e.preventDefault();
                event(jQuery(this));
            }); 
            */

            document.querySelectorAll($selector_btn).forEach(elem => elem.addEventListener('click', (e)=>{
                e.preventDefault();
                event(e.target);
            }, false));

        };

        init = () => {
            action();
        };

        init();
    };

    const wdk_custom_search_parameters = ($selector_customParameter = '.custom_parameter') => {
        var selector_customParameter = jQuery($selector_customParameter).first();

        var event = () => {
            jQuery('.btn_parameter.add').off().on('click', function(e){
                e.preventDefault();
                var cus_field = selector_customParameter.clone().insertAfter(jQuery($selector_customParameter).last());
                cus_field.find('input,select').val('');
                
                var key = 1;
                while (jQuery('.custom_parameter .cus_p_field[name="c_field_'+key+'_field"]').length) {
                    key++;
                }

                cus_field.find('.cus_p_field').attr('name', 'c_field_'+key+'_field');
                cus_field.find('.cus_p_like').attr('name', 'c_field_'+key+'_like');
                cus_field.find('.cus_p_value').attr('name', 'c_field_'+key+'_value');
            });

            jQuery('.btn_parameter.remove').off().on('click', function(e){
                e.preventDefault();
                if(jQuery($selector_customParameter).length>1) {
                    jQuery($selector_customParameter).last().remove();
                } else {
                    jQuery($selector_customParameter).last().find('input,select').val('');
                }
            });
        };

        event();
    };
</script>

<?php $this->view('general/footer', $data); ?>