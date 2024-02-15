<?php
/**
 * The template for Settings.
 *
 * This is the template that edit form settings
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('WDK Demo Import', 'wpdirectorykit'); ?></h1>
    <br /><br />
    <div class="wdk-body">
        <div class="postbox" style="display: block;">
            <div class="postbox-header">
                <h3><?php echo __('Step 2 Import for theme:', 'wpdirectorykit'); ?> <?php echo esc_html($current_theme->get( 'Name' )); ?></h3>
            </div>
            <div class="inside">
                <div class="wdk-install-plugins-content-header">
                    <h2><?php echo __('Importing Demo Content', 'wpdirectorykit'); ?></h2>
                    <p><?php echo __('Now we importing demo content like Pages/Posts/Menus and Listings', 'wpdirectorykit'); ?></p>
                </div>
                    <label slug="" source="" class="plugin-item install_content">
                        <div class="plugin-item-content">
                            <div class="plugin-item-content-title">
                                <h3><img src="<?php echo WPDIRECTORYKIT_URL; ?>admin/img/loader.svg" class="wdk-loading wdk-loading-md hidden" alt="Loading..."><?php echo __('Importing Pages/Posts/Menus','wpdirectorykit'); ?></h3>
                            </div>
                            <div class="plugin-item-error js-wdk-plugin-item-error"></div>
                            <div class="plugin-item-info js-wdk-plugin-item-info"></div>
                        </div>
                    </label>

                    <label slug="" source="" class="plugin-item install_listings">
                        <div class="plugin-item-content">
                            <div class="plugin-item-content-title">
                                <h3><img src="<?php echo WPDIRECTORYKIT_URL; ?>admin/img/loader.svg" class="wdk-loading wdk-loading-md hidden" alt="Loading..."><?php echo __('Importing Listings and Configurations','wpdirectorykit'); ?></h3>
                            </div>
                            <div class="plugin-item-error js-wdk-plugin-item-error"></div>
                            <div class="plugin-item-info js-wdk-plugin-item-info"></div>
                        </div>
                    </label>
            </div>
        </div>
        <p class="wdk_button-container">
            <a href="<?php echo get_home_url();?>" class="install_button wdk_button button button-hero button-primary" disabled="disabled"><?php echo __('Visit Website', 'wpdirectorykit'); ?></a>
        </p>
    </div>
    <br/>
    <div class="alert alert-info" role="alert"><a href="<?php echo esc_html($current_theme->get( 'ThemeURI' )); ?>" target="_blank"><?php echo __('Theme Data will be downloaded from API:','wpdirectorykit'); ?> <?php echo esc_html($current_theme->get( 'AuthorURI' )); ?> <?php echo __('On any trouble contact us via website contact page.','wpdirectorykit'); ?></a></div>
</div>

<script>

jQuery( document ).ready(function($) {

    function install_content(){

        var ajax_nonce = '<?php echo wp_create_nonce( 'updates' ); ?>';

        var that = $('.install_content');

        var ajax_param = { 
            "page": 'wdk_backendajax',
            "function": 'install_content',
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-install_content'));?>',
            };

        var ajax_indicator =  that.find('.wdk-loading');
        ajax_indicator.show();

        var jqxhr = $.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", ajax_param, function(data)
        {
            if(data.success == true)
            {
                that.find('h3').css('color', 'green');
            } else if(typeof(data.message) != "undefined") {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data.message+"</b></div>");
            }
            else
            {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data+"</b></div>");
            }

        })
        .done(function(data) {
        })
        .fail(function(data) {
            //console.log(data.responseText);

            if(typeof(data.responseText) != "undefined")
            {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data.responseText+"</b></div>");
            }
            else
            {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data+"</b></div>");
            }
            
            //alert( "Error: " + data );
            is_failed=true;
        })
        .always(function(data) {
            ajax_indicator.hide();
            that.removeClass('active');  
        });

    }

    function install_listings(){

        var ajax_nonce = '<?php echo wp_create_nonce( 'updates' ); ?>';

        var that = $('.install_listings');

        var ajax_param = { 
            "page": 'wdk_backendajax',
            "function": 'install_listings',
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-install_listings'));?>',
            };

        var ajax_indicator =  that.find('.wdk-loading');
        ajax_indicator.show();

        var jqxhr = $.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", ajax_param, function(data)
        {
            if(data.success == true)
            {
                that.find('h3').css('color', 'green');
            } else if(typeof(data.message) != "undefined") {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data.message+"</b></div>");
            }
            else
            {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data+"</b></div>");
            }

        })
        .done(function(data) {
        })
        .fail(function(data) {
            if(typeof(data.responseText) != "undefined")
            {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data.responseText+"</b></div>");
            }
            else
            {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data+"</b></div>");
            }
            is_failed=true;
        })
        .always(function(data) {
            ajax_indicator.hide();
            that.removeClass('active');  
        });

    }

    $.ajaxSetup({async: false});

    install_content();

    install_listings();

    $('.install_button').removeAttr('disabled');

    $.ajaxSetup({async: true});













/*
    $('.install_button').click(function (event) {

        // Don't follow the link
        event.preventDefault();

        if($(this)[0].hasAttribute("disabled"))
        {
            alert('Wait until proccess complete or if takes to long (more then 10 mins) then refresh');
            return false;
        }

        $(this).attr('disabled','disabled');

        var is_failed=false;

        $( ".plugin-item" ).each(function() {
            var slug = $( this ).attr('slug');
            var source = $( this ).attr('source');

            if (typeof source == "undefined" ) {
                source = '';
            }

            if(!$(this).find('input[type="checkbox"]').is(':checked'))
                return;
                
            var ajax_nonce = '<?php echo wp_create_nonce( 'updates' ); ?>';

            var that = $(this);

            var ajax_param = { 
                                "page": 'wdk_backendajax',
                                "function": 'plugin_upgrader',
                                "action": 'wdk_public_action',
                                "slug": slug,
                                "source": source,
                             };

            that.attr("disabled", "disabled");

            that.addClass('active');  
            var jqxhr = $.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", ajax_param, function(data) {

                if(data.success == true)
                {
                    that.find('h3').css('color', 'green');
                } else {
                    that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data.slug+" "+data.message+"</b></div>");
                }

            })
            .done(function(data) {
            })
            .fail(function(data) {
                that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data+"</b></div>");
                is_failed=true;
            })
            .always(function(data) {
                ajax_indicator.hide();
                that.removeClass('active');  
            });
                
        });

        $.ajaxSetup({async: true});

        if(is_failed == false)
            window.location.replace($(this).attr('href'));
        else
            $(this).removeAttr('disabled');
    });*/
});


</script>

<style>

.wdk-install-plugins-content-header {
  padding: 30px;
  border-bottom: 1px solid #dddddd;
}

.inside .plugin-item{
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  margin: 0 30px;
  padding: 0px 0;
  border-bottom: 1px solid #eeeeee;
}

.wdk-loading-md {
  width: 32px;
  height: 32px;
}

.wdk-loading {
  -webkit-animation: 0.65s linear infinite wdk-rotation;
  animation: 0.65s linear infinite wdk-rotation;
  height:18px;
  margin-right: 5px;
}

.wdk-loading.hidden
{
    display:none;
}

@keyframes wdk-rotation {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(359deg);
  }
}

span.green
{
    color: green;
}

</style>

<?php $this->view('general/footer', $data); ?>