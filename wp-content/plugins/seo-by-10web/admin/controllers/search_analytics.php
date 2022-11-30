<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Overview controller class.
 */
class WDSeosearch_analyticsController extends WDSeoAdminController {
  private $search_analytics;
  private $device;
  private $is_active;
  /**
   * @var int How many queries get from Google search analytics (-1 for all).
   */
  private $row_limit = -1;

  public function __construct( $page = NULL, $task = NULL ) {
    $this->is_active = WDSeo()->is_active();
    if ( !$this->is_active ) {
      $this->row_limit = 10;
    }
    $this->search_analytics = $this->search_analytics();
    parent::__construct($page, $task);
  }

  private function search_analytics() {
    $this->device = WD_SEO_Library::get('device', 'desktop');

    $crawl = new WD_SEO_CRAWL;
    $paged = WD_SEO_Library::get('paged', 1);
    $search = WD_SEO_Library::get('s', '');
    $country = WD_SEO_Library::get('country', '');
    $date = (int) WD_SEO_Library::get('date', '');
    if ( $date != 90 && $date != 7 ) {
      $date = 28;
    }

    $search_analytics = $crawl->search_analytics($this->device, $search, $country, $date);

    if ( !isset($search_analytics['error']) && $search_analytics !== FALSE && $search_analytics !== 0 ) {
      $search_analytics_arr = array();
      // Sort.
      usort($search_analytics, array($this, 'sort'));
      if ( $this->row_limit !== -1 ) {
        $search_analytics = array_slice($search_analytics, 0, $this->row_limit);
      }
      // Paged.
      $search_analytics_arr['queries'] = array_slice($search_analytics, ($paged - 1) * WD_SEO_HTML::$total_in_page, WD_SEO_HTML::$total_in_page);
      // Total.
      $search_analytics_arr['count'] = count($search_analytics);

      return $search_analytics_arr;
    }

    return $search_analytics;
  }

  /**
   * Display.
   */
  public function display() {
    // Get the page view if exist or global view otherwise.
    $view_class = class_exists($this->class_prefix . 'View') ? $this->class_prefix . 'View' : WD_SEO_CLASS_PREFIX . 'AdminView';
    $view = new $view_class();

    echo $view->display($this->search_analytics, $this->filters(), $this->is_active);
  }

  /**
   * Sort.
   *
   * @param $a
   * @param $b
   *
   * @return int
   */
  public function sort( $a, $b ) {
    $orderby = WD_SEO_Library::get('orderby', 'impressions');
    $order = WD_SEO_Library::get('order', 'desc');

    // For Clicks, Impressions, CTR, Position.
    $result = (float) $a->$orderby < (float) $b->$orderby;

    return ($order === 'desc') ? $result : (!$result);
  }

  /**
   * Filters.
   *
   * @return array
   */
  public function filters() {
    $filters = array(
      'filter' => TRUE,
      'country' => WD_SEO_Library::countries(),
      'date' => array(
        '7' => __('Last 7 days',  WD_SEO_PREFIX),
        '' => __('Last 28 days',  WD_SEO_PREFIX), // Default value.
        '90' => __('Last 90 days',  WD_SEO_PREFIX),
      ),
    );

    return $filters;
  }
}
