<div class="col-md-12">
    <!--blog single section start here-->
    <article  id="post-<?php the_ID(); ?>" <?php post_class('blog-single'); ?>>
        <?php if( has_post_thumbnail() ){ ?>
        <figure>
            <?php the_post_thumbnail( 'full' , array( 'class' => 'img-responsive') ) ?>
        </figure>
        <?php } ?>
        <div class="blog-content">
        <?php 
        the_title( '<h1 class="entry-title">', '</h1>' );
        ?>
        <ul class="post-time">
            <li><i class="ion-person"></i><span><?php esc_html_e('Post by: ','academix')?></span><?php the_author_posts_link(); ?></li>
            <li><i class="ion-calendar"></i><span><?php the_time(get_option('date_format')); ?></span></li>
            <li><i class="ion-chatbox"></i><?php comments_popup_link( esc_html__('No Comment','academix'), esc_html__('1 Comment','academix'), esc_html__('% Comments','academix'), ' ', esc_html__('Comments off','academix')); ?></li>     
        </ul>
        <?php the_content(); ?>

          <div class="blo-cat"><span><strong><?php esc_html_e('Categories: ','academix'); ?></strong><?php the_category(', '); ?></span></div>
           <?php if( has_tag() ){ ?>
           <div class="blo-tag"><span><strong><?php esc_html_e('Tagged: ','academix'); ?></strong><?php the_tags('', ', '); ?></span></div>
           <?php } ?>

           <?php 
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'academix' ),
                    'after'  => '</div>',
                    'link_before' => '<span>',
                    'link_after'  => '</span>',
                ) );
            ?>

        </div>
    </article> <!-- /.blog-single -->
    <!--blog single section end here-->


     <?php 
        
        $prev_post = get_previous_post();
        $next_post = get_next_post();

        if( $prev_post || $next_post ){

     ?>
        <nav class="navigation post-navigation">
            

            <div class="nav-links">
                
                <?php 
                
                previous_post_link('<div class="nav-previous">%link</div>', __( 'Previous post', 'academix') );
                next_post_link('<div class="nav-next">%link</div>', __( 'Next post', 'academix') );

                ?>

            </div>
        </nav>
        
    <?php } ?>
    
 
                            
    <?php if ( comments_open() || '0' != get_comments_number() ) : ?>

    <!--comment section start here-->
    
    <?php comments_template(); ?>
    
    <!--comment section end here-->
    
    <?php endif; ?>

</div>