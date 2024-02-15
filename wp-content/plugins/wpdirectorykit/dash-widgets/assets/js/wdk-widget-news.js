(function ($) {
  'use strict';

  jQuery(document).ready(function ($) {
    wdk_dash_widget_news();
  });
})(jQuery);

const wdk_dash_widget_news = ($plugin_api = false) => {
  var ajax_param = {
    "page": 'wdk_backendajax',
    "function": 'plugin_news',
    "action": 'wdk_public_action',
  };

  if($plugin_api) {
      var jqxhr = jQuery.post(script_parameters.ajax_url, ajax_param, function (data) {
        var content = '';
        if (typeof data.rss != 'undefined')
        jQuery.each(data.rss, function (key, val) {
          content += '<tr><td class="date">' + val.date + '</td><td class="title"><a href="' + val.link + '" target="_blank">' + val.title + '</a></td></tr>';
        });
        
        if(content == '') {
        content += '<tr><td colspan="5">' + script_parameters.empty_results + '</td></tr>';
      }
      jQuery('#wdk_script_news_table').html(content);
    });
  } else {
    
    jQuery.ajax({
        url: 'https://wpdirectorykit.com/wp/last_news.php?f=news.json',
        method: 'get',
        dataType: 'json',
        success: function(data){
          var content = '';
          if (typeof data != 'undefined')
          jQuery.each(data, function (key, val) {
            content += '<tr><td class="date">' + val.date + '</td><td class="title"><a href="' + val.link + '" target="_blank">' + val.title + '</a></td></tr>';
          });
           
          if(content == '') {
            content += '<tr><td colspan="5">' + script_parameters.empty_results + '</td></tr>';
          }
          jQuery('#wdk_script_news_table').html(content);
        }
    });
    
  }
  
}