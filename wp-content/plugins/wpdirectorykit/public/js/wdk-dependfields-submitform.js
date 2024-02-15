jQuery(document).ready(function($){
    wdk_dependfields_submit_form('form.form_listing');
    wdk_dependfields_submit_form_query('form.form_listing');
});

const wdk_dependfields_submit_form = ($selector = null, parent_field_selector = '.wdk-field-edit') => {
    var $ = jQuery;
    var el_category,el_form, class_hide, hide_fields;
    class_hide = 'wdk-depend-hidden';
    el_form = jQuery($selector);
    el_category = el_form.find('*[name="category_id"]');

    el_category.on('input', function(){
        hide_fields(el_category);
    });

    hide_fields = (el) => {
        var value = (jQuery(el).parent().hasClass('wdk_multi_treefield_dropdown_container')) ? jQuery(el).val() : el_category.find('option:selected').attr('value');
 
        el_form.find('.'+class_hide).removeClass(class_hide)
        if(value && typeof script_dependfields_submitform.hidden_fields[value] != 'undefined') {
            el_form.find('*[name *="field_"]').each(function(){
                var field_id = $(this).attr('name').substr(6);
                if(script_dependfields_submitform.hidden_fields[value].indexOf(','+field_id+',') != -1) {
                    $(this).closest(parent_field_selector).addClass(class_hide);
                } 
            });
        } 
    };

    /* init hide fields */
    hide_fields(el_category);

}

/* depend fields, remove required and data from hidden based on depend fields */
const wdk_dependfields_submit_form_query = ($selector = null, parent_field_selector = '.wdk-field-edit') => {
    var $ = jQuery;
    var el_form, class_hide, hide_fields;
    class_hide = 'wdk-depend-hidden';
    el_form = jQuery($selector);
   
    el_form.on('submit', function(e){
        el_form.find('.'+class_hide).find('input:not([type="checkbox"]):not([name="element_id"]):not([type="radio"]):not([type="hidden"]),textarea,select').val('').removeAttr('required');
        el_form.find('.'+class_hide).find('select').val(jQuery(this).find('option:first').val()).removeAttr('required'); 
        el_form.find('.'+class_hide).find('input[type="checkbox"]').prop('checked', false).removeAttr('required'); 

    })
}