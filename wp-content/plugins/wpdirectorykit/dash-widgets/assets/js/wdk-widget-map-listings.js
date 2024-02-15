const wdk_dash_widget_generate_marker_ajax_popup = (ajax_url, listing_post_id, lat, lng,innerMarker, wdk_jpopup_customOptions) => {
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

    var data = {
        "action": 'wdk_public_action',
        "page": 'wdk_frontendajax',
        "function": 'map_infowindow_dash',
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

    wdk_clusters.addLayer(marker);
    return marker;
}

const wdk_dash_widget_generate_marker_basic_popup = (lat, lng, innerMarker, wdk_jpopup_content, wdk_jpopup_customOptions) => {
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

    wdk_clusters.addLayer(marker);
    return marker;
}

const wdk_dash_widget_generate_marker_nopopup = (lat, lng,innerMarker) => {
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

    wdk_clusters.addLayer(marker);
    return marker;
}