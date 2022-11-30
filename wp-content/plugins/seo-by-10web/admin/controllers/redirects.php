<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Redirects controller class.
 */
class WDSeoredirectsController extends WDSeoAdminController {

  private $page_url;
  private $per_page = 20;
  private $bulk_action_name = '';


  public function __construct( $page = NULL, $task = NULL ) {
    require_once(wp_normalize_path(WD_SEO_DIR . '/admin/models/redirects.php'));
    require_once(wp_normalize_path(WD_SEO_DIR . '/admin/views/redirects.php'));
    $this->model = new WDSeoRedirectsModel();
    $this->views = new WDSeoredirectsView();
    $this->page = WD_SEO_PREFIX . '_redirects';
    $this->page_url = add_query_arg(array(
                                      'page' => $this->page,
                                      WD_SEO_NONCE => wp_create_nonce(WD_SEO_NONCE),
                                    ), admin_url('admin.php'));
    $this->bulk_action_name = 'bulk_action';
    $this->actions = array(
      'publish' => array(
        'title' => __('Publish', WD_SEO_PREFIX),
        $this->bulk_action_name => __('published', WD_SEO_PREFIX),
      ),
      'unpublish' => array(
        'title' => __('Unpublish', WD_SEO_PREFIX),
        $this->bulk_action_name => __('unpublished', WD_SEO_PREFIX),
      ),
      'delete' => array(
        'title' => __('Delete', WD_SEO_PREFIX),
        $this->bulk_action_name => __('deleted', WD_SEO_PREFIX),
      ),
    );

    $this->execute();
  }

  public function execute() {
    $id = (int) WD_SEO_Library::get('id', 0);
    $task = WD_SEO_Library::get('task');
    if ( method_exists($this, $task) ) {
      if ( !in_array($task, array( 'add', 'edit', 'display' )) ) {
        check_admin_referer(WD_SEO_NONCE, WD_SEO_NONCE);
      }
      $block_action = $this->bulk_action_name;
      $action = WD_SEO_Library::get($block_action, -1);
      if ( $action != -1 ) {
        $this->$block_action($action);
      }
      else {
        $this->$task($id);
      }
    }
    else {
      $this->display();
    }
  }

  /**
   * Bulk actions.
   *
   * @param string $task
   */
  public function bulk_action( $task = '' ) {
    $message = 0;
    $id_message_failed = 5;
    $successfully_updated = 0;
    $check = WD_SEO_Library::get('check', '');
    if ( $check ) {
      foreach ( $check as $id => $item ) {
        if ( method_exists($this, $task) ) {
          $message = $this->$task($id, TRUE);
          if ( $message != $id_message_failed ) {
            // Increase successfully updated items count, if action doesn't failed.
            $successfully_updated++;
          }
        }
      }
      if ( $successfully_updated ) {
        $block_action = $this->bulk_action_name;
        $message = urlencode(sprintf(_n('%s item successfully %s.', '%s items successfully %s.', $successfully_updated, WD_SEO_PREFIX), $successfully_updated, $this->actions[$task][$block_action]));
      }
    }
    $url = add_query_arg(array(
                           'page' => $this->page,
                           'task' => 'display',
                           ($message === $id_message_failed ? 'id_message' : 'message') => $message,
                         ), admin_url('admin.php'));
    wp_redirect($url, 302, WD_SEO_NICENAME);
  }

  /**
   * Display.
   *
   * @param null $redirects
   */
  public function display( $id = 0 ) {
    $order = WD_SEO_Library::get('order', 'desc');
    $order = ($order == 'desc') ? 'desc' : 'asc';
    $orderby = WD_SEO_Library::get('orderby', 'id');
    $page = (int) WD_SEO_Library::get('paged', 1);
    $num_page = $page ? ($page - 1) * $this->per_page : 0;
    $search = WD_SEO_Library::get('s', '');
    $params = array(
      'order' => $order,
      'orderby' => $orderby,
      'per_page' => $this->per_page,
      'num_page' => $num_page,
      'search' => $search,
    );
    // To prevent SQL injections.
    if ( !in_array($params['orderby'], array( 'id', 'count', 'type', 'date' )) ) {
      $params['orderby'] = 'id';
    }
    $data = $this->model->get_rows($params);

    $args = array();
    $args['page'] = $this->page;
    $args['page_url'] = $this->page_url;
    $args['add_new_link'] = add_query_arg(array( 'page' => $this->page, 'task' => 'add' ), 'admin.php');
    $args['actions'] = $this->actions;
    $args['total'] = $data['total'];
    $args['order'] = $order;
    $args['orderby'] = $orderby;
    $args['rows'] = $data['rows'];
    $this->views->display($args);
  }

  /**
   * Edit.
   *
   * @param int $id
   */
  public function add() {
    $this->edit(0);
  }

  /**
   * Edit.
   *
   * @param int $id
   */
  public function edit( $id = 0 ) {
    $args = array();
    $args['row'] = $this->model->get_row($id);
    $args['form_action'] = add_query_arg(array( 'page' => $this->page, 'id' => $id, 'task' => 'save' ), 'admin.php');
    $this->views->edit($args);
  }

  /**
   * Delete form by id.
   *
   * @param int  $id
   * @param bool $bulk
   *
   * @return int
   */
  public function delete( $id = 0, $bulk = FALSE ) {
    $id_message = 5;
    $delete = $this->model->delete(array( 'where' => 'id = ' . $id ));
    if ( $delete ) {
      $id_message = 10;
    }
    if ( $bulk ) {
      return $id_message;
    }
    else {
      $url = add_query_arg(array(
                             'page' => $this->page,
                             'task' => 'display',
                             'message' => $id_message,
                           ), admin_url('admin.php'));
      wp_redirect($url, 302, WD_SEO_NICENAME);
    }
  }

  /**
   * Publish by id.
   *
   * @param int  $id
   * @param bool $bulk
   *
   * @return int
   */
  public function publish( $id = 0, $bulk = FALSE ) {
    $id_message = 5;
    $updated = $this->model->update(array( 'enable' => 1 ), array( 'id' => $id ));
    if ( $updated ) {
      $id_message = 8;
    }
    if ( $bulk ) {
      return $id_message;
    }
    else {
      $url = add_query_arg(array(
                             'page' => $this->page,
                             'task' => 'display',
                             'message' => $id_message,
                           ), admin_url('admin.php'));
      wp_redirect($url, 302, WD_SEO_NICENAME);
    }
  }

  /**
   * Unpublish by id.
   *
   * @param int  $id
   * @param bool $bulk
   *
   * @return int
   */
  public function unpublish( $id = 0, $bulk = FALSE ) {
    $id_message = 5;
    $updated = $this->model->update(array( 'enable' => 0 ), array( 'id' => $id ));
    if ( $updated ) {
      $id_message = 9;
    }
    if ( $bulk ) {
      return $id_message;
    }
    else {
      $url = add_query_arg(array(
                             'page' => $this->page,
                             'task' => 'display',
                             'message' => $id_message,
                           ), admin_url('admin.php'));
      wp_redirect($url, 302, WD_SEO_NICENAME);
    }
  }

  /**
   * Save errors to DB.
   */
  public function save() {
    $args = array(
      'id' => WD_SEO_Library::get('id', 0),
      'enable' => WD_SEO_Library::get('enable', 1),
      'redirect_type' => WD_SEO_Library::get('redirect_type', ''),
      'url' => WD_SEO_Library::get('url', ''),
      'redirect_url' => WD_SEO_Library::get('redirect_url', ''),
      'agent' => $_SERVER['HTTP_USER_AGENT'],
      'date' => date('Y-m-d H:i:s'),
      'query_parameters' => WD_SEO_Library::get('query_parameters', ''),
      'regex' => WD_SEO_Library::get('regex', ''),
      'case' => WD_SEO_Library::get('case', ''),
      'slash' => WD_SEO_Library::get('slash', ''),
    );

    if ( $this->model->check_unique_url($args) ) {
      $url = add_query_arg(array(
                             'page' => $this->page,
                             'task' => 'edit',
                             'id' => $args['id'],
                             'message' => 11,
                           ), admin_url('admin.php'));
      wp_redirect($url, 302, WD_SEO_NICENAME);
    }

    $save = $this->model->save($args);
    $id_message = 5;
    if ( $save ) {
      $id_message = $save;
    }
    $url = add_query_arg(array(
                           'page' => $this->page,
                           'task' => 'display',
                           'message' => $id_message,
                         ), admin_url('admin.php'));
    wp_redirect($url, 302, WD_SEO_NICENAME);
  }
}