<?php

class WDSeo_Site {
  /**
   * Options instance.
   *
   * @var WD_SEO_Options
   */
  public $options = null;

  /**
   * Placeholders.
   *
   * @array
   */
  public $placeholders = array();

  /**
   * @var null
   */
  public $object = null;
  public $canonical = null;

  /**
   * WDSeo_Site constructor.
   */
  public function __construct() {
    $this->options = new WD_SEO_Options();
    $this->init();
  }

  /**
   * WDSeo_Site init.
   */
  private function init() {
    $this->og_type = 'article';
    $this->published_date = '';
    $this->modified_date = '';
    // The sequence is very important as Category can be identified as archive
    // if is_archive() will be checked before is_category().
    if ( is_front_page() ) {
      // Homepage displays latest posts or Homepage as a static page.
      $type = 'home';
      $this->og_type = 'website';
      $object = $this->options->metas->home;
      if ( is_front_page() ) {
        //  Homepage as a static page.
        $queried_object = get_queried_object();
        if ( isset($queried_object->ID) ) {
          $object_temp = new WD_SEO_Postmeta($queried_object->ID, 'post', 'site_values');
          $object->canonical_url = $object_temp->canonical_url ? $object_temp->canonical_url : $object->canonical_url;
          $object->redirect_url = $object_temp->redirect_url;
        }
      }
    }
    elseif ( is_home() ) {
      // Blog page.
      $type = 'post';
      $object = $this->options->metas->home;
      if ( 'page' == get_option('show_on_front') ) {
        //  Homepage as a static page.
        $queried_object = get_queried_object();
        if ( isset($queried_object->ID) ) {
          $object_temp = new WD_SEO_Postmeta($queried_object->ID, 'post', 'site_values');
          foreach ($object_temp as $name => $value) {
            if ( !empty($object_temp->$name) ) {
              $object->$name = $value;
            }
          }
        }
      }
    }
    else {
      if (is_category() || is_tag() || is_tax()) {
        //taxonomy
        $type = 'taxonomy';
        $queried_object = get_queried_object();
        $object = new WD_SEO_Postmeta($queried_object->term_id, $queried_object->taxonomy, 'site_values');
      }
      else {
        if (is_search()) {
          //search
          $type = 'search';
          $object = $this->options->metas->search;
        }
        else {
          if (is_author()) {
            //author archive
            $type = 'author_archive';
            $object = $this->options->metas->author_archive;
          }
          else {
            if (is_archive()) {
              //date archive
              $type = 'date_archive';
              $object = $this->options->metas->date_archive;
            }
            else {
              if (is_404()) {
                //404
                $type = '404';
                $object = $this->options->metas->{404};
              }
              else {
                if (is_singular()) {
                  //post
                  $type = 'post';
                  $this->published_date = get_the_date( DATE_W3C );
                  $this->modified_date = get_the_modified_date( DATE_W3C );
                  $queried_object = get_queried_object();
                  $object = new WD_SEO_Postmeta($queried_object->ID, 'post', 'site_values');
                }
                else {
                  return;
                }
              }
            }
          }
        }
      }
    }

    if (isset($object->redirect_url) && $object->redirect_url) {
      $status = $this->options->get_redirect_status($object->redirections);
      WD_SEO_Library::_redirect( $object->redirect_url, $status );
    }

    if ($this->options->meta && isset($object->metabox) && $object->metabox) {
      $this->placeholders = WD_SEO_Library::get_placeholders(true);
      $this->object = $object;
      $this->type = $type;

      add_action('wp_head', array($this, 'head'), 10, 1);
      add_filter('pre_get_document_title', array($this, 'title'), 15);
      add_filter('wp_title', array($this, 'title'), 10, 3);
      // Remove wordpress action, that we're going to replace.
      remove_action('wp_head', 'rel_canonical');
      if ('0' != get_option( 'blog_public' )) {
        remove_action('wp_head', 'noindex', 1);
      }
    }
    $this->woocommerce_init();
  }

  public function woocommerce_init() {
    if ( WD_SEO_Library::woocommerce_active() ) {
      remove_action( 'wp_head', 'wc_page_noindex' );
      add_action('get_header', array('WD_SEO_WooCommerce', 'meta_generator_remove'), 10, 1);
    }
  }

  public function head() {
    $this->canonical();
    $this->meta_description();
    $this->robots();
    $this->meta_keywords();
    $this->meta_opengraph();
    $this->meta_woocommerce();
    $this->meta_twitter();
    $this->meta_pagination();
    $this->faqs_jsonld();
    $this->accounts_jsonld_hook();
    // Verification codes.
    $this->webmaster_tools_authentication();
  }

  /**
   * Knowledge Graph.
   */
  public function accounts_jsonld_hook() {
    if( $this->options->knowledge_check ) {
      $wdseo_comma_array = array();
      $wdseo_knowledge = $this->options->knowledge;
      //If enable (!=none)
      if ( isset($wdseo_knowledge->knowledge_type) && $wdseo_knowledge->knowledge_type != 'none') {
        if ($wdseo_knowledge->knowledge_fb !='') {
          $knowledge_fb = json_encode( $wdseo_knowledge->knowledge_fb );
          array_push($wdseo_comma_array, $knowledge_fb);
        }
        if ($wdseo_knowledge->knowledge_tw !='') {
          $knowledge_tw = json_encode('https://twitter.com/'.$wdseo_knowledge->knowledge_tw);
          array_push($wdseo_comma_array, $knowledge_tw);
        }
        if ($wdseo_knowledge->knowledge_pin !='') {
          $knowledge_pin = json_encode($wdseo_knowledge->knowledge_pin);
          array_push($wdseo_comma_array, $knowledge_pin);
        }
        if ($wdseo_knowledge->knowledge_insta !='') {
          $knowledge_insta = json_encode($wdseo_knowledge->knowledge_insta);
          array_push($wdseo_comma_array, $knowledge_insta);
        }
        if ($wdseo_knowledge->knowledge_yt !='') {
          $knowledge_yt = json_encode($wdseo_knowledge->knowledge_yt);
          array_push($wdseo_comma_array, $knowledge_yt);
        }
        if ($wdseo_knowledge->knowledge_li !='') {
          $knowledge_li = json_encode($wdseo_knowledge->knowledge_li);
          array_push($wdseo_comma_array, $knowledge_li);
        }
        if ($wdseo_knowledge->knowledge_sound !='') {
          $knowledge_sound = json_encode($wdseo_knowledge->knowledge_sound);
          array_push($wdseo_comma_array, $knowledge_sound);
        }
        if ($wdseo_knowledge->knowledge_tu !='') {
          $knowledge_tu = json_encode($wdseo_knowledge->knowledge_tu);
          array_push($wdseo_comma_array, $knowledge_tu);
        }
        if ($wdseo_knowledge->knowledge_type !='') {
          $knowledge_type = json_encode($wdseo_knowledge->knowledge_type);
        } else {
          $knowledge_type = json_encode('Organization');
        }
        if ($wdseo_knowledge->knowledge_name !='' && $wdseo_knowledge->knowledge_type !='none') {
          $knowledge_name = json_encode($wdseo_knowledge->knowledge_name);
        } elseif ($knowledge_type !='none') {
          $knowledge_name = json_encode(get_bloginfo('name'));
        }
        if ($wdseo_knowledge->knowledge_img !='' && $wdseo_knowledge->knowledge_type =='Organization') {
          $knowledge_img = json_encode($wdseo_knowledge->knowledge_img);
        }
        if ($wdseo_knowledge->knowledge_phone !='') {
          $knowledge_phone = json_encode($wdseo_knowledge->knowledge_phone);
        }
        if ($wdseo_knowledge->knowledge_contact_type !='') {
          $knowledge_contact_type = json_encode($wdseo_knowledge->knowledge_contact_type);
        }
        if ($wdseo_knowledge->knowledge_contact_option !='') {
          $knowledge_contact_option = json_encode($wdseo_knowledge->knowledge_contact_option);
        }

        $html = '<script type="application/ld+json">';
        $html .= '{"@context" : "' . WD_SEO_Library::check_ssl() . 'schema.org","@type" : ' . $knowledge_type . ',';
        if ($wdseo_knowledge->knowledge_img !='' && $wdseo_knowledge->knowledge_type =='Organization') {
          $html .= '"logo": '.$knowledge_img.',';
        }
        $html .= '"name" : '.$knowledge_name.',"url" : '.json_encode(get_home_url());

        if ($wdseo_knowledge->knowledge_type =='Organization'
          && $wdseo_knowledge->knowledge_phone !=''
          && $wdseo_knowledge->knowledge_contact_type !=''
        ) {
          if ($knowledge_phone && $knowledge_contact_type ) {
            $html .= ',"contactPoint": [{
					"@type": "ContactPoint",
					"telephone": '.$knowledge_phone.',';
            if ($knowledge_contact_option !='' && $knowledge_contact_option !='None') {
              $html .= '"contactOption": '.$knowledge_contact_option.',';
            }
            $html .= '"contactType": '.$knowledge_contact_type.'
				}]';
          }
        }

        if ($wdseo_knowledge->knowledge_fb !='' || $wdseo_knowledge->knowledge_tw !='' ||  $wdseo_knowledge->knowledge_pin !='' || $wdseo_knowledge->knowledge_insta !='' || $wdseo_knowledge->knowledge_yt !='' || $wdseo_knowledge->knowledge_li !='' || $wdseo_knowledge->knowledge_sound !='' || $wdseo_knowledge->knowledge_tu !='' ) {
          $html .= ',"sameAs" : [';
          $wdseo_comma_count = count($wdseo_comma_array);
          for ($i = 0; $i < $wdseo_comma_count; $i++) {
            $html .= $wdseo_comma_array[$i];
            if ($i < ($wdseo_comma_count - 1)) {
              $html .= ', ';
            }
          }
          $html .= ']';
        }
        $html .= '}';
        $html .= '</script>';
        $html .= "\n";

        $html = apply_filters('wdseo_schemas_organization_html', $html);
        echo $html;
      }
    }
  }

  /**
   * Faqs json LD.
   */
  public function faqs_jsonld() {
    if ( !empty($this->object->faqs) ) {
      $jsonld = WD_SEO_Library::get_faqs_jsonld( array('faqs' => $this->object->faqs) );
      echo $jsonld;
    }
  }

  public function meta_pagination() {
    if( isset($this->object->meta_pagination) && $this->object->meta_pagination ) {
      $rel_links = new WDRel_Links();
      $rel_links->adjacent_rel_links();
    }
  }

  /**
   * Output Search engines verification codes to front page.
   */
  public function webmaster_tools_authentication() {
    // Get Google verification code when authorizing with Google.
    if ( $this->options->google_site_verification ) {
      echo $this->options->google_site_verification;
    }
    // Bing.
    if ($this->options->bing_verification) {
      echo '<meta name="msvalidate.01" content="' . esc_attr($this->options->bing_verification) . '" />' . "\n";
    }
    // Yandex.
    if ($this->options->yandex_verification) {
      echo '<meta name="yandex-verification" content="' . esc_attr($this->options->yandex_verification) . '" />' . "\n";
    }
  }

  /**
   * Output canonical url.
   */
  public function canonical( $echo = true, $un_paged = false ) {
    if (isset($this->object->canonical_url) && $this->object->canonical_url) {
      $canonical = $this->object->canonical_url;
    }
    else {
      switch ($this->type) {
        case 'home': {
          global $wp;
          $canonical = home_url(add_query_arg( !isset($wp->request) && isset($wp->query_vars) ? $wp->query_vars : array(), $wp->request ));
          break;
        }
        case 'taxonomy': {
          $queried_object = get_queried_object();
          $canonical = get_term_link($queried_object, $queried_object->taxonomy);
          break;
        }
        case 'search': {
          $canonical = get_search_link();
          break;
        }
        case 'author_archive': {
          $canonical = get_author_posts_url(get_query_var('author'), get_query_var('author_name'));
          break;
        }
        case 'date_archive': {
          if (is_date()) {
            if (is_day()) {
              $canonical = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
            }
            elseif (is_month()) {
              $canonical = get_month_link(get_query_var('year'), get_query_var('monthnum'));
            }
            elseif (is_year()) {
              $canonical = get_year_link(get_query_var('year'));
            }
            break;
          }
        }
        default: {
          $queried_object = get_queried_object();
          $canonical = isset($queried_object->ID) ? get_permalink($queried_object->ID) : '';
          break;
        }
      }
      if ( $un_paged ) {
        return $canonical;
      }
      if ( $canonical && get_query_var( 'paged' ) > 1 ) {
            global $wp_rewrite;
            if ( ! $wp_rewrite->using_permalinks() ) {
                if ( $this->type == 'home' ) {
                    $canonical = trailingslashit( $canonical );
                }
                $canonical = add_query_arg( 'paged', get_query_var( 'paged' ), $canonical );
            }
            else {
                if ( $this->type == 'home' ) {
                    $canonical = WDRel_Links::get_base_url( '' );
                }
                $canonical = user_trailingslashit( trailingslashit( $canonical ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var( 'paged' ) );
            }
      }
    }

    if (!empty($canonical) && $echo) {
      echo '<link rel="canonical" href="' . esc_attr($canonical) . '" />' . "\n";
      $this->canonical = $canonical;
    }
    $this->canonical = $canonical;
  }

  /**
   * Output title.
   *
   * @param $title
   * @return string|void
   */
  public function title($title) {
    if (!empty($this->object->meta_title)) {
      $meta_title = WD_SEO_Library::replace_placeholders($this->object->meta_title, $this->placeholders);
      if (!empty($meta_title)) {
        return esc_attr(strip_tags($meta_title));
      }
    }
    return $title;
  }

  /**
   * Output meta description.
   */
  public function meta_description() {
    if (!empty($this->object->meta_description)) {
      $meta_description = WD_SEO_Library::replace_placeholders($this->object->meta_description, $this->placeholders);
      if (!empty($meta_description)) {
        echo '<meta name="description" content="' . esc_attr(strip_tags($meta_description)) . '" />' . "\n";
      }
    }
  }

  /**
   * Output meta robots.
   */
  public function robots() {
    $robots = '';
    if (isset($this->object->index)) {
      $robots = $this->object->index ? 'index,' : 'noindex,';
    }
    if (isset($this->object->follow)) {
      $robots .= $this->object->follow ? 'follow,' : 'nofollow,';
    }
    if (isset($this->object->robots_advanced)) {
      $robots_advanced = ltrim(implode(',', $this->object->robots_advanced), '0,');
      $robots .= $robots_advanced;
    }
    $robots = rtrim($robots, ',');
    $robots = WD_SEO_Library::get_woocommerce_robots($robots);
    if ( 'index,follow' != $robots ) {
      echo '<meta name="robots" content="' . esc_attr($robots) . '"/>' . "\n";
    }
  }

  /**
   * Output meta keywords.
   */
  public function meta_keywords() {
    if (!empty($this->object->meta_keywords) && is_array($this->object->meta_keywords)) {
      $meta_keywords = implode(',', $this->object->meta_keywords);
      if (!empty($meta_keywords)) {
        echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '" />' . "\n";
      }
    }
  }

  /**
   * Output open graph meta.
   */
  public function meta_opengraph() {
    $opengraph_title = '';
    if (!empty($this->object->opengraph_title)) {
      $opengraph_title = WD_SEO_Library::replace_placeholders($this->object->opengraph_title, $this->placeholders);
      if (!empty($opengraph_title)) {
        echo '<meta property="og:title" content="' . esc_attr($opengraph_title) . '" />' . "\n";
      }
    }
    if (!empty($this->canonical)) {
      echo '<meta property="og:url" content="' . esc_attr($this->canonical) . '" />' . "\n";
    }
    if (!empty($this->og_type)) {
      echo '<meta property="og:type" content="' . esc_attr($this->og_type) . '" />' . "\n";
    }
    if (isset($this->object->date) && $this->object->date) {
      if ( !empty( $this->published_date ) ) {
        echo '<meta property="article:published_time" content="' . esc_attr( $this->published_date ) . '" />' . "\n";
      }
      if ( !empty( $this->modified_date ) && $this->published_date != $this->modified_date ) {
        echo '<meta property="article:modified_time" content="' . esc_attr( $this->modified_date ) . '" />' . "\n";
        echo '<meta property="og:updated_time" content="' . esc_attr( $this->modified_date ) . '" />' . "\n";
      }
    }
    if (!empty($this->object->opengraph_description)) {
      $opengraph_description = WD_SEO_Library::replace_placeholders($this->object->opengraph_description, $this->placeholders);
      if (!empty($opengraph_description)) {
        echo '<meta property="og:description" content="' . esc_attr($opengraph_description) . '" />' . "\n";
      }
    }
    if (!empty($this->object->opengraph_images)) {
      $attachment_ids = explode(',', $this->object->opengraph_images);
      if ( !empty($attachment_ids) ) {
        foreach ($attachment_ids as $id) {
          $image = wp_get_attachment_image_src($id, 'original');
          if ( !empty($image) ) {
            echo '<meta property="og:image" content="' . esc_attr($image[0]) . '" />' . "\n";
            echo '<meta property="og:image:width" content="' . esc_attr($image[1]) . '" />' . "\n";
            echo '<meta property="og:image:height" content="' . esc_attr($image[2]) . '" />' . "\n";
            $image_alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
            if (empty($image_alt) && !empty($opengraph_title)) {
              $image_alt = $opengraph_title;
            }
            if (!empty($image_alt)) {
              echo '<meta property="og:image:alt" content="' . esc_attr($image_alt) . '" />' . "\n";
            }
          }
        }
      }
    }
    else {
      // set og:image on page content.
      $post = get_post( get_the_ID() );
      if ( $post ) {
        $images = WD_SEO_Library::get_images_content($post, false);
        if (!empty($images[0])) {
          $image = $images[0];
          $src = esc_attr($image['src']);

          $headers = @get_headers($image['src']);

          // Condition to check the existence of URL
          if( $headers && strpos( $headers[0], '200') ) {
            list($width, $height) = getimagesize($image['src']);
            $alt = esc_attr($image['alt']);
            echo '<meta property="og:image" content="' . $src . '" />' . "\n";
            echo '<meta property="og:image:width" content="' . $width . '" />' . "\n";
            echo '<meta property="og:image:height" content="' . $height . '" />' . "\n";
          }

          if (!empty($image['alt'])) {
            echo '<meta property="og:image:alt" content="' . $alt . '" />' . "\n";
          }
        }
      }
    }
  }

  public function meta_woocommerce() {
    if ( WD_SEO_Library::woocommerce_active() ) {
       add_action('wp_head', array('WD_SEO_WooCommerce', 'meta_opengraph'), 11, 1);
    }
  }

  /**
   * Output open graph meta.
   */
  public function meta_twitter() {
    if (isset($this->object->use_og_for_twitter)) {
      echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
      $opengraph_title = $this->object->use_og_for_twitter ? $this->object->opengraph_title : $this->object->twitter_title;
      if (!empty($opengraph_title)) {
        $opengraph_title = WD_SEO_Library::replace_placeholders($opengraph_title, $this->placeholders);
        if (!empty($opengraph_title)) {
          echo '<meta name="twitter:title" content="' . esc_attr($opengraph_title) . '" />' . "\n";
        }
      }
      $opengraph_description = $this->object->use_og_for_twitter ? $this->object->opengraph_description : $this->object->twitter_description;
      if (!empty($this->object->opengraph_description)) {
        $opengraph_description = WD_SEO_Library::replace_placeholders($opengraph_description, $this->placeholders);
        if (!empty($opengraph_description)) {
          echo '<meta name="twitter:description" content="' . esc_attr($opengraph_description) . '" />' . "\n";
        }
      }
      $opengraph_images = $this->object->use_og_for_twitter ? $this->object->opengraph_images : $this->object->twitter_images;
      if (!empty($opengraph_images)) {
        $attachment_ids = explode(',', $opengraph_images);
        foreach ($attachment_ids as $id) {
          $image = wp_get_attachment_url($id);
          if (!empty($image)) {
            echo '<meta name="twitter:image" content="' . esc_attr($image) . '" />' . "\n";
          }
        }
      }
    }
  }
}

function WDSWDSeo_Site() {
  new WDSeo_Site();
}
add_action('wp', 'WDSWDSeo_Site');
