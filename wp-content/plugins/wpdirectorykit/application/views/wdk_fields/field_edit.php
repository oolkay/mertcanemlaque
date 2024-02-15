<?php
/**
 * The template for Edit Field.
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
    <h1 class="wp-heading-inline"><?php echo __('Fields Management','wpdirectorykit'); ?></h1>
    <br /><br />
    <div class="wdk-body">
        <div class="postbox" style="display: block;">
            <div class="postbox-header">
                <h3><?php echo __('Add/Edit Field','wpdirectorykit'); ?></h3>
            </div>
            <div class="inside">

                <form method="post" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">

                    <?php wp_nonce_field( 'wdk-fields-edit_'.wmvc_show_data('idfield', $db_data, 0), '_wpnonce'); ?>

                    <?php 
                    $form->messages('class="alert alert-danger"', sprintf(__('Successfully saved, <a href="%1$s">Back to Fields Management </a>','wpdirectorykit'),admin_url('admin.php?page=wdk_fields')));
                    ?>

                    <table class="form-table" role="presentation">
                        <tbody>
                            <?php if(wmvc_show_data('field_type', $db_data) != "SECTION"):?>
                            <tr>
                                <th scope="row">
                                    <label for="field_type"><?php echo __('Section','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <?php  
                                        echo wmvc_select_option('section', $section_list, (!empty(wmvc_show_data('idfield', $db_data, '')) && isset($fields_categories[wmvc_show_data('idfield', $db_data, '')])) ? $fields_categories[wmvc_show_data('idfield', $db_data)] : ''); 
                                    ?>
                                </td>
                            </tr>
                            <?php endif;?>
                            <tr>
                                <th scope="row">
                                    <label for="field_type"><?php echo __('Field Type','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <?php  
                                        echo wmvc_select_option('field_type', $field_types, wmvc_show_data('field_type', $db_data, '')); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="field_label"><?php echo __('Field Label','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="field_label" type="text" id="field_label"
                                        value="<?php echo wmvc_show_data('field_label', $db_data, ''); ?>"
                                        class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="hint"><?php echo __('Hint','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="hint" type="text" id="hint"
                                        value="<?php echo wmvc_show_data('hint', $db_data, ''); ?>"
                                        class="regular-text">
                                    <p class="description" id="hint-description">
                                        <?php echo __('Hint is showing below fields like this one','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="placeholder"><?php echo __('Placeholder','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="placeholder" type="text" id="placeholder"
                                        value="<?php echo wmvc_show_data('placeholder', $db_data, ''); ?>"
                                        class="regular-text">
                                    <p class="description" id="placeholder-description">
                                        <?php echo __('Placeholder is showing on placeholder of fields or first value on dropdown','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label
                                        for="columns_number"><?php echo __('Columns number/Width','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="columns_number" type="number" id="columns_number"
                                        value="<?php echo wmvc_show_data('columns_number', $db_data, ''); ?>"
                                        class="regular-text">
                                    <p class="description" id="columns_number-description">
                                        <?php echo __('Full width is 12 columns, so define wanted columns allocation number for this field','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('Field is required','wpdirectorykit'); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <span><?php echo __('Field is required','wpdirectorykit'); ?></span>
                                        </legend>
                                        <label for="is_required">
                                            <input name="is_required" type="checkbox" id="is_required"
                                                value="1"
                                                <?php echo !empty(wmvc_show_data('is_required', $db_data, ''))?'checked':''; ?>>
                                            <?php echo __('Required','wpdirectorykit'); ?>
                                            <?php echo __('( Not for section type)','wpdirectorykit'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('Field is visible on Listing Preview','wpdirectorykit'); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <span><?php echo __('Field is visible on Listing Preview','wpdirectorykit'); ?></span>
                                        </legend>
                                        <label for="is_visible_frontend">
                                            <input name="is_visible_frontend" type="checkbox" id="is_visible_frontend"
                                                value="1"
                                                <?php echo !empty(wmvc_show_data('is_visible_frontend', $db_data, ''))?'checked':''; ?>>
                                            <?php echo __('( Not for section type)','wpdirectorykit'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('Field is visible on frontend submission','wpdirectorykit'); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <span><?php echo __('Field is visible on frontend submission','wpdirectorykit'); ?></span>
                                        </legend>
                                        <label for="is_visible_dashboard">
                                            <input name="is_visible_dashboard" type="checkbox" id="is_visible_dashboard"
                                                value="1"
                                                <?php echo !empty(wmvc_show_data('is_visible_dashboard', $db_data, ''))?'checked':''; ?>>
                                            <?php echo __('( Not for section type)','wpdirectorykit'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('Price format','wpdirectorykit'); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <span><?php echo __('Price format','wpdirectorykit'); ?></span>
                                        </legend>
                                        <label for="is_price_format">
                                            <input name="is_price_format" type="checkbox" id="is_price_format"
                                                value="1"
                                                <?php echo !empty(wmvc_show_data('is_price_format', $db_data, ''))?'checked':''; ?>>
                                            <?php echo __('Use wp price format based on language ( Only for number field type)','wpdirectorykit'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="validation"><?php echo __('Validation','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <?php  
                                        echo wmvc_select_option('validation', array_merge(array(''=> __('Not Selected','wpdirectorykit')),$this->field_m->fields_validations), wmvc_show_data('validation', $db_data, ''), 'id="validation"'); 
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label
                                        for="min_length"><?php echo __('Min Length','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="min_length" type="number" id="min_length"
                                        value="<?php echo wmvc_show_data('min_length', $db_data, ''); ?>"
                                        class="regular-text">
                                    <p class="description" id="columns_number-description">
                                        <?php echo __('Min Characters or min number (if number field type)','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label
                                        for="max_length"><?php echo __('Max Length','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="max_length" type="number" id="max_length"
                                        value="<?php echo wmvc_show_data('max_length', $db_data, ''); ?>"
                                        class="regular-text">
                                    <p class="description" id="columns_number-description">
                                        <?php echo __('Max Characters or max number (if number field type)','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <?php if(false):?>
                            <tr>
                                <th scope="row"><?php echo __('Visible on','wpdirectorykit'); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <span><?php echo __('Visible on','wpdirectorykit'); ?></span>
                                        </legend>
                                        <label for="is_visible_frontend">
                                            <input name="is_visible_frontend" type="checkbox" id="is_visible_frontend"
                                                value="1"
                                                <?php echo !empty(wmvc_show_data('is_visible_frontend', $db_data, ''))?'checked':''; ?>>
                                            <?php echo __('Frontend','wpdirectorykit'); ?>
                                        </label>
                                        <label for="is_visible_dashboard">
                                            <input name="is_visible_dashboard" type="checkbox" id="is_visible_dashboard"
                                                value="1"
                                                <?php echo !empty(wmvc_show_data('is_visible_dashboard', $db_data, ''))?'checked':''; ?>>
                                            <?php echo __('Dashboard','wpdirectorykit'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <?php endif;?>
                            <tr>
                                <th scope="row"><label for="icon_id"><?php echo __('Icon','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <?php  
                                        echo wmvc_upload_media('icon_id', wmvc_show_data('icon_id', $db_data, '')); 
                                    ?>
                                    <p class="description" id="icon_id-description">
                                        <?php echo __('Icon used for amenities or special places on website','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="prefix"><?php echo __('Prefix','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="prefix" type="text" id="prefix"
                                        value="<?php echo wmvc_show_data('prefix', $db_data, ''); ?>"
                                        class="regular-text">
                                    <p class="description" id="prefix-description">
                                        <?php echo __('Visible before field value usable in currency like $999','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="suffix"><?php echo __('Suffix','wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="suffix" type="text" id="suffix"
                                        value="<?php echo wmvc_show_data('suffix', $db_data, ''); ?>"
                                        class="regular-text">
                                    <p class="description" id="suffix-description">
                                        <?php echo __('Visible after field value usable in metrics like 200ft','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label
                                        for="values_list"><?php echo __('Values','wpdirectorykit'); ?></label></th>
                                <td>
                                    <input name="values_list" type="text" id="values_list"
                                        value="<?php echo wmvc_show_data('values_list', $db_data, ''); ?>"
                                        class="regular-text">
                                    <p class="description" id="values_list-description">
                                        <?php echo __('Values selectable in dropdown, separate with, like value1,value2,value3','wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save Changes','wpdirectorykit'); ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->view('general/footer', $data); ?>