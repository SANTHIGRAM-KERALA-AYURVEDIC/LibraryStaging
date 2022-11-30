<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package academix
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(academix_option('academix_global_palette_color')); ?>>
<?php wp_body_open(); ?>
<?php 
    global $academix_options; 
    
    $box_layout = 'box-layout';
    $box_layout_navbar = 'box-layout-navbar';
    $is_elementor_editor = defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->preview->is_preview_mode();
?>

<!-- start preloader -->
<?php if( isset($academix_options['academix_preloader_visibility']) && $academix_options['academix_preloader_visibility'] == '1' && !$is_elementor_editor){ ?>
<div class="preloader-wrap">
    <div class="preloader-spinner">
        <div class="preloader-dot1"></div>
        <div class="preloader-dot2"></div>
    </div>
</div>
<?php } ?>
<!-- / end preloader -->


<div class="site-main<?php if( isset($academix_options['academix_menu_type']) && $academix_options['academix_menu_type'] == '0'){ echo esc_attr(' scrolly-nav'); } ?>" <?php if( isset( $academix_options['academix_box_layout_bg_image']['url'] ) && $academix_options['academix_box_layout_background_type'] == '1' ) { ?>style="background-image: url(<?php echo esc_url($academix_options['academix_box_layout_bg_image']['url']); ?>);" <?php } ?>>
<div id="page" class="site <?php if( isset($academix_options['academix_page_layout_style']) && $academix_options['academix_page_layout_style'] == '0'){ echo esc_attr($box_layout); } ?>">
    <header class="sabbi-site-head">
        <nav class="navbar hidden-sm hidden-xs navbar-white navbar-kawsa <?php if( isset($academix_options['academix_menu_type']) && $academix_options['academix_menu_type'] == '0'){ echo esc_attr('navbar-scroll'); } else{ echo esc_attr('navbar-fixed-top'); } ?> <?php if( isset($academix_options['academix_page_layout_style']) && $academix_options['academix_page_layout_style'] == '0'){ echo esc_attr($box_layout_navbar); } if( !isset($academix_options) ){ echo esc_attr('menu-padding'); } ?>" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button aria-controls="navbar" aria-expanded="false" class="navbar-toggle collapsed" data-target="#navbar" data-toggle="collapse" type="button"><span class="sr-only"> <?php esc_html_e('Toggle navigation', 'academix'); ?></span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></button> 
                    <?php if( function_exists('academix_logo')){ academix_logo(); } ?>
                </div>
                <div class="navbar-collapse collapse sabbi-navbar-collapse  navbar-nav-hov_underline" id="navbar">
                    <?php if( isset( $academix_options['academix_display_main_menu_btn'] ) && $academix_options['academix_display_main_menu_btn'] == '1') { 
                        $btn_text = ( isset($academix_options['academix_main_menu_btn_text']) && !empty( $academix_options['academix_main_menu_btn_text'] ) ) ? $academix_options['academix_main_menu_btn_text'] : '';
                        $btn_link = ( isset($academix_options['academix_main_menu_btn_link']) && !empty( $academix_options['academix_main_menu_btn_link'] ) ) ? $academix_options['academix_main_menu_btn_link'] : '#';

                    ?>
                    <div class="nav-btn-wrap"><a href="<?php echo esc_url($btn_link);?>" class="btn btn-primary pull-right"><?php echo esc_html($btn_text);?></a></div>
                    <?php } ?>
                    <?php if( function_exists( 'academix_main_menu' ) ) { academix_main_menu(); } ?>
                </div>
            </div>
        </nav><!-- /.navbar -->

        <div class="mobile-menu-area <?php if( isset($academix_options['academix_menu_type']) && $academix_options['academix_menu_type'] == '0'){ echo esc_attr('navbar-scroll'); } else{ echo esc_attr('navbar-fixed-top'); } ?>">
             <div class="mobile-menu hidden-md hidden-lg" id="mob-menu">
                <div class="mobile-menu-logo">
                    <?php if( function_exists('academix_logo') ){ academix_logo(); } ?>
                </div>
                <?php if( function_exists('academix_mobile_menu')){ academix_mobile_menu(); } ?>
            </div>
        </div>
    </header><!-- #masthead -->