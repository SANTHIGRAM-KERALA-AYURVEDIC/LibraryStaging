<?php 
function academix_custom_styles(){
	global $wp_query;
    $page_id = $wp_query->get_queried_object_id();
    
	$prefix = '_academix_';
	$banner_bg_color = get_post_meta( get_the_ID(), $prefix . 'banner_bg_color', true );
	$blog_banner_bg_color = get_post_meta( $page_id, $prefix . 'banner_bg_color', true );
	?>
	<style>
	.sabbi-page-header{
		background-color: <?php echo esc_attr($banner_bg_color); ?>;
	}
	.blog-banner.sabbi-page-header{
		background-color: <?php echo esc_attr($blog_banner_bg_color); ?>;
	}
    </style>
	<?php
}
add_action( 'wp_head', 'academix_custom_styles', 100 );
?>