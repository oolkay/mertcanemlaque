<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if(!function_exists('wdk_field_label')) {
    /**
	 * Get field name
	 * @param  int     $field_id      The id of field
	 * @param  string  $default       The default data if field not defined
	 * @return string  field name
	*/
    function wdk_field_label ($field_id='', $default = '') {
		$field_name = $default;

		$WMVC = &wdk_get_instance();
		$WMVC->model('field_m');
		$field_data = $WMVC->field_m->get_fields_data($field_id);
		
		if($field_data && isset($field_data->field_label) && !empty($field_data->field_label)) {
			$field_name = $field_data->field_label;
		}

		return $field_name;
    }
}

if(!function_exists('wdk_field_option')) {
    /**
	 * Get field option
	 * @param  int     $field_id      The id of field
	 * @param  string  $option        The option of field (prefix,suffix,label)
	 * @return string
	*/
    function wdk_field_option ($field_id='', $option = '', $default = '') {
		$field_option = $default;

		$WMVC = &wdk_get_instance();
		$WMVC->model('field_m');
		$field_data = $WMVC->field_m->get_fields_data($field_id);

		if($field_data && isset($field_data->$option)) {
			$field_option = $field_data->$option;
		}

		return $field_option;
    }
}

if(!function_exists('wdk_field_value')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      int|string    $field_id  field id
	 * @param      int|object|array    $listing listing object|array or id
	 * @param      string    $default     The default value, will be return if empty or no exists
	 * @return      string
	 */
    function wdk_field_value ($field_id='', $listing = array(), $default = '') {
		static $listings_data = array();
		$WMVC = &wdk_get_instance();
		$WMVC->model('listing_m');
		$WMVC->model('listingfield_m');
		$f_key = $field_id;
		
		if(is_intval($f_key))
			$f_key = 'field_'.wdk_field_option($field_id,'idfield').'_'.wdk_field_option($field_id,'field_type');

		$listing_data = array();
		if(is_array($listing) || is_object($listing)) {
			$listing_data = (array) $listing;
		}
		elseif(is_intval($listing)) {
			if(!isset($listings_data[$listing])) {
				$listing_data = $WMVC->listing_m->get($listing, TRUE);
				$listings_data[$listing] = $listing_data;
			} else {
				$listing_data = $listings_data[$listing];
			}
		}
		
		if(!empty(wdk_show_data($f_key, $listing_data, '', FALSE, TRUE))) {
			return wdk_show_data($f_key, $listing_data, '', FALSE, TRUE);
		}
		
		return $default;
    }
}

if(!function_exists('wdk_listing_images')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      string|object|array    $listing object or string with ids of images
	 * @param      string    $size     The size of image
	 * @param      string    $default     The default value, will be return if empty or no exists
	 * @return     array
	 */
    function wdk_listing_images ($listing = array(), $size = 'thumb',  $default = '') {
		$output = array();
		$image_ids = '';
		if(is_array($listing) || is_object($listing)) {
			$image_ids = explode(',', wdk_show_data('listing_images', $listing, '', TRUE, TRUE));
		} else if(is_string($listing)){
			$image_ids = explode(',', $listing);
		}

		if(is_array($image_ids)) {
			foreach ($image_ids as $key => $image_id) {
				if(is_numeric($image_id))
				{
					$image = wp_get_attachment_url( $image_id, $size  );
					$output[] = $image;
				}
			}
		} else if(is_numeric($image_ids))
		{
			$image = wp_get_attachment_url( $image_ids, $size  );
			$output[] = $image;
		}
		return $output;
    }
}

if(!function_exists('wdk_listing_images_fast_access')) {
    /**
	 * Initialize the class and set its properties, work only with column listing_images and listing_images_path
	 *
	 * @param      int|object|array    $listing listing data
	 * @param      string    $size     The size of image
	 * @param      string    $default     The default value, will be return if empty or no exists
	 * @return     array
	 */
    function wdk_listing_images_fast_access ($listing = array(), $size = 'thumb',  $default = '') {
		$output = array();
		if(!empty(wdk_show_data('listing_images_path', $listing, '', TRUE, TRUE))) {
			$images = explode(',', wdk_show_data('listing_images_path', $listing, '', TRUE, TRUE));
			if(is_array($images)) {
				foreach ($images as $key => $image_path) {
					$output[] = WP_CONTENT_URL. '/uploads/'.$image_path;
				}
			} else  {
				$output[] = WP_CONTENT_URL. '/uploads/'.$images;
			}
		} else {
			$image_ids = explode(',', wdk_show_data('listing_images', $listing, '', TRUE, TRUE));
			if(is_array($image_ids)) {
				foreach ($image_ids as $key => $image_id) {
					if(is_numeric($image_id))
					{
						$image = wp_get_attachment_image_src( $image_id, $size  );
						$output[] = $image[0];
					}
				}
			} else if(is_numeric($image_ids)) {
				$image = wp_get_attachment_image_src( $image_ids, $size  );
				$output[] = $image[0];
			}
		}

		return $output;
    }
}

if(!function_exists('wdk_listing_images_data')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      string|object|array    $listing object or string with ids of images
	 * @param      string    $size     The size of image
	 * @param      string    $default     The default value, will be return if empty or no exists
	 * @param      string|array    $ext_list     Allowed extensions, mixed sring with separated like jpg,doc,pdf or array
	 * @return     array
	 */
    function wdk_listing_images_data ($listing = array(), $size = 'thumb',  $default = '', $ext_list = array()) {
		$output = array();

		$image_ids = '';
		if(is_array($listing) || is_object($listing)) {
			$image_ids = explode(',', wdk_show_data('listing_images', $listing, '', TRUE, TRUE));
		} else if(is_string($listing)){
			$image_ids = explode(',', $listing);
		}

		if(is_string($ext_list)){
			if(!empty($ext_list)) {
				$ext_list = explode(',', $ext_list);
			} else {
				$ext_list = array();
			}
		}
		if(is_array($image_ids)) {
			foreach ($image_ids as $key => $image_id) {
				if(is_numeric($image_id))
				{
					$image = wp_get_attachment_url( $image_id, $size  );
					if(!empty($ext_list) && !in_array(wdk_file_extension($image), $ext_list)) {
						continue;
					}

					if(empty($image)/* || !file_exists(str_replace(WP_CONTENT_URL, WP_CONTENT_DIR, $image))*/)
					{
						$image = wdk_placeholder_image_src();
					}
					$attr = wdk_image_attr($image_id);
					$output[] = array('src' =>$image, 'title' => $attr['title'], 'alt' => $attr['alt']);
				}
			}
		} else if(is_numeric($image_ids))
		{
			$image = wp_get_attachment_url( $image_ids, $size  );
			if(!empty($ext_list) && !in_array(wdk_file_extension($image), $ext_list)) {
				$attr = wdk_image_attr($image_ids);
				$output[] = array('src' =>$image, 'title' => $attr['title'], 'alt' => $attr['alt']);
			}
		}
		return $output;
    }
}

if(!function_exists('wdk_image_src')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      int|object|array    $listing listing data
	 * @param      string    $size     The size of image
	 * @param      string    $default     The default value, will be return if empty or no exists
	 * @return     string
	 */
    function wdk_image_src ($listing = array(), $size = 'thumb',  $default = '', $field_image = 'listing_images', $field_images_paths = 'listing_images_path') {
		$output = wdk_placeholder_image_src();
		if(!empty($default)) {
			$output = $default;
		}
		
		if(!empty(wdk_show_data($field_images_paths, $listing, '', TRUE, TRUE))) {
			$images = explode(',', wdk_show_data($field_images_paths, $listing, '', TRUE, TRUE));

			if(is_array($images))
				$image = $images[0];
	
			if(!empty($image) && file_exists(WP_CONTENT_DIR. '/uploads/'.$image))
			{
				$output = WP_CONTENT_URL. '/uploads/'.$image;
			}
		} else {
			$image_ids = explode(',', trim(wdk_show_data($field_image , $listing, '', TRUE, TRUE), ','));
			
			if(is_array($image_ids))
				$image_id = $image_ids[0];
	
			if(is_numeric($image_id) && !empty($image_id) /*&& file_exists(get_attached_file($image_id))*/)
			{
				$image = wp_get_attachment_image_src( $image_id, 'full'  );
				if(!empty($image)/* && file_exists(str_replace(WP_CONTENT_URL, WP_CONTENT_DIR,$image[0]))*/)
					$output = $image[0];
			}
		}

		return $output;
    }
}

if(!function_exists('wdk_listing_media_src')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      int|object|array    $listing listing data
	 * @param      string    $default     The default value, will be return if empty or no exists
	 * @return     string
	 */
    function wdk_listing_media_src ($listing = array(), $default = '', $field_image = 'listing_images') {
		$output = wdk_placeholder_image_src();
		if(!empty($default)) {
			$output = $default;
		}

		$image_ids = explode(',', trim(wdk_show_data($field_image , $listing, '', TRUE, TRUE), ','));
		
		if(is_array($image_ids))
			$image_id = $image_ids[0];

		if(is_numeric($image_id) && !empty($image_id) /*&& file_exists(get_attached_file($image_id))*/)
		{
			$image = wp_get_attachment_url( $image_id);
			if(!empty($image)/* && file_exists(str_replace(WP_CONTENT_URL, WP_CONTENT_DIR, $image))*/)
				$output = $image;
		}

		return $output;
    }
}


if(!function_exists('wdk_resultitem_fields')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      int    $id The id of the resultitem list.
	 * @return     array
	 */
	function wdk_resultitem_fields($id = 1)
	{
		static $fields_data = array();
		$WMVC = &wdk_get_instance();

        $WMVC->model('resultitem_m');
		$fields = NULL;
		if(isset($fields_data[$id])) {
			return $fields_data[$id];
		}

		$db_data = wdk_resultitem($id);
		// generate/decode used fields

		if(is_object($db_data))
			$fields = json_decode($db_data->resultitem_json);

		$fields_data[$id] = $fields;
		
		return $fields;
    }
}

if(!function_exists('wdk_resultitem')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      int    $id The id of the resultitem list.
	 * @return     array
	 */
	function wdk_resultitem($id = 1)
	{
		static $fields_data = array();
		$WMVC = &wdk_get_instance();

        $WMVC->model('resultitem_m');
		$db_data = NULL;
		if(isset($fields_data[$id])) {
			return $fields_data[$id];
		}

		$db_data = $WMVC->resultitem_m->get($id, TRUE);

		$fields_data[$id] = $db_data;
		
		return $db_data;
    }
}

if(!function_exists('wdk_resultitem_fields_section')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      int    $section_index   section id
	 * @param      int    $id The id of the resultitem list.
	 * @return     array
	 */
	function wdk_resultitem_fields_section($section_index=1, $id = 1)
	{
		$fields = array();
		$list = wdk_resultitem_fields($id);

		if(isset($list[$section_index])) {
			$fields = $list[$section_index];
		}

		return $fields;
    }
}

if(!function_exists('wdk_resultitem_fields_section_value')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      int    $id           The id of the resultitem list.
	 * @param      int    $section_index      The id of section in resultitem_m
	 * @param      int|object|array     $listing  listing data or listing id
	 * @param      string   $default    Default Value if empty
 	
	 * @return     array
	 */
	function wdk_resultitem_fields_section_value($id = 1, $section_index=1, $listing='', $default='')
	{
		$output = [];
		$list = wdk_resultitem_fields_section($section_index, $id);
		$WMVC = &wdk_get_instance();
		$WMVC->model('field_m');
		$WMVC->model('category_m');
		$WMVC->model('location_m');

        static $category_static = array();
        static $location_static = array();
		foreach ($list as $key => $field) {
			$row = array();
			$row['value'] = wdk_field_value($field->field_id, $listing, $default);

			if(wmvc_show_data('field_id', $field) == 'agent_image') {
				$row['value'] = wmvc_show_data('user_id_editor', $listing);
			}

			if(empty($row['value'])) continue;

			if(wmvc_show_data('field_id', $field) == 'category_id') {

                if(!isset($category_static[$row['value']]))
                {
                    $tree_data = $WMVC->category_m->get($row['value'], TRUE);

                    $category_static[$row['value']] = $tree_data;
                }
                else
                {
                    $tree_data = $category_static[$row['value']];
                }

				$row['value'] = wmvc_show_data('category_title', $tree_data);
				
				if(isset($listing->categories_list)){
					$other_categories = wdk_generate_other_categories_fast($listing->categories_list);
					if(!empty($other_categories))
					$row['value'] .=', '.join(', ',$other_categories);
				}
			} else if(wmvc_show_data('field_id', $field) == 'location_id') {

                if(!isset($location_static[$row['value']]))
                {
                    $tree_data = $WMVC->location_m->get($row['value'], TRUE);

                    $location_static[$row['value']] = $tree_data;
                }
                else
                {
                    $tree_data = $location_static[$row['value']];
                }

				$row['value'] = wmvc_show_data('location_title', $tree_data);

				if(isset($listing->locations_list)){
					$other_locations = wdk_generate_other_locations_fast($listing->locations_list);
					if(!empty($other_locations))
					$row['value'] .=', '.join(', ',$other_locations);
				}
			} else if(wdk_field_option($field->field_id, 'field_type') == 'DATE') {
				$row['value'] = wdk_get_date($row['value'], FALSE);
			} else {
				
			}

			$field_data = $WMVC->field_m->get_fields_data($field->field_id);
			$row += (array) $field;
			$row += (array) $field_data;

			$output [] = $row;
		}
		if(empty($output)) return NULL;
		return $output;
    }
}
if(!function_exists('wdk_generate_resultitem_fields_section_value')) {
    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      int    $id           The id of the resultitem list.
	 * @param      int    $section_index      The id of section in resultitem_m
	 * @param      int|object|array     $listing  listing object|array or id
	 * @param      string   $default    Default Value if empty
	 * @param      string   $html       Html for sprintf(), where
  	 * 										%1$s - value		
	 * 										%2$s - title		
	 * 										%3$s - prefix		
	 * 										%4$s - suffix		
	 * @return     array
	 */
	function wdk_generate_resultitem_fields_section_value($id = 1, $section_index=1, $listing='', $default='', $html =' %3$s %1$s %4$s ')
	{
		$output = '';
		$list = wdk_resultitem_fields_section($section_index, $id);
		foreach ($list as $key => $field) {
			$value = wdk_field_value($field->field_id, $listing, $default);
			if(empty($value)) continue;
			if(wdk_field_option($field->field_id,'field_type') == 'CHECKBOX') {
				$html ='<span> %2$s </span>';
			} 
			$output .= sprintf($html,
									$value,
									wdk_field_option($field->field_id,'field_label'),
									wdk_field_option($field->field_id,'prefix'),
									wdk_field_option($field->field_id,'suffix')
								);
		}

		return trim($output);
    }
}

if(!function_exists('wdk_listing_card')) {
    /**
	 * Generate listing card html
	 *
	 * @param      array    $listing        The listing data.
	 * @param      array    $settings       The settings.
	 * @param      bool   	$json_output    Encode for json, default false
	 * @param      string   $html           Html for sprintf(), where
  	 * 										%1$s - content		
	 * @return     string
	 */

	function wdk_listing_card($listing = array(), $settings = array(), $json_output = FALSE, $html_sprintf = '%1$s', $template = 'result_item_card') {
		$data = ['listing'=>$listing, 'settings'=>$settings];

		$WMVC = &wdk_get_instance();

		/* Favorite module */
		$data ['favorite_added'] = wmvc_show_data('is_favorite', $listing, false, TRUE, TRUE);
		/* End Favorite module */

		$output = $WMVC->view('frontend/'.$template, $data, FALSE);

		$output = sprintf($html_sprintf, $output);
		if($json_output) {
			$output = str_replace("'", "\'", $output);
			$output = str_replace('"', '\"', $output);
			$output = str_replace(array("\n", "\r"), '', $output);
		}
		
		return ($output);
	}
}

if(!function_exists('wdk_image_alt')) {
    /**
	 * Generate alt tag
	 *
	 * @param      string|int    $link|id        The url or id of image.
	 * @return     string    alt or title
	 */

	function wdk_image_alt($link_id = '') {
		$alt = '';

		if(is_intval($link_id)) {
			$attached_id = $link_id;
		} else {
			$attached_id = attachment_url_to_postid($link_id);
		}

        $alt = get_post_meta($attached_id, '_wp_attachment_image_alt', true);
		if($alt == '') {
			$alt = get_the_title($attached_id);
		}
		
		return $alt;
	}
}

if(!function_exists('wdk_image_attr')) {
    /**
	 * Generate array with alt and title, if alt missing, return title like alt
	 *
	 * @param      string|int    $link|id        The url or id of image.
	 * @return     array    alt and title
	 */

	function wdk_image_attr($link_id = '') {
		$alt = '';
		$title = '';

		if(is_intval($link_id)) {
			$attached_id = $link_id;
		} else {
			$attached_id = attachment_url_to_postid($link_id);
		}

		$alt = get_post_meta($attached_id, '_wp_attachment_image_alt', true);
		$title = get_the_title($attached_id);
		if($alt == '') {
			$alt = $title;
		}
		
		return array('title'=>$title, 'alt'=>$alt);
	}
}



if(!function_exists('wdk_generate_other_locations')) {
    /**
	 * Return array with locations id from listing
	 *
	 * @param      int    post_id the listings id
	 * @return     array    locations
	 */

	function wdk_generate_other_locations($post_id = NULL) {
		$WMVC = &wdk_get_instance();
		$WMVC->model('locationslistings_m');
		$locations_array = array();
		$locations = $WMVC->locationslistings_m->get_locations($post_id);
		
		if($locations) foreach ($locations as $item) {
			if($item)
				$locations_array[wmvc_show_data('idlocation', $item, false, TRUE, TRUE)] = wmvc_show_data('location_title', $item, false, TRUE, TRUE);
		}
		return $locations_array;
	}
}

if(!function_exists('wdk_generate_other_categories')) {
    /**
	 * Return array with categories id from listing
	 *
	 * @param      int    post_id the listings id
	 * @return     array    categories
	 */

	function wdk_generate_other_categories($post_id = NULL) {
		$WMVC = &wdk_get_instance();
		$WMVC->model('categorieslistings_m');
		$categories_array = array();
		$categories = $WMVC->categorieslistings_m->get_categories($post_id);
		
		if($categories) foreach ($categories as $item) {
			if($item)
				$categories_array[wmvc_show_data('idcategory', $item, false, TRUE, TRUE)] = wmvc_show_data('category_title', $item, false, TRUE, TRUE);
		}
		return $categories_array;
	}
}

if(!function_exists('wdk_generate_other_locations_keys')) {
    /**
	 * Return array with locations id from listing
	 *
	 * @param      int    post_id the listings id
	 * @return     array    locations keys
	 */

	function wdk_generate_other_locations_keys($post_id = NULL) {
		$WMVC = &wdk_get_instance();
		$WMVC->model('locationslistings_m');
		$locations_array = array();
		$locations = $WMVC->locationslistings_m->get($post_id);
		
		if($locations) foreach ($locations as $item) {
			if($item)
				$locations_array[] = wmvc_show_data('location_id', $item, false, TRUE, TRUE);
		}
		return $locations_array;
	}
}

if(!function_exists('wdk_generate_other_categories_keys')) {
    /**
	 * Return array with categories id from listing
	 *
	 * @param      int    post_id the listings id
	 * @return     array    categories keys
	 */

	function wdk_generate_other_categories_keys($post_id = NULL) {
		$WMVC = &wdk_get_instance();
		$WMVC->model('categorieslistings_m');
		$categories_array = array();
		$categories = $WMVC->categorieslistings_m->get($post_id);
		
		if($categories) foreach ($categories as $item) {
			if($item)
				$categories_array[] = wmvc_show_data('category_id', $item, false, TRUE, TRUE);
		}
		return $categories_array;
	}
}

if(!function_exists('wdk_generate_other_categories_fast')) {
    /**
	 * Return array with categories id from listing
	 *
	 * @param      string   ids list separeted with ','
	 * @return     array    with categories list 
	 */

	function wdk_generate_other_categories_fast($ids = NULL) {
		static $categories_list = NULL;
		$categories_array = array();

		if(is_null($categories_list)) {
			$categories_list = array();
			$WMVC = &wdk_get_instance();
			$WMVC->model('category_m');
			$categories = $WMVC->category_m->get();

			if($categories) foreach ($categories as $item) {
				$categories_list[$item->idcategory] = $item->category_title;
			}
		}

		if(is_string($ids))
			foreach(explode(',', $ids) as $item_id) {
				if(empty($item_id)) continue;


				if(isset($categories_list[$item_id]))
					$categories_array[$item_id] = $categories_list[$item_id];
			}

		return $categories_array;
	}
}

if(!function_exists('wdk_generate_other_locations_fast')) {
    /**
	 * Return array with locations id from listing
	 *
	 * @param      string   ids list separeted with ','
	 * @return     array    with locations list 
	 */

	function wdk_generate_other_locations_fast($ids = NULL) {
		static $locations_list = NULL;
		$locations_array = array();

		if(is_null($locations_list)) {
			$locations_list = array();
			$WMVC = &wdk_get_instance();
			$WMVC->model('location_m');
			$locations = $WMVC->location_m->get();

			if($locations) foreach ($locations as $item) {
				$locations_list[$item->idlocation] = $item->location_title;
			}
		}

		if(is_string($ids))
			foreach(explode(',', $ids) as $item_id) {
				if(empty($item_id)) continue;


				if(isset($locations_list[$item_id]))
					$locations_array[$item_id] = $locations_list[$item_id];
			}

		return $locations_array;
	}
}


if(!function_exists('wdk_field_value_on_type')) {
    /**
	 * Generate field value, based on type
	 *
	 * @param      int|string    $field_id  field id
	 * @param      int|object|array    $listing listing object|array or id
	 * @param      string    $default     The default value, will be return if empty or no exists
	 * @return      string
	 */
    function wdk_field_value_on_type ($field_id='', $listing = array(), $default = '') {

		$value = wdk_field_value($field_id, $listing, $default);

		/* filter per type */
		if(empty($value)) {
			return $default;
		}

		if(wdk_field_option($field_id, 'is_price_format') && wdk_field_option($field_id, 'field_type') == 'NUMBER') {
			$value = wdk_number_format_i18n(wdk_filter_decimal($value));
		} elseif(wdk_field_option($field_id, 'field_type') == 'DATE') {
			$value = wdk_get_date($value, FALSE);
		} else {
			$value = wdk_filter_decimal($value);
		}

		return $value;
    }
}

if(!function_exists('wdk_location_get_all_childs')) {
    /**
	 * Get cached childs of location
	 *
	 * @param      int    $field_id  location id
	 * @return     array
	 */
    function wdk_location_get_all_childs ($field_id='') {
		static $childs_cache = array();
		$childs = array();
		if(isset($childs_cache[$field_id])) {
			$childs = $childs_cache [$field_id];
		} else {
			global $Winter_MVC_WDK;
			$Winter_MVC_WDK->load_helper('listing');
			$Winter_MVC_WDK->model('location_m');
	
			$childs = $Winter_MVC_WDK->location_m->get_all_childs($field_id); 
			if($childs) {
				$childs_cache [$field_id] = $childs;
			} else {
				$childs_cache [$field_id] = $childs;
			}
		}
		return $childs;
    }
}

if(!function_exists('wdk_category_get_all_childs')) {
     /**
	 * Get cached childs of category
	 *
	 * @param      int    $field_id  category id
	 * @return     array
	 */
    function wdk_category_get_all_childs ($field_id='') {
		static $childs_cache = array();
		$childs = array();
		if(isset($childs_cache[$field_id])) {
			$childs = $childs_cache [$field_id];
		} else {
			global $Winter_MVC_WDK;
			$Winter_MVC_WDK->load_helper('listing');
			$Winter_MVC_WDK->model('category_m');
	
			$childs = $Winter_MVC_WDK->category_m->get_all_childs($field_id); 
			if($childs) {
				$childs_cache [$field_id] = $childs;
			} else {
				$childs_cache [$field_id] = $childs;
			}
		}
		return $childs;
    }
}

if(!function_exists('wdk_get_user_data')) {
     /**
	 * Get user data
	 *
	 * @param      int    $user id
	 * @return     array  user data ('userdata'=>$userdata, 'avatar'=>get_avatar_url($user_id),'user_id'=>$user_id, 'profile_url'=>'url on profile');
	 */
    function wdk_get_user_data ($user_id='') {
		static $users_cache = array();
		
		$user = array();
		if(isset($users_cache[$user_id])) {
			$user = $users_cache [$user_id];
		} else {

			$userdata = get_userdata($user_id);
			if($userdata) {
				$user = array('userdata'=>$userdata, 'avatar'=>get_avatar_url($user_id, array("size"=>300)),'user_id'=>$user_id);
				$user['profile_url'] =NULL;

				if(function_exists('wdk_generate_profile_permalink'))
					$user['profile_url'] = wdk_generate_profile_permalink($userdata);
			}

			if($user) {
				$users_cache [$user_id] = $user;
			} else {
				$users_cache [$user_id] = $user;
			}
		}
		return $user;
    }
}

if(!function_exists('wdk_get_user_field')) {
     /**
	 * Get user field
	 *
	 * @param      int       $user id
	 * @param      string    $field use field
	 * @param      string    $default, value if empty or not find user
	 * @return     string|array    user field data or default value
	 */
    function wdk_get_user_field ($user_id='', $field = '', $default = '') {
		$user_data = wdk_get_user_data ($user_id);
		$output = $default;
		
		if(!empty($user_data)) {
			if(wmvc_show_data($field,$user_data['userdata'], false, TRUE, TRUE)) {
				$output = wmvc_show_data($field,$user_data['userdata'], false, TRUE, TRUE);
			}
		}

		return $output;
    }
}

if(!function_exists('wdk_get_category_title')) {
     /**
	 * Get user field
	 *
	 * @param      int       $user id
	 * @param      string    $field use field
	 * @param      string    $default, value if empty or not find user
	 * @return     string|array    user field data or default value
	 */
    function wdk_get_category_title ($category_id='', $empty = false) {
		$output = $empty;
		$category = wdk_generate_other_categories_fast($category_id);

		if(!empty($category)) {
			$output = $category[$category_id];
		}

		return $output;
    }
}

if(!function_exists('wdk_get_location_title')) {
     /**
	 * Get user field
	 *
	 * @param      int       $user id
	 * @param      string    $field use field
	 * @param      string    $default, value if empty or not find user
	 * @return     string|array    user field data or default value
	 */
    function wdk_get_location_title ($location_id='', $empty = false) {
		$output = $empty;
		$location = wdk_generate_other_locations_fast($location_id);

		if(!empty($location)) {
			$output = $location[$location_id];
		}

		return $output;
    }
}
