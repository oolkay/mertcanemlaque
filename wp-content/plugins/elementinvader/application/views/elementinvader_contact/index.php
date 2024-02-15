<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://listing-themes.com/
 * @since      1.0.0
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap elementinvader_wrap bg-white">

    <div class="menu-top">
        <div class="logo-box">
            <a href="https://elementinvader.com" target="_blank">
            <span>E</span><span>L</span><span>i</span>
            </a>
        </div>
        <nav class="">
            <a class="" href="<?php echo admin_url('admin.php?page=elementinvader'); ?>"><?php echo __('From Theme','elementinvader'); ?></a>
            <a class="" href="<?php echo admin_url('admin.php?page=elementinvader_marketplace'); ?>"><?php echo __('Other Layouts','elementinvader'); ?></a>
       
        </nav>
    </div>

    <div class="panel-search">
        <div class="btn-group group-import">
            <a target="_blank" href="https://www.facebook.com/groups/2631390953808199/" class="btn btn-import popup-with-form"><?php echo __('Join Our Community on Facebook','elementinvader'); ?></a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading flex">
            <h3 class="panel-title"><?php echo __('Contact Form','elementinvader'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="">

            <a target="_blank" href="https://www.facebook.com/groups/2631390953808199/" class="alert alert-warning"><?php echo __('If you have General question please join our Facebook Community','elementinvader'); ?></a>

            <div class="validation-messages">
            </div>

            <form id="contactForm" class="form-layout">
                <div class="form-group">
                    <label for="contactForm_name"><?php echo __('Full Name','elementinvader'); ?>*</label>
                    <input required="required" name="name" type="name" class="form-control" id="contactForm_name" placeholder="<?php echo __('Name Surname','elementinvader'); ?>">
                </div>
                <div class="form-group">
                    <label for="contactForm_email"><?php echo __('Your Email','elementinvader'); ?>*</label>
                    <input required="required" name="email" type="email" class="form-control" id="contactForm_email" value="<?php echo get_bloginfo( 'admin_email' ); ?>" placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label for="contactForm_category"><?php echo __('Question Category','elementinvader'); ?></label>
                    <select name="category" class="form-control" id="contactForm_category">
                    <option value="<?php echo __('Not Selected','elementinvader'); ?>"><?php echo __('Not Selected','elementinvader'); ?></option>
                    <option value="<?php echo __('Suggestion','elementinvader'); ?>"><?php echo __('Suggestion','elementinvader'); ?></option>
                    <option value="<?php echo __('Issue','elementinvader'); ?>"><?php echo __('Issue','elementinvader'); ?></option>
                    <option value="<?php echo __('Custom Work','elementinvader'); ?>"><?php echo __('Custom Work','elementinvader'); ?></option>
                    <option value="<?php echo __('Become Author','elementinvader'); ?>"><?php echo __('Become Author','elementinvader'); ?></option>
                    <option value="<?php echo __('Other','elementinvader'); ?>"><?php echo __('Other','elementinvader'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contactForm_rate"><?php echo __('Satisfication with plugin','elementinvader'); ?></label>
                    <select name="rate" class="form-control" id="contactForm_rate">
                    <option value="0"><?php echo __('Not Selected','elementinvader'); ?></option>
                    <option value="1"><?php echo __('1 (Not satisfied)','elementinvader'); ?></option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5"><?php echo __('5 (Very satisfied)','elementinvader'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contactForm_subject"><?php echo __('Subject','elementinvader'); ?></label>
                    <input name="subject" type="text" class="form-control" id="contactForm_subject" value="<?php if(isset($_GET['subject'])):?><?php echo esc_html($_GET['subject']);?><?php endif;?>" placeholder="Subject">
                </div>
                <div class="form-group">
                    <label for="contactForm_template_related"><?php echo __('Template related','elementinvader'); ?>*</label>
                    <input required="required" name="template_related" type="text" class="form-control" id="contactForm_template_related" value="<?php if(isset($_GET['page_title'])):?><?php echo esc_html($_GET['page_title']);?><?php endif;?> <?php if(isset($_GET['template_id'])):?>#<?php echo esc_html($_GET['template_id']);?><?php endif;?>" placeholder="<?php echo __('Link, id, title','elementinvader'); ?>">
                </div>
                <div class="form-group hidden">
                    <label for="contactwebsite_link"><?php echo __('Link Website','elementinvader'); ?></label>
                    <input name="website_link" type="text" class="form-control" id="contactwebsite_link" value="<?php echo home_url(); ?>" placeholder="website_link">
                </div>
                <div class="form-group hidden">
                    <label for="contactForm_version"><?php echo __('PHP version','elementinvader'); ?></label>
                    <input name="php_version" type="text" class="form-control" id="contactForm_version" value="<?php echo phpversion(); ?>" placeholder="php_version">
                </div>
                <div class="form-group hidden">
                    <label for="contactForm_wp_version"><?php echo __('WP version','elementinvader'); ?></label>
                    <input name="wp_version" type="text" class="form-control" id="contactForm_wp_version" value="<?php echo get_bloginfo( 'version' ); ?>" placeholder="wp_version">
                </div>
                <div class="form-group">
                    <label for="contactForm_message"><?php echo __('Message','elementinvader'); ?></label>
                    <textarea name="message" class="form-control" id="contactForm_message" rows="10"><?php if(isset($_GET['message'])):?><?php echo esc_html($_GET['message']);?><?php endif;?></textarea>
                </div>
                <button type="submit" id="contactForm_submit" class="btn btn-primary"><?php echo __('Send','elementinvader'); ?> <img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;" /></button>
            </form>

            </div>
        </div>
    </div>
    
</div>


<?php

?>

<script>

jQuery(document).ready(function($) {

    $('#contactForm').on('submit', function()
    {
        var data_form = $('#contactForm').serialize();

        $('#ajax-indicator-masking').show();

        // Assign handlers immediately after making the request,
        // and remember the jqxhr object for this request
        var jqxhr = $.post( "https://elementinvader.com/support/createTicket", data_form, function(data) {
        
            $('.validation-messages').html('');

            if(data.alert == 'danger')
            {
                $.each( data.errors, function( key, value ) {
                    $('.validation-messages').append( "<p class=\"alert alert-"+data.alert+"\">"+value+"</p>" );
                });
            }
            else if(data.alert == 'success')
            {
                $('.validation-messages').append( "<p class=\"alert alert-"+data.alert+"\">"+data.message+"</p>" );
                $('#contactForm')[0].reset();
            }
            else
            {
                $('.validation-messages').append( "<p class=\"alert alert-"+data.alert+"\">"+data.message+"</p>" );
            }
            
        })
        .done(function(data) {
        })
        .fail(function(data) {
            alert( "Error: " + data );
        })
        .always(function(data) {
            $('#ajax-indicator-masking').hide();
        });

        return false;
    });

});

</script>

<?php $this->view('general/footer', $data); ?>










