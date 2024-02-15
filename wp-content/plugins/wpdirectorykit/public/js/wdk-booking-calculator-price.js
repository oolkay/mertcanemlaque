jQuery(document).ready(function($){
   
    $('.wdk_booking_auto_calculate_price').each(function(){
        var form = $(this).closest('form');
        form.find('input[name="date_to"],.fees_checkbox,select[name="guests_number_adults"],select[name="guests_number_childs"]').on('change', function(){
            wdk_booking_price_calculate(form); 
        });
    });
});
var wdk_booking_price_calculate_jqxhr = null;
const wdk_booking_price_calculate = (form) => {
    if(typeof form == 'undefined') {
        return false;
    }
    var price_place = form.find('.wdk_booking_auto_calculate_price');
    
    price_place.html('<div class="wdk_alert wdk_alert-info">'+wdk_booking_script_parameters.text.loading+'</div>');
    if(form.find('[name="date_from"]').val() =='' || form.find('[name="date_to"]').val() =='' || form.find('[name="listing_id"]').val() =='') {
        price_place.html('');
        return false;
    }
    var data = {
        "action": 'wdk_public_action',
        "page": 'wdk_frontendajax',
        "function": 'booking_price_calculate',
    };
    data['post_id'] = form.find('[name="listing_id"]').val();
    data['date_from'] = form.find('[name="date_from"]').val();
    data['date_to'] = form.find('[name="date_to"]').val() ;
    data['guests'] = +(form.find('[name="guests_number_adults"] option:selected').val())+(+(form.find('[name="guests_number_childs"] option:selected').val()));

    form.find('.fees_checkbox:not([disabled])').each(function(){
        if(jQuery(this).prop('checked')) {
            data[jQuery(this).attr('name')] = 1 ;
        } else {
            data[jQuery(this).attr('name')] = 0 ;
        }
    });

    // Assign handlers immediately after making the request,
    // and remember the jqxhr object for this request
    if (wdk_booking_price_calculate_jqxhr != null)
        wdk_booking_price_calculate_jqxhr.abort();

    wdk_booking_price_calculate_jqxhr = jQuery.post(wdk_booking_script_parameters.ajax_url, data, function(data) {

        if(data.popup_text_success)
            wdk_log_notify(data.popup_text_success);
            
        if(data.popup_text_error)
            wdk_log_notify(data.popup_text_error, 'error');
              
        if(data.success)
        {
            var html = '';
            html += "<table class='list_booking_price'>"

            var price_symbol_prefix = '', 
            price_symbol_suffix = ''; 
            
            if(data.results.symbol == '$' || data.results.symbol == '&#36;') {
                price_symbol_prefix = data.results.symbol;
            } else {
                price_symbol_suffix = data.results.symbol; 
            }

            html += "<tr class='price'><th class='title'>"+wdk_booking_script_parameters.text.price+"</th> <td class='value'>"+price_symbol_prefix+' '+data.results.price+' '+price_symbol_suffix+"</td></tr>";

            jQuery.each(data.results.fees, function(k,v){
                html += "<tr class='fee'><th class='title'>"+k+"</th> <td class='value'>"+price_symbol_prefix+' '+v+' '+price_symbol_suffix+"</td></tr>";
            });
            html += "<tr class='total_price'><th class='title'>"+wdk_booking_script_parameters.text.total_price+"</th> <td class='value'>"+price_symbol_prefix+' '+data.results.total+' '+price_symbol_suffix+"</td></tr>";
            html += "</table>"

            price_place.html(html);
        } else {
            price_place.html('');
        }
       
    })
    .done(function () {

    })
    .fail(function() {

    });
}