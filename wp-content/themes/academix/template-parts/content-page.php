<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package academix
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'academix' ),
                'after'  => '</div>',
                'link_before' => '<span>',
                'link_after'  => '</span>',
            ) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
