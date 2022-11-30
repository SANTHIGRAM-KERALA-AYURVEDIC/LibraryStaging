<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package academix
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
	 if( has_post_thumbnail() ){ ?>
	<figure>
	    <?php the_post_thumbnail( 'full' , array( 'class' => 'img-responsive') ) ?>
	</figure>
	<?php } ?>
	<div class="blog-content">
	<header class="entry-header">
			<?php 
			the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
	        ?>
			<ul class="post-time">
				<li><i class="ion-calendar"></i><span><?php the_time(get_option('date_format')); ?></span></li>
				<li><i class="ion-chatbox"></i><?php comments_popup_link( esc_html__('No Comment','academix'), esc_html__('1 Comment','academix'), esc_html__('% Comments','academix'), ' ', esc_html__('Comments off','academix')); ?></li>	
			</ul>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_excerpt(); ?>
		<a href="<?php echo esc_url( get_permalink()); ?>" class="btn btn-unsolemn btn-action read-more"><?php academix_blog_read_more(); ?><i
		class="ion-arrow-right-c"></i></a>
		
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'academix' ),
				'after'  => '</div>',
				'link_before' => '<span>',
	            'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->
	</div>
</article>
