<?php

/**
 * MVC_Input
 *
 * @version 1.0
 *
 * @author Sandi Winter
 * @link https://github.com/sandiwinter/winter_mvc
 */
if ( ! class_exists( 'MVC_Input' ) ):

class MVC_Input {

    public function __construct()
    {
	}

    public function post($name = NULL)
    {
        if($name === NULL)
            return wmvc_xss_clean_array(wmvc_stripslashes_deep($_POST));

        if(isset($_POST[$name]))
        {
            return wmvc_xss_clean(wmvc_stripslashes_deep($_POST[$name]));
        }

        return NULL;
    }

    public function get($name = NULL)
    {
        if($name === NULL)
            return wmvc_xss_clean_array($_GET);

        if(isset($_GET[$name]))
        {
            return wmvc_xss_clean($_GET[$name]);
        }

        return NULL;
    }

    public function post_get($name = NULL)
    {
        if($name === NULL)
            return array_merge($_POST, $_GET);

        if(isset($_POST[$name]))
        {
            return wmvc_xss_clean($_POST[$name]);
        }

        if(isset($_GET[$name]))
        {
            return wmvc_xss_clean($_GET[$name]);
        }

        return NULL;
    }

}

endif;

?>