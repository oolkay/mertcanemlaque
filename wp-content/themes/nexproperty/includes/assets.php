<?php 
namespace NexProperty;

class Assets {

	private static $_instance = null;

	public static function instance() {
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	public function __construct(  ) {

		// register scripts and styles
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );


	}

	public function register_scripts() {

		wp_register_script( 'bootstrap', NEXPROPERTY_ASSETS_DIR_URI . '/libs/bootstrap-4.5.3/js/bootstrap.min.js' , ['jquery'], '4.5.0', true );
		wp_register_script( 'nexproperty-counter', NEXPROPERTY_ASSETS_DIR_URI . '/js/counter.min.js' , ['jquery'], '1.0', true );
		wp_register_script( 'nexproperty-custom-select', NEXPROPERTY_ASSETS_DIR_URI . '/js/custom-select.js' , ['jquery'], NEXPROPERTY_THEME_VERSION, true );
		wp_register_script( 'froogaloop2', NEXPROPERTY_ASSETS_DIR_URI . '/js/froogaloop2.min.js' , ['jquery'], NEXPROPERTY_THEME_VERSION, true );
		wp_register_script( 'html5lightbox', NEXPROPERTY_ASSETS_DIR_URI . '/js/html5lightbox.min.js' , ['jquery'], '6.7', true );
		wp_register_script( 'jquery-validate', NEXPROPERTY_ASSETS_DIR_URI . '/js/jquery.validate.min.js' , ['jquery'], NEXPROPERTY_THEME_VERSION, true );
		wp_register_script( 'modernizr', NEXPROPERTY_ASSETS_DIR_URI . '/js/modernizr-3.6.0.min.js' , ['jquery'], '3.6.0', true );
		wp_register_script( 'bootstrap-popper', NEXPROPERTY_ASSETS_DIR_URI . '/js/popper.min.js' , ['jquery'], NEXPROPERTY_THEME_VERSION, true );
		wp_register_script( 'nexproperty-script', NEXPROPERTY_ASSETS_DIR_URI . '/js/script.js' , ['jquery'], NEXPROPERTY_THEME_VERSION, true );
		wp_register_script( 'nexproperty-validator', NEXPROPERTY_ASSETS_DIR_URI . '/js/validator.js' , ['jquery'], NEXPROPERTY_THEME_VERSION, true );
                wp_register_script( 'slimselect', NEXPROPERTY_ASSETS_DIR_URI . '/libs/slim-select/slimselect.min.js', [ 'jquery' ], false, true );
                
		wp_register_script( 'slick', NEXPROPERTY_ASSETS_DIR_URI . '/slick/slick.min.js' , ['jquery'], NEXPROPERTY_THEME_VERSION, true );

		wp_register_style( 'slick-theme', NEXPROPERTY_ASSETS_DIR_URI . '/slick/slick-theme.css', '', NEXPROPERTY_THEME_VERSION, 'all' );
		wp_register_style( 'slick', NEXPROPERTY_ASSETS_DIR_URI . '/slick/slick.css', '', NEXPROPERTY_THEME_VERSION, 'all' );
		
		wp_register_style( 'nexproperty-animate', NEXPROPERTY_ASSETS_DIR_URI . '/css/animate.css', '', '3.5.2', 'all' );
		wp_register_style( 'bootstrap', NEXPROPERTY_ASSETS_DIR_URI . '/libs/bootstrap-4.5.3/css/bootstrap.min.css', '', '4.5.0', 'all' );
		wp_register_style( 'font-awesome', NEXPROPERTY_ASSETS_DIR_URI . '/css/font-awesome.min.css', '', '4.7.0', 'all' );
		wp_register_style( 'fontawesome-5', NEXPROPERTY_ASSETS_DIR_URI . '/libs/fontawesome-5.8/css/fontawesome-5.css', '', '5.8', 'all' );


		wp_register_style( 'nexproperty-responsive', NEXPROPERTY_ASSETS_DIR_URI . '/css/responsive.css', '', NEXPROPERTY_THEME_VERSION, 'all' );
		wp_register_style( 'nexproperty-winter', NEXPROPERTY_ASSETS_DIR_URI . '/css/winter.css', '', NEXPROPERTY_THEME_VERSION, 'all' );
		
		$remote_url = 'https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i|Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap';
		if(function_exists('wptt_get_webfont_url')){
			wp_register_style( 'google-fonts',  wptt_get_webfont_url( $remote_url ), false );
		}
		
		wp_register_style( 'slimselect', NEXPROPERTY_ASSETS_DIR_URI . '/libs/slim-select/slimselect.min.css', false, false);

		if(is_rtl())
			wp_register_style( 'nexproperty-rtl', NEXPROPERTY_ASSETS_DIR_URI . '/css/rtl.css', '', NEXPROPERTY_THEME_VERSION, 'all' );
		
		add_editor_style(  NEXPROPERTY_ASSETS_DIR_URI . '/css/editor-styles.css' );
	}

	public function enqueue_scripts(){


		wp_enqueue_script( 'bootstrap-popper' );
		wp_enqueue_script( 'bootstrap' );
		wp_enqueue_script( 'nexproperty-counter' );
		wp_enqueue_script( 'nexproperty-custom-select' );
		wp_enqueue_script( 'froogaloop2' );
		wp_enqueue_script( 'jquery-validate' );
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'nexproperty-script' );
		wp_enqueue_script( 'nexproperty-validator' );
		wp_enqueue_script( 'slick' );
		wp_enqueue_script( 'slimselect' );


		wp_enqueue_style( 'slick-theme' );
		wp_enqueue_style( 'slickstyle' );
		wp_enqueue_style( 'slimselect' );

		wp_enqueue_style( 'nexproperty-animate' );
		wp_enqueue_style( 'bootstrap' );
		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'fontawesome-5' );
		wp_enqueue_style( 'font-line-awesome' );
		wp_enqueue_style( 'nexproperty-winter' );
		wp_enqueue_style( 'google-fonts' );

		wp_enqueue_style( 'next-property-style', get_stylesheet_uri() );

		wp_enqueue_style( 'nexproperty-responsive' );
                
                if (is_rtl()) {
                    wp_enqueue_style( 'nexproperty-rtl');
                }

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

	}
}