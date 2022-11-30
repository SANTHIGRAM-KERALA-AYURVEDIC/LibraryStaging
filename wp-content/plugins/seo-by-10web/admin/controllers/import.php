<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Admin controller class.
 */
class WDSeoImportController {

  public function __construct() {
    $task = WD_SEO_Library::get('task', '');
    if ( method_exists($this, $task) ) {
      $this->$task();
    }
  }

  /**
   * Import meta data from Yoast
   */
  public function import_yoast_post_meta() {

    if (current_user_can('manage_options') && is_admin()) {

      $wdseo_offset = absint( WD_SEO_Library::get('wdseo_offset') );
      global $wpdb;
      $table_name = $wpdb->prefix . 'posts';
      $count_query = $wpdb->get_results( "SELECT * FROM $table_name" );
      $total_count_posts = $wpdb->num_rows;

      $increment = 200;
      global $post;

      if ($wdseo_offset > $total_count_posts) {
        wp_reset_query();

        $yoast_query_terms = get_option('wpseo_taxonomy_meta');

        if ($yoast_query_terms) {

          foreach ($yoast_query_terms as $taxonomies => $taxonomie) {
            foreach ($taxonomie as $term_id => $term_value) {
              $wd_seo_term_meta = array( 'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'opengraph_title' => '',
                'opengraph_description' => '',
                'opengraph_images' => '',
                'use_og_for_twitter' => 1,
                'twitter_title' => '',
                'twitter_description' => '',
                'twitter_images' => '',
                'canonical_url' => '',
                'redirect_url' => '',
                'index' => '',
                'follow' => '',
                'date' => '',
                'robots_advanced' => array()
              );

              $wd_seo_term_meta = (object) $wd_seo_term_meta;

              if ( !empty($term_value['wpseo_title']) ) { //Import title tag
                $wd_seo_term_meta->meta_title = $term_value['wpseo_title'];
              }
              if ( !empty($term_value['wpseo_desc']) ) { //Import meta desc
                $wd_seo_term_meta->meta_description = $term_value['wpseo_desc'];
              }
              if ( !empty($term_value['wpseo_opengraph-title']) ) { //Import Facebook Title
                $wd_seo_term_meta->opengraph_title = $term_value['wpseo_opengraph-title'];
              }
              if ( !empty($term_value['wpseo_opengraph-description']) ) { //Import Facebook Desc
                $wd_seo_term_meta->opengraph_description = $term_value['wpseo_opengraph-description'];
              }
              if ( !empty($term_value['wpseo_opengraph-image']) ) { //Import Facebook Image
                $wd_seo_term_meta->opengraph_images = $term_value['wpseo_opengraph-image'];
              }
              if ( !empty($term_value['wpseo_twitter-title']) ) { //Import Twitter Title
                $wd_seo_term_meta->twitter_title = $term_value['wpseo_twitter-title'];
              }

              if ( !empty($term_value['wpseo_twitter-description']) ) { //Import Twitter Desc
                $wd_seo_term_meta->twitter_description = $term_value['wpseo_twitter-description'];
              }
              if ( !empty($term_value['wpseo_twitter-image']) ) { //Import Twitter Image
                $wd_seo_term_meta->twitter_image = $term_value['wpseo_twitter-image'];
              }
              if ( !empty($term_value['wpseo_noindex']) &&  $term_value['wpseo_noindex'] == 'noindex' ) { //Import Robots NoIndex
                $wd_seo_term_meta->index = '0';
              }
              if ( !empty($term_value['wpseo_canonical']) ) { //Import Canonical URL
                $wd_seo_term_meta->canonical_url = $term_value['wpseo_canonical'];
              }
              update_term_meta($term_id, 'wdseo_options', $wd_seo_term_meta);
            }
          }
        }
        $wdseo_offset = 'done';
        wp_reset_query();
      } else {
        $args = array(
          'posts_per_page' => $increment,
          'post_type' => 'any',
          'post_status' => 'any',
          'offset' => $wdseo_offset,
        );

        $yoast_query = get_posts( $args );

        if ($yoast_query) {
          foreach ($yoast_query as $post) {
            $wd_seo_post_meta = array('meta_title' => '',
              'meta_description' => '',
              'meta_keywords' => '',
              'opengraph_title' => '',
              'opengraph_description' => '',
              'opengraph_images' => '',
              'use_og_for_twitter' => 1,
              'twitter_title' => '',
              'twitter_description' => '',
              'twitter_images' => '',
              'canonical_url' => '',
              'redirect_url' => '',
              'index' => '',
              'follow' => '',
              'date' => '',
              'robots_advanced' => array()
            );

            $wd_seo_post_meta = (object) $wd_seo_post_meta;

            $yoast_wpseo_title = get_post_meta($post->ID, '_yoast_wpseo_title', true);
            if ( $yoast_wpseo_title != '' ) { //Import title tag
              $wd_seo_post_meta->meta_title = $yoast_wpseo_title;
            }

            $yoast_wpseo_metadesc = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
            if ( $yoast_wpseo_metadesc != '' ) { //Import meta desc
              $wd_seo_post_meta->meta_description = $yoast_wpseo_metadesc;
            }

            $yoast_wpseo_opengraph_title = get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true);
            if ( $yoast_wpseo_opengraph_title !='' ) { //Import Facebook Title
              $wd_seo_post_meta->opengraph_title = $yoast_wpseo_opengraph_title;
            }

            $yoast_wpseo_opengraph_description = get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true);
            if ( $yoast_wpseo_opengraph_description !='' ) { //Import Facebook Desc
              $wd_seo_post_meta->opengraph_description = $yoast_wpseo_opengraph_description;
            }

            $yoast_wpseo_opengraph_image = get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true);
            if ( $yoast_wpseo_opengraph_image != '' ) { //Import Facebook Image
              $wd_seo_post_meta->opengraph_images = $yoast_wpseo_opengraph_image;
            }

            $yoast_wpseo_twitter_title = get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true);
            if ( $yoast_wpseo_twitter_title !='' ) { //Import Twitter Title
              $wd_seo_post_meta->twitter_title = $yoast_wpseo_twitter_title;
            }

            $yoast_wpseo_twitter_description = get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true);
            if ( $yoast_wpseo_twitter_description != '' ) { //Import Twitter Desc
              $wd_seo_post_meta->twitter_description = $yoast_wpseo_twitter_description;
            }

            $yoast_wpseo_twitter_image = get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true);
            if ( $yoast_wpseo_twitter_image != '' ) { //Import Twitter Image
              $wd_seo_post_meta->twitter_images = $yoast_wpseo_twitter_image;
            }

            $yoast_wpseo_meta_robots_noindex = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-noindex', true);
            if ( $yoast_wpseo_meta_robots_noindex == '1' ) { //Import Robots NoIndex
              $wd_seo_post_meta->index = '0';
            } elseif ( $yoast_wpseo_meta_robots_noindex == '2' ) {
              $wd_seo_post_meta->index = '1';
            }

            $yoast_wpseo_meta_robots_nofollow = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-nofollow', true);
            if ( $yoast_wpseo_meta_robots_nofollow == '1' ) { //Import Robots NoFollow
              $wd_seo_post_meta->follow = '0';
            } elseif ( $yoast_wpseo_meta_robots_nofollow == '2' ) {
              $wd_seo_post_meta->follow = '1';
            }

            $yoast_wpseo_meta_robots_adv = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true);
            if ( $yoast_wpseo_meta_robots_adv !='' ) { //Import Robots NoOdp, NoImageIndex, NoArchive, NoSnippet

              if (strpos($yoast_wpseo_meta_robots_adv, 'noodp') === false) {
                $wd_seo_post_meta->robots_advanced[] = 'noodp';
              }
              if (strpos($yoast_wpseo_meta_robots_adv, 'noimageindex') === false) {
                $wd_seo_post_meta->robots_advanced[] = 'noimageindex';
              }
              if (strpos($yoast_wpseo_meta_robots_adv, 'noarchive') === false) {
                $wd_seo_post_meta->robots_advanced[] = 'noarchive';
              }
              if (strpos($yoast_wpseo_meta_robots_adv, 'nosnippet') === false) {
                $wd_seo_post_meta->robots_advanced[] = 'nosnippet';
              }
            }

            $yoast_wpseo_canonical = get_post_meta($post->ID, '_yoast_wpseo_canonical', true);
            if ( $yoast_wpseo_canonical != '' ) { //Import Canonical URL
              $wd_seo_post_meta->canonical_url = $yoast_wpseo_canonical;
            }

            if (get_post_meta($post->ID, '_yoast_wpseo_focuskw', true) !='' || get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', true) !='') { //Import Focus Keywords
              $y_fkws_clean = array(); //reset array

              $y_fkws = get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', false);

              foreach ($y_fkws as $value) {
                foreach (json_decode($value) as $key => $value) {
                  $y_fkws_clean[] .= $value->keyword;
                }
              }

              $y_fkws_clean[] .= get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);

              $wd_seo_post_meta->meta_keywords = $y_fkws_clean;
            }

            update_post_meta($post->ID, 'wdseo_options', $wd_seo_post_meta);
          }
        }
        $wdseo_offset += $increment;
      }
      $data = array();
      $data['offset'] = $wdseo_offset;
      wp_send_json_success($data);
      die();
    }
  }

  /**
   * Remove placeholder from Yoast params if there are not the same in 10WEB seo
   *
   * $value String
   *
   * $placeholders array
   *
   * return String
   */
  public function clear_placeholder( $value = "", $placeholders = array() ) {

    if( $value != "" && !empty( $placeholders ) ) {
      foreach ( $placeholders as $placeholder ) {
        $value = str_replace($placeholder, "", $value);
      }
    }

    return $value;
  }

  /**
   * Import global settings from Yoast
   */
  public function import_yoast_global_settings() {
    $wdseo_options = new WD_SEO_Options();

    $wpseo = get_option('wpseo');

    $wdseo_options->notify_google = $wpseo['googleverify'];
    $wdseo_options->notify_yandex = $wpseo['yandexverify'];
    $wdseo_options->sitemap = $wpseo['enable_xml_sitemap'];

    $wpseo_titles = get_option('wpseo_titles');

    $str = $this->clear_placeholder(json_encode($wpseo_titles),['%%sep%%']);
    $wpseo_titles = json_decode( $str, 1 );

    foreach ($wdseo_options->metas as $metaName => $metaValue ) {
      if( $metaName == 'home' ) {
        $wdseo_options->metas->home->meta_title = $wpseo_titles['title-home-wpseo'];
        $wdseo_options->metas->home->meta_description = $wpseo_titles['metadesc-home-wpseo'];
      } else if ( $metaName == 'search' ) {
        $wdseo_options->metas->search->meta_title = $wpseo_titles['title-search-wpseo'];
      } else if ( $metaName == '404' ) {
        $page_404 = '404';
        $wdseo_options->metas->$page_404->meta_title = $wpseo_titles['title-404-wpseo'];
      } else {
        foreach ( $wpseo_titles as $wpseo_title_key => $wpseo_title_value ) {

          $strArray = explode('-', $wpseo_title_key);
          $wpseo_option_key = end($strArray);
          if ( $wpseo_option_key === $metaName && strpos($wpseo_title_key, 'title-') !== FALSE ) {
            $wdseo_options->metas->$metaName->meta_title = $wpseo_title_value;
          }
          if ( $wpseo_option_key === $metaName && strpos($wpseo_title_key, 'metadesc-') !== FALSE ) {
            $wdseo_options->metas->$metaName->meta_description = $wpseo_title_value;
          }
          if ( $wpseo_option_key === $metaName && strpos($wpseo_title_key, 'noindex-') !== FALSE ) {
            $wdseo_options->metas->$metaName->index = $wpseo_title_value;
          }
          if ( $wpseo_option_key === $metaName && strpos($wpseo_title_key, 'metabox-') !== FALSE ) {
            $wdseo_options->metas->$metaName->metabox = $wpseo_title_value;
          }
          if ( $wpseo_option_key === $metaName && strpos($wpseo_title_key, 'showdate-') !== FALSE ) {
            $wdseo_options->metas->$metaName->date = $wpseo_title_value;
          }
        }
      }
    }
    update_option( 'wdseo_options', json_encode($wdseo_options), 'no' );
    die();
  }

}