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

<?php

// Check for required plugins

$path = get_template_directory().'/elementinvader/';

$plugins_to_activate = array();
if(file_exists($path))
{
    $templates = array();

    if (is_dir($path))
    {
        if ($dh = opendir($path))
        {
        while (($file = readdir($dh)) !== false)
        {
            if(file_exists($path.$file.'/description.xml'))
            {
                $required_plugins = elementinvader_template_data($file, 'required-plugins');

                $plugins = array();
                if(isset($required_plugins->plugin)) {
                    $plugins = $required_plugins->plugin;
                } else if(is_array($required_plugins) || is_object($required_plugins)) {
                    $plugins = $required_plugins;
                }
               
                foreach($plugins as $key=>$plugin)
                {
                    $plugin = (string) $plugin;
                    if(!is_plugin_active($plugin.'/'.$plugin.'.php'))
                    {
                        $plugins_to_activate[$plugin] = $plugin;
                    }
                }
            }
        }
        closedir($dh);
        }
    }
}

// Check for required plugins
$required_plugins = elementinvader_template_data($template, 'required-plugins');

$plugins_activated = TRUE;
echo '<pre>';

if(count($plugins_to_activate) == 0)
{
    echo '<br /><br />'.__('All plugins already activated','elementinvader');
}

foreach($plugins_to_activate as $key=>$plugin)
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

            echo '<br /><div style="margin-top: 10px;" class="btn-group group-import inline"><div class="elementinvader_separate"><span class="number_container"><span class="number_btn">1</span></span></div><a href="#" plugin="'.esc_attr($plugin).'" class="btn btn-invader action-plugin">'.__('Please click here to  install required plugin, then you can import template','elementinvader').' '.esc_html($plugin).' <img id="ajax-indicator-masking" src="'.ELEMENTINVADER_URL . 'admin/images/ajax-loader-white-small.gif'.'" style="display: none;" /></a></div>';
        }
    }
    else
    {
        echo '<br /><br />'.__('Plugin already activated:','elementinvader').' <b style="color:blue;">'.esc_html($plugin).'</b>';
    }
}

echo '</pre>';

$path = get_template_directory().'/elementinvader/';

if(file_exists($path))
{
    $templates = array();

    if (is_dir($path))
    {
        if ($dh = opendir($path))
        {
        while (($file = readdir($dh)) !== false)
        {
            if(file_exists($path.$file.'/description.xml'))
            {
                $file_name = pathinfo($path.$file, PATHINFO_FILENAME);
                $templates[] =  $file_name;
            }
        }
        closedir($dh);
        }
    }
}

?>

<?php if(!$plugins_activated): ?>
<div class="row">
    <div class="col-md-12">         
        <div class="btn-group group-import inline">
            <div class="elementinvader_separate"><span class="number_container"><span class="number_btn">2</span></span></div>
            <a href="#" class="btn btn-invader action-refresh"><?php echo __('Required plugins not installed or activated, please above install it and then click here to recheck', 'elementinvader'); ?>.<img id="ajax-indicator-masking" src="<?php echo ELEMENTINVADER_URL;?>admin/images/ajax-loader-white-small.gif" style="display: none; margin-left: 3px;" /></a>
        </div>
    </div>
</div>          
<?php else: ?>
<div class="row">
    <div class="col-md-4">

<!-- form itself -->
<form id="template-import-popup" class="form-horizontal white-popup-block wrap form-no-popup">
    <?php if(count($templates) == 0): ?>
    <div class="alert alert-info" role="alert">
        <a href="<?php echo admin_url('admin.php?page=elementinvader_marketplace'); ?>"><?php echo __('Templates not found in your theme, check in other Layouts tab or suggest one here and we will consider to build it','elementinvader'); ?></a>
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

    </div>
</div>
<?php endif; ?>


            </div>
        </div>
    </div>
</div>

<script>

jQuery(document).ready(function($) {

    $('a.action-refresh').click(function()
    {
        ajax_indicator.show();
        $(this).addClass('active'); 
        location.reload(); 
        return false;
    });

    $('a.action-plugin').click(function(e)
    {
        e.preventDefault(); 
        var plugin = $(this).attr('plugin');
        var ajax_nonce = '<?php echo wp_create_nonce( 'updates' ); ?>';
        var data_form = "action=install-plugin&username=&password=&_ajax_nonce="+ajax_nonce+"&_fc_nonce=&connection_type=&public_key=&private_key=&slug="+plugin;
        var that = $(this);

        that.attr("disabled", "disabled");
        that.unbind('click');
        
        var ajax_indicator =  $(this).find('#ajax-indicator-masking');
        ajax_indicator.show();
        that.addClass('active');  
        var jqxhr = $.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data_form, function(data) {

            if(data.success == true)
            {
                that.parent().html("<?php echo __('Plugin installed successfuly:', 'elementinvader'); ?>  <b style=\"color:blue;\">"+plugin+"</b>");

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

    $('#import-pages-button').click(function()
    {
        var data_form = $('#template-import-popup').serialize();
        var data_form_array = $('#template-import-popup').serializeArray();
        var ajax_indicator =  $('#template-import-popup').find('#ajax-indicator-masking');
        var that = $(this);
        
        ajax_indicator.show();
        that.addClass('active');      
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
        })
        .fail(function(data) {
            alert( "Error: " + data );
        })
        .always(function(data) {
            ajax_indicator.hide();
            that.removeClass('active');  
        });
    });

});

</script>