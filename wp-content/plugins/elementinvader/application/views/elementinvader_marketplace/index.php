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
            <!-- <a class="" href="#"><?php echo __('Premium','elementinvader'); ?></a> -->
            <a class="active" href="<?php echo admin_url('admin.php?page=elementinvader_marketplace'); ?>"><?php echo __('Other Layouts','elementinvader'); ?></a>
        </nav>
    </div>

    <div class="panel-search">
        <form id="search_form">
        <div class="btn-group group-import ">
            
            <div class="input-group">
                <span class="dashicons dashicons-search"></span>
                <input type="text" id="input-find" name="find" class="form-control find" placeholder="<?php echo __('What you need?','elementinvader'); ?>" />
                <a href="#" class="reset-icon"><span class="dashicons dashicons-no"></span></a>
                <button id="button-find" tyle="submit" class="btn btn-find"><?php echo __('Find Design','elementinvader'); ?> <img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;" /></button>
            </div>            
        </div>

        <div class="btn-group">
            
            <div class="input-group second-row">

                <div class="sort-element">
                    <span>Sort By:</span>
                    
                    <div class="select-choze">
                        <select name="order_by">
                            <option value="created_at DESC" >Newer First</option>
                            <option value="created_at ASC" >Older First</option>
                            <option value="count_download DESC" >Downloads</option>
                            <option value="count_save DESC" >Archived/Saved</option>
                            <option value="count_like DESC" >Liked</option>
                        </select>
                    </div>
                 </div>


                 <div class="toggle-pr">
                     <span>Free Only</span>
                     <label class="switch">
                      <input type="checkbox" name="free_only" value="1"  />
                      <span class="slider"></span>
                    </label>
                 </div><!--toggle-pr end-->

                 <div class="toggle-pr">
                     <span><span class="dashicons dashicons-heart"></span> My Favorite</span>
                     <label class="switch">
                      <input type="checkbox" name="my_favourite" value="1"  />
                      <span class="slider"></span>
                    </label>
                 </div><!--toggle-pr end-->

                 <div class="toggle-pr">
                     <span><span class="dashicons dashicons-thumbs-up"></span> My Likes</span>
                     <label class="switch">
                      <input type="checkbox" name="my_likes" value="1"  />
                      <span class="slider"></span>
                    </label>
                 </div><!--toggle-pr end-->

                 <div class="toggle-pr">
                     <span><span class="dashicons dashicons-download"></span> My Pages</span>
                     <label class="switch">
                      <input type="checkbox" name="my_pages" value="1"  />
                      <span class="slider"></span>
                    </label>
                 </div><!--toggle-pr end-->
                
                 <div class="clear-form">
                 <a href="#" title="" id="clear-all" class="clear-all">Clear All</a>
                 </div>

            </div>
            
        </div>

        </form>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading flex">
            <h3 class="panel-title" id="panel_title"><?php //echo __(' Design Templates found','elementinvader'); ?></h3>
        </div>
        <div class="panel-body">
        <div class="container" id="results_container">




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

#login-popup input{
    width: 100%;
}


</style>

<script>

jQuery(document).ready(function($) {

    jQuery('.panel-search .reset-icon').click(function()
    {
        jQuery('#input-find').val('').focus();

    });

    $('#clear-all').on('click', function() 
    {
        $('#search_form')[0].reset();

        $(':input:not([type="checkbox"])','#search_form')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .prop('checked', false)
            .prop('selected', false);

        $('#search_form option[value="created_at DESC"]').prop('selected', true);

        return false;
    });

    jQuery('#button-find').click(function()
    {
        elementinvader_search_tempaltes();
        return false;
    });

    const elementinvader_search_tempaltes = (page = 1) => {
        var data_form = $('#search_form').serialize()+'&api_token=<?php echo get_option('elementinvader_api_token', ''); ?>&page='+page;
        var that = jQuery('#button-find');
        var ajax_indicator =  $('#search_form').find('#ajax-indicator-masking');
        
        // Assign handlers immediately after making the request,
        // and remember the jqxhr object for this request
        ajax_indicator.show();
        var jqxhr = $.post( "<?php echo ELEMENTINVADER_WEBSITE; ?>marketplace/find", data_form, function(data) {
            
            jQuery('#results_container').html('');

            jQuery('#panel_title').html(data.total+' Design Templates found');

            if(data.login_message)
            {
                jQuery('#results_container').append(
                '<div class="row">'+
                '    <div class="col-md-12">'+
                '        <div class="alert alert-info" role="alert">'+
                '        <a href="#" class="open_login_form">'+data.login_message+'</a>'+
                '        </div>'+
                '    </div>'+
                '</div>'
                );
            }

            if(data.results.length == 0)
            {
                jQuery('#results_container').append(
                '<div class="row">'+
                '    <div class="col-md-12">'+
                '        <div class="alert alert-info" role="alert">'+
                '        <a href="<?php echo admin_url('admin.php?page=elementinvader_contact'); ?>"><?php echo __('Templates not found, please suggest one here and we will consider to build it','elementinvader'); ?></a>'+
                '        </div>'+
                '    </div>'+
                '</div>'
                );
            }

            jQuery('#results_container').append('<div class="row"></div>');
            
            jQuery.each( data.results, function( i, obj )
            {
                // remove time from datetime format
                var date_formated = obj.created_at.substr(0, obj.created_at.indexOf(' '));

                var like_is_enabled = (obj.like_is_enabled=='1'?'liked':'');
                var download_is_enabled = (obj.download_is_enabled=='1'?'liked':'');
                var favourite_is_enabled = (obj.favourite_is_enabled=='1'?'liked':'');

                jQuery('#results_container').find('div.row:first-child').append(
                ''+
                '   <div class="col-md-4">'+
                '       <div class="card mb-4 shadow-sm">'+
                        ((+obj.price >= 1) ? '<div class="badge premium"><span class="badge-content"><?php echo __('Premium','elementinvader'); ?></span></div>':'')+
                '           <a href="#" class="img_link"><img src="<?php echo ELEMENTINVADER_WEBSITE; ?>uploads/'+obj.id+'_screenshot.jpg" alt="<?php echo __('Screenshot','elementinvader'); ?>" /></a>'+
                '           <div class="card-body">'+
                '               <p class="card-name">'+obj['kit-title']+'<span>'+date_formated+'</span></p>'+
                '               <div class="row list-icons">'+
                '                   <div class="col-md-4 '+download_is_enabled+'"><span class="kit-download" kit_id="'+obj.id+'"><span class="dashicons dashicons-download"></span> <span class="num">'+obj.count_download+'</span></span></div>'+
                '                   <div class="col-md-4 '+like_is_enabled+'"><a href="#" class="kit-like" kit_id="'+obj.id+'"><span class="dashicons dashicons-thumbs-up"></span> <span class="num">'+obj.count_like+'</span></a></div>'+
                '                   <div class="col-md-4 '+favourite_is_enabled+'"><a href="#" class="kit-saved" kit_id="'+obj.id+'"><span class="dashicons dashicons-heart"></span> <span class="num">'+obj.count_save+'</span></a></div>'+
                '               </div>'+
                '               <p class="page-name">'+obj['page-title']+'</p>'+
                '               <p class="card-tags">'+obj.tags+'</p>'+
                '               <div class="btn-group">'+
                '                   <a href="#add-page-popup" f_item="download_kit_'+obj.id+'" f_price="'+obj.price+'" class="btn btn-invader popup-with-form"><?php echo __('Add Page','elementinvader'); ?> ('+obj.price+' $)</a>'+
                '               </div>'+
                '           </div>'+
                '       </div>'+
                '   </div>'+
                '');
            });

            if(typeof data.pagination !='undefined')
                jQuery('#results_container').append('<div class="pagination">' + data.pagination + '</div>');

            define_animate();

            define_popup_trigers();

            define_results_triggers();

            $('.pagination a').click(function (e) { 
                e.preventDefault();

                var href = $(this).attr('href').replace(/#results/, '');
                var n = href.lastIndexOf("page="); 
                var page_num = href.substr(n+5)
                elementinvader_search_tempaltes(page_num);
                jQuery('html, body').animate({
                        scrollTop: jQuery('#search_form').first().offset().top-150
                    },
                150);

                return false;
            });
            
            //ShowStatus.show('<?php echo wmvc_js(__('We still working on this feature', 'elementinvader')); ?>, tag:'+jQuery('#input-find').val());
        })
        .done(function(data) {
            //alert( "second success" );
        })
        .fail(function(data) {
            alert( "Error: " + data );
        })
        .always(function(data) {
            //alert( "finished" );
            ajax_indicator.hide();
            jQuery(".elementinvader_wrap a.img_link").each(function() {
                var self = this;
                jQuery(this).find('img').one("load", function() {
                    if(jQuery(self).find('img').height() < jQuery(self).height() ) jQuery(self).addClass('img-vcenter');
                })
            });
            jQuery(".elementinvader_wrap .card a.img_link").on('click', function (e) {
                e.preventDefault();
                return false;
            });
        });
    }

    $('#add-page-button').click(function(){
        var _this = $(this);
        if(_this.hasClass('active')) {console.log('elementinvader: import already started');sw_log_notify('<?php echo __('Please wait', 'elementinvader'); ?>', 'error'); return false;}
      
        var data_form = $('#add-page-popup').serialize();
        var data_form_array = $('#add-page-popup').serializeArray();
        var ajax_indicator = _this.find('#ajax-indicator-masking');
        if($('#inputPage').val() == '')
        {
            alert('<?php echo wmvc_js(__('Please enter Page Title', 'elementinvader')); ?>');
            return;
        }

        if($('#inputPage').val() == '')
        {
            alert('<?php echo wmvc_js(__('Please enter Page Title', 'elementinvader')); ?>');
            return;
        }

        if(!$('.license-group').hasClass('hidden'))
        {
            if($('#inputLicense').val() == '')
            {
                alert('<?php echo wmvc_js(__('License key is required for this template, you can purchase one on link below', 'elementinvader')); ?>');
                return;
            }
        }
        _this.addClass('active');
        ajax_indicator.show();

        // Assign handlers immediately after making the request,
        // and remember the jqxhr object for this request
        var jqxhr = $.post( "<?php echo admin_url('admin-ajax.php?action=elementinvader_action&function=add_page&page=elementinvader'); ?>", data_form, function(data) {
            
            if(data.login_message)
            {
                var el = $('#login-popup');
                if (el.length) {
                    $.magnificPopup.open({
                        items: {
                            src: el
                        },
                        type: 'inline',
                    });
                }

                $('#login-popup-form-validation').append( "<p class=\"alert alert-danger\">"+data.login_message+"</p>" );

                return;
            }

            if(data.status == 'success')
            {
                $('#add-page-popup .form-elements-container').hide();
                $('#add-page-popup').prepend('<a class="elementor_button btn btn-invader" href="'+data.page_url+'&'+data_form+'">'+data.message+'<img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;margin-left:5px" /></a>');
               
                $('#add-page-popup').find('.elementor_button').on('click', function(e){
                    $(this).addClass('active');
                    $(this).find('#ajax-indicator-masking').show();
                });
                
           
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
            ajax_indicator.hide();
            _this.removeClass('active');
        });

        return false;
    });

    jQuery('#button-find').trigger( "click" );

    if($('#popup-login-button').length){
    	$('#popup-login-button').on("click", function(){
        
			var o = new Object();
			var form = '#login-popup';
                        var ajax_indicator = $(form).find('#ajax-indicator-masking');
                        ajax_indicator.show();
                        var that = $(this);
                        that.addClass('active');
			$.ajax({
				url:"<?php echo admin_url('admin-ajax.php?action=elementinvader_action&function=login&page=elementinvader'); ?>",
				method:"POST",
				data: $(form).serialize() + "&website_url=<?php echo get_home_url(); ?>",
				beforeSend:function(){
					jQuery('#popup-login-button .fa-spin').removeClass('hidden');
                },
				success:function(data){

					$('#login-popup-form-validation').html('');

					if(data.alert == 'danger')
					{
						$.each( data.errors, function( key, value ) {
							$('#login-popup-form-validation').append( "<p class=\"alert alert-"+data.alert+"\">"+value+"</p>" );
						});
					}
					else if(data.alert == 'success')
					{
						$('#login-popup-form-validation').append( "<p class=\"alert alert-"+data.alert+"\">"+data.message+"</p>" );
						$(form)[0].reset();

                        setTimeout(function(){ location.reload(); }, 3000);
					}
					else
					{
						$('#login-popup-form-validation').append( "<p class=\"alert alert-"+data.alert+"\">"+data.message+"</p>" );
					}

				},
				error:function(){
				},
                                complete: function(data) {
                                    ajax_indicator.hide();
                                    that.removeClass('active');
                                }
			});
    	});
	}

    function define_results_triggers()
    {

        $('a.open_login_form').on('click', function(){

            var el = $('#login-popup');
            if (el.length) {
                $.magnificPopup.open({
                    items: {
                        src: el
                    },
                    type: 'inline',
                });
            }

            return false;
        });

        $('a.kit-like').on('click', function(){
            <?php if(elementinvader_loggedin()): ?>

            var is_active = !$(this).parent().hasClass('liked');
        
            if($(this).parent().hasClass('liked'))
            {
                $(this).parent().removeClass('liked');
            }
            else
            {
                $(this).parent().addClass('liked');
            }

            var a_button = $(this);

			$.ajax({
				url:"<?php echo admin_url('admin-ajax.php?action=elementinvader_action&function=icon_click&page=elementinvader'); ?>",
				method:"POST",
				data: "icon=like&template_id="+$(this).attr('kit_id')+"&is_enabled="+is_active,
				beforeSend:function(){
                },
				success:function(data){
                    if(data.status == 'FAILED')
                    {
                        ShowStatus.show(data.message);

                        var el = $('#login-popup');
                        if (el.length) {
                            $.magnificPopup.open({
                                items: {
                                    src: el
                                },
                                type: 'inline',
                            });
                        }
                    }
                    else
                    {
                        ShowStatus.show(data.message);

                        a_button.find('span.num').html(data.counter);
                    }
				},
				error:function(){
				}
			});

            <?php else: ?>
                var el = $('#login-popup');
                if (el.length) {
                    $.magnificPopup.open({
                        items: {
                            src: el
                        },
                        type: 'inline',
                    });
                }
            <?php endif; ?>

            return false;
        });

        $('a.kit-saved').on('click', function(){
            <?php if(elementinvader_loggedin()): ?>

            var is_active = !$(this).parent().hasClass('liked');
        
            if($(this).parent().hasClass('liked'))
            {
                $(this).parent().removeClass('liked');
            }
            else
            {
                $(this).parent().addClass('liked');
            }

            var a_button = $(this);

			$.ajax({
				url:"<?php echo admin_url('admin-ajax.php?action=elementinvader_action&function=icon_click&page=elementinvader'); ?>",
				method:"POST",
				data: "icon=favourite&template_id="+$(this).attr('kit_id')+"&is_enabled="+is_active,
				beforeSend:function(){
                },
				success:function(data){
                    if(data.status == 'FAILED')
                    {
                        ShowStatus.show(data.message);

                        var el = $('#login-popup');
                        if (el.length) {
                            $.magnificPopup.open({
                                items: {
                                    src: el
                                },
                                type: 'inline',
                            });
                        }
                    }
                    else
                    {
                        ShowStatus.show(data.message);

                        a_button.find('span.num').html(data.counter);
                    }
				},
				error:function(){
				}
			});

            <?php else: ?>
                var el = $('#login-popup');
                if (el.length) {
                    $.magnificPopup.open({
                        items: {
                            src: el
                        },
                        type: 'inline',
                    });
                }
            <?php endif; ?>

            return false;
        });
    }

    function define_animate()
    {
        jQuery("a.img_link").hover(function(){
            if(jQuery(this).find('img').height() < jQuery(this).height() ) return false;
        var top_size = jQuery(this).find('img').height()-jQuery(this).height();
        jQuery(this).find('img').animate({
            top: -top_size,
        }, top_size*5,"linear", function() {
            // Animation complete.
        });
        },function(){
            jQuery(this).find('img').stop().css('top', '0px');
        });
    }

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

                    $('.license-group').removeClass('hidden');

                    $('.elementor_button').remove();
                    $('#add-page-popup p.alert').remove();
                    $('#inputPage').val('');
                    $('#inputLicense').val('');

        		},
                
        		open: function() {

                    var magnificPopup = $.magnificPopup.instance,
                    cur = magnificPopup.st.el.parent();

                    $('#inputTemplate').val(cur.prevObject.attr('f_item'));   
                    $('#inputPage').val(cur.parent().find('.page-name').html());   

                    if(cur.prevObject.attr('f_price') == '0.00')
                    {
                        $('.license-group').addClass('hidden');
                    }            
                    
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
<form id="add-page-popup" class="form-horizontal mfp-hide incl_title white-popup-block wrap elementinvader_wrap">
    <h3 class="white-popup-block-title"><?php echo __('Import template', 'elementinvader'); ?></h3>
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
    <div class="control-group hidden license-group">
        <label class="control-label" for="inputLicense"><?php echo __('License Key', 'elementinvader'); ?></label>
        <div class="controls">
            <input type="text" name="license_key" id="inputLicense" value="" placeholder="<?php echo __('License key', 'elementinvader'); ?>">
        </div>
        <a target="_blank" href="https://order.shareit.com/cart/new?vendorid=200252231&PRODUCT[300993299]=1&currency=USD">Purchase license key here if you don't have one</a>
    </div>
    <div class="control-group">
        <div class="controls">
            <button id="add-page-button" type="button" class="btn btn-invader"><?php echo __('Add this page', 'elementinvader'); ?> <img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;" /></button>
        </div>
    </div>
    </div>
</form>

<form id="login-popup" class="form-horizontal mfp-hide white-popup-block wrap elementinvader_wrap">
    <div id="login-popup-form-validation">
    </div>
    
    <div class="form-elements-container">

    <div class="control-group">
        <label class="control-label" for="login_inputEmail"><?php echo __('Your Email'); ?></label>
        <div class="controls">
            <input type="text" name="email" id="login_inputEmail" value="" placeholder="">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="login_inputPassword"><?php echo __('Password'); ?></label>
        <div class="controls">
            <input type="password" name="password" id="login_inputPassword" value="" placeholder="">
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            <button id="popup-login-button" type="button" class="btn btn-invader"><?php echo __('Login'); ?> <img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;" />
</button>
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            <br />
            <a href="<?php echo ELEMENTINVADER_WEBSITE.'login/register'; ?>" target="_blank" class="btn-link">You don't have account? Then register here!</a>
        </div>
    </div>
    
    </div>
</form>