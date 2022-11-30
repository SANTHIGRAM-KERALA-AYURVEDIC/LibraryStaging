<?php
class WDSeo_Robots {
  /**
   * Options instance.
   *
   * @var WD_SEO_Options
   */
  public $options = null;

  /**
   * WDSeo_Site constructor.
   */
  public function __construct() {
    $this->options = new WD_SEO_Options();
    // Use PHP_INT_MAX to prioritize the plugin robots.txt.
    add_filter('robots_txt', array( $this, 'robots_include_template' ), PHP_INT_MAX);
    add_filter('redirect_canonical', array( $this, 'robots_canonical'), 10, 2 );
  }

  /**
   * Include template-robots.php
   */
  public function robots_include_template( $template ) {
    if ( isset($this->options->enable_robots) && $this->options->enable_robots == '1' ) {
      $wdseo_robots = WD_SEO_DIR . '/site/template-robots.php';
      if ( file_exists($wdseo_robots) ) {
        require_once($wdseo_robots);
        return wdseo_robots_file();
      }
    }

    return $template;
  }

  /**
   * Canonical redirect for robots.txt
   */
  public function robots_canonical($redirect_url, $requested_url) {
    if ( isset($this->options->enable_robots) && $this->options->enable_robots == '1' ) {
      if ( $redirect_url == get_home_url() . '/robots.txt/' ) {
        return FALSE;
      }
    }

    return $redirect_url;
  }
}

$robots = new WDSeo_Robots();
