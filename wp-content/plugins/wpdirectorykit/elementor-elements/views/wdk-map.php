<?php
/**
 * The template for Element Listings Results Map.
 * This is the template that elementor element map with markers of listings, show results
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-map" class="wdk_map_results">
        <?php if($settings['conf_custom_dragging_mobile'] == 'yes'):?> <div class="map-hint"><?php echo esc_html__( 'Require 2 finger for map movement', 'wpdirectorykit' );?></div><?php endif;?>
        <div id="wdk_map_results_<?php echo esc_html($id_element);?>" style="height:<?php echo esc_attr($settings['conf_custom_map_height']['size']);?>px" 
            data-el_id="<?php echo esc_attr($this->get_id());?>" 
            data-el_type="<?php echo esc_attr($this->get_name());?>" 
            <?php
                $post_id = get_the_ID();
                $post_object_id = get_queried_object_id();
                if($post_object_id)
                $post_id = $post_object_id;
                
                global $wdk_listing_page_id;
                if(!empty($wdk_listing_page_id))
                $post_id = $wdk_listing_page_id;
            ?>
            data-el_page_id="<?php echo esc_attr($post_id);?>"

            class="wdk_map_results <?php echo wmvc_show_data('styles_thmbn_des_type',$settings, '');?> 
            <?php if
                (
                    wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_ajax_results') &&
                    isset($settings['is_ajax_enable']) && $settings['is_ajax_enable'] == 'yes'
                ):?>
                    ajax_results_enabled
            <?php endif;?>
            " >
        </div>
    </div>
</div>
<?php
    $zoom_index = $settings['conf_custom_map_zoom_index']['size'];
?>
<?php
    if($lat == 0)
    {
        $lat = wmvc_show_data('conf_custom_map_center_gps_lat', $settings);
        $lng = wmvc_show_data('conf_custom_map_center_gps_lng', $settings);
    }
?>
<?php
$WMVC = &wdk_get_instance();
$WMVC->model('category_m');
if (!$is_edit_mode)
    ob_start();
?>
 <script>
    var wdk_map ='';
    var wdk_markers = [];
    var wdk_clusters ='';
    var wdk_jpopup_customOptions =
    {
        'maxWidth': 'initial',
        'width': 'initial',
        'className' : 'popupCustom'
    };
    jQuery(document).ready(function($) {
        if(wdk_clusters=='')
            wdk_clusters = L.markerClusterGroup({spiderfyOnMaxZoom: true, showCoverageOnHover: false, zoomToBoundsOnClick: true});
            wdk_map = L.map('wdk_map_results_<?php echo esc_html($id_element);?>', {
                center: ["<?php echo esc_js($lat);?>","<?php echo esc_js($lng);?>"],
                zoom: "<?php echo esc_js($zoom_index);?>",
                scrollWheelZoom: false,
                dragging: ((!L.Browser.mobile) ? <?php if($settings['conf_custom_dragging'] == 'yes'):?> true <?php else:?> else <?php endif;?> : <?php if($settings['conf_custom_dragging_mobile'] == 'yes'):?> true <?php else:?> false <?php endif;?>),
                tap: !L.Browser.mobile,
                fullscreenControl: true,
                fullscreenControlOptions: {
                    position: 'topleft'
                },
            });     

        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(wdk_map);

        let drawMap = wdk_map_draw(wdk_map);

        // define rectangle geographical bounds
        <?php if(isset($_GET['rectangle_ne']) && isset($_GET['rectangle_sw'])) :?>
            <?php if(wdk_is_gps($_GET['rectangle_ne']) && wdk_is_gps($_GET['rectangle_sw']))  :?>
                var bounds = [[<?php echo isset($_GET['rectangle_ne']) ? esc_attr(sanitize_text_field($_GET['rectangle_ne'])) : '';?>], [<?php echo isset($_GET['rectangle_sw']) ? esc_attr(sanitize_text_field($_GET['rectangle_sw'])) : '';?>]];
                drawMap.setRectangle(L.rectangle(bounds, {color: "#ff7800", weight: 1}));
            <?php endif;?>
        <?php endif;?>

        <?php if(!empty($settings['conf_custom_map_style']) && $settings['conf_custom_map_style'] =='custom' && !empty($settings['conf_custom_map_style_self'])):?>
            var positron = L.tileLayer('<?php echo esc_js($settings['conf_custom_map_style_self']);?>').addTo(wdk_map);
        <?php elseif(!empty($settings['conf_custom_map_style']) && $settings['conf_custom_map_style'] !='custom'):?>
            <?php if($settings['conf_custom_map_style']=='google_map'):?>
                <?php
                    wp_enqueue_script('google-map-api', 'https://maps.googleapis.com/maps/api/js?key='.wmvc_show_data('google_map_key', $settings, ''));
                ?>
                var roadMutant = L.gridLayer
                    .googleMutant({
                        type: "roadmap",
                    });

                var satMutant = L.gridLayer.googleMutant({
                    type: "satellite",
                });

                var terrainMutant = L.gridLayer.googleMutant({
                    type: "terrain",
                });

                var hybridMutant = L.gridLayer.googleMutant({
                    type: "hybrid",
                });

                var styleMutant = L.gridLayer.googleMutant({
                    styles: [
                        { elementType: "labels", stylers: [{ visibility: "off" }] },
                        { featureType: "water", stylers: [{ color: "#444444" }] },
                        { featureType: "landscape", stylers: [{ color: "#eeeeee" }] },
                        { featureType: "road", stylers: [{ visibility: "off" }] },
                        { featureType: "poi", stylers: [{ visibility: "off" }] },
                        { featureType: "transit", stylers: [{ visibility: "off" }] },
                        { featureType: "administrative", stylers: [{ visibility: "off" }] },
                        {
                            featureType: "administrative.locality",
                            stylers: [{ visibility: "off" }],
                        },
                    ],
                    maxZoom: 24,
                    type: "roadmap",
                });

                var trafficMutant = L.gridLayer.googleMutant({
                    type: "roadmap",
                });
                trafficMutant.addGoogleLayer("TrafficLayer");

                var transitMutant = L.gridLayer.googleMutant({
                    type: "roadmap",
                });

                var satelliteMutant = L.gridLayer.googleMutant({
                    type: "satellite",
                });
                transitMutant.addGoogleLayer("TransitLayer");

                var google_map_style = roadMutant;
                <?php switch (wmvc_show_data('google_map_default_type', $settings, 'roadMutant')){
                    case 'roadmap':
                            echo 'google_map_style=roadMutant';
                            break;
                    case 'aerial':
                            echo 'google_map_style=satMutant';
                            break;
                    case 'terrain':
                            echo 'google_map_style=terrainMutant';
                            break;
                    case 'hybrid':
                            echo 'google_map_style=hybridMutant';
                            break;
                    case 'satellite':
                            echo 'google_map_style=satelliteMutant';
                            break;
                    case 'styles':
                            echo 'google_map_style=styleMutant';
                            break;
                    case 'traffic':
                            echo 'google_map_style=trafficMutant';
                            break;
                    case 'transit':
                            echo 'google_map_style=transitMutant';
                            break;
                }
                ?>

                google_map_style.addTo(wdk_map);
                L.control.layers(
					{
						Roadmap: roadMutant,
						Aerial: satMutant,
						Terrain: terrainMutant,
						Hybrid: hybridMutant,
						Satellite: satelliteMutant,
						Traffic: trafficMutant,
						Transit: transitMutant,
					}
				).addTo(wdk_map);  

            <?php elseif(in_array($settings['conf_custom_map_style'], array(
                            'https://{s}.tile.thunderforest.com/mobile-atlas/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/transport-dark/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/pioneer/{z}/{x}/{y}.png',
                            'https://{s}.tile.thunderforest.com/neighbourhood/{z}/{x}/{y}.png',
                        ))):?>
                var positron = L.tileLayer('<?php echo esc_js($settings['conf_custom_map_style']);?><?php echo (wmvc_show_data('thunderforest_map_key', $settings, false)) ? '?apikey='.esc_js(wmvc_show_data('thunderforest_map_key', $settings)) : '';?>').addTo(wdk_map);
            <?php elseif(in_array($settings['conf_custom_map_style'], array(
                            'https://{s}.tile.jawg.io/jawg-terrain/{z}/{x}/{y}{r}.png',
                            'https://{s}.tile.jawg.io/jawg-streets/{z}/{x}/{y}{r}.png',
                            'https://{s}.tile.jawg.io/jawg-dark/{z}/{x}/{y}{r}.png',
                            'https://{s}.tile.jawg.io/jawg-light/{z}/{x}/{y}{r}.png',
                        ))):?>
                var positron = L.tileLayer('<?php echo esc_js($settings['conf_custom_map_style']);?><?php echo (wmvc_show_data('jawg_map_key', $settings, false)) ? '?access-token='.esc_js(wmvc_show_data('jawg_map_key', $settings)) : '';?>').addTo(wdk_map);
            <?php else:?>
                var positron = L.tileLayer('<?php echo esc_js($settings['conf_custom_map_style']);?>').addTo(wdk_map);
            <?php endif;?>
        <?php endif;?>

        var auto_marker_size = false;
        <?php foreach($results as $key=>$listing): ?>
        <?php  if(!is_numeric(wmvc_show_data('lng', $listing)))continue;?>
        <?php 
        $pin_icon = "";
        $font_class = "";
        $font_icon = $this->generate_icon($settings['conf_custom_map_pin_icon']);
        $pin_icon = $settings['conf_custom_map_pin']['url'];

        if(!empty(wmvc_show_data('category_id', $listing))){
            $category = $WMVC->category_m->get_data(wmvc_show_data('category_id', $listing));
            if(wmvc_show_data('marker_image_id', $category, false, TRUE, TRUE)){
                $pin_icon = wdk_image_src($category, 'full', NULL,'marker_image_id');
            } else if(!empty(wmvc_show_data('font_icon_code', $category))) {
                $font_class = wmvc_show_data('font_icon_code', $category);
            } 
        } else {
            $font_class = "";
        }

        $listing_lat = $listing_lng = NULL;
        $listing_lat = wmvc_show_data('lat', $listing);
        $listing_lng = wmvc_show_data('lng', $listing);

        if(wmvc_show_data('conf_hide_real_location', $settings) == 'yes') {
            $gps = wdk_get_near_location($listing_lat, $listing_lng);
            //$listing_lat = (wmvc_show_data('lat', $gps));
            //$listing_lng = (wmvc_show_data('lng', $gps));
            
            $listing_lat = wdk_move_gps($listing_lat);
            $listing_lng = wdk_move_gps($listing_lng);
        }

        ?>
        <?php if(!empty($settings['custom_marker_fields']) &&  substr($this->data['settings']['custom_marker_fields'], strpos($this->data['settings']['custom_marker_fields'],'__')+2) == 'first_image'):?>
            auto_marker_size = true;
            var innerMarker = '<div class="wdk_marker-container wdk_marker_label wdk_marker_clear category_id_<?php echo esc_js(wmvc_show_data('category_id', $listing));?>"><img src="<?php echo esc_js(wdk_image_src($listing));?>"></img></div>';

        <?php elseif(!empty($settings['custom_marker_fields']) &&  wdk_field_value (substr($this->data['settings']['custom_marker_fields'], strpos($this->data['settings']['custom_marker_fields'],'__')+2), $listing)):?>
            <?php
                $field_id = substr($this->data['settings']['custom_marker_fields'], strpos($this->data['settings']['custom_marker_fields'],'__')+2); 

                $field_value = '';
                $field_value .= apply_filters( 'wpdirectorykit/listing/field/prefix', wdk_field_option ($field_id, 'prefix'), $field_id);

                /* if price field use like 1l */
                if(wdk_field_option($field_id, 'is_price_format')) {
                    
                    $value = wdk_field_value($field_id, $listing);
                    if($value>=1000) {
                        $value = apply_filters( 'wpdirectorykit/listing/field/value', number_format_i18n(wdk_filter_decimal($value/1000)), $field_id).'k';
                    } else {
                        $value = apply_filters( 'wpdirectorykit/listing/field/value', wdk_field_value_on_type($field_id, $listing), $field_id);
                    }

                    $field_value = $value;

                } else {
                    $field_value .= apply_filters( 'wpdirectorykit/listing/field/value', wdk_field_value_on_type($field_id, $listing), $field_id);
                }

                $field_value .= apply_filters( 'wpdirectorykit/listing/field/suffix',wdk_field_option ($field_id, 'suffix'), $field_id);
            ?>
            auto_marker_size = true;
            var innerMarker = '<div class="wdk_marker-container wdk_marker_label category_id_<?php echo esc_js(wmvc_show_data('category_id', $listing));?>"><?php echo esc_js(strip_tags($field_value));?></div>';
        <?php elseif($pin_icon):?>
            var image = '<?php echo esc_html($pin_icon);?>'; var innerMarker = '<div class="wdk_marker-container wdk_marker-container-image"><img src='+image+'></img></div>';
        <?php elseif($font_icon && empty($font_class)):?> 
            var innerMarker = '<div class="wdk_marker-container category_id_<?php echo esc_js(wmvc_show_data('category_id', $listing));?>"><div class="front wdk_face"><?php echo wdk_viewe($font_icon);?></div><div class="wdk_marker-card"><div class="wdk_marker-arrow"></div></div></div>';
        <?php else:?> 
            var innerMarker = '<div class="wdk_marker-container category_id_<?php echo esc_js(wmvc_show_data('category_id', $listing));?>"><div class="front wdk_face"><i class="<?php echo esc_html($font_class);?>"></i></div><div class="wdk_marker-card"><div class="wdk_marker-arrow"></div></div></div>';
        <?php endif;?>
        
        wdk_markers.push(wdk_generate_marker_ajax_popup('<?php echo esc_url(admin_url('admin-ajax.php'));?>','<?php echo esc_html(wmvc_show_data('post_id', $listing));?>','<?php echo esc_html($listing_lat);?>','<?php echo esc_html($listing_lng);?>',innerMarker, wdk_jpopup_customOptions, auto_marker_size
                    , <?php if(wmvc_show_data('disable_cluster', $settings) == 'yes'):?> false <?php endif;?>));
    <?php endforeach; ?> 
    wdk_map.addLayer(wdk_clusters);
    /* set center */
    if(wdk_markers.length){
        var limits_center = [];
        for (var i in wdk_markers) {
            var latLngs = [ wdk_markers[i].getLatLng() ];
            limits_center.push(latLngs)
        };
        var bounds = L.latLngBounds(limits_center);
        <?php if(wdk_get_option('wdk_fixed_map_results_position') && wdk_get_option('wdk_default_lat') && wdk_get_option('wdk_default_lng')): ?>
            wdk_map.setView(["<?php echo esc_js(wdk_get_option('wdk_default_lat'));?>","<?php echo esc_js(wdk_get_option('wdk_default_lng'));?>"]);
        <?php elseif($settings['enable_custom_gps_center'] == 'yes'): ?>
            wdk_map.setView(["<?php echo esc_js($settings['conf_custom_map_center_gps_lat']);?>","<?php echo esc_js($settings['conf_custom_map_center_gps_lng']);?>"]);
        <?php else: ?>
            wdk_map.fitBounds(bounds);
        <?php endif; ?>
    }
 })
</script>
<?php

if (!$is_edit_mode) {
    $js_content = ob_get_clean();
    $js_content = str_replace(array('</script>','<script>'),'',$js_content );
    wp_add_inline_script( 'wdk-elementor-main', $js_content );
}
?>

