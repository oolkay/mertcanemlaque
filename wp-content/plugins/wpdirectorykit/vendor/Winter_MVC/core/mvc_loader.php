<?php

/**
 * MVC_Loader
 *
 * @version 1.0
 *
 * @author Sandi Winter
 * @link https://github.com/sandiwinter/winter_mvc
 */
if ( ! class_exists( 'MVC_Loader' ) ):
#[AllowDynamicProperties]
class MVC_Loader {

    /**
     * controllers_repository array
     *
     * @var array
     */
    protected $controllers_repository = array();

    /**
     * models_repository array
     *
     * @var array
     */
    protected $models_repository = array();

    /**
     * db object
     *
     * @var object
     */
    public $db = NULL;

    public $plugin_directory = NULL;

    public function __construct($plugin_directory = NULL)
    {
        $this->plugin_directory = $plugin_directory;

        require_once WINTER_MVC_PATH.'/core/helpers.php';
        require_once WINTER_MVC_PATH.'/core/input.php';
        require_once WINTER_MVC_PATH.'/core/form.php';
        require_once WINTER_MVC_PATH.'/core/database.php';
        require_once WINTER_MVC_PATH.'/core/winter_mvc_model.php';
        require_once WINTER_MVC_PATH.'/core/winter_mvc_controller.php';

        add_action('admin_init', array( $this, 'register_styles' ));
        add_action('admin_init', array( $this, 'register_scripts' ));

        $this->db = MVC_Database::instance();
    }

    public function register_styles()
    {
        wp_enqueue_style('winter_mvc', plugins_url(plugin_basename(WINTER_MVC_PATH).'/assets/css/winter_mvc.css'));
    }

    public function register_scripts()
    {
        wp_register_script( 'wpmediaelement_file',  plugins_url(plugin_basename(WINTER_MVC_PATH).'/assets/js/jquery.wpmediaelement_file.js'), array(), false, false );
        wp_register_script( 'wpmediaelement',  plugins_url(plugin_basename(WINTER_MVC_PATH).'/assets/js/jquery.wpmediaelement.js'), array(), false, false );
        wp_register_script( 'wpmediamultiple',  plugins_url(plugin_basename(WINTER_MVC_PATH).'/assets/js/jquery.wpmediamultiple.js'), array(), false, false );
        $params = array(
            'text' =>array(
				'frame_title' => esc_html__('Select or Upload Media Of Your Chosen Persuasion', 'wpdirectorykit'),
				'frame_button' => esc_html__('Use this media', 'wpdirectorykit'),
			),
        );
		wp_localize_script('wpmediamultiple', 'wpmediamultiple_parameters', $params);
		wp_localize_script('wpmediaelement', 'wpmediaelement_parameters', $params);
		wp_localize_script('wpmediaelement_file', 'wpmediaelement_file_parameters', $params);
    }
    
    public function load_helper($filename)
    {
        if(empty($this->plugin_directory))
        {
            $file = WINTER_MVC_PATH.'/../../application/helpers/'.ucfirst(sanitize_file_name($filename)).'.php';
        }
        else
        {
            $file = $this->plugin_directory.'application/helpers/'.ucfirst(sanitize_file_name($filename)).'.php';
        }

        if(file_exists($file))
            require_once $file;
    }

    public function load_controller($class, $method = '', $params = array())
    {
        if(empty($this->plugin_directory))
        {
            $file = WINTER_MVC_PATH.'/../../application/controllers/'.ucfirst(sanitize_file_name($class)).'.php';
        }
        else
        {
            $file = $this->plugin_directory.'application/controllers/'.ucfirst(sanitize_file_name($class)).'.php';
        }

        if(file_exists($file))
        {
            require_once $file;

            $class = ucfirst($class);

            $class = str_replace('-', '_', $class);

            if(class_exists($class.'_index'))
            {
                $class = $class.'_index';
            }

            // Init controller
            if(!isset($this->controllers_repository[$class]))
            {
                $this->controllers_repository[$class] = new $class();
                $this->controllers_repository[$class]->set_loader($this);
            }

            // Run controller method
            if(empty($method))
                $method = 'index';



            return call_user_func_array(array($this->controllers_repository[$class], $method), $params);
                
            //return $this->controllers_repository[$class]->$method(extract($params));

        }
        else
        {
            echo 'Controller file not found in: '.esc_html($file);
        }

    }

    public function view($view_file, &$data, $output = TRUE)
    {

        if(!empty($this->plugin_directory)) {
            $plugin = basename($this->plugin_directory);
        } else {
            $plugin = basename( plugin_dir_path(  dirname( __FILE__ , 3 ) ) );
        }

        if(is_child_theme() && file_exists(get_stylesheet_directory().'/'.$plugin.'/application/views/'.$view_file.'.php'))
        {
            $file = get_stylesheet_directory().'/'.$plugin.'/application/views/'.$view_file.'.php';
        }
        elseif(file_exists(get_template_directory().'/'.$plugin.'/application/views/'.$view_file.'.php'))
        {
            $file = get_template_directory().'/'.$plugin.'/application/views/'.$view_file.'.php';
        }
        elseif(empty($this->plugin_directory))
        {
            $file = WINTER_MVC_PATH.'/../../application/views/'.$view_file.'.php';
        }
        else
        {
            $file = $this->plugin_directory.'application/views/'.$view_file.'.php';
        }

        if(file_exists($file))
        {
            if($output === TRUE)
            {
                extract($data);
                include $file;
            }
            else
            {
                ob_start();

                extract($data);
                include $file;

                $generated_output = ob_get_contents();

                ob_end_clean();

                return $generated_output;

            }

        }
        else
        {
            echo 'View file not found in: '.esc_html($file);
        }
    }

    public function model($class)
    { 
        if(empty($this->plugin_directory))
        {
            $file = WINTER_MVC_PATH.'/../../application/models/'.ucfirst(sanitize_file_name($class)).'.php';
        }
        else
        {
            $file = $this->plugin_directory.'application/models/'.ucfirst(sanitize_file_name($class)).'.php';
        }
        

        if(file_exists($file))
        {
            require_once $file;

            $class = ucfirst($class);

            // Init controller
            if(!isset($this->models_repository[$class]))
            {
                $this->models_repository[$class] = new $class();
                $this->models_repository[$class]->set_loader($this);
            }

            foreach($this->controllers_repository as $key =>$rep)
            {
                $this->controllers_repository[$key]->{strtolower($class)} = &$this->models_repository[$class];
            }

            foreach($this->models_repository as $key =>$rep)
            {
                $this->models_repository[$key]->{strtolower($class)} = &$this->models_repository[$class];
            }

            $this->{strtolower($class)} = &$this->models_repository[$class];
                
        }
        else
        {
            echo 'Model file not found in: '.esc_html($file);
        }

    }

}

endif;

?>