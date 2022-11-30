<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package academix
 */

get_header();
global $academix_options;


$academix_search_sidebar = 'right';
    
if( isset( $academix_options['academix_search_sidebar'] ) && $academix_options['academix_search_sidebar'] != '' ){
	$academix_search_sidebar = $academix_options['academix_search_sidebar'];
}


if( academix_option('academix_search_sidebar') == 'no-sidebar' ){
	$col_class = '12';
} else{
	$col_class = '8';
}

academix_blog_banner();
academix_blog_breadcrumb();
?>

	<div class="academix-content-area blog site-padding">
		<div class="container">
			<div class="row">
				<?php if( $academix_search_sidebar == 'left' ) { get_sidebar(); } ?>
			    <div class="col-md-<?php echo esc_attr($col_class); ?>">
					<?php
						if ( have_posts() ) : 
							/* Start the Loop */
							while ( have_posts() ) : the_post();

								/**
								 * Run the loop for the search to output the results.
								 * If you want to overload this in a child theme then include a file
								 * called content-search.php and that will be used instead.
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
				<?php if( $academix_search_sidebar == 'right' ) { get_sidebar(); } ?>
				
			</div>
        </div>
	</div>

<?php
get_footer();
