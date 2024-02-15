<?php
/**
 * The template for Search field CATEGORY.
 *
 * This is the template that field layout for search form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php
$field_key = 'search_category';
$field_attr_id = 'wdk_search_'.wmvc_show_data('idfield', $field_data);
$placeholder = wmvc_show_data('field_label', $field_data);
$field_value = '';
$placeholder = esc_html__($placeholder,'wpdirectorykit');

$filter_ids = array();
if(isset($predefinedfields_query) && !empty($predefinedfields_query[$field_key])) {
    $field_value = intval($predefinedfields_query[$field_key]);
}

if(isset($_GET['category_root'])) {
    $predefinedfields_query['custom_category_root'] = sanitize_text_field($_GET['category_root']);
}

if(isset($predefinedfields_query) && !empty($predefinedfields_query['custom_category_root'])) {
    $custom_field_value = intval($predefinedfields_query['custom_category_root']);
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->load_helper('listing');
    $Winter_MVC_WDK->model('category_m');
    $category = $Winter_MVC_WDK->category_m->get($custom_field_value, TRUE); 
    /* if root search childs */
    if(isset($_GET['category_root']) || empty(wmvc_show_data('parent_id', $category, false, TRUE, TRUE))) {
        $filter_ids = wdk_category_get_all_childs($custom_field_value); 
        if(!empty($filter_ids)) {
            if(in_array($field_value, $filter_ids) === FALSE) {
                $field_value = $custom_field_value;
            }
        } else {
            $filter_ids[] = $custom_field_value;
        }
    }
}    

if(isset($_GET[$field_key])) {
    $field_value = ($_GET[$field_key]);
}

$hide_fields = '';
if(wmvc_show_data('search_type_tree_hide', $field_data)) {
    $hide_fields = wmvc_show_data('search_type_tree_hide', $field_data);
}
wdk_search_fields_toggle();
?>
 <?php if(isset($custom_field_value)):?>
    <input type="hidden" name="search_category[]" value="<?php echo esc_attr($custom_field_value);?>">
<?php endif;?>
<?php if(wdk_get_option('wdk_multi_categories_search_field_type') == 'wdk_treefield_dropdown'):?>
    <?php
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->load_helper('listing');
        $Winter_MVC_WDK->model('category_m');

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

            <div data-level="<?php echo esc_attr($level);?>" data-field="<?php echo esc_attr($field_key); ?>" class="wdk-field wdk-col wdk_treefield_dropdown <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?> 
                wdk_field_id_category">
                <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
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
    
                ?>
    
                <div data-level="<?php echo esc_attr($level);?>" data-field="<?php echo esc_attr($field_key); ?>" class="wdk-field wdk-col wdk_treefield_dropdown <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?>">
                    <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
                    <div class="wdk-field-group">
                        <?php echo wmvc_select_option('category_'.$level, $values_list, NULL, 'class="wdk-control"');?>
                    </div>
                </div>
    
                <?php
                $level++;
            }
        }
    ?>
<?php else:?>
    <div class="wdk-field wdk-col <?php echo esc_attr(wmvc_show_data('field_type', $field_data)); ?> <?php echo esc_attr(wmvc_show_data('class', $field_data)); ?>">
        <label class="wdk-field-label"><?php echo esc_html(wmvc_show_data('field_label', $field_data)); ?></label>
        <div class="wdk-field-group">
            <?php if(wdk_get_option('wdk_multi_categories_search_field_type') == 'select2'):?>
                <?php echo wdk_treefield_select_ajax ($field_key.'[]', 'category_m', $field_value, 'category_title','idcategory', '', __('All Categories', 'wpdirectorykit'), $filter_ids);?>
            <?php elseif(wdk_get_option('wdk_multi_categories_search_field_type') == 'wdk_treefield_checkboxes'):?>
                <?php
                    wp_enqueue_style( 'wdk-treefield-checkboxes');
                    wp_enqueue_script( 'wdk-treefield-checkboxes');
                ?>
                <?php echo wdk_treefield_option_checkboxes  ('search_category', 'category_m', $field_value, 'category_title', '', __('All Categories', 'wpdirectorykit'), $filter_ids);?>
            <?php else:?>
                <?php echo wdk_treefield_option ('search_category', 'category_m', $field_value, 'category_title', '', __('All Categories', 'wpdirectorykit'), $filter_ids, FALSE, '', $hide_fields);?>
            <?php endif;?>
        </div>
    </div>
<?php endif;?>