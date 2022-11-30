<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeosettingsView extends WDSeoAdminView {
  /**
   * Display page.
   */
  public function display($options) {
    ob_start();
    echo $this->header();
    echo $this->tabs($options);

    // Pass the content to form.
    echo $this->form(ob_get_clean());
  }

  /**
   * Page header.
   *
   * @return string Generated html.
   */
  private function header() {
    wp_enqueue_script('jquery-ui-tabs');
    ob_start();
    echo $this->title(__('Options', WD_SEO_PREFIX));
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

  private function tabs($options) {
    ob_start();
    ?>
    <div class="wdseo_tabs wdseo_options_tab">
      <ul class="wdseo-tabs">
        <li class="tabs">
          <a href="#wdseo_tab_general_content" class="wdseo-tablink"><?php _e('General', WD_SEO_PREFIX); ?></a>
        </li>
        <?php
        do_action('wdseo_options_tabs', $options);
        ?>
      </ul>
      <input id="active_tab" name="active_tab" type="hidden" value="<?php echo (int) WD_SEO_Library::get('tab'); ?>" />
      <?php
      echo $this->body($options);
      do_action('wdseo_options_tabs_content', $options);
      ?>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Generate page body.
   *
   * @param object $options
   *
   * @return string Body html.
   */
  private function body($options) {
    $redirect_types = WD_SEO_Library::get_redirect_types();
    ob_start();
    ?>
    <div id="wdseo_tab_general_content" class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('User Permissions', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Show SEO Metabox to Role', WD_SEO_PREFIX); ?></label>
              <select name="wd_settings[meta_role]">
                <?php wp_dropdown_roles( $options->meta_role ); ?>
              </select>
            </span>
            <!--<span class="wd-group">
              <label class="wd-label"><?php /*_e('Show Moz metabox to roles', WD_SEO_PREFIX); */?></label>
              <select name="wd_settings[moz_role]">
                <?php /*wp_dropdown_roles( $options->moz_role ); */?>
              </select>
            </span>-->
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php echo sprintf(__('Uninstall %s Plugin', WD_SEO_PREFIX), WD_SEO_NICENAME); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <a class="button button-secondary" href="<?php echo add_query_arg(array( 'page' => WD_SEO_PREFIX . '_uninstall', ), admin_url('admin.php')); ?>"><?php _e('Uninstall', WD_SEO_PREFIX); ?></a>
            </span>
          </div>
        </div>
      </div>
      <div class="wd-table-col wd-table-col-50 wd-table-col-right">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Defaults', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Default Redirection Type', WD_SEO_PREFIX); ?></label>
              <select name="wd_settings[redirections]">
                <?php foreach ( $redirect_types as $key => $val) {
                  $selected = selected($options->redirections, $key);
                  echo '<option value="'. $key .'" '. $selected .'>' . $val . '</option>';
                } ?>
              </select>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Meta Information Optimization', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->meta, 1); ?> id="wd-meta-1" class="wd-radio" value="1" name="wd_settings[meta]" type="radio" />
              <label class="wd-label-radio" for="wd-meta-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->meta, 0); ?> id="wd-meta-0" class="wd-radio" value="0" name="wd_settings[meta]" type="radio" />
              <label class="wd-label-radio" for="wd-meta-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Redirect Attachment URLs Directly to File', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->attachment_redirect, 1); ?> id="wd-attachment_redirect-1" class="wd-radio" value="1" name="wd_settings[attachment_redirect]" type="radio" />
              <label class="wd-label-radio" for="wd-attachment_redirect-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->attachment_redirect, 0); ?> id="wd-attachment_redirect-0" class="wd-radio" value="0" name="wd_settings[attachment_redirect]" type="radio" />
              <label class="wd-label-radio" for="wd-attachment_redirect-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Remove Categories Prefix', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->remove_cat_prefix, 1); ?> id="wd-remove-cat-1" class="wd-radio" value="1" name="wd_settings[remove_cat_prefix]" type="radio" />
              <label class="wd-label-radio" for="wd-remove-cat-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->remove_cat_prefix, 0); ?> id="wd-remove-cat-0" class="wd-radio" value="0" name="wd_settings[remove_cat_prefix]" type="radio" />
              <label class="wd-label-radio" for="wd-remove-cat-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <!--<span class="wd-group">
              <label class="wd-label" for="autocrawl-interval"><?php //_e('Auto crawl interval', WD_SEO_PREFIX); ?></label>
              <input class="wd-int" id="autocrawl-interval" name="wd_settings[autocrawl_interval]" value="<?php //echo $options->autocrawl_interval; ?>" type="text" size="4" />&nbsp;<?php //_e('day', WD_SEO_PREFIX); ?>
              <p class="description"><?php //_e('Set 0 to disable auto crawl.', WD_SEO_PREFIX); ?></p>
            </span>-->
          </div>
        </div>
        <?php
        if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) || is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) ) { ?>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Import from Yoast', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content wd-width-100">
            <div class="updated below-h2 wdseo_success_msg">
              <p>
                <strong><?php echo __('Datas Successfuly imported.', WD_SEO_PREFIX); ?></strong>
              </p>
            </div>

            <div class="wd-group">
              <label class="wd-label"><?php echo __('Import from Yoast', WD_SEO_PREFIX); ?></label>
              <div class="bwg-flex">
                <input type="hidden" id="wdseo_ajax_nonce" value="<?php echo wp_create_nonce(WD_SEO_NONCE); ?>">

                <button class="button button-primary" id="wdseo_yoast_imoprt">
                  <?php _e( 'Import Now', WD_SEO_PREFIX ); ?>

                </button>
                <span class="wdseo_loading spinner"></span>
              </div>
              <p class="description"><?php _e('Import posts and terms metadata from Yoast.', WD_SEO_PREFIX); ?></p>
              <p class="description"><?php _e('By clicking Migrate, we\'ll import: Title tags, Meta description, Facebook Open Graph tags (title, description and image thumbnail), Twitter tags (title, description and image thumbnail), Meta Robots (noindex, nofollow...), Canonical URL, Focus keywords ', WD_SEO_PREFIX); ?></p>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}
