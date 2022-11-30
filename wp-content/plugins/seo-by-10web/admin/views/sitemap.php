<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeositemapView extends WDSeoAdminView {
  /**
   * Display page.
   */
  public function display($options, $post_types, $taxonomies, $archives) {
    wp_enqueue_style(WD_SEO_PREFIX . '_select2');
    wp_enqueue_script(WD_SEO_PREFIX . '_select2');
    wp_enqueue_style( WD_SEO_PREFIX . '_jquery_ui' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    ob_start();
    echo $this->header();
    echo $this->body($options, $post_types, $taxonomies, $archives);

    // Pass the content to form.
    echo $this->form(ob_get_clean());
  }

  /**
   * Page header.
   *
   * @return string Generated html.
   */
  private function header() {
    ob_start();
    echo $this->title(__('Sitemap', WD_SEO_PREFIX));
    $buttons = array(
      'save' => array(
        'title' => __('Save', WD_SEO_PREFIX),
        'value' => 'save',
        'name' => 'task',
        'class' => 'button-primary',
      ),
      'reset' => array(
        'title' => __('Reset', WD_SEO_PREFIX),
        'value' => 'reset',
        'name' => 'task',
        'class' => 'button-secondary',
      ),
      'cancel' => array(
        'title' => __('Cancel', WD_SEO_PREFIX),
        'value' => 'cancel',
        'name' => 'task',
        'class' => 'button-secondary',
      ),
    );
    echo $this->buttons($buttons);
    return ob_get_clean();
  }

  /**
   * Generate page body.
   *
   * @param object $options
   *
   * @return string Body html.
   */
  private function body($options, $post_types, $taxonomies, $archives) {
    ob_start();
    $sitemap_dir = $options->get_sitemap_dir();
    $sitemap_path = $sitemap_dir['path'] . $sitemap_dir['name'];
    $sitemap_url = $sitemap_dir['url'] . $sitemap_dir['name'];
    ?>
    <div class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('XML SITEMAP', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Generate XML Sitemap', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->sitemap, 1); ?> id="wd-sitemap-1" class="wd-radio" value="1" name="wd_settings[sitemap]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->sitemap, 0); ?> id="wd-sitemap-0" class="wd-radio" value="0" name="wd_settings[sitemap]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <?php
            if ( !$options->sitemap ) {
              echo WD_SEO_HTML::message(0, __('Sitemap will not be published until you switch the option.', WD_SEO_PREFIX), 'error');
            }
            ?>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Your Sitemap is Located at', WD_SEO_PREFIX); ?></label>
              <div class="wd-block-content wd-select-all">
                <?php echo $sitemap_path; ?>
              </div>
            </span>
            <span class="wd-group">
              <?php echo sprintf(__('Your Sitemap URL is %s', WD_SEO_PREFIX), '<a href="' . $sitemap_url . '" target="_blank">' . $sitemap_url . '</a>'); ?>
            </span>
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('SEARCH ENGINES', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label" for="wd-google-verification"><?php _e('Google Site Verification', WD_SEO_PREFIX); ?></label>
              <p class="description"><?php echo $options->google_verification_msg; ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wd-bing-verification"><?php _e('Bing Site Verification Code', WD_SEO_PREFIX); ?></label>
              <input type="text" id="wd-bing-verification" name="wd_settings[bing_verification]" value="<?php echo $options->bing_verification; ?>" />
              <p class="description"><?php echo sprintf(__('Click %shere%s to get your site verificaion code.', WD_SEO_PREFIX), '<a href="https://www.bing.com/webmaster/home/mysites" target="_blank">', '</a>'); ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wd-yandex-verification"><?php _e('Yandex Site Verification Code', WD_SEO_PREFIX); ?></label>
              <input type="text" id="wd-yandex-verification" name="wd_settings[yandex_verification]" value="<?php echo $options->yandex_verification; ?>" />
              <p class="description"><?php echo sprintf(__('Click %shere%s to get your site verificaion code.', WD_SEO_PREFIX), '<a href="https://webmaster.yandex.com/sites/" target="_blank">', '</a>'); ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Notify Search Engines When My Sitemap Updates', WD_SEO_PREFIX); ?></label>
              <input value="0" name="wd_settings[notify_google]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <input <?php checked($options->notify_google, 1); ?> id="wd-notify-google" class="wd-radio" value="1" name="wd_settings[notify_google]" type="checkbox" />
              <label class="wd-label-radio" for="wd-notify-google"><?php _e('Google', WD_SEO_PREFIX); ?></label>
              <input value="0" name="wd_settings[notify_bing]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <input <?php checked($options->notify_bing, 1); ?> id="wd-notify-bing" class="wd-radio" value="1" name="wd_settings[notify_bing]" type="checkbox" />
              <label class="wd-label-radio" for="wd-notify-bing"><?php _e('Bing', WD_SEO_PREFIX); ?></label>
            </span>
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('SITEMAP INFO', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            if ( $options->sitemap_items_count > 0 ) {
              ?>
            <span class="wd-group">
              <?php echo sprintf(_n('Sitemap contains %d item.', 'Sitemap contains %d items.', $options->sitemap_items_count, WD_SEO_PREFIX), $options->sitemap_items_count); ?>
            </span>
              <?php
            }
            ?>
            <span class="wd-group">
              <?php
              if ( isset($options->sitemap_last_modified->date)
                && isset($options->sitemap_last_modified->time) ) {
                if ( $options->sitemap_items_count == -1 ) {
                  echo sprintf(__('Sitemap was deleted on %s at %s.', WD_SEO_PREFIX), $options->sitemap_last_modified->date, $options->sitemap_last_modified->time);
                }
                else {
                  echo sprintf(__('Last updated on %s at %s.', WD_SEO_PREFIX), $options->sitemap_last_modified->date, $options->sitemap_last_modified->time);
                }
              }
              else {
                _e('Sitemap is not generated yet.', WD_SEO_PREFIX);
              }
              ?>
            </span>
            <span class="wd-group">
              <?php
              $buttons = array(
                'update_sitemap' => array(
                  'title' => __('Manually update', WD_SEO_PREFIX),
                  'value' => 'update_sitemap',
                  'name' => 'task',
                  'class' => 'button-primary',
                ),
              );
              echo $this->buttons($buttons, TRUE);
              ?>
              <?php
              $buttons = array(
                'update_sitemap' => array(
                  'title' => __('Delete Sitemap', WD_SEO_PREFIX),
                  'value' => 'delete',
                  'name' => 'task',
                  'class' => 'button-secondary',
                ),
              );
              if ( $options->sitemap_items_count == -1 ) {
                $buttons['update_sitemap']['disabled'] = 'disabled';
              }
              echo $this->buttons($buttons, TRUE);
              ?>
            </span>
          </div>
        </div>
      </div>
      <div class="wd-table-col wd-table-col-50 wd-table-col-right">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('ADDITIONAL PAGES', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-button button-secondary" onclick="wdseo_add_new_page()"><?php _e('Add new page', WD_SEO_PREFIX); ?></span>
            <br>
            <br>
            <?php _e('Here you can specify files or URLs which should be included in the sitemap, but do not belong to your Site/WordPress.', WD_SEO_PREFIX); ?><br>
            <?php _e('For example, if your domain is www.10web.io and your site is located on www.10web.io/site you might want to include your homepage at www.10web.io', WD_SEO_PREFIX); ?>
            <ul>
              <li><strong><?php _e('Note', WD_SEO_PREFIX); ?></strong>: <?php _e('If your site is in a subdirectory and you want to add pages which are NOT in the site directory or beneath, you MUST place your sitemap file in the root directory (Look at the "Location of your sitemap file" section on this page)!', WD_SEO_PREFIX); ?></li>
              <li><strong><?php _e('URL to the page', WD_SEO_PREFIX); ?></strong>: <?php _e('Enter the URL to the page. Examples: http://www.10web.io/index.html or www.10web.io/home', WD_SEO_PREFIX); ?></li>
              <li><strong><?php _e('Priority', WD_SEO_PREFIX); ?></strong>: <?php _e('Choose the priority of the page relative to the other pages. For example, your homepage might have a higher priority than your imprint.', WD_SEO_PREFIX); ?></li>
              <li><strong><?php _e('Last modified', WD_SEO_PREFIX); ?></strong>: <?php _e('Set the date of the last change (optional).', WD_SEO_PREFIX); ?></li>
            </ul>
            <table id="wdseo_additional_pages" width="100%" cellpadding="1" cellspacing="1">
              <tbody>
              <tr>
                <th scope="col"><?php _e('URL to the page', WD_SEO_PREFIX); ?></th>
                <th scope="col"><?php _e('Priority', WD_SEO_PREFIX); ?></th>
                <th scope="col"><?php _e('Frequency', WD_SEO_PREFIX); ?></th>
                <th scope="col"><?php _e('Last Modified', WD_SEO_PREFIX); ?></th>
                <th scope="col">#</th>
              </tr>
              <?php
              if ( !empty($options->additional_pages) && !empty($options->additional_pages->page_url) ) {
                echo $this->additional_pages_template($options->additional_pages);
              }
              else {
                ?>
                <tr>
                  <td colspan="5" align="center"><?php _e('No pages defined.', WD_SEO_PREFIX); ?></td>
                </tr>
                <?php
              }
              ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('EXCLUDES', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Exclude Post Types', WD_SEO_PREFIX); ?></label>
              <input value="" name="wd_settings[exclude_post_types][]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <select id="wd-exclude-post-types" multiple="multiple" name="wd_settings[exclude_post_types][]" style="width: 100%;"><?php //style="width: 100%;" is written here to make select2 responsive ?>
                <?php
                foreach ($post_types as $item => $label) {
                  ?>
                  <option value="<?php echo esc_attr($item); ?>" <?php selected(true, in_array($item, $options->exclude_post_types)); ?>><?php echo esc_html($label['name']); ?></option>
                  <?php
                }
                ?>
              </select>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Exclude Taxonomies', WD_SEO_PREFIX); ?></label>
              <input value="" name="wd_settings[exclude_taxonomies][]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <select id="wd-exclude-taxonomies" multiple="multiple" name="wd_settings[exclude_taxonomies][]" style="width: 100%;"><?php //style="width: 100%;" is written here to make select2 responsive ?>
                <?php
                foreach ($taxonomies as $item => $label) {
                  ?>
                  <option value="<?php echo esc_attr($item); ?>" <?php selected(true, in_array($item, $options->exclude_taxonomies)); ?>><?php echo esc_html($label['name']); ?></option>
                  <?php
                }
                ?>
              </select>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Exclude Archives', WD_SEO_PREFIX); ?></label>
              <input value="" name="wd_settings[exclude_archives][]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <select id="wd-exclude_archive" multiple="multiple" name="wd_settings[exclude_archives][]" style="width: 100%;"><?php //style="width: 100%;" is written here to make select2 responsive ?>
                <?php
                foreach ($archives as $item => $label) {
                  ?>
                  <option value="<?php echo esc_attr($item); ?>" <?php selected(true, in_array($item, $options->exclude_archives)); ?>><?php echo esc_html($label['name']); ?></option>
                  <?php
                }
                ?>
              </select>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wd-exclude-posts"><?php _e('Exclude Posts', WD_SEO_PREFIX); ?></label>
              <input type="text" id="wd-exclude-posts" name="wd_settings[exclude_posts]" value="<?php echo $options->exclude_posts; ?>" />
              <p class="description"><?php _e('You can exclude multiple posts from the sitemap by using a comma separated string with the Post ID\'s (e.g. 1,2,99,100).', WD_SEO_PREFIX); ?></p>
            </span>
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('OPTIONS', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Include Image Items Within the Sitemap', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_image, 1); ?> id="wd-sitemap_image-1" class="wd-radio" value="1" name="wd_settings[sitemap_image]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_image-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_image, 0); ?> id="wd-sitemap_image-0" class="wd-radio" value="0" name="wd_settings[sitemap_image]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_image-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Enable XML Video Sitemaps', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_video, 1); ?> id="wd-sitemap_video-1" class="wd-radio" value="1" name="wd_settings[sitemap_video]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_video-1"><?php _e('Enable', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_video, 0); ?> id="wd-sitemap_video-0" class="wd-radio" value="0" name="wd_settings[sitemap_video]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_video-0"><?php _e('Disable', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Include Stylesheet Within the Generated Sitemap', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_stylesheet, 1); ?> id="wd-sitemap_stylesheet-1" class="wd-radio" value="1" name="wd_settings[sitemap_stylesheet]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_stylesheet-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_stylesheet, 0); ?> id="wd-sitemap_stylesheet-0" class="wd-radio" value="0" name="wd_settings[sitemap_stylesheet]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_stylesheet-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wd-limit"><?php _e('Max Entries per Sitemap', WD_SEO_PREFIX); ?></label>
              <input type="text" id="wd-limit" name="wd_settings[limit]" value="<?php echo $options->limit; ?>" />
              <p class="description"><?php _e('Maximum number of entries per sitemap page. Lower this to prevent memory issues on some installs.', WD_SEO_PREFIX); ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Autoupdate Sitemap', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->autoupdate_sitemap, 1); ?> id="wd-autoupdate_sitemap-1" class="wd-radio" value="1" name="wd_settings[autoupdate_sitemap]" type="radio" />
              <label class="wd-label-radio" for="wd-autoupdate_sitemap-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->autoupdate_sitemap, 0); ?> id="wd-autoupdate_sitemap-0" class="wd-radio" value="0" name="wd_settings[autoupdate_sitemap]" type="radio" />
              <label class="wd-label-radio" for="wd-autoupdate_sitemap-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
              <p class="description"><?php _e('Autoupdate sitemap on posts/pages have been edited.', WD_SEO_PREFIX); ?></p>
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }

  private function additional_pages_template( $values, $template = false ) {
    ob_start();
    if ( !empty($values) ) {
      foreach($values->page_url as $index => $val) {
        if( !empty($val) ){
          $last_changed = !empty($values->last_changed[$index]) ? $values->last_changed[$index] : '';
          ?>
          <tr class="alternate">
            <td><input type="text" name="wd_settings[additional_pages][page_url][]" value="<?php echo $val; ?>" style="width: 100%;" /></td>
            <td style="width: 45px;">
              <select name="wd_settings[additional_pages][priority][]" style="width: 100%;">
                <?php
                $priorities = WD_SEO_Library::get_additional_page_priorities();
                foreach ( $priorities as $priority ) {
                  $s = (!empty($values->priority[$index]) && $values->priority[$index] == $priority ) ? 'selected' : '';
                  echo '<option value="' . $priority . '" ' . $s . '>' . $priority . '</option>';
                }
                ?>
              </select>
            </td>
            <td style="width: 75px;">
              <select name="wd_settings[additional_pages][frequency][]" style="width: 100%;">
                <?php
                $frequencies = WD_SEO_Library::get_additional_page_frequencies();
                foreach( $frequencies as $frequency ) {
                  $s = (!empty($values->frequency[$index]) && $values->frequency[$index] == strtolower($frequency) ) ? 'selected' : '';
                  echo '<option value="' . strtolower($frequency) . '" ' . $s . '>' . $frequency . '</option>';
                }
                ?>
              </select>
            </td>
            <td style="width: 100px;"><input type="text" name="wd_settings[additional_pages][last_changed][]" autocomplete="off" value="<?php echo date(get_option("date_format"), strtotime($last_changed)); ?>" class="wdseo-date" style="width: 100%;" /></td>
            <td style="text-align: center; width: 5px;"><a href="javascript:void(0);" onclick="wdseo_remove_additional_page( this )" class="dashicons dashicons-trash"></a></td>
          </tr>
        <?php
        }
      }
    }
    return ob_get_clean();
  }
}
