<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package academix
 */

get_header(); 

global $academix_options;
global $wp_query;
global $post;
$page_id = $wp_query->get_queried_object_id();
$prefix = '_academix_';

$el_check = get_post_meta( $post->ID , '_elementor_data', true );

if( !is_home() ){
	$display_blog_breadcrumbs = get_post_meta( $page_id,  $prefix . 'display_page_breadcrumbs', true );
	$display_metabox_blog_banner = get_post_meta( $page_id,  $prefix . 'display_page_banner', true );
} else{
	$id = get_option( 'page_for_posts' );
	$display_blog_breadcrumbs = get_post_meta( $id,  $prefix . 'display_page_breadcrumbs', true );
	$display_metabox_blog_banner = get_post_meta( $id,  $prefix . 'display_page_banner', true );
}

if( $el_check == true ){
    $el_class = '';
} else{
    $el_class = 'site-padding';
}

$academix_blog_sidebar = 'right';
    
if( isset( $academix_options['academix_blog_sidebar'] ) && $academix_options['academix_blog_sidebar'] != '' ){
	$academix_blog_sidebar = $academix_options['academix_blog_sidebar'];
}

if( academix_option('academix_blog_sidebar') == 'no-sidebar' ){
	$col_class = '12';
} else{
	$col_class = '8';
}

academix_blog_banner();
academix_blog_breadcrumb();
?>
    
	<div class="academix-content-area blog <?php echo ( ( ( $display_metabox_blog_banner === '0' || academix_option('academix_display_blog_banner') === '0' ) && ( $display_blog_breadcrumbs === '0' || academix_option('academix_display_blog_breadcrumbs') === '0' ) ) || ( academix_option('academix_display_blog_banner') === '0' && academix_option('academix_display_blog_breadcrumbs') === '0' ) ) ? 'header-mt-110' : ''; ?>  <?php echo esc_attr($el_class); ?>">
		<div class="container">
			<div class="row">
			    <?php if( $academix_blog_sidebar == 'left' ) { get_sidebar(); } ?>
			    <div class="col-md-<?php echo esc_attr($col_class); ?>">
					<?php
					if ( have_posts() ) :
						/* Start the Loop */
						while ( have_posts() ) : the_post();

							/*
							 * Include the Post-Format-specific template for the content.
							 * If you want to override this in a child theme, then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
							get_template_part( 'template-parts/content', get_post_format() );

						endwhile;
                    ?>
					<nav aria-label="Page navigation" class="pagination_wraper">
						<?php academix_pagination(); ?>
		            </nav>
				    <?php
					else :

						get_template_part( 'template-parts/content', 'none' );

					endif; ?>

				</div>
				<?php if( $academix_blog_sidebar == 'right' ) { get_sidebar(); } ?>
				
			</div>
        </div>
	</div>

<?php
get_footer();
