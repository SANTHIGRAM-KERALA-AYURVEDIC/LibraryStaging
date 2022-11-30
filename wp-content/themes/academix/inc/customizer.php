<?php
/**
 * academix Theme Customizer
 *
 * @package academix
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function academix_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'academix_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'academix_customize_partial_blogdescription',
		) );
	}
}
add_action( 'customize_register', 'academix_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function academix_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function academix_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

function academix_inline_css(){
	global $academix_options;
    $height = isset($academix_options['academix_site_image_logo_dimensions']['height']) ? $academix_options['academix_site_image_logo_dimensions']['height'] : null;
    $width = isset($academix_options['academix_site_image_logo_dimensions']['width']) ? $academix_options['academix_site_image_logo_dimensions']['width'] : null;

	$output = '';

	if($height) {
		$output .= '.navbar-brand img{ height:' . $height .'; }';
	}

	if($width) {
		$output .= '.navbar-brand img{ width:' . $width .'; }';
	}

	wp_add_inline_style( 'academix-style', $output );
}
add_action( 'wp_enqueue_scripts', 'academix_inline_css' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function academix_customize_preview_js() {
	wp_enqueue_script( 'academix-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'academix_customize_preview_js' );
