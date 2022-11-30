<?php
/**
 *  Template Name: Page Left Sidebar
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package academix
 */

get_header(); 
academix_page_banner();
academix_page_breadcrumb();
?>
	
	<div class="academix-content-area site-padding">
		<div class="container">
			<div class="row">
				<?php get_sidebar(); ?>
			    <div class="col-md-8">
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
			</div>
        </div>
	</div>

<?php
get_footer();
