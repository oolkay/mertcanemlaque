<?php

namespace Wdk\Extensions;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class WdkDependfields
{
    /**
     * data array
     *
     * @var array
     */
    public $data = array();
    
    public function __construct($data = array(), $args = null)
    {

        /* admin actions */
        add_action( 'wpdirectorykit/admin/category/edit/after_form', array($this, 'dependfields'));

        /* admin actions */
        add_action( 'wpdirectorykit/admin/listing/edit/after_form', array($this, 'listing_edit'));
        add_action( 'wdk-membership/view/listing_edit/after_form', array($this, 'listing_edit'));

        add_action( 'wp_enqueue_scripts', array($this, 'front_search'), 100);
    }

    public function listing_edit($data = NULL)
    {
       
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('dependfields_m');
        
        $hidden_fields = array();

        $depend_fields = $Winter_MVC_WDK->dependfields_m->get_by(array('main_field' => 'categories'));
        if($depend_fields) {
            foreach ($depend_fields as $depend_field) {
                $hidden_fields[$depend_field->field_id] = ','.$depend_field->hidden_fields_list.',';
            }
        }

        $params = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'hidden_fields'=> $hidden_fields
        );
        wp_enqueue_script( 'wdk-dependfields-submitform');
        wp_localize_script( 'wdk-dependfields-submitform', 'script_dependfields_submitform', $params);
    }

    public function front_search($data = NULL)
    {
       
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('dependfields_m');
        
        $hidden_fields = array();

        $depend_fields = $Winter_MVC_WDK->dependfields_m->get_by(array('main_field' => 'categories'));
        if($depend_fields) {
            foreach ($depend_fields as $depend_field) {
                $hidden_fields[$depend_field->field_id] = ','.$depend_field->hidden_fields_list.',';
            }
        }

        $params = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'hidden_fields'=> $hidden_fields
        );
        wp_enqueue_script( 'wdk-dependfields-search');
        wp_localize_script( 'wdk-dependfields-search', 'script_dependfields_search', $params);
    }

    public function dependfields($data = NULL)
    {
        if (empty($data))
            return false;

        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('field_m');
        $Winter_MVC_WDK->model('dependfields_m');
        $this->data['fields_data'] = $Winter_MVC_WDK->field_m->get();
        $this->data['hidden_fields'] = array();

        wp_enqueue_script( 'wdk-dependfields-edit');

        $depend_fields = $Winter_MVC_WDK->dependfields_m->get_by(array('field_id' => wmvc_show_data('idcategory', $data),'main_field' => 'categories'), TRUE);
        if($depend_fields && !empty($depend_fields->hidden_fields_list)) {
            $this->data['hidden_fields'] = explode(',', $depend_fields->hidden_fields_list);
        }
        ?>

            <div class="wdk-body">
                <div class="postbox" style="display: block;">
                    <div class="postbox-header flex-align-left">
                        <h3 class="wdk-mr-5">
                            <?php echo __('Visible fields','wpdirectorykit'); ?>
                        </h3>
                        <a href="#" class="wdk-mr-5 button button-secondary wdk_copy_on_subcategories" data-category="<?php echo esc_attr(wmvc_show_data('idcategory', $data));?>" data-wpnonce="<?php echo wp_create_nonce('wdk_depend_copy_on_subcategories');?>">
                            <span class="dashicons dashicons-admin-page" style="margin-top: 4px;"></span>  <?php echo __('Copy on subcategories','wpdirectorykit'); ?>                    
                        </a>
                    </div>
                    <div class="inside">
                        <form method="post" action="#" novalidate="novalidate" class="wdk-depend-fields" enctype="multipart/form-data">
                        <input name="_wpnonce" type="hidden" value="<?php echo esc_attr(wp_create_nonce( 'wdk-update_depend'));?>" class="regular-text">
                        <input name="main_field" type="hidden" value="categories" class="regular-text">
                        <input name="field_id" type="hidden" value="<?php echo esc_attr(wmvc_show_data('idcategory', $data));?>" class="regular-text">
                            
                        <div class="wdk-row">
                            <?php echo esc_html(wmvc_show_data('field_label', $field));?>:
                            <hr/>
                        </div>
                        
                            <div class="wdk-row">
                                <?php foreach ($this->data['fields_data'] as $field) :;?>
                                    <?php if($field->field_type == "SECTION"):?>
                                        <div class="wdk-col-md-12 wdk-col-section">
                                            <div class="wdk_field section">
                                                <?php echo esc_html(wmvc_show_data('field_label', $field));?>:
                                                <label for="">
                                                    <input name="field_all_depend_<?php echo esc_attr(wmvc_show_data('idfield', $field));?>" type="checkbox" id="field_all_depend_<?php echo esc_attr(wmvc_show_data('idfield', $field));?>" value="1" class="regular-text trigger_section">
                                                </label>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="wdk-col-md-<?php echo esc_attr($field->columns_number); ?> ">
                                            <div class="wdk_field">
                                                <label for="field_depend_<?php echo esc_attr(wmvc_show_data('idfield', $field));?>"><?php echo esc_html(wmvc_show_data('field_label', $field));?></label>
                                                <input <?php if(empty($this->data['hidden_fields']) || !in_array(wmvc_show_data('idfield', $field), $this->data['hidden_fields'])):?> checked="checked"<?php endif;?> name="field_hide_<?php echo esc_attr(wmvc_show_data('idfield', $field));?>" type="checkbox" id="field_depend_<?php echo esc_attr(wmvc_show_data('idfield', $field));?>" value="1" class="regular-text">
                                            </div>
                                        </div>
                                    <?php endif;?>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php
    }

}
