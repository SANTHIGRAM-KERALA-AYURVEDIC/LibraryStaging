<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Class WD_SEO_Postmeta.
 */
class WD_SEO_Postmeta {
  /**
   * @var string
   */
  public $meta_title = '';
  public $meta_description = '';
  public $meta_keywords = '';
  public $opengraph_title = '';
  public $opengraph_description = '';
  public $opengraph_images = '';
  public $use_og_for_twitter = 1;
  public $twitter_title = '';
  public $twitter_description = '';
  public $twitter_images = '';
  public $canonical_url = '';
  public $redirect_url = '';
  public $redirections = '';

  public $video_url = '';
  public $video_title = '';
  public $video_description = '';
  public $video_thumbnail = '';
  public $video_tags = '';
  public $video_categories = '';
  public $meta_pagination = 1;

  /**
   * @var int 1/0
   */
  public $index = '';
  public $follow = '';
  public $date = '';
  public $exclude_from_video_sitemap = 0;
  public $metabox;
  
  /**
   * @var array
   */
  public $wdseo_video = array(
    array(
      'video_url' => '',
      'internal_video' => '',
      'video_title' => '',
      'video_description' => '',
      'video_thumbnail' => '',
      'video_duration' => '',
      'video_rating' => '',
      'view_count' => '',
      'video_tags' => '',
      'video_categories' => '',
      'not_family_friendly' => '',
    )
  );
  public $robots_advanced = array('');
  public $faqs = array();
  public $nofollow_excluded_urls = array();

  /**
   * WD_SEO_Postmeta constructor.
   *
   * @param int $id
   * @param string $type
   * @param string $mode ('default', 'parent', 'site_values')
   */
  public function __construct( $id = 0, $type = 'post', $mode = 'default' ) {
    if ($id) {
      if ( 'post' == $type ) {
        $post_type = get_post_type($id);
        // Get options from db.
        $options = get_post_meta($id, WD_SEO_PREFIX . '_options', TRUE);
      }
      else {
        $post_type = $type;
        $options = get_term_meta($id, WD_SEO_PREFIX . '_options', TRUE);
      }
      $values = null;
      if ('post' == $type && 'site_values' == $mode) {
        $post = get_post($id);
        if ($post) {
          $values = new stdClass();
          $values->title = $post->post_title;
          $values->description = @get_the_excerpt();
          $values->image = get_post_thumbnail_id($post->ID);
        }
      }
      foreach ($this as $name => $value) {
        switch ($mode) {
          case 'site_values' : {
            if (isset($options->$name) && (is_array($options->$name) ? !in_array('', $options->$name) : $options->$name != '')) {
              $this->$name = $options->$name;
            }
            else if (('opengraph_images' == $name || 'twitter_images' == $name) && isset($values->image) && $values->image) {
              $this->$name = $values->image;
            }
            else if (isset(WDSeo()->options->metas->$post_type->$name)) {
              if ( $post_type == 'page' && $id == get_option( 'page_on_front' ) && isset(WDSeo()->options->metas->home->$name) ) {
                $this->$name = WDSeo()->options->metas->home->$name;
              } else {
                $this->$name = WDSeo()->options->metas->$post_type->$name;
              }
            }
            else {
              $this->$name = $value;
            }
            break;
          }
          case 'parent' : {
            $this->$name = (isset(WDSeo()->options->metas->$post_type->$name) ? WDSeo()->options->metas->$post_type->$name : $value);
            break;
          }
          default : {
            $this->$name = isset($options->$name) ? $options->$name : $value;
          }
        }
      }
      if ('post' != $type && isset($this->redirect_url)) {
        $this->redirect_url = null;
      }
      if ($values) {
        foreach ($this->get_inherited_fields_list() as $name => $value_name) {
          if (empty($this->$name)) {
            $this->$name = $values->$value_name;
          }
        }
      }
    }
    else {
      // Get options from $_POST
      $settings = WD_SEO_Library::get('wd_settings');
      foreach ( $this as $name => $value ) {
        $this->$name = isset($settings[$name]) ? $settings[$name] : $value;
        if ( $name == 'faqs' ) {
          $this->$name = $this->set_faq( $settings );
        }
        if ($name == 'nofollow_excluded_urls') {
          $this->$name = $this->set_nofollow_exclude_urls( $settings );
        }
      }
    }
    foreach ($this as $name => $value) {
      // The old redirect value has been changed to a new one. (ex. 1 => 301)
      if ( $name == 'redirections' ) {
        $this->$name = WD_SEO_Library::change_old_redirect_status($value);
      }
    }
  }

  /**
   * WD_SEO_Postmeta store function.
   *
   * @param $object_id
   * @param string $post_type
   */
  public function store( $object_id, $post_type = '' ) {
    if ('post' == $post_type) {
      $post_type = get_post_type($object_id);
      $object_type = 'post';
    }
    else {
      $object_type = 'taxonomy';
    }
	
    if ($post_type) {
      if (isset(WDSeo()->options->metas->$post_type->metabox) && WDSeo()->options->metas->$post_type->metabox) {
        if ('post' == $object_type) {
          update_post_meta($object_id, WD_SEO_PREFIX . '_options', $this);
        }
        else {
          update_term_meta($object_id, WD_SEO_PREFIX . '_options', $this);
        }
      }
    }
  }

  /**
   * Get fields list to be inherited if empty.
   *
   * @return array
   */
  public function get_inherited_fields_list() {
    return array('meta_title' => 'title', 'meta_description' => 'description', 'opengraph_title' => 'title', 'opengraph_description' => 'description', 'opengraph_images' => 'image', 'twitter_title' => 'title', 'twitter_description' => 'description', 'twitter_images' => 'image');
  }

  /**
   * Set faq.
   * @param array $args
   *
   * @return array
   */
  private function set_faq( $args = array() ) {
    static $data = array();
    if ( !empty($args['question']) && !empty($args['answer']) ) {
      foreach ( $args['question'] as $key => $question ) {
        $answer = !empty($args['answer'][$key]) ? $args['answer'][$key] : '';
        if ( !empty($question) && !empty($answer) ) {
          $data[$key] = array(
            'question' => $question,
            'answer' => $answer
          );
        }
      }
    }
    return $data;
  }

  /**
   * Set posts excluded urls from nofollow.
   * @param array $args
   *
   * @return array
   */
  private function set_nofollow_exclude_urls( $args = array() ) {
    static $data = array();
    if ( !empty($args['nofollow_excluded_urls'])) {
      foreach ( $args['nofollow_excluded_urls'] as $excluded_url ) {
        if (!empty($excluded_url)) {
          array_push($data, $excluded_url);
          $data = array_unique($data);
        }
      }
    }
    return $data;
  }
}
