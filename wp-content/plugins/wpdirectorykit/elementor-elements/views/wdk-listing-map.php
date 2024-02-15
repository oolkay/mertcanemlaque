<?php
/**
 * The template for Element Listing Map.
 * This is the template that elementor element map
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php
if(empty($lng) || empty($lat)) {
    ?>
    <div class="">
        <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Missing address', 'wpdirectorykit');?></p>
    </div>
    <?php
    return false;
}
?>

<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-map infobox-basic">
        <div id="wdk_map_results_<?php echo esc_html($id_element);?>" style="height:<?php echo esc_attr($settings['conf_custom_map_height']['size']);?>px" ></div>
               
        <?php if(wmvc_show_data('enable_router_suggest',$settings) =='yes'): ?>
            <form class="route_suggestion" action="">
                <input class="input_text" type="hidden" name="address" value="<?php echo esc_attr(wdk_field_value('address', $wdk_listing_id));?>" />
                <input class="input_text" type="hidden" name="gps" value="<?php echo esc_attr($lat.','.$lng);?>" />
                <div class="wdk-field-group">
                    <input class="input_text" type="text" placeholder="<?php echo esc_html__(wmvc_show_data('text_suggestion_route_placeholder', $settings), 'wpdirectorykit');?>" name="route_from" />
                </div>
                <div class="wdk-field-group">
                    <button type="submit" class="wdk-btn"><?php echo esc_html__(wmvc_show_data('text_suggestion_route', $settings), 'wpdirectorykit');?></button>
                </div>
            </form>
        <?php endif;?>
    </div>
</div>
<?php
    $zoom_index = $settings['conf_custom_map_zoom_index']['size'];
?>

<?php
$WMVC = &wdk_get_instance();
$WMVC->model('category_m');
if (!$is_edit_mode)
    ob_start();
?>
 <script>
    var wdk_map_<?php echo esc_html($id_element);?> ='';
    var wdk_markers_<?php echo esc_html($id_element);?> = [];
    var wdk_clusters_<?php echo esc_html($id_element);?> ='';
    var wdk_jpopup_customOptions =
    {
        'maxWidth': 'initial',
        'width': 'initial',
        'className' : 'popupCustom'
    };
    jQuery(document).ready(function($) {
        if(wdk_clusters_<?php echo esc_html($id_element);?>=='')
            wdk_clusters_<?php echo esc_html($id_element);?> = L.markerClusterGroup({spiderfyOnMaxZoom: true, showCoverageOnHover: false, zoomToBoundsOnClick: true});
            wdk_map_<?php echo esc_html($id_element);?> = L.map('wdk_map_results_<?php echo esc_html($id_element);?>', {
            center: ["<?php echo esc_js($lat);?>","<?php echo esc_js($lng);?>"],
            zoom: "<?php echo esc_js($zoom_index);?>",
            scrollWheelZoom: false,
            <?php if($settings['conf_custom_dragging'] == 'yes'):?>
                dragging: true,
            <?php else:?>
                dragging: false,
            <?php endif;?>
            tap: !L.Browser.mobile,
            fullscreenControl: true,
            fullscreenControlOptions: {
                position: 'topleft'
            }
        });     
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(wdk_map_<?php echo esc_html($id_element);?>);

        <?php if(!empty($settings['conf_custom_map_style']) && $settings['conf_custom_map_style'] =='custom' && !empty($settings['conf_custom_map_style_self'])):?>
            var positron = L.tileLayer('<?php echo esc_js($settings['conf_custom_map_style_self']);?>').addTo(wdk_map_<?php echo esc_html($id_element);?>);
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

                google_map_style.addTo(wdk_map_<?php echo esc_html($id_element);?>);
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
				).addTo(wdk_map_<?php echo esc_html($id_element);?>);  

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
                    var positron = L.tileLayer('<?php echo esc_js($settings['conf_custom_map_style']);?>').addTo(wdk_map_<?php echo esc_html($id_element);?>);
                <?php endif;?>
        <?php endif;?>

    <?php if(is_numeric($lng)):?>
        <?php 
        $font_class = "fa fa-home";
        $font_icon = $this->generate_icon($settings['conf_custom_map_pin_icon']);
        $pin_icon = $settings['conf_custom_map_pin']['url'];

        if(!empty(wdk_field_value('category_id', $wdk_listing_id))){
            $category = $WMVC->category_m->get_data(wdk_field_value('category_id', $wdk_listing_id));
            if(wmvc_show_data('marker_image_id', $category, false, TRUE, TRUE)){
                $pin_icon = wdk_image_src($category, 'full', NULL,'marker_image_id');
            } else if(!empty(wmvc_show_data('font_icon_code', $category))) {
                $font_class = wmvc_show_data('font_icon_code', $category);
            } 
        } else {
            $font_class = "";
        }

        $wdk_popup_content = '<div class="infobox map-box wdk-infobox-basic">'
                                .'<h3 class="title">'.esc_html(wdk_field_value('post_title', $wdk_listing_id)).'</h3>'
                                .'<p>'.esc_html(wdk_field_value('address', $wdk_listing_id)).'</p>'
                            .'</div>';

        if($settings['puopup_custom_content'] == 'yes') {
            $title_field_id = substr($settings['title_field_id'], strpos($settings['title_field_id'],'__')+2);
            $content_field_id = substr($settings['content_field_id'], strpos($settings['content_field_id'],'__')+2);

            $wdk_popup_content = '<div class="infobox map-box wdk-infobox-basic">'
                .'<h3 class="title">'.esc_html(wdk_field_value($title_field_id, $wdk_listing_id)).'</h3>'
                .'<p>'.esc_html(wdk_field_value($content_field_id, $wdk_listing_id)).'</p>'
            .'</div>';

        }


        $wdk_popup_content = str_replace("'", "\'", $wdk_popup_content);
        $wdk_popup_content = str_replace("\n", "", $wdk_popup_content);
        $wdk_popup_content = str_replace("\r", "", $wdk_popup_content);
        ?>
        let marker;
        <?php if($pin_icon):?>
            var image = '<?php echo esc_html($pin_icon);?>'; var innerMarker = '<div class="wdk_marker-container wdk_marker-container-image category_id_<?php echo esc_js(wdk_field_value('category_id', $wdk_listing_id));?>""><img src='+image+'></img></div>';
        <?php elseif($font_icon && empty($font_class)):?> 
            var innerMarker = '<div class="wdk_marker-container category_id_<?php echo esc_js(wdk_field_value('category_id', $wdk_listing_id));?>""><div class="front wdk_face"><?php echo wdk_viewe($font_icon);?></div><div class="wdk_marker-card"><div class="wdk_marker-arrow"></div></div></div>';
        <?php else:?> 
            var innerMarker = '<div class="wdk_marker-container category_id_<?php echo esc_js(wdk_field_value('category_id', $wdk_listing_id));?>""><div class="front wdk_face"><i class="<?php echo esc_html($font_class);?>"></i></div><div class="wdk_marker-card"><div class="wdk_marker-arrow"></div></div></div>';
        <?php endif;?>

        <?php if($settings['conf_custom_popup_enable'] == 'yes'):?>
            marker = wdk_generate_marker_basic_popup('<?php echo esc_html($lat);?>','<?php echo esc_html($lng);?>',innerMarker,'<?php echo $wdk_popup_content;?>', wdk_jpopup_customOptions);
        <?php else:?>
            marker = wdk_generate_marker_nopopup('<?php echo esc_html($lat);?>','<?php echo esc_html($lng);?>',innerMarker);
        <?php endif;?>

        wdk_markers_<?php echo esc_html($id_element);?>.push(marker);
        wdk_clusters_<?php echo esc_html($id_element);?>.addLayer(marker);
        wdk_map_<?php echo esc_html($id_element);?>.addLayer(wdk_clusters_<?php echo esc_html($id_element);?>);
    <?php endif;?>
    /* set center */
 })
</script>

<?php
if (!$is_edit_mode) {
    $js_content = ob_get_clean();
    $js_content = str_replace(array('</script>','<script>'),'',$js_content );
    wp_add_inline_script( 'wdk-elementor-main', $js_content );
}
?>

