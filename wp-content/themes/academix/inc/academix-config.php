<?php
    /**
     * ReduxFramework Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    class reduxNewsflash
    {
        
    }


    // This is your option name where all the Redux data is stored.
    $opt_name = "academix_options";

    // If Redux is running as a plugin, this will remove the demo notice and links
    add_action( 'redux/loaded', 'remove_demo' );

    // This line is only for altering the demo. Can be easily removed.
    $opt_name = apply_filters( 'redux_demo/opt_name', $opt_name );

    /*
     *
     * --> Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
     *
     */

    $sampleHTML = '';


    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        // TYPICAL -> Change these values as you need/desire
        'opt_name'             => $opt_name,
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name'         => $theme->get( 'Name' ),
        // Name that appears at the top of your panel
        'display_version'      => $theme->get( 'Version' ),
        // Version that appears at the top of your panel
        'menu_type'            => 'menu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu'       => true,
        // Show the sections below the admin menu item or not
        'menu_title'           => esc_html__( 'Academix Options', 'academix' ),
        'page_title'           => esc_html__( 'Academix Options', 'academix' ),
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key'       => '',
        // Set it you want google fonts to update weekly. A google_api_key value is required.
        'google_update_weekly' => false,
        // Must be defined to add google fonts to the typography module
        'async_typography'     => true,
        // Use a asynchronous font on the front end or font string
        //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
        'admin_bar'            => true,
        // Show the panel pages on the admin bar
        'admin_bar_icon'       => 'dashicons-portfolio',
        // Choose an icon for the admin bar menu
        'admin_bar_priority'   => 50,
        // Choose an priority for the admin bar menu
        'global_variable'      => '',
        // Set a different name for your global variable other than the opt_name
        'dev_mode'             => false,
        // Show the time the page took to load, etc
        'update_notice'        => false,
        // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
        'customizer'           => true,
        // Enable basic customizer support
        //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
        //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

        // OPTIONAL -> Give you extra features
        'page_priority'        => null,
        // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_parent'          => 'themes.php',
        // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
        'page_permissions'     => 'manage_options',
        // Permissions needed to access the options panel.
        'menu_icon'            => '',
        // Specify a custom URL to an icon
        'last_tab'             => '',
        // Force your panel to always open to a specific tab (by id)
        'page_icon'            => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug'            => '',
        // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
        'save_defaults'        => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show'         => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark'         => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export'   => true,
        // Shows the Import/Export panel when not used as a field.

        // CAREFUL -> These options are for advanced use only
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output_tag'           => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
        // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

        // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
        'database'             => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
        'use_cdn'              => true,
        // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

        // HINTS
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'red',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        )
    );

    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */



    /*
     *
     * ---> START SECTIONS
     *
     */

    //General
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('General', 'academix'),
        'id'     => 'general_settings',
        'desc'   => esc_html__('This section contains general settings options', 'academix'),
        'icon'   => 'el el-cog',
        'fields' => array(
            array(
                'id'          => 'academix_preloader_visibility',
                'type'        => 'switch',
                'title'       => esc_html__('Display Preloader?', 'academix'),
                'subtitle'    => esc_html__( 'show/hide your website preloader?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Show', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
	        array(
		        'id'          => 'academix_preloader_bg_color',
		        'type'        => 'color',
		        'transparent' => false,
		        'title'       => esc_html__( 'Preloader Color', 'academix' ),
		        'subtitle'    => esc_html__( 'Pick a color for the preloader color. ( default: #D30015 )', 'academix' ),
		        'default'     => '',
		        'required'    => array('academix_preloader_visibility', '=', 1),
		        'output'    => array('background-color' => '.preloader-dot1, .preloader-dot2',
		                             'color' => '')
	        ),
            array(
                'id'          => 'academix_page_layout_style',
                'type'        => 'switch',
                'title'       => esc_html__('Page Layout Style', 'academix'),
                'subtitle'    => esc_html__( 'Select your page layout style.', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Full Width', 'academix' ),
                'off'       => esc_html__( 'Boxed', 'academix' ),
            ),
            array(
                'id'          => 'academix_box_layout_background_type',
                'type'        => 'switch',
                'title'       => esc_html__('Box Layout Background type', 'academix'),
                'subtitle'    => esc_html__( 'Select a background type to display background in box layout.', 'academix'),
                'default'     => 1,
                'on'          => esc_html__( 'Background Image', 'academix' ),
                'off'         => esc_html__( 'Background Color', 'academix' ),
                'required'    => array('academix_page_layout_style', '=', 0),
            ), 
            array(
                'id'          => 'academix_box_layout_bg_image',
                'type' => 'media',
                'url' => false,
                'title' => esc_html__('Box Layout Background image', 'academix'),
                'subtitle' => esc_html__('Upload background image for your box layout.', 'academix'),
                'default' => '',
                'required'    => array('academix_box_layout_background_type', '=', 1),
            ),
            array(
                'id'          => 'academix_box_layout_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Box Layout Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the box layout background color. ( default: #ffffff )', 'academix' ),
                'default'     => '#ffffff',
                'output'    => array('background-color' => '.site-main',
                                'color' => ''),
                'required'    => array('academix_box_layout_background_type', '=', 0),
            ),
            array(
                'id'          => 'academix_page_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Page Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/bottom padding. In pixels for page.', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.academix-content-area'),
                'left'    => false,
                'right'    => false,
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '0px', 
                    'padding-bottom'  => '0px', 
                    'units'           => 'px', 
                )
            ),
        )
    ));


    // Colors
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Colors', 'academix'),
        'id'     => 'colors_settings',
        'icon'   => 'el el-adjust',
        'desc'  => esc_html__('This section contains Controls the main color scheme throughout the theme.', 'academix'),
          'fields' => array(
	          array(
		          'id'       => 'academix_global_palette_color',
		          'type'     => 'palette',
		          'title'    => __( 'Palette Color Option', 'academix' ),
		          'subtitle' => __( 'Only color validation can be done on this field type', 'academix' ),
		          'default'  => '',
		          'palettes' => array(
			          'color0'  => array(
				          '#D30015',
			          ),
			          'color1'  => array(
				          '#0296d3',
			          ),
			          'color2' => array(
				          '#df6a02',
			          ),
			          'color3' => array(
				          '#00c257',
			          ), 
			          'color4' => array(
				          '#0101ee',
			          ),
			          'color5' => array(
				          '#a001c2',
			          ),
			          'color6' => array(
				          '#d70093',
			          ),
		          ),
	          ),
	          array(
		          'id'   => 'academix_global_color_info',
		          'type' => 'info',
		          'icon'  => 'el-icon-info-sign',
		          'style' => 'warning',
		          'desc' => __('Note: If you don\'t set any color from Palette Color Option then all other color option will work', 'academix')
	          ),
            array(
                'id'          => 'academix_global_link_color',
                'type'        => 'color',
                'transparent' => false,
                'output'    => array(
                    'color' => '.academix-content-area .blo-cat a,
                    a.comment-reply-link,
                    .logged-in-as a,
                    .blog-breadcrumb.auth-breadcrumb-wrap .sabbi-breadcrumb a,
                    .blog a.btn.btn-unsolemn.btn-action.read-more,
                    .copyright a, 
                    .powredby a,
                    .widget_academix_latest_events .action-wrap a,
                    .academix-content-area .sabbi-thumlinepost-card .btn-action,
                    .academix-content-area a.btn-action,
                    .academix-content-area .pdf-link,
                    .journal-papers-doi a,
                    .academix-content-area .btn-expand,
                    .academix-content-area .timeline-meta .staff-title a,
                    .widget td#today a, .widget tr td#prev a,
                    .icon-card .icon-card-limn > i,
                     .btn-gules,
                    .sabbi-breadcrumb a,
                    .kc-blog-posts .content .kc-post-2-button, .kc-blog-posts .kc-list-item-2 .kc-post-2-button,
                    .kc-blog-posts .content .kc-post-2-button i, .kc-blog-posts .kc-list-item-2 .kc-post-2-button i,
                    .pub-item.with-icon .elem-wrapper i,
                    .page-numbers > li > a',
                ),

                'title'       => esc_html__('Link Font Color', 'academix'),
                'subtitle' => esc_html__( 'Pick a color for the website link font color.', 'academix' ),
                'default'     => ''
            ),
	        
	        array(
		          'id'          => 'academix_global_link_hover_font_color',
		          'type'        => 'color',
		          'transparent' => false,
		          'output'    => array(
			          'color' => '.footer-bar .copyright a:hover, .powredby a:hover, .kc-blog-posts .content .kc-post-2-button:hover, .kc-blog-posts .kc-list-item-2 .kc-post-2-button:hover, .kc-blog-posts .content .kc-post-2-button:hover i, .kc-blog-posts .kc-list-item-2 .kc-post-2-button:hover i, .sabbi-breadcrumb a:hover, .journal-papers-doi a:hover, .blog a.btn.btn-unsolemn.btn-action.read-more:hover, .widget ul li a:hover, a.comment-reply-link:hover, .logged-in-as a:hover, .page-numbers > li:hover > a, .page-numbers > li:hover > span, .post-time li a:hover, .blog .entry-header h3.entry-title a:hover',
		          ),

		          'title'       => esc_html__('Link Font Hover Color', 'academix'),
		          'subtitle' => esc_html__( 'Pick a color for the website link font hover color.', 'academix' ),
		          'default'     => ''
	          ),
	        
	        array(
		          'id'          => 'academix_global_link_bg_color',
		          'type'        => 'color',
		          'transparent' => false,
		          'output'    => array(
			          'background-color' => '
                     .contact-form .fluentform .ff-btn,
                     .btn-gules,
                     .nav-btn-wrap .btn-primary,
                     .sabbi-page-header .page-title,
                     button, input[type="button"], input[type="reset"], input[type="submit"],
                     .widget .tagcloud a,
                     .page-numbers > li a:hover, .page-numbers > li > .current,
                     .blog .nav-previous, .blog .nav-next,
                     .comments-area .comment-form input[type="submit"],
                     .blog-banner.sabbi-page-header,
                     .navbar-nav-hov_underline .navbar-nav .dropdown-menu:before,
                     .lil-line:before,
                     .journal-papers-nav-list > li.active a,
                     .sec-navigate-wrap .seq-next, .sec-navigate-wrap .seq-prev,
                       .mean-container a.meanmenu-reveal span
                     ',
			          'border-color' => '.nav-btn-wrap .btn-primary, .navbar-nav-hov_underline .navbar-nav li.active a, .navbar-nav-hov_underline > .navbar-nav > li > a:hover, .navbar-nav-hov_underline > .navbar-nav > li > a:focus, button, input[type="button"], input[type="reset"], input[type="submit"], .widget .tagcloud a, .widget.widget_search .search-form .input-group input, .comments-area .comment-form input[type="submit"], blockquote,body.kc-css-system .divider_line div.divider_inner, .pub-item.with-icon .elem-wrapper i',
		          ),

		          'title'       => esc_html__('Button Background Color', 'academix'),
		          'subtitle' => esc_html__( 'Pick a color for the website button background color.', 'academix' ),
		          'default'     => ''
	          ), 
	        array(
		          'id'          => 'academix_global_link_bg_font_color',
		          'type'        => 'color',
		          'transparent' => false,
		          'output'    => array(
			          'color' => '
			          .academix-content-area a.btn-action.btn-gules,
                     .contact-form .fluentform .ff-btn,
                     .btn-gules,
                     .nav-btn-wrap .btn-primary,
                     .sabbi-page-header .page-title,
                     button, input[type="button"], input[type="reset"], input[type="submit"],
                     .widget .tagcloud a,
                     .page-numbers > li a:hover, .page-numbers > li > .current,
                     .blog .nav-previous, .blog .nav-next,
                     .comments-area .comment-form input[type="submit"],
                     .blog-banner.sabbi-page-header,
                     .navbar-nav-hov_underline .navbar-nav .dropdown-menu:before,
                     .lil-line:before,
                     .journal-papers-nav-list > li.active a,
                     .sec-navigate-wrap .seq-next, .sec-navigate-wrap .seq-prev,
                       .mean-container a.meanmenu-reveal span
                     ',
		          ),

		          'title'       => esc_html__('Button Font Color', 'academix'),
		          'subtitle' => esc_html__( 'Pick a color for the website button background font color.', 'academix' ),
		          'default'     => ''
	          ),

	          array(
		          'id'          => 'academix_global_link_hover_bg_color',
		          'type'        => 'color',
		          'transparent' => false,
		          'output'    => array(
			          'background-color' => '.academix-content-area .btn-gules:hover, .nav-btn-wrap .btn-primary:hover, .journal-papers-nav-list > li a:hover, .journal-papers-nav-list > li a:focus, .widget .tagcloud a:hover, .blog .nav-previous:hover, .blog .nav-next:hover, .comments-area .comment-form input[type="submit"]:hover, .widget.widget_search .search-form .input-group span button:hover',
			          'border-color' => '.nav-btn-wrap .btn-primary, .nav-btn-wrap .btn-primary:hover, .journal-papers-nav-list > li a:hover, .journal-papers-nav-list > li a:focus, .widget .tagcloud a:hover, .blog .nav-previous:hover, .blog .nav-next:hover, .comments-area .comment-form input[type="submit"]:hover, .widget.widget_search .search-form .input-group span button:hover',
		          ),

		          'title'       => esc_html__('Button Background Hover Color', 'academix'),
		          'subtitle' => esc_html__( 'Pick a color for the website button background hover color.', 'academix' ),
		          'default'     => ''
	          ),

	          array(
		          'id'          => 'academix_global_link_hover_bg_font_color',
		          'type'        => 'color',
		          'transparent' => false,
		          'output'    => array(
			          'color' => '.academix-content-area a.btn-action.btn-gules:hover, .nav-btn-wrap .btn-primary:hover, .widget .tagcloud a:hover, .blog .nav-previous:hover a, .blog .nav-next:hover a, .comments-area .comment-form input[type="submit"]:hover, .widget.widget_search .search-form .input-group span button:hover, .page-numbers > li a:hover, .page-numbers > li:hover > .current',
		          ),

		          'title'       => esc_html__('Button Hover Font Color', 'academix'),
		          'subtitle' => esc_html__( 'Pick a color for the website button hover font color.', 'academix' ),
		          'default'     => ''
	          ),
        )
    ));




    //Header
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Header', 'academix'),
        'id'     => 'header_settings',
        'icon'   => 'el el-arrow-up',
    ));

    // Header logo
    Redux::setSection( $opt_name, array(
        'title' => esc_html__('Logo', 'academix'),
        'id'    => 'logo',
        'desc'  => esc_html__('This section contains header menu logo settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_website_logo_type',
                'type'        => 'switch',
                'title'       => esc_html__('Logo type', 'academix'),
                'subtitle'    => esc_html__( 'Select a logo type to display logo in header menu.', 'academix'),
                'default'     => 1,
                'on'          => esc_html__( 'Image', 'academix' ),
                'off'         => esc_html__( 'Text', 'academix' ),
            ),
            array(
                'id'          => 'academix_website_image_logo',
                'type' => 'media',
                'url' => false,
                'title' => esc_html__('Site Logo', 'academix'),
                'subtitle' => esc_html__('Upload logo image for your Website. Otherwise site title will be displayed in place of logo', 'academix'),
                'desc'     => esc_html__('Upload Logo: png, jpg, gif and image size is 310 x 73px', 'academix'),
                'default' => array(
                    'url' => get_template_directory_uri() . '/assets/img/logo.png',
                ),
                'required'    => array('academix_website_logo_type', '=', 1),
            ),
	        array(
		        'id'          => 'academix_site_image_logo_dimensions',
		        'type'        => 'dimensions',
		        'units'       => array('em','px','%'),
		        'title'       => esc_html__('Set Logo Height/Width', 'academix'),
		        'default'     => array(
			        'width'   => '',
			        'height'  => ''
		        ),
		        'required'    => array('academix_website_logo_type', '=', 1),
	        ),
            array(
                'id'          => 'academix_website_text_logo',
                'type' => 'text',
                'title' => esc_html__('Text Logo', 'academix'),
                'subtitle' => esc_html__('Provide the text to display in header menu logo', 'academix'),
                'required'    => array('academix_website_logo_type', '=', 0),
                'default' => 'academix'
            ),
            array(
                'id'          => 'academix_text_logo_color',
                'type'        => 'color',
                'transparent' => false,
                'output' => array('a.navbar-brand h2'),
                'required'    => array('academix_website_logo_type', '=', 0),
                'title'       => esc_html__('Text Logo Font Color', 'academix'),
                'subtitle' => esc_html__( 'Pick a color for the text logo font color.', 'academix' ),
                'default'     => '#000'
            ),
           
        )
    ));

    // Menu
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Menu', 'academix'),
        'id'     => 'menu_settings',
        'icon'   => 'el el-lines',
    ));

    // Main Menu
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Main Menu', 'academix'),
        'id'     => 'academix_main_menu',
        'desc'  => esc_html__('This section contains main menu settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_menu_type',
                'type'        => 'switch',
                'title'       => esc_html__('Enable/Disable Sticky Menu?', 'academix'),
                'default'     => 1,
                'on'        => esc_html__( 'Enable', 'academix' ),
                'off'       => esc_html__( 'Disable', 'academix' ),
                'class'     => esc_html__('scrolly', 'academix')
            ),
            array(
                'id'          => 'academix_main_menu_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Main Menu Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/bottom padding. In pixels for main menu', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.navbar-white'),
                'left'    => false,
                'right'    => false,
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '0px', 
                    'padding-bottom'  => '0px', 
                    'units'           => 'px', 
                )
            ),
            array(
                'id'          => 'academix_main_menu_item_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Main Menu Item Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/right/bottom/left padding. In pixels for main menu item', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.navbar-nav>li>a'),
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '15px',
                    'padding-right'  => '15px', 
                    'padding-bottom'  => '15px', 
                    'padding-left'  => '15px', 
                    'units'           => 'px', 
                )
            ),
            array(
                'id'          => 'academix_main_menu_dropdown_width',
                'type'        => 'dimensions',
                'title'       => esc_html__('Main Menu Dropdown Width', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the width. In pixels for main menu dropdown', 'academix' ),
                'height'          => false,
                'output' => array('.navbar-nav-hov_underline .navbar-nav .dropdown-menu'),
                'units'   => array('px'),
                'default'        => array(
                    'width'  => 210,
                )
            ),
            array(
                'id'          => 'academix_main_menu_dropdown_item_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Main Menu Dropdown Item Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/right/bottom/left padding. In pixels for main menu dropdown item', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.navbar-nav-hov_underline .navbar-nav .dropdown-menu li a'),
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '10px',
                    'padding-right'  => '15px', 
                    'padding-bottom'  => '10px', 
                    'padding-left'  => '15px', 
                    'units'           => 'px', 
                )
            ),

        )
    ));

    // Main Menu styling
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Main Menu Styling', 'academix'),
        'id'     => 'main_menu_styling',
        'desc'  => esc_html__('This section contains header main menu styling options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_main_menu_font_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('Main Menu Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all menus.', 'academix'),
                'desc'       => esc_html__('Montserrat is default font', 'academix'),
                'google'      => true, 
                'font-style' => false,
                'color'         => false,
                'text-align'    => false,
                'output'      => array('.navbar-nav'),
                'default'     => array(
                  'font-family' => 'Montserrat',
                  'google'      => true,
                  'font-size'   => '12px',
                  'line-height' => '20px',
                  'font-weight' => '400'
                 ),
            ),
            array(
                'id'          => 'academix_main_menu_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Main Menu Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the main menu background color. ( default: #fff )', 'academix' ),
                'default'     => '#fff',
                'output'    => array('background-color' => '.navbar-white',
                                'color' => '')
            ),
            array(
                'id'            => 'academix_main_menu_f_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Main Menu Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the main menu font color. ( default: #000000 )', 'academix' ),
                'default'       => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.navbar-nav-hov_underline .navbar-nav li a'),
            ),
            array(
                'id'            => 'academix_main_menu_f_hover_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Main Menu Font Hover Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the main menu font hover color. ( default: #000000 )', 'academix' ),
                'default'       => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.navbar-nav-hov_underline .navbar-nav li a:hover'),
            ),
            array(
                'id'            => 'academix_main_menu_itam_border',
                'type'          => 'border',
                'title'         => esc_html__( 'Main Menu Item Hover Border', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the main menu item hover border color. ( default: #D30015 )', 'academix' ),
                'all' => false,
                'top' => false,
                'right' => false,
                'left' => false,
                'default'  => array(
                    'border-color'  => '', 
                    'border-style'  => '', 
                    'border-bottom' => '', 
                ),
                'output'    => array('.navbar-nav-hov_underline > .navbar-nav > li > a:hover, .navbar-nav-hov_underline > .navbar-nav > li > a:focus'),
            ),
            array(
                'id'            => 'academix_main_menu_active_itam_border',
                'type'          => 'border',
                'title'         => esc_html__( 'Main Menu Active Item Border', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the main menu active item border color. ( default: #D30015 )', 'academix' ),
                'all' => false,
                'top' => false,
                'right' => false,
                'left' => false,
                'default'  => array(
                    'border-color'  => '', 
                    'border-style'  => '', 
                    'border-bottom' => '',
                ),
                'output'    => array('.navbar-nav-hov_underline > .navbar-nav > li.active > a'),
            ),
            array(
                'id'            => 'academix_main_menu_dropdown_top_border',
                'type'        => 'color',
                'transparent' => false,
                'title'         => esc_html__( 'Main Menu Dropdown Down Border Top Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the main menu dropdown border top color. ( default: #D30015 )', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '.navbar-nav-hov_underline .navbar-nav .dropdown-menu:before',
                                'color' => '')
            ),
	        array(
		        'id'            => 'academix_main_menu_dropdown_item_border',
		        'type'          => 'border',
		        'title'         => esc_html__( 'Main Menu Dropdown Down Menu Item Border Bottom', 'academix' ),
		        'all' => false,
		        'top' => false,
		        'right' => false,
		        'left' => false,
		        'default'  => array(
			        'border-color'  => '',
			        'border-style'  => '',
			        'border-bottom' => '',
		        ),
		        'output'    => array('.navbar-nav-hov_underline .navbar-nav .dropdown-menu li a'),
	        ),
            array(
                'id'          => 'academix_main_menu_dropdown_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Main Menu Dropdown Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the main menu dropdown background color. ( default: #ffffff )', 'academix' ),
                'default'     => '#fff',
                'output'    => array('background-color' => '.navbar-nav-hov_underline .navbar-nav .dropdown-menu',
                                'color' => '')
            ),
            array(
                'id'            => 'academix_main_menu_dropdown_f_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Main Menu Dropdown Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the main menu dropdown font color. ( default: #000000 )', 'academix' ),
                'default'       => '#000000',
                'output'    => array('background-color' => '',
                                'color' => '.navbar-nav-hov_underline .navbar-nav .dropdown-menu > li > a'),
            ),
            array(
                'id'            => 'academix_main_menu_dropdown_f_hover_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Main Menu Dropdown Font Hover Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the main menu dropdown font hover color. ( default: #000000 )', 'academix' ),
                'default'       => '#000000',
                'output'    => array('background-color' => '',
                                'color' => '.navbar-nav-hov_underline .navbar-nav .dropdown-menu > li > a:hover'),
            ),
            
        )
    ));

    // Mobile Menu
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Mobile Menu', 'academix'),
        'id'     => 'academix_mobile_menu',
        'desc'  => esc_html__('This section contains mobile menu settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
	        array(
		        'id'          => 'academix_mobile_menu_logo_dimensions',
		        'type'        => 'dimensions',
		        'units'       => array('em','px','%'),
		        'title'       => esc_html__('Set Logo Height/Width', 'academix'),
		        'default'     => array(
			        'width'   => '',
			        'max-height'  => ''
		        ),
		        'output' => array('.mean-nav .mobile-menu-logo a.navbar-brand img'),
	        ),
            array(
                'id'          => 'academix_mobile_menu_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Mobile Menu Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/bottom padding. In pixels for mobile menu', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.mean-container .mean-nav'),
                'left'    => false,
                'right'    => false,
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '0px', 
                    'padding-bottom'  => '0px', 
                    'units'           => 'px', 
                )
            ),
            array(
                'id'          => 'academix_mobile_menu_item_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Mobile Menu Item Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/right/bottom/left padding. In pixels for mobile menu item', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.mean-container .mean-nav ul li a'),
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '8px',
                    'padding-right'  => '', 
                    'padding-bottom'  => '', 
                    'padding-left'  => '15px', 
                    'units'           => 'px', 
                )
            ),
            array(
                'id'          => 'academix_mobile_menu_dropdown_item_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Mobile Menu Dropdown Item Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/right/bottom/left padding. In pixels for mobile menu dropdown item', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.mean-container .mean-nav ul li li a'),
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '8px',
                    'padding-right'  => '', 
                    'padding-bottom'  => '', 
                    'padding-left'  => '', 
                    'units'           => 'px', 
                )
            ),

            array(
                'id'          => 'academix_mobile_menu_font_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('Mobile Menu Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all menus.', 'academix'),
                'desc'       => esc_html__('Montserrat is default font', 'academix'),
                'google'      => true, 
                'font-style' => false,
                'color'         => false,
                'text-align'    => false,
                'output'      => array('.mean-container .mean-nav'),
                'default'     => array(
                  'font-family' => 'Montserrat',
                  'google'      => true,
                  'font-size'   => '12px',
                  'line-height' => '26px',
                  'font-weight' => '400'
                 ),
            ),
	        array(
		        'id'          => 'academix_mobile_menu_icon_bg_color',
		        'type'        => 'color',
		        'transparent' => false,
		        'title'       => esc_html__( 'Mobile Menu Icon Background Color', 'academix' ),
		        'subtitle'    => esc_html__( 'Pick a color for the mobile menu icon background color. ( default: #D30015 )', 'academix' ),
		        'default'     => '',
		        'output'    => array('background-color' => '.mean-container a.meanmenu-reveal span',
		                             'color' => '')
	        ),
	        array(
		        'id'          => 'academix_mobile_menu_icon_close_bg_color',
		        'type'        => 'color',
		        'transparent' => false,
		        'title'       => esc_html__( 'Mobile Menu Icon Close Background Color', 'academix' ),
		        'subtitle'    => esc_html__( 'Pick a color for the mobile menu icon close background color. ( default: #D30015 )', 'academix' ),
		        'default'     => '',
		        'output'    => array('background-color' => '.mean-container a.meanmenu-reveal span',
		                             'color' => '.mean-container a.meanmenu-reveal')
	        ),
	        array(
		        'id'          => 'academix_mobile_menu_expand_bg_color',
		        'type'        => 'color',
		        'transparent' => false,
		        'title'       => esc_html__( 'Mobile Menu Expand Background Color', 'academix' ),
		        'subtitle'    => esc_html__( 'Pick a color for the mobile menu expand background color. ( default: #D30015 )', 'academix' ),
		        'default'     => '',
		        'output'    => array('background-color' => '',
		                             'color' => '.mean-container .mean-nav ul li a.mean-expand:hover')
	        ),
	        array(
		        'id'          => 'academix_mobile_menu_expand_icon_color',
		        'type'        => 'color',
		        'transparent' => false,
		        'title'       => esc_html__( 'Mobile Menu Expand Icon Color', 'academix' ),
		        'subtitle'    => esc_html__( 'Pick a color for the mobile menu expand icon color. ( default: #fff )', 'academix' ),
		        'default'     => '',
		        'output'    => array('background-color' => '.mean-container .mean-nav ul li a.mean-expand:hover',
		                             'color' => '')
	        ),
            array(
                'id'          => 'academix_mobile_menu_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Mobile Menu Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the mobile menu background color. ( default: #fff )', 'academix' ),
                'default'     => '#fff',
                'output'    => array('background-color' => '.mean-container .mean-nav',
                                'color' => '')
            ),
            array(
                'id'            => 'academix_mobile_menu_f_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Mobile Menu Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the mobile menu font color. ( default: #000000 )', 'academix' ),
                'default'       => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.mean-container .mean-nav ul li a'),
            ),
            array(
                'id'            => 'academix_mobile_menu_f_hover_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Mobile Menu Font Hover Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the mobile menu font hover color. ( default: #000000 )', 'academix' ),
                'default'       => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.mean-container .mean-nav ul li a:hover'),
            ),
            array(
                'id'            => 'academix_mobile_menu_itam_border',
                'type'          => 'border',
                'title'         => esc_html__( 'Mobile Menu Item Hover Border', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the mobile menu item hover border color. ( default: #D30015 )', 'academix' ),
                'all' => false,
                'bottom' => false,
                'right' => false,
                'left' => false,
                'default'  => array(
                    'border-color'  => '', 
                    'border-style'  => '', 
                    'border-top' => '', 
                ),
                'output'    => array('.mean-container .mean-nav ul li a, .mean-container .mean-nav ul li li a'),
            ),
            array(
                'id'          => 'academix_mobile_menu_dropdown_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Mobile Menu Dropdown Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the mobile menu dropdown background color. ( default: #ffffff )', 'academix' ),
                'default'     => '#fff',
                'output'    => array('background-color' => '.mean-container .mean-nav ul li li a',
                                'color' => '')
            ),
            array(
                'id'            => 'academix_mobile_menu_dropdown_f_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Mobile Menu Dropdown Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the mobile menu dropdown font color. ( default: #000000 )', 'academix' ),
                'default'       => '#000000',
                'output'    => array('background-color' => '',
                                'color' => '.mean-container .mean-nav ul li li a'),
            ),
            array(
                'id'            => 'academix_mobile_menu_dropdown_f_hover_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Mobile Menu Dropdown Font Hover Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the mobile menu dropdown font hover color. ( default: #000000 )', 'academix' ),
                'default'       => '#000000',
                'output'    => array('background-color' => '',
                                'color' => '.mean-container .mean-nav ul li li a:hover'),
            ),

        )
    ));

    // Main Menu button
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Main Menu Button', 'academix'),
        'id'     => 'main_menu_button',
        'desc'  => esc_html__('This section contains header main menu button options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_display_main_menu_btn',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Main Menu Button', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display main menu button?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
            array(
                'id'          => 'academix_main_menu_btn_text',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'        => 'text',
                'title'       => esc_html__( 'Button Text', 'academix' ),
                'subtitle'    => esc_html__( 'Provide a text for the main menu button.', 'academix' ),
                'default'     => 'Join Research'
            ),
            array(
                'id'          => 'academix_main_menu_btn_link',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'        => 'text',
                'title'       => esc_html__( 'Button Link', 'academix' ),
                'subtitle'    => esc_html__( 'Provide a link for the main menu button.', 'academix' ),
                'default'     => '#'
            ),
            array(
                'id'          => 'academix_main_menu_button_font_size',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'        => 'typography', 
                'title'       => esc_html__('Button Font Size', 'academix'),
                'subtitle'       => esc_html__('Provide the font size for main menu button text. Enter value including any valid CSS unit, ex: 14px.', 'academix'),
                'google'      => false, 
                'font-family' => false,
                'font-weight' => false,
                'line-height' => false,
                'font-size'  => true,
                'font-style' => false,
                'color'         => false,
                'text-align'    => false,
                'output'      => array('.navbar-nav'),
                'default'     => array(
                  'font-size'   => '14px',
                 ),
            ),
            array(
                'id'          => 'academix_btn_bg_color',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Button Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the button background color. ( default: #D30015 )', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '.nav-btn-wrap .btn-primary',
                                'color' => '')
            ),
            array(
                'id'            => 'academix_btn_border_color',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'          => 'border',
                'title'         => esc_html__( 'Button Border Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the button border color. ( default: #D30015 )', 'academix' ),
                'all' => true,
                'default'  => array(
                    'border-color'  => '', 
                    'border-style'  => '', 
                    'border-top' => '',
                    'border-right' => '',
                    'border-bottom' => '',
                    'border-left' => '',
                ),
                'output'    => array('.nav-btn-wrap .btn-primary'),
            ),
            array(
                'id'          => 'academix_btn_font_color',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Button Font Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the button font color. ( default: #fff )', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '',
                                'color' => '.nav-btn-wrap .btn-primary')
            ),
            array(
                'id'          => 'academix_btn_hover_bg_color',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Button Hover Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the button hover background color. ( default: #b70012 )', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '.nav-btn-wrap .btn-primary:hover',
                                'color' => '')
            ),
            array(
                'id'            => 'academix_btn_hover_border_color',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'          => 'border',
                'title'         => esc_html__( 'Button Hover Border Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the button hover border color. ( default: #b70012 )', 'academix' ),
                'all' => true,
                'default'  => array(
                    'border-color'  => '', 
                    'border-style'  => '', 
                    'border-top' => '',
                    'border-right' => '',
                    'border-bottom' => '',
                    'border-left' => '',
                ),
                'output'    => array('.nav-btn-wrap .btn-primary:hover'),
            ),
            array(
                'id'          => 'academix_btn_hover_font_color',
                'required'    => array('academix_display_main_menu_btn', '=', 1),
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Button Hover Font Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the button hover font color. ( default: #fff )', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '',
                                'color' => '.nav-btn-wrap .btn-primary:hover')
            ),
            
        )
    ));


    // Page Banner Settings
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Page Banner', 'academix'),
        'id'     => 'page_banner_settings',
        'icon'   => 'el el-adjust-alt',
    ));

    // page banner
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Page Banner', 'academix'),
        'id'     => 'academix_page_banner',
        'desc'  => esc_html__('This section contains page banner settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
	        array(
		        'id'          => 'academix_display_page_banner',
		        'type'        => 'switch',
		        'title'       => esc_html__('Display / Hide Page Banner', 'academix'),
		        'subtitle'    => esc_html__( 'Do you want to display page banner section?', 'academix' ),
		        'default'     => 1,
		        'on'        => esc_html__( 'Display', 'academix' ),
		        'off'       => esc_html__( 'Hide', 'academix' ),
	        ),
            array(
                'id'          => 'academix_display_page_banner_on_mobile',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Page Banner On Mobile', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display page banner section on mobile device?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
	        array(
		        'id'          => 'academix_page_banner_height',
		        'type'        => 'dimensions',
		        'title'       => esc_html__('Page Banner Height', 'academix'),
		        'subtitle'   => esc_html__( 'You can provide the height in pixels for page banner section', 'academix' ),
		        'output' => array('.sabbi-page-header.page-header-lg'),
		        'width'    => false,
		        'units'   => array('px'),
		        'default'            => array(
			        'height'     => '',
			        'units'           => 'px',
		        )
	        ),
            array(
                'id'          => 'academix_page_banner_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Page Banner Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the page banner background color.', 'academix' ),
                'desc'        => esc_html__( 'if you don\'t set any featured image from page setting option then it will work', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '.page-header-lg.sabbi-page-header',
                                'color' => ''),
            ),
            array(
                'id'          => 'academix_page_banner_title_bg_color',
                'type'        => 'color_rgba',
                'title'       => esc_html__( 'Title Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the page banner title background color. ( default: #d94553 )', 'academix' ),
                'default'     => array(
                        'color' => '',
                        'alpha' => '',
                ),
                'output'    => array('background-color' => '.sabbi-page-header .page-title',
                                'color' => ''),
            ),
            array(
                'id'          => 'academix_page_banner_font_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('Page Banner Font Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for page banner.', 'academix'),
                'desc'       => esc_html__('Montserrat is default font', 'academix'),
                'google'      => true, 
                'font-style' => false,
                'color'         => false,
                'text-align'    => false,
                'output'      => array('.sabbi-page-header .page-title'),
                'default'     => array(
                  'font-family' => 'Montserrat',
                  'google'      => true,
                  'font-size'   => '28px',
                  'line-height' => '40px',
                  'font-weight' => '700'
                 ),
            ),
            array(
                'id'            => 'academix_page_banner_font_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Page Banner Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the page banner font color. ( default: #ffffff )', 'academix' ),
                'default'       => '#ffffff',
                'output'    => array('background-color' => '',
                                'color' => '.sabbi-page-header .page-title'),
            ),
           
        )
    ));

     // page breadcrumbs
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Page Breadcrumbs', 'academix'),
        'id'     => 'academix_page_breadcrumbs',
        'desc'  => esc_html__('This section contains page breadcrumbs settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
	        array(
		        'id'          => 'academix_display_page_breadcrumbs',
		        'type'        => 'switch',
		        'title'       => esc_html__('Display / Hide Page Breadcrumbs', 'academix'),
		        'subtitle'    => esc_html__( 'Do you want to display page breadcrumbs section?', 'academix' ),
		        'default'     => 1,
		        'on'        => esc_html__( 'Display', 'academix' ),
		        'off'       => esc_html__( 'Hide', 'academix' ),
	        ),
            array(
                'id'          => 'academix_display_page_breadcrumbs_on_mobile',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Page Breadcrumbs On Mobile', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display page breadcrumbs section on mobile device?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
            array(
                'id'          => 'academix_page_breadcrumbs_separator',
                'type'        => 'text',
                'title'       => esc_html__( 'Page Breadcrumbs Separator', 'academix' ),
                'subtitle'    => esc_html__( 'Provide a separator for the page breadcrumbs ( default: / )', 'academix' ),
                'default'     => '/'
            ),
            array(
                'id'          => 'academix_page_breadcrumbs_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Page Breadcrumbs Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/right/bottom/left padding. In pixels for page breadcrumbs section', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.breadcrumb'),
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '8px', 
                    'padding-right'   => '15px',
                    'padding-bottom'  => '8px', 
                    'padding-left'    => '15px', 
                    'units'           => 'px', 
                )
            ),
            array(
                'id'          => 'academix_page_breadcrumbs_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Page Breadcrumbs Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the page breadcrumbs background color. ( default: #EDF2F6 )', 'academix' ),
                'default'     => '#EDF2F6',
                'output'    => array('background-color' => '.auth-breadcrumb-wrap',
                                'color' => ''),
            ),
            array(
                'id'            => 'academix_page_breadcrumbs_font_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Page Breadcrumbs Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the page breadcrumbs font color. ( default: #D30015 )', 'academix' ),
                'default'       => '',
                'output'    => array('background-color' => '',
                                'color' => '.sabbi-breadcrumb a'),
            ),
             array(
                'id'            => 'academix_page_breadcrumbs_font_active_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Page Breadcrumbs Font Active Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the page breadcrumbs font active color. ( default: #000000 )', 'academix' ),
                'default'       => '#000000',
                'output'    => array('background-color' => '',
                                'color' => '.sabbi-breadcrumb'),
            ),
            array(
                'id'            => 'academix_page_breadcrumbs_separator_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Page Breadcrumbs Separator Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the page breadcrumbs separator color. ( default: #000 )', 'academix' ),
                'default'       => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.sabbi-breadcrumb > li > span.separator'),
            )
           
        )
    ));

    // Event
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Event', 'academix'),
        'id'     => 'event_settings',
        'icon'   => 'el el-calendar',
    ));


    // Event
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Single Event', 'academix'),
        'id'     => 'academix_single_event',
        'desc'  => esc_html__('This section contains single event settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_display_event_gallery',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Event Gallery?', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display event gallery?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
            array(
                'id'          => 'academix_event_gallery_title_text',
                'type'        => 'text',
                'title'       => esc_html__('Event Gallery Title Text', 'academix'),
                'default'     => esc_html__('From Media', 'academix'),
                'required'    => array('academix_display_event_gallery', '=', 1),
            ),
        )
    ));


     // Blog
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Blog', 'academix'),
        'id'     => 'blog_settings',
        'icon'   => 'el el-edit',
    ));

    // Blog banner
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Blog Banner', 'academix'),
        'id'     => 'academix_blog_banner',
        'desc'  => esc_html__('This section contains blog banner settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
	        array(
		        'id'          => 'academix_display_blog_banner',
		        'type'        => 'switch',
		        'title'       => esc_html__('Display / Hide Blog Banner', 'academix'),
		        'subtitle'    => esc_html__( 'Do you want to display blog banner section?', 'academix' ),
		        'default'     => 1,
		        'on'        => esc_html__( 'Display', 'academix' ),
		        'off'       => esc_html__( 'Hide', 'academix' ),
	        ),
            array(
                'id'          => 'academix_display_blog_banner_on_mobile',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Blog Banner On Mobile', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display blog banner section on mobile device?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
            array(
                'id'          => 'academix_blog_banner_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Blog Banner Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the blog breadcrumbs background color. ( default: #b03838 ) . if you don\'t set any banner image or background-color in blog page then it will work.', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '.blog-banner.sabbi-page-header',
                                'color' => ''),
            ),
            array(
                'id'          => 'academix_blog_banner_title_bg_color',
                'type'        => 'color_rgba',
                'title'       => esc_html__( 'Title Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the blog banner title background color. ( default: #d94553 )', 'academix' ),
                'default'     => array(
                        'color' => '',
                        'alpha' => '',
                ),
                'output'    => array('background-color' => '.blog-banner.sabbi-page-header .page-title',
                                'color' => ''),
            ),
            array(
                'id'          => 'academix_page_banner_font_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('Page Banner Font Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for blog banner.', 'academix'),
                'desc'       => esc_html__('Montserrat is default font', 'academix'),
                'google'      => true, 
                'font-style' => false,
                'color'         => false,
                'text-align'    => false,
                'output'      => array('.blog-banner.sabbi-page-header .page-title'),
                'default'     => array(
                  'font-family' => 'Montserrat',
                  'google'      => true,
                  'font-size'   => '28px',
                  'line-height' => '40px',
                  'font-weight' => '700'
                 ),
            ),
            array(
                'id'            => 'academix_blog_banner_font_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Blog Banner Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the blog banner font color. ( default: #ffffff )', 'academix' ),
                'default'       => '#ffffff',
                'output'    => array('background-color' => '',
                                'color' => '.blog-banner.sabbi-page-header .page-title'),
            ),
           
        )
    ));

    // Blog breadcrumbs
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Blog Breadcrumbs', 'academix'),
        'id'     => 'academix_blog_breadcrumbs',
        'desc'  => esc_html__('This section contains blog breadcrumbs settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
	        array(
		        'id'          => 'academix_display_blog_breadcrumbs',
		        'type'        => 'switch',
		        'title'       => esc_html__('Display / Hide Blog Breadcrumbs', 'academix'),
		        'subtitle'    => esc_html__( 'Do you want to display Blog breadcrumbs section?', 'academix' ),
		        'default'     => 1,
		        'on'        => esc_html__( 'Display', 'academix' ),
		        'off'       => esc_html__( 'Hide', 'academix' ),
	        ),
            array(
                'id'          => 'academix_display_blog_breadcrumbs_on_mobile',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Blog Breadcrumbs On Mobile', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display Blog breadcrumbs section on mobile device?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
            array(
                'id'          => 'academix_blog_breadcrumbs_separator',
                'type'        => 'text',
                'title'       => esc_html__( 'Blog Breadcrumbs Separator', 'academix' ),
                'subtitle'    => esc_html__( 'Provide a separator for the blog breadcrumbs ( default: / )', 'academix' ),
                'default'     => '/'
            ),
           array(
                'id'          => 'academix_blog_breadcrumbs_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Blog Breadcrumbs Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/right/bottom/left padding. In pixels for blog breadcrumbs section', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.blog-breadcrumb .breadcrumb'),
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '8px', 
                    'padding-right'   => '15px',
                    'padding-bottom'  => '8px', 
                    'padding-left'    => '15px', 
                    'units'           => 'px', 
                )
            ),
            array(
                'id'          => 'academix_blog_breadcrumbs_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Blog Breadcrumbs Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the blog breadcrumbs background color. ( default: #EDF2F6 )', 'academix' ),
                'default'     => '#EDF2F6',
                'output'    => array('background-color' => '.blog-breadcrumb.auth-breadcrumb-wrap',
                                'color' => ''),
            ),
            array(
                'id'            => 'academix_blog_breadcrumbs_font_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Blog Breadcrumbs Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the blog breadcrumbs font color. ( default: #D30015 )', 'academix' ),
                'default'       => '',
                'output'    => array('background-color' => '',
                                'color' => '.blog-breadcrumb .sabbi-breadcrumb a'),
            ),
             array(
                'id'            => 'academix_blog_breadcrumbs_font_active_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Blog Breadcrumbs Font Active Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the blog breadcrumbs font color. ( default: #000 )', 'academix' ),
                'default'       => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.blog-breadcrumb .breadcrumb'),
            ),
            array(
                'id'            => 'academix_blog_breadcrumbs_separator_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Blog Breadcrumbs Separator Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the blog breadcrumbs separator color. ( default: #000 )', 'academix' ),
                'default'       => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.blog-breadcrumb .sabbi-breadcrumb > li > span.separator'),
            ),
        )
    ));

    // Blog general
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Blog General', 'academix'),
        'id'     => 'academix_blog_general',
        'desc'  => esc_html__('This section contains blog general settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_blog_page_title',
                'type'        => 'text',
                'title'       => esc_html__('Blog page title', 'academix'),
                'subtitle'    => esc_html__( 'Provide the title text that displays in the page title bar of the assigned blog page.', 'academix' ),
                'default'     => 'Blog',
            ),
            array(
                'id'          => 'academix_blog_read_more',
                'type'        => 'text',
                'title'       => esc_html__( 'Blog Button Text', 'academix' ),
                'subtitle'    => esc_html__( 'Provide the button text that displays in the blog post button. ( Default: Read more )', 'academix' ),
                'default'     => 'Read More'
            ),
        )
    ));

    // Sidebars
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Sidebars', 'academix'),
        'id'     => 'sidebars_settings',
        'icon'   => 'el el-website',
    ));

    // Blog sidebar
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Blog Sidebar', 'academix'),
        'id'     => 'academix_blog_sidebar',
        'desc'  => esc_html__('This section contains blog sidebar settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_blog_sidebar',
                'type'        => 'select',
                'title'    => esc_html__( 'Select Sidebar Layout', 'academix' ),
                'options'  => array(
                    'no-sidebar'     => esc_html__( 'No Sidebar', 'academix' ),
                    'right'     => esc_html__( 'Right Sidebar', 'academix' ),
                    'left'     => esc_html__( 'Left Sidebar', 'academix' ),
                ),
                'default' => 'right',
            ),
           
        )
    ));

    // Single Blog sidebar
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Single Blog Sidebar', 'academix'),
        'id'     => 'academix_single_blog_sidebar',
        'desc'  => esc_html__('This section contains single blog sidebar settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_single_blog_sidebar',
                'type'        => 'select',
                'title'    => esc_html__( 'Select Sidebar Layout', 'academix' ),
                'options'  => array(
                    'no-sidebar'     => esc_html__( 'No Sidebar', 'academix' ),
                    'right'     => esc_html__( 'Right Sidebar', 'academix' ),
                    'left'     => esc_html__( 'Left Sidebar', 'academix' ),
                ),
                'default' => 'right',
            ),
           
        )
    ));

    // Archive sidebar
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Archive Sidebar', 'academix'),
        'id'     => 'academix_archive_sidebar',
        'desc'  => esc_html__('This section contains blog sidebar settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_archive_sidebar',
                'type'        => 'select',
                'title'    => esc_html__( 'Select Sidebar Layout', 'academix' ),
                'options'  => array(
                    'no-sidebar'     => esc_html__( 'No Sidebar', 'academix' ),
                    'right'     => esc_html__( 'Right Sidebar', 'academix' ),
                    'left'     => esc_html__( 'Left Sidebar', 'academix' ),
                ),
                'default' => 'right',
            ),
           
        )
    ));

     // Search sidebar
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Search Sidebar', 'academix'),
        'id'     => 'academix_search_sidebar',
        'desc'  => esc_html__('This section contains blog sidebar settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_search_sidebar',
                'type'        => 'select',
                'title'    => esc_html__( 'Select Sidebar Layout', 'academix' ),
                'options'  => array(
                    'no-sidebar'     => esc_html__( 'No Sidebar', 'academix' ),
                    'right'     => esc_html__( 'Right Sidebar', 'academix' ),
                    'left'     => esc_html__( 'Left Sidebar', 'academix' ),
                ),
                'default' => 'right',
            ),
           
        )
    ));

    // Footer
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Footer', 'academix'),
        'id'     => 'footer_settings',
        'icon'   => 'el el-arrow-down',
    ));

    // Footer Widgets
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Footer Widgets', 'academix'),
        'id'     => 'academix_footer_widgets',
        'desc'  => esc_html__('This section contains footer widgets settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_display_footer_widgets',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Footer Widgets', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display footer widgets section?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
            array(
                'id' => 'academix_footer_column_layout',
                'type' => 'image_select',
                'title' => esc_html__('Number of Footer Columns', 'academix'),
                'subtitle' => esc_html__('Controls the number of columns for the footer widgets.', 'academix'),
                'options' => array(
                    '1' => array(
                        'alt' => '1 Column',
                        'img' => get_template_directory_uri(). '/assets/img/col1.png'
                    ),
                    '2' => array(
                        'alt' => '2 Column',
                        'img' => get_template_directory_uri(). '/assets/img/col2.png'
                    ),
                    '3' => array(
                        'alt' => '3 Column',
                        'img' => get_template_directory_uri(). '/assets/img/col3.png'
                    ),
                    '4' => array(
                        'alt' => '4 Column',
                        'img' => get_template_directory_uri(). '/assets/img/col4.png'
                    )
                ),
                'default' => '4'
            ),
            array(
                'id'          => 'display_academix_footer_width',
                'type'        => 'switch',
                'title'       => esc_html__('100% Footer Width', 'academix'),
                'subtitle'    => esc_html__( 'Turn on to have the footer area display at 100% width according to the window size. Turn off to follow site width.', 'academix' ),
                'default'     => 0,
                'on'        => esc_html__( 'On', 'academix' ),
                'off'       => esc_html__( 'Off', 'academix' ),
            ),
            array(
                'id'          => 'academix_footer_widgets_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Footer Widgets Section Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/bottom padding. In pixels for footer widgets section', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.section-footer .footer-site-info'),
                'left'    => false,
                'right'    => false,
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '55px', 
                    'padding-bottom'  => '55px', 
                    'units'           => 'px', 
                )
            ),
            array(
                'id'          => 'academix_footer_widgets_banner_bg_type',
                'type'        => 'button_set',
                'title'       => esc_html__('Background type', 'academix'),
                'subtitle'    => esc_html__( 'Select a background type to display background in footer widgets section', 'academix' ),
                'multi'    => true,
                'options' => array(
                    '1' => esc_html__( 'Background Image', 'academix' ),
                    '2' => esc_html__( 'Background Color', 'academix' ),
                 ), 
                'default' => array('1', '2'),
            ),
            array(
                'id'          => 'academix_footer_widgets_bg_image',
                'type' => 'media',
                'url' => false,
                'title' => esc_html__('Background image', 'academix'),
                'subtitle' => esc_html__('Upload background image for your footer widgets section.', 'academix'),
                'default' => array(
                    'url' => get_template_directory_uri() . '/assets/img/footer_bg.jpg'
                ),
            ),
            array(
                'id'          => 'academix_footer_widgets_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Footer Widgets Section Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the footer widgets section background color. ( default: #EDF2F6 )', 'academix' ),
                'default'     => '#EDF2F6',
                'output'    => array('background-color' => '.site-footer.section-footer',
                                'color' => ''),
            ),
           
        )
    ));
    
    // Footer Typography
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Footer Typography', 'academix'),
        'id'     => 'footer_typography',
        'desc'   => esc_html__('This section contains footer widgets typography settings options.', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_footer_heading_typography',
                'type'        => 'typography', 
                'title'       => esc_html__('Footer Headings Typography', 'academix'),
                'output'      => array('.section-footer .footer-site-info .widget-title'),
                'units'       => array('px'),
                'subtitle'    => esc_html__('These settings control the typography for the footer headings.', 'academix'),
                'desc'       => esc_html__('Open Sans is default font', 'academix'),
                'google'      => true, 
                'font-style' => false,
                'color'         => false,
                'text-align'    => false,
                'default'     => array(
                  'font-family' => 'Montserrat',
                  'google'      => true,
                  'font-size'   => '16px',
                  'line-height' => '20px',
                  'font-weight' => '700'
                 ),
            ),

            array(
                'id'          => 'academix_footer_text_typography',
                'type'        => 'typography', 
                'title'       => esc_html__('Footer Content Typography', 'academix'),
                'output'      => array('.section-footer .widget_text .textwidget p, .section-footer p, .footer-site-info .event-list .title a, .section-footer .widget ul li a, .footer-site-info .event-list .date'),
                'units'       => array('px'),
                'subtitle'    => esc_html__('These settings control the typography for the footer content.', 'academix'),
                'desc'       => esc_html__('Open Sans is default font', 'academix'),
                'google'      => true, 
                'font-style' => false,
                'font-weight' => false,
                'color'         => false,
                'text-align'    => false,
                'default'     => array(
                  'font-family' => 'Open Sans',
                  'google'      => true,
                  'font-size'   => '14px',
                  'line-height' => '25px',
                 ),
            ),

            array(
                'id'    => 'academix_footer_heading_color',
                'type'  => 'color',
                'transparent' => false,
                'title' => esc_html__( 'Widget Heading Color', 'academix' ),
                'subtitle' => esc_html__( 'Controls the heding color of the footer widget. ( default: #000 )', 'academix' ),
                'default'     => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.section-footer .footer-site-info .widget-title'),
            ),
            array(
                'id'    => 'academix_footer_font_color',
                'type'  => 'color',
                'transparent' => false,
                'title' => esc_html__( 'Footer Font Color', 'academix' ),
                'subtitle' => esc_html__( 'Controls the text color of the footer font. ( default: #000 )', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '',
                                'color' => '.section-footer .widget_text .textwidget p, .section-footer p, .footer-site-info .event-list .date'),
            ),
            array(
                'id'    => 'academix_footer_link_color',
                'type'  => 'color',
                'transparent' => false,
                'title' => esc_html__( 'Footer Link Color', 'academix' ),
                'subtitle' => esc_html__( 'Controls the text color of the footer link font. ( default: #666 )', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '',
                                'color' => '.section-footer .widget ul li a, .footer-site-info .event-list .title a, .section-footer .widget .btn'),
            ),
            array(
                'id'    => 'academix_footer_link_hover_color',
                'type'  => 'color',
                'transparent' => false,
                'title' => esc_html__( 'Footer Link Hover Color', 'academix' ),
                'subtitle' => esc_html__( 'Controls the text hover color of the footer link font. ( default: #b70012 )', 'academix' ),
                'default'     => '',
                'output'    => array('background-color' => '',
                                'color' => '.section-footer .widget ul li a:hover, .footer-site-info .event-list .title a:hover'),
            ),
           
        )
    ));


    // Copyright
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Copyright', 'academix'),
        'id'     => 'academix_copyright',
        'desc'  => esc_html__('This section contains footer copyright settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_display_left_footer_copyright',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Copyright Left Side Information', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display footer copyright right side information?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
            array(
                'id'          => 'academix_display_right_footer_copyright',
                'type'        => 'switch',
                'title'       => esc_html__('Display / Hide Copyright Right Side Information', 'academix'),
                'subtitle'    => esc_html__( 'Do you want to display footer copyright right side information?', 'academix' ),
                'default'     => 1,
                'on'        => esc_html__( 'Display', 'academix' ),
                'off'       => esc_html__( 'Hide', 'academix' ),
            ),
            array(
                'id'          => 'academix_copyright_left_text',
                'type'        => 'editor',
                'title'       => esc_html__('Copyright Left Information', 'academix'),
                'subtitle'    => esc_html__( 'HTML tags allowed: a, br, strong, em', 'academix' ),
                'default'     => esc_html__( 'Copyright', 'academix') . "&copy; ".esc_html__( '2017 - ', 'academix' )."<a href='".esc_url('https://themeforest.net/user/webtechtoday/', 'academix')."'>".esc_html__('Academix Research Group', 'academix')."</a>",
                'args'        => array(
                    'teeny'   => true,
                    'textarea_rows' => 8,
                    'media_buttons' => true
                ),
            ),
            array(
                'id'          => 'academix_copyright_right_text',
                'type'        => 'editor',
                'title'       => esc_html__('Copyright Right Information', 'academix'),
                'subtitle'    => esc_html__( 'HTML tags allowed: a, br, strong, em', 'academix' ),
                'default'     => esc_html__( 'Developed and Managed by: ', 'academix') ."<a href='".esc_url('https://themeforest.net/user/webtechtoday/', 'academix')."'>".esc_html__('Webtechtoday', 'academix')."</a>",
                'args'        => array(
                    'teeny'   => true,
                    'textarea_rows' => 8,
                    'media_buttons' => true
                ),
            ),
            array(
                'id'          => 'academix_copyright_padding',
                'type'        => 'spacing',
                'title'       => esc_html__('Copyright Section Padding', 'academix'),
                'subtitle'   => esc_html__( 'You can provide the top/bottom padding. In pixels for copyright section', 'academix' ),
                'mode'    => 'padding',
                'output' => array('.section-footer .footer-bar'),
                'left'    => false,
                'right'    => false,
                'units'   => array('px'),
                'default'            => array(
                    'padding-top'     => '20px', 
                    'padding-bottom'  => '20px', 
                    'units'           => 'px', 
                )
            ),
           array(
                'id'          => 'academix_copyright_font_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('Copyright Font Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for footer copyright.', 'academix'),
                'desc'       => esc_html__('Open Sans is default font', 'academix'),
                'google'      => true, 
                'font-style' => false,
                'color'         => false,
                'text-align'    => false,
                'output'      => array('.section-footer .footer-bar .powredby, .copyright'),
                'default'     => array(
                  'font-family' => 'Open Sans',
                  'google'      => true,
                  'font-size'   => '12px',
                  'line-height' => '25px',
                  'font-weight' => '400'
                 ),
            ),
            array(
                'id'          => 'academix_copyright_bg_color',
                'type'        => 'color',
                'transparent' => false,
                'title'       => esc_html__( 'Copyright Background Color', 'academix' ),
                'subtitle'    => esc_html__( 'Pick a color for the copyright background color. ( default: #e6ebef )', 'academix' ),
                'default'     => '#e6ebef',
                'output'    => array('background-color' => '.section-footer .footer-bar',
                                'color' => '')
            ),
            array(
                'id'            => 'academix_copyright_f_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Copyright Font Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the copyright font color. ( default: #000 )', 'academix' ),
                'default'       => '#000',
                'output'    => array('background-color' => '',
                                'color' => '.powredby, .copyright'),
            ),
            array(
                'id'            => 'academix_copyright_link_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Copyright Link Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the copyright link color. ( default: #D30015 )', 'academix' ),
                'default'       => '',
                'output'    => array('background-color' => '',
                                'color' => '.footer-bar .copyright a, .powredby a'),
            ),
            array(
                'id'            => 'academix_copyright_link_hover_color',
                'type'          => 'color',
                'transparent'   => false,
                'title'         => esc_html__( 'Copyright Link Hover Color', 'academix' ),
                'subtitle'      => esc_html__( 'Pick a color for the copyright link hover color. ( default: #D30015 )', 'academix' ),
                'default'       => '',
                'output'    => array('background-color' => '',
                                'color' => '.footer-bar .copyright a:hover, .powredby a:hover'),
            ),
        )
    ));

    // Typography
    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Typography', 'academix'),
        'id'     => 'typography_settings',
        'icon'   => 'el el-fontsize',
    ));

    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Body Typography', 'academix'),
        'id'     => 'body_typography_settings',
        'desc'   => esc_html__('This section contains body typography settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_full_body_typography',
                'type'        => 'typography', 
                'title'       => esc_html__('Body Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all body text', 'academix'),
                'google'      => true, 
                'subsets'   => false,
                'output'      => array('body'),
                'default'     => array(
                    'color'       => '#000000', 
                    'font-weight'  => '400', 
                    'font-family' => 'Open Sans', 
                    'google'      => true,
                    'font-size'   => '14px', 
                    'line-height' => '25.032px'
                 ),
            ),
            array(
                'id'          => 'academix_link_color',
                'type'        => 'link_color', 
                'title'       => esc_html__('Link Color', 'academix'),
                'subtitle'       => esc_html__('Controls the color of all text links.', 'academix'),
                'output'      => array('a'),
                'default'     => array(
                    'regular'       => '#D30015', 
                    'hover'  => '#D30015', 
                    'active' => '#D30015', 
                    'visited' => '#D30015',
                 ),
            ),
        )
    ));

    Redux::setSection( $opt_name, array(
        'title'  => esc_html__('Headers Typography', 'academix'),
        'id'     => 'all_header_typo',
        'desc'   => esc_html__('This section contains headers typography settings options', 'academix'),
        'subsection' => true,
        'fields' => array(
            array(
                'id'          => 'academix_h_one_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('H1 Headers Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all H1 Headers.', 'academix'),
                'google'      => true, 
                'subsets'   => false,
                'output'      => array('h1'),
                'default'     => array(
                    'color'       => '#111', 
                    'font-weight'  => '700', 
                    'font-family' => 'Montserrat', 
                    'google'      => true,
                    'font-size'   => '',
                    'line-height' => ''
                 ),
            ),
            array(
                'id'          => 'academix_h_two_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('H2 Headers Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all H2 Headers.', 'academix'),
                'google'      => true, 
                'subsets'   => false,
                'output'      => array('h2'),
                'default'     => array(
                    'color'       => '', 
                    'font-weight'  => '700', 
                    'font-family' => 'Montserrat', 
                    'google'      => true,
                    'font-size'   => '',
                    'line-height' => ''
                 ),
            ),
             array(
                'id'          => 'academix_h_three_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('H3 Headers Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all H3, Headers.', 'academix'),
                'google'      => true, 
                'subsets'   => false,
                'output'      => array('h3'),
                'default'     => array(
                    'color'       => '#000', 
                    'font-weight'  => '700', 
                    'font-family' => 'Montserrat', 
                    'google'      => true,
                    'font-size'   => '24px',
                    'line-height' => '20px'
                 ),
            ),
             array(
                'id'          => 'academix_h_four_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('H4 Headers Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all H4, Headers.', 'academix'),
                'google'      => true, 
                'subsets'   => false,
                'output'      => array('h4'),
                'default'     => array(
                    'color'       => '#000', 
                    'font-weight'  => '700', 
                    'font-family' => 'Montserrat', 
                    'google'      => true,
                    'font-size'   => '18px',
                    'line-height' => '20px'
                 ),
            ),
            array(
                'id'          => 'academix_h_five_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('H5 Headers Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all H5, Headers.', 'academix'),
                'google'      => true, 
                'subsets'   => false,
                'output'      => array('h5'),
                'default'     => array(
                    'color'       => '#000', 
                    'font-weight'  => '700', 
                    'font-family' => 'Montserrat', 
                    'google'      => true,
                    'font-size'   => '14px',
                    'line-height' => '20px'
                 ),
            ),
             array(
                'id'          => 'academix_h_six_typo',
                'type'        => 'typography', 
                'title'       => esc_html__('H6 Headers Typography', 'academix'),
                'subtitle'       => esc_html__('These settings control the typography for all H6, Headers.', 'academix'),
                'google'      => true, 
                'subsets'   => false,
                'output'      => array('h6'),
                'default'     => array(
                    'color'       => '#000', 
                    'font-weight'  => '700', 
                    'font-family' => 'Montserrat', 
                    'google'      => true,
                    'font-size'   => '12px',
                    'line-height' => '20px'
                 ),
            ),

        )
    ));
    
    /*
     * <--- END SECTIONS
     */


    /*
     *
     * YOU MUST PREFIX THE FUNCTIONS BELOW AND ACTION FUNCTION CALLS OR ANY OTHER CONFIG MAY OVERRIDE YOUR CODE.
     *
     */

    /*
    *
    * --> Action hook examples
    *
    */

    // If Redux is running as a plugin, this will remove the demo notice and links
    //add_action( 'redux/loaded', 'remove_demo' );

    // Function to test the compiler hook and demo CSS output.
    // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
    //add_filter('redux/options/' . $opt_name . '/compiler', 'compiler_action', 10, 3);

    // Change the arguments after they've been declared, but before the panel is created
    //add_filter('redux/options/' . $opt_name . '/args', 'change_arguments' );

    // Change the default value of a field after it's been set, but before it's been useds
    //add_filter('redux/options/' . $opt_name . '/defaults', 'change_defaults' );

    // Dynamically add a section. Can be also used to modify sections/fields
    //add_filter('redux/options/' . $opt_name . '/sections', 'dynamic_section');


    /**
     * Custom function for the callback validation referenced above
     * */
    if ( ! function_exists( 'redux_validate_callback_function' ) ) {
        function redux_validate_callback_function( $field, $value, $existing_value ) {
            $error   = false;
            $warning = false;

            //do your validation
            if ( $value == 1 ) {
                $error = true;
                $value = $existing_value;
            } elseif ( $value == 2 ) {
                $warning = true;
                $value   = $existing_value;
            }

            $return['value'] = $value;

            if ( $error == true ) {
                $field['msg']    = 'your custom error message';
                $return['error'] = $field;
            }

            if ( $warning == true ) {
                $field['msg']      = 'your custom warning message';
                $return['warning'] = $field;
            }

            return $return;
        }
    }

    /**
     * Custom function for the callback referenced above
     */
    if ( ! function_exists( 'redux_my_custom_field' ) ) {
        function redux_my_custom_field( $field, $value ) {
            print_r( $field );
            echo '<br/>';
            print_r( $value );
        }
    }

    /**
     * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
     * Simply include this function in the child themes functions.php file.
     * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
     * so you must use get_template_directory_uri() if you want to use any of the built in icons
     * */
    if ( ! function_exists( 'dynamic_section' ) ) {
        function dynamic_section( $sections ) {
            //$sections = array();
            $sections[] = array(
                'title'  => esc_html__( 'Section via hook', 'academix' ),
                'desc'   => esc_html__( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'academix' ),
                'icon'   => 'el el-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }
    }

    /**
     * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
     * */
    if ( ! function_exists( 'change_arguments' ) ) {
        function change_arguments( $args ) {
            //$args['dev_mode'] = true;

            return $args;
        }
    }

    /**
     * Filter hook for filtering the default value of any given field. Very useful in development mode.
     * */
    if ( ! function_exists( 'change_defaults' ) ) {
        function change_defaults( $defaults ) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }
    }

    /**
     * Removes the demo link and the notice of integrated demo from the redux-framework plugin
     */
    if ( ! function_exists( 'remove_demo' ) ) {
        function remove_demo() {
            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
            }
        }
    }

