<?php
namespace NexProperty;

class Customizer {

	private static $_instance = null;

	public static function instance() {
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}


	public function __construct() {

		add_action( 'customize_register', [$this, 'register_customizer'], 99, 1 );
	}

	public function register_customizer( $wp_customize ) {

		$panel_id = 'nexproperty_customizer_theme_options';

		$wp_customize->add_panel( $panel_id, [
			'title'		=> __( 'Theme Options', 'nexproperty' ),
			'priority' => 10,
			'theme_supports' => '',

		]);

		$wp_customize->selective_refresh->add_partial('property_button_text', array(
			'selector' => 'header .sign-in-pr .sign li a.lnk-btn',
			'render_callback' => [ $this, 'nexproperty_sanitize_callback'],
		));

		$wp_customize->selective_refresh->add_partial('footer_content', array(
			'selector' => '.top-footer .my-property.widget',
			'render_callback' => [ $this, 'nexproperty_sanitize_callback'],
		));

		$controls = $this->customizer_control_settings();

		foreach ( $controls as $section_key => &$section ) {

			$section_id = 'nexproperty_section' . $section_key;

			$wp_customize->add_section( $section_id, [
				'title'		=> $section['label'],
				'panel'		=> $panel_id,	
				'capability'=> 'edit_theme_options',
				'priority' => $section['priority'],

			]);

			foreach ( $section['fields'] as $field_key => &$field ) {
				if( ! isset( $field['default'] ) ){
					$field['default'] = '';
				}

				$settings = array_merge( $field, [ 'section' => $section_id, 'settings'  => $field_key ] );
                                if(isset($field['choices']) ) {
                                    $wp_customize->add_setting( $field_key,[ 
                                            'sanitize_callback' => [ $this, 'nexproperty_sanitize_switch_callback'],
                                            'default'			=> $field['default'],
                                    ] );
                                }
                                elseif(strpos($field_key, 'email') !==FALSE ) {
                                    $wp_customize->add_setting( $field_key,[ 
                                            'sanitize_callback' => [ $this, 'nexproperty_sanitize_email_callback'],
                                            'default'			=> $field['default'],
                                    ] );
                                } else {
                                    $wp_customize->add_setting( $field_key,[ 
                                            'sanitize_callback' => [ $this, 'nexproperty_sanitize_callback'],
                                            'default'			=> $field['default'],
                                    ]);
                                }

				if( $field['type'] === 'image' ) {
					unset( $settings['type'] );
					$wp_customize->add_control( new \WP_Customize_Image_Control( $wp_customize, $field_key, $settings ));
					continue;
				}
// print_r( $settings ); 
				$wp_customize->add_control( $field_key, $settings );	
			}
		}

		$settings = [
			'label'			=> __( 'Logo Footer', 'nexproperty' ),
			'description'	=> __( 'Upload/Change Footer logo', 'nexproperty' ),
			'section' => 'title_tagline', 
			'settings'  => 'footer_logo'
		];
		$wp_customize->add_control( new \WP_Customize_Image_Control( $wp_customize, 'footer_logo', $settings ));
		
	}

	public function nexproperty_sanitize_callback( $value ) {
            return sanitize_text_field($value);
	}

	public function nexproperty_sanitize_switch_callback( $value ) {
            if($value == 'no')
                return 'no';
            elseif($value == 'yes')
                return 'yes';
	}

	public function nexproperty_sanitize_email_callback( $value ) {
		return sanitize_email($value);
	}

	public function customizer_control_settings() {

		$current_post = get_queried_object();
		$post_id = $current_post ? $current_post->ID : null;
		return [
			'header' => [
				'label'		=> __( 'Header', 'nexproperty' ),
				'priority'	=> 130,
				'fields'	=> [
					'show_sign_in_button'	=> [
						'label'			=> __( 'Show Login button?', 'nexproperty' ),
						'description'	=> __( 'Whether to show header sign-in button or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'no',
					],
					'sign_in_button_text'	=> [
						'label'			=> __( 'Sign-in Text', 'nexproperty' ),
						'description'	=> __( 'Enter Sign-In button Text', 'nexproperty' ),
						'type'			=> 'text',
						'default'		=> __('Login', 'nexproperty'),
					],
					'show_property_button'	=> [
						'label'			=> __( 'Show Property Button?', 'nexproperty' ),
						'description'	=> __( 'Whether to show header sign-in button or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'no',
					],
					'property_button_text'	=> [
						'label'			=> __( 'Property Button Text', 'nexproperty' ),
						'description'	=> __( 'Enter Property button Text', 'nexproperty' ).'<br><span style="color:red">'.__( 'This only works on standard WP pages, for Elementor pages please check in Elementor on specific page related', 'nexproperty' ).'</span>',
						'type'			=> 'text',
						'default'		=> __('Add Property', 'nexproperty'),
					],
				],
			],
			'footer' => [
				'label'		=> __( 'Footer', 'nexproperty' ),
				'priority'	=> 150,
				'fields'	=> [
					'footer_content'		=> [
						'label'			=> __( 'Footer Content', 'nexproperty' ),
						'description'	=> __( 'Enter the content', 'nexproperty' ),
						'type'			=> 'textarea',
						'default'		=> get_bloginfo( 'description' ),
					],
					'footer_phone_number'	=> [
						'label'			=> __( 'Phone Number', 'nexproperty' ),
						'description'	=> __( 'Enter Phone Number', 'nexproperty' ),
						'type'			=> 'text',
					],
					'footer_email_address'	=> [
						'label'			=> __( 'Email Address', 'nexproperty' ),
						'description'	=> __( 'Enter Email Address', 'nexproperty' ),
						'type'			=> 'text',
					],
					'show_footer_sidebar'	=> [
						'label'			=> __( 'Show Footer Widgets?', 'nexproperty' ),
						'description'	=> __( 'Whether to show Footer Widgets or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'no',
					],
					'footer_copyright_text'	=> [
						'label'			=> __( 'Copyright Text', 'nexproperty' ),
						'description'	=> __( 'Enter Copyright text', 'nexproperty' ),
						'type'			=> 'text',
						'default'		=> '',
					],
					'footer_powered_by'	=> [
						'label'			=> __( 'Powered By', 'nexproperty' ),
						'type'			=> 'text',
						'default'		=> '',
					],
					'footer_powered_by_link'	=> [
						'label'			=> __( 'Powered By Link', 'nexproperty' ),
						'type'			=> 'text',
						'default'		=> '',
					],
				],
			],
			'blog_page' => [
				'label'		=> __( 'Blog Page', 'nexproperty' ),
				'priority'	=> 140,
				'fields'	=> [
					'show_page_heading'		=> [
						'label'			=> __( 'Show Page Heading?', 'nexproperty' ),
						'description'	=> __( 'Whether to show Blog page heading or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'no',
					],
					'page_heading_text'		=> [
						'label'			=> __( 'Page Heading Text', 'nexproperty' ),
						'description'	=> __( 'Enter the blog page heading. Leave empty if you want to show default page name', 'nexproperty' ),
						'type'			=> 'text',
						'default'		=> __( 'Blog Standard', 'nexproperty' ),
					],
					'show_page_sidebar'		=> [
						'label'			=> __( 'Show Sidebar?', 'nexproperty' ),
						'description'	=> __( 'Whether to show Sidebar or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'yes',
					],
					'show_post_date'		=> [
						'label'			=> __( 'Show post date?', 'nexproperty' ),
						'description'	=> __( 'Whether to show post date or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'yes',
					],
					'show_post_author'		=> [
						'label'			=> __( 'Show post author?', 'nexproperty' ),
						'description'	=> __( 'Whether to show post author or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'yes',
					],
					'show_author_info'		=> [
						'label'			=> __( 'Show author info on single post?', 'nexproperty' ),
						'description'	=> __( 'Whether to show post author info on single post page or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'yes',
					],
					'show_post_comments'		=> [
						'label'			=> __( 'Show comments count?', 'nexproperty' ),
						'description'	=> __( 'Whether to show number of comments on posts or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'yes',
					],
					'show_cat'		=> [
						'label'			=> __( 'Show categories?', 'nexproperty' ),
						'description'	=> __( 'Whether to show categories on posts or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'yes',
					],
					'show_post_tags'		=> [
						'label'			=> __( 'Show Tags?', 'nexproperty' ),
						'description'	=> __( 'Whether to show Tags on posts or not?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'yes',
					],
					'show_post_excerpt'		=> [
						'label'			=> __( 'Show post excerpt, instead content?', 'nexproperty' ),
						'choices'		=> [
							'yes'	=> __( 'Yes', 'nexproperty' ),
							'no'	=> __( 'No', 'nexproperty' ),
						],
						'type'			=> 'radio',
						'default'		=> 'no',
					],
					'words_limit'			=> [
						'label'			=> __( 'Words to show in Posts content', 'nexproperty' ),
						'description'	=> __( 'Enter the number of words to be shown in the posts content on main blog page', 'nexproperty' ),
						'type'			=> 'text',
						'default'		=> '1000'
					],
				],
			],
		];
	}
}
