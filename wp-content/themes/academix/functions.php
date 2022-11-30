<?php
/**
 * academix functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package academix
 */

if ( ! function_exists( 'academix_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function academix_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on academix, use a find and replace
	 * to change 'academix' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'academix', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

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
		'academix-primary' => esc_html__( 'Primary', 'academix' ),
		'academix-footer' => esc_html__( 'Footer', 'academix' ),
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

    // Add support for Block Styles.
    add_theme_support( 'wp-block-styles' );
    // Add support for responsive embedded content.
    add_theme_support( 'responsive-embeds' );
    // Add support for full and wide align images.
    add_theme_support( 'align-wide' );
	/*
	 * Enable support for Post Formats.
	 */
	add_theme_support( 'post-formats', array( 'gallery', 'image', 'quote', 'video', 'audio' ) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'academix_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_image_size( 'academix-single-team-thumb', '360', '270', true );
	add_image_size( 'academix-team-thumbnail', '263', '216', true );
	add_image_size( 'academix-event-thumbnail', '555', '370', true );
	add_image_size( 'academix-event-gallery-thumb', '227', '168', true );
	add_image_size( 'academix-blog-thumb', '100', '80', true );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 250,
		'width'       => 250,
		'flex-width'  => true,
		'flex-height' => true,
	) );
	
	add_theme_support( 'gutenberg', array( 
			'wide-images' => true,
		    'colors' => array(
		    	'#ffffff',
			    '#000000',
			    '#cccccc'
		    ),
	) );
}
endif;
add_action( 'after_setup_theme', 'academix_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function academix_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'academix_content_width', 640 );
}
add_action( 'after_setup_theme', 'academix_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function academix_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'academix' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'academix' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Page', 'academix' ),
		'id'            => 'sidebar-page',
		'description'   => esc_html__( 'Add page widgets here.', 'academix' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	// Footer widget
    for( $footer_col = 1; $footer_col <= 4; $footer_col++ ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Widgets', 'academix' ) .$footer_col,
			'id'            => 'academix' . '-footer-' . $footer_col,
			'description'   => esc_html__( 'Add footer widgets here.', 'academix' ),
			'before_widget' => '<div id="%1$s" class="f-top-center widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
    }
}
add_action( 'widgets_init', 'academix_widgets_init' );


/**
 * Enqueue google fonts.
 */
function academix_google_fonts_url(){
	$fonts_url = '';
	 
	/* Translators: If there are characters in your language that are not
	* supported by Montserrat, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$montserrat = _x( 'on', 'Montserrat font: on or off', 'academix' );
	 
	/* Translators: If there are characters in your language that are not
	* supported by Open Sans, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'academix' );
	 
	if ( 'off' !== $montserrat || 'off' !== $open_sans ) {
	$font_families = array();
	 
	if ( 'off' !== $montserrat ) {
	$font_families[] = 'Montserrat:400,500,700';
	}
	 
	if ( 'off' !== $open_sans ) {
	$font_families[] = 'Open Sans:300,400,400italic,600,700';
	}
	 
	$query_args = array(
	'family' => urlencode( implode( '|', $font_families ) ),
	'subset' => urlencode( 'latin,latin-ext' ),	
	);
	 
	$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}
	 
	return esc_url_raw( $fonts_url );
}

/**
 * Enqueue scripts and styles.
 */
function academix_scripts() {

	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'academix-fonts', academix_google_fonts_url(), array(), null );

	//Load CSS
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '3.3.2' );
	wp_enqueue_style( 'ionicons-min', get_template_directory_uri() . '/assets/css/ionicons.min.css', array(), '2.0.0' );
	wp_enqueue_style( 'animate-min', get_template_directory_uri() . '/assets/css/animate.min.css', array(), '1.0.0' );
	wp_enqueue_style( 'bootstrap-dropdownhover-min', get_template_directory_uri() . '/assets/css/bootstrap-dropdownhover.min.css', array(), '1.0.0' );
	wp_enqueue_style( 'meanmenu', get_template_directory_uri() . '/assets/css/meanmenu.css', array(), '2.0.7' );
    wp_enqueue_style( 'ekko-lightbox', get_template_directory_uri() . '/assets/css/ekko-lightbox.min.css' );
    wp_enqueue_style( 'academix-main', get_template_directory_uri() . '/assets/css/main.css' , array(), '2.0.1' );
	wp_enqueue_style( 'academix-style', get_stylesheet_uri(), array(), '2.0.1' );
	

	// Add print CSS.
	wp_enqueue_style( 'academix-print-style', get_template_directory_uri() . '/print.css', null, '1.2.1', 'print' );
	
	//Load JS
	wp_enqueue_script( 'bootstrap-dropdownhover-min', get_template_directory_uri() . '/assets/js/bootstrap-dropdownhover.min.js', array('jquery'), '1.0.0', true );
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '3.3.2', true );
	wp_enqueue_script( 'jquery-meanmenu', get_template_directory_uri() . '/assets/js/jquery.meanmenu.js', array('jquery'), '2.0.8', true );
    wp_enqueue_script( 'ekko-lightbox', get_template_directory_uri() . '/assets/js/ekko-lightbox.min.js', array('jquery'), '2.0', true );
	wp_enqueue_script( 'academix-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '2.0.0', true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'academix_scripts' );



// include dynamic custom css 
include_once( get_template_directory() . '/inc/custom-css.php' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load theme custom functions
 */
require_once get_template_directory() . '/inc/global-functions.php';

/**
 * Bootstrap wp menu walker
 */
require_once get_template_directory() . '/inc/wp-academix-bootstrap-navwalker.php';

/**
 * Load Breadcrumbs function
 */
require_once get_template_directory() . '/inc/breadcrumbs.php';

/**
 * Redux Framework configuration
 */
if( file_exists( get_template_directory() . '/inc/academix-config.php') ){
	require_once( get_template_directory() . '/inc/academix-config.php' );
}

/**
 * Custom Metabox 2
 */
if( file_exists( get_template_directory() . '/inc/academix-metabox.php') ) {
	require_once( get_template_directory() . '/inc/academix-metabox.php' );
}

/**
 * Load TGM
 */
require get_template_directory() . '/inc/tgm-plugin/class-tgm-plugin-activation.php';
require get_template_directory() . '/inc/tgm-plugin/academix-required-plugins.php';

// add custom icon
add_action('init', 'academix_custom_icon');
function academix_custom_icon() {
 
	if( function_exists( 'academix_custom_icon' ) ) {
 		if( function_exists( 'kc_add_icon' ) ){
			kc_add_icon( get_template_directory_uri() . '/assets/css/ionicons.min.css' );
	    }
	}
 
}

// remove pages from search
function academix_remove_pages_from_search() {
    global $wp_post_types;
    $wp_post_types['page']->exclude_from_search = true;
}
add_action('init', 'academix_remove_pages_from_search');

/**
 * Modify archive widget
 */
function academix_archive_count_span( $links ) {
	$links = str_replace('</a>&nbsp;(', ' <span>(', $links);
	$links = str_replace(')', ')</span></a>', $links);
	return $links;
}
add_filter('get_archives_link', 'academix_archive_count_span');
/**
 * Modify category widget
 */
function academix_category_count_span( $links ) {
	$links = str_replace('</a> (', ' <span>(', $links);
	$links = str_replace(')', ')</span></a>', $links);
	return $links;
}
add_filter('wp_list_categories', 'academix_category_count_span');

function academix_get_single_team_image_id($image_url) {
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
	return $attachment ? $attachment[0] : '';
}

/**
 * one click demo import
 */
function academix_ocdi_import_files() {
  return array(
    array(
      'import_file_name'             => esc_html__( 'Demo Import 1', 'academix' ),
      'local_import_file'            => trailingslashit( get_template_directory() ) . '/inc/import/academix-demo-content.xml',
      'local_import_widget_file'     => trailingslashit( get_template_directory() ) . '/inc/import/academix-widgets.wie',
      'local_import_customizer_file' => trailingslashit( get_template_directory() ) . '/inc/import/academix-export.dat',
      'local_import_redux'           => array(
        array(
          'file_path'   => trailingslashit( get_template_directory() ) . '/inc/import/theme-options.json',
          'option_name' => 'academix_options',
        ),
      ),
      'import_preview_image_url'     => get_template_directory_uri() . '/inc/import/import_preview_image_01.png',
    ),
  );
}

add_filter( 'pt-ocdi/import_files', 'academix_ocdi_import_files' );
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

function academix_ocdi_register_plugins( $plugins ) {
    $theme_plugins = [
        [ // A WordPress.org plugin repository example.
            'name'     => 'WP Social Ninja', // Name of the plugin.
            'slug'     => 'wp-social-reviews', // Plugin slug - the same as on WordPress.org plugin repository.
            'description' => 'The all-in-one WordPress Social Media Plugin for Social Reviews, Social Feeds and Social Chat.',
            'required' => false,                     // If the plugin is required or not.
        ],
        [ // A WordPress.org plugin repository example.
            'name'     => 'Guten Post Layout', // Name of the plugin.
            'slug'     => 'guten-post-layout', // Plugin slug - the same as on WordPress.org plugin repository.
            'description' => 'An Advanced Post Grid Collection for WordPress Gutenberg',
            'required' => false,                     // If the plugin is required or not.
        ],
    ];

    return array_merge( $plugins, $theme_plugins );
}
add_filter( 'ocdi/register_plugins', 'academix_ocdi_register_plugins' );

