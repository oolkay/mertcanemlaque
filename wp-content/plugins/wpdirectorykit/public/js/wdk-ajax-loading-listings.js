jQuery(document).ready(function($){
    //wdk_ajax_loading_listings();
});


const wdk_ajax_loading_listings = ($url = '') => {
    var $ = jQuery,
    jqxhr = null;
    js_show_data = ($field_name, $db_value = null, $default = false) => {
        let output = $default;

        if(typeof $db_value[$field_name] !='undefined' && $db_value[$field_name] !='' ) {
            output = $db_value[$field_name]
        }
        return output;
    };


    if ($url != '' && 'history' in window && 'pushState' in history)
        history.pushState(null, null, $url);

    /* ajax loading */


    var data = {
        "action": 'wdk_public_action',
        "page": 'wdk_frontendajax',
        "function": 'loading_listings',
        "url": $url,
    };

    if($('.wdk_map_results.ajax_results_enabled').length) {
        var el_map  = $('.wdk_map_results.ajax_results_enabled').first();
        data['el_map_id'] = el_map.attr("data-el_id");
        data['el_map_page_id'] = el_map.attr("data-el_page_id");
        data['el_map_type'] = el_map.attr("data-el_type");
    }

    if($('.wdk-listings-results.ajax_results_enabled').length) {
        var el_map = $('.wdk-listings-results.ajax_results_enabled').first();
        data['el_results_id'] = el_map.attr("data-el_id");
        data['el_results_page_id'] = el_map.attr("data-el_page_id");
        data['el_results_type'] = el_map.attr("data-el_type");
    }

    // Assign handlers immediately after making the request,
    // and remember the jqxhr object for this request
    if (jqxhr != null)
        jqxhr.abort();

    jqxhr = jQuery.post( script_parameters.ajax_url, data, function (data) {
        if(data.success) {
            //that.messages_list.find('.message.placeholder').remove(); 
            
            /* map_results */
            if($('.wdk_map_results.ajax_results_enabled').length) {
                $('.wdk_map_results.ajax_results_enabled').each(function(){
                    let self = $(this);

                    /* clear markers */
                    //Loop through all the markers and remove
                    for (var i in wdk_markers) {
                        wdk_clusters.removeLayer(wdk_markers[i]);
                    }
                    wdk_markers = [];

                    var auto_marker_size = false;
                    var clusters_enabled = true;

                    $.each(data.output.map_results, function(index, listing){
                    if(!js_show_data('lng',listing)) return true;

              
            
                    var listing_lat = null, listing_lng = null;

                    listing_lat = js_show_data('lat', listing);
                    listing_lng = js_show_data('lng', listing);
                
                    wdk_markers.push(wdk_generate_marker_ajax_popup(script_parameters.ajax_url, 
                                                                    js_show_data('post_id', listing), 
                                                                    listing_lat, 
                                                                    listing_lng,
                                                                    js_show_data('inner_marker', listing), 
                                                                    wdk_jpopup_customOptions, 
                                                                    auto_marker_size,
                                                                    clusters_enabled));
                    })

                    wdk_map.addLayer(wdk_clusters);

                    /* set center */
                    if(wdk_markers.length){
                        var limits_center = [];
                        for (var i in wdk_markers) {
                            var latLngs = [ wdk_markers[i].getLatLng() ];
                            limits_center.push(latLngs)
                        };
                        var bounds = L.latLngBounds(limits_center);
                        wdk_map.fitBounds(bounds);
                    }
                });

            }

            /* listings results */
            if($('.wdk-listings-results.ajax_results_enabled').length) {
                $('.wdk-listings-results.ajax_results_enabled').each(function(){
                    let self = $(this);
                    self.find('.wdk-inner-listings-results').html('');

                    self.find('.wdk-inner-listings-results').append(js_show_data('listings_result_message',data.output));
                    $.each(data.output.listings_result, function(index, listing){
                        if(!js_show_data('card_view',listing)) return true;
                        self.find('.wdk-inner-listings-results').append('<div class="wdk-col">'+js_show_data('card_view',listing)+'</div>');
                    })
                    self.find('.filter-status').html('<span>'+js_show_data('listings_count_html',data.output)+'</span>');
                    self.find('.wdk-pagination').replaceWith(js_show_data('pagination_html',data.output));
                });
            }

            var html='';
            if(data.output.related_message) {
            }

            if(html != '') {
                //that.messages_list.append(html);
               // that.messages_list.scrollTop(that.messages_list.prop('scrollHeight')  + 250);
            }

        }
    }).always(function() {
        $("form.wdk-search-form .wdk-search-start").find('.fa-ajax-indicator').css('display', 'none');
        $('.wdk-listings-results.ajax_results_enabled .wdk-pagination .page-numbers').on('click', function (e) {
            e.preventDefault();
            wdk_ajax_loading_listings($(this).attr('href'));
        })
    });
}

