<?php

class WDSeoRedirectsModel extends WDSeoAdminModel {

  public $tbl;

  public function __construct() {
    global $wpdb;
    $this->tbl = $wpdb->prefix . WD_SEO_PREFIX . '_redirects';
  }

  /**
   * Get rows.
   * @param array $args
   *
   * @return array
   */
  public function get_rows( $args = array() ) {
    global $wpdb;
    $order = $args['order'];
    $orderby = $args['orderby'];
    $per_page = $args['per_page'];
    $num_page = $args['num_page'];
    $search = $args['search'];
    $select = 'SELECT t1.* FROM ' . $this->tbl . ' AS t1';
    $where = '';
    if ( $search ) {
      $where .= ' WHERE (
              t1.url LIKE "%' . $search . '%" OR 
              t1.redirect_url LIKE "%' . $search . '%" OR 
              t1.redirect_type LIKE "%' . $search . '%"
            )';
    }
    $query_count = str_replace( 't1.*', 'COUNT(*) qty', $select . '' . $where );
    $order = ' ORDER BY t1.' . $orderby . ' ' . $order;
    $limit = " LIMIT " . $num_page . "," . $per_page;
    $query = $select . '' . $where . '' . $order . '' . $limit;
    $data = array();
    $data['total'] = $wpdb->get_var($query_count);
    $data['rows'] = $wpdb->get_results($query);

    return $data;
  }

  /**
   * Get row.
   * @param int $id
   *
   * @return array|object|void|null
   */
  public function get_row( $id = 0 ) {
    global $wpdb;
    if ( !$id ) {
      $row = new stdClass();
      $row->enable = 1;
      $row->count = 0;
      $row->url = '';
      $row->redirect_url = '';
      $row->redirect_type = '';
      $row->agent = '';
      $row->query_parameters = '';
      $row->regex = 0;
      $row->case = 0;
      $row->slash = 0;
    } else {
      $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $this->tbl . ' WHERE id="%d"', $id));
    }

    return $row;
  }

  public function save( $args = array() ) {
    $id = $args['id'];
    if( $id ) {
      $success = $this->update($args, array('id' => $id) );
    }
    else {
      unset($args['id']);
      $success = $this->insert($args);
    }

    return $success;
  }
  
  /**
   * Insert row(s) in db.
   *
   * @param array  $args
   * @param array  $format
   *
   * @return bool
   */
  public function insert( $args = array(), $format = array() ) {
    global $wpdb;
    return $wpdb->insert($this->tbl, $args, $format);
  }

  /**
   * Update row(s) in db.
   *
   * @param array  $args
   * @param array  $where
   *
   * @return bool
   */
  public function update( $args = array(), $where = array() ) {
    global $wpdb;
    return $wpdb->update($this->tbl, $args, $where);
  }
  
   /**
   * Delete row(s) from db.
   *
   * @param array $params
   * params = [selection, table, where, order_by, limit]
   *
   * @return array
   */
  public function delete( $args = array() ) {
    global $wpdb;
    $query = "DELETE FROM " . $this->tbl;
    if ( isset($args['where']) ) {
      $query .= " WHERE " . $args['where'];
    }
    return $wpdb->query($query);
  }

  public function check_unique_url( $args = array() ) {
    global $wpdb;
    $id = $args['id'];
    $url = $args['url'];
    $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $this->tbl . ' WHERE url="%s" AND id != "%d"', $url, $id));
    if ( !empty($row) ) {
      return TRUE;
    }
    return FALSE;
  }
}