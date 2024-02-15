
(function ($) {
	'use strict';
	jQuery(document).ready(function ($) {
		wdk_dependfields_update('form.wdk-depend-fields');

		$('.wdk_copy_on_subcategories').on('click', function(e){
			e.preventDefault();
			wdk_copy_on_subcategories($(this).attr('data-category'), this, $(this).attr('data-wpnonce'));
		});
	});
  })(jQuery);
  
  var wdk_dependfields_update_jqxhr = null;
  const wdk_dependfields_update = ($selector = null) => {
	if(!$selector){
		console.log('form not detected')
		return false;
	}

	
	jQuery($selector).find('input.trigger_section').on('change', function(){
		var that = jQuery(this);
		if(that.prop('checked')) {
			that.closest('.wdk-col-section').nextAll().each(function(){
				if(jQuery(this).hasClass('wdk-col-section'))
					return false;

				jQuery(this).find('input:not(.trigger_section)').prop('checked', true);
			});
		} else {
			that.closest('.wdk-col-section').nextAll().each(function(){
				if(jQuery(this).hasClass('wdk-col-section'))
					return false;

				jQuery(this).find('input:not(.trigger_section)').prop('checked', false);
			});
		}

		var nextEl = that.closest('.wdk-col-section').next();
		if(!nextEl.hasClass('wdk-col-section')) {
			nextEl.find('input').trigger('change');
		}
	});

	jQuery($selector).find('.wdk-col-section').each(function(){
		var that = jQuery(this);
		var checked = true;
		that.nextAll().each(function(){
			if(jQuery(this).hasClass('wdk-col-section'))
				return false;

			if(!jQuery(this).find('input:not(.trigger_section)').prop('checked')) {
				checked = false;
				return false;
			}
		});	
		if(checked) {
			that.find('input.trigger_section').prop('checked', true);
		} else {
			that.find('input.trigger_section').prop('checked', false);
		}
	});

	jQuery($selector).find('input:not(.trigger_section)').on('change', function(){
		var _this = jQuery(this);
		var this_form = _this.closest('form');
		var loading_el = _this.closest('.wdk_field');
		
		//var data = this_form.serializeArray();
		var data = new Array();
		data.push({ name: 'action', value: "wdk_admin_action" });
		data.push({ name: 'page', value: "wdk_backendajax" });
		data.push({ name: 'function', value: "update_depend" });
		data.push({ name: '_wpnonce', value: this_form.find('input[name="_wpnonce"]').val() });
		data.push({ name: 'main_field', value: this_form.find('input[name="main_field"]').val() });
		data.push({ name: 'field_id', value: this_form.find('input[name="field_id"]').val() });

		this_form.find('input:not(:checked)').each(function(){
			if(jQuery(this).attr('name').indexOf('field_hide_') != -1)
				data.push({ name: jQuery(this).attr('name'), value: "1" });
		});

   		// Assign handlers immediately after making the request,
		// and remember the jqxhr object for this request
		if (wdk_dependfields_update_jqxhr != null)
			wdk_dependfields_update_jqxhr.abort();

		loading_el.addClass('wdk_btn_load_indicator out');

		wdk_dependfields_update_jqxhr = jQuery.post(script_dependfields_parameters.ajax_url, data, function (data) {
		
			
		}).always(function(data) {
			loading_el.removeClass('wdk_btn_load_indicator out');
        });
	})
  }

  var wdk_copy_on_subcategories_jqxhr = null;
  const wdk_copy_on_subcategories = ($category_id = null, el, $_wpnonce) => {

		if(!$category_id) return false;

		var el = jQuery(el);
		
		//var data = this_form.serializeArray();
		var data = new Array();
		data.push({ name: 'action', value: "wdk_admin_action" });
		data.push({ name: 'page', value: "wdk_backendajax" });
		data.push({ name: 'function', value: "depend_copy_on_subcategories" });
		data.push({ name: 'category_id', value: $category_id });
		data.push({ name: '_wpnonce', value: $_wpnonce });

		// and remember the jqxhr object for this request
		if (wdk_copy_on_subcategories_jqxhr != null)
			wdk_copy_on_subcategories_jqxhr.abort();

		el.addClass('wdk_btn_load_indicator out');
		wdk_copy_on_subcategories_jqxhr = jQuery.post(script_dependfields_parameters.ajax_url, data, function (data) {
			console.log('success copied')
		}).always(function(data) {
			el.removeClass('wdk_btn_load_indicator out');
		});
  }