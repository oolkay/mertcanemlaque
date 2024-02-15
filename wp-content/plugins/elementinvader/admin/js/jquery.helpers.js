
/**
 * This tiny script just helps us show save status
 */

var ShowStatus;
jQuery(document).ready(function($) {
    ShowStatus = (function() {
        "use strict";
    
        var elem,
            hideHandler2,
            that = {};
    
        that.init = function(options) {
            jQuery('body').append('<div style="display: none; z-index:10040;" class="bb-alert alert alert-danger"><span>Data saved...</span></div>');
            elem = $('div.bb-alert');
        };
    
        that.show = function(text) {
            clearTimeout(hideHandler2);
            
            if(jQuery('div.bb-alert').length == 0)
            {
                that.init();
            }

            var scroll = $(window).scrollTop();

            jQuery('div.bb-alert').css({ top: (scroll+200)+'px' });

            elem.find("span").html(text);

            elem.fadeIn(200);
    
            hideHandler2 = setTimeout(function() {
                console.log('setTimeout');
                that.hide();
            }, 4000);
        };
    
        that.hide = function() {
            elem.fadeOut(200);
        };
    
        return that;
    }());

});


const sw_log_notify = (text, type, popup_place) => {
    var $ = jQuery;
    if (!$('.sw_log_notify-box').length) $('body').append('<div class="sw_log_notify-box"></div>')
    if (typeof text == "undefined") var text = 'Undefined text';
    if (typeof type == "undefined") var type = 'success';
    if (typeof popup_place == "undefined") var popup_place = $('.sw_log_notify-box');
    var el_class = '';
    var el_timer = 5000;
    switch (type) {
        case "success":
            el_class = "success";
            break
        case "error":
            el_class = "error";
            break
        case "loading":
            el_class = "loading";
            el_timer = 2000;
            break
        default:
            el_class = "success";
            break
    }

    /* notify */
    var html = '';
    html = '<div class="sw_log_notify ' + el_class + '">\n\
                   ' + text + '\n\
           </div>';
    var notification = $(html).appendTo(popup_place).delay(100).queue(function() {
            $(this).addClass('show')
            setTimeout(function() {
                notification.removeClass('show')
                setTimeout(function() {
                    notification.remove();
                }, 1000);
            }, el_timer);
        })
        /* end notify */
}

