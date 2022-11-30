<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package academix
 */

get_header(); 
$postID = get_queried_object_id();
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

$academix_single_blog_sidebar = 'right';
    
if( isset( $academix_options['academix_single_blog_sidebar'] ) && $academix_options['academix_single_blog_sidebar'] != '' ){
	$academix_single_blog_sidebar = $academix_options['academix_single_blog_sidebar'];
}


if( academix_option('academix_single_blog_sidebar') == 'no-sidebar' ){
	$col_class = '12';
} else{
	$col_class = '8';
}

academix_blog_banner();
academix_blog_breadcrumb();
?>

	<div class="academix-content-area blog site-padding <?php echo ( ( ( $display_metabox_blog_banner === '0' || academix_option('academix_display_blog_banner') === '0' ) && ( $display_blog_breadcrumbs === '0' || academix_option('academix_display_blog_breadcrumbs') === '0' ) ) || ( academix_option('academix_display_blog_banner') === '0' && academix_option('academix_display_blog_breadcrumbs') === '0' ) ) ? 'header-mt-110' : ''; ?>">
		<div class="container">
			<div class="row">
				<?php if( $academix_single_blog_sidebar == 'left' ) { get_sidebar(); } ?>
			    <div class="col-md-<?php echo esc_attr($col_class); ?>">
                    <div class="row">
						<?php
						while ( have_posts() ) : the_post();

							get_template_part( 'template-parts/content', 'single');
	
						endwhile; // End of the loop.
						?>
					    </div>
				    </div>
				<?php if( $academix_single_blog_sidebar == 'right' ) { get_sidebar(); } ?>
			</div>
        </div>
	</div>

<?php
get_footer();
