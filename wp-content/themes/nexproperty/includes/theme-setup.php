<?php 
if ( ! function_exists( 'nexproperty_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function nexproperty_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on nexproperty, use a find and replace
		 * to change 'nexproperty' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'nexproperty', get_template_directory() . '/languages' );
	
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'main_menu' => esc_html__( 'Primary Menu', 'nexproperty' ),
			'footer_links' => esc_html__( 'Footer Links', 'nexproperty' ),
			'mobile_menu' => esc_html__( 'Mobile Menu', 'nexproperty' ),
		) );
	
		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );
                
                // Add theme support for Custom Logo.
                add_theme_support('custom-logo', array(
                    'width' => 215,
                    'height' => 35,
                    'flex-width' => true,
                ));
                
                add_image_size( 'nexproperty-footer-thumbnail', 90, 70, true );
                add_image_size( 'nexproperty-slider-thumbnail', 270, 240, true );
                add_image_size( 'nexproperty-post-thumbnail', 870, 557, true );
                add_image_size( 'nexproperty-post-thumbnail-nocrop', 870, 557, false );

		add_theme_support( "wp-block-styles" );
		add_theme_support( "responsive-embeds" );
		add_theme_support( 'custom-header' );
		add_theme_support( "custom-background");
		add_theme_support( "align-wide" );

		// support WooCommerce
		add_theme_support( 'woocommerce' );
		// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
		// zoom.
		add_theme_support( 'wc-product-gallery-zoom' );
		// lightbox.
		add_theme_support( 'wc-product-gallery-lightbox' );
		// swipe.
		add_theme_support( 'wc-product-gallery-slider' );
		
		// custom logo
		add_theme_support( 'custom-logo' );
	}
endif;
add_action( 'after_setup_theme', 'nexproperty_setup' );

/*
 * Register theme sidebars.
 */
add_action( 'widgets_init', function() {

	register_sidebar( [
		'name'          => __( 'Sidebar', 'nexproperty' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	] );

	register_sidebar( [
		'name'          => __( 'Sidebar Woo Products Page', 'nexproperty' ),
		'id'            => 'sidebar-woo-products',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	] );

	register_sidebar( [
		'name'          => __( 'Sidebar Woo songle Product Page', 'nexproperty' ),
		'id'            => 'sidebar-woo-product',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	] );

	register_sidebar( [
		'name'          => __( 'Footer', 'nexproperty' ),
		'id'            => 'footer',
		'before_widget' => '<div id="%1$s" class="col-lg-3 col-md-4 col-sm-6 col-12 widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	] );

	
} );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
if ( ! function_exists( 'nexproperty_content_width' ) ) {
	function nexproperty_content_width() {
		// This variable is intended to be overruled from themes.
		// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		$GLOBALS['content_width'] = apply_filters( 'nexproperty_content_width', 640 );
	}
	add_action( 'after_setup_theme', 'nexproperty_content_width', 0 );
}

if ( ! function_exists( 'next_property_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function next_property_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( 'nexproperty-post-thumbnail', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
			?>
		</a>

		<?php
		endif; // End is_singular().
	}
endif;


function nexporperty_custom_css() {

    $custom_css = '';

    if (get_custom_header() && get_custom_header()->url !='') {
	
        $custom_css .= '
			.blog-standart {
				background-image: url('.get_custom_header()->url.');
				background-attachment: fixed;
				background-size: cover;
				background-blend-mode: overlay;
				background-color: #ffffff6e;
            }
        ';
    }   
	
	$custom_css .= '
		body #wpadminbar {
			position: fixed;
		}
	';

    wp_add_inline_style('nexproperty-winter', $custom_css);
}

add_action('wp_enqueue_scripts', 'nexporperty_custom_css');