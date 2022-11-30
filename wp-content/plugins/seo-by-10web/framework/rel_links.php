<?php

class WDRel_Links {

    public static function home_url( $path = '', $scheme = null ) {

        $home_url = home_url( $path, $scheme );

        if ( ! empty( $path ) ) {
            return $home_url;
        }

        $home_path = wp_parse_url( $home_url, PHP_URL_PATH );

        if ( '/' === $home_path ) { // Home at site root, already slashed.
            return $home_url;
        }

        if ( is_null( $home_path ) ) { // Home at site root, always slash.
            return trailingslashit( $home_url );
        }

        if ( is_string( $home_path ) ) { // Home in subdirectory, slash if permalink structure has slash.
            return user_trailingslashit( $home_url );
        }

        return $home_url;
    }

    /**
     * Output the rel next/prev links for an archive page.
     */
    protected function rel_links_archive() {
      $wdseosite = new WDSeo_Site();
      $url = $wdseosite->canonical(false, true);
      if ( ! is_string( $url ) || $url === '' ) {
        return;
      }

      $paged = max( 1, (int) get_query_var( 'paged' ) );

      if ( $paged === 2 ) {
//        $url = $this->home_url();
        $this->adjacent_rel_link( 'prev', $url, ( $paged - 1 ) );
      }

      // Make sure to use index.php when needed, done after paged == 2 check so the prev links to homepage will not have index.php erroneously.
      if ( is_front_page() ) {
        $url = $this->get_base_url( '' );
      }

      if ( $paged > 2 ) {
        $this->adjacent_rel_link( 'prev', $url, ( $paged - 1 ) );
      }

      if ( $paged < $GLOBALS['wp_query']->max_num_pages ) {
        $this->adjacent_rel_link( 'next', $url, ( $paged + 1 ) );
      }
    }

    /**
     * Get adjacent pages link for archives.
     *
     * @since 1.0.2
     * @since 7.1    Added $query_arg parameter for single post/page pagination.
     *
     * @param string $rel       Link relationship, prev or next.
     * @param string $url       The un-paginated URL of the current archive.
     * @param string $page      The page number to add on to $url for the $link tag.
     * @param string $query_arg Optional. The argument to use to set for the page to load.
     *
     * @return void
     */
    public function adjacent_rel_link( $rel, $url, $page, $query_arg = 'paged' ) {

        global $wp_rewrite;
        if ( ! $wp_rewrite->using_permalinks() ) {
            if ( $page > 1 ) {
                $url = add_query_arg( $query_arg, $page, $url );
            }
        }
        else {
            if ( $page > 1 ) {
                $base = '';
                if ( ! is_singular() || $this->is_home_static_page() ) {
                    $base = trailingslashit( $GLOBALS['wp_rewrite']->pagination_base );

                }
                $url = user_trailingslashit( trailingslashit( $url ) . $base . $page );
            }
        }

        /**
         * Filter: 'wdseo_adjacent_rel_url' - Allow changing the URL for rel output by SEO 10Web.
         *
         * @api string $url The URL that's going to be output for $rel.
         *
         * @param string $rel Link relationship, prev or next.
         */
        $url = apply_filters( 'wdseo_adjacent_rel_url', $url, $rel );
        /**
         * Filter: 'wdseo_' . $rel . '_rel_link' - Allow changing link rel output by SEO 10Web.
         *
         * @api string $unsigned The full `<link` element.
         */
        $link = apply_filters( 'wdseo_' . $rel . '_rel_link', '<link rel="' . esc_attr( $rel ) . '" href="' . esc_url( $url ) . "\" />\n" );

        if ( is_string( $link ) && $link !== '' ) {
            echo $link;
        }
    }

    /**
   * Determine whether this is the static frontpage.
   *
   * @return bool Whether or not the current page is a static frontpage.
   */
    public function is_home_static_page() {
        return ( is_front_page() && get_option( 'show_on_front' ) === 'page' && is_page( get_option( 'page_on_front' ) ) );
    }

  /**
   * Adds 'prev' and 'next' links to archives.
   *
   * @link  http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
   * @since 1.0.3
   */
    public function adjacent_rel_links() {
        /**
         * Filter: 'wdseo_disable_adjacent_rel_links' - Allows disabling of SEO 10Web adjacent links if this is being handled by other code.
         *
         * @api bool $links_generated Indicates if other code has handled adjacent links.
         */
        if ( true === apply_filters( 'wdseo_disable_adjacent_rel_links', false ) ) {
            return;
        }

        if ( is_singular() ) {
            $this->rel_links_single();
            return;
        }

        $this->rel_links_archive();
    }

  /**
   * Output the rel next/prev links for a single post / page.
   *
   * @return void
   */
  protected function rel_links_single() {
    $num_pages = 1;

    $queried_object = get_queried_object();
    if ( ! empty( $queried_object ) ) {
      $num_pages = ( substr_count( $queried_object->post_content, '<!--nextpage-->' ) + 1 );
    }

    if ( $num_pages === 1 ) {
      return;
    }

    $page = max( 1, (int) get_query_var( 'page' ) );
    $url  = get_permalink( get_queried_object_id() );

    if ( $page > 1 ) {
      $this->adjacent_rel_link( 'prev', $url, ( $page - 1 ), 'page' );
    }

    if ( $page < $num_pages ) {
      $this->adjacent_rel_link( 'next', $url, ( $page + 1 ), 'page' );
    }
  }

  /**
   * Create base URL for the sitemap.
   *
   * @param string $page Page to append to the base URL.
   *
   * @return string base URL (incl page)
   */

  public static function get_base_url( $page ) {

    global $wp_rewrite;

    $base = $wp_rewrite->using_index_permalinks() ? 'index.php/' : '/';

    /**
     * Filter the base URL of the sitemaps.
     *
     * @param string $base The string that should be added to home_url() to make the full base URL.
     */
    $base = apply_filters( 'wdseo_sitemaps_base_url', $base );

    /*
     * Get the scheme from the configured home URL instead of letting WordPress
     * determine the scheme based on the requested URI.
     */
    return home_url( $base . $page, wp_parse_url( get_option( 'home' ), PHP_URL_SCHEME ) );
  }
}
?>