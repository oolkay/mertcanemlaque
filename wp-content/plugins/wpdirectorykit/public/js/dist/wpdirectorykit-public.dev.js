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

  $('.wdk-click-load-animation').on('click', function () {
    $(this).find('i.fa-ajax-indicator').css('display', 'block');
  });
})(jQuery);

var wdk_generate_marker = function wdk_generate_marker(ajax_url, listing_post_id, lat, lng, innerMarker, wdk_jpopup_customOptions) {
  var marker = L.marker([lat, lng], {
    icon: L.divIcon({
      html: innerMarker,
      className: 'open_steet_map_marker',
      iconSize: [40, 60],
      popupAnchor: [-1, -35],
      iconAnchor: [25, 60]
    })
  });
  var data = {
    "action": 'wdk_public_action',
    "page": 'wdk_frontendajax',
    "function": 'map_infowindow',
    "listing_post_id": listing_post_id
  };
  marker.bindPopup(function () {
    var content = '<div class="infobox"><div class="map_infowindow"><div class="loading_content animated-background"><div class="box_line m170"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div></div></div></div>';
    marker.getPopup().setContent(content);
    marker.getPopup().update();
    jQuery.ajax({
      url: ajax_url,
      type: "POST",
      data: data,
      success: function success(data) {
        marker.getPopup().setContent(data.popup_content);
        marker.getPopup().update();
      }
    });
    return content;
  }, wdk_jpopup_customOptions); //marker.bindPopup(popup_content, wdk_jpopup_customOptions);

  wdk_clusters.addLayer(marker);
  return marker;
};