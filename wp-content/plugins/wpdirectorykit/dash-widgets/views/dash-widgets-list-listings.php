<?php
/**
 * The template for Shortcode Listings list
 * This is the template that Shortcode listings list
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?> 
<div class="wdk-dashwidget-element" id="wdk_dashwidget_<?php echo esc_attr($id_element);?>">
    <div class="wdk-latest-listings wdk-wrap">
        <?php if(empty($results)):?>
            <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Results not found', 'wpdirectorykit');?></p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped table-view-list pages wdk-table responsive" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width:50px;"><?php echo __('#ID', 'wpdirectorykit'); ?></th>
                        <th><?php echo __('Image', 'wpdirectorykit'); ?></th>
                        <th><?php echo __('Title', 'wpdirectorykit'); ?></th>
                        <th class="actions_col"><?php echo __('Actions', 'wpdirectorykit'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($results as $key=>$listing): ?>
                    <?php
                        $url = get_permalink($listing);
                    ?>
                    <tr>
                        <td data-label="<?php echo __('#ID', 'wpdirectorykit'); ?>"><?php echo wdk_field_value('post_id', $listing); ?></td>
                        <td>
                            <a href="<?php echo esc_url($url);?>" target="_blank" title="<?php echo esc_attr(wdk_field_value('post_title', $listing));?>" class="d-block">
                                <img src="<?php echo esc_url(wdk_image_src($listing, 'full'));?>" alt="<?php echo esc_attr(wdk_show_data('post_title', $listing, '', TRUE, TRUE));?>">
                            </a>
                        </td>
                        <td class="title column-title page-title"  data-label="<?php echo esc_html__('Title','wpdirectorykit');?>" class="max-width">
                            <strong>
                                <a class="row-title" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_listing&id=" . wmvc_show_data('ID', $listing, '-')); ?>"><?php echo wmvc_show_data('post_title', $listing, '-'); ?></a>
                                <?php if(!wmvc_show_data('is_activated', $listing, 0)): ?>
                                <span class="label label-danger"><?php echo __('Not activated', 'wpdirectorykit'); ?></span>
                                <?php endif; ?>
                                <?php if(!wmvc_show_data('is_approved', $listing, 0) && function_exists('run_wdk_membership')): ?>
                                <span class="label label-danger"><?php echo esc_html__('Not approved', 'wpdirectorykit'); ?></span>
                                <?php endif; ?>
                                <?php if(wmvc_show_data('is_featured', $listing, 0)): ?>
                                <span class="label label-info"><?php echo esc_html__('featured', 'wpdirectorykit'); ?></span>
                                <?php endif; ?>
                            </strong>
                        </td>
                        <td data-label="<?php echo esc_html__('Actions','wpdirectorykit');?>" class="actions_col check-column">
                            <div class="nav">
                                <a href="<?php echo esc_url($url); ?>" class="" target="_blank"><span class="dashicons dashicons-search"></span></a>

                                <?php if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')):?>
                                    <?php if(function_exists('wdk_dash_url') && wdk_dash_url('dash_page=listings&function=edit') && current_user_can('edit_own_listings')):?>
                                        <a href="<?php echo esc_url(wdk_dash_url('dash_page=listings&function=edit&id=' . wmvc_show_data('post_id', $listing, '-'))); ?>" class=""><span class="dashicons dashicons-edit"></span></a>
                                    <?php endif;?>
                                <?php else:?>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&id='.wdk_field_value('post_id', $listing))); ?>" class=""><span class="dashicons dashicons-edit"></span></a>
                                <?php endif;?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="footer">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wdk'));?>" class="button">
                    <?php echo esc_html__('Show More', 'wpdirectorykit'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

