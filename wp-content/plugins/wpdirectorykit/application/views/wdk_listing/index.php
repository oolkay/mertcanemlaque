<?php
/**
 * The template for Edit Listing.
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
    <h1 class="wp-heading-inline"><?php echo __('Add/Edit Listing', 'wpdirectorykit'); ?></h1>
    <br /><br />
    <div class="wdk-body">
        <form method="post" class="form_listing" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" enctype="multipart/form-data" novalidate="novalidate">
            <?php wp_nonce_field( 'wdk-listing-edit_'.wmvc_show_data('ID', $db_data, 0), '_wpnonce'); ?>

            <div class="postbox" style="display: block;">
                <div class="postbox-header">
                    <h3 class="wide">
                        <?php echo __('Main Data', 'wpdirectorykit'); ?> 
                        <?php if(get_option('wdk_sub_listings_enable')): ?>
                            <?php if(!empty(wmvc_show_data('listing_parent_post_id', $db_data))):?>
                                <?php echo __('Child Listing of', 'wpdirectorykit'); ?>  <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&id='.wmvc_show_data('listing_parent_post_id', $db_data)));?>"><?php echo esc_html(wdk_field_value('post_title', wmvc_show_data('listing_parent_post_id', $db_data)));?></a>
                            <?php elseif(isset($_GET['parent_post_id']) && !empty($_GET['parent_post_id'])):?>
                                <?php echo __('Child Listing of', 'wpdirectorykit'); ?>  <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&id='.intval($_GET['parent_post_id'])));?>"><?php echo esc_html(wdk_field_value('post_title', intval($_GET['parent_post_id'])));?></a>
                            <?php endif;?>
                        <?php endif;?>
                    </h3>
                    <?php if(!empty(wmvc_show_data('ID', $db_data))):?>

                        <?php if($calendar_id):?>
                        <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk-bookings-calendar&function=edit&id=".esc_attr($calendar_id)); ?>" 
                                class="wdk-mr-5 button button-secondary alignright"
                        >
                            <span class="dashicons dashicons-calendar" style="margin-top: 4px;"></span> <?php echo __('Edit Calendar','wpdirectorykit')?>
                        </a>
                        <?php endif;?>

                        <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk-duplicate-listing&function=duplicate&listing_post_id=".wmvc_show_data('ID', $db_data)); ?>" 
                            <?php if ( !file_exists(ABSPATH . 'wp-content/plugins/wdk-duplicate-listing/wdk-duplicate-listing.php') ):?>
                                class="wdk-mr-5 button button-secondary alignright wdk-pro"
                                data-button-succuss = "<?php echo esc_attr__('Purchase Now', 'wpdirectorykit');?>" 
                                data-title = "<?php echo esc_attr__('Your version doesn\'t support this functionality, please upgrade', 'wpdirectorykit');?>" 
                                data-content = "<?php echo esc_attr__('We constantly maintain compatibility and improving this plugin for living, please support us and purchase, we provide very reasonable prices and will always do our best to help you!','wpdirectorykit');?>" 
                                data-action =  "https://www.wpdirectorykit.com/plugins/wp-directory-duplicate-listing.html" 
                            <?php elseif ( file_exists(ABSPATH . 'wp-content/plugins/wdk-duplicate-listing/wdk-duplicate-listing.php') && !function_exists('run_wdk_duplicate_listing')):?>
                                class="wdk-mr-5 button button-secondary alignright wdk-pro"
                                data-button-succuss = "<?php echo esc_attr__('Activate addon WDK Duplicate Listing', 'wpdirectorykit');?>" 
                                data-title = "<?php echo esc_attr__('Your version doesn\'t support this functionality, please upgrade', 'wpdirectorykit');?>" 
                                data-content = "<?php echo esc_attr__('We constantly maintain compatibility and improving this plugin for living, please support us and purchase, we provide very reasonable prices and will always do our best to help you!','wpdirectorykit');?>" 
                           
                                data-action =  "<?php echo esc_url(get_admin_url() . "plugins.php?plugin_status=all#activate-wdk-duplicate-listing"); ?>" 
                            <?php else:?>
                                class="wdk-mr-5 button button-secondary alignright"
                            <?php endif;?>
                        >
                            <span class="dashicons dashicons-admin-page" style="margin-top: 4px;"></span> <?php echo __('Duplicate Listing','wpdirectorykit')?>
                        </a>
                        <a href="<?php echo get_permalink(wmvc_show_data('ID', $db_data)); ?>" title="<?php echo esc_attr__('View','wpdirectorykit');?>" class="button button-secondary alignright" target="_blank" style="margin-right:15px;"><span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php echo __('View listing', 'wpdirectorykit'); ?></a>
                    <?php endif;?>
                </div>
                <div class="inside">
                    <?php

                        $success_message = NULL;
                        if(isset($_GET['custom_message']))
                            $success_message = esc_html(urldecode($_GET['custom_message']));

                        if(function_exists('run_wdk_bookings')) {
                            /* if booking addon exists, show custom message with link to edit calendar */
                            $success_message .= esc_html__('Successfully saved','wpdirectorykit').'. <a target="_blank" href="'. admin_url('admin.php?page=wdk-bookings-calendar&function=edit&id='.$calendar_id.'&post_id='.wmvc_show_data('ID', $db_data)).'">'.esc_html__('To define calendar availability dates please click here', 'wpdirectorykit').'</a>';
                        }
                        
                        $form->messages('class="alert alert-danger"', $success_message);
                    ?>
                    <div class="wdk-side-content">
                        <div class="wdk-col main">
                            <table class="form-table" role="presentation">
                                <tbody>
                                    <tr>
                                        <th scope="row"><label for="post_title"><?php echo __('Title', 'wpdirectorykit'); ?>*</label></th>
                                        <td><input name="post_title" type="text" id="post_title" value="<?php echo wmvc_show_data('post_title', $db_data, ''); ?>" placeholder="<?php echo esc_html__('Title', 'wpdirectorykit');?>" class="regular-text"></td>
                                    </tr>
                                    <?php if(get_option('wdk_is_address_enabled', FALSE)): ?>
                                    <tr>
                                        <th scope="row"><label for="input_address"><?php echo __('Address', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                        <input name="address" type="text" id="input_address" value="<?php echo wmvc_show_data('address', $db_data, ''); ?>" placeholder="<?php echo esc_html__('Address', 'wpdirectorykit');?>" class="regular-text">
                                        <p class="description" id="input_address-description"><?php echo __('After you enter address system will try to autodetect and pin location on map, then you can drag and drop pin on map to fine tune location','wpdirectorykit'); ?></p>
                                        </td>
                                    </tr>
                                    <?php endif; ?>

                                    <?php if(get_option('wdk_sub_listings_enable')): ?>

                                        <?php if(!empty(wmvc_show_data('listing_parent_post_id', $db_data)) || (isset($_GET['parent_post_id']) && !empty($_GET['parent_post_id']))):?>
                                            <tr class="<?php echo (defined( 'WP_DEBUG' ) && WP_DEBUG) ? '': 'hidden';?>">
                                                <th scope="row"><label for="listing_parent_post_id"><?php echo __('Parent ID', 'wpdirectorykit'); ?>*</label></th>
                                                <td>
                                                    <?php if(!empty(wmvc_show_data('listing_parent_post_id', $db_data))):?>
                                                        <input readonly name="listing_parent_post_id" type="text" id="listing_parent_post_id" value="<?php echo wmvc_show_data('listing_parent_post_id', $db_data, ''); ?>" placeholder="<?php echo esc_html__('listing_parent_post_id', 'wpdirectorykit');?>" class="regular-text">
                                                    <?php elseif(isset($_GET['parent_post_id']) && !empty($_GET['parent_post_id'])):?>
                                                        <input readonly name="listing_parent_post_id" type="text" id="listing_parent_post_id" value="<?php echo esc_attr(intval($_GET['parent_post_id'])); ?>" placeholder="<?php echo esc_html__('listing_parent_post_id', 'wpdirectorykit');?>" class="regular-text">
                                                    <?php endif;?>
                                                </td>
                                            </tr>
                                        <?php else:?>
                                            <?php if(!empty(wmvc_show_data('ID', $db_data)) && empty(wmvc_show_data('parent_post_id', $db_data))):?>
                                            <tr>
                                                <th scope="row"><label for="listing_related_ids"><?php echo __('Related', 'wpdirectorykit'); ?></label></th>
                                                <td>
                                                    <div class="wdk-listing-childs-wrap">
                                                        
                                                        <div class="wdk-listing-childs-drop">
                                                            <?php $related_ids = wmvc_show_data('listing_related_ids', $db_data, ''); ?>
                                                            <?php if(!empty(wmvc_show_data('listing_related_ids', $db_data, ''))):?>
                                                                <?php foreach (explode(',',wmvc_show_data('listing_related_ids', $db_data, '')) as $key => $child_idlisting):?>
                                                                    
                                                                        <?php 
                                                                            if(empty(wdk_field_value('post_id', $child_idlisting))) {
                                                                                $related_ids = trim(str_replace(','.$child_idlisting.'','', ','.$related_ids.','),','); 
                                                                                continue;
                                                                            }
                                                                        ?>

                                                                        <div class="drop-listing-item" data-idlisting="<?php echo esc_attr($child_idlisting);?>">
                                                                            <h3 class="handle"> 
                                                                                <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&id='.$child_idlisting));?>" class="title"><?php echo esc_html('#'.$child_idlisting.', '.wdk_field_value('post_title', $child_idlisting));?></a> 
                                                                                <a target="_blank" class="btn" href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&id='.$child_idlisting));?>" target="_blank" title="Edit"><span class="dashicons dashicons-edit"></span></a>
                                                                                <a class="question_sure btn remove" href="#" title="Remove"><span class="dashicons dashicons-no"></span></a>
                                                                            </h3>
                                                                        </div>
                                                                <?php endforeach;?>
                                                            <?php endif;?>
                                                        </div>
                                                        <input readonly name="listing_related_ids" type="hidden" id="listing_related_ids" value="<?php echo esc_attr($related_ids); ?>" class="regular-text">
                                                                                                            
                                                        <div class="wdk-field-edit">
                                                            <div class="wdk-field-container" style="padding: 0;">
                                                                <?php echo wdk_treefield_option('new_listing_id', 'listing_m', NULL, 'post_title', '', __('Not Selected', 'wpdirectorykit'),'',FALSE,'listing_parent_post_id IS NOT NULL');?>
                                                                <a class="button button-primary add_new_listing" style="margin-left: 5px" href="#"><?php echo __('Add', 'wpdirectorykit'); ?></a>
                                                            </div>
                                                        </div>
                                                        <a target="_blank" class="button button-secondary" href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&parent_post_id='.wmvc_show_data('ID', $db_data)));?>"><?php echo __('Add New Related Listing', 'wpdirectorykit'); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endif;?>

                                        <?php endif;?>

                                    <?php endif;?>

                                    <?php if(get_option('wdk_is_category_enabled', FALSE)): ?>
                                        <?php if(wdk_get_option('wdk_multi_categories_edit_field_type') == 'wdk_treefield_dropdown'):?>
                                            <tr>
                                                <th scope="row"><label for="category_id"><?php echo __('Category', 'wpdirectorykit'); ?><?php if(wdk_get_option('wdk_listing_category_required')):?>*<?php endif;?></label></th>
                                                <td class="wdk_multi_treefield_dropdown_container">
                                                    <?php
                                                    global $Winter_MVC_WDK;
                                                    $Winter_MVC_WDK->load_helper('listing');
                                                    $Winter_MVC_WDK->model('category_m');
                                                    $field_value =  wmvc_show_data('category_id', $db_data, '');
                                                    $field_key = 'category_id';
                                                    
                                                    $categories = array();
                                                    if(!empty($field_value)) {
                                                        $categories[] = $field_value;
                                                        $category = $Winter_MVC_WDK->category_m->get($field_value, TRUE); 

                                                        while(!empty($category->parent_id)) {
                                                            $category = $Winter_MVC_WDK->category_m->get($category->parent_id, TRUE); 
                                                            $categories[] = $category->idcategory;
                                                        }
                                                        krsort($categories);
                                                    } else {
                                                        $categories[] = 0;
                                                    }

                                                    wp_enqueue_style('wdk-treefield-dropdown');
                                                    wp_enqueue_script('wdk-treefield-dropdown');
                                                    wp_enqueue_style( 'dashicons' );


                                                    $level_max = $Winter_MVC_WDK->category_m->get_max_level();

                                                    $placeholder = [
                                                        0 => esc_html__('Select Categories','wpdirectorykit'),
                                                        1 => esc_html__('Select Sub Categories','wpdirectorykit'),
                                                        2 => esc_html__('Select Sub Categories','wpdirectorykit'),
                                                        3 => esc_html__('Select Sub Categories','wpdirectorykit'),
                                                        4 => esc_html__('Select Sub Categories','wpdirectorykit'),
                                                        5 => esc_html__('Select Sub Categories','wpdirectorykit'),
                                                    ];
                                                    ?>

                                                    <input name="<?php echo esc_attr($field_key); ?>" type="hidden" value="<?php echo esc_attr($field_value); ?>">
                                                    <?php
                                                    $level = 0;
                                                    $current = NULL;

                                                    foreach ($categories as $category) {
                                                        $current = $Winter_MVC_WDK->category_m->get($category, TRUE); 

                                                        $list = $Winter_MVC_WDK->category_m->get_by(array('parent_id = '.$current->parent_id => NULL)); 

                                                        if(isset($placeholder[$level])) {
                                                            $values_list = array(''=> $placeholder[$level]);
 
                                                          } else {
                                                            $values_list = array(''=> esc_html__('Select Sub Categories','wpdirectorykit'));
                                                        }

                                                        foreach ($list as $list_value) {
                                                            $values_list[$list_value->idcategory] = $list_value->category_title; 
                                                        }
                                                        ?>

                                                        <div data-level="<?php echo esc_attr($level);?>" data-field="<?php echo esc_attr($field_key); ?>" class="wdk_multi_treefield_dropdown wdk_treefield_dropdown">
                                                            <div class="wdk-field-group">
                                                                <?php echo wmvc_select_option('category_'.$level, $values_list, $category, 'class="wdk-control"');?>
                                                            </div>
                                                        </div>

                                                        <?php
                                                        $level++;
                                                    }
                                                            
                                                    if($level<$level_max ) {
                                                        for (; $level<$level_max;) {
                                                        
                                                            if(isset($placeholder[$level])) {
                                                                $values_list = array(''=> $placeholder[$level]);
                                                            } else {
                                                                $values_list = array(''=> esc_html__('Select Sub Categories','wpdirectorykit'));
                                                            }

                                                            if($category) {
                                                                $list = $Winter_MVC_WDK->category_m->get_by(array('parent_id = '.$category => NULL)); 
                                                                foreach ($list as $list_value) {
                                                                    $values_list[$list_value->idcategory] = $list_value->category_title; 
                                                                }
                                                                $category = NULL;
                                                            }

                                                            ?>
                                                            <div data-level="<?php echo esc_attr($level);?>" data-field="<?php echo esc_attr($field_key); ?>" class="wdk_multi_treefield_dropdown wdk_treefield_dropdown">
                                                                <div class="wdk-field-group">
                                                                    <?php echo wmvc_select_option('category_'.$level, $values_list, NULL, 'class="wdk-control"');?>
                                                                </div>
                                                            </div>

                                                            <?php
                                                            $level++;
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <th scope="row"><label for="category_id"><?php echo __('Category', 'wpdirectorykit'); ?><?php if(wdk_get_option('wdk_listing_category_required')):?>*<?php endif;?></label></th>
                                                <td >
                                                    <div class="wdk-field-edit edittable">
                                                        <div class="wdk-field-container">
                                                            <?php echo wdk_treefield_option ('category_id', 'category_m',  wmvc_show_data('category_id', $db_data, ''), 'category_title', '', __('Not Selected', 'wpdirectorykit'));?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if(get_option('wdk_is_category_enabled', FALSE) && get_option('wdk_multi_categories_other_enable', FALSE)): ?>
                                    <tr>
                                        <th scope="row"><label for="listing_categories"><?php echo __('More Categories', 'wpdirectorykit'); ?></label></th>
                                        <td class="">
                                            <?php echo wdk_treefield_select_ajax ('listing_sub_categories[]', 'category_m', wmvc_show_data('listing_sub_categories', $db_data, '', TRUE, TRUE), 'category_title', 'idcategory', '', __('All Categories', 'wpdirectorykit'), '', 'data-limit="10"');?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>

                                    <?php if(get_option('wdk_is_location_enabled', FALSE)): ?>
                                        <?php if(wdk_get_option('wdk_multi_categories_edit_field_type') == 'wdk_treefield_dropdown'):?>
                                            <tr>
                                                <th scope="row"><label for="location_id"><?php echo __('Location', 'wpdirectorykit'); ?><?php if(wdk_get_option('wdk_listing_category_required')):?>*<?php endif;?></label></th>
                                                <td class="wdk_multi_treefield_dropdown_container">
                                                    <?php
                                                    global $Winter_MVC_WDK;
                                                    $Winter_MVC_WDK->load_helper('listing');
                                                    $Winter_MVC_WDK->model('location_m');
                                                    $field_value =  wmvc_show_data('location_id', $db_data, '');
                                                    $field_key = 'location_id';

                                                    $locations = array();
                                                    if(!empty($field_value)) {
                                                        $locations[] = $field_value;
                                                        $location = $Winter_MVC_WDK->location_m->get($field_value, TRUE); 
                                                        
                                                        while(!empty($location->parent_id)) {
                                                            $location = $Winter_MVC_WDK->location_m->get($location->parent_id, TRUE); 
                                                            $locations[] = $location->idlocation;
                                                        }
                                                        krsort($locations);
                                                    } else {
                                                        $locations[] = 0;
                                                    }
                                            
                                                    wp_enqueue_style('wdk-treefield-dropdown');
                                                    wp_enqueue_script('wdk-treefield-dropdown');
                                                    wp_enqueue_style( 'dashicons' );
                                            
                                                    $level_max = $Winter_MVC_WDK->location_m->get_max_level();
                                                    
                                                    $placeholder = [
                                                        0 => esc_html__('Select Country','wpdirectorykit'),
                                                        1 => esc_html__('Select City','wpdirectorykit'),
                                                        2 => esc_html__('Select Neighborhood','wpdirectorykit'),
                                                        3 => esc_html__('Select Sub Area','wpdirectorykit'),
                                                        4 => esc_html__('Select Sub Area','wpdirectorykit'),
                                                        5 => esc_html__('Select Sub Area','wpdirectorykit'),
                                                    ];
                                                    ?>

                                                    <input name="<?php echo esc_attr($field_key); ?>" type="hidden" value="<?php echo esc_attr($field_value); ?>">
                                                    <?php
                                                        $level = 0;
                                                        $current = NULL;
                                                       
                                                        foreach ($locations as $location) {
                                                            $current = $Winter_MVC_WDK->location_m->get($location, TRUE); 

                                                            $list = $Winter_MVC_WDK->location_m->get_by(array('parent_id = '.$current->parent_id => NULL)); 

                                                            if(isset($placeholder[$level])) {
                                                                $values_list = array(''=> $placeholder[$level]);
                                                            } else {
                                                                $values_list = array(''=> esc_html__('Select Sub Area','wpdirectorykit'));
                                                            }

                                                            foreach ($list as $list_value) {
                                                                $values_list[$list_value->idlocation] = $list_value->location_title; 
                                                            }
                                                            ?>

                                                            <div data-level="<?php echo esc_attr($level);?>" data-field="<?php echo esc_attr($field_key); ?>" class="wdk_multi_treefield_dropdown wdk_treefield_dropdown">
                                                                <div class="wdk-field-group">
                                                                    <?php echo wmvc_select_option('location_'.$level, $values_list, $location, 'class="wdk-control"');?>
                                                                </div>
                                                            </div>

                                                            <?php
                                                            $level++;
                                                        }
                                                        
                                                        if($level<$level_max ) {
                                                            for (; $level<$level_max;) {
                                                            
                                                            if(isset($placeholder[$level])) {
                                                                $values_list = array(''=> $placeholder[$level]);
                                                            } else {
                                                                $values_list = array(''=> esc_html__('Select Sub Area','wpdirectorykit'));
                                                            }

                                                                
                                                            if(isset($placeholder[$level])) {
                                                                $values_list = array(''=> $placeholder[$level]);
                                                            } else {
                                                                $values_list = array(''=> esc_html__('Select Sub Area','wpdirectorykit'));
                                                            }
                                                                
                                                            if($location) {
                                                                $list = $Winter_MVC_WDK->location_m->get_by(array('parent_id = '.$location => NULL));
                                                                foreach ($list as $list_value) {
                                                                    $values_list[$list_value->idlocation] = $list_value->location_title; 
                                                                }
                                                                $location = NULL;
                                                            }
                                                    
                                                                ?>
                                                                <div data-level="<?php echo esc_attr($level);?>" data-field="<?php echo esc_attr($field_key); ?>" class="wdk_multi_treefield_dropdown wdk_treefield_dropdown">
                                                                    <div class="wdk-field-group">
                                                                        <?php echo wmvc_select_option('location_'.$level, $values_list, NULL, 'class="wdk-control"');?>
                                                                    </div>
                                                                </div>
                                                    
                                                                <?php
                                                                $level++;
                                                            }
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                                <tr>
                                                    <th scope="row"><label for="location_id"><?php echo __('Location', 'wpdirectorykit'); ?><?php if(wdk_get_option('wdk_listing_category_required')):?>*<?php endif;?></label></th>
                                                    <td >
                                                        <div class="wdk-field-edit edittable">
                                                            <div class="wdk-field-container">
                                                                <?php echo wdk_treefield_option ('location_id', 'location_m',  wmvc_show_data('location_id', $db_data, ''), 'location_title', '', __('Not Selected', 'wpdirectorykit'));?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if(get_option('wdk_is_location_enabled', FALSE) && get_option('wdk_multi_locations_other_enable', FALSE)): ?>
                                    <tr>
                                        <th scope="row"><label for="listing_agents"><?php echo __('More Locations', 'wpdirectorykit'); ?></label></th>
                                        <td class="">
                                            <?php echo wdk_treefield_select_ajax ('listing_sub_locations[]', 'location_m', wmvc_show_data('listing_sub_locations', $db_data, '', TRUE, TRUE), 'location_title', 'idlocation', '', __('All Locations', 'wpdirectorykit'), '', 'data-limit="10"');?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if(wdk_get_option('wdk_is_user_editor_enabled', FALSE)): ?>
                                    <tr>
                                        <th scope="row"><label for="user_id_editor"><?php echo __('Agent Editor', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                            <div class="wdk-field-edit edittable">
                                                <div class="wdk-field-container">
                                                    <?php echo wdk_treefield_option('user_id_editor', 'user_m', wmvc_show_data('user_id_editor', $db_data, ''), 'display_name', '', __('Not Selected', 'wpdirectorykit'));?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if(wdk_get_option('wdk_is_alt_agent_enabled', FALSE)): ?>
                                        <?php if(function_exists('run_wdk_membership')): ?>
                                        <tr>
                                            <th scope="row"><label for="listing_agents"><?php echo __('Alternative Agents', 'wpdirectorykit'); ?></label></th>
                                            <td class="">
                                                <?php echo wdk_user_select_ajax ('listing_agents[]', array_keys( wmvc_show_data('listing_agents', $db_data, '', TRUE, TRUE)), __('Add Agents', 'wpdirectorykit'));?>
                                            </td>
                                            <?php if(false):?>
                                            <td class="agents_group">
                                                <?php echo wdk_select_multi_option('listing_agents[]',  wmvc_show_data('listing_agents', $db_data, '', TRUE, TRUE), array_keys( wmvc_show_data('listing_agents', $db_data, '', TRUE, TRUE)), "id='listing_agents'");?>
                                                <div class="agent_add form-inline">
                                                    <div class="wdk-field-edit">
                                                        <div class="wdk-field-container">
                                                            <?php echo wdk_treefield_option('agent_id', 'user_m', '', 'display_name', '', __('Not Selected', 'wpdirectorykit'));?>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="button button-primary add_button"><?php echo __('Add agent', 'wpdirectorykit'); ?></button>
                                                    <button type="button" title="<?php echo __('Remove latest on list', 'wpdirectorykit'); ?>" class="button button-secondary rem_button"><?php echo __('X', 'wpdirectorykit'); ?></button>
                                                </div>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if(wdk_get_option('wdk_membership_is_enable_subscriptions') && function_exists('run_wdk_membership')):?>
                                    <tr>
                                        <th scope="row"><label for="subscription_id"><?php echo __('Membership Subscription', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                            <?php
                                            echo wmvc_select_option('subscription_id', $subscriptions, wmvc_show_data('subscription_id', $db_data, ''), "id='subscription_id'", __('Not Selected', 'wpdirectorykit'));
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endif;?>

                                    <?php if(function_exists('run_wdk_payments') && isset($packages)):?>
                                    <tr>
                                        <th scope="row"><label for="packages"><?php echo __('Package', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                            <?php
                                            echo wmvc_select_option('package_id', $packages, wmvc_show_data('package_id', $db_data, ''), "id='packages'", __('Not Selected', 'wpdirectorykit'));
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endif;?>

                                    <?php if(wdk_get_option('wdk_is_rank_enabled', FALSE)): ?>
                                    <tr>
                                        <th scope="row"><label for="rank"><?php echo __('Rank', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                            <input <?php if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')):?> readonly="readonly" <?php endif;?> name="rank" type="number" id="rank" value="<?php echo wmvc_show_data('rank', $db_data, ''); ?>" placeholder="<?php echo esc_html__('Rank', 'wpdirectorykit');?>" class="regular-text">
                                            <p class="description" id="input_rank-description"><b><?php echo __('Rank', 'wpdirectorykit'); ?></b> <?php echo esc_html__('is number, higher number means a better position/rank in results.','wpdirectorykit');?></p>
                                        </td>
                                    </tr>
                                    <?php endif;?>

                                    <?php if(wdk_get_option('wdk_is_featured_enabled', FALSE)): ?>
                                    <tr>
                                        <th scope="row"><label for="is_featured"><?php echo __('Is Featured', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                            <input name="is_featured" type="checkbox" id="is_featured" value="1" <?php echo !empty(wmvc_show_data('is_featured', $db_data, ''))?'checked':''; ?>><label for="is_featured"><?php echo __('Make it featured','wpdirectorykit'); ?></label>
                                            <p class="description" id="is_featured-description"><?php echo __('Featured/Highlighted listing in results','wpdirectorykit'); ?></p>
                                        </td>
                                    </tr>
                                    <?php endif;?>

                                    <tr>
                                        <th scope="row"><label for="is_activated"><?php echo __('Is Activated', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                            <input name="is_activated" type="checkbox" id="is_activated" value="1" <?php echo !empty(wmvc_show_data('is_activated', $db_data, ''))?'checked':''; ?>><label for="is_activated"><?php echo __('Make it available for public','wpdirectorykit'); ?></label>
                                            <p class="description" id="is_activated-description"><?php echo __('When listing is activated will be visible on frontend','wpdirectorykit'); ?></p>
                                        </td>
                                    </tr>
                                    <?php if(function_exists('run_wdk_membership')):?>
                                    <tr>
                                        <th scope="row"><label for="is_approved"><?php echo __('Is Approved', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                            <input name="is_approved" type="checkbox" id="is_approved" value="1" <?php echo !empty(wmvc_show_data('is_approved', $db_data, ''))?'checked':''; ?>><label for="is_approved"><?php echo __('Make it approved for public','wpdirectorykit'); ?></label>
                                            <p class="description" id="is_approved-description"><?php echo __('When listing is approved will be visible on frontend','wpdirectorykit'); ?> (<?php echo __('only admin can approve','wpdirectorykit'); ?>)</p>
                                        </td>
                                    </tr>
                                    <?php endif;?>
                                    <tr>
                                        <th scope="row"><label for="slug"><?php echo __('Slug', 'wpdirectorykit'); ?></label></th>
                                        <td>
                                            <input <?php if(!wmvc_user_in_role('administrator') && !current_user_can('wdk_listings_manage')):?> readonly="readonly" <?php endif;?> name="slug" type="text" id="slug" value="<?php echo wmvc_show_data('post_name', $db_data, ''); ?>" placeholder="<?php echo esc_html__('Slug', 'wpdirectorykit');?>" class="regular-text">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="wdk-col sidebar">
                            <?php if(get_option('wdk_is_address_enabled', FALSE)): ?>
                                <div class="clearfix">
                                    <a href="#" class="button button-secondary alignright wdk_action_clear_gps_reset_map"><?php echo esc_html__('Clear Gps coordinates', 'wpdirectorykit');?></a>
                                </div>
                                <br/>
                                <div id="map" class="listing_edit_map"></div>
                                <br/>
                                <p class="alert alert-info"><?php echo esc_html__('Drag and drop pin to desired location','wpdirectorykit');?></p>
                                <div class="wdk-field-edit inline">
                                    <label for="listing_gps"><?php echo esc_html__('GPS','wpdirectorykit');?>:</label>
                                    <div class="wdk-field-container">
                                        <input name="lat" readonly="readonly" type="text" id="input_lat" value="<?php echo wmvc_show_data('lat', $db_data, ''); ?>" class="regular-text" placeholder="<?php echo esc_html__('lat', 'wpdirectorykit');?>">
                                        <input name="lng" readonly="readonly" type="text" id="input_lng" value="<?php echo wmvc_show_data('lng', $db_data, ''); ?>" class="regular-text" placeholder="<?php echo esc_html__('lng', 'wpdirectorykit');?>">
                                    </div>
                                </div>
                            <?php endif;?>
                            <?php if(!empty($edit_log)):?>
                                    <div class="wdk_editlog">
                                    <table class="table table-bordered">
                                    <caption><?php echo esc_html__('Edit History', 'wpdirectorykitesc_html(');?></caption>
                                    <thead>
                                        <tr>
                                        <th><?php echo esc_html__('User', 'wpdirectorykitesc_html(');?></th>
                                        <th><?php echo esc_html__('Date', 'wpdirectorykitesc_html(');?></th>
                                        <th><?php echo esc_html__('IP', 'wpdirectorykitesc_html(');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php  foreach ($edit_log as $log_row):?>
                                        <tr>
                                            <td><?php echo esc_html($log_row->display_name);?></td>
                                            <td><?php echo esc_html(wdk_get_date($log_row->date));?></td>
                                            <td><?php echo esc_html($log_row->ip);?></td>
                                        </tr>
                                        <?php endforeach;?>

                                    </tbody>
                                    </table>
                                    </div>
                                    
                                <?php endif;?>

                        </div>
                    </div>
                    
                    <?php if(wdk_get_option('wdk_is_post_content_enable', FALSE)): ?>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="post_content"><?php echo __('Content', 'wpdirectorykit'); ?>*</label></th>
                                <td><?php wp_editor(wmvc_show_data('post_content', $db_data, ''), 'post_content', array('media_buttons' => FALSE)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php endif;?>
                </div>
            </div>
            <div class="postbox listing_custom_fields" style="display: block;">
                <div class="inside wdk-row">
                    <?php if(count($fields) == 0): ?>
                        <div class="wdk-col-12">
                            <div class="alert alert-success mb0"><p><?php echo __('Fields doesn\'t exists','wpdirectorykit'); ?> <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_fields"); ?>" class="button button-primary" id="add_field_button"><?php echo __('Manage Fields','wpdirectorykit'); ?></a></p></div>
                        </div>
                    <?php endif; ?>

                    <?php echo wdk_generate_fields($fields, $db_data); ?>                   
                </div>
            </div>
        <?php if(!wdk_get_option('wdk_listing_plangs_documents_disable')):?>
            <div class="postbox" style="display: block;">
                <div class="postbox-header">
                    <h3><?php echo __('Listing plans and documents', 'wpdirectorykit'); ?></h3>
                </div>
                <div class="inside">
                    <p class="alert alert-info"><?php echo __('Drag and drop image to change order', 'wpdirectorykit'); ?></p>
                    <?php  
                        echo wdk_upload_multi_files('listing_plans_documents', wmvc_show_data('listing_plans_documents', $db_data, '')); 
                    ?>               
                </div>
            </div>
        <?php endif;?>
        <?php if(!wdk_get_option('wdk_listing_images_disable')):?>
            <div class="postbox" style="display: block;">
                <div class="postbox-header">
                    <h3><?php echo __('Listing Images/Videos', 'wpdirectorykit'); ?></h3>
                </div>
                <div class="inside">
                    <p class="alert alert-info"><?php echo __('Drag and drop image to change order', 'wpdirectorykit'); ?></p>
                    <?php  
                        echo wmvc_upload_multiple('listing_images', wmvc_show_data('listing_images', $db_data, '')); 
                    ?>               
                </div>
            </div>
            <?php endif;?>
            <button type="submit" class="button button-primary wdk-submit-loading out"><?php echo esc_html__('Save Changes','wpdirectorykit'); ?></button>
        </form>
    </div>

    <?php do_action('wpdirectorykit/admin/listing/edit/after_form', $db_data);?>
</div>
<?php
    wp_enqueue_style('leaflet');
    wp_enqueue_script('leaflet');
            
    wp_enqueue_style('wdk-notify');
    wp_enqueue_script('wdk-notify');
            
    wp_enqueue_style('jquery-confirm');
    wp_enqueue_script('jquery-confirm');
?>

<?php
    wp_enqueue_script( 'jquery-ui-core', false, array('jquery') );
    wp_enqueue_script( 'jquery-ui-sortable', false, array('jquery') );
?>

<script>
    // Generate table
    jQuery(document).ready(function($) {

        <?php if(get_option('wdk_is_address_enabled', FALSE)): ?>
            var wdk_edit_map_marker,wdk_timerMap,wdk_edit_map;
            wdk_edit_map = L.map('map', {
                center: [<?php echo (wmvc_show_data('lat', $db_data) ?: get_option('wdk_default_lat', 51.505)); ?>, <?php echo (wmvc_show_data('lng', $db_data) ?: get_option('wdk_default_lng', -0.09)); ?>],
                zoom: 4,
            });   

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(wdk_edit_map);

            wdk_edit_map_marker = L.marker(
                [<?php echo (wmvc_show_data('lat', $db_data) ?: get_option('wdk_default_lat', 51.505)); ?>, <?php echo (wmvc_show_data('lng', $db_data) ?: get_option('wdk_default_lng', -0.09)); ?>],
                {draggable: true}
            ).addTo(wdk_edit_map);

            wdk_edit_map_marker.on('dragend', function(event){
                clearTimeout(wdk_timerMap);
                var marker = event.target;
                var {lat,lng} = marker.getLatLng();
                $('#input_lat').val(lat);
                $('#input_lng').val(lng);
                //retrieved the position
            });

            /* reset map and gps */
            $('.wdk_action_clear_gps_reset_map').on('click', function(e){
                e.preventDefault();
                $('#input_lat,#input_lng').val('');

                /* move marker on default poisition */
                wdk_edit_map_marker.setLatLng([<?php echo esc_js(get_option('wdk_default_lat', 51.505)); ?>,  <?php echo esc_js(get_option('wdk_default_lng', -0.09)); ?>]).update(); 
                wdk_edit_map.panTo(new L.LatLng(<?php echo esc_js(get_option('wdk_default_lat', 51.505)); ?>,  <?php echo esc_js(get_option('wdk_default_lng', -0.09)); ?>));
            });

            $('#input_address').on('change keyup', function (e) {
                clearTimeout(wdk_timerMap);
                wdk_timerMap = setTimeout(function () {
                    $.get('https://nominatim.openstreetmap.org/search?format=json&q='+$('#input_address').val(), function(data){
                        if(data.length && typeof data[0]) {
                            var {lat,lon} =data[0];
                            wdk_edit_map_marker.setLatLng([lat, lon]).update(); 
                            wdk_edit_map.panTo(new L.LatLng(lat, lon));
                            $('#input_lat').val(lat);
                            $('#input_lng').val(lon);
                        } else {
                            wdk_log_notify('<?php echo esc_js(__('Address not found', 'wpdirectorykit')); ?>', 'error');
                            return;
                        }
                    });
                }, 1000);
            });

            $('#input_gps').on('change keyup', function (e) {
                wdk_edit_map.panTo(new L.LatLng($('#input_lat').val(), $('#input_lng').val()));
                wdk_edit_map_marker.setLatLng([parseFloat($('#input_lat').val()), parseFloat($('#input_lng').val())]).update(); 
            })
        <?php endif;?>

        /* agents */
                        
        $('.agents_group .add_button').on( "click", function(e) {
            e.preventDefault();
            var group_agent = $(this).closest('.agents_group');
            var agent_id = group_agent.find('input[name="agent_id"]').val();
        
            if(agent_id != '')
            {
                var exists = 0 != group_agent.find('#listing_agents').find('option[value='+agent_id+']').length;
                var agent_name =  group_agent.find('.wdk_dropdown_tree .btn-group .btn:first').text();
                
                if(!exists)
                {
                    group_agent.find('#listing_agents').append('<option value="'+agent_id+'" selected>'+agent_name+'</option>');
                }
                else
                {
                    wdk_log_notify('<?php echo esc_js(__('Already on list', 'wpdirectorykit')); ?>', 'error');
                }
            }   
            else
            {
                wdk_log_notify('<?php echo esc_js(__('Not selected', 'wpdirectorykit')); ?>', 'error');
            }
        });

        $('.agents_group .rem_button').on( "click", function() {
            $(this).closest('.agents_group').find('#listing_agents option:selected:last').remove();
        });

                
        $('form.form_listing').on('submit', function(e){
            if($(this).find('#listing_agents').length) {
                $('#listing_agents').find('option').prop("selected", true).trigger('change')  
            } 
        });
        
        wdk_childs_listings_list();

        $('.select_ajax_user').on('select2:selecting', function (e) {
            // Get the selected option value
            var selectedValue = e.params.args.data.id;
            var selecteduserEditor = $('[name="user_id_editor"]').val();

            // Check if the selected option should be prevented from being added
            if (selectedValue == selecteduserEditor) {
            // Prevent the option from being added
                e.preventDefault();
                wdk_log_notify('<?php echo esc_js(esc_attr__('User Alread defiend like editor','wpdirectorykit'));?>', 'error');
            }
        });

    });


const wdk_childs_listings_list = ($selector= '.wdk-listing-childs-wrap', $field_name = 'listing_related_ids') => {
    var el_wrapper = jQuery($selector);
    var remove_child_listing,save_data;
     
    remove_child_listing = () => {
        el_wrapper.find( ".wdk-listing-childs-drop .drop-listing-item .remove" ).off().on('click', function(e){
            e.preventDefault();

            if(confirm("<?php echo esc_js(__('Are you sure?','wpdirectorykit')); ?>")) {
                jQuery(this).closest('.drop-listing-item').remove();
                save_data();
            }
        });
    };

    save_data = () => {
        var data_fields_sublist = '';
        el_wrapper.find('.wdk-listing-childs-drop .drop-listing-item').each(function( index ) {
            if(data_fields_sublist !='')
                data_fields_sublist +=',';

            data_fields_sublist += jQuery(this).attr('data-idlisting');
        });
        console.log(el_wrapper.find('input[name="'+$field_name+'"]'));
        el_wrapper.find('input[name="'+$field_name+'"]').val(data_fields_sublist);
    }

    remove_child_listing(); 

    el_wrapper.find( ".wdk-listing-childs-drop" ).sortable({
            connectWith: ".wdk-listing-childs-drop",
            placeholder: "ui-sortable-placeholder widget-placeholder",
            update: function(event, ui) {
                save_data();
            }
    }).disableSelection();

    el_wrapper.find('.add_new_listing').on('click', function(e){
        e.preventDefault();

        var listing_id = el_wrapper.find('input[name="new_listing_id"]').val();
        var listing_title = el_wrapper.find('.wdk_dropdown_tree button:first-child').text();

        if(listing_id == '') {
            wdk_log_notify('<?php echo esc_js(esc_attr__('Listing Not Defined','wpdirectorykit'));?>', 'error');
            return;
        }

        if(listing_id == '<?php echo esc_js(wmvc_show_data('ID', $db_data));?>') {
            wdk_log_notify('<?php echo esc_js(esc_attr__('Impossible add current listing','wpdirectorykit'));?>', 'error');
            return;
        }

        if( el_wrapper.find( ".wdk-listing-childs-drop .drop-listing-item[data-idlisting='"+listing_id+"']" ).length) {
            wdk_log_notify('<?php echo esc_js(esc_attr__('Listing already added','wpdirectorykit'));?>', 'error');
            return;
        }

        var item_html = '<div class="drop-listing-item" data-idlisting="'+listing_id+'">\n\
                            <h3 class="handle"> \n\
                                <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&id='));?>'+listing_id+'" class="title">'+listing_title+'</a>\n\
                                <a target="_blank" class="btn" href="<?php echo esc_url(admin_url('admin.php?page=wdk_listing&id='));?>'+listing_id+'" target="_blank" title="Edit"><span class="dashicons dashicons-edit"></span></a>\n\
                                <a class="question_sure btn remove" href="#" title="<?php echo esc_js(__('Remove','wpdirectorykit'));?>"><span class="dashicons dashicons-no"></span></a>\n\
                            </h3> \n\
                        </div>';

        el_wrapper.find( ".wdk-listing-childs-drop" ).append(item_html);
        remove_child_listing();
        save_data();
    });
};

</script>

<style>

.form-table .agent_add.form-inline .wdk-field-edit {
  display: inline-block;
}

.form-table .agent_add.form-inline .wdk-field-edit .wdk-field-container {
    padding: 0;
}

.form-table .agent_add.form-inline .wdk-field-edit,
.form-table .agent_add.form-inline .button  {
  margin: 15px 0px;
}

.form-table #listing_agents {
    max-width: 100%;
    width: 25em;
}

.form-table .agent_add.form-inline .wdk_dropdown_tree {
    width: 230px;
    max-width: 100%;
}
    
</style>

<?php $this->view('general/footer', $data); ?>