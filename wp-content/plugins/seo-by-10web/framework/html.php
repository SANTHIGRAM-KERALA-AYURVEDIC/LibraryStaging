<?php
defined('ABSPATH') || die('Access Denied');

/**
 * HTML class to create necessary HTML templates.
 */
class WD_SEO_HTML {
  public static $total_in_page = 20;

  /**
   * Generate message by message id.
   *
   * @param int $message_id
   * @param string $message
   * @param string $type
   *
   * @return mixed|string|void
   */
  public static function message($message_id, $message = '', $type = 'updated') {
    if( !$message_id && is_numeric($message) ) {
      $message_id = $message;
    }
    if ( $message_id ) {
       switch ( $message_id ) {
        case 0:
          {
            break;
          }
        case 1:
          {
            $message = __('The changes are saved.', WD_SEO_PREFIX);
            $type = 'updated';
            break;
          }
        case 2:
          {
            $message = __('Failed to save changes.', WD_SEO_PREFIX);
            $type = 'error';
            break;
          }
        case 3:
          {
            $message = __('You must save changes.', WD_SEO_PREFIX);
            $type = 'error';
            break;
          }
        case 4:
          {
            $message = __('Sitemap XML generated successfully.', WD_SEO_PREFIX);
            $type = 'updated';
            break;
          }
        case 5:
          {
            $message = __('Failed.', WD_SEO_PREFIX);
            $type = 'error';
            break;
          }
        case 6:
          {
            $message = __('Plugin succesfully deactivated.', WD_SEO_PREFIX);
            $type = 'updated';
            break;
          }
        case 7:
          {
            $message = __('Sitemap successfully deleted.', WD_SEO_PREFIX);
            $type = 'updated';
            break;
          }
        case 8:
          {
            $message = __('Item successfully published.', WD_SEO_PREFIX);
            $type = 'updated';
            break;
          }
        case 9:
          {
            $message = __('Item successfully unpublished.', WD_SEO_PREFIX);
            $type = 'updated';
            break;
          }
        case 10: {
          $message = 'Item successfully deleted.';
          $type = 'updated';
          break;
        }
         case 11:
           {
             $message = __('The page URL already exists, please try again.', WD_SEO_PREFIX);
             $type = 'error';
             break;
           }
        default:
          {
            $message = '';
            break;
          }
      }
    }
    if ($message) {
      ob_start();
      ?><div class="<?php echo $type; ?> below-h2">
      <p>
        <strong><?php echo $message; ?></strong>
      </p>
      </div><?php
      $message = ob_get_clean();
    }
    return $message;
  }

  /**
   * Ordering.
   *
   * @param        $id
   * @param        $orderby
   * @param        $order
   * @param        $text
   * @param        $page_url
   * @param string $additional_class
   *
   * @return string
   */
  public static function ordering($id, $orderby, $order, $text, $page_url, $additional_class = '', $is_active = true) {
    $class = array(
      'manage-column',
      ($orderby == $id ? 'sorted': 'sortable'),
      $order,
      $additional_class,
      'col_' . $id,
    );
    $order = (($orderby == $id) && ($order == 'desc')) ? 'asc' : 'desc';
    ob_start();
    ?>
    <th id="<?php echo $id; ?>" class="<?php echo implode(' ', $class); ?>">
      <?php
      if ($is_active) {
        ?>
        <a href="<?php echo add_query_arg(array('orderby' => $id, 'order' => $order), $page_url); ?>"
           title="<?php _e('Click to sort by this item', WD_SEO_PREFIX); ?>">
          <span><?php echo $text; ?></span><span class="sorting-indicator"></span>
        </a>
      <?php
      }
      else {
      ?>
        <span>
          <?php
          echo $text;
          WD_SEO_Library::pro_banner();
          ?>
        </span>
      <?php
      }
      ?>
    </th>
    <?php
    return ob_get_clean();
  }

  /**
   * No items.
   *
   * @param $title
   *
   * @return string
   */
  public static function no_items($title) {
    $title = ($title != '') ? strtolower($title) : 'items';
    ob_start();
    ?><tr class="no-items">
    <td class="colspanchange" colspan="0"><?php echo sprintf(__('No %s found.', WD_SEO_PREFIX), $title); ?></td>
    </tr><?php
    return ob_get_clean();
  }

  /**
   * Pagination.
   *
   * @param      $total
   * @param bool $search
   * @param bool $params
   *
   * @return string
   */
  public static function pagination($total, $search = FALSE, $params = FALSE) {
    $bulk_action = !empty($params['actions']) ? TRUE: FALSE;
    $paged = WD_SEO_Library::get('paged', 1);
    $args = array(
      'base' => add_query_arg( 'paged', '%#%' ),
      'format' => '',
      'show_all' => TRUE,
      'end_size' => 1,
      'mid_size' => 1,
      'prev_next' => TRUE,
      'prev_text' => '&laquo;',
      'next_text' => '&raquo;',
      'total' => ceil($total / self::$total_in_page),
      'current' => $paged,
    );
    $page_links = paginate_links( $args );

    ob_start();
    ?>
    <div class="tablenav <?php echo ($bulk_action) ? 'wdseo-tablenav-bulk-action' : ''; ?>">
      <?php
      if ( $search ) {
        echo self::search();
      }
      if ( $bulk_action ) {
        echo self::bulk_actions( $params['actions'] );
      }
      if ( !empty($params['filter']) ) {
        echo self::filter($params);
      }
      ?>
      <div class="tablenav-pages">
        <span class="displaying-num"><?php printf( _n( '%s item', '%s items', $total, WD_SEO_PREFIX ), $total ); ?></span><?php
        if ( $page_links && self::$total_in_page < $total ) {
          echo $page_links;
        }
        ?></div>
    </div>
    <?php

    return ob_get_clean();
  }

  /**
   * Filter.
   *
   * @return string
   */
  public static function search() {
    $search = WD_SEO_Library::get('s', '');
    ob_start();
    ?>
    <p class="search-box">
      <input id="post-search-input" name="s" value="<?php echo $search; ?>" type="search" />
      <input class="button" value="<?php _e('Search', WD_SEO_PREFIX); ?>" type="button" onclick="wdseo_search()" />
    </p>
    <?php

    return ob_get_clean();
  }

  /**
   * Search.
   *
   * @return string
   */
  public static function filter($filters) {
    ob_start();
    $is_active = WDSeo()->is_active();
    ?>
    <div class="alignleft actions">
      <?php
      foreach ($filters as $filter => $filter_arr) {
        ?>
        <div class="free filter_box">
        <select name="<?php echo $filter; ?>" onchange="wdseo_filter(this)" <?php if ( !$is_active ) { echo ' disabled="disabled"'; } ?> >
          <?php
          $filter_value = WD_SEO_Library::get($filter, '');
          if (!empty($filter_arr) && is_array($filter_arr)) {
            foreach ( $filter_arr as $key => $value ) {
              ?>
              <option value="<?php echo $key; ?>"<?php if ( !$is_active ) {
                echo ' disabled="disabled"';
              } ?>
                <?php selected($filter_value, $key); ?>><?php echo $value; ?></option>
              <?php
            }
          }
          ?>
        </select>
        <?php  if ( !$is_active ) { WD_SEO_Library::pro_banner( $filter ); } ?>
        </div>

        <?php
      }
      ?>
    </div>
    <?php

    return ob_get_clean();
  }

  /**
   * Redirect types.
   *
   * @return false|string
   */
  public static function redirect_types( $selected = FALSE ) {
    ob_start();
    $types = WD_SEO_Library::get_redirect_types();
    echo '<option value="-1">' . __('Select', WD_SEO_PREFIX) . '</option>';
    echo '<optgroup label="' . __('Redirection', WD_SEO_PREFIX) . '">';
    foreach ( $types as $key => $value ) {
      ?><option value="<?php echo $key; ?>" <?php echo ($selected && $selected == $key) ? 'selected' : ''; ?>><?php echo $value; ?></option><?php
    }
    echo '</optgroup>';

    $types = WD_SEO_Library::get_client_error_types();
    echo '<optgroup label="' . __('Client errors', WD_SEO_PREFIX) . '">';
    foreach ( $types as $key => $value ) {
      ?><option value="<?php echo $key; ?>" <?php echo ($selected && $selected == $key) ? 'selected' : ''; ?>><?php echo $value; ?></option><?php
    }
    echo '</optgroup>';

    return ob_get_clean();
  }

  /**
   * Query parameters.
   *
   * @return false|string
   */
  public static function query_parameters_types( $selected = FALSE ) {
    ob_start();
    $types = WD_SEO_Library::get_query_parameter_types();
    foreach ( $types as $key => $value ) {
      ?><option value="<?php echo $key; ?>" <?php echo ($selected && $selected == $key) ? 'selected' : ''; ?>><?php echo $value; ?></option><?php
    }
    return ob_get_clean();
  }

  /**
   * Bulk actions.
   *
   * @param array $actions
   * @return string
   */
  public static function bulk_actions( $actions = array() ) {
    ob_start();
    ?>
    <div class="bulk-actions-box alignleft">
      <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e('Select bulk action', WD_SEO_PREFIX); ?></label>
      <select name="bulk_action" id="bulk-action-selector-top">
        <option value="-1"><?php _e('Bulk Actions', WD_SEO_PREFIX); ?></option>
        <?php
        foreach ( $actions as $key => $action ) {
          ?>
          <option value="<?php echo $key; ?>"><?php echo $action['title']; ?></option>
          <?php
        }
        ?>
      </select>
      <input type="button" id="doaction" class="button action" onclick="wdseo_bulk_action(this); return false;" value="<?php _e('Apply', WD_SEO_PREFIX); ?>" />
    </div>
    <?php

    return ob_get_clean();
  }
}
