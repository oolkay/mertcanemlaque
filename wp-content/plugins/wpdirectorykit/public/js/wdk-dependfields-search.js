jQuery(document).ready(function($){
    wdk_dependfields_search_form();
});
const wdk_dependfields_search_form = ($selector = '.wdk-search', parent_field_selector = '.wdk-field') => {
    var $ = jQuery;
    var el_category,el_form, class_hide, hide_fields;
    class_hide = 'wdk-depend-hidden';
    el_form = jQuery($selector);
    el_category = el_form.find('*[name="search_category"]');
 
    el_category.on('change', function(){
        hide_fields();
    });

    hide_fields = () => {
        var value = el_category.val();
        el_form.find('.'+class_hide).removeClass(class_hide)
        if(value && typeof script_dependfields_search.hidden_fields[value] != 'undefined') {
            el_form.find('*[name *="field_"]').each(function(){
                var field_id = $(this).attr('name').substr(6);
                if(script_dependfields_search.hidden_fields[value].indexOf(','+field_id+',') != -1) {
                    $(this).closest(parent_field_selector).addClass(class_hide);
                } 
            });
        } 
    };

    /* init hide fields */
    hide_fields();

}