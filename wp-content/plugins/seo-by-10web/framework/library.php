<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Library class.
 */
class WD_SEO_Library {
  /**
   * @var array SEO and Analytics plugins recommend to deactivate.
   */
  public static $seo_plugins = array( 'wordpress-seo', 'all-in-one-seo-pack' );
  public static $analytics_plugins = array( 'google-analytics-for-wordpress', 'google-analytics-dashboard-for-wp' );
  /**
   * Get request value.
   *
   * @param string $key
   * @param string $default_value
   * @param bool $esc_html
   *
   * @return string|array
   */
  public static function get($key, $default_value = '', $esc_html = true) {
    if (isset($_GET[$key])) {
      $value = $_GET[$key];
    }
    elseif (isset($_POST[$key])) {
      $value = $_POST[$key];
    }
    elseif (isset($_REQUEST[$key])) {
      $value = $_POST[$key];
    }
    else {
      $value = '';
    }
    if (!$value) {
      $value = $default_value;
    }
    if (is_array($value)) {
      array_walk_recursive($value, array('self', 'validate_data'), $esc_html);
    }
    else {
      self::validate_data($value, $esc_html);
    }
    return $value;
  }

  /**
   * Validate data.
   *
   * @param $value
   * @param $esc_html
   */
  private static function validate_data(&$value, $esc_html) {
    $value = stripslashes($value);
    if ($esc_html) {
      $value = esc_html($value);
    }
  }

  /**
   * Verify nonce for given page.
   *
   * @param string $page
   */
  public static function verify_nonce( $page ) {
    $nonce_verified = FALSE;
    if ( isset($_GET[WD_SEO_NONCE]) && wp_verify_nonce($_GET[WD_SEO_NONCE], $page) ) {
      $nonce_verified = TRUE;
    }
    if ( !$nonce_verified ) {
      die(__('Sorry, your nonce did not verify.', WD_SEO_PREFIX));
    }
  }

  /**
   * Require php files in specified folder.
   *
   * @param string $dir_path
   */
  public static function require_dir($dir_path) {
    $files = scandir($dir_path);
    foreach ($files as $file) {
      if (($file == '.') || ($file == '..')) {
        continue;
      }
      $file = $dir_path . '/' . $file;

      if (is_dir($file) == TRUE) {
        self::require_dir($file);
      }
      else {
        if ((is_file($file) == TRUE)
          && (pathinfo($file, PATHINFO_EXTENSION) == 'php')) {
          require_once wp_normalize_path( $file );
        }
      }
    }
  }

  /**
   * Get special pages.
   *
   * @return array.
   */
  public static function get_special_pages() {
    $options = array(
      'home' => array(
        'name' => __('Homepage', WD_SEO_PREFIX),
        'exclude_fields' => array('date','meta_pagination'),
        'defaults' => array(
          'meta_title' => '%%sitename%%',
          'meta_description' => '%%sitedesc%%',
          'opengraph_title' => '%%sitename%%',
          'opengraph_description' => '%%sitedesc%%',
          'twitter_title' => '%%sitename%%',
          'twitter_description' => '%%sitedesc%%',
        ),
      ),
      'search' => array(
        'name' => __('Search page', WD_SEO_PREFIX),
        'exclude_fields' => array('meta_keywords', 'date', 'robots_advanced','opengraph'),
        'defaults' => array(
          'meta_title' => '%%sitename%%',
          'meta_description' => '%%searchphrase%%',
        ),
      ),
      '404' => array(
        'name' => __('404 page', WD_SEO_PREFIX),
        'exclude_fields' => array('meta_keywords', 'index', 'follow', 'date', 'robots_advanced','opengraph', 'meta_pagination'),
        'defaults' => array(
          'meta_title' => '%%sitename%%',
          'meta_description' => __('Page not found.', WD_SEO_PREFIX),
        ),
      ),
    );
    return $options;
  }

  /**
   * Get post types.
   *
   * @return array.
   */
  public static function get_post_types() {
    $options = array();
    $post_types = get_post_types(array(
                                   'public' => TRUE,
                                   'show_ui' => TRUE,
                                   'exclude_from_search' => FALSE,
                                 ));
    $exclude_types = array('revision', 'nav_menu_item', 'attachment');
    foreach ( $post_types as $post_type ) {
      if ( in_array($post_type, $exclude_types) ) {
        continue;
      }
      $options[$post_type] = array();
      $obj = get_post_type_object($post_type);
      $options[$post_type]['name'] = $obj->label;
      $options[$post_type]['exclude_fields'] = array('canonical_url');
      $options[$post_type]['defaults'] = array(
        'meta_title' => '%%title%%',
        'meta_description' => '%%excerpt%%',
        'opengraph_title' => '%%title%%',
        'opengraph_description' => '%%excerpt%%',
        'twitter_title' => '%%title%%',
        'twitter_description' => '%%excerpt%%',
      );
    }

    return $options;
  }

  /**
   * Get taxanomies.
   *
   * @return array.
   */
  public static function get_taxanomies() {
    $options = array();
    $taxanomies = get_taxonomies(array(
                                   'public' => true,
                                   'show_ui' => true,
                                 ));
    $exclude_taxanomies = array('nav_menu', 'link_category', 'post_format');
    foreach ( $taxanomies as $taxonomy ) {
      if ( in_array($taxonomy, $exclude_taxanomies) ) {
        continue;
      }
      $options[$taxonomy] = array();
      $obj = get_taxonomy($taxonomy);
      $options[$taxonomy]['name'] = $obj->label;
      $options[$taxonomy]['exclude_fields'] = array('canonical_url', 'date');
      $options[$taxonomy]['defaults'] = array(
        'meta_title' => '%%term_title%%',
        'meta_description' => '%%term_description%%',
        'opengraph_title' => '%%term_title%%',
        'opengraph_description' => '%%term_description%%',
        'twitter_title' => '%%term_title%%',
        'twitter_description' => '%%term_description%%',
      );
    }

    return $options;
  }

  /**
   * Get archives.
   *
   * @return array.
   */
  public static function get_archives() {
    $options = array(
      'author_archive' => array(
        'name' => __('Author archive', WD_SEO_PREFIX),
        'description' => __('Author archives could in some cases be seen as duplicate content. So you must manually to add noindex,follow to it so it doesn\'t show up in the search results.', WD_SEO_PREFIX),
        'exclude_fields' => array('canonical_url', 'date'),
        'defaults' => array(
          'meta_title' => '%%name%%',
          'meta_description' => '%%name%%\'s posts',
          'opengraph_title' => '%%name%%',
          'opengraph_description' => '%%name%%\'s posts',
          'twitter_title' => '%%name%%',
          'twitter_description' => '%%name%%\'s posts',
        ),
      ),
      'date_archive' => array(
        'name' => __('Date archive', WD_SEO_PREFIX),
        'description' => __('Date archives could in some cases be seen as duplicate content. So you must manually to add noindex,follow to it so it doesn\'t show up in the search results.', WD_SEO_PREFIX),
        'exclude_fields' => array('canonical_url', 'date'),
        'defaults' => array(
          'meta_title' => '%%currentdate%%',
          'meta_description' => 'Posts of %%currentdate%%',
          'opengraph_title' => '%%currentdate%%',
          'opengraph_description' => 'Posts of %%currentdate%%',
          'twitter_title' => '%%currentdate%%',
          'twitter_description' => 'Posts of %%currentdate%%',
        ),
      ),
    );

    return $options;
  }

  /**
   * Get all types of pages.
   *
   * @return array.
   */
  public static function get_page_types() {
    return array(
      'special_pages' => array(
        'title' => __('Special pages', WD_SEO_PREFIX),
        'types' => WD_SEO_Library::get_special_pages(),
      ),
      'post_types' => array(
        'title' => __('Post types', WD_SEO_PREFIX),
        'types' => WD_SEO_Library::get_post_types(),
      ),
      'taxanomies' => array(
        'title' => __('Taxonomies', WD_SEO_PREFIX),
        'types' => WD_SEO_Library::get_taxanomies(),
      ),
      'archives' => array(
        'title' => __('Archives', WD_SEO_PREFIX),
        'types' => WD_SEO_Library::get_archives(),
      ),
    );
  }

  /**
   * Generate placeholder container template.
   *
   * @return string
   */
  public static function placeholder_template() {
    ob_start();
    ?>
    <div class="wd-placeholder-cont-template">
      <div class="wd-placeholder">
        <?php
        foreach (WD_SEO_Library::get_placeholders() as $item => $label) {
          ?>
          <div data-value="<?php echo esc_attr($item); ?>"
               title="<?php _e('Click to insert', WD_SEO_PREFIX); ?>">
            <?php echo esc_html($label); ?>
          </div>
          <?php
        }
        ?>
      </div>
      <span class="wd-placeholder-btn button-primary"><?php _e('Insert placeholder', WD_SEO_PREFIX); ?></span>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Return placeholders.
   *
   * @return array
   */
  public static function get_placeholders($get_values = false) {
    $options = array(
      "%%date%%" => $get_values ? '' : __('Date of the post/page', WD_SEO_PREFIX),
      "%%title%%" => $get_values ? '' : __('Title of the post/page', WD_SEO_PREFIX),
      "%%sitename%%" => $get_values ? '' : __('Site\'s name', WD_SEO_PREFIX),
      "%%sitedesc%%" => $get_values ? '' : __('Site\'s tagline / description', WD_SEO_PREFIX),
      "%%excerpt%%" => $get_values ? '' : __('Post/page excerpt (or auto-generated if it does not exist)', WD_SEO_PREFIX),
      "%%excerpt_only%%" => $get_values ? '' : __('Post/page excerpt (without auto-generation)', WD_SEO_PREFIX),
      "%%tag%%" => $get_values ? '' : __('Current tag/tags', WD_SEO_PREFIX),
      "%%category%%" => $get_values ? '' : __('Post categories (comma separated)', WD_SEO_PREFIX),
      "%%category_description%%" => $get_values ? '' : __('Category description', WD_SEO_PREFIX),
      "%%tag_description%%" => $get_values ? '' : __('Tag description', WD_SEO_PREFIX),
      "%%term_description%%" => $get_values ? '' : __('Term description', WD_SEO_PREFIX),
      "%%term_title%%" => $get_values ? '' : __('Term name', WD_SEO_PREFIX),
      "%%modified%%" => $get_values ? '' : __('Post/page modified time', WD_SEO_PREFIX),
      "%%id%%" => $get_values ? '' : __('Post/page ID', WD_SEO_PREFIX),
      "%%name%%" => $get_values ? '' : __('Post/page author\'s \'nicename\'', WD_SEO_PREFIX),
      "%%userid%%" => $get_values ? '' : __('Post/page author\'s userid', WD_SEO_PREFIX),
      "%%searchphrase%%" => $get_values ? '' : __('Current search phrase', WD_SEO_PREFIX),
      "%%currenttime%%" => $get_values ? '' : __('Current time', WD_SEO_PREFIX),
      "%%currentdate%%" => $get_values ? '' : __('Current date', WD_SEO_PREFIX),
      "%%currentmonth%%" => $get_values ? '' : __('Current month', WD_SEO_PREFIX),
      "%%currentyear%%" => $get_values ? '' : __('Current year', WD_SEO_PREFIX),
      "%%page%%" => $get_values ? '' : __('Current page number (i.e. page 2 of 4)', WD_SEO_PREFIX),
      "%%pagetotal%%" => $get_values ? '' : __('Current page total', WD_SEO_PREFIX),
      "%%pagenumber%%" => $get_values ? '' : __('Current page number', WD_SEO_PREFIX),
      "%%caption%%" => $get_values ? '' : __('Attachment caption', WD_SEO_PREFIX),
    );
    if ($get_values) {
      $screen = is_admin() ? get_current_screen() : get_queried_object();
      $is_post = is_admin() ? !$screen->taxonomy && $screen->post_type : $screen && 'WP_Post' == get_class($screen);
      $is_taxonomy = is_admin() ? $screen->taxonomy && !$screen->post_type : $screen && 'WP_Term' == get_class($screen);

      global $wp_query;
      $date_format = get_option("date_format");
      $pagenum = get_query_var('paged');
      if ($pagenum === 0) {
        $pagenum = ($wp_query->max_num_pages > 1) ? 1 : '';
      }

      $options['%%sitename%%'] = get_bloginfo("name");
      $options['%%sitedesc%%'] = get_bloginfo("description");
      $options['%%searchphrase%%'] = esc_html(get_query_var('s'));
      $options['%%currenttime%%'] = date('H:i');
      $options['%%currentdate%%'] = date($date_format);
      $options['%%currentmonth%%'] = date('F');
      $options['%%currentyear%%'] = date('Y');
      $options['%%page%%'] = (get_query_var('paged') != 0) ? 'Page ' . get_query_var('paged') . ' of ' . $wp_query->max_num_pages : '';
      $options['%%pagetotal%%'] = ($wp_query->max_num_pages > 1) ? $wp_query->max_num_pages : '';
      $options['%%pagenumber%%'] = $pagenum;

      if ( $is_post ) {
        global $post;
        if (!empty( $post )) {
          $posttags = get_the_tags();
          $posttags_names = array();
          if ($posttags) {
            foreach ($posttags as $tag) {
              $posttags_names[] = $tag->name;
            }
          }
          $posttags = implode(', ', $posttags_names);

          $postcategories = get_the_category();
          $postcategories_names = array();
          if ($postcategories) {
            foreach ($postcategories as $category) {
              $postcategories_names[] = $category->name;
            }
          }
          $postcategories = implode(', ', $postcategories_names);

          $author_id = !empty($post->post_author) ? $post->post_author : get_query_var('author');
          $author_name = get_the_author_meta('display_name', $author_id);
          $options['%%date%%'] = mysql2date($date_format, $post->post_date);
          $options['%%title%%'] = $post->post_title;
          $options['%%excerpt%%'] = @get_the_excerpt();
          $options['%%excerpt_only%%'] = $post->post_excerpt;
          $options['%%tag%%'] = $posttags;
          $options['%%category%%'] = $postcategories;
          $options['%%modified%%'] = mysql2date($date_format, $post->post_modified);
          $options['%%id%%'] = $post->ID;
          $options['%%name%%'] = $author_name;
          $options['%%userid%%'] = $author_id;
          $options['%%caption%%'] = $post->post_excerpt;
        }
      }
      else if ( $is_taxonomy ) {
        $term_id = isset( $_REQUEST['tag_ID'] ) ? (int) $_REQUEST['tag_ID'] : $screen->term_id;
        if ($term_id) {
          $term = get_term($term_id, $screen->taxonomy);
          if (!is_wp_error($term)) {
            $options['%%term_title%%'] = $term->name;
            $options['%%term_description%%'] = $term->description;
            $options['%%category%%'] = $term->name;
            $options['%%category_description%%'] = $term->description;
            $options['%%tag%%'] = $term->name;
            $options['%%tag_description%%'] = $term->description;
          }
        }
      }
    }

    return $options;
  }

  /**
   * Filter to auto generate and strip post excerpt.
   *
   * @param $post_excerpt
   * @param $post
   * @return bool|string
   */
  public static function filter_excerpt( $post_excerpt, $post = NULL ) {
    if ( !$post_excerpt && !empty($post) ) {
      return WD_SEO_Library::truncate_html(wp_strip_all_tags(strip_shortcodes($post->post_content)));
    }

    return wp_strip_all_tags(strip_shortcodes($post_excerpt));
  }

  /**
   * Replace placeholders with values.
   *
   * @param $string
   * @param $placeholders
   *
   * @return mixed
   */
  public static function replace_placeholders( $string, $placeholders ) {
    foreach ( $placeholders as $var => $ph ) {
      $string = str_replace($var, $ph, $string);
    }

    return $string;
  }

  /**
   *
   *
   * @param $text
   * @param int $length
   * @param string $ending
   * @param bool $exact
   * @param bool $considerHtml
   *
   * @return bool|string
   */
  public static function truncate_html($text, $length = 100, $ending = '', $exact = false, $considerHtml = true) {
    if ( $considerHtml ) {
      // If the plain text is shorter than the maximum length, return the whole text
      if ( strlen(preg_replace('/<.*?>/', '', $text)) <= $length ) {
        return $text;
      }
      // Splits all html-tags to scanable lines.
      preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
      $total_length = strlen($ending);
      $open_tags = array();
      $truncate = '';
      foreach ( $lines as $line_matchings ) {
        // If there is any html-tag in this line, handle it and add it (uncounted) to the output.
        if ( !empty($line_matchings[1]) ) {
          // If it's an "empty element" with or without xhtml-conform closing slash.
          if ( preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1]) ) {
            // Do nothing
            // if tag is a closing tag.
          }
          else if ( preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings) ) {
            // Delete tag from $open_tags list.
            $pos = array_search($tag_matchings[1], $open_tags);
            if ( $pos !== FALSE ) {
              unset($open_tags[$pos]);
            }
            // If tag is an opening tag.
          }
          else {
            if ( preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings) ) {
              // Add tag to the beginning of $open_tags list.
              array_unshift($open_tags, strtolower($tag_matchings[1]));
            }
          }
          // Add html-tag to $truncate'd text.
          $truncate .= $line_matchings[1];
        }
        // Calculate the length of the plain text part of the line; handle entities as one character.
        $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
        if ( $total_length + $content_length > $length ) {
          // The number of characters which are left.
          $left = $length - $total_length;
          $entities_length = 0;
          // search for html entities
          if ( preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE) ) {
            // Calculate the real length of all entities in the legal range.
            foreach ( $entities[0] as $entity ) {
              if ( $entity[1] + 1 - $entities_length <= $left ) {
                $left--;
                $entities_length += strlen($entity[0]);
              }
              else {
                // No more characters left.
                break;
              }
            }
          }
          $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
          // Maximum lenght is reached, so get off the loop.
          break;
        }
        else {
          $truncate .= $line_matchings[2];
          $total_length += $content_length;
        }
        // If the maximum length is reached, get off the loop.
        if ( $total_length >= $length ) {
          break;
        }
      }
    }
    else {
      if ( strlen($text) <= $length ) {
        return $text;
      }
      else {
        $truncate = substr($text, 0, $length - strlen($ending));
      }
    }
    // if the words shouldn't be cut in the middle...
    if ( !$exact ) {
      // ...search the last occurance of a space...
      $spacepos = strrpos($truncate, ' ');
      if (isset($spacepos)) {
        // ...and cut the text in this position
        //$truncate = substr($truncate, 0, $spacepos);
        $truncateArray = preg_split("//u", $truncate, -1, PREG_SPLIT_NO_EMPTY);
        if (is_array($truncateArray)) {
          $truncate = join("", array_slice($truncateArray, 0, $spacepos));
        }
      }
    }
    // Add the defined ending to the text.
    $truncate .= $ending;
    if ( $considerHtml ) {
      // Close all unclosed html-tags.
      foreach ( $open_tags as $tag ) {
        $truncate .= '</' . $tag . '>';
      }
    }

    return $truncate;
  }

  /**
   * Get recommends and problems list.
   */
  public static function get_recommends_problems($for_count = FALSE) {
    $data = array();

    // Get all plugins.
    if ( ! function_exists( 'get_plugins' ) ) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();

    if ( !empty($plugins) ) {
      $data['recommends'] = array();
      $link = add_query_arg(array( 'page' => 'wdseo_overview' ), admin_url('admin.php'));

      // Get disabled notices.
      $option_name = WD_SEO_PREFIX . '_disabled_notices';
      $option = get_option($option_name);
      if ( $option ) {
        $disabled_notices = json_decode($option, TRUE);
      }
      else {
        $disabled_notices = array();
      }

      foreach ( $plugins as $key => $val ) {
        if ( is_plugin_active($key) ) {
          // Check SEO and Analytics plugins.
          if ( (empty($disabled_notices) || !array_key_exists($val['TextDomain'], $disabled_notices) )
            && (in_array($val['TextDomain'], WD_SEO_Library::$seo_plugins) || in_array($val['TextDomain'], WD_SEO_Library::$analytics_plugins)) ) {
            if ( !$for_count ) {
              $link = add_query_arg(array( 'task' => 'deactivate', 'plugin' => $val['TextDomain'] ), $link);
              $link = wp_nonce_url($link, WD_SEO_NONCE, WD_SEO_NONCE);
            }
            if ( in_array($val['TextDomain'], WD_SEO_Library::$seo_plugins) ) {
              $message = sprintf(__('Please note that having more than one SEO plugin activated can be misleading for Search Engines, therefore we recommend deactivating other such plugins. %s %s plugin.', WD_SEO_PREFIX), '<a href="' . $link . '">' . __('Deactivate', WD_SEO_PREFIX) . '</a>', $val['Name']);
            }
            elseif ( in_array($val['TextDomain'], WD_SEO_Library::$analytics_plugins) ) {
              $message = sprintf(__('To avoid any possible conflict with %s, we recommend to deactivate any analytics plugins you have installed on your WordPress. You can use the Google Analytics WD instead, which has been fully tested with this plugin. %s %s plugin.', WD_SEO_PREFIX), WD_SEO_NICENAME, '<a href="' . $link . '">' . __('Deactivate', WD_SEO_PREFIX) . '</a>', $val['Name']);
            }
            $plugin_info = array(
              'key' => $val['TextDomain'],
              'domain' => $val['TextDomain'],
              'name' => $val['Name'],
              'message' => $message,
              'link' => $link,
            );

            $data['recommends']['plugins'][] = $plugin_info;
          }
        }
      }
    }

    if( empty($disabled_notices) || !array_key_exists('wdseo_posts_per_page', $disabled_notices) ) {
      // Posts per page count.
      if ( intval(get_option('posts_per_page')) < 16 ) {
        $message = sprintf(__('Display more posts per page on homepage and archives. Go to %s and increase the number of posts per page.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-reading.php') . '">' . __('Settings->Reading->Blog pages show at most', WD_SEO_PREFIX) . '</a>');
        $data['recommends']['plugins']['posts_per_page'] = array(
          'key' => WD_SEO_PREFIX . '_posts_per_page',
          'name' => __('Posts per page', WD_SEO_PREFIX),
          'message' => $message,
          'link' => admin_url('/options-reading.php'),
        );
      }
    }


    // Tagline setting.
    $blog_description = get_bloginfo('description');
    if ( $blog_description == __('Just another WordPress site')
      || $blog_description == 'Just another WordPress site' ) {
      $message = sprintf(__('Please note that you need to %s to have your website recognized by search engines.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-general.php') . '">' . __('change the default tagline', WD_SEO_PREFIX) . '</a>');
      $data['problems']['general_settings']['tagline'] = array(
        'key' => WD_SEO_PREFIX . '_tagline',
        'name' => __('Tagline', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-general.php'),
      );
    }

    // Permalink setting.
    if ( strpos(get_option('permalink_structure'), '/%postname%/') === FALSE ) {
      $message = sprintf(__('Please note that you need to include your post name in the %s to make it more descriptive.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-permalink.php') . '">' . __('permalink', WD_SEO_PREFIX) . '</a>');
      $data['problems']['options_permalink']['postname'] = array(
        'key' => WD_SEO_PREFIX . '_postname',
        'name' => __('Permalinks', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-permalink.php'),
      );
    }

    // Reading setting.
    if ( get_option('blog_public') == 0 ) {
      $message = sprintf(__('Your blog is not publicly visible. Go to %s and uncheck the “Discourage search engines from indexing this site" option.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-reading.php') . '">' . __('Settings->Reading->Search Engine Visibility', WD_SEO_PREFIX) . '</a>');
      $data['problems']['reading_settings']['postname'] = array(
        'key' => WD_SEO_PREFIX . '_blog_public',
        'name' => __('Search Engine Visibility', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-reading.php'),
      );
    }

    // Discussion setting.
    if ( get_option('page_comments') == 1 ) {
      $message = sprintf(__('To avoid issues associated with duplicate content, you are recommended to disable %s.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-discussion.php') . '">' . __('comment pagination', WD_SEO_PREFIX) . '</a>');
      $data['problems']['discussion_settings']['postname'] = array(
        'key' => WD_SEO_PREFIX . '_page_comments',
        'name' => __('Break comments into pages', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-discussion.php'),
      );
    }

    // Check SSL certificate
    if ( !self::is_ssl() ) {
      $message = __('Your site doesn\'t use an SSL certificate!', WD_SEO_PREFIX);
      $data['problems']['ssl_settings']['ssl'] = array(
        'key' => WD_SEO_PREFIX . '_is_ssl',
        'name' => __('SSL certificate', WD_SEO_PREFIX),
        'message' => $message,
        'link' => '',
      );
    }

    // RSS full text option.
    if ( get_option('rss_use_excerpt' ) == '0' ) {
      $message = sprintf(__('Your RSS feed shows full text!. Go to %s and change the option.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-reading.php') . '">' . __('Settings->Reading->For each article in a feed, show', WD_SEO_PREFIX) . '</a>');
      $data['problems']['reading_settings_rss']['rss_full'] = array(
        'key' => WD_SEO_PREFIX . '_rss_full_text',
        'name' => __('RSS full text', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-reading.php'),
      );
    }

    return $data;
  }

  /**
   * Determines if SSL is used.
   *
   * @return bool True if SSL, otherwise false.
   */
  public static function is_ssl() {

    if ( isset( $_SERVER['HTTPS'] ) ) {
      if ( 'on' == strtolower( $_SERVER['HTTPS'] ) ) {
        return true;
      }

      if ( '1' == $_SERVER['HTTPS'] ) {
        return true;
      }
    } elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
      return true;
    }
    return false;
  }


  /**
   * Get notices count.
   *
   * @return array
   */
  public static function get_notices_count() {
    $notices = WD_SEO_Library::get_recommends_problems(TRUE);
    $recommends_count = 0;
    $problems_count = 0;

    if ( isset($notices['recommends']) && isset($notices['recommends']['plugins']) ) {
      $recommends_count = count($notices['recommends']['plugins']);
    }
    if ( isset($notices['problems']) ) {
      $problems_count = count($notices['problems']);
    }
    $notices_count = $recommends_count + $problems_count;

    return array(
      'recommends_count' => $recommends_count,
      'problems_count' => $problems_count,
      'count' => $notices_count,
    );
  }

  /**
   * Remove directory with its content.
   *
   * @param $path
   */
  public static function remove_directory( $path ) {
    if ( is_dir($path) ) {
      $del_folder = scandir($path);
      foreach ( $del_folder as $file ) {
        if ( $file != '.' and $file != '..' ) {
          self::remove_directory($path . '/' . $file);
        }
      }
      rmdir($path);
    }
    else {
      if ( file_exists($path) ) {
        unlink($path);
      }
    }
  }

  public static function generate_crawl_data( WP_REST_Request $request ) {
    if ( defined( 'TENWEB_INCLUDES_DIR' ) ) {
      include_once TENWEB_INCLUDES_DIR . '/class-tenweb-services.php';
      if (true === TenwebServices::manager_ready()) {
        $data = array();

        $data['domain_id'] = TenwebServices::get_domain_id();

        $crawl = new WD_SEO_CRAWL;
        $data['crawl_errors'] = $crawl->get_crawl_errors();

        $moz = new WD_SEO_MOZ;
        $moz_url_metrics = $moz->get_url_metrics();
        $data['moz_url_metrics'] = $moz_url_metrics;

        $devices = array('desktop', 'mobile', 'tablet');
        $countries = array('worldwide' => ''); // WD_SEO_Library::countries('worldwide');
        foreach ($devices as $device) {
          foreach ($countries as $key => $country) {
            $data['search_analytics'][$device][$key] = $crawl->search_analytics($device, false, $country, 0);
          }
        }

        // Create the response object
        $response = new WP_REST_Response($data);
        return $response;
      }
    }
  }

  /**
   * User manual and support forum links.
   *
   * @return string
   */
  public static function topic() {
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    $user_guide_link = 'https://help.10web.io/hc/en-us/articles/';
    $support_forum_link = 'https://wordpress.org/support/plugin/seo-by-10web';
    $support_icon = WD_SEO_URL . '/images/icons/support.png';
    $prefix = 'wdseo';
    switch ($page) {
      case 'wdseo_overview': {
        $help_text = 'configure initial settings of the plugin';
        $user_guide_link .= '360011836259-Introducing-SEO-by-10Web';
        break;
      }
      case 'wdseo_search_analytics': {
        $help_text = 'provides statistics taken from Google Search Console';
        $user_guide_link .= '360011836259-Introducing-SEO-by-10Web';
        break;
      }
      case 'wdseo_search_console': {
        $help_text = 'provides details about crawl errors taken from Google Search Console';
        $user_guide_link .= '360011836259-Introducing-SEO-by-10Web';
        break;
      }
      case 'wdseo_redirects': {
        $help_text = 'set redirections for your 404 pages. Visit <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a> and review the errors';
        $user_guide_link .= '4405953694482-How-to-use-the-redirect-functionality-in-SEO-by-10Web';
        break;
      }
      case 'wdseo_meta_info': {
        $help_text = 'manage and modify the meta information of your website';
        $user_guide_link .= '360011737060-How-to-Set-Meta-Robots';
        break;
      }
      case 'wdseo_sitemap': {
        $help_text = 'construct the sitemap for your website';
        $user_guide_link .= '360011938480-How-to-Generate-an-XML-Sitemap';
        break;
      }
      case 'wdseo_settings': {
        $help_text = 'configure general options';
        $user_guide_link .= '360011737460-How-to-Redirect-Attachment-URLs-to-the-Attachment-Itself';
        break;
      }
      //      case 'wdseo_knowledge_graph': {
      //        $help_text = 'configure knowledge graph';
      //        $user_guide_link .= 'knowledge_graph';
      //        break;
      //      }
      default: {
        return '';
        break;
      }
    }
    ob_start();
    ?>
    <style>
      .wd_topic {
        background-color: #ffffff;
        border: none;
        box-sizing: border-box;
        clear: both;
        color: #6e7990;
        font-size: 14px;
        font-weight: bold;
        line-height: 44px;
        padding: 0 0 0 15px;
        vertical-align: middle;
        width: 98%;
      }
      .wd_topic .wd_help_topic {
        float: left;
      }
      .wd_topic .wd_help_topic a {
        color: #0073aa;
      }
      .wd_topic .wd_help_topic a:hover {
        color: #00A0D2;
      }
      .wd_topic .wd_support {
        float: right;
        margin: 0 10px;
      }
      .wd_topic .wd_support img {
        vertical-align: middle;
      }
      .wd_topic .wd_support a {
        text-decoration: none;
        color: #6E7990;
      }
      .wd_topic .wd_pro {
        float: right;
        padding: 0;
      }
      .wd_topic .wd_pro a {
        border: none;
        box-shadow: none !important;
        text-decoration: none;
      }
      .wd_topic .wd_pro img {
        border: none;
        display: inline-block;
        vertical-align: middle;
      }
      .wd_topic .wd_pro a,
      .wd_topic .wd_pro a:active,
      .wd_topic .wd_pro a:visited,
      .wd_topic .wd_pro a:hover {
        background-color: #D8D8D8;
        color: #175c8b;
        display: inline-block;
        font-size: 11px;
        font-weight: bold;
        padding: 0 10px;
        vertical-align: middle;
      }
    </style>
    <div class="update-nag wd_topic">
      <?php
      if ($help_text) {
        ?>
        <span class="wd_help_topic">
          <?php echo sprintf(__('This section allows you to %s.', $prefix), $help_text); ?>
          <a target="_blank" href="<?php echo $user_guide_link; ?>">
            <?php _e('Read More in User Manual', $prefix); ?>
          </a>
        </span>
        <?php
      }
      if ( TRUE ) {
        ?>
        <span class="wd_support">
          <a target="_blank" href="<?php echo $support_forum_link; ?>">
            <img src="<?php echo $support_icon; ?>" />
            <?php _e('Support Forum', $prefix); ?>
          </a>
        </span>
        <?php
      }
      ?>
    </div>
    <?php
    echo ob_get_clean();
  }

  public static function countries($worldwide = '') {
    return array(
      $worldwide => __('Worldwide',  WD_SEO_PREFIX), // Default value.
      'ABW' => __('Aruba',  WD_SEO_PREFIX),
      'AFG' => __('Afghanistan',  WD_SEO_PREFIX),
      'AGO' => __('Angola',  WD_SEO_PREFIX),
      'AIA' => __('Anguilla',  WD_SEO_PREFIX),
      'ALA' => __('Åland Islands',  WD_SEO_PREFIX),
      'ALB' => __('Albania',  WD_SEO_PREFIX),
      'AND' => __('Andorra',  WD_SEO_PREFIX),
      'ARE' => __('United Arab Emirates',  WD_SEO_PREFIX),
      'ARG' => __('Argentina',  WD_SEO_PREFIX),
      'ARM' => __('Armenia',  WD_SEO_PREFIX),
      'ASM' => __('American Samoa',  WD_SEO_PREFIX),
      'ATA' => __('Antarctica',  WD_SEO_PREFIX),
      'ATF' => __('French Southern Territories',  WD_SEO_PREFIX),
      'ATG' => __('Antigua and Barbuda',  WD_SEO_PREFIX),
      'AUS' => __('Australia',  WD_SEO_PREFIX),
      'AUT' => __('Austria',  WD_SEO_PREFIX),
      'AZE' => __('Azerbaijan',  WD_SEO_PREFIX),
      'BDI' => __('Burundi',  WD_SEO_PREFIX),
      'BEL' => __('Belgium',  WD_SEO_PREFIX),
      'BEN' => __('Benin',  WD_SEO_PREFIX),
      'BES' => __('Bonaire, Sint Eustatius and Saba',  WD_SEO_PREFIX),
      'BFA' => __('Burkina Faso',  WD_SEO_PREFIX),
      'BGD' => __('Bangladesh',  WD_SEO_PREFIX),
      'BGR' => __('Bulgaria',  WD_SEO_PREFIX),
      'BHR' => __('Bahrain',  WD_SEO_PREFIX),
      'BHS' => __('Bahamas',  WD_SEO_PREFIX),
      'BIH' => __('Bosnia and Herzegovina',  WD_SEO_PREFIX),
      'BLM' => __('Saint Barthélemy',  WD_SEO_PREFIX),
      'BLR' => __('Belarus',  WD_SEO_PREFIX),
      'BLZ' => __('Belize',  WD_SEO_PREFIX),
      'BMU' => __('Bermuda',  WD_SEO_PREFIX),
      'BOL' => __('Bolivia, Plurinational State of',  WD_SEO_PREFIX),
      'BRA' => __('Brazil',  WD_SEO_PREFIX),
      'BRB' => __('Barbados',  WD_SEO_PREFIX),
      'BRN' => __('Brunei Darussalam',  WD_SEO_PREFIX),
      'BTN' => __('Bhutan',  WD_SEO_PREFIX),
      'BVT' => __('Bouvet Island',  WD_SEO_PREFIX),
      'BWA' => __('Botswana',  WD_SEO_PREFIX),
      'CAF' => __('Central African Republic',  WD_SEO_PREFIX),
      'CAN' => __('Canada',  WD_SEO_PREFIX),
      'CCK' => __('Cocos (Keeling) Islands',  WD_SEO_PREFIX),
      'CHE' => __('Switzerland',  WD_SEO_PREFIX),
      'CHL' => __('Chile',  WD_SEO_PREFIX),
      'CHN' => __('China',  WD_SEO_PREFIX),
      'CIV' => __('Côte d\'Ivoire',  WD_SEO_PREFIX),
      'CMR' => __('Cameroon',  WD_SEO_PREFIX),
      'COD' => __('Congo, the Democratic Republic of the',  WD_SEO_PREFIX),
      'COG' => __('Congo',  WD_SEO_PREFIX),
      'COK' => __('Cook Islands',  WD_SEO_PREFIX),
      'COL' => __('Colombia',  WD_SEO_PREFIX),
      'COM' => __('Comoros',  WD_SEO_PREFIX),
      'CPV' => __('Cape Verde',  WD_SEO_PREFIX),
      'CRI' => __('Costa Rica',  WD_SEO_PREFIX),
      'CUB' => __('Cuba',  WD_SEO_PREFIX),
      'CUW' => __('Curaçao',  WD_SEO_PREFIX),
      'CXR' => __('Christmas Island',  WD_SEO_PREFIX),
      'CYM' => __('Cayman Islands',  WD_SEO_PREFIX),
      'CYP' => __('Cyprus',  WD_SEO_PREFIX),
      'CZE' => __('Czech Republic',  WD_SEO_PREFIX),
      'DEU' => __('Germany',  WD_SEO_PREFIX),
      'DJI' => __('Djibouti',  WD_SEO_PREFIX),
      'DMA' => __('Dominica',  WD_SEO_PREFIX),
      'DNK' => __('Denmark',  WD_SEO_PREFIX),
      'DOM' => __('Dominican Republic',  WD_SEO_PREFIX),
      'DZA' => __('Algeria',  WD_SEO_PREFIX),
      'ECU' => __('Ecuador',  WD_SEO_PREFIX),
      'EGY' => __('Egypt',  WD_SEO_PREFIX),
      'ERI' => __('Eritrea',  WD_SEO_PREFIX),
      'ESH' => __('Western Sahara',  WD_SEO_PREFIX),
      'ESP' => __('Spain',  WD_SEO_PREFIX),
      'EST' => __('Estonia',  WD_SEO_PREFIX),
      'ETH' => __('Ethiopia',  WD_SEO_PREFIX),
      'FIN' => __('Finland',  WD_SEO_PREFIX),
      'FJI' => __('Fiji',  WD_SEO_PREFIX),
      'FLK' => __('Falkland Islands (Malvinas)',  WD_SEO_PREFIX),
      'FRA' => __('France',  WD_SEO_PREFIX),
      'FRO' => __('Faroe Islands',  WD_SEO_PREFIX),
      'FSM' => __('Micronesia, Federated States of',  WD_SEO_PREFIX),
      'GAB' => __('Gabon',  WD_SEO_PREFIX),
      'GBR' => __('United Kingdom',  WD_SEO_PREFIX),
      'GEO' => __('Georgia',  WD_SEO_PREFIX),
      'GGY' => __('Guernsey',  WD_SEO_PREFIX),
      'GHA' => __('Ghana',  WD_SEO_PREFIX),
      'GIB' => __('Gibraltar',  WD_SEO_PREFIX),
      'GIN' => __('Guinea',  WD_SEO_PREFIX),
      'GLP' => __('Guadeloupe',  WD_SEO_PREFIX),
      'GMB' => __('Gambia',  WD_SEO_PREFIX),
      'GNB' => __('Guinea-Bissau',  WD_SEO_PREFIX),
      'GNQ' => __('Equatorial Guinea',  WD_SEO_PREFIX),
      'GRC' => __('Greece',  WD_SEO_PREFIX),
      'GRD' => __('Grenada',  WD_SEO_PREFIX),
      'GRL' => __('Greenland',  WD_SEO_PREFIX),
      'GTM' => __('Guatemala',  WD_SEO_PREFIX),
      'GUF' => __('French Guiana',  WD_SEO_PREFIX),
      'GUM' => __('Guam',  WD_SEO_PREFIX),
      'GUY' => __('Guyana',  WD_SEO_PREFIX),
      'HKG' => __('Hong Kong',  WD_SEO_PREFIX),
      'HMD' => __('Heard Island and McDonald Islands',  WD_SEO_PREFIX),
      'HND' => __('Honduras',  WD_SEO_PREFIX),
      'HRV' => __('Croatia',  WD_SEO_PREFIX),
      'HTI' => __('Haiti',  WD_SEO_PREFIX),
      'HUN' => __('Hungary',  WD_SEO_PREFIX),
      'IDN' => __('Indonesia',  WD_SEO_PREFIX),
      'IMN' => __('Isle of Man',  WD_SEO_PREFIX),
      'IND' => __('India',  WD_SEO_PREFIX),
      'IOT' => __('British Indian Ocean Territory',  WD_SEO_PREFIX),
      'IRL' => __('Ireland',  WD_SEO_PREFIX),
      'IRN' => __('Iran, Islamic Republic of',  WD_SEO_PREFIX),
      'IRQ' => __('Iraq',  WD_SEO_PREFIX),
      'ISL' => __('Iceland',  WD_SEO_PREFIX),
      'ISR' => __('Israel',  WD_SEO_PREFIX),
      'ITA' => __('Italy',  WD_SEO_PREFIX),
      'JAM' => __('Jamaica',  WD_SEO_PREFIX),
      'JEY' => __('Jersey',  WD_SEO_PREFIX),
      'JOR' => __('Jordan',  WD_SEO_PREFIX),
      'JPN' => __('Japan',  WD_SEO_PREFIX),
      'KAZ' => __('Kazakhstan',  WD_SEO_PREFIX),
      'KEN' => __('Kenya',  WD_SEO_PREFIX),
      'KGZ' => __('Kyrgyzstan',  WD_SEO_PREFIX),
      'KHM' => __('Cambodia',  WD_SEO_PREFIX),
      'KIR' => __('Kiribati',  WD_SEO_PREFIX),
      'KNA' => __('Saint Kitts and Nevis',  WD_SEO_PREFIX),
      'KOR' => __('Korea, Republic of',  WD_SEO_PREFIX),
      'KWT' => __('Kuwait',  WD_SEO_PREFIX),
      'LAO' => __('Lao People\'s Democratic Republic',  WD_SEO_PREFIX),
      'LBN' => __('Lebanon',  WD_SEO_PREFIX),
      'LBR' => __('Liberia',  WD_SEO_PREFIX),
      'LBY' => __('Libya',  WD_SEO_PREFIX),
      'LCA' => __('Saint Lucia',  WD_SEO_PREFIX),
      'LIE' => __('Liechtenstein',  WD_SEO_PREFIX),
      'LKA' => __('Sri Lanka',  WD_SEO_PREFIX),
      'LSO' => __('Lesotho',  WD_SEO_PREFIX),
      'LTU' => __('Lithuania',  WD_SEO_PREFIX),
      'LUX' => __('Luxembourg',  WD_SEO_PREFIX),
      'LVA' => __('Latvia',  WD_SEO_PREFIX),
      'MAC' => __('Macao',  WD_SEO_PREFIX),
      'MAF' => __('Saint Martin (French part)',  WD_SEO_PREFIX),
      'MAR' => __('Morocco',  WD_SEO_PREFIX),
      'MCO' => __('Monaco',  WD_SEO_PREFIX),
      'MDA' => __('Moldova, Republic of',  WD_SEO_PREFIX),
      'MDG' => __('Madagascar',  WD_SEO_PREFIX),
      'MDV' => __('Maldives',  WD_SEO_PREFIX),
      'MEX' => __('Mexico',  WD_SEO_PREFIX),
      'MHL' => __('Marshall Islands',  WD_SEO_PREFIX),
      'MKD' => __('Macedonia, the former Yugoslav Republic of',  WD_SEO_PREFIX),
      'MLI' => __('Mali',  WD_SEO_PREFIX),
      'MLT' => __('Malta',  WD_SEO_PREFIX),
      'MMR' => __('Myanmar',  WD_SEO_PREFIX),
      'MNE' => __('Montenegro',  WD_SEO_PREFIX),
      'MNG' => __('Mongolia',  WD_SEO_PREFIX),
      'MNP' => __('Northern Mariana Islands',  WD_SEO_PREFIX),
      'MOZ' => __('Mozambique',  WD_SEO_PREFIX),
      'MRT' => __('Mauritania',  WD_SEO_PREFIX),
      'MSR' => __('Montserrat',  WD_SEO_PREFIX),
      'MTQ' => __('Martinique',  WD_SEO_PREFIX),
      'MUS' => __('Mauritius',  WD_SEO_PREFIX),
      'MWI' => __('Malawi',  WD_SEO_PREFIX),
      'MYS' => __('Malaysia',  WD_SEO_PREFIX),
      'MYT' => __('Mayotte',  WD_SEO_PREFIX),
      'NAM' => __('Namibia',  WD_SEO_PREFIX),
      'NCL' => __('New Caledonia',  WD_SEO_PREFIX),
      'NER' => __('Niger',  WD_SEO_PREFIX),
      'NFK' => __('Norfolk Island',  WD_SEO_PREFIX),
      'NGA' => __('Nigeria',  WD_SEO_PREFIX),
      'NIC' => __('Nicaragua',  WD_SEO_PREFIX),
      'NIU' => __('Niue',  WD_SEO_PREFIX),
      'NLD' => __('Netherlands',  WD_SEO_PREFIX),
      'NOR' => __('Norway',  WD_SEO_PREFIX),
      'NPL' => __('Nepal',  WD_SEO_PREFIX),
      'NRU' => __('Nauru',  WD_SEO_PREFIX),
      'NZL' => __('New Zealand',  WD_SEO_PREFIX),
      'OMN' => __('Oman',  WD_SEO_PREFIX),
      'PAK' => __('Pakistan',  WD_SEO_PREFIX),
      'PAN' => __('Panama',  WD_SEO_PREFIX),
      'PCN' => __('Pitcairn',  WD_SEO_PREFIX),
      'PER' => __('Peru',  WD_SEO_PREFIX),
      'PHL' => __('Philippines',  WD_SEO_PREFIX),
      'PLW' => __('Palau',  WD_SEO_PREFIX),
      'PNG' => __('Papua New Guinea',  WD_SEO_PREFIX),
      'POL' => __('Poland',  WD_SEO_PREFIX),
      'PRI' => __('Puerto Rico',  WD_SEO_PREFIX),
      'PRK' => __('Korea, Democratic People\'s Republic of',  WD_SEO_PREFIX),
      'PRT' => __('Portugal',  WD_SEO_PREFIX),
      'PRY' => __('Paraguay',  WD_SEO_PREFIX),
      'PSE' => __('Palestinian Territory, Occupied',  WD_SEO_PREFIX),
      'PYF' => __('French Polynesia',  WD_SEO_PREFIX),
      'QAT' => __('Qatar',  WD_SEO_PREFIX),
      'REU' => __('Réunion',  WD_SEO_PREFIX),
      'ROU' => __('Romania',  WD_SEO_PREFIX),
      'RUS' => __('Russian Federation',  WD_SEO_PREFIX),
      'RWA' => __('Rwanda',  WD_SEO_PREFIX),
      'SAU' => __('Saudi Arabia',  WD_SEO_PREFIX),
      'SDN' => __('Sudan',  WD_SEO_PREFIX),
      'SEN' => __('Senegal',  WD_SEO_PREFIX),
      'SGP' => __('Singapore',  WD_SEO_PREFIX),
      'SGS' => __('South Georgia and the South Sandwich Islands',  WD_SEO_PREFIX),
      'SHN' => __('Saint Helena, Ascension and Tristan da Cunha',  WD_SEO_PREFIX),
      'SJM' => __('Svalbard and Jan Mayen',  WD_SEO_PREFIX),
      'SLB' => __('Solomon Islands',  WD_SEO_PREFIX),
      'SLE' => __('Sierra Leone',  WD_SEO_PREFIX),
      'SLV' => __('El Salvador',  WD_SEO_PREFIX),
      'SMR' => __('San Marino',  WD_SEO_PREFIX),
      'SOM' => __('Somalia',  WD_SEO_PREFIX),
      'SPM' => __('Saint Pierre and Miquelon',  WD_SEO_PREFIX),
      'SRB' => __('Serbia',  WD_SEO_PREFIX),
      'SSD' => __('South Sudan',  WD_SEO_PREFIX),
      'STP' => __('Sao Tome and Principe',  WD_SEO_PREFIX),
      'SUR' => __('Suriname',  WD_SEO_PREFIX),
      'SVK' => __('Slovakia',  WD_SEO_PREFIX),
      'SVN' => __('Slovenia',  WD_SEO_PREFIX),
      'SWE' => __('Sweden',  WD_SEO_PREFIX),
      'SWZ' => __('Swaziland',  WD_SEO_PREFIX),
      'SXM' => __('Sint Maarten (Dutch part)',  WD_SEO_PREFIX),
      'SYC' => __('Seychelles',  WD_SEO_PREFIX),
      'SYR' => __('Syrian Arab Republic',  WD_SEO_PREFIX),
      'TCA' => __('Turks and Caicos Islands',  WD_SEO_PREFIX),
      'TCD' => __('Chad',  WD_SEO_PREFIX),
      'TGO' => __('Togo',  WD_SEO_PREFIX),
      'THA' => __('Thailand',  WD_SEO_PREFIX),
      'TJK' => __('Tajikistan',  WD_SEO_PREFIX),
      'TKL' => __('Tokelau',  WD_SEO_PREFIX),
      'TKM' => __('Turkmenistan',  WD_SEO_PREFIX),
      'TLS' => __('Timor-Leste',  WD_SEO_PREFIX),
      'TON' => __('Tonga',  WD_SEO_PREFIX),
      'TTO' => __('Trinidad and Tobago',  WD_SEO_PREFIX),
      'TUN' => __('Tunisia',  WD_SEO_PREFIX),
      'TUR' => __('Turkey',  WD_SEO_PREFIX),
      'TUV' => __('Tuvalu',  WD_SEO_PREFIX),
      'TWN' => __('Taiwan, Province of China',  WD_SEO_PREFIX),
      'TZA' => __('Tanzania, United Republic of',  WD_SEO_PREFIX),
      'UGA' => __('Uganda',  WD_SEO_PREFIX),
      'UKR' => __('Ukraine',  WD_SEO_PREFIX),
      'UMI' => __('United States Minor Outlying Islands',  WD_SEO_PREFIX),
      'URY' => __('Uruguay',  WD_SEO_PREFIX),
      'USA' => __('United States',  WD_SEO_PREFIX),
      'UZB' => __('Uzbekistan',  WD_SEO_PREFIX),
      'VAT' => __('Holy See (Vatican City State)',  WD_SEO_PREFIX),
      'VCT' => __('Saint Vincent and the Grenadines',  WD_SEO_PREFIX),
      'VEN' => __('Venezuela, Bolivarian Republic of',  WD_SEO_PREFIX),
      'VGB' => __('Virgin Islands, British',  WD_SEO_PREFIX),
      'VIR' => __('Virgin Islands, U.S.',  WD_SEO_PREFIX),
      'VNM' => __('Viet Nam',  WD_SEO_PREFIX),
      'VUT' => __('Vanuatu',  WD_SEO_PREFIX),
      'WLF' => __('Wallis and Futuna',  WD_SEO_PREFIX),
      'WSM' => __('Samoa',  WD_SEO_PREFIX),
      'YEM' => __('Yemen',  WD_SEO_PREFIX),
      'ZAF' => __('South Africa',  WD_SEO_PREFIX),
      'ZMB' => __('Zambia',  WD_SEO_PREFIX),
      'ZWE' => __('Zimbabwe',  WD_SEO_PREFIX),
    );
  }

  /**
   * Pro banner
   *
   * @param string $className
   */
  public static function pro_banner( $className = '' ) {
    ?>
    <div class="free_tooltip <?php echo $className; ?> hidden">
      <div>
        <div class="free_tooltip_text"><?php _e('Sign up for 10Web and get full access to SEO by 10Web premium for free.', WD_SEO_PREFIX) ?></div>
        <div class="free_tooltip_button">
          <a href="https://10web.io/wordpress-seo/" target="_blank" class="button"><?php _e('Sign Up', WD_SEO_PREFIX) ?></a>
        </div>
      </div>
    </div>
    <?php
  }

  /**
   * _redirect
   *
   * @param $url
   * @param $status
   *
   * @return void
   */
  public static function _redirect( $url, $status = FALSE ) {
    if ( FALSE === $status ) {
      $status = WDSeo()->options->get_redirect_status();
    }
    header('X-Redirect-By: ' . WD_SEO_NICENAME);
    wp_redirect($url, $status, WD_SEO_NICENAME);
    exit;
  }

  public static function check_ssl() {
    if (is_ssl()) {
      return 'https://';
    } else {
      return 'http://';
    }
  }

  /**
   * Get images existing in given html.
   *
   * @param object $post
   *
   * @param bool $xml
   * @return array|bool
   */
  public static function get_images_content( $post, $xml = true ) {
    $images = array();
    $matches = array();

    $post_thumbnail_image_tag = get_the_post_thumbnail($post);
    if ( $post_thumbnail_image_tag ) {
      array_push($matches, $post_thumbnail_image_tag);
    }

    preg_match_all("/(<img [^>]+?>)/", $post->post_content, $matches, PREG_OFFSET_CAPTURE);
    if ( isset($matches[0]) && !empty($matches[0]) && !$post_thumbnail_image_tag ) {
      $matches = $matches[0];
      foreach ($matches as $tmp) {
        $img = (is_array($tmp) && isset($tmp[0]) ? $tmp[0] : $tmp);
        $res = preg_match('/src=("|\')([^"\']+)("|\')/', $img, $match);
        $src = $res ? $match[2] : '';

        if (strpos($src, 'http') !== 0) {
          $src = site_url($src);
        }

        $res = preg_match('/title=("|\')([^"\']+)("|\')/', $img, $match);
        $title = $res ? str_replace('-', ' ', str_replace('_', ' ', $match[2])) : '';
        $res = preg_match('/alt=("|\')([^"\']+)("|\')/', $img, $match);
        $alt = $res ? str_replace('-', ' ', str_replace('_', ' ', $match[2])) : '';
        $images[] = array(
          'src' => $src,
          'title' => $title,
          'alt' => $alt,
        );
      }
    }
    if ( $xml ) {
      // Filter images to allow plugins add images to sitemap.
      $images = apply_filters('wd_seo_sitemap_images', $images, $post->ID);
    }

    return $images;
  }

  /**
   * Get WooCommerce pages.
   * @return array
   */
  public static function get_woocommerce_pages() {
    $pages = array(
      'cart' => wc_get_page_id('cart'),
      'checkout' => wc_get_page_id('checkout'),
      'myaccount' => wc_get_page_id('myaccount')
    );
    return $pages;
  }

  /**
   * Get WooCommerce robots.
   * @return string
   */
  public static function get_woocommerce_robots( $robots = '' ) {
    $options = new WD_SEO_Options();
    if ( WD_SEO_Library::woocommerce_active() ) {
      $woocommerce_pages = self::get_woocommerce_pages();
      if ( !empty($woocommerce_pages) ) {
        $id = get_the_id();
        $cart = !empty($woocommerce_pages['cart']) ? $woocommerce_pages['cart'] : 0;
        $checkout = !empty($woocommerce_pages['checkout']) ? $woocommerce_pages['checkout'] : 0;
        $myaccount = !empty($woocommerce_pages['myaccount']) ? $woocommerce_pages['myaccount'] : 0;
        if ( (!empty($cart) && $cart == $id && isset($options->woocommerce->cart_page_index) && $options->woocommerce->cart_page_index == 0)
          || (!empty($checkout) && $checkout == $id && isset($options->woocommerce->checkout_page_index) && $options->woocommerce->checkout_page_index == 0)
          || (!empty($myaccount) && $myaccount == $id && isset($options->woocommerce->customer_account_page_index) && $options->woocommerce->customer_account_page_index == 0)
        ) {
          $robots = preg_replace( array('/\bindex\b/i', '/\bnoindex\b/i'), 'noindex', $robots );
        }
      }
    }

    return $robots;
  }

  /**
   * Get Redirect types.
   *
   * @return array
   */
  public static function get_redirect_types() {
    $types = array(
      // '300' => __('300 Multiple Choices', WD_SEO_PREFIX),
      '301' => __('301 Moved Permanently', WD_SEO_PREFIX),
      '302' => __('302 Found (Previously "Moved temporarily")', WD_SEO_PREFIX),
      '303' => __('303 See Other', WD_SEO_PREFIX),
      // '304' => __('304 Not Modified', WD_SEO_PREFIX),
      // '305' => __('305 Use Proxy', WD_SEO_PREFIX),
      // '306' => __('306 Switch Proxy', WD_SEO_PREFIX),
      '307' => __('307 Temporary Redirect', WD_SEO_PREFIX),
      '308' => __('308 Permanent Redirect', WD_SEO_PREFIX),
    );

    return $types;
  }

  /**
   * Get Query parameter types.
   *
   * @return array
   */
  public static function get_query_parameter_types() {
    $types = array(
      'exact' => __('Exact match all parameters in any order', WD_SEO_PREFIX),
      'ignore' => __('Ignore all parameters', WD_SEO_PREFIX),
      'pass' => __('Ignore and pass parameters to the target', WD_SEO_PREFIX)
    );

    return $types;
  }

  /**
   * Get Client error types.
   *
   * @return array
   */
  public static function get_client_error_types() {
    $types = array(
      '400' => __('400 Bad Request', WD_SEO_PREFIX),
      '401' => __('401 Unauthorized', WD_SEO_PREFIX),
      '402' => __('402 Payment Required', WD_SEO_PREFIX),
      '403' => __('403 Forbidden', WD_SEO_PREFIX),
      '404' => __('404 Not Found', WD_SEO_PREFIX),
      '405' => __('405 Method Not Allowed', WD_SEO_PREFIX),
      '406' => __('406 Not Acceptable', WD_SEO_PREFIX),
      '407' => __('407 Proxy Authentication Required', WD_SEO_PREFIX),
      '408' => __('408 Request Timeout', WD_SEO_PREFIX),
      '409' => __('409 Conflict', WD_SEO_PREFIX),
      '410' => __('410 Gone', WD_SEO_PREFIX),
      '411' => __('411 Length Required', WD_SEO_PREFIX),
      '412' => __('412 Precondition Failed', WD_SEO_PREFIX),
      '413' => __('413 Payload Too Large', WD_SEO_PREFIX),
      '414' => __('414 URI Too Long', WD_SEO_PREFIX),
      '415' => __('415 Unsupported Media Type', WD_SEO_PREFIX),
      '416' => __('416 Range Not Satisfiable', WD_SEO_PREFIX),
      '417' => __('417 Expectation Failed', WD_SEO_PREFIX),
      '418' => __('418 I&prime;m a teapot', WD_SEO_PREFIX),
      '421' => __('421 Misdirected Request', WD_SEO_PREFIX),
      '422' => __('422 Unprocessable Entity', WD_SEO_PREFIX),
      '423' => __('423 Locked', WD_SEO_PREFIX),
      '424' => __('424 Failed Dependency', WD_SEO_PREFIX),
      '425' => __('425 Too Early', WD_SEO_PREFIX),
      '426' => __('426 Upgrade Required', WD_SEO_PREFIX),
      '428' => __('428 Precondition Required', WD_SEO_PREFIX),
      '429' => __('429 Too Many Requests', WD_SEO_PREFIX),
      '431' => __('431 Request Header Fields Too Large', WD_SEO_PREFIX),
      '451' => __('451 Unavailable For Legal Reasons', WD_SEO_PREFIX),
    );

    return $types;
  }

  /**
   * Change old redirect status.
   *
   * @param string $status
   *
   * @return int
   */
  public static function change_old_redirect_status( $status = '301' ) {
    if ( $status == '0' || $status == '1' || $status == '2' ) {
      if ( $status == '0' ) {
        $status = '302';
      }
      elseif ( $status == '2' ) {
        $status = '307';
      }
      else {
        $status = '301';
      }
    }

    return $status;
  }

  /**
   * Custom redirects functionality.
   *
   * @param array $args
   */
  public static function custom_redirects_init( $args = array() ) {
    global $wpdb;
    $tbl = $wpdb->prefix . WD_SEO_PREFIX . '_redirects';
    $site_url = $args['site_url'];
    $current_url = $args['current_url'];
    $redirects = $wpdb->get_results('SELECT * FROM ' . $tbl . ' WHERE `enable` = 1');
    $redirect_types = WD_SEO_Library::get_redirect_types();
    $client_error_types = WD_SEO_Library::get_client_error_types();
    if ( !empty($redirects) ) {
      function compare_urls($current_url,$site_url,$sub_url,$url,$url_query_params_array,$redirect,$current_url_query_params_array) {
        if($current_url[strlen($current_url)-1] == '/') {
          $curr_url_last_char_slash = True;
        } else {
          $curr_url_last_char_slash = False;
        };
        if($sub_url[strlen($sub_url)-1] == '/') {
          $sub_url_last_char_slash = True;
        } else {
          $sub_url_last_char_slash = False;
        };
        return (
            add_query_arg($current_url_query_params_array, $current_url) === // curr url and site url
            $site_url . add_query_arg($url_query_params_array, $sub_url) ||
            add_query_arg($current_url_query_params_array, $current_url)  ===
            add_query_arg($url_query_params_array, $sub_url) ||
            $redirect->slash == 1 && // ignore slash
            add_query_arg($current_url_query_params_array, $current_url.($curr_url_last_char_slash ? '':'/')) ===
            $site_url . add_query_arg($url_query_params_array, $sub_url.($sub_url_last_char_slash ? '':'/')) ||
            $redirect->slash == 1 &&
            add_query_arg($current_url_query_params_array, $current_url.($curr_url_last_char_slash ? '':'/'))  ===
            add_query_arg($url_query_params_array, $sub_url.($sub_url_last_char_slash ? '':'/')) ||
            $redirect->case == 1 && strcasecmp(add_query_arg($current_url_query_params_array, $current_url), $site_url . add_query_arg($url_query_params_array, $sub_url)) === 0 || // ignore case
            $redirect->case == 1 && strcasecmp(add_query_arg($current_url_query_params_array, $current_url), add_query_arg($url_query_params_array, $sub_url)) === 0) ||
          $redirect->case == 1 &&
          $redirect ->slash == 1 && // ignore slash and case
          strcasecmp(add_query_arg($current_url_query_params_array, $current_url.($curr_url_last_char_slash ? '':'/')) , $site_url . add_query_arg($url_query_params_array, $sub_url.($sub_url_last_char_slash ? '':'/'))) === 0 ||
          $redirect->case == 1 &&
          $redirect ->slash == 1 &&
          strcasecmp(add_query_arg($current_url_query_params_array, $current_url.($curr_url_last_char_slash ? '':'/')) , add_query_arg($url_query_params_array, $sub_url.($sub_url_last_char_slash ? '':'/'))) === 0
          ;
      }

      function custome_url_param_parse( $str ) {
        $params_str = explode('&', $str);
        $return = array();
        foreach ( $params_str as $param_value_pair ) {
          $param = explode("=", $param_value_pair);
          if ( isset($param[0]) && isset($param[1]) ) {
            $return[$param[0]] = $param[1];
          }
        }

        return $return;
      }
      foreach ( $redirects as $redirect ) {
        $id = $redirect->id;
        $url = $redirect->url; // page to redirect
        $redirect_url = $redirect->redirect_url;
        $redirect_type = $redirect->redirect_type;
        if ($redirect->regex == 0) {
          if ( $redirect->query_parameters == 'exact' || $redirect->query_parameters == '') { // for exact query parameter and client_error_types
            $question_mark_pos = strpos($current_url, '?');
            if ( $question_mark_pos != FALSE ) {
              $sub_current_url = substr($current_url, 0, $question_mark_pos); //site url without parameters
              $url = htmlspecialchars_decode($url); //decode url
              $url_question_mark_pos = strpos($url, '?');
              $current_url_question_mark_pos = strpos($current_url, '?');
              if($url_question_mark_pos != FALSE) {
                $sub_url = substr($url, 0, $url_question_mark_pos); //site url without parameters
                $url_query_params = substr($url, $url_question_mark_pos+1);
                $url_query_params_array = custome_url_param_parse($url_query_params);
              } else {
                $sub_url = $url;
                $url_query_params_array = array();
              }
              if($current_url_question_mark_pos != FALSE) {
                $current_url_query_params = substr($current_url, $current_url_question_mark_pos+1);
                $current_url_query_params_array = custome_url_param_parse($current_url_query_params);
              } else {
                $current_url_query_params_array = array();
              }
              $arraysAreEqual = ($current_url_query_params_array == $url_query_params_array);
              if ($arraysAreEqual) {
                $current_url = add_query_arg($url_query_params_array, $sub_current_url);
              }
              $should_redirect = compare_urls($sub_current_url, $site_url, $sub_url, $url, $url_query_params_array, $redirect,$current_url_query_params_array);
            }
            else {
              $sub_current_url = $current_url;
              $url = htmlspecialchars_decode($url); //decode url
              $url_question_mark_pos = strpos($url, '?');
              if($url_question_mark_pos != FALSE) {
                $sub_url = substr($url, 0, $url_question_mark_pos); //site url without parameters
                $url_query_params = substr($url, $url_question_mark_pos+1);
                $url_query_params_array = custome_url_param_parse($url_query_params);
              } else {
                $sub_url = $url;
                $url_query_params_array = array();
              }
              $current_url_query_params_array = array();
              $should_redirect = compare_urls($sub_current_url, $site_url, $sub_url, $url, $url_query_params_array, $redirect,$current_url_query_params_array);
            }
          }
          elseif ( $redirect->query_parameters == 'ignore' ) {
            $question_mark_pos = strpos($current_url, '?');
            if ( $question_mark_pos != FALSE ) {
              $sub_current_url = substr($current_url, 0, $question_mark_pos);
              $url = htmlspecialchars_decode($url); //decode url
              $url_question_mark_pos = strpos($url, '?');
              $current_url_question_mark_pos = strpos($current_url, '?');
              if($url_question_mark_pos != FALSE) {
                $sub_url = substr($url, 0, $url_question_mark_pos); //site url without parameters
                $url_query_params = substr($url, $url_question_mark_pos+1);
                $url_query_params_array = custome_url_param_parse($url_query_params);
              } else {
                $sub_url = $url;
                $url_query_params_array = array();
              }
              if($current_url_question_mark_pos != FALSE) {
                $current_url_query_params = substr($current_url, $current_url_question_mark_pos+1);
                $current_url_query_params_array = custome_url_param_parse($current_url_query_params);
              } else {
                $current_url_query_params_array = array();
              }
              if(count(array_intersect($current_url_query_params_array, $url_query_params_array)) === count($url_query_params_array)) {
                $areUrlParamArrIsSubOfCurrUrlParamArr = TRUE;
                $url_query_params_array = array();
              } else {
                $areUrlParamArrIsSubOfCurrUrlParamArr = FALSE;
              }
              if ($areUrlParamArrIsSubOfCurrUrlParamArr) {
                $should_redirect = compare_urls($sub_current_url,$site_url,$sub_url,$sub_url,$current_url_query_params_array,$redirect,$current_url_query_params_array);
              } else {
                $should_redirect = FALSE;
              }
            }
            else {
              $sub_url = $url;
              $url_query_params_array = array();
              $current_url_query_params_array = array();
              $sub_current_url = $current_url;
              $should_redirect = compare_urls($sub_current_url,$site_url,$sub_url,$sub_url,$url_query_params_array,$redirect,$current_url_query_params_array);
            }
          }
          elseif ( $redirect->query_parameters == 'pass' ) {
            $question_mark_pos = strpos($current_url, '?');
            if ( $question_mark_pos != FALSE ) {
              $sub_current_url = substr($current_url, 0, $question_mark_pos);
              $url = htmlspecialchars_decode($url); //decode url
              $url_question_mark_pos = strpos($url, '?');
              $current_url_question_mark_pos = strpos($current_url, '?');
              if($url_question_mark_pos != FALSE) {
                $sub_url = substr($url, 0, $url_question_mark_pos); //site url without parameters
                $url_query_params = substr($url, $url_question_mark_pos+1);
                $url_query_params_array = custome_url_param_parse($url_query_params);
              } else {
                $sub_url = $url;
                $url_query_params_array = array();
              }
              if($current_url_question_mark_pos != FALSE) {
                $current_url_query_params = substr($current_url, $current_url_question_mark_pos+1);
                $current_url_query_params_array = custome_url_param_parse($current_url_query_params);
              } else {
                $current_url_query_params_array = array();
              }
              if(count(array_intersect($current_url_query_params_array, $url_query_params_array)) === count($url_query_params_array)) {
                $areUrlParamArrIsSubOfCurrUrlParamArr = TRUE;
                $url_query_params_array = array();
              } else {
                $areUrlParamArrIsSubOfCurrUrlParamArr = FALSE;
              }
              if ($areUrlParamArrIsSubOfCurrUrlParamArr) {
                $should_redirect = compare_urls($sub_current_url,$site_url,$sub_url,$sub_url,$current_url_query_params_array,$redirect,$current_url_query_params_array);
              } else {
                $should_redirect = FALSE;
              }
              $redirect_url = add_query_arg($current_url_query_params_array,$redirect_url);
            }
            else {
              $sub_url = $url;
              $url_query_params_array = array();
              $current_url_question_mark_pos = strpos($current_url, '?');
              if($current_url_question_mark_pos != FALSE) {
                $current_url_query_params = substr($current_url, $current_url_question_mark_pos+1);
                $current_url_query_params_array = custome_url_param_parse($current_url_query_params);
              } else {
                $current_url_query_params_array = array();
              }
              $sub_current_url = $current_url;
              $should_redirect = compare_urls($sub_current_url,$site_url,$sub_url,$sub_url,$url_query_params_array,$redirect,$current_url_query_params_array);
              $redirect_url = add_query_arg($current_url_query_params_array,$redirect_url);
            }
          }
        } else { // regex
          if (strpos($url, $site_url) === FALSE) {
            $url = $site_url . $url;
          }
          $new_url = '/'.str_replace("/","\\/",$url) . ($redirect->slash == 1?'\\/?':'') .'/'. ($redirect->case == 1?"i":'');
          $redirect_url = preg_replace($new_url, $redirect_url, $current_url);
          $should_redirect = preg_match($new_url,$current_url);
        }
        if ($redirect_url !== $url) {
          if ( !empty($redirect_url) && $should_redirect && in_array($redirect_type, array_keys($redirect_types)) ) {
            // adding count every redirect.
            $wpdb->query('UPDATE ' . $tbl . ' SET `count` = `count` + 1 WHERE `id` = ' . $id);
            WD_SEO_Library::_redirect($redirect_url, $redirect_type);
          }
          elseif ( !empty($url) && $should_redirect && in_array($redirect_type, array_keys($client_error_types)) ) {
            $wpdb->query('UPDATE ' . $tbl . ' SET `count` = `count` + 1 WHERE `id` = ' . $id);
            $header_str = $client_error_types[$redirect_type];
            header("HTTP/1.0 $header_str", TRUE, $redirect_type);
            exit();
          }
        }
      }
    }
  }

  /**
   * WooCommerce plugin is active.
   * @return bool
   */
  public static function woocommerce_active() {
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Set faqs json LD.
   * @param array $args
   *
   * @return array
   */
  public static function set_faqs_jsonld( $args = array() ) {
    $data = array();
    if ( !empty($args['faqs']) ) {
      $data = array(
        '@context' => 'http://schema.org',
        '@type' => 'FAQPage',
      );
      foreach ( $args['faqs'] as $faq ) {
        $question[] = array(
          '@type' => 'Question',
          'name' => $faq['question'],
          'acceptedAnswer' => array(
            '@type' => 'Answer',
            'text' => $faq['answer'],
          ),
        );
      }
      $data['mainEntity'] = $question;
    }
    return $data;
  }

  /**
   * Get faqs json LD.
   * @param array $args
   *
   * @return string
   */
  public static function get_faqs_jsonld( $args = array() ) {
    $faqs = self::set_faqs_jsonld($args);
    if ( !empty($faqs) ) {
      return '<script type="application/ld+json">' . json_encode($faqs) . '</script>';
    }
  }

  /**
   * Get additional page priorities.
   * @return array
   */
  public static function get_additional_page_priorities() {
    return array( '0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1' );
  }

  /**
   * Get additional page frequencies.
   * @return array
   */
  public static function get_additional_page_frequencies() {
    return array( 'Always', 'Hourly', 'Daily', 'Weekly', 'Monthly', 'Yearly', 'Never' );
  }
}
