<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Class WD_SEO_XML.
 */
class WD_SEO_XML {
  /**
   * @var object Options.
   */
  private $options;
  /**
   * @var array Page types.
   */
  public $page_types;
  /**
   * @var int URLs count per Sitemap.
   */
  private $limit = 10000;
  /**
   * @var string Date format in XML.
   */
  private $date_format = 'Y-m-d\TH:m:sP';
  /**
   * @var bool Error during Sitemap generation.
   */
  public $error = FALSE;
  /**
   * @var int Sitemap items count.
   */
  public $sitemap_items_count = 0;
  /**
   * @var array Sitemap last modified date and time.
   */
  public $sitemap_last_modified;
  /**
   * @var string Index Sitemap name.
   */
  public $index_sitemap_name;
  /**
   * @var string Sitemap name.
   */
  public $sitemap_name;
  /**
   * @var string Sitemap path.
   */
  public $sitemap_path;
  /**
   * @var string Sitemap url.
   */
  public $sitemap_url;
  /**
   * @var string Sitemap stylesheet url.
   */
  private $sitemap_stylesheet_url;
  /**
   * @var bool Compress Sitemap xml.
   */
  private $compress = FALSE;
  /**
   * @var array Created Sitemap files.
   */
  public $sitemap_files = array();
  /**
   *
   * @var array Additional pages.
   */
  public $additional_pages = array();

  /**
   * WD_SEO_XML constructor.
   *
   * @param bool $generate
   */
  public function __construct($generate = TRUE) {
    // Get options.
    $this->options = WDSeo()->options;
    $this->limit = $this->options->limit;

    $sitemap_dir = $this->options->get_sitemap_dir();
    $this->sitemap_name = $sitemap_dir['name'];
    $this->sitemap_path = $sitemap_dir['path'];
    $this->sitemap_url = $sitemap_dir['url'];
    // You must manually generate Sitemap after changing stylesheet URL.
    $this->sitemap_stylesheet_url = WD_SEO_URL . '/css/xml-sitemap.xsl';

    // Page types.
    $post_types = WD_SEO_Library::get_post_types();
    if ( isset($this->options->metas->home->index) && $this->options->metas->home->index == 1 ) {
      $this->page_types = array( 'home' => array() );
      $this->add_home_info();
      $this->page_types = array_merge($this->page_types, $post_types);
    }

    foreach ( $post_types as $post_type => $post_arr ) {
      $this->add_post_info($post_type);
    }
    $taxanomies = WD_SEO_Library::get_taxanomies();
    $this->page_types = array_merge($this->page_types, $taxanomies);
    foreach ( $taxanomies as $taxanomy => $taxanomy_arr ) {
      $this->add_taxanomy_info($taxanomy);
    }

    $archives = WD_SEO_Library::get_archives();
    $this->page_types = array_merge( $this->page_types, $archives );
    foreach ( $archives as $archive => $archive_arr ) {
      $this->add_archive_info($archive);
    }

    // Video sitemap
    if (isset($this->options->sitemap_video) && $this->options->sitemap_video == '1') {
      $this->page_types = array_merge( $this->page_types, array( 'video' => array() ) );
      $this->page_types['video']['items'] = array();
      foreach ( $post_types as $post_type => $post_arr ) {
        $this->add_video_info($post_type);
      }
    }
    // Additional pages
    if( !empty($this->options->additional_pages) ) {
      $this->page_types = array_merge( $this->page_types, array( 'additional_pages' => array() ) );
      $this->page_types['additional_pages']['items'] = array();
      $additional_pages = (array) $this->options->additional_pages;
      $this->add_additional_page_info( $additional_pages );
    }

    // Generate Sitemaps.
    if ( $generate ) {
      $this->generate();
    }
  }

  /**
   * Generate Sitemap.
   */
  private function generate() {
    // Delete existing Sitemap XMLs.
    WD_SEO_Library::remove_directory($this->sitemap_path);

    // Create Sitemap folder.
    if ( !is_dir($this->sitemap_path) ) {
      mkdir($this->sitemap_path);
    }

    // Create Sitemap index XML.
    $this->create($this->sitemap_name, $this->sitemap_index_content(), $this->compress);

    // Notify search engines of the updated Sitemap.
    $this->ping_search_engines();

    // Save last modified date and items count to options.
    $this->options->sitemap_last_modified = $this->sitemap_last_modified;
    $this->options->sitemap_items_count = $this->sitemap_items_count;
    $this->options->sitemap_files = $this->sitemap_files;
    update_option(WD_SEO_PREFIX . '_options', json_encode($this->options), 'no');
  }

  /**
   * Return Sitemap info.
   *
   * @param string $url
   * @param bool   $priority
   * @param bool   $date
   * @param bool   $changefreq
   * @param array  $images
   *
   * @return array
   */
  private function add_sitemap_info( $url, $priority = FALSE, $date = FALSE, $changefreq = FALSE, $images = array(), $videos = array() ) {
    $priority = $priority === FALSE ? 0.8 : $priority;
    $date = $date === FALSE ? time() : $date;
    $changefreq = $changefreq === FALSE ? $this->options->changefreq : $changefreq;
    $item = array(
      'loc' => esc_url($url),
      'lastmod' => date($this->date_format, $date),
      'changefreq' => $changefreq,
      'priority' => sprintf("%.1f", $priority),
      'images' => $images,
      'videos' => $videos,
    );
    return $item;
  }

  /**
   * Get Sitemap parameters for home page.
   */
  private function add_home_info() {
    $url = home_url();
    $priority = 1;
    $this->page_types['home']['items'] = array();
    $this->page_types['home']['items'][0] = $this->add_sitemap_info($url, $priority);
  }

  /**
   * Get Sitemap parameters for each post for given post type.
   *
   * @param string $post_type
   */
  private function add_post_info( $post_type ) {
    $frontpage_id = intval( get_option('page_on_front') );
    $this->page_types[$post_type]['items'] = array();
    if ( is_array($this->options->exclude_post_types) && in_array($post_type, $this->options->exclude_post_types) ) {
      return;
    }
    $args = array(
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'exclude' => $this->options->exclude_posts,
    );
    $posts = get_posts($args);
    $this->page_types[$post_type]['items'] = array();
    foreach ( $posts as $post ) {
      $options = new WD_SEO_Postmeta( $post->ID, 'post', 'site_values' );
      $type = $post->post_type;
      $meta_type_index = $this->options->metas->$type->index;
      if ( WD_SEO_Library::woocommerce_active() ) {
        $options->index = $this->check_wc_page_index_by_id($post->ID, $options->index);
        $meta_type_index = $this->check_wc_page_index_by_id($post->ID, $meta_type_index);
      }
      if ( $options->redirect_url != "" // Don't add redirected URLs.
        || $options->index == 0 // Don't add no-index files.
        || ($options->index == "" && $meta_type_index == 0 ) // Don't add no-index(set from Meta information) files.
        || $frontpage_id == $post->ID // Don't add Home page URL.
      ) {
        continue;
      }
      $canonical = $options->canonical_url;
      $get_permalink = get_permalink($post->ID);
      $url = $canonical ? $canonical : $get_permalink;
      // Get priority.
      $priority = '';
      $priority = $priority ? $priority : ($post->post_parent ? 0.6 : 0.8);
      // Last modified date.
      $date = $post->post_modified ? strtotime($post->post_modified) : time();
      // Get images.
      $images = array();
      if ( $this->options->sitemap_image && isset($post->post_content) ) {
        $images = WD_SEO_Library::get_images_content($post);
      }
      // Add posts.
      if( $url == $get_permalink ){
        $this->page_types[$post_type]['items'][$post->ID] = $this->add_sitemap_info($url, $priority, $date, FALSE, $images);
      }
    }
  }

  /**
   * Get Video Sitemap parameters for each post for given post type.
   *
   * @param string $post_type
   */
  private function add_video_info( $post_type ) {
    if ( is_array($this->options->exclude_post_types) && in_array($post_type, $this->options->exclude_post_types) ) {
      return;
    }
    $args = array(
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'exclude' => $this->options->exclude_posts,
    );
    $posts = get_posts($args);
    foreach ( $posts as $post ) {
      $options = new WD_SEO_Postmeta( $post->ID, 'post', 'site_values' );
      $videos = $this->get_videos($post, $options);
      $type = $post->post_type;
      if ( $options->redirect_url != "" // Don't add redirected URLs.
        || $options->index == 0 // Don't add no-index files.
        || ($options->index == "" && $this->options->metas->$type->index == 0 ) // Don't add no-index(set from Meta information) files.
        || $options->exclude_from_video_sitemap =='1' // Don't add exclude from video sitemap?
      ) {
        continue;
      }
      $canonical = $options->canonical_url;
      $get_permalink = get_permalink($post->ID);
      $url = $canonical ? $canonical : $get_permalink;
      // Get priority.
      $priority = '';
      $priority = $priority ? $priority : ($post->post_parent ? 0.6 : 0.8);
      // Last modified date.
      $date = $post->post_modified ? strtotime($post->post_modified) : time();
      // Add posts.
      if( $url ==  $get_permalink ){
        $this->page_types['video']['items'][$post->ID] = $this->add_sitemap_info($url, $priority, $date, FALSE, array(), $videos);
      }
    }
  }

  /**
   * Get videos.
   *
   * @param object $post
   * @param object $videos
   *
   * @return array|bool
   */
  private function get_videos( $post, $options ) {
    /*Videos default values*/
    $videos = $options->wdseo_video;
    foreach ($videos as $key => $video) {
      if (!empty($video["video_url"])) {
        if ($video["video_thumbnail"]) {
          $video["video_thumbnail"] = htmlspecialchars(urldecode(esc_attr(wp_filter_nohtml_kses($video["video_thumbnail"]))));
        } else {
          $video["video_thumbnail"] = htmlspecialchars(urldecode(esc_attr(wp_filter_nohtml_kses(get_the_post_thumbnail_url($post->ID)))));
        }

        if ($video["video_title"] == '') {
          if ($options->meta_title != '') {
            $video["video_title"] = $options->meta_title;
          } elseif (get_the_title($post->ID) != '') {
            $video["video_title"] = get_the_title($post->ID);
          }
        }
        if ($video["video_description"] == '') {
          if ($options->meta_description != '') {
            $video["video_description"] = $options->meta_description;
          } elseif (get_the_excerpt($post->ID) != '') {
            $video["video_description"] = wp_trim_words(esc_attr(wp_filter_nohtml_kses(htmlentities(get_the_excerpt($post->ID)))), 60);
          }
        }
        $video["publication_date"] = get_the_modified_date('c', $post);

        if ($video["not_family_friendly"] != '') {
          $video["not_family_friendly"] = 'no';
        } else {
          $video["not_family_friendly"] = 'yes';
        }

        if ($video["video_tags"] != '') {
          $video["video_tags"] = esc_attr(wp_filter_nohtml_kses($video["video_tags"]));
        } else {
          $tags = get_the_tags($post->ID);
          if (!empty($tags)) {
            $tags_list = '';
            foreach ($tags as $tag) {
              $tags_list .= $tag->name . ',';
            }
            $video["video_tags"] = substr($tags_list, 0, -1);
          }
        }

        if ($video["video_categories"] != '') {
          $video["video_categories"] = esc_attr(wp_filter_nohtml_kses($video["video_categories"]));
        } else {
          $categories = get_the_category($post->ID);
          if (!empty($categories)) {
            $first_category = esc_html($categories[0]->name);
            $video["video_categories"] = $first_category;
          }
        }
      }
      else {
        unset($videos[$key]);
      }
    }

    return $videos;
  }

  /**
   * Get sitemap parameters for each term for given taxanomy.
   *
   * @param string $taxanomy
   */
  private function add_taxanomy_info( $taxanomy ) {
    $this->page_types[$taxanomy]['items'] = array();
    if ( is_array($this->options->exclude_taxonomies) && in_array($taxanomy, $this->options->exclude_taxonomies) ) {
      return;
    }
    $terms = get_terms($taxanomy, array( 'hide_empty' => TRUE ));
    foreach ( $terms as $term ) {
      $type = $term->taxonomy;
      $options = new WD_SEO_Postmeta( $term->term_id, $type, 'site_values' );
      if ( $options->index == 0 // Don't add no-index files.
        || ($options->index == "" && $this->options->metas->$type->index == 0 ) // Don't add no-index(set from Meta information) files.
      ) {
        continue;
      }
      $canonical = $options->canonical_url;
      $get_term_link = get_term_link($term, $term->taxonomy);
      $url = $canonical ? $canonical : $get_term_link;
      // Get priority.
      $priority = '';
      $priority = $priority ? $priority : ($term->count > 10 ? 0.6 : ($term->count > 3 ? 0.4 : 0.2));
      // Last modified date.
      $date = $term->post_date ? strtotime($term->post_date) : time();
      if( $url == $get_term_link ) {
        $this->page_types[$taxanomy]['items'][$term->slug] = $this->add_sitemap_info($url, $priority, $date);
      }
    }
  }

  /**
   * Get sitemap parameters for archives.
   *
   * @param string $archive
   */
  private function add_archive_info( $archive ) {
    $this->page_types[$archive]['items'] = array();
    if ( is_array($this->options->exclude_archives) && in_array($archive, $this->options->exclude_archives) ) {
      return;
    }

    $priority = 0.8;
    $date = time();
    if ( $archive == 'author_archive' ) {
      // Authors archives.
      $users = get_users();
      foreach ( $users as $user ) {
        $url = get_author_posts_url($user->ID);
        $this->page_types[$archive]['items'][$user->ID] = $this->add_sitemap_info($url, $priority, $date);
      }
    }
    elseif ( $archive == 'date_archive' ) {
      // Date archives by month.
      $args = array(
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'ASC',
      );
      $get_first_post = get_posts($args);
      if ( empty($get_first_post) ) {
        return;
      }
      $first_post_date = $get_first_post[0]->post_date;
      $first_post_year = date("Y", strtotime($first_post_date));
      $first_post_month = date("m", strtotime($first_post_date));
      $args = array(
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
      );
      $get_last_post = get_posts($args);
      $last_post_date = $get_last_post[0]->post_modified;
      $last_post_year = date("Y", strtotime($last_post_date));
      $last_post_month = date("n", strtotime($last_post_date));

      for ( $year = $first_post_year; $year <= $last_post_year; $year++ ) {
        for ( $month = ($year == $first_post_year ? $first_post_month : 1); $month <= ($year == $last_post_year ? $last_post_month : 12); $month++ ) {
          $args = array(
            'posts_per_page'   => 1,
            'year'     => $year,
            'monthnum' => $month
          );
          $get_a_post = query_posts( $args );
          if (!empty($get_a_post)) {
            $url = get_month_link($year, $month);
            $this->page_types[$archive]['items'][$year . '-' . $month] = $this->add_sitemap_info($url, $priority, $date);
          }
        }
      }
    }
  }

  /**
   * Add additional page info.
   *
   * @param array $args
   */
  private function add_additional_page_info( $args = array() ) {
    $this->page_types['additional_pages']['items'] = array();
    if ( !empty($args) && !empty($args['page_url']) ) {
      foreach ( $args['page_url'] as $index => $val ) {
        $url = $val;
        $priority = !empty($args['priority'][$index]) ? $args['priority'][$index] : '';
        $changefreq = !empty($args['frequency'][$index]) ? $args['frequency'][$index] : '';
        $date = !empty($args['last_changed'][$index]) ? strtotime($args['last_changed'][$index]) : time();
        $this->page_types['additional_pages']['items'][] = $this->add_sitemap_info($url, $priority, $date, $changefreq);
      }
    }
  }

  /**
   * Return the url of created file.
   *
   * @param string $filename XML file name.
   * @param string $content  XML file content to write.
   * @param bool   $compress Compress created file.
   *
   * @return bool|string
   */
  private function create( $filename, $content, $compress = FALSE ) {
    $archive_extension = $compress ? '.gz' : '';
    $path = $this->sitemap_path . $filename . $archive_extension;
    if ($compress) {
      $content = gzencode($content, 9);
    }
    $file = @fopen($path, "w");
    if ( $file === FALSE ) {
      $this->error = TRUE;
      return FALSE;
    }
    $fwrite = @fwrite($file, $content);
    @fclose($file);
    if ( $fwrite === FALSE ) {
      $this->error = TRUE;

      return FALSE;
    }
    else {
      $url = $this->sitemap_url . $filename . $archive_extension;
      $this->sitemap_files[$filename] = array(
        'url' => $url,
        'path' => $path,
      );

      return $url;
    }
  }

  /**
   * Generate sitemap index xml content.
   *
   * @return string
   */
  private function sitemap_index_content() {
    // Create Sitemap XMLs depend on page type.
    foreach ( $this->page_types as $page_type => $page_type_value ) {
      if ( empty($page_type_value['items']) ) {
        unset($this->page_types[$page_type]);
        continue;
      }
      $this->sitemap_content($page_type, $page_type_value['items'], 0);
    }

    // Sitemap modify date.
    $this->sitemap_last_modified = array(
      'date' => current_time(get_option('date_format')),
      'time' => current_time(get_option('time_format')),
    );
    $date = date($this->date_format);

    // Create Sitemap index XML.
    $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $content .= $this->add_stylesheet();
    $content .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Generate Sitemap index XML content depend on created XMLs.
    foreach ( $this->sitemap_files as $sitemap_name => $sitemap_file ) {
      $content .= '<sitemap>' . "\n";
      $content .= '<loc>' . $sitemap_file['url'] . '</loc>' . "\n";
      $content .= '<lastmod>' . $date . '</lastmod>' . "\n";
      $content .= '</sitemap>' . "\n";
    }

    $content .= '</sitemapindex>';

    return $content;
  }

  /**
   * Generate sitemap xml content.
   *
   * @param string $page_type
   * @param array  $items
   * @param int    $limit_start
   *
   * @return string|bool
   */
  private function sitemap_content( $page_type, $items, $limit_start = 0 ) {
    if ( empty($items) ) {
      return FALSE;
    }
    $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $content .= $this->add_stylesheet();
    $content .= '<urlset';
    $content .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
    $content .= ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"';
    $content .= ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
    if ( $this->options->sitemap_image ) {
      $content .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
    }
    $content .= '>' . "\n";
    $items_values = array_values($items);
    $count = count($items_values);
    $loop = min(array( $limit_start + $this->limit, $count ));

    for ( $i = $limit_start; $i < $loop; $i++ ) {
      if ($page_type != "video" || ($page_type == "video" && isset($items_values[$i]['videos']) && !empty($items_values[$i]['videos']))) {
        $this->sitemap_items_count++;
        $content .= '<url>' . "\n";

        foreach ($items_values[$i] as $key => $value) {
          if ('images' == $key) {
            if (!empty($value) && is_array($value)) {
              foreach ($value as $image) {
                if (is_array($image) && !empty($image['src'])) {
                  $content .= '<image:image>' . "\n";
                  $content .= '<image:loc>' . esc_url($image['src']) . '</image:loc>' . "\n";
                  if (!empty($image['title'])) {
                    $content .= '<image:title>' . ent2ncr($image['title']) . '</image:title>' . "\n";
                  }
                  if (!empty($image['alt'])) {
                    $content .= '<image:caption>' . ent2ncr($image['alt']) . '</image:caption>' . "\n";
                  }
                  $content .= '</image:image>' . "\n" . "\n";
                }
              }
            }
          } elseif ('videos' == $key) {
            if (!empty($value) && is_array($value)) {

              foreach ($value as $video) {
                if (is_array($video) && !empty($video['video_url'])) {
                  $content .= '<video:video>' . "\n";

                  if (!empty($video["video_thumbnail"])) {
                    $content .= '<video:thumbnail_loc>' . $video["video_thumbnail"] . '</video:thumbnail_loc>' . "\n";
                  }
                  if (!empty($video["video_title"])) {
                    $content .= '<video:title>' . $video["video_title"] . '</video:title>' . "\n";
                  }
                  if (!empty($video["video_description"])) {
                    $content .= '<video:description>' . $video["video_description"] . '</video:description>' . "\n";
                  }
                  if ($video['internal_video'] != '') {
                    $content .= '<video:content_loc>' . $video["video_url"] . '</video:content_loc>' . "\n";
                  } else {
                    $content .= '<video:player_loc>' . $video["video_url"] . '</video:player_loc>' . "\n";
                  }
                  if (!empty($video["video_duration"])) {
                    $content .= '<video:duration>' . $video["video_duration"] . '</video:duration>' . "\n";
                  }
                  if (!empty($video["video_rating"])) {
                    $content .= '<video:rating>' . $video["video_rating"] . '</video:rating>' . "\n";
                  }
                  if (!empty($video["view_count"])) {
                    $content .= '<video:view_count>' . $video["view_count"] . '</video:view_count>' . "\n";
                  }
                  if (!empty($video["publication_date"])) {
                    $content .= '<video:publication_date>' . $video["publication_date"] . '</video:publication_date>' . "\n";
                  }
                  if (!empty($video["not_family_friendly"])) {
                    $content .= '<video:family_friendly>' . $video["not_family_friendly"] . '</video:family_friendly>' . "\n";
                  }
                  if (!empty($video["video_tags"])) {
                    $content .= '<video:tag>' . $video["video_tags"] . '</video:tag>' . "\n";
                  }
                  if (!empty($video["video_categories"])) {
                    $content .= '<video:category>' . $video["video_categories"] . '</video:category>' . "\n";
                  }
                  $content .= '</video:video>' . "\n" . "\n";
                }
              }
            }
          } else {
            $content .= '<' . $key . '>' . $value . '</' . $key . '>' . "\n";
          }
        }
        $content .= '</url>' . "\n";
      }
    }
    $content .= '</urlset>';

    $postfix = ( $count - $limit_start > $this->limit ) ? round($limit_start / $this->limit) + 1 : '';
    $file_name = $page_type . $postfix . '-' . $this->sitemap_name;
    $this->create($file_name, $content, $this->compress);

    // Provide multiple Sitemap files to limit URLs in a sitemap.
    if ( $count - $limit_start > $this->limit ) {
      $this->sitemap_content($page_type, $items, $limit_start + $this->limit);
    }

    return $content;
  }

  /**
   * Add stylesheet to Sitemap.
   *
   * @return string
   */
  private function add_stylesheet() {
    $content = '';
    if ( $this->options->sitemap_stylesheet ) {
      $content = '<?xml-stylesheet type="text/xml" href="' . $this->sitemap_stylesheet_url . '"?>' . "\n";
    }

    return $content;
  }

  /**
   * Notify search engines of the updated Sitemap.
   *
   * @return array Last notify date.
   */
  private function ping_search_engines() {
    $result = array();
    $date = time();
    // Search engines to be notified.
    $engines = array(
      'google' => array(
        'option' => $this->options->notify_google,
        'url' => 'http://www.google.com/webmasters/tools/ping?sitemap=',
      ),
      'bing' => array(
        'option' => $this->options->notify_bing,
        'url' => 'http://www.bing.com/webmaster/ping.aspx?sitemap=',
      ),
    );
    foreach ( $engines as $index => $engine ) {
      if ( $engine['option'] ) {
        wp_remote_get($engine['url'] . esc_url($this->sitemap_url));
        $result[$index] = array(
          'date' => $date,
        );
      }
    }

    return $result;
  }

  /**
   * Check WooCommerce page index/noindex.
   *
   * @param $id
   * @param $default
   *
   * @return int
   */
  private function check_wc_page_index_by_id( $id, $default ) {
    $woocommerce_pages = WD_SEO_Library::get_woocommerce_pages();
    if ( !empty($woocommerce_pages) ) {
      $cart = !empty($woocommerce_pages['cart']) ? $woocommerce_pages['cart'] : 0;
      $checkout = !empty($woocommerce_pages['checkout']) ? $woocommerce_pages['checkout'] : 0;
      $myaccount = !empty($woocommerce_pages['myaccount']) ? $woocommerce_pages['myaccount'] : 0;
      if ( (!empty($cart) && $cart == $id && isset($this->options->woocommerce->cart_page_index) && $this->options->woocommerce->cart_page_index == 1)
			|| (!empty($checkout) && $checkout == $id && isset($this->options->woocommerce->checkout_page_index) && $this->options->woocommerce->checkout_page_index == 1)
			|| (!empty($myaccount) && $myaccount == $id && isset($this->options->woocommerce->customer_account_page_index) && $this->options->woocommerce->customer_account_page_index == 1)
		) {
        $default = 1;
      }
    }

    return $default;
  }
}
