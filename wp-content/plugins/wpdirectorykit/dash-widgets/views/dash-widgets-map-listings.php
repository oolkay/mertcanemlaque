<?php
/**
 * The template for Shortcode Listings list
 * This is the template that Shortcode listings list
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?> 
<div class="wdk-dashwidget-element" id="wdk_dashwidget_<?php echo esc_attr($id_element);?>">
    <div class="wdk-map">
        <div id="wdk_map_results_<?php echo esc_html($id_element);?>" style="height:<?php echo esc_attr($settings['conf_custom_map_height']);?>px" class="wdk_map_results <?php echo wmvc_show_data('styles_thmbn_des_type',$settings, '');?> " ></div>
    </div>
    <?php
        $zoom_index = 10;
    ?>

    <?php
        $zoom_index = $settings['conf_custom_map_zoom_index'];
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
            <?php if($settings['conf_custom_dragging'] == 'yes'):?>
                dragging: true,
            <?php else:?>
                dragging: false,
            <?php endif;?>
            tap: !L.Browser.mobile
        });     
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(wdk_map);

        <?php if(!empty($settings['conf_custom_map_style']) && $settings['conf_custom_map_style'] =='custom' && !empty($settings['conf_custom_map_style_self'])):?>
            var positron = L.tileLayer('<?php echo esc_js($settings['conf_custom_map_style_self']);?>').addTo(wdk_map);
        <?php elseif(!empty($settings['conf_custom_map_style']) && $settings['conf_custom_map_style'] !='custom'):?>
            var positron = L.tileLayer('<?php echo esc_js($settings['conf_custom_map_style']);?>').addTo(wdk_map);
        <?php endif;?>

        <?php foreach($results as $key=>$listing): ?>
        <?php  if(!is_numeric(wmvc_show_data('lng', $listing)))continue;?>
        <?php 
        $pin_icon = "";
        $font_class = "dashicons dashicons-admin-home";
        $font_icon = "";

        $pin_icon = wmvc_show_data('conf_custom_map_pin',$settings, false);

        if(!empty(wmvc_show_data('category_id', $listing))){
            $category = $WMVC->category_m->get_data(wmvc_show_data('category_id', $listing));
            if(wmvc_show_data('marker_image_id', $category, false, TRUE, TRUE)){
                $pin_icon = wdk_image_src($category, 'full', NULL,'marker_image_id');
            } else if(defined('ELEMENTOR_ASSETS_URL') && !empty(wmvc_show_data('font_icon_code', $category))) {
                $font_class = wmvc_show_data('font_icon_code', $category);
            } 
        } else {
            $font_class = "";
            $font_icon = "";
        }

        ?>
        <?php if($pin_icon):?>
            var image = '<?php echo esc_html($pin_icon);?>'; var innerMarker = '<div class="wdk_marker-container wdk_marker-container-image"><img src='+image+'></img></div>';
        <?php elseif($font_icon && empty($font_class)):?> 
            var innerMarker = '<div class="wdk_marker-container"><div class="front wdk_face"><?php echo wdk_viewe($font_icon);?></div><div class="wdk_marker-card"><div class="wdk_marker-arrow"></div></div></div>';
        <?php else:?> 
            var innerMarker = '<div class="wdk_marker-container"><div class="front wdk_face"><i class="<?php echo esc_html($font_class);?>"></i></div><div class="wdk_marker-card"><div class="wdk_marker-arrow"></div></div></div>';
        <?php endif;?>
        wdk_markers.push(wdk_dash_widget_generate_marker_ajax_popup('<?php echo esc_url(admin_url('admin-ajax.php'));?>','<?php echo esc_html(wmvc_show_data('post_id', $listing));?>','<?php echo esc_html(wmvc_show_data('lat', $listing));?>','<?php echo esc_html(wmvc_show_data('lng', $listing));?>',innerMarker, wdk_jpopup_customOptions));
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

    $js_content = ob_get_clean();
    $js_content = str_replace(array('</script>','<script>'),'',$js_content );
    wp_add_inline_script( 'wdk-dash-widgets-main', $js_content );
?>


</div>

