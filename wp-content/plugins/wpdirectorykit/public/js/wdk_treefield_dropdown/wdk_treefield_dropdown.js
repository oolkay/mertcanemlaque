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
        wdk_treefield_dropdown();
    });

})(jQuery);

const wdk_treefield_dropdown = (hide_select = false) => {
        var $ = jQuery,
        jqxhr = null;
        
        var events =() => {
            $('.wdk_treefield_dropdown select').off().on('input', function(e){
                var _this = $(this),
                this_val = _this.val();
                parent = _this.closest('.wdk_treefield_dropdown'),
                field = parent.data('field');

                if(this_val != '') {
                    $('input[name="'+field+'"]').val(this_val);
                } else if(this_val== '') {
                    if(parent.data('level') == '0') {
                        $('input[name="'+field+'"]').val('');
                    } else {
                        $('input[name="'+field+'"]').val($('.wdk_treefield_dropdown[data-field="'+field+'"][data-level="'+(+parent.data('level')-1)+'"] select option:selected').attr('value'));
                    }   
                }
                $('input[name="'+field+'"]').trigger('input')
                generate_fields(_this);
            });
        }

        events();

        var generate_fields = (field) => {
            var _this = field,
            this_val = _this.val(),
            parent = _this.closest('.wdk_treefield_dropdown'),
            field = parent.data('field');
            _this.addClass('sel_class')

            if(hide_select) {
                parent.nextAll('.wdk_treefield_dropdown[data-field="'+field+'"]').remove();
            } else {
                parent.nextAll('.wdk_treefield_dropdown[data-field="'+field+'"]').find('select').val('').removeClass('sel_class').find('option:not([value=""])').remove();
            }

            if(this_val != '') {
                
                // Assign handlers immediately after making the request,
                // and remember the jqxhr object for this request
                if(jqxhr != null) {
                    jqxhr.abort();
                    $('.wdk_treefield_dropdown[data-field="'+field+'"]').removeClass('wdk_loading');
                }
                parent.addClass('wdk_loading');

                var data = {
                    "action": 'wdk_public_action',
                    "page": 'wdk_frontendajax',
                    "function": 'wdk_tree_dropdowns',
                    "table": field,
                    "id": this_val
                };

                jqxhr = jQuery.post( script_parameters.ajax_url, data, function(data) {
                    if(data.success && data.results.length) {
                        var $field_group = $('.wdk_treefield_dropdown[data-field="'+field+'"][data-level="0"]').clone()
                        $field_group.attr('data-level',(+parent.data('level')+1)).removeClass('wdk_loading');

                        var html = '<select name="'+field+'_'+(+parent.data('level')+1)+'" class="wdk-control">';
                        var html_option = '';
                        $.each( data.results, function( key, row ) {
                            html += '<option value="'+row.id+'">'+row.text+'</option>';
                            html_option += '<option value="'+row.id+'">'+row.text+'</option>';
                        });

                        html += '</select>';
                        if(hide_select) {
                            $field_group.find('.wdk-field-group').html(html);
                            $field_group.insertAfter(parent);
                        } else {
                            parent.next('.wdk_treefield_dropdown[data-field="'+field+'"]').find('select').html(html_option);
                        }

                    }
                }).always(function(){
                    events();
                    $('.wdk_treefield_dropdown[data-field="'+field+'"]').removeClass('wdk_loading');
                })
                .fail(function() {
                    //alert( "error" ); 
                    console.log( "abort" );
                });
        
            } else if(this_val== '') {
               
            }
        }
};