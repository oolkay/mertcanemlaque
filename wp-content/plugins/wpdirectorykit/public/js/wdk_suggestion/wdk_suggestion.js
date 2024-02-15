/*
Item Name: wdk Suggestion
Author: sanljiljan
Author URI: http://codecanyon.net/user/sanljiljan
Version: 1.0
*/

jQuery.fn.wdkSuggestion = function(options) {
    var defaults = {
        ajax_url: null,
        ajax_param: {},
        language_id: null,
        callback_selected: function(key) {
            console.log('called callback: ' + key);
        }
    };

    var options = jQuery.extend(defaults, options);

    var jqxhr;
    var is_loading = false;
    var request;

    /* Public API */
    /*
    this.getCurrent = function()
    {
        return options.currElImg;
    }

    this.getIndex = function(){
        return options.currIndex;
    };
    */
    var last_search,last_results = null;
     
    return this.each(function () {
        options.obj = jQuery(this);

        options.firstLoad = true;
        options.endLoad = false;

        generateHtml();
        
        options.obj.attr('autocomplete','off');

        // Add loading indicator
        //options.obj.parent().find(".circle-loading-bar").addClass(options.progressBar);

        // open scroll part
        options.obj.click();

        // hide when click outside
        jQuery(document).mouseup(function (e) {
            var container = options.obj.parent();
            if (!container.is(e.target) // if the target of the click isn't the container...
                &&
                container.has(e.target).length === 0) // ... nor a descendant of the container
            {
                hideSuggestion();
            }
        });

        // load first n values
        //loadMore();

        var timeout_init;
        // keypress/typing event
        jQuery(options.obj).on('keyup',function () {
            clearTimeout(timeout_init);
            if (jQuery(this).val().length < 3) return false;
            timeout_init = setTimeout(function () {
                options.endLoad = false;
                loadMore();
            }, 1000);
        }).on('keypress',function(event) {
            if (event.which == 13) {
                // return false;
            }
        }).on('focus', function(event) {
            options.endLoad = false;
            //loadMore();
        });

        return this;
    });

    function showSuggestion() {
        var container = options.obj.parent().find('.list_container');
        
        if (container.hasClass('win_visible')) {
            //container.hide();
        } else {
            if (parseInt(jQuery(window).height() - (options.obj.offset().top-jQuery(window).scrollTop())) < 300) {
                options.obj.parent().addClass('suggestion_above')
            } else {
                options.obj.parent().removeClass('suggestion_above')
            }

            container.show();
            container.parent().addClass('win_open');
            container.addClass('win_visible');
            if (options.firstLoad)
                options.obj.parent().find('.list_scroll').scrollTop(0);
            options.firstLoad = false;

            jQuery(options.obj.parent().find('.search_term')).focus();
        }

        return false;
    }

    function hideSuggestion() {
        var container = options.obj.parent().find('.list_container');
        container.hide();
        container.parent().removeClass('win_open');
        container.removeClass('win_visible');
        return false;
    }

    function loadMore() {
        showSpinner();

        var search_term_val = options.obj.val();
        
        options.obj.parent().find('ul').html('');

        /* if same query */
        if(last_search == search_term_val) {
            var list_items = options.obj.parent().find('ul');
            generateList(last_results, list_items);
            
            options.obj.parent().find('.list_scroll').scrollTop(0);

            if(true) {
                list_items.find('li:not(.no-event)').on('click', function(){
                    options.obj.val(jQuery(this).data('value'));
                    hideSuggestion();
                });
            }

            options.endLoad = true;
        } else {

            var param = {
                search: search_term_val,
                language_id: options.language_id,
            };

            jQuery.extend(param, options.ajax_param);

            // Assign handlers immediately after making the request,
            // and remember the jqxhr object for this request
            if (jqxhr != null)
                jqxhr.abort();

            options.callback_selected(options.obj.val());

            is_loading = true;
            jqxhr = jQuery.post(options.ajax_url, param, function(data) {
                    hideSpinner();
                    var list_items = options.obj.parent().find('ul');
                    //list_items.html('<span class="no_results">' + data.message + '</span>');

                    if (data.success == false) {
                        options.endLoad = true;
                        //ist_items.html('<span class="no_results">' + data.message + '</span>');
                        //alert(data.message);
                    } else {

                        last_search = search_term_val;
                        last_results = data.results;

                        generateList(data.results, list_items)

                        options.obj.parent().find('.list_scroll').scrollTop(0);

                        if(true) {
                            list_items.find('li:not(.no-event)').on('click', function(){
                                if(jQuery(this).attr('data-field') == 'link') {
                                    window.location.href = jQuery(this).attr('data-value')
                                } else {
                                    options.obj.val(jQuery(this).data('value'));
                                    hideSuggestion();
                                }
                            });
                        }

                        options.endLoad = true;
                    }

                    is_loading = false;
            })
            .done(function () {
                //showSuggestion();
            })
            .fail(function() {
                hideSpinner();
                is_loading = false;
            });
        }
    }

    function hideSpinner() {
        options.obj.closest('form').find('.ajax-indicator').addClass('hidden');
    }

    function showSpinner() {
        options.obj.closest('form').find('.ajax-indicator').removeClass('hidden');
    }

    function generateHtml() {
        // hide input element

        options.obj.after(
            '<div class="wdk_suggestion color-secondary">' +
                '<div class="list_container color-primary">' +
                    '<div class="list_scroll">' +
                        '<ul class="list_items">' +
                        '</ul>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function generateList(results,list_items){
        /*
        [
            'field_key' => 'string',
            'value' => 'string',
            'print' => [
                'html' => 'string',
                'parsed_html' => [
                    'left_column' => 'string',
                    'middle_column' => 'string',
                    'right_column' => 'string',
                ],
                'parsed_content' => [
                    'icon' => 'string',
                    'title' => 'string',
                    'sub_title' => 'string',
                    'right_text' => 'string',
                ]
            ]
        ]*/
        var _exists_results = false;
        jQuery.each(results, function (key, item) {
            var html_item = ''

            if(typeof item.print.html !='undefined' && item.print.html != '') {

                html_item = item.print.html;

            } else if(typeof item.print.parsed_html !='undefined' && item.print.parsed_html != '') {
                
                if(typeof item.print.parsed_html.left_column !='undefined' && item.print.parsed_html.left_column != '') {
                    html_item +=  '<div class="column left">' +
                                    +item.print.parsed_html.left_column+
                                '</div>';
                }
                if(typeof item.print.parsed_html.middle_column !='undefined' && item.print.parsed_html.middle_column != '') {
                    html_item += '<div class="column middle">' +
                                +item.print.parsed_html.middle_column+
                            '</div>';
                }
                if(typeof item.print.parsed_html.right_text !='undefined' && item.print.parsed_html.right_column != '') {
                    html_item += '<div class="column right">' +
                                +item.print.parsed_html.right_column+
                            '</div>';
                }

            } else if(typeof item.print.parsed_content !='undefined' && item.print.parsed_content != '') {

                if(typeof item.print.parsed_content.icon_class !='undefined') {
                    html_item +=  '<div class="column left">' +
                                '<i class="'+item.print.parsed_content.icon_class+'"></i>'+
                                '</div>';
                }
                if(typeof item.print.parsed_content.title !='undefined') {
                    html_item += '<div class="column middle"><span class="title">'+item.print.parsed_content.title+'</span>';

                    if(typeof item.print.parsed_content.sub_title !='undefined' && item.print.parsed_content.sub_title != '') {
                        html_item += '<span class="sub-title">'+item.print.parsed_content.sub_title+'</span>';
                    }

                    html_item += '</div>';
                }
                if(typeof item.print.parsed_content.right_text !='undefined') {
                    html_item += '<div class="column right">' +
                                '<span class="right-text">'+item.print.parsed_content.right_text+'</span>'+
                            '</div>';
                }
            }

            if (html_item != '') {
                if(typeof item.value !='undefined' && item.value != '') {
                    list_items.append("<li class='row' data-field='"+item.field_key+"' data-value='"+item.value+"'>"+html_item+"</li>");
                } else {
                    list_items.append("<li class='row no-active'>"+html_item+"</li>");
                }
                _exists_results = true;
            }
        });

        if (_exists_results) {
            showSuggestion();
        }
    }

}