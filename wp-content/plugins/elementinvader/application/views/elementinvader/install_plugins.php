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
            <h3 class="panel-title"><?php echo __('Plugins installation','elementinvader'); ?></h3>
        </div>
        <div class="panel-body">
        <div class="container">
        <div class="row">
        <div class="col-12">
<?php
$number_step = 1;
$required_plugins_array = array();
$use_theme_kit = TRUE;

if(!isset($license_key))
    $license_key = '';

if(strpos($template, 'download_kit_') === 0)

{
    $license_key = (isset($license_key)) ? $license_key : '';
    $online_kit_id = str_replace('download_kit_', '', $template);
    $ret_call = wmvc_api_call('POST', ELEMENTINVADER_WEBSITE.'index.php/marketplace/logdownload/'.$online_kit_id, 
                                        array(
                                            'website_url'   =>  get_home_url(),
                                            'api_token'     =>  get_option('elementinvader_api_token', ''),
                                            'license_key'   =>  $license_key
                                            )
                                    );    

    $ret_call_obj = json_decode($ret_call);

    if(isset($ret_call_obj->{'required-plugins'}) && !empty($ret_call_obj->{'required-plugins'}))
    {
        $required_plugins_array = explode(',', $ret_call_obj->{'required-plugins'});
    }

    $use_theme_kit = FALSE;
}
else
{

    // Check for required plugins
    $required_plugins = elementinvader_template_data($template, 'required-plugins');

    $plugins = array();
    if(isset($required_plugins->plugin)) {
        $required_plugins_array = $required_plugins->plugin;
    } else if(is_array($required_plugins) || is_object($required_plugins)) {
        $required_plugins_array = $required_plugins;
    }
   
}

$plugins_to_activate = array();
$plugins_activated = TRUE;
echo '<pre style="margin-bottom:0;">';
foreach($required_plugins_array as $key=>$plugin)
{
    $plugin = (string) $plugin;
    if(!is_plugin_active($plugin.'/'.$plugin.'.php'))
    {
        $plugins_to_activate[$plugin] = $plugin;
        
        echo '<br /><br />'.__('Start activating plugin:','elementinvader').' <b style="color:blue;">'.esc_html($plugin).'</b>';

        if(file_exists(ELEMENTINVADER_PATH.'../'.$plugin))
        {
            echo '<br />'.__('Plugin found in your WordPress: ','elementinvader').esc_html($plugin);
            echo '<br />'.__('Activating: ','elementinvader').esc_html($plugin);
    
            elementinvader_run_activate_plugin( $plugin.'/'.$plugin.'.php' );
    
            echo '<br /><b style="color:green;">'.__('Plugin ACTIVATED','elementinvader').'</b>';
        }
        else
        {
            $plugins_activated = FALSE;
            echo '<br />'.__('Plugin NOT found in your WordPress: ','elementinvader').' <b style="color:red;">'.esc_html($plugin).'</b>';

            echo '<br /><div style="margin-top: 10px;" class="btn-group group-import inline"><div class="elementinvader_separate"><span class="number_container"><span class="number_btn">'.esc_html($number_step).'</span></span></div><a href="#" plugin="'.esc_html($plugin).'" class="btn btn-invader action-plugin">'.__('Please click here to  install required plugin, then you can import template','elementinvader').' '.esc_html($plugin).' <img id="ajax-indicator-masking" src="'.ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'.'" style="display: none;" /></a></div>';
            $number_step++;
        }
    }
    else
    {
        echo '<br /><br />'.__('Plugin already activated:','elementinvader').' <b style="color:blue;">'.esc_html($plugin).'</b>';
    }
}
echo '</pre>';


?>
    </div>
</div>

<?php if(!$plugins_activated): ?>
<div class="row">
    <div class="col-md-12">         
        <div class="btn-group group-import inline">
            <div class="elementinvader_separate"><span class="number_container"><span class="number_btn"><?php echo esc_html($number_step++);?></span></span></div>
            <a href="#" class="btn btn-invader action-refresh"><?php echo __('Required plugins not installed or activated, please above install it and then click here to recheck', 'elementinvader'); ?>.<img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL;?>admin/images/ajax-loader-white-small.gif" style="display: none; margin-left: 3px;" /></a>
        </div>
    </div>
</div>

<?php else: ?>
<div class="row">
    <div class="col-md-12">
    <br>
    <form id="add-page-popup" class="form-horizontal white-popup-block wrap form-no-popup">
        <div id="popup-form-validation">
        <p class="hidden alert alert-error"><?php echo __('Submit failed, please populate all fields!', 'elementinvader'); ?></p>
        </div>

        <div class="form-elements-container">
        <div class="control-group hidden">
            <label class="control-label" for="inputTemplate"><?php echo __('Template', 'elementinvader'); ?></label>
            <div class="controls">
                <input type="text" name="template" id="inputTemplate" value="<?php echo esc_attr($template); ?>" placeholder="<?php echo __('Template', 'elementinvader'); ?>" readonly>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPage"><?php echo __('Page Title', 'elementinvader'); ?></label>
            <div class="controls">
                <input type="text" name="page_title" id="inputPage" value="<?php echo esc_attr($page_title); ?>" placeholder="<?php echo __('Page Title', 'elementinvader'); ?>">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button id="add-page-button" type="button" class="btn btn-invader"><?php echo __('Add this page', 'elementinvader'); ?> <img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'; ?>" style="display: none;" /></button>
            </div>
        </div>
        </div>
    </form>
            
    </div>
</div>

<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4 shadow-sm">

        <?php if($use_theme_kit): ?>
        <a href="#" class="img_link"><img src="<?php echo get_template_directory_uri().'/elementinvader/'.esc_html($template).'/screenshot.jpg';?>" alt="<?php echo __('Screenshot','elementinvader'); ?>" /></a>
        <?php else: ?>
        <a href="#" class="img_link"><img src="<?php echo ELEMENTINVADER_WEBSITE.'uploads/'.esc_html($online_kit_id).'_screenshot.jpg';?>" alt="<?php echo __('Screenshot','elementinvader'); ?>" /></a>
        <?php endif; ?>
        
        <div class="card-body">
            <p class="card-name"><?php echo esc_html(elementinvader_template_data($template, 'kit-title')); ?><span><?php echo esc_html(elementinvader_template_data($template, 'date')); ?></span></p>
            <p class="page-name"><?php echo esc_html(elementinvader_template_data($template, 'page-title')); ?></p>
            <p class="card-tags"><?php echo esc_html(elementinvader_template_data($template, 'tags')); ?></p>
        </div>
        </div>
    </div>
</div>




            </div>
        </div>
    </div>
</div>

<script>

jQuery(document).ready(function($) {

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

    $('a.action-refresh').click(function(e)
    {
        
        if($(this).hasClass('active')) {console.log('elementinvader: import already started');sw_log_notify('<?php echo __('Please wait', 'elementinvader'); ?>', 'error');return false;}    
       
        if($('a.action-plugin,a.action-refresh').first().not(this).length) {
            console.log('elementinvader: ignored pre step');
            sw_log_notify('<?php echo __('Please follow step by step', 'elementinvader'); ?>', 'loading');
            return false;
        }

        var ajax_indicator =  $(this).find('#ajax-indicator-masking');
        ajax_indicator.show();
        $(this).addClass('active'); 
        
        location.reload(); 
        return false;
    });

    $('a.action-plugin').click(function(e)
    {
        e.preventDefault(); 
        if($(this).hasClass('active')) {console.log('elementinvader: import already started');sw_log_notify('<?php echo __('Please wait', 'elementinvader'); ?>', 'error');return false;}    
        if($('a.action-plugin,a.action-refresh').first().not(this).length) {
            console.log('elementinvader: ignored pre step');
            sw_log_notify('<?php echo __('Please follow step by step', 'elementinvader'); ?>', 'loading');
            return false;
        }

        var plugin = $(this).attr('plugin');
        var ajax_nonce = '<?php echo wp_create_nonce( 'updates' ); ?>';
        var data_form = "action=install-plugin&username=&password=&_ajax_nonce="+ajax_nonce+"&_fc_nonce=&connection_type=&public_key=&private_key=&slug="+plugin;
        var that = $(this);

        that.attr("disabled", "disabled");
        /*that.unbind('click');*/
        
        var ajax_indicator =  $(this).find('#ajax-indicator-masking');
        ajax_indicator.show();
        that.addClass('active');  
        var jqxhr = $.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data_form, function(data) {

            if(data.success == true)
            {
                that.parent().html("<?php echo __('Plugin installed successfuly:', 'elementinvader'); ?>  <b style=\"color:blue;\">"+plugin+"</b>");
            } else {
                that.html("<?php echo __('Error:', 'elementinvader'); ?>  <b style=\"color:red;\">"+data.data.slug+" "+data.data.errorMessage+"</b>");
            }

        })
        .done(function(data) {
        })
        .fail(function(data) {
            alert( "Error: " + data );
        })
        .always(function(data) {
            ajax_indicator.hide();
            that.removeClass('active');  
        });
        
        return false;
    });

    $('#add-page-button').click(function(){
        var _this = $(this);
        if(_this.hasClass('active')) {console.log('elementinvader: import already started');sw_log_notify('<?php echo __('Please wait', 'elementinvader'); ?>', 'error'); return false;}
        _this.addClass('active');
        var data_form = $('#add-page-popup').serialize();
        var data_form_array = $('#add-page-popup').serializeArray();
        var ajax_indicator =  $('#add-page-popup').find('#ajax-indicator-masking');
        var that = $(this);
        
        if($('#inputPage').val() == '')
        {
            alert('<?php echo wmvc_js(__('Please enter Page Title', 'elementinvader')); ?>');
            return;
        }

        ajax_indicator.show();
        
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
            ajax_indicator.hide();
            that.removeClass('active');  
        });

        return false;
    });

});

</script>