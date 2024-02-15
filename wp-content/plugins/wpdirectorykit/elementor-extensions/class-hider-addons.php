<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

add_action( 'elementor/init', function() {
    new WDK_Extension_Hider_Addons();
});

/**
 * WDK_Extension_Hider_Addons
 *
 * Class to extend Elementor controls functionality, adding hide feature based on specific wdk field
 *
 */

class WDK_Extension_Hider_Addons {

	public $name = 'WDK Hider Addons';

	private $is_common = true;

	private $depended_scripts = [];

	private $depended_styles = [];
    
    private $has_controls = TRUE;
    
	public $addons = array();

	public $common_sections_actions = array(
		array(
			'element' => 'common',
			'action' => '_section_style',
		),
        array(
			'element' => 'section',
			'action' => 'section_advanced',
		),
        array(
			'element' => 'container',
			'action' => 'section_layout',
		),
        array(
			'element' => 'column',
			'action' => 'section_advanced',
		),
	);

    private $supported_elements = array(
        'heading'
    );

	public function __construct() {

        /*
        Controls_Manager::add_tab(
			'wdk_hider',
			__( 'Hider', 'wpdirectorykit' )
		);*/

        
        $this->addons = array();

        $this->addons []  = array(
            'title' => __('Energy efficiency', 'wpdirectorykit'),
            'slug' => 'sweet_energy_efficiency',
            'is_activated_slug' => 'run_sweet_energy_efficiency',
        );

        $this->addons []  = array(
            'title' => __('Multy Currency', 'wpdirectorykit'),
            'slug' => 'wdk_currency_conversion',
            'is_activated_slug' => 'run_wdk_currency_conversion',
        );

        $this->addons []  = array(
            'title' => __('Booking & Calendar', 'wpdirectorykit'),
            'slug' => 'wdk_bookings',
            'is_activated_slug' => 'run_wdk_bookings',
        );

        $this->addons []  = array(
            'title' => __('Review system', 'wpdirectorykit'),
            'slug' => 'wdk_reviews',
            'is_activated_slug' => 'run_wdk_reviews',
        );

        $this->addons []  = array(
            'title' => __('Membership Features', 'wpdirectorykit'),
            'slug' => 'wdk_membership',
            'is_activated_slug' => 'run_wdk_membership',
        );

        $this->addons []  = array(
            'title' => __('Import/Export', 'wpdirectorykit'),
            'slug' => 'wdk_export_xml',
            'is_activated_slug' => 'wdk_export_xml',
        );

        $this->addons []  = array(
            'title' => __('Favorites', 'wpdirectorykit'),
            'slug' => 'wdk_favorites',
            'is_activated_slug' => 'run_wdk_favorites',
        );

        $this->addons []  = array(
            'title' => __('Mortgage Calculator', 'wpdirectorykit'),
            'slug' => 'wdk_mortgage',
            'is_activated_slug' => 'run_wdk_mortgage',
        );

        $this->addons []  = array(
            'title' => __('Profile picture uploader', 'wpdirectorykit'),
            'slug' => 'profile_picture_uploader',
            'is_activated_slug' => 'ppu_custom_user_profile_fields',
        );

        $this->addons []  = array(
            'title' => __('MailChimp Newsletter', 'wpdirectorykit'),
            'slug' => 'wdk_mailchimp',
            'is_activated_slug' => 'run_wdk_mailchimp',
        );

        $this->addons []  = array(
            'title' => __('Facebook Comments', 'wpdirectorykit'),
            'slug' => 'wdk_facebook_comments',
            'is_activated_slug' => 'run_wdk_facebook_comments',
        );

        $this->addons []  = array(
            'title' => __('Report Abuse', 'wpdirectorykit'),
            'slug' => 'wdk_report_abuse',
            'is_activated_slug' => 'run_wdk_report_abuse',
        );

        $this->addons []  = array(
            'title' => __('Payments Listing Packages', 'wpdirectorykit'),
            'slug' => 'wdk_payments',
            'is_activated_slug' => 'run_wdk_payments',
        );

        $this->addons []  = array(
            'title' => __('Compare Listings', 'wpdirectorykit'),
            'slug' => 'wdk_compare_listing',
            'is_activated_slug' => 'run_wdk_compare_listing',
        );

        $this->addons []  = array(
            'title' => __('Save Search', 'wpdirectorykit'),
            'slug' => 'wdk_save_search',
            'is_activated_slug' => 'run_wdk_save_search',
        );

        $this->addons []  = array(
            'title' => __('WDK Claim / Take Ownership', 'wpdirectorykit'),
            'slug' => 'wdk_listing_claim',
            'is_activated_slug' => 'run_wdk_listing_claim',
        );

        $this->addons []  = array(
            'title' => __('WDK PDF Download', 'wpdirectorykit'),
            'slug' => 'wdk_pdf_export',
            'is_activated_slug' => 'run_wdk_pdf_export',
        );

		$this->init();
	}

	public function init( $param = null ) {
		// Enqueue scripts
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// Enqueue styles
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );

		// Elementor hooks

		if ( $this->is_common ) {
			// Add the advanced section required to display controls
			$this->add_common_sections_actions();
		}

		$this->add_actions();
	}

	public static function is_enabled() {
		return true;
	}

	public function add_script_depends( $handler ) {
		$this->depended_scripts[] = $handler;
	}

	public function add_style_depends( $handler ) {
		$this->depended_styles[] = $handler;
	}

	public function get_script_depends() {
		return $this->depended_scripts;
	}

	public function enqueue_scripts() {
		foreach ( $this->get_script_depends() as $script ) {
			wp_enqueue_script( $script );
		}
	}

	public function get_style_depends() {
		return $this->depended_styles;
	}

	public static function get_description() {
		return '';
	}

	public function enqueue_styles() {
		foreach ( $this->get_style_depends() as $style ) {
			wp_enqueue_style( $style );
		}
	}

	public function _enqueue_scripts() {
		$scripts = $this->get_script_depends();
		if ( ! empty( $scripts ) ) {
			foreach ( $scripts as $script ) {
				wp_enqueue_script( $script );
			}
		}
	}

	public function _enqueue_styles() {
		$styles = $this->get_style_depends();
		if ( ! empty( $styles ) ) {
			foreach ( $styles as $style ) {
				wp_enqueue_style( $style );
			}
		}
	}

	public function enqueue_all() {
		$this->_enqueue_styles();
		$this->_enqueue_scripts();
	}

	public function get_low_name() {
		return 'hider-addons';
	}

	final public function add_common_sections( $element, $args ) {
		$low_name = $this->get_low_name();
		$section_name = 'wdk_section_' . $low_name . '_advanced';

		if ( ! $this->has_controls ) {
			// no need settings
			return false;
		}

		// Check if this section exists
		$section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $element->get_unique_name(), $section_name );

		if ( ! is_wp_error( $section_exists ) ) {
			// We can't and should try to add this section to the stack
			return false;
		}

		$this->get_control_section( $section_name, $element );
	}

	public function add_common_sections_actions() {
		foreach ( $this->common_sections_actions as $action ) {
			// Activate action for elements
			add_action('elementor/element/' . $action['element'] . '/' . $action['action'] . '/after_section_end', function ( $element, $args ) {
				$this->add_common_sections( $element, $args );
			}, 10, 2);
		}
	}

	protected function add_actions() {

        // WIDGET
		add_action( 'elementor/frontend/widget/before_render', [ $this, '_start_element' ], 10, 1 );
		add_action( 'elementor/frontend/widget/after_render', [ $this, '_end_element' ], 10, 1 );
        
		// SECTION
		add_action( 'elementor/frontend/section/before_render', [ $this, '_start_element' ], 10, 1 );
		add_action( 'elementor/frontend/section/after_render', [ $this, '_end_element' ], 10, 1 );
   
		// CONTAINER
		add_action( 'elementor/frontend/container/before_render', [ $this, '_start_element' ], 10, 1 );
		add_action( 'elementor/frontend/container/after_render', [ $this, '_end_element' ], 10, 1 );
        
		// COLUMN
		add_action( 'elementor/frontend/column/before_render', [ $this, '_start_element' ], 10, 1 );
		add_action( 'elementor/frontend/column/after_render', [ $this, '_end_element' ], 10, 1 );


	}

	protected function remove_controls( $element, $controls = null ) {
		if ( empty( $controls ) ) {
			return;
		}

		if ( is_array( $controls ) ) {
			$control_id = $controls;

			foreach ( $controls as $control_id ) {
				$element->remove_control( $control_id );
			}
		} else {
			$element->remove_control( $controls );
		}
	}

    public function get_control_section( $section_name, $element ) {
		$low_name = $this->get_low_name();

		$element->start_controls_section(
			$section_name,
			[
				'label' => '<span class="color-wdk icon icon-dyn-logo-wdk pull-right ml-1"></span> ' . $this->name,
                'tab' => 'advanced',
			]
		);

        foreach($this->addons as $addon) {
            $element->add_control(
                'wdk_addon_hide_'.wmvc_show_data('slug', $addon, '', TRUE, TRUE),
                [
                    'label' => wmvc_show_data('title', $addon, '', TRUE, TRUE),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'wpdirectorykit' ),
                    'label_off' => __( 'Off', 'wpdirectorykit' ),
                    'return_value' => 'true',
                    'default' => '',
                ]
            );
        }


		$element->end_controls_section();

	}

    public function _start_element( $element ) {
        if(Plugin::$instance->editor->is_edit_mode())return;

        $element_skip = FALSE;
        $settings = $element->get_settings_for_display();
        foreach($this->addons as $addon) {
            $key = 'wdk_addon_hide_'.wmvc_show_data('slug', $addon, '', TRUE, TRUE);
            if(isset($settings[$key]) && $settings[$key] == 'true') {
               
                if(!function_exists(wmvc_show_data('is_activated_slug', $addon, 'wdk_non_function', TRUE, TRUE))) {
                    $element_skip = TRUE;
                    break;
                }
            }
        }

        if($element_skip)
        {
            ob_start();
        }
	}

	public function _end_element( $element ) {
        if(Plugin::$instance->editor->is_edit_mode())return;

        $element_skip = FALSE;
        $settings = $element->get_settings_for_display();
        foreach($this->addons as $addon) {
            $key = 'wdk_addon_hide_'.wmvc_show_data('slug', $addon, '', TRUE, TRUE);
            if(isset($settings[$key]) && $settings[$key] == 'true') {
               
                if(!function_exists(wmvc_show_data('is_activated_slug', $addon, 'wdk_non_function', TRUE, TRUE))) {
                    $element_skip = TRUE;
                    break;
                }
            }
        }

        if($element_skip)
        {
            $content = ob_get_clean();
        }
	}
    
}






