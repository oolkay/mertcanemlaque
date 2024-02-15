<?php
/**
 * The template for Categories Management.
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
    <h1 class="wp-heading-inline"><?php echo __('Categories','wpdirectorykit'); ?> <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_category&function=edit"); ?>" class="button button-primary" id="add_category_button"><?php echo __('Add Category','wpdirectorykit'); ?></a></h1>
    <br />
    <?php
        $success_message = NULL;
        if(isset($_GET['custom_message']))
            $success_message = esc_html(urldecode($_GET['custom_message']));

        $form->messages('class="alert alert-danger"', $success_message);
    ?>
    <br />
    <form method="GET" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
        <table class="wp-list-table widefat fixed striped table-view-list pages">
            <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php echo __('Select All', 'wpdirectorykit'); ?></label><input id="cb-select-all-1" type="checkbox"></td>
                    <th><?php echo __('Title','wpdirectorykit'); ?></th>
                    <th><?php echo __('Order','wpdirectorykit'); ?></th>
                    <th><?php echo __('Level','wpdirectorykit'); ?></th>
                    <th><?php echo __('Date','wpdirectorykit'); ?></th>
                    <th class="actions_column"><?php echo __('Actions','wpdirectorykit'); ?></th>
                </tr>
            </thead>
            <?php if(count($categories) == 0): ?>
                <tr class="no-items"><td class="colspanchange" colspan="6"><?php echo __('No Categories found.','wpdirectorykit'); ?></td></tr>
            <?php endif; ?>
            <?php foreach ( $categories as $category ):?>
                <tr>
                    <th scope="row" class="check-column">
                        <input id="cb-select-<?php echo wmvc_show_data('idcategory', $category, '-'); ?>" type="checkbox" name="post[]" value="<?php echo wmvc_show_data('idcategory', $category, '-'); ?>">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text"><?php echo __('Is Locked', 'wpdirectorykit'); ?></span>
                        </div>
                    </th>
                    <td>
                        <?php echo str_pad('', wmvc_show_data('level', $category, 0)*12, '&nbsp;').'|-'; ?><a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_category&function=edit&id=".wmvc_show_data('idcategory', $category, '-')); ?>"><?php echo wmvc_show_data('category_title', $category, '-').' #'.wmvc_show_data('idcategory', $category, '-'); ?></a>
                    </td>
                    <td>
                        <?php echo wmvc_show_data('order_index', $category, '-'); ?>
                    </td>
                    <td>
                        <?php echo wmvc_show_data('level', $category, '-'); ?>
                    </td>
                    <td>
                        <?php echo wdk_get_date(wmvc_show_data('date', $category), false); ?>
                    </td>
                    <td class="actions_column">
                        <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_category&function=edit&id=".wmvc_show_data('idcategory', $category, '-')); ?>" title="<?php echo esc_attr__('Edit','wpdirectorykit');?>"><span class="dashicons dashicons-edit"></span></a>
                        <a class="question_sure" title="<?php echo esc_attr__('Remove','wpdirectorykit');?>" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_category&function=delete&id=".wmvc_show_data('idcategory', $category, '-')); ?>&_wpnonce=<?php echo wp_create_nonce( 'wdk-category-delete_'.wmvc_show_data('idcategory', $category, '-'));?>"><span class="dashicons dashicons-no"></span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2"><?php echo __('Select All', 'wpdirectorykit'); ?></label><input id="cb-select-all-2" type="checkbox"></td>
                    <th><?php echo __('Title','wpdirectorykit'); ?></th>
                    <th><?php echo __('Order','wpdirectorykit'); ?></th>
                    <th><?php echo __('Level','wpdirectorykit'); ?></th>
                    <th><?php echo __('Date','wpdirectorykit'); ?></th>
                    <th class="actions_column"><?php echo __('Actions','wpdirectorykit'); ?></th>
                </tr>
            </tfoot>
        </table>
        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <?php wp_nonce_field( 'wdk-category-bulk', '_wpnonce'); ?>
                <label for="bulk-action-selector-bottom" class="screen-reader-text"><?php echo __('Select bulk action', 'wpdirectorykit'); ?></label>
                <select name="action" id="bulk-action-selector-bottom">
                    <option value="-1"><?php echo __('Bulk actions', 'wpdirectorykit'); ?></option>
                    <option value="delete" class="hide-if-no-js"><?php echo __('Delete', 'wpdirectorykit'); ?></option>
                </select>
                <input type="hidden" name="page" value="wdk_category" />
                <input type="submit" id="table_action" class="button action" name="table_action" value="<?php echo esc_attr__('Apply', 'wpdirectorykit'); ?>">
            </div>
            <br class="clear">
        </div>
    </form>
    <br />
    <div class="alert alert-info" style="margin-bottom:20px" role="alert"><?php echo sprintf(__('%1$s How to manage Categories documentation%2$s', 'wpdirectorykit'),'<a href="//wpdirectorykit.com/documentation/#!/categories" target="_blank">','</a>'); ?></div>
    <iframe width="560" height="315" src="//www.youtube.com/embed/051E9Lzn0Vs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

</div>

<script>
    // Generate table
    jQuery(document).ready(function($) {
        $('.question_sure').on('click', function(){
            return confirm("<?php echo esc_js(__('Are you sure? Selected item will be completely removed!','wpdirectorykit')); ?>");
        });
    });
</script>

<?php $this->view('general/footer', $data); ?>
