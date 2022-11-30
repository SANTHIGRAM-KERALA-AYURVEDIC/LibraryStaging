<?php

/**
 * Class WD_SEO_nofollow_urls
 */
class WD_SEO_nofollow_urls {
  private static function link_available( $content = '' ) {
    if ( $content == '' ) {
      return NULL;
    }
    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
    if ( preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER) ) {
      return $matches;
    }

    return NULL;
  }

  private static function add_target_blank( $url, $tag ) {
    $no_follow = '';
    $pattern = '/target\s*=\s*"\s*_(blank|parent|self|top)\s*"/';
    if ( preg_match($pattern, $url) === 0 ) {
      $no_follow .= ' target="_blank"';
    }
    if ( $no_follow ) {
      $tag = self::update_close_tag($tag, $no_follow);
    }

    return $tag;
  }

  private static function is_domain_not_excluded( $url, $all_uniq_excluded_urls ) {
    $domain_check_flag = TRUE;
    if ( !count($all_uniq_excluded_urls) ) {
      return $domain_check_flag;
    }
    foreach ( $all_uniq_excluded_urls as $domain ) {

      $domain = trim($domain);
      if ( $domain == '' ) {
        continue;
      }
      $pos = strpos($url, $domain);
      if ( $pos === FALSE ) {
        continue;
      }
      else {
        $domain_check_flag = FALSE;
        break;
      }
    }

    return $domain_check_flag;
  }

  private static function get_all_uniq_excluded_urls( $global_excluded_urls_array, $post_excluded_urls_array ) {
    $all_excludes = array_merge($global_excluded_urls_array, $post_excluded_urls_array);

    return array_unique($all_excludes);
  }

  private static function add_rel_nofollow( $url, $tag ) {
    $no_follow = '';
    // $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
    // $pattern = '/rel\s*=\s*\"[a-zA-Z0-9_\s]*[n|d]ofollow[a-zA-Z0-9_\s]*\"/';
    $pattern = '/rel\s*=\s*\"[a-zA-Z0-9_\s]*\"/';
    $result = preg_match($pattern, $url, $match);
    if ( $result === 0 ) {
      $no_follow .= ' rel="nofollow noreferrer"';
    }
    else {
      if ( strpos($match[0], 'nofollow') === FALSE && strpos($match[0], 'dofollow') === FALSE ) {
        $temp = $match[0];
        $temp = substr_replace($temp, ' nofollow"', -1);
        $tag = str_replace($match[0], $temp, $tag);
      }
    }
    if ( $no_follow ) {
      $tag = self::update_close_tag($tag, $no_follow);
    }

    return $tag;
  }

  private static function update_close_tag( $tag, $no_follow ) {
    return substr_replace($tag, $no_follow . '>', -1);
  }

  private static function internal_link( $url ) {
    // bypass #more type internal link
    $result = preg_match('/href(\s)*=(\s)*"[#|\/]*[a-zA-Z0-9-_\/]+"/', $url);
    if ( $result ) {
      return TRUE;
    }
    $pos = strpos($url, self::get_domain());
    if ( $pos !== FALSE ) {
      return TRUE;
    }

    return FALSE;
  }

  private static function get_domain() {

    return $_SERVER['HTTP_HOST'];
  }

  /**
   * Get content, modify and return with nofollowed links
   *
   * @param string $content                           Wordpress post/page content
   * @param array  $nofollow_global_options           Nofollow global options
   * @param array  $nofollow_post_excluded_urls_array excluded Urls from post
   *
   * @return string Returns WP post content.
   */
  public static function url_parse( $content, $nofollow_global_options, $nofollow_post_excluded_urls_array ) {
    if ( is_array($nofollow_global_options) ) {
      $wdseo_nofollow_external_urls_global_enable = !empty($nofollow_global_options['wdseo_nofollow_external_urls_global_enable']) ? $nofollow_global_options['wdseo_nofollow_external_urls_global_enable'] : '';
      $wdseo_nofollow_external_urls_global_exclude_array = !empty($nofollow_global_options['wdseo_nofollow_external_urls_global_exclude_array']) ? $nofollow_global_options['wdseo_nofollow_external_urls_global_exclude_array'] : array();
    }
    elseif ( is_object($nofollow_global_options) ) {
      $wdseo_nofollow_external_urls_global_enable = !empty($nofollow_global_options->wdseo_nofollow_external_urls_global_enable) ? $nofollow_global_options->wdseo_nofollow_external_urls_global_enable : '';
      $wdseo_nofollow_external_urls_global_exclude_array = !empty($nofollow_global_options->wdseo_nofollow_external_urls_global_exclude_array) ? $nofollow_global_options->wdseo_nofollow_external_urls_global_exclude_array : array();
    }
    else {
      $wdseo_nofollow_external_urls_global_enable = '';
      $wdseo_nofollow_external_urls_global_exclude_array = array();
    }
    if ( !$wdseo_nofollow_external_urls_global_enable ) {
      return $content;
    }
    $matches = self::link_available($content);
    if ( $matches === NULL ) {
      return $content;
    }
    $all_uniq_excluded_urls = self::get_all_uniq_excluded_urls($wdseo_nofollow_external_urls_global_exclude_array, $nofollow_post_excluded_urls_array); // get all uniq URL's
    for ( $i = 0; $i < count($matches); $i++ ) {
      $tag = $matches[$i][0];
      $url = $matches[$i][0];
      if ( self::internal_link($url) ) { // check URL is internal(self) or not
        continue;
      }
      $tag = self::add_target_blank($url, $tag); // add target _blank to link
      //exclude domain or add nofollow
      if ( self::is_domain_not_excluded($url, $all_uniq_excluded_urls) ) {
        $tag = self::add_rel_nofollow($url, $tag); //add nofollow to link
      }
      $content = str_replace($url, $tag, $content); // replace old <a> to new <a>($tag)
    }
    $content = str_replace(']]>', ']]&gt;', $content);

    return $content;
  }
}
