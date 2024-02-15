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
                <h3><?php echo __('Step 1 Import for theme:', 'wpdirectorykit'); ?> <?php echo esc_html($current_theme->get( 'Name' )); ?></h3>
            </div>
            <div class="inside">
                <div class="wdk-install-plugins-content-header">
                    <h2><?php echo __('Before We Import Your Demo', 'wpdirectorykit'); ?></h2>
                    <p><?php echo __('To ensure the best experience, installing the following plugins is strongly recommended, and in some cases required.', 'wpdirectorykit'); ?></p>
                </div>
                <?php foreach($theme_plugins as $theme_plugin): ?>

                    <label slug="<?php echo esc_attr($theme_plugin['slug']); ?>" source="<?php echo isset($theme_plugin['source'])?esc_attr($theme_plugin['source']):''; ?>" 
                            class="plugin-item plugin-item-<?php echo esc_attr($theme_plugin['slug']); ?> <?php echo is_plugin_active( $theme_plugin['slug'].'/'.$theme_plugin['slug'].'.php' )?'plugin_active':'';?>plugin-item--active plugin-item--required" for="wdk-<?php echo esc_attr($theme_plugin['slug']); ?>-plugin">
                        <div class="plugin-item-content">
                            <div class="plugin-item-content-title">
                                <h3><img src="<?php echo WPDIRECTORYKIT_URL; ?>admin/img/loader.svg" class="wdk-loading wdk-loading-md hidden" alt="Loading..."><?php echo esc_html($theme_plugin['name']); ?></h3>
                            </div>
                            <div class="plugin-item-error js-wdk-plugin-item-error"></div>
                            <div class="plugin-item-info js-wdk-plugin-item-info"></div>
                        </div>
                        <span class="plugin-item-checkbox">
                            <?php if(is_plugin_active( $theme_plugin['slug'].'/'.$theme_plugin['slug'].'.php' )): ?>
                            <span class="green"><?php echo __('Already active', 'wpdirectorykit'); ?><span>
                            <?php else: ?>
                            <input type="checkbox" id="wdk-<?php echo esc_attr($theme_plugin['slug']); ?>-plugin" name="<?php echo esc_attr($theme_plugin['slug']); ?>" <?php echo is_plugin_active( $theme_plugin['slug'].'/'.$theme_plugin['slug'].'.php' )?'':'checked="checked"';?>>
                            <?php endif; ?>
                        </span>
                    </label>

                <?php endforeach; ?>
            </div>
        </div>
        <p class="wdk_button-container">
            <a href="<?php echo admin_url('tools.php?page=wdk_demo_import&function=step_2');?>" class="install_button wdk_button button button-hero button-primary"><?php echo __('Continue & Import','wpdirectorykit'); ?></a>
        </p>
    </div>
    <br/>
    <div class="alert alert-info" role="alert"><a href="<?php echo esc_html($current_theme->get( 'ThemeURI' )); ?>" target="_blank"><?php echo __('Theme Data will be downloaded from API:','wpdirectorykit'); ?> <?php echo esc_html($current_theme->get( 'AuthorURI' )); ?> <?php echo __('On any trouble contact us via website contact page.','wpdirectorykit'); ?></a></div>
</div>

<script>

jQuery( document ).ready(function($) {

    $('.install_button').click(function (event) {

        // Don't follow the link
        event.preventDefault();

        if($(this)[0].hasAttribute("disabled"))
        {
            alert('Wait until proccess complete or if takes to long (more then 10 mins) then refresh');
            return false;
        }

        $(this).attr('disabled','disabled');

        $.ajaxSetup({async: false});

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
                                "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-plugin_upgrader'));?>',
                             };

            that.attr("disabled", "disabled");
            /*that.unbind('click');*/
            
            var ajax_indicator =  $(this).find('.wdk-loading');
            ajax_indicator.show();
            that.addClass('active');  
            var jqxhr = $.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", ajax_param, function(data) {

                if(data.success == true)
                {
                    that.find('h3').css('color', 'green');
                } else if(typeof(data.slug) != "undefined") {
                    that.after("<div class=\"alert alert-danger\" role=\"alert\"><?php echo __('Error:', 'wpdirectorykit'); ?>  <b style=\"color:red;\">"+data.slug+" "+data.message+"</b></div>");
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
                
        });

        $.ajaxSetup({async: true});

        if(is_failed == false)
            window.location.replace($(this).attr('href'));
        else
            $(this).removeAttr('disabled');
    });
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