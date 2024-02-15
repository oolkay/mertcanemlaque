"use strict";

(function ($) {
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

  $('.wdk-order').on('change', function () {
    var curr_url = wdk_removeParam('order_by', document.location.toString());

    if (curr_url.indexOf('#') == -1) {// Fine
    } else {
      // Remove this part related
      curr_url = curr_url.substr(0, curr_url.indexOf('#'));
    }

    var del_char = '&';
    if (curr_url.indexOf('?') == -1) del_char = '?';
    document.location = curr_url + del_char + 'order_by=' + $(this).val() + '#results';
  });
  $('.wmvc-view-type a').on('click', function (e) {
    e.preventDefault();
    var curr_url = wdk_removeParam('wmvc_view_type', document.location.toString());

    if (curr_url.indexOf('#') == -1) {// Fine
    } else {
      // Remove this part related
      curr_url = curr_url.substr(0, curr_url.indexOf('#'));
    }

    var del_char = '&';
    if (curr_url.indexOf('?') == -1) del_char = '?';
    document.location = curr_url + del_char + 'wmvc_view_type=' + $(this).attr('data-id') + '#results';
  });
})(jQuery);

function wdk_removeParam(key, sourceURL) {
  var rtn = sourceURL.split("?")[0],
      param,
      params_arr = [],
      queryString = sourceURL.indexOf("?") !== -1 ? sourceURL.split("?")[1] : "";

  if (queryString !== "") {
    params_arr = queryString.split("&");

    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
      param = params_arr[i].split("=")[0];

      if (param === key) {
        params_arr.splice(i, 1);
      }
    }

    if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
  }

  return rtn;
}

function wdk_splitUrl() {
  var vars = [],
      hash;
  var url = document.URL.split('?')[0];
  var p = document.URL.split('?')[1];

  if (p != undefined) {
    p = p.split('&');

    for (var i = 0; i < p.length; i++) {
      hash = p[i].split('=');
      vars.push(hash[1]);
      vars[hash[0]] = hash[1];
    }
  }

  vars['url'] = url;
  return vars;
}

;