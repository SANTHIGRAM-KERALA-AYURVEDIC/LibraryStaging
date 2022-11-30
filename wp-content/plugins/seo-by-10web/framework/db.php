<?php
defined('ABSPATH') || die('Access Denied');

class WD_SEO_DB {

  public function __construct() {
    $this->init();
  }

  public function init() {
    $version_key = WD_SEO_PREFIX . '_initial_version';
    $version = get_option($version_key);
    $last_version = WD_SEO_DB_VERSION;
    if ( !$version ) {
      add_option($version_key, WD_SEO_DB_VERSION, '', 'no');
    }
    elseif ( version_compare($version, $last_version, '<') ) {
      update_option($version_key, $last_version);
    }
    $this->redirects_table();
  }

  private function redirects_table() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $query = 'CREATE TABLE `' . $wpdb->prefix . '' .  WD_SEO_PREFIX . '_redirects` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `enable` tinyint(1) NOT NULL,
                `count` int(11) NOT NULL,
                `url` varchar(255) NOT NULL,
                `redirect_url` varchar(255) NOT NULL,
                `agent` varchar(255) NOT NULL,
                `redirect_type` smallint(4) NOT NULL,
                `redirect_404` smallint(4) NOT NULL,
                `date` datetime NOT NULL,
                `query_parameters` varchar(255) NOT NULL,
                `regex` tinyint(1) NOT NULL DEFAULT "0",
                `case` tinyint(1) NOT NULL DEFAULT "0",
                `slash` tinyint(1) NOT NULL DEFAULT "0",
      PRIMARY KEY (id)
    ) ' . $charset_collate . ';';
    dbDelta($query);
    $redirects = get_option(WD_SEO_PREFIX . '_redirects');
    if ( !empty($redirects) ) {
      $redirects = json_decode($redirects, true);
      foreach ( $redirects as $redirect ) {
        $data= array(
          'enable' => 1,
          'url' => $redirect['pageUrl'],
          'redirect_url' => $redirect['redirect_url'],
          'redirect_type' => 301,
          'agent' => $_SERVER['HTTP_USER_AGENT'],
          'date' => date('Y-m-d H:i:s')
        );
        $wpdb->insert($wpdb->prefix . WD_SEO_PREFIX . '_redirects', $data);
      }
      delete_option(WD_SEO_PREFIX . '_redirects');
    }
  }
}