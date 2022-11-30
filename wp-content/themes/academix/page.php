<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package academix
 */

get_header(); 

global $academix_options;
global $post;
$prefix = '_academix_';

$el_check = get_post_meta( $post->ID , '_elementor_data', true );
$display_metabox_page_banner = get_post_meta( $post->ID,  $prefix . 'display_page_banner', true );
$display_page_breadcrumbs = get_post_meta( $post->ID,  $prefix . 'display_page_breadcrumbs', true );

if( $el_check == true ){
    $el_class = '';
} else{
    $el_class = 'site-padding';
}

academix_page_banner();
academix_page_breadcrumb();
?>
	
	<div class="academix-content-area <?php echo ( ( ( $display_metabox_page_banner === '0' || academix_option('academix_display_page_banner') === '0') && !is_front_page() ) && ( ($display_page_breadcrumbs === '0' ||  academix_option('academix_display_page_breadcrumbs') === '0') && !is_front_page() ) ) ? 'header-page-mt-110' : ''; ?> <?php echo esc_attr($el_class); ?>">
        <?php
        while ( have_posts() ) : the_post();

            get_template_part( 'template-parts/content', 'page' );

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        endwhile; // End of the loop.
        ?>
	</div>

<?php
get_footer();
