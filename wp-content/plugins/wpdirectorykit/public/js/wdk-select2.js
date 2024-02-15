jQuery(document).ready(function($){
    wdk_select_init();
    /* elementor popup */
    jQuery( document ).on( 'elementor/popup/show', () => {
        $('.select2-hidden-accessible', '.elementor-popup-modal').each(function () { 
            $(this).removeClass('select2-hidden-accessible').next('.select2').remove();
        });
        
        wdk_select_init('.elementor-popup-modal');
    } );
});


const wdk_select_init = ($wrapper = 'body') => {
    var $ = jQuery;

    /* Start select2
    *  Site https://select2.org/
    */
    if (typeof $.fn.select2 == 'function') {
        //Set Dropdown with SearchBox via dropdownAdapter option (https://stackoverflow.com/questions/35799854/how-to-add-selectall-using-dropdownadapter-on-select2-v4)
        var Utils = $.fn.select2.amd.require('select2/utils');
        var Dropdown = $.fn.select2.amd.require('select2/dropdown');
        var DropdownSearch = $.fn.select2.amd.require('select2/dropdown/search');
        var CloseOnSelect = $.fn.select2.amd.require('select2/dropdown/closeOnSelect');
        var AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');

        var dropdownAdapter = Utils.Decorate(Utils.Decorate(Utils.Decorate(Dropdown, DropdownSearch), CloseOnSelect), AttachBody);

        $('.select_ajax', $wrapper).each(function () { 
            var self = $(this);
            let ajax_url = $(this).attr('data-ajax');
            var data = {
                "action": 'wdk_public_action',
                "page": 'wdk_frontendajax',
                "function": 'select_2_ajax',
            }; 

            if(self.hasClass('select2-hidden-accessible')) return true;

            var $select = $(this).select2({
                placeholder: $(this).attr('data-placeholder') || '',
                maximumSelectionLength: parseInt($(this).attr('data-limit')) || 10,
                dropdownAdapter: dropdownAdapter,
                minimumResultsForSearch: 1,
                ajax: {
                    url: ajax_url,
                    dataType: 'json',
                    data: data,
                    type: "POST",
                    quietMillis: 10,

                    data: function (term, page) { // page is the one-based page number tracked by Select2
                        return {
                            q: term, //search term
                            "page_result":  term.page || 1,
                            "action": 'wdk_public_action',
                            "page": 'wdk_frontendajax',
                            "function": 'select_2_ajax',
                            "table": $(this).attr('data-table') || '',
                        };
                    },
                    results: function (data, page) {
                        var more = (page * 30) < data.total_count; // whether or not there are more results available
        
                        // notice we return the value of more so Select2 knows if more results can be loaded
                        return { results: data.items, more: more };
                    }
                },
                templateResult: wdk_select_ajax_formatRepo,
                templateSelection: wdk_select_ajax_templateSelection
            });
            $select.data('select2').$dropdown.addClass('select_multi_dropdown_tree');
        }).on('select2:open', function (e) {
            // Do something
            $('.select_ajax').find('option[value=""]').remove();
        });
       

        $('.select_ajax_user', $wrapper).each(function () { 
            var self = $(this);
            let ajax_url = $(this).attr('data-ajax');
            var data = {
                "action": 'wdk_public_action',
                "page": 'wdk_frontendajax',
                "function": 'select_2_ajax_user',
            }; 

            if(self.hasClass('select2-hidden-accessible')) return true;

            var $select =  $(this).select2({
                multiple: true,
                placeholder: $(this).attr('data-placeholder') || '',
                maximumSelectionLength: 10,
                dropdownAdapter: dropdownAdapter,
                minimumResultsForSearch: 1,
                ajax: {
                    url: ajax_url,
                    dataType: 'json',
                    data: data,
                    type: "POST",
                    quietMillis: 10,
                 
                    data: function (term, page) { // page is the one-based page number tracked by Select2
                        return {
                            q: term, //search term
                            "page_result":  term.page || 1,
                            "action": 'wdk_public_action',
                            "page": 'wdk_frontendajax',
                            "function": 'select_2_ajax_user',
                        };
                    },
                    results: function (data, page) {
                        var more = (page * 30) < data.total_count; // whether or not there are more results available
        
                        // notice we return the value of more so Select2 knows if more results can be loaded
                        return { results: data.items, more: more };
                    }
                },
                templateResult: wdk_select_ajax_formatRepo,
                templateSelection: wdk_select_ajax_templateSelection
            }).on('select2:opening select2:closing', function (event) {
                //Disable original search (https://select2.org/searching#multi-select)
                var searchfield = $(this).parent().find('.select2-search__field');
                searchfield.prop('disabled', true);
            });

            $select.data('select2').$dropdown.addClass('select_multi_dropdown_users');
        });

        $('.select_multi', $wrapper).each(function () { 
            var self = $(this);
            if(self.hasClass('select2-hidden-accessible')) return true;

            var $select = $(this).select2({
                closeOnSelect:false,
                placeholder: $(this).attr('data-placeholder') || '',
                maximumSelectionLength: parseInt($(this).attr('data-limit')) || 10,
                dropdownAdapter: dropdownAdapter,
                minimumResultsForSearch: 1,
                maximumSelectionLength: +($(this).attr('data-maxselectlimit')) || 10,
                templateResult: wdk_select_ajax_formatRepo,
                templateSelection: wdk_select_ajax_templateSelection
            });

            $select.data('select2').$dropdown.addClass('select_multi_dropdown');
        });

    }

    function wdk_select_ajax_formatRepo (repo) {

        if (repo.loading) {
            return repo.text;
          }
        
          var $container = $(
            "<span>"+repo.text+"</span>"
          );
        
          return $container;
        
      }
    function wdk_select_ajax_templateSelection (repo) {

        if (repo.loading) {
            return repo.text;
          }
        
          var $container = $(
            "<span>"+repo.text+"</span>"
          );
        
          return $container;
        
      }
}

