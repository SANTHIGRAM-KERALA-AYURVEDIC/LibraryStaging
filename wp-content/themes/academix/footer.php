<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package academix
 */

global $academix_options;

// define theme slug name
define( 'ACADEMIX_THEME_SLUG', 'academix' );

$footer_padding_cls = ( ( isset( $academix_options['academix_display_footer_widgets'] ) && $academix_options['academix_display_footer_widgets'] == '0' ) ? 'no-padding' : '');
if( isset($academix_options['academix_footer_widgets_banner_bg_type']) && is_array( $academix_options['academix_footer_widgets_banner_bg_type'] )){
    foreach ( $academix_options['academix_footer_widgets_banner_bg_type'] as $academix_footer_widgets_banner_bg_type ) {
        
        if( $academix_footer_widgets_banner_bg_type == 1 || $academix_footer_widgets_banner_bg_type == 1 && $academix_options['academix_footer_widgets_banner_bg_type'] == 2 ){
            $academix_footer_widgets_bg_image = $academix_options['academix_footer_widgets_bg_image'];
        } 
    }
}

?>
	<footer id="colophon" class="site-footer section-footer <?php echo esc_attr( $footer_padding_cls ); ?>" <?php if( !empty( $academix_footer_widgets_bg_image ) ) { ?>style="background-image: url( <?php echo esc_url( $academix_footer_widgets_bg_image['url'] ); ?>);" <?php } ?>>
        <?php 
            if( academix_option('academix_display_footer_widgets') == '1' ) : 
            $academix_footer_column_layout = $academix_options['academix_footer_column_layout'];
            if( 0 != $academix_footer_column_layout ):
        ?>
        <div class="container<?php if( isset( $academix_options['display_academix_footer_width'] ) && $academix_options['display_academix_footer_width'] == '1' ): echo esc_attr('-fluid'); endif; ?>">
            <div class="row">
                <?php 
                    if ( $academix_footer_column_layout == '' ) {
                        $academix_footer_column_layout = 3;
                    } else {
                        $academix_footer_column_class = floor( 12/$academix_footer_column_layout );
                    }

                    for( $col_class = 1; $col_class <= $academix_footer_column_layout ; $col_class++ ) {
                ?>
                <div class="col-sm-<?php echo esc_attr( $academix_footer_column_class ); ?>">
                    <div class="footer-site-info footer-widget">
                        <?php dynamic_sidebar( ACADEMIX_THEME_SLUG . '-footer-' . $col_class ); ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div> 
        <?php endif; endif; ?>

        <?php 
            global $academix_options; 
            if( academix_option('academix_display_left_footer_copyright') == '1' || academix_option('academix_display_right_footer_copyright') == '1' ){
        ?>
        <div id="site-footer-bar" class="footer-bar">
            <div class="container">
                <div class="row">
                    <?php 
                        if( isset( $academix_options['academix_display_left_footer_copyright'] ) && academix_option('academix_display_left_footer_copyright') == '1' ){
                    ?>
                    <div class="col-sm-8">
                        <div class="widget_black_studio_tinymce" id="black-studio-tinymce-4">
                            <div class="copyright"><?php if( function_exists('academix_copyright')){ academix_copyright(); }?></div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php 
                        if( isset( $academix_options['academix_display_right_footer_copyright'] ) && $academix_options['academix_display_right_footer_copyright'] == '1' ){
                    ?>
                    <div class="col-sm-4 ">
                        <div class="powredby"><?php if( function_exists('academix_poweredby')){ academix_poweredby(); }?></div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
	</footer><!-- #colophon -->
</div><!-- #page -->
</div> <!-- end site main -->

<?php wp_footer(); ?>

</body>
</html>
