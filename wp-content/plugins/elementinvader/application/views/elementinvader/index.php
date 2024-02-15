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
            <a class="active" href="#"><?php echo __('From Theme','elementinvader'); ?></a>
            <a class="" href="<?php echo admin_url('admin.php?page=elementinvader_marketplace'); ?>"><?php echo __('Other Layouts','elementinvader'); ?></a>
        </nav>
    </div>

    <div class="panel-search">
        <div class="btn-group group-import">
            <a href="#template-import-popup" class="btn btn-import popup-with-form"><?php echo __('One Click and Import All Templates','elementinvader'); ?></a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading flex">
            <h3 class="panel-title"><?php echo count($templates).__(' Design Templates found','elementinvader'); ?></h3>
        </div>
        <div class="panel-body">
        <div class="container">

<?php if(count($templates) == 0): ?>
<div class="row">
<div class="col-md-12">
<div class="alert alert-info" role="alert">
<a href="<?php echo admin_url('admin.php?page=elementinvader_marketplace'); ?>"><?php echo __('Templates not found in your theme, check in Other Layouts tab or suggest one here and we will consider to build it','elementinvader'); ?></a>
</div>
</div>
</div>
<?php endif; ?>

            <div class="row">
<?php foreach($templates as $key=>$item):?>

                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                    <a href="#" class="img_link"><img src="<?php echo get_template_directory_uri().'/elementinvader/'.esc_attr($item).'/screenshot.jpg';?>" alt="<?php echo __('Screenshot','elementinvader'); ?>" /></a>
                    <div class="card-body">
                        <p class="card-name"><?php echo esc_html(elementinvader_template_data($item, 'kit-title')); ?><span><?php echo esc_html(elementinvader_template_data($item, 'date')); ?></span></p>
                        <p class="page-name"><?php echo esc_html(elementinvader_template_data($item, 'page-title')); ?></p>
                        <p class="card-tags"><?php echo esc_html(elementinvader_template_data($item, 'tags')); ?></p>
                        <div class="btn-group">
                            <a href="#add-page-popup" f_item="<?php echo esc_attr($item); ?>" class="btn btn-invader popup-with-form"><?php echo __('Add Page','elementinvader'); ?></a>
                        </div>
                    </div>
                    </div>
                </div>

                <?php endforeach; ?>

            </div>
            </div>
        </div>
    </div>
    
</div>


<?php

wp_enqueue_script( 'datatables' );

?>
<script>

jQuery(document).ready(function($) {

});

</script>

<style>



</style>

<script>

jQuery(document).ready(function($) {
    
    jQuery("a.img_link").hover(function(){
        if(jQuery(this).find('img').height() < jQuery(this).height() ) return false;
        var top_size = jQuery(this).find('img').height()-jQuery(this).height();

        jQuery(this).find('img').animate({
            top: -top_size,
        },  top_size*5,"linear", function() {
            // Animation complete.
        });
    },function(){
        jQuery(this).find('img').stop().css('top', '0px');
    });

    define_popup_trigers();

    $('#import-pages-button').click(function()
    {
        var data_form = $('#template-import-popup').serialize();
        var data_form_array = $('#template-import-popup').serializeArray();

        $('#ajax-indicator-masking').show();

        // Assign handlers immediately after making the request,
        // and remember the jqxhr object for this request
        var jqxhr = $.post( "<?php echo admin_url('admin-ajax.php?action=elementinvader_action&function=import_pages&page=elementinvader'); ?>", data_form, function(data) {
        
            if(data.status == 'success')
            {
                $('#template-import-popup .form-elements-container').hide();
                $('#template-import-popup').prepend('<a class="elementor_button btn btn-invader" href="'+data.page_url+'">'+data.message+'</a>');
            
                if(data.plugins_required)
                {
                    $.each( data.plugins_required, function( key, value ) {
                        $('#template-import-popup').prepend('<p class="alert alert-warning">'+value+'</p>');
                    });
                }
            }
            
        })
        .done(function(data) {
            //alert( "second success" );
        })
        .fail(function(data) {
            alert( "Error: " + data );
        })
        .always(function(data) {
            //alert( "finished" );
            $('#ajax-indicator-masking').hide();
        });
    });

    $('#add-page-button').click(function(){
        var _this = $(this);
        if(_this.hasClass('active')) {console.log('elementinvader: import already started');sw_log_notify('<?php echo __('Please wait', 'elementinvader'); ?>', 'error'); return false;}
        _this.addClass('active');
        var data_form = $('#add-page-popup').serialize();
        var data_form_array = $('#add-page-popup').serializeArray();

        if($('#inputPage').val() == '')
        {
            alert('<?php echo wmvc_js(__('Please enter Page Title', 'elementinvader')); ?>');
            return;
        }

        $('#ajax-indicator-masking').show();

        // Assign handlers immediately after making the request,
        // and remember the jqxhr object for this request
        var jqxhr = $.post( "<?php echo admin_url('admin-ajax.php?action=elementinvader_action&function=add_page&page=elementinvader'); ?>", data_form, function(data) {
            
            if(data.status == 'success')
            {
                $('#add-page-popup .form-elements-container').hide();
                $('#add-page-popup').prepend('<a class="elementor_button btn btn-invader" href="'+data.page_url+'&'+data_form+'">'+data.message+'</a>');

                if(data.plugins_required)
                {
                    $.each( data.plugins_required, function( key, value ) {
                        $('#add-page-popup').prepend('<p class="alert alert-info">'+value+'</p>');
                    });
                }

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

    function define_popup_trigers()
    {
        $('.popup-with-form').magnificPopup({ 
        	type: 'inline',
        	preloader: true,
        	focus: '#inputStyle',
                            
        	// When elemened is focused, some mobile browsers in some cases zoom in
        	// It looks not nice, so we disable it:
        	callbacks: {
        		beforeOpen: function() {
                    
        			if($(window).width() < 700) {
        				this.st.focus = false;
        			} else {
        				this.st.focus = '#inputPage';
        			}

                    $('#add-page-popup .form-elements-container').show();
                    $('#template-import-popup .form-elements-container').show();
                    $('.elementor_button').remove();
                    $('#add-page-popup p.alert').remove();
                    $('#inputPage').val(''); 
        		},
                
        		open: function() {

                    var magnificPopup = $.magnificPopup.instance,
                    cur = magnificPopup.st.el.parent();

                    $('#inputTemplate').val(cur.prevObject.attr('f_item'));                 
                    $('#inputPage').val(cur.parent().find('.page-name').html()); 
        		},
                close : function(){
                    $('body.elementinvader-page .white-popup-block .btn.btn-invader').removeClass('active');
                }
        	}
        });
    }

});

</script>

<?php $this->view('general/footer', $data); ?>

<!-- form itself -->
<form id="add-page-popup" class="form-horizontal mfp-hide white-popup-block wrap elementinvader_wrap">
    <div id="popup-form-validation">
    <p class="hidden alert alert-error"><?php echo __('Submit failed, please populate all fields!', 'elementinvader'); ?></p>
    </div>
    
    <div class="form-elements-container">
    <div class="control-group hidden">
        <label class="control-label" for="inputTemplate"><?php echo __('Template', 'elementinvader'); ?></label>
        <div class="controls">
            <input type="text" name="template" id="inputTemplate" value="" placeholder="<?php echo __('Template', 'elementinvader'); ?>" readonly>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPage"><?php echo __('Page Title', 'elementinvader'); ?></label>
        <div class="controls">
            <input type="text" name="page_title" id="inputPage" value="" placeholder="<?php echo __('Page Title', 'elementinvader'); ?>">
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button id="add-page-button" type="button" class="btn btn-invader"><?php echo __('Add this page', 'elementinvader'); ?> <img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;" /></button>
        </div>
    </div>
    </div>
</form>

<!-- form itself -->
<form id="template-import-popup" class="form-horizontal mfp-hide white-popup-block wrap elementinvader_wrap">
    <?php if(count($templates) == 0): ?>
    <div class="alert alert-info" role="alert">
        <a href="<?php echo admin_url('admin.php?page=elementinvader_marketplace'); ?>"><?php echo __('Templates not found in your theme, check in Other Layouts tab or suggest one here and we will consider to build it','elementinvader'); ?></a>
    </div>
    <?php else: ?>
    <p class=""><?php echo __('This pages will be Created, or updated if already exists in Element Invader Menu:', 'elementinvader'); ?></p>
    
    <?php foreach($templates as $key=>$item):?>
    <p class="alert alert-info"><?php echo esc_html(elementinvader_template_data($item, 'page-title')); ?></p>
    <?php endforeach; ?>

    <div class="form-elements-container">
    <div class="">
        <div class="controls">
            <button id="import-pages-button" type="button" class="btn btn-invader"><?php echo __('Import All Pages', 'elementinvader'); ?> <img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;" /></button>
        </div>
    </div>
    </div>
    <?php endif; ?>
</form> 









