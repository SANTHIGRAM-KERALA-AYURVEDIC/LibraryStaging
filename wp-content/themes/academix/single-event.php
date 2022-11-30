<?php
/**
 * The template for displaying all single post
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package academix
 */
get_header();
?>

<?php 
    global $academix_options;

    academix_page_banner();
    academix_page_breadcrumb();

    while( have_posts() ): the_post();  

    $idd = get_the_ID();
    $prefix = '_academix_';

    $event_image = '';
    if( wp_get_attachment_image( get_post_meta( $idd, $prefix . 'event_image_id', true ), 'full', false, array( 'class' => 'sabbi-events-img img-responsive' ) ) ){
        $event_image = wp_get_attachment_image( get_post_meta( $idd, $prefix . 'event_image_id', true ), 'full', false, array( 'class' => 'sabbi-events-img img-responsive' ) );
    }

    $event_location = '';
    if( get_post_meta( $idd , $prefix . 'event_location', true) ){
        $event_location = get_post_meta( $idd , $prefix . 'event_location', true);
    }

    $event_date = '';
    if( get_post_meta( $idd , $prefix . 'event_date', true) ){
        $event_date = get_post_meta( $idd , $prefix . 'event_date', true);
    }

    $event_image_gallery = get_post_meta( $idd , $prefix . 'event_image_gallery', true);
?>
<section class="sabbi-section stage_events_post stage_events_post-single">
    <div class="container">
        <div class="events_post_single-sagment">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <article class="sabbi-events-item">
                        <a href="<?php esc_url( the_permalink() ); ?>" class="sabbi-events-link">
                            <figure>
                                <?php echo wp_kses_post( $event_image ); ?>
                                <figcaption>
                                    <h2 class="sabbi-events-title font-md__x"><?php the_title(); ?></h2>
                                </figcaption>
                            </figure>
                        </a>
                        <div class="events-item-meta">
                            <?php if( !empty( $event_location ) ) { ?>
                            <div class="events-loc"><i class="ion-location"></i><span class="text"><?php echo wp_kses_post($event_location); ?></span></div>
                            <?php } ?>

                            <?php if( !empty( $event_date ) ) { ?>
                            <div class="events-date"><i class="ion-calendar"></i><span class="text"><?php echo wp_kses_post($event_date); ?></span></div>
                            <?php } ?>
                           <?php the_content(); ?>
                        </div>
                        <?php 
                        if( isset($academix_options['academix_display_event_gallery']) && $academix_options['academix_display_event_gallery'] == '1' ){
                        if( !empty( $event_image_gallery ) ){
                        ?>
                        <div class="events-item-media">
                            <h4 class="sagment-title"><?php if( isset($academix_options['academix_event_gallery_title_text']) && !empty( $academix_options['academix_event_gallery_title_text'] ) ){ echo esc_html($academix_options['academix_event_gallery_title_text']); }?></h4>
                            <div class="row events-item-media-scoup">
                                <?php
                                if ( isset( $event_image_gallery ) && is_array( $event_image_gallery ) ){
                                    foreach ( $event_image_gallery as $image_id => $image ) { 

                                    $img = wp_get_attachment_image( $image_id, 'academix-event-gallery-thumb', '', array( 'class' => 'img-responsive events-item-media-img' ) );
                                    ?>
                                    <div class="col-xs-6 col-sm-6 col-md-4">
                                        <figure>
                                            <a href="<?php echo esc_url($image); ?>" data-toggle="lightbox" data-gallery="sabb-gallery"><?php echo wp_kses_post( $img ); ?></a>
                                        </figure>
                                    </div>
                                <?php } } ?>
                               
                            </div>
                        </div>
                        <?php } ?>
                        <?php } ?>
                    </article><!-- /.sabbi-events-item -->
                </div>
            </div>
        </div><!-- /.events_post_single-sagment -->
    </div>
</section>

<?php 
endwhile;
get_footer();