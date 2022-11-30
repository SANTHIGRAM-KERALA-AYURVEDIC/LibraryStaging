<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeoNofollowExternalUrlsGlobalView extends WDSeoAdminView {
  public function __construct() {
    add_action('wdseo_options_tabs', array($this, 'tab_title'));
    add_action('wdseo_options_tabs_content', array($this, 'body'));
  }

  public function tab_title() {
    ?>
    <li class="tabs">
      <a href="#wdseo_tab_nofollow_external_urls_global" class="wdseo-tablink"><?php _e( 'Nofollow External Links', WD_SEO_PREFIX ); ?></a>
    </li>
    <?php
  }

  /**
   * Generate page body.
   *
   * @param object $options
   * @param array $groups
   *
   * @return string Body html.
   */
  public function body($options) {
    // Add all scripts, styles necessary to use media library.
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-tabs');
    ob_start();
    $wdseo_nofollow_external_urls_global = (object) $options->wdseo_nofollow_external_urls_global;
    $wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_enable = isset($wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_enable) ? $wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_enable : 0;
    $wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_exclude_array = isset($wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_exclude_array) ? $wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_exclude_array : array();
    ?>
    <div id="wdseo_tab_nofollow_external_urls_global" class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php echo 'Disallow bots to follow the links inside posts' ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Set Nofollow To External Links', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_enable, 1); ?> id="wdseo_nofollow_external_urls_global_enable-1" class="wd-radio" value="1" name="wd_settings[wdseo_nofollow_external_urls_global][wdseo_nofollow_external_urls_global_enable]" type="radio" />
              <label class="wd-label-radio" for="wdseo_nofollow_external_urls_global_enable-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_enable, 0); ?> id="wdseo_nofollow_external_urls_global_enable-0" class="wd-radio" value="0" name="wd_settings[wdseo_nofollow_external_urls_global][wdseo_nofollow_external_urls_global_enable]" type="radio" />
              <label class="wd-label-radio" for="wdseo_nofollow_external_urls_global_enable-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <div class="wd-group">
              <div id="wdseo-nofollow_url_global_container"
                   class="<?php echo $wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_enable ? '' : 'wdseo-hide' ?>">
                <label class="wd-label" for="nofollow_external_urls">Exclude URLs From Nofollow</label>
                <select class="wd-select2 wd-hide-dropdown nofollow_external_urls" id="nofollow_external_urls"
                        name="wd_settings[wdseo_nofollow_external_urls_global][wdseo_nofollow_external_urls_global_exclude_array][]"
                        multiple data-placeholder="">
                  <?php foreach ( $wdseo_nofollow_external_urls_global->wdseo_nofollow_external_urls_global_exclude_array as $key => $excluded_url ) {
                    if ( isset($excluded_url) && $excluded_url ) { ?>
                      <option <?php selected(TRUE, TRUE); ?> data-select2-tag="true"
                                                             value="<?php echo $excluded_url; ?>"><?php echo $excluded_url; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
                <p class="description"><?php _e('Exclude the URLs you want search bots to follow. ', WD_SEO_PREFIX); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    echo ob_get_clean();
  }
}

add_action('wdseo_init_after', function() {
  new WDSeoNofollowExternalUrlsGlobalView();
});