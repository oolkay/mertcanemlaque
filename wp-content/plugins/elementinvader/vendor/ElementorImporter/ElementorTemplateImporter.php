<?php
/**
 * Main class to import an Elementor template from outside the plugin itself.
 * @package MerlinWP
 * @subpackage ElementorImporter
 * @since -
 * @see https://github.com/richtabor/MerlinWP
 */

class Elementor_Template_Importer {

    /**
     * Stores the state for the import process.
     */
    public $import_flag;

    /**
     * Holds the raw templates passed from the constructor.
     */
    protected $template_data;

    /**
     * After the templates have been inserted, this holds the now-parsed templates.
     */
    private $parsed_templates;

    /**
     * Additional args.
     */
    protected $args;

    /**
     * Sets the template data to be used by importTemplate.
     */
    public function __construct( $templates, $args )
    {
        foreach( $templates as $template_name => $template_url ) {
            $this->template_data[$template_name] = $template_url;
        }
        $this->args = $args;

        if( !class_exists( 'Elementor\Plugin' ) ) {
            return new WP_Error( 'elementor-not-activated', esc_html__( 'Elementor is not activated.', 'elementinvader' ) );
        }

        $this->begin_import_process();
        //add_action( 'elementor/init', [$this, 'begin_import_process'] );
    }


    /**
     * Manager for all the inner-functions of the class. Asigned every process
     * to a variable for debug purposes.
     */
    public function begin_import_process() {
        $import_templates = $this->import_templates();
        $actions = $this->register_actions();
    }

    /**
     * Loops through all the templates and loads each one. Note: the import is
     * not asynchronous, templates must wait for the last one's import process
     * to finish.
     */
    public function import_templates() {

        foreach( $this->template_data as $name => $url ) {
            /**
             * Checks if the template exists and returns its data or an error.
             */
            $template_state = $this->template_exists( $name, $return_data = True );
            if ( !is_wp_error( $template_state ) ) {
                /**
                 * If the template is valid, add it to the parsed templates for later use.
                 */
                $this->parsed_templates[] = $template_state;

                /**
                 * Sets default page template for each Elementor template loaded.
                 */
                if( $this->args['set_default_template'] ) {
                    $this->set_default_page_template( $template_state['id'] );
                }
                continue;
            }

            $this->import_template( $url );

        }

        $this->import_flag = True;
        //@NOTE: Is this really right to return True here?
        return True;
    }

    /**
     * Registers actions for the importer based on logic.
     */
    public function register_actions() {
        if( $this->import_flag ) {
            /**
             * Registers the action for when it's done with the importing of the template.
             */
            do_action( 'finished_importing_elementor_templates' );
        }
    }
    /**
     * Imports the actual template. Note that this uses Elementor's template manager
     * capabilities.
     * @see Elementor/includes/template-library/sources/yordy.php::795 - import_template()
     */
    public function import_template( $url )
    {
        global $_FILES, $wp_filesystem;

        WP_Filesystem();

        if( !empty($_FILES) ) {
            return new WP_Error( 'elementor-template-global-not-empty', esc_html__( 'Seems the $_FILE global already has a file and I do not wanna conflict.', 'elementinvader' ) );
        }

        $_FILES['file']['tmp_name'] = $url;
        $_FILES['file']['name'] = $url;
        // @codingStandardsIgnoreStart
        $elem = Elementor\Plugin::instance();
        $import = $elem->templates_manager->direct_import_template();

        // @codingStandardsIgnoreEnd
        unset( $_FILES );

        if( !is_wp_error( $import ) ) {


            if( $this->args['set_default_template'] && isset($import[0]['template_id']) ) {
                $this->set_default_page_template( $import[0]['template_id'] );
            }

            return True;
        } else {
            return $import;
        }
    }

    /**
     * Checks if the template we're trying to insert already exists.
     * @return WP_Error elementor-template-already-loaded - template already exists / loaded, will bail.
     * @param $return_data - Allows for return of the data of said template if the template exists.
     */
    public function template_exists( $template_name )
    {

        $posts = get_posts(
            array(
                'name' => $template_name,
                'post_type' => 'elementor_library',
                'post_status' => 'publish'
            )
        );

        if( $posts ) {
            return array(
                'name' => $posts[0]->post_name,
                'id' => $posts[0]->ID
            );
        }

        return new WP_Error( 'elementor-template-already-loaded', esc_html__( 'This template has already been loaded.', 'elementinvader' ) );
    }

    /**
     * Sets the default page template (note, this is not the template we're loading, just
     * the layout of the page that we pre-defined in a file).
     * @since -
     */
    public function set_default_page_template( $id )
    {
        update_post_meta( $id, '_wp_page_template', $this->args['default_page_template'] );
    }

}

/**
 * Initializes the Importer.
 */

/*

$importer = new Elementor_Template_Importer(
    $templates = [
        'new-template' => get_parent_theme_file_path() . '/Assets/elementor_templates/elementor-166-2018-04-17.json',
        'new-template-2' => get_parent_theme_file_path() . '/Assets/elementor_templates/elementor-166-2018-04-17-2.json',
    ],
    $args = [
        'set_default_template' => True,
        'default_page_template' => 'templates/full-width-template.php',
    ]
);

*/