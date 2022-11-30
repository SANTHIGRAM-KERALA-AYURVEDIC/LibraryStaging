<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package academix
 */

if ( ! is_active_sidebar( 'sidebar-page' ) ) {
	return;
}
?>

<aside id="secondary" class="col-md-4 widget-area">
    <div class="blog-sidebar">
		<?php dynamic_sidebar( 'sidebar-page' ); ?>
	</div>
</aside><!-- #secondary -->
