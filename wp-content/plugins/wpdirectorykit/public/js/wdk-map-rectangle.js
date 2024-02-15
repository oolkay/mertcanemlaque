const wdk_map_draw = (wdk_map, options = null) => {
    var $ = jQuery,
        map = wdk_map,
        editableLayers, drawPluginOptions, drawControl, removeButtonControl;

    const event_init = () => {
        editableLayers = new L.FeatureGroup();
        map.addLayer(editableLayers);

        drawPluginOptions = {
            position: 'topleft',
            draw: {
                drag: true,
                polyline: false,
                polygon: false,
                circle: false,
                rectangle: {
                    shapeOptions: {
                        clickable: false,
                        color: '#ff7800',
                        weight: 1,
                    },
                    showRadius: true,
                },
                circlemarker: false,
                marker: false,
                showArea: true,
                showLength: true
            }
        };

        drawPluginOptions = $.extend(drawPluginOptions, options);

        drawControl = new L.Control.Draw(drawPluginOptions);
        map.addControl(drawControl);

        // Create a custom button for removing all rectangles
        const RemoveRectanglesButton = L.Control.extend({
            options: {
                position: drawPluginOptions.position
            },
            onAdd: function () {
                var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom leaflet-draw-toolbar');
                container.innerHTML = '<a class="leaflet-draw-draw-rectangle-remove" href="#"></a>';
                container.style.display = 'none';

                container.onclick = function () {
                    removeAllRectangles();
                };

                return container;
            }
        });

        // Add the custom button to the map
        removeButtonControl = new RemoveRectanglesButton();
        map.addControl(removeButtonControl);

        map.on(L.Draw.Event.CREATED, function (e) {
            var type = e.layerType,
                layer = e.layer;

            if (type === 'rectangle') {
                setRectangle(layer, false);

                // Get coordinates of the polygon
                var coordinates = layer.getLatLngs();
                if(coordinates) {
                    $('.wdk-search-form').find('input[name="rectangle_ne"]').val(coordinates[0][1].lat+','+coordinates[0][1].lng);
                    $('.wdk-search-form').find('input[name="rectangle_sw"]').val(coordinates[0][3].lat+','+coordinates[0][3].lng);
                }
            }

            editableLayers.addLayer(layer);
        });

        map.on('draw:drawstart', function (e) {
            editableLayers.clearLayers();
        });
    };

    const setRectangle = (layer, fitBounds = true) => {
        editableLayers.addLayer(layer);

        // Set the zoom level
        if(fitBounds) {
            let zoom_map = map.getZoom();
            map.fitBounds(layer.getBounds());
            map.setZoom(zoom_map);
        }

        removeButtonControl.getContainer().style.display = 'block';
    };

    const removeAllRectangles = () => {
        editableLayers.clearLayers();
        removeButtonControl.getContainer().style.display = 'none';
    };

    // FeatureGroup is to store editable layers
    event_init();

    return {
        setRectangle: setRectangle,
        removeAllRectangles: removeAllRectangles
    };
};
