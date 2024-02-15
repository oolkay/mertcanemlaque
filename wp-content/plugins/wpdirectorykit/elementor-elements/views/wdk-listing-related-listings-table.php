<?php

/**
 * The template for Element Bookings Prices table.
 * This is the template that elementor element table, listings
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>
<div class="wdk-booking-element" id="wdk_el_<?php echo esc_html($id_element); ?>">
    <div class="wdk-listing-related-listings-table">

        <table class="wdk-table responsive">
            <?php if (count($results) == 0) : ?>
                <tr class="no-items">
                    <td class="colspanchange" colspan="7"><?php echo esc_html__('Related listings not found', 'wpdirectorykit'); ?></td>
                </tr>
            <?php endif; ?>

            <?php foreach ($results as $key => $child_idlisting) : ?>
                <?php if (!wdk_field_value('is_activated', $child_idlisting) || !wdk_field_value('is_approved', $child_idlisting)) continue; ?>
                <tr class="wdk-sublistings-part">
                    <td>
                        <img src="<?php echo esc_url(wdk_image_src($child_idlisting, 'full')); ?>" alt="<?php echo esc_attr(wmvc_show_data('post_title', $child_idlisting)); ?>" class="wdk-image">
                    </td>
                    <?php foreach ($settings['related_fields_list'] as $field) : ?>
                        <?php
                        if (strpos($field['field'], '__') !== FALSE) {
                            $field['field'] = substr($field['field'], strpos($field['field'], '__') + 2);
                        }
                        ?>
                        <td>
                            <?php if ($field['is_link'] == 'yes') : ?>
                                <a href="<?php echo get_permalink($child_idlisting); ?>" title="<?php echo esc_attr__('View', 'wpdirectorykit'); ?>" target="blank">
                                <?php else : ?>
                                    <span>
                                    <?php endif; ?>

                                    <?php if ($field['is_stars'] == 'yes') : ?>
                                        <?php $stars = round(floatval(wdk_field_value($field['field'], $child_idlisting)), 2); ?>
                                        <span class="stars-lst">
                                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                <?php if ($i <= $stars) : ?>
                                                    <span><i class="fas fa-star star-active"></i></span>
                                                <?php elseif (abs($stars - $i) < 1) : ?>
                                                    <span><i class="fas fa-star-half-alt"></i></span>
                                                <?php elseif (false) : ?>
                                                    <span><i class="far fa-star innactive"></i></span>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </span>
                                    <?php else : ?>
                                        <?php echo esc_html($field['field_prefix']); ?>
                                        <?php echo esc_html(apply_filters('wpdirectorykit/listing/field/prefix', wdk_field_option($field['field'], 'prefix'), $field['field'])); ?>
                                        <?php echo esc_html(apply_filters('wpdirectorykit/listing/field/value', wdk_field_value_on_type($field['field'], $child_idlisting, '-'), $field['field'])); ?>
                                        <?php echo esc_html(apply_filters('wpdirectorykit/listing/field/suffix', wdk_field_option($field['field'], 'suffix'), $field['field'])); ?>
                                        <?php echo esc_html($field['field_suffix']); ?>
                                    <?php endif; ?>

                                    <?php if ($field['is_link'] == 'yes') : ?>
                                </a>
                            <?php else : ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <a href="<?php echo get_permalink($child_idlisting); ?>" class="wdk-btn"><?php echo esc_html__('More info', 'wpdirectorykit'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>