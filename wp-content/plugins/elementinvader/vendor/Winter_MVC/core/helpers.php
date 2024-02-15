<?php

if ( ! function_exists('hmvc_download_file'))
{
    /**
    * @deprecated 2.0 Use wmvc_download_file()
    * @see wmvc_download_file()
    */
    function hmvc_download_file($url, $save_file_loc, $data = array())
    {
        return wmvc_download_file($url, $save_file_loc, $data);
    }
}

if ( ! function_exists('hmvc_current_edit_url'))
{
    /**
    * @deprecated 2.0 Use wmvc_current_edit_url()
    * @see wmvc_current_edit_url()
    */
    function hmvc_current_edit_url()
    {
        return wmvc_current_edit_url();
    }
}

if ( ! function_exists('hmvc_api_call'))
{
    /**
    * @deprecated 2.0 Use wmvc_api_call()
    * @see wmvc_api_call()
    */
    function hmvc_api_call($method, $url, $data, $headers = false)
    {
        return wmvc_api_call($method, $url, $data, $headers);
    }
}


if ( ! function_exists('echo_js'))
{
    /**
    * @deprecated 2.0 Use wmvc_echo_js()
    * @see wmvc_echo_js()
    */
    function echo_js($str)
    {
        $str = str_replace("'", "\'", trim($str));
        $str = str_replace('"', '\"', $str);
        
        echo $str;
    }
}

if ( ! function_exists('_js'))
{
    /**
    * @deprecated 2.0 Use wmvc_js()
    * @see wmvc_js()
    */
    function _js($str)
    {
        $str = str_replace("\\", "", trim($str));
        $str = str_replace("'", "\'", trim($str));
        $str = str_replace('"', '\"', $str);
        
        return $str;
    }
}

if ( ! function_exists('dump'))
{
    /**
    * @deprecated 2.0 Use wmvc_dump()
    * @see wmvc_dump()
    */
    function dump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

if ( ! function_exists('resolve_ip'))
{
    /**
    * @deprecated 2.0 Use wmvc_resolve_ip()
    * @see wmvc_resolve_ip()
    */
    function resolve_ip($ip)
    {
        if($ip == 'DISABLED')
            return $ip;

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            // $ip is a valid IP address"
        } else {
            return '';
        }

        if($ip == '::1')
        {
            return $ip.', local';
        }

        $str = '<a target="_blank" href="https://whatismyipaddress.com/ip/'.$ip.'">'.$ip.'</a>';
        
        return $str;
    }
}

if ( ! function_exists('stringify_attributes'))
{
    /**
     * Stringify attributes for use in html tags.
     * 
     * @since 1.0
     * @deprecated 2.0 Use wmvc_stringify_attributes()
     * @see wmvc_stringify_attributes()
     * 
     * Helper function used to convert an array or object of
     * attributes to a string
     *
     * @param	mixed
     * @return	string
     */
    function stringify_attributes($attributes, $js = FALSE)
    {
        if (is_object($attributes) && count($attributes) > 0)
        {
            $attributes = (array) $attributes;
        }

        if (is_array($attributes))
        {
            $atts = '';
            if (count($attributes) === 0)
            {
                return $atts;
            }
            foreach ($attributes as $key => $val)
            {
                if ($js)
                {
                    $atts .= $key.'='.$val.',';
                }
                else
                {
                    $atts .= ' '.$key.'="'.$val.'"';
                }
            }
            return rtrim($atts, ',');
        }
        elseif (is_string($attributes) && strlen($attributes) > 0)
        {
            return ' '.$attributes;
        }

        return $attributes;
    }
}

if ( ! function_exists('anchor'))
{
    /**
     * Anchor Link
     *
     * Creates an anchor based on the local URL.
     *
     * @deprecated 2.0 Use wmvc_anchor()
     * @see wmvc_anchor()
     * 
     * @param	string	the URL
     * @param	string	the link title
     * @param	mixed	any attributes
     * @return	string
     */
	function anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		$site_url = is_array($uri)
			? site_url($uri)
			: (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri));

		if ($title === '')
		{
			$title = $site_url;
		}

		if ($attributes !== '')
		{
			$attributes = stringify_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}

if ( ! function_exists('btn_edit'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_edit()
    * @see wmvc_btn_edit()
    */
    function btn_edit($uri)
    {
        return anchor($uri, '<i class="glyphicon glyphicon-pencil"></i>', array('class'=>'btn btn-success btn-xs'));
    }
}


if ( ! function_exists('btn_read'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_read()
    * @see wmvc_btn_read()
    */
    function btn_read($uri, $title=NULL)
    {
        if(empty($title))$title=__('Read', 'wmvc_win');
        
        return anchor($uri, '<i class="glyphicon glyphicon-search"></i> '.$title, array('class'=>'btn btn-primary btn-xs'));
    }
}

if ( ! function_exists('btn_open'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_open()
    * @see wmvc_btn_open()
    */
    function btn_open($uri, $target=NULL)
    {
        if($target === NULL)
            $target = '_blank';

        return anchor($uri, '<i class="glyphicon glyphicon-search"></i>', array('class'=>'btn btn-primary btn-xs', 'target'=>$target, 'title'=>__('Open details', 'wmvc_win')));
    }
}

if ( ! function_exists('btn_open_ajax'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_open_ajax()
    * @see wmvc_btn_open_ajax()
    */
    function btn_open_ajax($uri, $target=NULL)
    {
        if($target === NULL)
            $target = '_blank';

        return anchor($uri, '<i class="glyphicon glyphicon-search"></i>', array('class'=>'btn btn-primary btn-xs popup-with-form-ajax', 'target'=>$target, 'title'=>__('Open details', 'wmvc_win')));
    }
}

if ( ! function_exists('btn_delete_noconfirm'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_delete_noconfirm()
    * @see wmvc_btn_delete_noconfirm()
    */
    function btn_delete_noconfirm($uri)
    {
        return anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array('class'=>'btn btn-danger btn-xs delete_button'));
    }
}


if ( ! function_exists('btn_delete'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_delete()
    * @see wmvc_btn_delete()
    */
    function btn_delete($uri, $confirm_question = TRUE, $title='')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '';
        }

        if($confirm_question)
        {
            return anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array( 'target' => $target,  'title' => $title, 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')', 'class'=>'btn btn-danger btn-xs delete_button'));
        }
        else
        {
            return anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array( 'target' => $target,  'title' => $title, 'class'=>'btn btn-danger btn-xs delete_button'));
        }
    }
}


if ( ! function_exists('btn_save'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_save()
    * @see wmvc_btn_save()
    */
    function btn_save($uri, $empty = '-empty')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '';
        }

        return anchor($uri, '<i class="glyphicon glyphicon-heart'.$empty.'"></i> ', array( 'target' => $target, 'class'=>'btn btn-danger btn-xs save_button', 'title'=>__('Save as Favourite for further analysis', 'wmvc_win')));
    }
}

if ( ! function_exists('btn_block'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_block()
    * @see wmvc_btn_block()
    */
    function btn_block($uri, $confirm_question = FALSE, $title='')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '_blank';
        }

        if($confirm_question)
        {
            return anchor($uri, '<i class="glyphicon glyphicon-lock"></i> ', array( 'target' => $target, 'title' => $title, 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')', 'class'=>'btn btn-warning btn-xs block_button'));
        }
        else
        {
            return anchor($uri, '<i class="glyphicon glyphicon-lock"></i> ', array( 'target' => $target, 'title' => $title, 'class'=>'btn btn-warning btn-xs block_button'));
        }
    }
}

if ( ! function_exists('btn_view'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_view()
    * @see wmvc_btn_view()
    */
    function btn_view($uri, $confirm_question = FALSE, $title='')
    {
        if($confirm_question)
        {
            return anchor($uri, '<i class="glyphicon glyphicon-search"></i> ', array( 'title' => $title, 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')', 'class'=>'btn btn-info btn-xs'));
        }
        else
        {
            return anchor($uri, '<i class="glyphicon glyphicon-search"></i> ', array( 'title' => $title, 'class'=>'btn btn-info btn-xs'));
        }
    }
}

if ( ! function_exists('btn_hide'))
{
    /**
    * @deprecated 2.0 Use wmvc_btn_hide()
    * @see wmvc_btn_hide()
    */
    function btn_hide($uri)
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '_blank';
        }

        return anchor($uri, '<i class="glyphicon glyphicon-eye-close"></i> ', array( 'target' => $target, 'class'=>'btn btn-default btn-xs', 'title'=>__('Define hide rules', 'wmvc_win')));
    }
}


if ( ! function_exists('get_file_extension'))
{
    /**
    * @deprecated 2.0 Use wmvc_get_file_extension()
    * @see wmvc_get_file_extension()
    */
    function get_file_extension($filepath)
    {
        return substr($filepath, strrpos($filepath, '.')+1);
    }
}

if ( ! function_exists('character_hard_limiter'))
{
    /**
    * @deprecated 2.0 Use wmvc_character_hard_limiter()
    * @see wmvc_character_hard_limiter()
    */
    function character_hard_limiter($string, $max_len)
    {
        if(strlen($string)>$max_len)
        {
            return substr($string, 0, $max_len-3).'...';
        }
        
        return $string;
    }
}

if ( ! function_exists('wmvc_echo_js'))
{
    function wmvc_echo_js($str)
    {
        $str = str_replace("'", "\'", trim($str));
        $str = str_replace('"', '\"', $str);
        
        echo $str;
    }
}

if ( ! function_exists('wmvc_js'))
{
    function wmvc_js($str)
    {
        $str = str_replace("\\", "", trim($str));
        $str = str_replace("'", "\'", trim($str));
        $str = str_replace('"', '\"', $str);
        
        return $str;
    }
}

if ( ! function_exists('wmvc_dump'))
{
    function wmvc_dump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}


if ( ! function_exists('wmvc_resolve_ip'))
{
    function wmvc_resolve_ip($ip)
    {
        if($ip == 'DISABLED')
            return $ip;

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            // $ip is a valid IP address"
        } else {
            return '';
        }

        if($ip == '::1')
        {
            return $ip.', local';
        }

        $str = '<a target="_blank" href="https://whatismyipaddress.com/ip/'.$ip.'">'.$ip.'</a>';
        
        return $str;
    }
}

/**
 * Stringify attributes for use in html tags.
 *
 * Helper function used to convert an array or object of
 * attributes to a string
 *
 * @param	mixed
 * @return	string
 */
if ( ! function_exists('wmvc_stringify_attributes'))
{
    function wmvc_stringify_attributes($attributes, $js = FALSE)
    {
        if (is_object($attributes) && count($attributes) > 0)
        {
            $attributes = (array) $attributes;
        }

        if (is_array($attributes))
        {
            $atts = '';
            if (count($attributes) === 0)
            {
                return $atts;
            }
            foreach ($attributes as $key => $val)
            {
                if ($js)
                {
                    $atts .= $key.'='.$val.',';
                }
                else
                {
                    $atts .= ' '.$key.'="'.$val.'"';
                }
            }
            return rtrim($atts, ',');
        }
        elseif (is_string($attributes) && strlen($attributes) > 0)
        {
            return ' '.$attributes;
        }

        return $attributes;
    }
}

if ( ! function_exists('wmvc_anchor'))
{
	/**
	 * Anchor Link
	 *
	 * Creates an anchor based on the local URL.
	 *
	 * @param	string	the URL
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function wmvc_anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		$site_url = is_array($uri)
			? site_url($uri)
			: (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri));

		if ($title === '')
		{
			$title = $site_url;
		}

		if ($attributes !== '')
		{
			$attributes = wmvc_stringify_attributes($attributes);
		}

		return '<a href="'.esc_url($site_url).'"'.wp_kses_post($attributes).'>'.wp_kses_post($title).'</a>';
	}
}


if ( ! function_exists('wmvc_btn_edit'))
{
    function wmvc_btn_edit($uri)
    {
        return wmvc_anchor($uri, '<i class="glyphicon glyphicon-pencil"></i>', array('class'=>'btn btn-success btn-xs'));
    }
}

if ( ! function_exists('wmvc_btn_read'))
{
    function wmvc_btn_read($uri, $title=NULL)
    {
        if(empty($title))$title=__('Read', 'wmvc_win');
        
        return wmvc_anchor($uri, '<i class="glyphicon glyphicon-search"></i> '.esc_html($title), array('class'=>'btn btn-primary btn-xs'));
    }
}

if ( ! function_exists('wmvc_btn_open'))
{
    function wmvc_btn_open($uri, $target=NULL)
    {
        if($target === NULL)
            $target = '_blank';

        return wmvc_anchor($uri, '<i class="glyphicon glyphicon-search"></i>', array('class'=>'btn btn-primary btn-xs', 'target'=>esc_html($target), 'title'=>esc_html__('Open details', 'wmvc_win')));
    }
}

if ( ! function_exists('wmvc_btn_open_ajax'))
{
    function wmvc_btn_open_ajax($uri, $target=NULL)
    {
        if($target === NULL)
            $target = '_blank';

        return wmvc_anchor($uri, '<i class="glyphicon glyphicon-search"></i>', array('class'=>'btn btn-primary btn-xs popup-with-form-ajax', 'target'=>esc_html($target), 'title'=>esc_html__('Open details', 'wmvc_win')));
    }
}

if ( ! function_exists('wmvc_btn_delete_noconfirm'))
{
    function wmvc_btn_delete_noconfirm($uri)
    {
        return wmvc_anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array('class'=>'btn btn-danger btn-xs delete_button'));
    }
}


if ( ! function_exists('wmvc_btn_delete'))
{
    function wmvc_btn_delete($uri, $confirm_question = TRUE, $title='')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '';
        }

        if($confirm_question)
        {
            return wmvc_anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array( 'target' => esc_html($target),  'title' => esc_html($title), 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')',  'class'=>'btn btn-danger btn-xs delete_button action_confirm'));
        }
        else
        {
            return wmvc_anchor($uri, '<i class="glyphicon glyphicon-remove"></i> ', array( 'target' => esc_html($target),  'title' => esc_html($title), 'class'=>'btn btn-danger btn-xs delete_button'));
        }
    }
}

if ( ! function_exists('wmvc_btn_save'))
{
    function wmvc_btn_save($uri, $empty = '-empty')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '';
        }

        return wmvc_anchor($uri, '<i class="glyphicon glyphicon-heart'.esc_attr($empty).'"></i> ', array( 'target' => esc_html($target), 'class'=>'btn btn-danger btn-xs save_button', 'title'=>esc_html__('Save', 'wmvc_win')));
    }
}

if ( ! function_exists('wmvc_btn_block'))
{
    function wmvc_btn_block($uri, $confirm_question = FALSE, $title='')
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '_blank';
        }

        if($confirm_question)
        {
            return wmvc_anchor($uri, '<i class="glyphicon glyphicon-lock"></i> ', array( 'target' => esc_attr($target), 'title' => esc_html($title), 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')', 'class'=>'btn btn-warning btn-xs block_button action_block'));
        }
        else
        {
            return wmvc_anchor($uri, '<i class="glyphicon glyphicon-lock"></i> ', array( 'target' => esc_attr($target), 'title' => esc_html($title), 'class'=>'btn btn-warning btn-xs block_button'));
        }
    }
}

if ( ! function_exists('wmvc_btn_view'))
{
    function wmvc_btn_view($uri, $confirm_question = FALSE, $title='')
    {
        if($confirm_question)
        {
            return wmvc_anchor($uri, '<i class="glyphicon glyphicon-search"></i> ', array( 'title' => esc_html($title), 'onclick' => 'return confirm(\''.__('Are you sure?', 'wmvc_win').'\')', 'class'=>'btn btn-info btn-xs'));
        }
        else
        {
            return wmvc_anchor($uri, '<i class="glyphicon glyphicon-search"></i> ', array( 'title' => esc_html($title), 'class'=>'btn btn-info btn-xs'));
        }
    }
}

if ( ! function_exists('wmvc_btn_hide'))
{
    function wmvc_btn_hide($uri)
    {
        $target = '';
        if(isset($_GET['popup']))
        {
            $target = '_blank';
        }

        return wmvc_anchor($uri, '<i class="glyphicon glyphicon-eye-close"></i> ', array( 'target' => esc_attr($target), 'class'=>'btn btn-default btn-xs', 'title'=>esc_html__('Define hide rules', 'wmvc_win')));
    }
}

if ( ! function_exists('wmvc_get_file_extension'))
{
    function wmvc_get_file_extension($filepath)
    {
        return substr($filepath, strrpos($filepath, '.')+1);
    }
}


if ( ! function_exists('wmvc_character_hard_limiter'))
{
    function wmvc_character_hard_limiter($string, $max_len)
    {
        if(strlen($string)>$max_len)
        {
            return substr($string, 0, $max_len-3).'...';
        }
        
        return $string;
    }
}


if(!function_exists('wmvc_count')) {
    function wmvc_count($mixed='') {
        $count = 0;
        
        if(!empty($mixed) && (is_array($mixed))) {
            $count = count($mixed);
        } else if(!empty($mixed) && function_exists('is_countable') && version_compare(PHP_VERSION, '7.3', '<') && is_countable($mixed)) {
            $count = count($mixed);
        }
        else if(!empty($mixed) && is_object($mixed)) {
            $count = 1;
        }
        return $count;
    }
}

function wmvc_is_user_in_role( $user, $role  ) {
    return in_array( $role, $user->roles );
}

function wmvc_is_logged_user()
{
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) {
        return false;
    } else {
        // Logged in.
        return true;
    }
}

function wmvc_get_current_user_role() {
    if( is_user_logged_in() ) {
      $user = wp_get_current_user();
      $role = ( array ) $user->roles;
      return $role[0];
    } else {
      return '';
    }
   }

function wmvc_user_in_role($role)
{
    if(!wmvc_is_logged_user())
        return false;
    
    $current_user = wp_get_current_user();
    
    return wmvc_is_user_in_role( $current_user, $role  );
}

function wmvc_character_limiter($text, $limit)
{
    if(is_array($text))
        $text = 'Array( '. join(', ', $text).' )';
        
    if(strlen($text) > $limit)
    {
        return substr($text, 0, $limit-4).'...';
    }

    return $text;
}

function wmvc_show_data($field_name, &$db_value = NULL, $default = '', $xss_clean = TRUE, $skip_post = FALSE)
{
    if(!$skip_post && isset($_POST[$field_name]))
    {
        if($xss_clean === FALSE)
            return stripslashes($_POST[$field_name]);

        return wmvc_xss_clean(stripslashes($_POST[$field_name]));
    }

    if(is_array($db_value))
    {
        if(isset($db_value[$field_name]))
        {
            if($xss_clean === FALSE)
                return $db_value[$field_name];

            return wmvc_xss_clean($db_value[$field_name]);
        }
        else
        {
            return $default;
        }
    }

    if(is_object($db_value))
    {
        if(isset($db_value->$field_name))
        {
            if($xss_clean === FALSE)
                return $db_value->$field_name;

            return wmvc_xss_clean($db_value->$field_name);
        }
        else
        {
            return $default;
        }
    }

    if(!empty($db_value))
    {
        if($xss_clean === FALSE)
            return $db_value;

        return wmvc_xss_clean($db_value);  
    }
        
    if($xss_clean === FALSE)
        return $default;

    return wmvc_xss_clean($default);
}


/*
<select class="form-control" name="<?php echo 'control_operator_'.$i_fieldnum; ?>">
    <option value="CONTAINS">CONTAINS</option>
    <option value="NOT_CONTAINS">NOT_CONTAINS</option>
</select>
*/
function wmvc_select_option($field_name, $options = array(), $selected = NULL, $extra=NULL, $empty_text = NULL, $empty_val = '')
{
    $output = '<select name="'.$field_name.'" '.$extra.' >';

    if(!is_null($empty_text))
        $output.= '<option value="'.esc_attr($empty_val).'">'.esc_html($empty_text).'</option>';

    if(is_array($options) && count($options) > 0)
    foreach($options as $key=>$val)
    {
        $output.= '<option value="'.$key.'" '.($selected==$key?'selected':'').'>'.$val.'</option>';
    }

    $output.= '</select>';

    return $output;
}

function wmvc_upload_media($field_name, $image_id)
{
    static $media_element_counter = 0;
        
    $media_element_counter++;
    
    $img_field = $field_name.'_'.$media_element_counter;
    
    wp_enqueue_script(  'wpmediaelement' );
    wp_enqueue_media();

    ?>
    <div id="<?php echo esc_attr($field_name); ?>meta-box-id" class="postbox-upload">
    <?php
    // Get WordPress' media upload URL
    $upload_link = '#';
    
    // Get the image src
    $your_img_src = wp_get_attachment_image_src( $image_id, 'full' );

    // For convenience, see if the array is valid
    $you_have_img = is_array( $your_img_src );
    ?>
    
    <!-- Your image container, which can be manipulated with js -->
    <div class="custom-img-container">
        <?php if ( $you_have_img ) : ?>
            <img src="<?php echo esc_html($your_img_src[0]); ?>" alt="<?php echo esc_attr__('thumb', 'wmvc_win');?>" style="max-width:100%;" class="thumbnail"/>
        <?php endif; ?>
    </div>
    
    <?php //if(sw_user_in_role('administrator')):  ?>
    <!-- Your add & remove image links -->
    <p class="hide-if-no-js">
        <a class="button button-primary upload-custom-img <?php if ( $you_have_img  ) { echo 'hidden'; } ?>" 
        href="<?php echo esc_url($upload_link) ?>">
            <?php echo esc_html__('Select image','wmvc_win') ?>
        </a>
        <a class="button button-secondary delete-custom-img <?php if ( ! $you_have_img  ) { echo 'hidden'; } ?>"
        href="#">
            <?php echo esc_html__('Remove image','wmvc_win') ?>
        </a>
    </p>
    <?php //endif; ?>
    
    <!-- A hidden input to set and post the chosen image id -->
    <input class="logo_image_id" type="hidden" id="<?php echo esc_html(esc_html($field_name)); ?>" name="<?php echo esc_html($field_name); ?>" value="<?php echo esc_html($image_id); ?>" />
    </div>
    
    <?php
    $custom_js ='';
    $custom_js .=" jQuery(function($) {
                        if( typeof jQuery.fn.wpMediaElement == 'function')
                            $('#".esc_js($field_name)."meta-box-id.postbox-upload').wpMediaElement();
                    });";
    
    echo "<script>".$custom_js."</script>";

    ?>

    <?php
}

function wmvc_upload_file($field_name, $file_id)
{
    static $media_element_counter = 0;
        
    $media_element_counter++;
    
    $img_field = $field_name.'_'.$media_element_counter;
    
    wp_enqueue_script(  'wpmediaelement' );
    wp_enqueue_media();

    ?>
    <div id="<?php echo esc_attr($field_name); ?>meta-box-id" class="postbox-upload">
    <?php
    // Get WordPress' media upload URL
    $upload_link = '#';
    
    // Get the file src
        
    // Get the file src
    $your_file_src = get_attached_file(intval( $file_id));

    // For convenience, see if the array is valid
    $you_have_file = false;
    if($your_file_src)
        $you_have_file = basename($your_file_src);

    ?>
    
    <!-- Your file container, which can be manipulated with js -->
    <div class="custom-img-container">
        <?php if ( $you_have_file ) : ?>
            <?php echo esc_html($you_have_file); ?>
        <?php endif; ?>
    </div>
    
    <?php //if(sw_user_in_role('administrator')):  ?>
    <!-- Your add & remove file links -->
    <p class="hide-if-no-js">
        <a class="upload-custom-img <?php if ( $you_have_file  ) { echo 'hidden'; } ?>" 
        href="<?php echo esc_url($upload_link) ?>">
            <?php echo esc_html__('Select file','wmvc_win') ?>
        </a>
        <a class="delete-custom-img <?php if ( ! $you_have_file  ) { echo 'hidden'; } ?>" 
        href="#">
            <?php echo esc_html__('Remove file','wmvc_win') ?>
        </a>
    </p>
    <?php //endif; ?>
    
    <!-- A hidden input to set and post the chosen file id -->
    <input class="file_id" type="hidden" id="<?php echo esc_html($field_name); ?>" name="<?php echo esc_html($field_name); ?>" value="<?php echo esc_html($file_id); ?>" />
    </div>
    
    <?php
    $custom_js ='';
    $custom_js .=" jQuery(function($) {
                        if( typeof jQuery.fn.wpMediaElement == 'function')
                            $('#".esc_js($field_name)."meta-box-id.postbox-upload').wpMediaElement({'imgIdInputSelector':'.file_id','isfileUpload':'true'});
                    });";
    
    echo "<script>".$custom_js."</script>";

    ?>

    <?php
}

function wmvc_upload_multiple($field_name, $image_ids='')
{
    static $media_element_counter = 0;
        
    $media_element_counter++;
    
    $img_field = $field_name.'_'.$media_element_counter;
    
    wp_enqueue_script(  'wpmediamultiple' );
    wp_enqueue_script(  'jquery-ui-mouse' );
    wp_enqueue_media();

    ?>
    <div id="<?php echo esc_attr($field_name); ?>meta-box-id" class="postbox-upload-multiple">
    <?php
    // Get WordPress' media upload URL
    $upload_link = '#';
    
    
    // Get the image src

    $your_img_src = array();

    foreach(explode(',', $image_ids) as $image_id)
    {
        if(is_numeric($image_id)){
            if(false)
                $your_img_src[$image_id] = wp_get_attachment_image_src( $image_id, 'full' );
            
            $your_img_src[$image_id] = wp_get_attachment_url( $image_id, 'full' );
        }
    }
    

    // For convenience, see if the array is valid
    $you_have_img = count($your_img_src) > 0;
    ?>

    <!-- Your image container, which can be manipulated with js -->
    <div class="custom-img-container winter_mvc-media">
        <?php if($you_have_img)foreach($your_img_src as $image_id => $img_src) : ?>
            <?php
            
            $filetype = wp_check_filetype(str_replace(WP_CONTENT_URL, WP_CONTENT_DIR, $img_src));
            if(strpos($filetype['type'], 'video') !== FALSE):?>
                <div class="winter_mvc-media-card" data-media-id="<?php echo esc_attr($image_id);?>">
                    <video src="<?php echo esc_html($img_src); ?>" controls alt="<?php echo esc_attr__('thumb', 'wmvc_win');?>" style="max-width:100%;" class="thumbnail"></video>
                    <a href="#" class="remove"></a>
                    <span href="#" class="move"><span class="dashicons dashicons-editor-expand"></span></span>
                </div>
            <?php else:?>
                <div class="winter_mvc-media-card" data-media-id="<?php echo esc_attr($image_id);?>">
                    <img src="<?php echo esc_html($img_src); ?>" alt="<?php echo esc_attr__('thumb', 'wmvc_win');?>" style="max-width:100%;" class="thumbnail"/>
                    <a href="#" class="remove"></a>
                </div>
            <?php endif;?>


        <?php endforeach; ?>
    </div>
    <br style="clear:both;" />
    
    <?php //if(sw_user_in_role('administrator')): ?>
    <!-- Your add & remove image links -->
    <p class="hide-if-no-js">
        <a class="button button-primary upload-custom-img <?php if ( $you_have_img  ) { echo ''; } ?>" 
        href="<?php echo esc_url($upload_link) ?>">
            <?php echo esc_html__('Add images','wmvc_win') ?>
        </a>
        <a class="button button-secondary delete-custom-img <?php if ( ! $you_have_img  ) { echo 'hidden'; } ?>" 
        href="#">
            <?php echo esc_html__('Remove images','wmvc_win') ?>
        </a>
    </p>
    <?php //endif; ?>
    
    <!-- A hidden input to set and post the chosen image id -->
    <input class="logo_image_id" type="hidden" id="<?php echo esc_html(esc_html($field_name)); ?>" name="<?php echo esc_html($field_name); ?>" value="<?php echo esc_html($image_ids); ?>" />
    </div>
    <?php
    $custom_js ='';
    $custom_js .=" jQuery(function($) {
                        if( typeof jQuery.fn.wpMediaMultiple == 'function')
                            $('#".esc_js($field_name)."meta-box-id.postbox-upload-multiple').wpMediaMultiple();
                            /* order */
                            var re_order = function(media_element){
                                var list_media = '';
                                media_element.find('.winter_mvc-media-card').each(function(){
                                    if(list_media !='')
                                        list_media +=',';

                                    list_media += $(this).attr('data-media-id');
                                })
                                media_element.closest('.postbox-upload-multiple').find('.logo_image_id').val(list_media);
                            }
                            /* Sort table */
                            $( '.winter_mvc-media' ).sortable({
                                update: function(event, ui) {
                                    re_order($(this));
                                }
                            });
                            /* remove media */
                            $( '.winter_mvc-media' ).find('.winter_mvc-media-card .remove').on('click', function(e){
                                e.preventDefault();
                                var media = $(this).closest('.winter_mvc-media')
                                $(this).closest('.winter_mvc-media-card').remove();
                                re_order(media)
                            })
                        });
                    ";
    
    echo "<script>".$custom_js."</script>";

    ?>

    <?php
}

function wmvc_select_radio($field_name, $options = array(), $selected = NULL)
{
    $output = ''; //'<div class="radio"> <select name="'.$field_name.'" '.$extra.' >';

    if(is_array($options) && count($options) > 0)
    foreach($options as $key=>$val)
    {
        $output.= '<div class="radio">';
        $output.= '<label>';
        $output.= '<input type="radio" name="'.esc_attr($field_name).'" id="optionsRadios1_format" value="'.esc_attr($key).'" '.($selected==$key?'checked':'').' />';
        $output.= esc_html($val);
        $output.= '</label>';
        $output.= '</div>';
    }

    return $output;
}


function wmvc_form_messages()
{
    echo 'ok';
}

function wmvc_xss_clean($data)
{
    //if($data == '0000-00-00 00:00:00')return '';
    if(is_array($data))
        return wmvc_xss_clean_array($data);

    if(is_object($data))
        return wmvc_xss_clean_object($data);

    if($data === NULL)
        return '';

    // Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:framXe|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);

    //$data = sanitize_textarea_field($data);

    // we are done...
    return $data;
}

function wmvc_xss_clean_array($array)
{
    if(!is_array($array))return array();

    $arr_cleaned = array();
    foreach($array as $key=>$val)
    {
        if($key == 'newcontent')
        {
            $arr_cleaned[wmvc_xss_clean($key)] = $val;
        }
        else
        {
            $arr_cleaned[wmvc_xss_clean($key)] = wmvc_xss_clean($val);
        }
        
    }

    //dump($arr_cleaned);

    return $arr_cleaned;
}

function wmvc_xss_clean_object($object)
{
    if(!is_object($object))return NULL;

    $array = get_object_vars($object);
    foreach($array as $key=>$val)
    {
        $object->{$key} = wmvc_xss_clean($object->{$key});
    }

    return $object;
}

function wmvc_roles_array() {
    $editable_roles = get_editable_roles();
    foreach ($editable_roles as $role => $details) {
        $sub['role'] = esc_attr($role);
        $sub['name'] = translate_user_role($details['name']);
        $roles[] = $sub;
    }
    return $roles;
}

function wmvc_seconds_to_hms($seconds, $show_days=true) {
    $t = round($seconds);
    $days = floor($t/86400);
    $day_sec = $days*86400;
    $hours = floor( ($t-$day_sec) / (60 * 60) );
    $hour_sec = $hours*3600;
    $minutes = floor((($t-$day_sec)-$hour_sec)/60);
    $min_sec = $minutes*60;
    $sec = (($t-$day_sec)-$hour_sec)-$min_sec;

    if($show_days)
    {
        return sprintf('%02d:%02d:%02d:%02d', $days, $hours, $minutes, $sec);
    }
    
    // to show only hours, no days
    $hours = floor( ($t) / (60 * 60) );
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $sec);
}

function wmvc_xml_encode($array)
{

    if (is_null($DOMDocument)) {
        $DOMDocument =new DOMDocument;
        $DOMDocument->formatOutput = true;
        wmvc_xml_encode($mixed, $DOMDocument, $DOMDocument);
        echo $DOMDocument->saveXML();
    }
    else {
        // To cope with embedded objects 
        if (is_object($mixed)) {
          $mixed = get_object_vars($mixed);
        }
        if (is_array($mixed)) {
            foreach ($mixed as $index => $mixedElement) {
                if (is_int($index)) {
                    if ($index === 0) {
                        $node = $domElement;
                    }
                    else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                }
                else {
                    $plural = $DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node = $plural;
                    if (!(rtrim($index, 's') === $index)) {
                        $singular = $DOMDocument->createElement(rtrim($index, 's'));
                        $plural->appendChild($singular);
                        $node = $singular;
                    }
                }

                wmvc_xml_encode($mixedElement, $node, $DOMDocument);
            }
        }
        else {
            $mixed = is_bool($mixed) ? ($mixed ? 'true' : 'false') : $mixed;
            $domElement->appendChild($DOMDocument->createTextNode($mixed));
        }
    }
}

function wmvc_add_wp_image($filename_source, $parent_post_id = 0)
{
    $file = $filename_source;
    $filename = basename($file);

    $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
    if (!$upload_file['error']) {
    	$wp_filetype = wp_check_filetype($filename, null );
    	$attachment = array(
    		'post_mime_type' => $wp_filetype['type'],
    		'post_parent' => $parent_post_id,
    		'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
    		'post_content' => '',
    		'post_status' => 'inherit'
    	);
    	$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
    	if (!is_wp_error($attachment_id)) {
    		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
    		wp_update_attachment_metadata( $attachment_id,  $attachment_data );
            
            return $attachment_id;
    	}
    }
    
    return NULL;
}

function wmvc_wp_paginate($total_items, $per_page = 10, $page_var = 'paged', $texts = array())
{
    $current_page = 1;
    
    if(isset($_GET[$page_var]))
        $current_page = intval(wmvc_xss_clean($_GET[$page_var]));

    if(!isset($texts['previous_page']))$texts['previous_page'] = 'Previous page';
    if(!isset($texts['next_page']))$texts['next_page'] = 'Next page';
    if(!isset($texts['first_page']))$texts['first_page'] = 'First page';
    if(!isset($texts['last_page']))$texts['last_page'] = 'Last page';
    if(!isset($texts['items']))$texts['items'] = 'items';

    if(empty($current_page))$current_page = 1;

    // get url
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    $qs_parameters = wmvc_xss_clean( $_GET );
    unset($qs_parameters[$page_var]);
    
    $qs_part = http_build_query($qs_parameters);
    $url.='?'.$qs_part;

    // total pages
    $total_pages = intval($total_items/$per_page+0.99);
    $output = '';

    $output.= '<div class="tablenav-pages"><span class="displaying-num">'.esc_html($total_items).' '.esc_html($texts['items']).'</span>';
    $output.= '<span class="pagination-links">';
    
    if($current_page != 1)
        $output.= '<a class="first-page button" href="'.esc_url($url).'"><span class="screen-reader-text">'.esc_html($texts['first_page']).'</span><span aria-hidden="true">«</span></a>';
   
    if($current_page-1 > 0)
    {
        $output.= '<a class="prev-page button" href="'.esc_url($url).'&amp;'.esc_attr($page_var).'='.esc_attr(($current_page-1)).'"><span class="screen-reader-text">'.esc_html($texts['previous_page']).'</span><span aria-hidden="true">‹</span></a>';
    }
    else
    {
        $output.= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
    }

    $output.= '<span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">'.esc_html($current_page).' of <span class="total-pages">'.esc_html(($total_pages) ? $total_pages : 1).'</span></span></span>';

    if($current_page+1 <= $total_pages)
    {
        $output.= '<a class="next-page button" href="'.esc_url($url).'&amp;'.esc_attr($page_var).'='.esc_attr(($current_page+1)).'"><span class="screen-reader-text">'.esc_html($texts['next_page']).'</span><span aria-hidden="true">›</span></a>';
    
    }
    else
    {
        $output.= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>';
    }

    if($current_page != $total_pages)
        $output.= '<a class="last-page button" href="'.esc_url($url).'&amp;'.esc_attr($page_var).'='.esc_attr($total_pages).'"><span class="screen-reader-text">'.esc_html($texts['last_page']).'</span><span aria-hidden="true">»</span></a>';
    
    $output.= '</span></div>';

    return $output;
}

function wmvc_download_file($url, $save_file_loc, $data = array())
{   
    // Initialize the cURL session 
    $ch = curl_init($url); 
    
    // Use basename() function to return 
    // the base name of file  
    $file_name = basename($url); 
    
    if(!file_exists(dirname($save_file_loc)))
        mkdir(dirname($save_file_loc));

    // Open file  
    $fp = fopen($save_file_loc, 'wb'); 
    
    // It set an option for a cURL transfer 
    curl_setopt($ch, CURLOPT_FILE, $fp); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: multipart/form-data',
     ));
    
    //Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
    //setting them to false.
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Perform a cURL session 
    $result = curl_exec($ch); 

    if(curl_errno($ch))
    {
        //exit(curl_error($ch));
    }

    // Closes a cURL session and frees all resources 
    curl_close($ch); 
    
    // Close file 
    fclose($fp); 

    return $result;
}

function wmvc_api_call($method, $url, $data, $headers = false){
    $curl = curl_init();

    //$data = 'email=test';

    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
          {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            if($headers === FALSE)
                $headers = array('Content-Type: multipart/form-data');
          }
             
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    if(!$headers){
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
        ));
    }else{
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    //Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
    //setting them to false.
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){return FALSE;}
    curl_close($curl);
    return $result;
}

function wmvc_current_edit_url()
{
    $query_string_array = wmvc_xss_clean( $_GET );
    unset($query_string_array['is_updated']);

    return admin_url("admin.php?".http_build_query($query_string_array));
}

function wmvc_stripslashes_deep($value)
{
    $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

    return $value;
}

function wmvc_get_date($datetime = NULL, $default='timestamp') 
{
    if(is_null($datetime))
    {
        if($default == 'timestamp')
        {
            $datetime = current_time('timestamp');
        }
        else
        {
            return $default;
        }
    }
    else if(!is_numeric($datetime))
    {
        $datetime = strtotime($datetime);
    }
    
	$date_format = get_option('date_format');
	$time_format = get_option('time_format');
	$date = date("{$date_format} {$time_format}", $datetime);
	return $date;
}

if ( ! function_exists('wmvc_url_suffix'))
{
	function wmvc_url_suffix($base_url, $extension_url="")
	{
        if(strpos($base_url,'?') !== FALSE){
            $base_url .='&';
        } else {
            $base_url .='?';
        }
        return  $base_url.$extension_url;
	}
}


if ( ! function_exists('wmvc_filter_decimal'))
{
	function wmvc_filter_decimal($string = '')
	{
        if(substr($string, -3, 3) == '.00') {
            return substr($string, 0, -3);
        }

        return $string;
	}
}

if(!function_exists('wmvc_get_option')) {
    /**
	 * Cached and get wp options
	 *
	 * @param      string    option       Option key
	 * @return     string    alt or title
	 */

	function wmvc_get_option($option_key = '') {
        return get_option($option_key);
        
        if(isset($options[$option_key])) {
            return $options[$option_key];
        }

        $options[$option_key] = get_option($option_key);

		return $options[$option_key];
	}
}

if ( ! function_exists('wmvc_is_phone'))
{
    // Validation phone "-+()0123456789"
    /**
    * @param string $value phone in string
    * @return bool
    */

    
    function wmvc_is_phone($value = '') {
        if(preg_match("/^[.+]{0,1}[0-9-)(]{0,25}$/", $value)) {
            return true;
        }
        
        return false;
    }	
}	


?>