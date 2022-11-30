<?php 

// main menu
if( ! function_exists( 'academix_main_menu' ) ){
	function academix_main_menu(){
		wp_nav_menu( array( 
			'theme_location' => 'academix-primary', 
			'depth' => 4,
			'container' => false,
			'menu_class' => 'nav navbar-nav navbar-right',
			'menu_id'   => 'menu-main-nav',
			'fallback_cb' => 'academix_menu_setting', 
			'walker' => new WP_Academix_Bootstrap_Navwalker()
	    ) ); 
	}
}

// mobile menu
if( !function_exists( 'academix_mobile_menu' ) ){

	function academix_mobile_menu(){
		wp_nav_menu(array( 
			'theme_location' => 'academix-primary', 
			'depth' => 4,
			'container' => false,
			'menu_class' => 'nav navbar-nav navbar-right m-menu',
			'menu_id'   => 'menu-main-nav',
			'fallback_cb' => 'academix_menu_setting',
	    )); 
	}
}

// menu settings
if( ! function_exists( 'academix_menu_setting' ) ){
	function academix_menu_setting(){
		?>
		<div>
		    <ul class="nav navbar-nav navbar-right" id="menu-main-nav">
		      <?php if( is_user_logged_in() ): ?>
		        <li class="active">
		            <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">
		                <?php echo esc_html__('Create A Menu', 'academix');?>
		            </a>
		        </li>
		      <?php else: ?>
		        <li class="active">
		            <a href="<?php echo esc_url( home_url('/') );?>">
		                <?php echo esc_html__('Home', 'academix');?>
		            </a>
		        </li>
		        <?php endif; ?>
		    </ul>
	    </div>
       <?php 
	}
}

// academix logo
if( !function_exists( 'academix_logo' ) ){

	function academix_logo(){

	  global $academix_options;

	  if( is_ssl() && isset( $academix_options['academix_website_image_logo']['url'] )){
	    $academix_options['academix_website_image_logo']['url'] = str_replace( 'http:', 'https:', $academix_options['academix_website_image_logo']['url'] );
	  }

	  if( isset( $academix_options['academix_website_image_logo']['url'] ) && academix_option('academix_website_logo_type') == '1' ) : ?>
	    <a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" data-scroll><img src="<?php echo esc_url($academix_options['academix_website_image_logo']['url']); ?>" alt="" class="img-responsive"></a>
	  <?php elseif( isset( $academix_options['academix_website_text_logo'] ) && academix_option('academix_website_logo_type') == '0' ): ?>
	    <a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><h2><?php echo esc_html($academix_options['academix_website_text_logo']);?></h2></a>
	  <?php else: ?>
	    <h1 class="academix-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
	  <?php endif;
	}
}

// academix copyright
if( !function_exists('academix_copyright') ){
	function academix_copyright(){
		global $academix_options;

		if( isset( $academix_options['academix_copyright_left_text'] ) && $academix_options['academix_copyright_left_text'] != '' ){
		    echo wp_kses( $academix_options['academix_copyright_left_text'], array(
		        'a' => array(
		          'href' => array(),
		          'title' => array()
		        ),
		        'img' => array(
		        	'src' => array(),
		        	'alt' => array(),
		        ),
		        'br' => array(),
		        'em' => array(),
		        'strong' => array()
		    ));
		} else{
		    echo esc_html__( 'Copyright', 'academix') . "&copy; ".esc_html__( '2017 - ', 'academix' )."<a href='".esc_url('https://themeforest.net/user/webtechtoday/', 'academix')."'>".esc_html__('Academix Research Group', 'academix')."</a>";
		}
	}
}


// academix poweredby
if( !function_exists('academix_poweredby') ){
	function academix_poweredby(){
		global $academix_options;

		if( isset( $academix_options['academix_copyright_right_text'] ) && $academix_options['academix_copyright_right_text'] != '' ){
		    echo wp_kses( $academix_options['academix_copyright_right_text'], array(
		        'a' => array(
		          'href' => array(),
		          'title' => array()
		        ),
		        'img' => array(
		        	'src' => array(),
		        	'alt' => array(),
		        ),
		        'br' => array(),
		        'em' => array(),
		        'strong' => array()
		    ));
		} else{
		    echo esc_html__( 'Developed and Managed by:', 'academix') . "<a href='".esc_url('https://themeforest.net/user/webtechtoday/', 'academix')."'>".esc_html__('Webtechtoday', 'academix')."</a>";
		}
	}
}


// academix page banner
if ( !function_exists( 'academix_page_banner' ) ) {
	function academix_page_banner(){
	    
	global $academix_options;
	global $post;
	$prefix = '_academix_';
	
	$display_metabox_page_banner = get_post_meta( $post->ID,  $prefix . 'display_page_banner', true );
	
	if( has_post_thumbnail() ) {
        $bg_image = get_the_post_thumbnail_url(null, 'full');
	}
	
	$is_team = get_post_type( get_the_ID() ) == 'team' && is_single();

		if( $is_team ) {
			?>
            <header class="sabbi-page-header page-header-lg>" <?php if ( ! empty( $bg_image ) ) { ?> style="background-image: url(<?php echo esc_url( $bg_image ); ?>);" <?php } ?>>
                <div class="page-header-content conternt-center"></div>
            </header>
			<?php
		}
		
    if( ( $display_metabox_page_banner !== '0' && academix_option('academix_display_page_banner') !== '0' )  || ( ( !is_front_page() && academix_option('academix_display_page_banner') === '1') && ( $display_metabox_page_banner === '2' || $display_metabox_page_banner === '1') ) ) { 
        if( $is_team ){
            return;
        }
    ?>
	<header class="sabbi-page-header page-header-lg <?php if(isset($academix_options['academix_display_page_banner_on_mobile']) && $academix_options['academix_display_page_banner_on_mobile'] == '0') { echo esc_attr('hidden-xs'); } ?>" <?php if( !empty( $bg_image ) ) { ?> style="background-image: url(<?php echo esc_url($bg_image); ?>);" <?php } ?>>
        <div class="page-header-content conternt-center">
            <div class="header-title-block">
                <?php echo '<h1 class="page-title">'.get_the_title().'</h1>'; ?>
            </div>
        </div>
    </header>
	<?php 
	}
   }
}


// academix page breadcrumb
if ( !function_exists( 'academix_page_breadcrumb' ) ) {
	function academix_page_breadcrumb(){ 

	global $post;
	global $academix_options;
		
	$prefix = '_academix_';
	$display_page_breadcrumbs = get_post_meta( $post->ID,  $prefix . 'display_page_breadcrumbs', true );
    $display_metabox_page_banner = get_post_meta( $post->ID,  $prefix . 'display_page_banner', true );

		if( ( $display_page_breadcrumbs !== '0' && academix_option('academix_display_page_breadcrumbs') !== '0' )  || ( ( !is_front_page() && academix_option('academix_display_page_breadcrumbs') === '1') && ( $display_page_breadcrumbs === '2' || $display_page_breadcrumbs === '1') ) ) {

        $header_mt_110 =  ( $display_metabox_page_banner === '0' || academix_option('academix_display_page_banner') === '0' )  ? 'header-mt-110' : '';
	?>
		<div class="auth-breadcrumb-wrap <?php echo esc_attr($header_mt_110); ?> <?php if( isset($academix_options['academix_display_page_breadcrumbs_on_mobile']) && $academix_options['academix_display_page_breadcrumbs_on_mobile'] == '0') { echo esc_attr('hidden-xs'); } ?>">
	        <div class="container">
	        	<?php echo academix_breadcrumbs(); ?>
	        </div>
        </div>
	<?php 
	    }
    }
}

// Blog header title
if ( !function_exists( 'academix_blog_header_title' ) ) {
	function academix_blog_header_title(){
	  global $academix_options;
	    if (is_home() && ! is_front_page() ) {
	      if( !empty( $academix_options['academix_blog_page_title'] ) ){ 
	        echo esc_html( $academix_options['academix_blog_page_title'] );
	      } else{
	        echo esc_html_e( 'Blog', 'academix' );
	      }
	    } 
	    elseif( is_single()) {
	      echo get_the_title();
	    }
	    elseif( is_archive()) {
	        if ( is_day() ) :
	          printf( esc_html__( 'Daily Archives: %s', 'academix' ), '<span>' . esc_html(get_the_date()) . '</span>' );
	        elseif ( is_month() ) :
	          printf( esc_html__( 'Monthly Archives: %s', 'academix' ), '<span>' . esc_html(get_the_date( _x( 'F Y', 'monthly archives date format', 'academix' ) )) . '</span>' );
	        elseif ( is_year() ) :
	          printf( esc_html__( 'Yearly Archives: %s', 'academix' ), '<span>' . esc_html(get_the_date( _x( 'Y', 'yearly archives date format', 'academix' ) )) . '</span>' );
	        else :
	          esc_html_e( 'Archives', 'academix' );
	        endif;
	    } elseif( is_search() ){
	       printf( esc_html__( 'Search Results for: &ldquo;%s&rdquo;', 'academix' ), '<span>' . esc_html(get_search_query()) . '</span>' );
	    } else{
	      echo esc_html_e( 'Blog', 'academix' );
	    }
	}
}


// academix blog banner
if ( !function_exists( 'academix_blog_banner' ) ) {
	function academix_blog_banner(){ 

	global $wp_query;
    $page_id = $wp_query->get_queried_object_id();

	$prefix = '_academix_';
	if( !is_home() ){
	$display_metabox_blog_banner = get_post_meta( $page_id,  $prefix . 'display_page_banner', true );
    } else{
        $blog_page_id = get_option( 'page_for_posts' );
        $display_metabox_blog_banner = get_post_meta( $blog_page_id,  $prefix . 'display_page_banner', true );
    }

	global $academix_options;

	if( is_home() ) {
	    $bg_image = get_the_post_thumbnail_url($page_id, 'full');
	    
	} else {
		$bg_image = get_the_post_thumbnail_url('full');
	}

		if( ( $display_metabox_blog_banner !== '0' && academix_option('academix_display_blog_banner') !== '0' )  || ( ( academix_option('academix_display_blog_banner') === '1') && ( $display_metabox_blog_banner === '2' || $display_metabox_blog_banner === '1') ) ) {
    ?>
	<header class="blog-banner sabbi-page-header page-header-lg <?php if( isset($academix_options['academix_display_blog_banner_on_mobile']) && $academix_options['academix_display_blog_banner_on_mobile'] == '0') { echo esc_attr('hidden-xs'); } ?>" <?php if( !empty( $bg_image ) ) { ?> style="background-image: url(<?php echo esc_url($bg_image); ?>);" <?php } ?>>
        <div class="page-header-content conternt-center">
            <div class="header-title-block">
                <h1 class="page-title"><?php if( function_exists('academix_blog_header_title') ){ academix_blog_header_title(); } ?></h1>
            </div>
        </div>
    </header>

	<?php } elseif ($display_metabox_blog_banner == '0' ) {
		echo '';
	}
   }
}


// academix blog breadcrumb
if ( !function_exists( 'academix_blog_breadcrumb' ) ) {
	function academix_blog_breadcrumb(){
	    
	global $academix_options;
	global $wp_query;
    $page_id = $wp_query->get_queried_object_id();
	$prefix = '_academix_';
	
    if( !is_home() ){
        $display_blog_breadcrumbs = get_post_meta( $page_id,  $prefix . 'display_page_breadcrumbs', true );
        $display_metabox_blog_banner = get_post_meta( $page_id,  $prefix . 'display_page_banner', true );
    } else{
        $id = get_option( 'page_for_posts' );
        $display_blog_breadcrumbs = get_post_meta( $id,  $prefix . 'display_page_breadcrumbs', true );
        $display_metabox_blog_banner = get_post_meta( $id,  $prefix . 'display_page_banner', true );
    }
    
		if( ( $display_blog_breadcrumbs !== '0' && academix_option('academix_display_blog_breadcrumbs') !== '0' )  || ( ( academix_option('academix_display_blog_breadcrumbs') === '1') && ( $display_blog_breadcrumbs === '2' || $display_blog_breadcrumbs === '1') ) ) {

        $header_mt_110  = ( $display_metabox_blog_banner === '0' || academix_option('academix_display_blog_banner') === '0' ) ? 'header-mt-110' : '';
	?>
		<div class="blog-breadcrumb auth-breadcrumb-wrap <?php echo esc_attr($header_mt_110); ?> <?php if( isset($academix_options['academix_display_blog_breadcrumbs_on_mobile']) && $academix_options['academix_display_blog_breadcrumbs_on_mobile'] == '0') { echo esc_attr('hidden-xs'); } ?>">
	        <div class="container">
	            <?php echo academix_breadcrumbs(); ?>
	        </div>
        </div>
	<?php } elseif ( $display_blog_breadcrumbs == '0' ) {
		echo '';
	}
    }
}

// Blog read more
function academix_blog_read_more(){
  global $academix_options;
  if( !empty( $academix_options['academix_blog_read_more'] ) ){ 
    echo esc_html( $academix_options['academix_blog_read_more'] );
  } else{
    echo esc_html_e( 'Read More', 'academix' );
  }
}


// Pagination
function academix_pagination( $numpages = ''){
    if ($numpages == '') {
	    global $wp_query;
	    $numpages = $wp_query->max_num_pages;
	    if(!$numpages) {
	        $numpages = 1;
	    }
	}
        
  $big = 999999999; // need an unlikely integer
  echo paginate_links( array(
      'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
      'format'       => '',
      'add_args'     => '',
      'current'      => max( 1, get_query_var( 'paged' ) ),
      'total'        => $numpages,
      'prev_text'    => '<i class="ion-arrow-left-c"></i>',
      'next_text'    => '<i class="ion-arrow-right-c"></i>',
      'type'         => 'list',
      'end_size'     => 2,
      'mid_size'     => 2
    ) );
  
}

// academix options
if( !function_exists('academix_option') ) {

	function academix_option($key, $default = false)
	{
		global $academix_options;
		if(isset($academix_options[$key])) {
			return $academix_options[$key];
		}

		return $default;
	}
}