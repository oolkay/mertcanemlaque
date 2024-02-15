(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $(function () {
        $('.wdk-click-load-animation').on('click', function () {
            $(this).find('.fa-ajax-indicator').css('display', 'inline-block');
            
            if ($(this).closest('form').length) {
                if (!$(this).closest('form')[0].checkValidity()) {
                    $(this).find('.fa-ajax-indicator').css('display', 'none');
                }
            }
        });

        $('.wdk-form-animation').on('submit', function () {
            var form, indicator;
            form = $(this);
            indicator = form.find('.fa-ajax-indicator');
            indicator.css('display', 'inline-block');

            if(form[0].checkValidity())
                indicator.css('display', 'none');
        });

        
        $('.wdk-submit-loading').on('click', function () {
            var form;
            form = $(this).closest('form');

            if( $(this).hasClass('wdk_btn_load_indicator')) {
                console.log('disabled')
                return false;
            }

            $(this).addClass('wdk_btn_load_indicator disabled');
        });
        
        $('.wdk-click-loading').on('click', function (e) {

            if( $(this).hasClass('wdk_btn_load_indicator')) {
                return false;
            }
            $(this).addClass('wdk_btn_load_indicator disabled');
        });

        const wdk_start_search = (form) => {
            var url,scrollTo, data = {};
            url = form.attr('action').replace(/#results/, '');
            if (url.indexOf('?') == -1) {
                url += '?';
            } else {
                url += '&';
            }
            var str_parameters = "";
            $.each($("form.wdk-search-form:visible").serializeArray(), function (i, k) {
                if (k.value != '' && k.name.indexOf('skip') == -1) {
                    if (str_parameters != "") {
                        str_parameters += "&";
                    }
                    str_parameters += k.name + "=" + encodeURIComponent(k.value); 
                }
            });

            $.each($(".wdk-search-popup .toggle-btn:visible"), function (i, k) {
                let el = $($(this).attr('data-wdk-target'));
                if(el && el.length) {
                    $.each(el.find('.wdk-search-form').serializeArray(), function (i, k) {
                        if (k.value != '' && k.name.indexOf('skip') == -1) {
                            if (str_parameters != "") {
                                str_parameters += "&";
                            }
                            str_parameters += k.name + "=" + encodeURIComponent(k.value); 
                        }
                    });
                }
            });

            /* view_type */
            if($('.wmvc-view-type .nav-link.active').length) {
                str_parameters += "&wmvc_view_type="+$('.wmvc-view-type .nav-link.active').attr('data-id'); 
            } 

            /* order by */
            if($('.wdk-order').val() != '' && $('.wdk-order').val()) {
                str_parameters += "&order_by="+$('.wdk-order').val(); 
            } 

            if ($('.wdk-search-form[data-scrollto]').length)
                scrollTo = '#'+$('.wdk-search-form[data-scrollto]').attr('data-scrollto');
                


            var setCookie = (cname, cvalue, exdays) => {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires="+d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
            setCookie('wdk_last_search', str_parameters);

            return url+str_parameters+scrollTo;
        }

        /* set current page for search results if exists results widget and enabled in setttings*/
        if($('form.wdk-search-form').length && $('.wdk-listings-results').length && $("form.wdk-search-form").attr('data-current-link') != '') {
            $('form.wdk-search-form').attr('action', $("form.wdk-search-form").attr('data-current-link'));
        }

        $("form.wdk-search-form").on('submit', function (e) {
            e.preventDefault();
            var url = wdk_start_search($(this));
            $(this).closest('form').addClass('loading')
            if (decodeURI(window.location.href) == decodeURI(url)) {
                window.location.reload();
            } else {
                window.location.href = url;
            }
            return false;
        });

        $("form.wdk-search-form.auto_search").find('input, select').on('input', function (e) {
            $("form.wdk-search-form").first().trigger('submit')
        });

        $("form.wdk-search-form .wdk-search-start").on('click', function (e) {
            e.preventDefault();
            var url = wdk_start_search($(this).closest('form'));
            $(this).closest('form').addClass('loading')

            if($(this).closest('.ajax_results_enabled').length) {
                if (typeof wdk_ajax_loading_listings == 'function') {
                    wdk_ajax_loading_listings(url);
                }
            } else if (decodeURI(window.location.href) == decodeURI(url)) {
                window.location.reload(url);
            } else {
                window.location.href = url;
            }
            return false;
        })

        $('.wdk-listings-results.ajax_results_enabled .wdk-pagination .page-numbers').on('click', function (e) {
            e.preventDefault();
            if (typeof wdk_ajax_loading_listings == 'function') {
                wdk_ajax_loading_listings($(this).attr('href'));
            }
        })

        /* elementor popup */
        jQuery( document ).on( 'elementor/popup/show', () => {
            wdk_reset_form();
        } );

        const wdk_reset_form = () => {
            $("form.wdk-search-form .wdk-search-reset").off().on('click', function (e) {
                e.preventDefault();
                let this_form = jQuery(this).closest('form');
                this_form.find('input:not([type="checkbox"]):not([name="element_id"]):not([type="radio"]):not([type="hidden"]),textarea,select').val('');
                this_form.find('select').val(jQuery(this).find('option:first').val()).trigger('change')
                this_form.find('input[type="checkbox"]').prop('checked', false); 
                this_form.find('.wdk-field.LOCATION .wdk_dropdown_tree button:first-child').html(this_form.find('.wdk-field.LOCATION .list_items li:first').text()); 
                this_form.find('.wdk-field.CATEGORY .wdk_dropdown_tree button:first-child').html(this_form.find('.wdk-field.CATEGORY .list_items li:first').text()); 
               
                return false;
            })
        };
        wdk_reset_form();

        /* date time fields init */
        if ($('.wdk-fielddate').length && typeof $.datepicker != 'undefined') {

            $('.wdk-fielddate').each(function () {
                let dateFormat = script_parameters.format_date_js;
				var self = $(this);

                if (self.attr('date-format'))
                    dateFormat = self.attr('date-format');

                self.datepicker({ dateFormat: dateFormat,  onSelect: function() {
						self.parent().find('.db-date').val(wdk_date_sql_normalize(self.val(), self)).trigger('input');
					}
				}).on( "change", function() {
					self.parent().find('.db-date').val(wdk_date_sql_normalize(self.val(), self));
				});
                
                if(self.parent().find('.db-date').val() == '') {
                    self.parent().find('.db-date').val(wdk_date_notime_sql_normalize());
                }
            })
        } ;

        
        if ($('.wdk-fielddatetime').length && typeof $.datepicker != 'undefined') {

            $('.wdk-fielddatetime').each(function () {
                let dateFormat = script_parameters.format_datetime_js;

				var self = $(this);
                if (self.attr('date-format'))
                    dateFormat = self.attr('date-format');

                self.datepicker({ dateFormat: dateFormat,  onSelect: function() {
                        var datetime = wdk_date_notime_sql_normalize(self.val(), self);
                        if(self.parent().find('[name="hours_mask"]').val() !='') {
                            datetime += ' '+wdk_pad(self.parent().find('[name="hours_mask"]').val());
                        } else {
                            datetime += ' 00';
                        }
                        if(self.parent().find('[name="minutes_mask"]').val() !='') {
                            datetime += ':'+wdk_pad(self.parent().find('[name="minutes_mask"]').val());
                        } else {
                            datetime += ':00';
                        }
                        datetime += ':00';

						self.parent().find('.db-date').val(datetime).trigger('input');
					}
				}).on( "change", function() {
                    
                    var datetime = wdk_date_notime_sql_normalize(self.val(), self);
                    if(self.parent().find('[name="hours_mask"]').val() !='') {
                        datetime += ' '+wdk_pad(self.parent().find('[name="hours_mask"]').val());
                    } else {
                        datetime += ' 00';
                    }
                    if(self.parent().find('[name="minutes_mask"]').val() !='') {
                        datetime += ':'+wdk_pad(self.parent().find('[name="minutes_mask"]').val());
                    } else {
                        datetime += ':00';
                    }
                    datetime += ':00';
					self.parent().find('.db-date').val(datetime);
				});

                if(self.parent().find('.db-date').val() == '') {
                    self.parent().find('.db-date').val(wdk_date_sql_normalize());
                }

                self.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function(){
                    var datetime = wdk_date_notime_sql_normalize(self.val(), self);
                    if(self.parent().find('[name="hours_mask"]').val() !='') {
                        datetime += ' '+wdk_pad(self.parent().find('[name="hours_mask"]').val());
                    } else {
                        datetime += ' 00';
                    }
                    if(self.parent().find('[name="minutes_mask"]').val() !='') {
                        datetime += ':'+wdk_pad(self.parent().find('[name="minutes_mask"]').val());
                    } else {
                        datetime += ':00';
                    }
                    datetime += ':00';
					self.parent().find('.db-date').val(datetime);
                });
            })
		};

		if ($('.wdk-fielddate_from').length && $('.wdk-fielddate_to').length && typeof $.datepicker != 'undefined') {
			var dateFormat, from, to;
			const getDate = ( element ) => {
				var date;
				try {
					date = $.datepicker.parseDate( dateFormat, element.value );
				} catch( error ) {
					date = null;
				}
				return date;
			} 

			dateFormat = script_parameters.format_date_js;
			if ($('.wdk-fielddate_from').attr('date-format'))
				dateFormat = $('.wdk-fielddate_from').attr('date-format');	
				
			from = $('.wdk-fielddate_from')
				.datepicker({
					dateFormat: dateFormat,
					onSelect: function( selectedDate ) {
						to.datepicker("option", "minDate", selectedDate );
						setTimeout(function(){
							to.datepicker('show');
						}, 16);
						
						from.parent().find('.db-date').val(wdk_date_sql_normalize(from.val(), from));
                        wdk_date_add_hours(from);
					}
				}).on( "change", function() {
                    from.parent().find('.db-date').val(wdk_date_sql_normalize(from.val(), from)).trigger('input');
                });
				
			to = $('.wdk-fielddate_to').datepicker({
				dateFormat: dateFormat
			})
			.on( "change", function() {
				from.datepicker( "option", "maxDate", getDate( this ) );
				to.parent().find('.db-date').val(wdk_date_sql_normalize(to.val(), to)).trigger('input');
                wdk_date_add_hours(to);
			});

            if(to.parent().find('.db-date').val() == '') {
                to.parent().find('.db-date').val(wdk_date_sql_normalize());
            }

            if(from.parent().find('.db-date').val() == '') {
                from.parent().find('.db-date').val(wdk_date_sql_normalize());
            }
            			
			from.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function(){
				wdk_date_add_hours(from);
			});
			
			to.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function(){
				wdk_date_add_hours(to);
			});
		}
        if(typeof $.fn.wdkSuggestion == 'function') {

            if(typeof script_parameters.settings_wdk_field_search_suggestion_disable != 'undefined' && script_parameters.settings_wdk_field_search_suggestion_disable == 1) {

            } else {
                $('#wdk_field_search').wdkSuggestion({
                    ajax_url: script_parameters.ajax_url,
                    ajax_param: { 
                        "action": 'wdk_public_action',
                        "page": 'wdk_frontendajax',
                        "function": 'search_suggestion',
                    },
                    language_id: '',
                    text_search: 'Search',
                    callback_selected: function(key) {
                        
                    }
                });
            }

        }
        
        $('.wdk-control[type="number"]').on('change',function(){
            let val = $(this).val();
            let step = 1;
            if(val.length>=7){
                step = 10000;
            } else if(val.length>=4) {
                step = 10;
            }
            $(this).attr('step', step);
        })


        if(typeof $.fn.fieldSliderRange == 'function' && typeof $.fn.ionRangeSlider == 'function') {
            $('.wdk-slider-range-field').fieldSliderRange();
        }

        wdk_result_listings_thumbnail_slider();

        /* if video exists on thumbnail css trick */
        $('.wdk-listing-card .wdk-thumbnail .wdk-image.media').find('iframe,video').css('padding-bottom', +($('.wdk-listing-card .wdk-thumbnail .wdk-over-image-bottom').outerHeight())+'px');


        if(typeof $.fn.WdkScrollMobileSwipe == 'function') {
            $('.WdkScrollMobileSwipe_enable').WdkScrollMobileSwipe();
            $('.WdkScrollMobileSwipe_elementor_enable .elementor-container').WdkScrollMobileSwipe();
        }

        if($('.wl-menu-toggle').length) {
            $('.wdk-footer-menu .wdk_mobile_footer_menu_gumb-open').addClass('trigger')
            $('.wdk-footer-menu .wdk_mobile_footer_menu_gumb-open').on('click', function(e){
                e.preventDefault();
                $('.wl-menu-toggle').trigger('click');
            });
        }
    });

})(jQuery);

/* slider for result listings thumbnails */
if (typeof wdk_result_listings_thumbnail_slider != 'function') {
    var wdk_result_listings_thumbnail_slider = ($wrapper = 'body') => {
        if(typeof jQuery.fn.slick == 'function') {
            jQuery('.wdk_js_gallery_slider_box', $wrapper).each(function(){
                var _this = jQuery(this); 

                if(_this.closest('.slick-slide').hasClass('slick-cloned')){return true;}

                if(_this.find('.wdk_js_gallery_slider').hasClass('slick-initialized')) {
                    _this.find('.wdk_js_gallery_slider').slick("unslick");
                }

                _this.find('.wdk_js_gallery_slider').slick({
                    dots: true,
                    infinite: true,
                    speed: 500,
                    fade: true,
                    arrows: true,
                    cssEase: 'linear',
                    nextArrow: _this.find('.wdk_js_gallery_slider-carousel_arrows .wdk-slider-next'),
                    prevArrow: _this.find('.wdk_js_gallery_slider-carousel_arrows .wdk-slider-prev'),
                });
            });
        }
    };
}
/*
    Fix for slick slider, event after init slick
    
    @param object el slick object
    @param function callback
*/

if (typeof wdk_slick_slider_init != 'function') {
var wdk_slick_slider_init = (el, callback) => {
    try {
        el.slick("slickGoTo", 0, true);
    }
    catch(error) {
        setTimeout(wdk_slick_slider_init, 1000);
        return;
    }
    
    if(callback) {
        callback.call();
    }
};
}
if (typeof wdk_pad != 'function') {
var wdk_pad = (num = '') => {
    return ('00'+num).slice(-2);
};
}

if (typeof wdk_date_add_hours != 'function') {
var wdk_date_add_hours = (selector = '') => {
	if(typeof selector !='undefined' && selector.parent().find('[name="hours_mask"]').length) {
		var datetime = wdk_date_notime_sql_normalize(selector.val(), selector);
		if(selector.parent().find('[name="hours_mask"]').val() !='') {
			datetime += ' '+wdk_pad(selector.parent().find('[name="hours_mask"]').val());
		} else {
			datetime += ' 00';
		}
		if(selector.parent().find('[name="minutes_mask"]').val() !='') {
			datetime += ':'+wdk_pad(selector.parent().find('[name="minutes_mask"]').val());
		} else {
			datetime += ':00';
		}
		datetime += ':00';
		selector.parent().find('.db-date').val(datetime);
	}
};
}
/*
    Convert date to sql format

    @param string $date, string of date

    @return string normalize date or Current date/time
*/
if (typeof wdk_date_sql_normalize != 'function') {
var wdk_date_sql_normalize = (date = '', datepicker_el = null) => {
    if(date == '') {
		var d = new Date();
	} else {
		var d = new Date(date);
	}

	if(d == 'Invalid Date' && datepicker_el) {
		var d = new Date(+(jQuery.datepicker.formatDate("@", datepicker_el.datepicker("getDate"))));
	} 
	
	d = d.getUTCFullYear()        + '-' +
	wdk_pad(d.getMonth() + 1)  + '-' +
	wdk_pad(d.getDate())          + ' ' +
	wdk_pad(d.getHours())         + ':' +
	wdk_pad(d.getMinutes())       + ':' +
	wdk_pad(d.getSeconds());
	return d;
};
}

if (typeof wdk_date_notime_sql_normalize != 'function') {
var wdk_date_notime_sql_normalize = (date = '', datepicker_el = null) => {
    if(date == '') {
		var d = new Date();
	} else {
		var d = new Date(date);
	}

	if(d == 'Invalid Date' && datepicker_el) {
		var d = new Date(+(jQuery.datepicker.formatDate("@", datepicker_el.datepicker("getDate"))));
	} 
	
	d = d.getUTCFullYear()        + '-' +
	wdk_pad(d.getMonth() + 1)  + '-' +
	wdk_pad(d.getDate());
	return d;
};
}

if (typeof wdk_generate_marker_ajax_popup != 'function') {
var wdk_generate_marker_ajax_popup = (ajax_url, listing_post_id, lat, lng,innerMarker, wdk_jpopup_customOptions, auto = false, clusters_enabled = true) => {

    if(auto) {
        var marker = L.marker(
            [lat, lng],
            {icon: L.divIcon({
                    html: innerMarker,
                    className: 'open_steet_map_marker',
                    iconSize: 'auto',
                })
            }
        );
    } else {
        var marker = L.marker(
            [lat, lng],
            {icon: L.divIcon({
                    html: innerMarker,
                    className: 'open_steet_map_marker',
                    iconSize: [40, 60],
                    popupAnchor: [-1, -35],
                    iconAnchor: [25, 60],
                })
            }
        );
    }

    var data = {
        "action": 'wdk_public_action',
        "page": 'wdk_frontendajax',
        "function": 'map_infowindow',
        "listing_post_id": listing_post_id
      };
  
    let favorite_init = false;
    let compare_init = false;
    marker.bindPopup(function () {
        var content = '<div class="infobox"><div class="map_infowindow"><div class="loading_content animated-background"><div class="box_line m170"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div></div></div></div>';
        marker.getPopup().setContent(content);
        marker.getPopup().update();
        jQuery.ajax({
            url : ajax_url,
            type : "POST",
            data: data,
            success: function (data) {
                marker.getPopup().setContent(data.popup_content);
                marker.getPopup().update();
                if (!favorite_init && typeof wdk_favorite == 'function')
                    wdk_favorite('.infobox');
                
                favorite_init = false;

                if (!compare_init && typeof wdk_init_compare_elem == 'function')
                    wdk_init_compare_elem();
                
                compare_init = false;
            },
        });
        return content;
    }, wdk_jpopup_customOptions);

    if (typeof wdk_favorite == 'function')
        marker.on('popupopen', function (popup) {
            if (!favorite_init)
                wdk_favorite('.infobox');
        });

    if (typeof wdk_init_compare_elem == 'function')
        marker.on('popupopen', function (popup) {
            if (!compare_init)
                wdk_init_compare_elem();
        });

    if (typeof wdk_favorite == 'function')
        marker.on('popupclose', function (popup) {
            marker.getPopup().setContent(jQuery('.leaflet-popup-content-wrapper .leaflet-popup-content').html());
            marker.getPopup().update();
        });

    if(clusters_enabled) {
        wdk_clusters.addLayer(marker);
    } else {
        wdk_map.addLayer(marker);
    }
    return marker;
}
}

if (typeof wdk_generate_marker_basic_popup != 'function') {
var wdk_generate_marker_basic_popup = (lat, lng, innerMarker, wdk_jpopup_content, wdk_jpopup_customOptions) => {
    var marker = L.marker(
        [lat, lng],
        {icon: L.divIcon({
                html: innerMarker,
                className: 'open_steet_map_marker',
                iconSize: [40, 60],
                popupAnchor: [-1, -35],
                iconAnchor: [25, 60],
            })
        }
    );
    
    marker.bindPopup(wdk_jpopup_content, wdk_jpopup_customOptions);

    return marker;
}
}

if (typeof wdk_generate_marker_nopopup != 'function') {
var wdk_generate_marker_nopopup = (lat, lng,innerMarker) => {
    var marker = L.marker(
        [lat, lng],
        {icon: L.divIcon({
                html: innerMarker,
                className: 'open_steet_map_marker',
                iconSize: [40, 60],
                popupAnchor: [-1, -35],
                iconAnchor: [25, 60],
            })
        }
    );

    return marker;
}
}
