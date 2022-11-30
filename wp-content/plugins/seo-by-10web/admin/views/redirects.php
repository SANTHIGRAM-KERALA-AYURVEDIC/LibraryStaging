<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeoredirectsView extends WDSeoAdminView {
  /**
   * Display page.
   */
  public function display( $args = array() ) {
    ob_start();
    echo $this->header($args);
    echo $this->body($args);
    // Pass the content to form.
    echo $this->form(ob_get_clean());
  }

  /**
   * Page header.
   *
   * @return string Generated html.
   */
  private function header( $args = array() ) {
    ob_start();
    $add_new_link = $args['add_new_link'];
    echo $this->title(__('Redirects', WD_SEO_PREFIX));
    echo '<span class="free create_edit_redirect edit">';
    echo '<a class="page-title-action" ' . (!WDSeo()->is_active() ? 'disabled="disabled"' : 'href="' . $add_new_link . '"') . '>' . __('Add New', WD_SEO_PREFIX) . '</a>';
    if ( !WDSeo()->is_active() ) {
      WD_SEO_Library::pro_banner();
    }
    echo '</span>';

    return ob_get_clean();
  }

  /**
   * Generate page body.
   *
   * @param string $authorization_url
   *
   * @return string Body html.
   */
  private function body( $args = array() ) {
    $page = $args['page'];
    $page_url = $args['page_url'];
    $rows = $args['rows'];
    $total = $args['total'];
    $orderby = $args['orderby'];
    $order = $args['order'];
    $filters['actions'] = $args['actions'];
    ob_start();
    ?>
    <div class="wdseo-section">
      <?php
      $total = isset($total) ? $total : 0;
      echo WD_SEO_HTML::pagination($total, TRUE, $filters);
      ?>
      <table class="adminlist table table-striped wp-list-table widefat fixed pages wdseo-redirect-table">
        <thead>
        <tr>
          <td id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select all', WD_SEO_PREFIX); ?></label>
            <input id="check_all" type="checkbox" />
          </td>
          <th class="column-primary column-url"><?php _e('URL', WD_SEO_PREFIX); ?></th>
          <th class="column-redirect-url"><?php _e('Redirect URL', WD_SEO_PREFIX); ?></th>
          <th class="column-query-parameters"><?php _e('Query Parameters', WD_SEO_PREFIX); ?></th>
          <?php echo WD_SEO_HTML::ordering('redirect_type', $orderby, $order, __('Type', WD_SEO_PREFIX), $page_url, 'column-redirect-type'); ?>
          <?php echo WD_SEO_HTML::ordering('count', $orderby, $order, __('Count', WD_SEO_PREFIX), $page_url, 'column-count'); ?>
          <?php echo WD_SEO_HTML::ordering('date', $orderby, $order, __('Date', WD_SEO_PREFIX), $page_url, 'column-date'); ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if ( isset($rows) && !empty($rows) ) {
          foreach ( $rows as $key => $row ) {
            $alternate = (!isset($alternate) || $alternate == 'alternate') ? '' : 'alternate';
            $id = $row->id;
            $enable = $row->enable;
            $count = $row->count;
            $url = $row->url;
            $redirect_url = $row->redirect_url;
            $redirect_type = $row->redirect_type;
            $query_parameters = $row->query_parameters;
            $date = $row->date;
            $edit_link = add_query_arg(array( 'page' => $page, 'task' => 'edit', 'id' => $id ), $page_url);
            $publish_link = add_query_arg(array(
                                            'task' => ($enable ? 'unpublish' : 'publish'),
                                            'id' => $id,
                                          ), $page_url);
            $delete_link = add_query_arg(array( 'task' => 'delete', 'id' => $id ), $page_url);
            ?>
            <tr id="tr_<?php echo $id; ?>" class="alternate row-<?php echo $key % 2; ?>">
              <th class="check-column">
                <input id="check_<?php echo $id; ?>" name="check[<?php echo $id; ?>]" type="checkbox" data-id="<?php echo $id; ?>" />
              </th>
              <td class="col_url column-primary" data-colname="<?php _e('URL', WD_SEO_PREFIX); ?>">
                <strong>
                  <?php
                  echo $url;
                  echo (!$enable) ? ' - <span class="post-state">' . __('Unpublished', WD_SEO_PREFIX) . '</span>' : '';
                  ?>
                </strong>
                <div class="row-actions">
                    <span class="free create_edit_redirect edit">
                      <a <?php echo (WDSeo()->is_active()) ? 'href="' . $edit_link . '"' : 'disabled="disabled"'; ?>><?php _e('Edit', WD_SEO_PREFIX); ?></a>
                      <?php if ( !WDSeo()->is_active() ) {
                        WD_SEO_Library::pro_banner();
                      } ?> |
                    </span>
                  <span>
                      <a href="<?php echo $publish_link; ?>"><?php echo($enable ? __('Unpublish', WD_SEO_PREFIX) : __('Publish', WD_SEO_PREFIX)); ?></a> |
                    </span>
                  <span class="free mark-as-fixed trash">
                      <a <?php echo (WDSeo()->is_active()) ? 'href="' . $delete_link . '"' : 'disabled="disabled"'; ?>><?php _e('Delete', WD_SEO_PREFIX); ?></a>
                      <?php if ( !WDSeo()->is_active() ) {
                        WD_SEO_Library::pro_banner();
                      } ?>
                    </span>
                </div>
                <button class="toggle-row" type="button">
                  <span class="screen-reader-text"><?php _e('Show more details', WD_SEO_PREFIX); ?></span>
                </button>
              </td>
              <td class="col_redirect_url" data-colname="<?php _e('Redirect URL', WD_SEO_PREFIX); ?>">
                <?php echo $redirect_url; ?>
              </td>
              <td class="col_redirect_url" data-colname="<?php _e('Query Parameters', WD_SEO_PREFIX); ?>">
                <?php echo ucfirst($query_parameters); ?>
              </td>
              <td class="col_redirect_type" data-colname="<?php _e('Redirect type', WD_SEO_PREFIX); ?>">
                <?php echo $redirect_type; ?>
              </td>
              <td class="col_count" data-colname="<?php _e('Count', WD_SEO_PREFIX); ?>">
                <?php echo $count; ?>
              </td>
              <td class="col_date" data-colname="<?php _e('Date', WD_SEO_PREFIX); ?>">
                <?php echo $date; ?>
              </td>
            </tr>
            <?php
          }
        }
        else {
          echo WD_SEO_HTML::no_items('items');
        }
        ?>
        </tbody>
      </table>
      <?php echo WD_SEO_HTML::pagination($total); ?>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Edit.
   *
   * @param array $params
   */
  public function edit( $args = array() ) {
    ob_start();
    echo $this->edit_header($args);
    echo $this->edit_body($args);
    // Pass the content to form.
    echo $this->form(ob_get_clean(), array( 'action' => $args['form_action'] ));
  }

  /**
   * Page header.
   *
   * @return string Generated html.
   */
  private function edit_header( $args = array() ) {
    ob_start();
    $row = $args['row'];
    $title = !empty($row->id) ? __('Edit Redirect', WD_SEO_PREFIX) : __('Add Redirect', WD_SEO_PREFIX);
    echo $this->title($title);
    if ( !WDSeo()->is_active() ) {
      echo '<span class="free create_edit_redirect edit">';
      WD_SEO_Library::pro_banner();
      echo '</span>';
    }
    $buttons = array(
      'create_redirect' => array(
        'title' => __((empty($row->id) ? 'Save' : 'Update'), WD_SEO_PREFIX),
        'class' => 'button-primary',
        'onclick' => 'wdseo_redirect_form(this); return false;',
      ),
    );
    echo $this->buttons($buttons);

    return ob_get_clean();
  }

  /**
   * Create redirect edit_body html.
   *
   * @return string
   */
  private function edit_body( $args = array() ) {
    $row = $args['row'];
    ob_start();
    ?>
    <div class="wdseo-section">
      <div class="wd-table">
        <div class="wd-table-col wd-table-col-50 wd-table-col-left">
          <div class="wd-box-section">
            <div class="wd-box-content">
              <?php if ( !empty($row->id) ) { ?>
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Enable', WD_SEO_PREFIX); ?></label>
                  <input class="wd-label" type="radio" name="enable" id="enable_yes" value="1" <?php echo(($row->enable == 1) ? 'checked' : (empty($row) ? 'checked' : '')); ?> /><label class="wd-label-radio" for="enable_yes"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
                  <input class="wd-label" type="radio" name="enable" id="enable_no" value="0" <?php echo ($row->enable == 0) ? 'checked' : ''; ?>/><label class="wd-label-radio" for="enable_no"><?php _e('No', WD_SEO_PREFIX); ?></label>
                  <p class="description"></p>
                </div>
              <?php } ?>
              <div class="wd-group">
                <label class="wd-label" for="redirect_type"><?php _e('Redirect or Client Error Types', WD_SEO_PREFIX); ?></label>
                <select id="redirect_type" name="redirect_type" onchange="wdseo_change_redirect_client_error_type()">
                  <?php echo WD_SEO_HTML::redirect_types($row->redirect_type); ?>
                </select>
                <p class="description"><?php _e('Select the redirection or client error type for this item.', WD_SEO_PREFIX); ?></p>
              </div>
              <div class="wd-group">
                <label class="wd-label" for="url"><?php _e('Page URL', WD_SEO_PREFIX); ?></label>
                <input type="text" name="url" id="url" value="<?php echo $row->url; ?>" />
                <p class="description"><?php _e('Specify the URL from which the user should be redirected.', WD_SEO_PREFIX); ?></p>
              </div>
              <div class="wd-group">
                <label class="wd-label" id="regex_enable_disable"><?php _e('Regex', WD_SEO_PREFIX); ?></label>
                <input class="wd-label" type="radio" name="regex" id="regex_enable"
                       value="1" <?php echo(($row->regex == 1) ? 'checked' : ''); ?>
                       onclick="wdseo_change_pageurl_to_regex()"/>
                <label class="wd-label-radio" for="regex_enable"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
                <input class="wd-label" type="radio" name="regex" id="regex_disable"
                       value="0" <?php echo ($row->regex == 0) ? 'checked' : (empty($row) ? 'checked' : ''); ?>
                       onclick="wdseo_change_pageurl_to_regex()"/>
                <label class="wd-label-radio" for="regex_disable"><?php _e('No', WD_SEO_PREFIX); ?></label>
                <p class="description"><?php _e('Enable regexp?', WD_SEO_PREFIX); ?></p>
              </div>
              <div class="wd-group">
                <label class="wd-label" id="case_enable_disable"><?php _e('Ignore Case', WD_SEO_PREFIX); ?></label>
                <input class="wd-label" type="radio" name="case" id="case_enable"
                       value="1" <?php echo(($row->case == 1) ? 'checked' : ''); ?>/>
                <label class="wd-label-radio" for="case_enable"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
                <input class="wd-label" type="radio" name="case" id="case_disable"
                       value="0" <?php echo ($row->case == 0) ? 'checked' : (empty($row) ? 'checked' : ''); ?>/>
                <label class="wd-label-radio" for="case_disable"><?php _e('No', WD_SEO_PREFIX); ?></label>
                <p class="description"><?php _e('Make URL/Regex case insensitive?', WD_SEO_PREFIX); ?></p>
              </div>
              <div class="wd-group">
                <label class="wd-label" id="slash_enable_disable"><?php _e('Ignore Slash', WD_SEO_PREFIX); ?></label>
                <input class="wd-label" type="radio" name="slash" id="slash_enable"
                       value="1" <?php echo(($row->slash == 1) ? 'checked' : ''); ?>/>
                <label class="wd-label-radio" for="slash_enable"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
                <input class="wd-label" type="radio" name="slash" id="slash_disable"
                       value="0" <?php echo ($row->slash == 0) ? 'checked' : (empty($row) ? 'checked' : ''); ?>/>
                <label class="wd-label-radio" for="slash_disable"><?php _e('No', WD_SEO_PREFIX); ?></label>
                <p class="description"><?php _e('Make URL/Regex last slash insensitive?', WD_SEO_PREFIX); ?></p>
              </div>
              <div class="wd-group">
                <label class="wd-label" for="redirect_query_parameters"><?php _e('Query Parameters', WD_SEO_PREFIX); ?></label>
                <select name="query_parameters"  id="redirect_query_parameters">
                  <?php echo WD_SEO_HTML::query_parameters_types($row->query_parameters); ?>
                </select>
                <p class="description"></p>
              </div>
              <div class="wd-group">
                <label class="wd-label" for="redirect_url"><?php _e('Redirect URL', WD_SEO_PREFIX); ?></label>
                <input type="text" name="redirect_url" id="redirect_to" value="<?php echo $row->redirect_url; ?>" />
                <p class="description"><?php _e('Specify the URL where the user will be redirected to. Make sure to provide an absolute URL.', WD_SEO_PREFIX); ?></p>
              </div>
              <?php if ( !empty($row->id) ) { ?>
                <div class="wd-group">
                  <label class="wd-label" for="count"><?php _e('Count', WD_SEO_PREFIX); ?></label>
                  <input type="text" name="count" id="count" disabled value="<?php echo $row->count; ?>" />
                  <p class="description"><?php _e('This option shows you how many times this redirection took place on your website.', WD_SEO_PREFIX); ?></p>
                </div>
                <div class="wd-group">
                  <label class="wd-label"><?php _e('Agent', WD_SEO_PREFIX); ?></label>
                  <textarea type="text" name="agent" id="agent" disabled><?php echo $row->agent; ?></textarea>
                  <p class="description"><?php _e('Here you can check the user agent where this redirection was completed.', WD_SEO_PREFIX); ?></p>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="wd-table-col wd-table-col-50 wd-table-col-right" id="regex-cheat-sheet">
          <div class="wd-box-section">
            <div class="wd-box-content">
              <div class="wd-group">
                <div class="cheat-group">
                  <label class="wd-label"><?php _e('Cheat Sheet', WD_SEO_PREFIX); ?></label>
                  <div class="sheet-group">
                    <div class="sheet-col">
                      <div class="sheet">
                        <div class="sheet-example">[abc]</div>
                        <div class="sheet-description"><?php _e('A single character of: a, b or c', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">[^abc]</div>
                        <div class="sheet-description"><?php _e('Any single character except: a, b, or c', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">[a-z]</div>
                        <div class="sheet-description"><?php _e('Any single character in the range a-z', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">[a-zA-Z]</div>
                        <div class="sheet-description"><?php _e('Any single character in the range a-z or A-Z', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">^</div>
                        <div class="sheet-description"><?php _e('Start of line', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">$</div>
                        <div class="sheet-description"><?php _e('End of line', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\A</div>
                        <div class="sheet-description"><?php _e('Start of string', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\z</div>
                        <div class="sheet-description"><?php _e('End of string', WD_SEO_PREFIX); ?></div>
                      </div>
                    </div>
                    <div class="sheet-col">
                      <div class="sheet">
                        <div class="sheet-example">.</div>
                        <div class="sheet-description"><?php _e('Any single character', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\s</div>
                        <div class="sheet-description"><?php _e('Any whitespace character', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\S</div>
                        <div class="sheet-description"><?php _e('Any non-whitespace character', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\d</div>
                        <div class="sheet-description"><?php _e('Any digit', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\D</div>
                        <div class="sheet-description"><?php _e('Any non-digit', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\w</div>
                        <div class="sheet-description"><?php _e('Any word character (letter, number, underscore)', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\W</div>
                        <div class="sheet-description"><?php _e('Any non-word character', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">\b</div>
                        <div class="sheet-description"><?php _e('Any word boundary', WD_SEO_PREFIX); ?></div>
                      </div>
                    </div>
                    <div class="sheet-col">
                      <div class="sheet">
                        <div class="sheet-example">(...)</div>
                        <div class="sheet-description"><?php _e('Capture everything enclosed', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">(a|b)</div>
                        <div class="sheet-description"><?php _e('a or b', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">a?</div>
                        <div class="sheet-description"><?php _e('Zero or one of a', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">a*</div>
                        <div class="sheet-description"><?php _e('Zero or more of a', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">a+</div>
                        <div class="sheet-description"><?php _e('One or more of a', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">a{3}</div>
                        <div class="sheet-description"><?php _e('Exactly 3 of a', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">a{3,}</div>
                        <div class="sheet-description"><?php _e('3 or more of a', WD_SEO_PREFIX); ?></div>
                      </div>
                      <div class="sheet">
                        <div class="sheet-example">a{3,6}</div>
                        <div class="sheet-description"><?php _e('Between 3 and 6 of a', WD_SEO_PREFIX); ?></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="option-group">
                  <label class="wd-label">Options</label>
                  <div class="inline-sheets">
                    <div class="sheet">
                      <div class="sheet-example">i</div>
                      <div class="sheet-description"><?php _e('case insensitive', WD_SEO_PREFIX); ?></div>
                    </div>
                    <div class="sheet">
                      <div class="sheet-example">m</div>
                      <div class="sheet-description"><?php _e('treat as multi-line string', WD_SEO_PREFIX); ?></div>
                    </div>
                    <div class="sheet">
                      <div class="sheet-example">s</div>
                      <div class="sheet-description"><?php _e('dot matches newline', WD_SEO_PREFIX); ?></div>
                    </div>
                    <div class="sheet">
                      <div class="sheet-example">x</div>
                      <div class="sheet-description"><?php _e('ignore whitespace in regex', WD_SEO_PREFIX); ?></div>
                    </div>
                    <div class="sheet">
                      <div class="sheet-example">A</div>
                      <div class="sheet-description"><?php _e('matches only at the start of string', WD_SEO_PREFIX); ?></div>
                    </div>
                    <div class="sheet">
                      <div class="sheet-example">D</div>
                      <div class="sheet-description"><?php _e('matches only at the end of string', WD_SEO_PREFIX); ?></div>
                    </div>
                    <div class="sheet">
                      <div class="sheet-example">U</div>
                      <div class="sheet-description"><?php _e('non-greedy matching by default', WD_SEO_PREFIX); ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}
