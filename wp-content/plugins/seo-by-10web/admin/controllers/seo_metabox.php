<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Meta boxes controller class.
 */
class WDSeometaboxController {
  /**
   * Display metaboxes.
   *
   * @param $object
   * @param $args
   */
  public static function display( $object, $args ) {
    if ( is_array( $args ) && isset( $args['args'] ) && 'post' == $args['args']['type'] ) {
      $id = isset($object->ID) ? $object->ID : get_the_ID();
      $type = 'post';
    }
    elseif ( isset($object->term_id) ) {
      $id = $object->term_id;
      $type = $object->taxonomy;
    }
    else {
      return;
    }
    $options = new WD_SEO_Postmeta( $id, $type );
    $options_defaults = new WD_SEO_Postmeta( $id, $type, 'parent' );
    $options->url = ('post' == $type) ? get_permalink() : get_term_link($object);
    $wdseo_options = WDSeo()->options;
    $options->sitemap_video = isset($wdseo_options->sitemap_video) ? $wdseo_options->sitemap_video : 0;
    $nofollow_global_options = (new WD_SEO_Options())->wdseo_nofollow_external_urls_global;
    if ( is_array($nofollow_global_options) ) {
      $options->nofollow_global = $nofollow_global_options['wdseo_nofollow_external_urls_global_enable'];
    }
    elseif ( is_object($nofollow_global_options) ) {
      $options->nofollow_global = $nofollow_global_options->wdseo_nofollow_external_urls_global_enable;
    }
    else {
      $options->nofollow_global = '';
    }
    WDSeometaboxView::display( $options, $options_defaults );
  }

  /**
   * Save metaboxes values.
   *
   * @param int $object_id
   * @param string $type
   * @return bool
   */
  public static function save( $object_id = 0, $type = 'post' ) {
    if ( !$object_id ) {
      $object_id = get_the_ID();
    }
    if ( !$object_id ) return false;
    $options = new WD_SEO_Postmeta();
    // @TODO when not in use Elementor editor.
    if ( !in_array(WD_SEO_Library::get('action'), array('elementor', 'elementor_ajax') ) ) {
      $options->store( $object_id, $type );
    }
  }
}
