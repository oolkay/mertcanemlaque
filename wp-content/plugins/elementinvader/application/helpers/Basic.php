<?php

function elementinvader_template_data($template, $key, $data = array())
{

    if (file_exists(get_template_directory().'/elementinvader/'.$template.'/description.xml') || file_exists(get_template_directory().'/elementinvader/'.$template.'/description.pac')) {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
            WP_Filesystem();
        }

        if (file_exists(get_template_directory().'/elementinvader/'.$template.'/description.pac')) {
            $xml_data =  $wp_filesystem->get_contents(get_template_directory().'/elementinvader/'.$template.'/description.pac');
        } else {
            $xml_data =  $wp_filesystem->get_contents(get_template_directory().'/elementinvader/'.$template.'/description.xml');
        }

        $xml_data = str_replace('&','&amp;', str_replace('&amp;','&',$xml_data));
        $xml = simplexml_load_string($xml_data);
        
        if(isset($xml->$key))
            return $xml->$key;
    }

    if(strpos($template, 'download_kit_') === 0)
    {
        $online_kit_id = str_replace('download_kit_', '', $template);

        $download_url = ELEMENTINVADER_WEBSITE.'elementor/download_package/'.$online_kit_id;

        if(!file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'))
            mkdir(WP_CONTENT_DIR.'/uploads/elementinvader/');

         if(file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package.zip'))
         {
            if(filesize(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package.zip') < 1000)
            {
                unlink(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package.zip');
            }
         }            
        
        if(!file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/description.xml'))
        {
            $res = wmvc_download_file($download_url, WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package.zip', $data);
        }

        if(file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package.zip'))
        {
            $zip = new ZipArchive;
            if ($zip->open(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package.zip') === TRUE) {
                $zip->extractTo(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/');
                $zip->close();

                if(file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/description.xml'))
                {
                    global $wp_filesystem;
                    if (empty($wp_filesystem)) {
                        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
                        WP_Filesystem();
                    }
                    $xml_data =  $wp_filesystem->get_contents(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/description.xml');
                    $xml_data = str_replace('&','&amp;', str_replace('&amp;','&',$xml_data));
                    $xml = simplexml_load_string($xml_data);
                    
                    if(isset($xml->$key))
                        return $xml->$key;
                }
            }
        }
    }

    return '-';
}

/* Elementor Import */

function elementinvader_elementor_import_templates($path, $default_template = 'templates/template-full-width.php')
{

    $templates = array();

    if (is_dir($path)){
        if ($dh = opendir($path)){
          while (($file = readdir($dh)) !== false){
              if(strrpos($file, ".json") !== FALSE)
              {
                $file_name = pathinfo($path.$file, PATHINFO_FILENAME);
                $templates[$file_name] = $path.$file;
              }
          }
          closedir($dh);
        }
      }

    if(count($templates) > 0)
        $importer = new Elementor_Template_Importer(
            $templates,
            $args = array(
                'set_default_template' => True,
                'default_page_template' => $default_template,
            )
        );

}

function elementinvader_upload_files($template)
{
    global $wp_filesystem;
    // Initialize the WP filesystem, no more using 'file-put-contents' function
    if (empty($wp_filesystem)) {
        WP_Filesystem();
    }
    
    $online_kit_id = NULL;

    if(strpos($template, 'download_kit_') === 0)
    {
        $online_kit_id = str_replace('download_kit_', '', $template);

        $file_content = $wp_filesystem->get_contents(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/template.json');
    }
    elseif(strpos($template, 'export_') === 0 && file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'.$template.'/template.json'))
    {
        $file_content = $wp_filesystem->get_contents(WP_CONTENT_DIR.'/uploads/elementinvader/'.$template.'/template.json');
    }
    else
    {
        $file_content = $wp_filesystem->get_contents(get_template_directory() . '/elementinvader/'.$template.'/template.json');
    }

    // detect all links in format http*"
    $pattern = '/"http(.*?)"/s';
    preg_match_all($pattern, $file_content, $matches);

    $image_urls = array();
    $image_filenames = array();
    foreach($matches[1] as $match)
    {
        if(strpos($match, 'wp-content') !== FALSE)
        {
            $image_urls[] = 'http'.$match;

            $img_filename = substr($match, strrpos($match, '\\/')+2);
            $image_filenames[] = $img_filename;
        }
    }
    
    // upload files

    $for_replace = array();
    foreach($image_filenames as $key => $filename)
    {
        if(file_exists(get_template_directory() . '/elementinvader/'.$template.'/images/'.$filename))
        {
            $image_id = wmvc_add_wp_image(get_template_directory() . '/elementinvader/'.$template.'/images/'.$filename);
            $image_url = wp_get_attachment_url($image_id);
            $for_replace[$image_urls[$key]] = array('url' =>str_replace('/', '\/', $image_url), 'id' => $image_id);
        }
        elseif(file_exists(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/images/'.$filename))
        {
            $image_id = wmvc_add_wp_image(WP_CONTENT_DIR.'/uploads/elementinvader/'.$online_kit_id.'_package/images/'.$filename);
            $image_url = wp_get_attachment_url($image_id);
            $for_replace[$image_urls[$key]] = array('url' =>str_replace('/', '\/', $image_url), 'id' => $image_id);
        }
        else
        {
            //echo 'NOT FOUND: '.$filename."<br />";
        }
    }

    return $for_replace;
}

function elementinvader_export_add_files($template)
{
    global $wp_filesystem;
    // Initialize the WP filesystem, no more using 'file-put-contents' function
    if (empty($wp_filesystem)) {
        WP_Filesystem();
    }
    
    $file_content = $wp_filesystem->get_contents(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$template.'/template.json');

    /* Fisrt Detect images */
    // detect all links in format http*"
    $pattern = '/image":(.*?)}/s';
    preg_match_all($pattern, $file_content, $matches);
    
    $image_urls = array();
    $image_filenames = array();
    $image_paths = array();
    $image_extern_paths = array();

    foreach($matches[1] as $key => $match)
    {
        if(strpos($match, 'wp-content') !== FALSE)
        {
            $image_id = substr($match, strrpos($match, '"id":')+5);

            if(strpos($image_id, ',') !== FALSE)
                $image_id = substr($image_id, 0, strpos($image_id, ','));


            $original_image_path = NULL;
            $original_image_url  = NULL;
            if(!is_numeric($image_id))
            {
                $image_id = NULL;
            }
            else
            {
                $original_image_path = wp_get_original_image_path($image_id);
                $original_image_url  = wp_get_original_image_url($image_id);

                if(!file_exists($original_image_path))
                {
                    $original_image_path = NULL;
                    $original_image_url = NULL;
                }
            }

            $match = substr($match, strpos($match, 'http')+4);
            $match = substr($match, 0, strpos($match, '"'));

            if($original_image_url !== NULL)
            {
                $image_urls[$key] = $original_image_url;
            }
            else
            {
                $image_urls[$key] = 'http'.$match;
            }

            if($original_image_path !== NULL)
            {
                $image_paths[$key] = substr($original_image_path, strlen(WP_CONTENT_DIR)+1);
            } else {
                $img_path = substr($match, strrpos($match, '\\/uploads')+2);
                $img_path = str_replace('\\/', '/', $img_path);
    
                $image_paths[$key] = $img_path;
            }
            
            $img_filename = substr($match, strrpos($match, '\\/')+2);
            $image_filenames[$key] = $img_filename;
        } else {
            /* extern files */
            $match = substr($match, strpos($match, 'http')+4);
            $match = substr($match, 0, strpos($match, '"'));

            $image_urls[$key] = 'http'.$match;
            $image_extern_paths[$key] = 'http'.str_replace('\\/', '/', $match);
        }
    }

    /* Second Detect images */
    $pattern = '/"id":\s*(\d+),\s*"url":\s*"([^"]+)"/';
    preg_match_all($pattern, $file_content, $matches, PREG_SET_ORDER);
    /* Extracted example $matches => 
                                [0] : ""id":3708,"url":"https:\/\/wpdirectorykit.com\/demo_data\/real-estate-villa\/images_pac\/gallery\/gallery_image_2.jpg"",
                                [1]=> "3708",
                                [2]=> "https:\/\/wpdirectorykit.com\/demo_data\/real-estate-villa\/images_pac\/gallery\/gallery_image_2.jpg"
    */                            
    foreach($matches as $k => $match)
    {
        if(in_array($match[2], $image_urls) || in_array($match[2], $image_extern_paths) 
            || in_array(str_replace('\\/', '/', $match[2]), $image_urls) || in_array(str_replace('\\/', '/', $match[2]), $image_extern_paths)) 
                continue;
        $key++;

        if(strpos($match[2], 'wp-content') !== FALSE)
        {
            $image_id = $match[1];

            $original_image_path = NULL;
            $original_image_url  = NULL;
            if(!is_numeric($image_id))
            {
                $image_id = NULL;
            }
            else
            {
                $original_image_path = wp_get_original_image_path($image_id);
                $original_image_url  = wp_get_original_image_url($image_id);

                if(!file_exists($original_image_path))
                {
                    $original_image_path = NULL;
                    $original_image_url = NULL;
                }
            }

            $url = substr($match[2], strpos($match[2], 'http')+4);

            if($original_image_url !== NULL)
            {
                $image_urls[$key] = $original_image_url;
            }
            else
            {
                $image_urls[$key] = 'http'.$url;
            }

            if($original_image_path !== NULL)
            {
                $image_paths[$key] = substr($original_image_path, strlen(WP_CONTENT_DIR)+1);
            } else {
                $img_path = substr($url, strrpos($url, '\\/uploads')+2);
                $img_path = str_replace('\\/', '/', $img_path);
    
                $image_paths[$key] = $img_path;
            }
            
            $img_filename = substr($url, strrpos($url, '\\/')+2);
            $image_filenames[$key] = $img_filename;
        } else {
            /* extern files */
            $url = substr($match[2], strpos($match[2], 'http')+4);
            $image_urls[$key] = 'http'.$url;
            $image_extern_paths[$key] = 'http'.str_replace('\\/', '/', $url);
        }
    }
    
    /* 3th Detect images */
    $pattern = '/"url":\s*"([^"}]+?\.(?:jpg|jpeg|png|gif))"/';
    preg_match_all($pattern, $file_content, $matches);

    // Extracted URLs are in $matches[1]
    if(!empty($matches[1])) {
        foreach($matches[1] as $k => $url){
            if(in_array($url, $image_urls) || in_array($url, $image_extern_paths) || in_array(str_replace('\\/', '/', $url), $image_urls) || in_array(str_replace('\\/', '/', $url), $image_extern_paths)) continue;
            $key++;

            if(strpos($url, 'wp-content') !== FALSE)
            {
                $url = substr($url, strpos($url, 'http')+4);
                $image_urls[$key] = 'http'.$url;
    
                $img_path = substr($url, strrpos($url, '\\/uploads')+2);
                $img_path = str_replace('\\/', '/', $img_path);
    
                $image_paths[$key] = $img_path;
                
                $img_filename = substr($url, strrpos($url, '\\/')+2);
                $image_filenames[$key] = $img_filename;
            } else {
    
                /* extern files */
                $url = substr($url, strpos($url, 'http')+4);

                $image_urls[$key] = 'http'.$url;
                $image_extern_paths[$key] = 'http'.$url;
            }
        }
    }

    // remove files
    if(file_exists((WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$template.'/images/')))
    if ($handle = opendir(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$template.'/images/'))
    {
        while (false !== ($file = readdir($handle)))
        {
            if( is_file(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$template.'/images/'.$file) )
            {
                echo 'Remove file: '.WP_CONTENT_DIR.'/uploads/elementinvader/export_'.esc_html($template.'/images/'.$file).'<br />';

                unlink(WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$template.'/images/'.$file);
            }
        }
        closedir($handle);
    }

    // copy files

    $missing_images = array();

    $for_replace = array();
    foreach($image_paths as $key => $path)
    {
        if(!file_exists(WP_CONTENT_DIR.'/'.$path)) {
            $missing_images[] = WP_CONTENT_DIR.'/'.$path;
            continue;
        }
        echo 'Copy file: '.WP_CONTENT_DIR.'/'.esc_html($path).'<br />';
        copy(WP_CONTENT_DIR.'/'.$path, WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$template.'/images/'.$image_filenames[$key]);

        $for_replace[$image_urls[$key]] = str_replace('/', '\/', $image_urls[$key]);
    }

    $for_replace = array();
    foreach($image_extern_paths as $key => $file_url)
    {
        $file_url = str_replace('\\/', '/', $file_url);

        $file_name = basename($file_url);
        $destination_path = WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$template.'/images/' . $file_name;

        /* same file names */
        $i = 0;
        while (file_exists($destination_path) && $i<10) {
            // If the file exists, generate a new unique name
            $file_name = uniqid() . '-' . $file_name;
            $destination_path = WP_CONTENT_DIR.'/uploads/elementinvader/export_'.$template.'/images/' . $file_name;
            $i++;
        }

        $response = wp_remote_get($file_url);
        if (!is_wp_error($response) && $response['response']['code'] === 200) {
            $saved = file_put_contents($destination_path, $response['body']);
        
            if ($saved !== false) {
                echo 'Download file: '.esc_url($file_url).'<br />';
            } else {
                echo 'Error on download file: '.esc_url($file_url).'<br />';
            }
        } else {
            $missing_images[] = $file_url;
            continue;
        }

        $for_replace[$file_url] = str_replace('/', '\/', content_url().'/uploads/elementinvader/export_'.$template.'/images/'.$file_name);
        $for_replace[$image_urls[$key]] = str_replace('/', '\/', content_url().'/uploads/elementinvader/export_'.$template.'/images/'.$file_name);
        
    }
    
    if(!empty( $missing_images)) {
        echo '<div style="color:red;">';
        echo '<br /><b>'.esc_html__('Missing image','elementinvader').'</b>';
        foreach ($missing_images as $image_src) {
            echo '<br /><b> Image not found:</b> '.esc_html($image_src).'';
        }
        echo '<br /><p>'.esc_html__('Missing images file you can add into zip manually, inside images folder','elementinvader').'</p>';
        echo '</div>';
    }

    return $for_replace;
}

function elementinvader_elementor_assign($page_id, $json_template_file, $replace_array = array())
{
    if(!file_exists($json_template_file) || !class_exists('Elementor\Plugin'))
    {
        return false;
    }

    $page_template =  get_page_template_slug( $page_id );

    add_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
    //add_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );

    global $wp_filesystem;
    // Initialize the WP filesystem, no more using 'file-put-contents' function
    if (empty($wp_filesystem)) {
        WP_Filesystem();
    }
    
    $string =  $wp_filesystem->get_contents($json_template_file);

    // replace by $replace_array
    
    if(count($replace_array) > 0)
    {
        foreach($replace_array as $key=>$rep)
        {
            $string = str_replace($key, $rep['url'], $string);

            
            $offset = 0;
            $allpos = array();
            while (($pos = strpos($string, $rep['url'], $offset)) !== FALSE) {
                $offset   = $pos + 1;
                
                // pos is url position start

                $start_id = strrpos(substr($string, 0, $offset), '{')+1;

                $end_id = strpos($string, '}', $offset);

                $str_id_find = substr($string, $start_id, $end_id-$start_id);

                if(strpos($str_id_find, '"id":') !== FALSE)
                {
                    $string = str_replace($str_id_find , '"id":'.$rep['id'].', "url":"'.$rep['url'].'"', $string);
                }
            }
        }
    }
    
    /* replace "{sw_file:/assets/images/resources/vid-img.jpg}" to attached image */
    preg_match_all('/{sw_file\:(.*?)\}/is', $string, $matches);
    if(!empty($matches[0])) {
        foreach ($matches[1] as $key => $value) {
            $post_image_src_id = sw_add_wp_image(get_template_directory().$value);
            $post_image_src = wp_get_attachment_image_src($post_image_src_id, 'full');

            $r_str = '{"url":"'.$post_image_src[0].'","id":'.$post_image_src_id.'}';
            $string = str_replace('"'.$matches[0][$key].'"', $r_str, $string);
        }
    }
    
    // for test, generated replaced file
    // $wp_filesystem->put_contents( $json_template_file.'.ch.json', $string); 

    $json_template = json_decode($string, true);

    if($json_template === NULL) return FALSE;

    $elements = $json_template['content'];

    $data = array(
        'elements' => $elements,
        'settings' => array('post_status'=>'autosave', 'template'=>$page_template),
    );   
    // @codingStandardsIgnoreStart
    $document = Elementor\Plugin::$instance->documents->get( $page_id, false );
    // @codingStandardsIgnoreEnd

    /* reCreate KIt */
    $kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();
    if(empty($kit->get_id())){
        $created_default_kit = Elementor\Plugin::$instance->kits_manager->create_default();
        if($created_default_kit)
            update_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, $created_default_kit );
    }

    return $document->save( $data );
}



if ( ! function_exists('elementinvader_update_page'))
{
    function elementinvader_update_page($post_ID, $post_content, $post_template)
    {
        
        if(!is_numeric($post_ID))
        {
            $post = get_page_by_title($post_ID, 'OBJECT', 'page' );
            
            if(!empty($post))
            $post_ID   = $post->ID;
        }
        
        
        $my_post = array(
            'ID'           => $post_ID,
            'page_template'=> $post_template,
            'post_content' => $post_content,
        );
    
        // Update the post into the database
        $post_insert = wp_update_post( $my_post );
        
        if (is_wp_error($post_ID)) {
            $errors = $post_ID->get_error_messages();
            foreach ($errors as $error) {
                echo esc_html($error);
            }
        }
        
        return $post_insert;
    }
}

if ( ! function_exists('elementinvader_create_page'))
{
    function elementinvader_create_page($post_title, $post_content = '', $post_template = NULL, $post_parent=0)
    {
        //$post_title = __('Register / Login', 'elementinvader');
        $post      = get_page_by_title($post_title, 'OBJECT', 'page' );
        
        $post_id = NULL;
        
        // Delete posts and rebuild
        if(!empty($post))
        {
            wp_delete_post($post->ID, true);
            $post=NULL;
        }
        
        if(!empty($post))
        $post_id  = $post->ID;

        if(empty($post_id))
        {
            $error_obj = NULL;
            $post_insert = array(
                'post_title'    => wp_strip_all_tags( $post_title ),
                'post_content'  => $post_content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_author'   => get_current_user_id(),
                'post_category' => array(1,2),
                'page_template' => $post_template,
                'post_parent'   => $post_parent
            );
            $post_id = wp_insert_post( $post_insert, $error_obj );
        }

        $post_insert = get_post( $post_id );
        
        return $post_insert;
    }
}

if ( ! function_exists('elementinvader_get_menu_item_by_title'))
{
    function elementinvader_get_menu_item_by_title($menu_name, $title)
    {        
        $array_menu = wp_get_nav_menu_items($menu_name);
        
        $menu = array();
        foreach ($array_menu as $m) {
            if (empty($m->menu_item_parent)) {
                $menu[$m->ID] = array();
                $menu[$m->ID]['ID']          =   $m->ID;
                $menu[$m->ID]['title']       =   $m->title;
                $menu[$m->ID]['url']         =   $m->url;
                $menu[$m->ID]['children']    =   array();
                
                if(isset($menu[$m->ID]['title']) && $menu[$m->ID]['title'] == $title)
                    return $m;
            }
        }
        $submenu = array();
        foreach ($array_menu as $m) {
            if ($m->menu_item_parent) {
                $submenu[$m->ID] = array();
                $submenu[$m->ID]['ID']       =   $m->ID;
                $submenu[$m->ID]['title']    =   $m->title;
                $submenu[$m->ID]['url']      =   $m->url;
                $menu[$m->menu_item_parent]['children'][$m->ID] = $submenu[$m->ID];
                
                if(isset($menu[$m->ID]['title']) && $menu[$m->ID]['title'] == $title)
                    return $m;
            }
        }

        return NULL;
    }
}

function elementinvader_run_activate_plugin( $plugin ) {
    $current = get_option( 'active_plugins' );
    $plugin = plugin_basename( trim( $plugin ) );

    if ( !in_array( $plugin, $current ) ) {
        $current[] = $plugin;
        sort( $current );
        do_action( 'activate_plugin', trim( $plugin ) );
        update_option( 'active_plugins', $current );
        do_action( 'activate_' . trim( $plugin ) );
        do_action( 'activated_plugin', trim( $plugin) );
    }

    return null;
}

function elementinvader_loggedin()
{
    $api_key = get_option('elementinvader_api_token', false);

    return $api_key !== FALSE;
}


if ( ! function_exists('is_elementinvader_size_640'))
{
    function is_elementinvader_size_640($param)
    {   
        if(!empty($param))
        {
            $image = wp_get_attachment_metadata(intval($param));
            if($image && $image['width'] == 640) return TRUE;
        }

        return FALSE;
    }
}

if ( ! function_exists('is_elementinvader_size_1280'))
{
    function is_elementinvader_size_1280($param)
    {   
        if(!empty($param))
        {
            $image = wp_get_attachment_metadata(intval($param));
            if($image && $image['width'] == 1280) return TRUE;
        }

        return FALSE;
    }
}

if ( ! function_exists('is_elementinvader_size_maxheight_2000'))
{
    function is_elementinvader_size_maxheight_2000($param)
    {   
        if(!empty($param))
        {
            $image = wp_get_attachment_metadata(intval($param));
            if($image && $image['height'] <= 2000) return TRUE;
        }

        return FALSE;
    }
}

if ( ! function_exists('is_elementinvader_size_maxheight_4000'))
{
    function is_elementinvader_size_maxheight_4000($param)
    {   
        if(!empty($param))
        {
            $image = wp_get_attachment_metadata(intval($param));
            if($image && $image['height'] <= 4000) return TRUE;
        }

        return FALSE;
    }
}

?>