<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeoNotFoundMonitoringView extends WDSeoAdminView {
  public function __construct() {
    add_action('wdseo_options_tabs', array($this, 'tab_title'));
    add_action('wdseo_options_tabs_content', array($this, 'body'));
  }

  public function tab_title() {
    ?>
    <li class="tabs">
      <a href="#wdseo_tab_404_monitoring" class="wdseo-tablink"><?php _e( '404 Monitoring', WD_SEO_PREFIX ); ?></a>
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
    $wdseo_404_monitoring = (object) $options->wdseo_404_monitoring;
    $wdseo_404_monitoring->wdseo_404_enable = isset($wdseo_404_monitoring->wdseo_404_enable) ? $wdseo_404_monitoring->wdseo_404_enable : 0;
    $wdseo_404_monitoring->wdseo_404_cleaning = isset($wdseo_404_monitoring->wdseo_404_cleaning) ? $wdseo_404_monitoring->wdseo_404_cleaning : 0;
    $wdseo_404_monitoring->wdseo_404__redirectTo = isset($wdseo_404_monitoring->wdseo_404__redirectTo) ? $wdseo_404_monitoring->wdseo_404__redirectTo : 0;
    $wdseo_404_monitoring->wdseo_404_redirecturl = isset($wdseo_404_monitoring->wdseo_404_redirecturl) ? $wdseo_404_monitoring->wdseo_404_redirecturl : '';
    $wdseo_404_monitoring->wdseo_404_status_code = isset($wdseo_404_monitoring->wdseo_404_status_code) ? $wdseo_404_monitoring->wdseo_404_status_code : '301';
    $wdseo_404_monitoring->wdseo_404_enable_email = isset($wdseo_404_monitoring->wdseo_404_enable_email) ? $wdseo_404_monitoring->wdseo_404_enable_email : 0;
    $wdseo_404_monitoring->wdseo_404_email_to = isset($wdseo_404_monitoring->wdseo_404_email_to) ? $wdseo_404_monitoring->wdseo_404_email_to : '';
    ?>
    <div id="wdseo_tab_404_monitoring" class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php echo '404 Monitoring' ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Enable Feature', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($wdseo_404_monitoring->wdseo_404_enable, 1); ?> id="wdseo_404_enable-1" class="wd-radio" value="1" name="wd_settings[wdseo_404_monitoring][wdseo_404_enable]" type="radio" />
              <label class="wd-label-radio" for="wdseo_404_enable-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($wdseo_404_monitoring->wdseo_404_enable, 0); ?> id="wdseo_404_enable-0" class="wd-radio" value="0" name="wd_settings[wdseo_404_monitoring][wdseo_404_enable]" type="radio" />
              <label class="wd-label-radio" for="wdseo_404_enable-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
              <?php
              $url = add_query_arg( array('page' => 'wdseo_redirects'), admin_url('admin.php') );
              ?>
              <p>
              <a href="<?php echo $url; ?>" ><?php _e('View your 404 / 301', WD_SEO_PREFIX); ?></a>
              </p>
            </span>

            <span class="wd-group">
              <label class="wd-label"><?php _e('404 Cleaning', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($wdseo_404_monitoring->wdseo_404_cleaning, 1); ?> class="wd-checkbox" value="1" name="wd_settings[wdseo_404_monitoring][wdseo_404_cleaning]" type="checkbox" />
              <p class="description"><?php _e('Automatically delete 404 redirects after 30 days.', WD_SEO_PREFIX); ?></p>
            </span>

            <span class="wd-group">
              <label class="wd-label" for="wdseo_404__redirectTo"><?php esc_html_e('Redirect 404 to','WD_SEO_PREFIX'); ?></label>
              <select id="wdseo_404__redirectTo" name="wd_settings[wdseo_404_monitoring][wdseo_404__redirectTo]">
                <option value="0" <?php if( $wdseo_404_monitoring->wdseo_404__redirectTo === "0" ) { echo 'selected="selected"'; } ?>><?php _e('None','WD_SEO_PREFIX'); ?></option>
                <option value="1" <?php if( $wdseo_404_monitoring->wdseo_404__redirectTo === "1" ) { echo 'selected="selected"'; } ?>><?php _e('Homepage','WD_SEO_PREFIX'); ?></option>
                <option value="2" <?php if( $wdseo_404_monitoring->wdseo_404__redirectTo === "2" ) { echo 'selected="selected"'; } ?>><?php _e('Custom URL','WD_SEO_PREFIX'); ?></option>
              </select>
            </span>

            <span class="wd-group wds_custom_url <?php echo ($wdseo_404_monitoring->wdseo_404__redirectTo !== '2') ? 'wdseo_hidden' : ''; ?>">
              <label class="wd-label" for="wdseo_404_redirecturl"><?php esc_html_e( 'Redirect to specific URL', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="wdseo_404_redirecturl" class="location-input" name="wd_settings[wdseo_404_monitoring][wdseo_404_redirecturl]" placeholder="<?php esc_html_e('Enter your custom url', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($wdseo_404_monitoring->wdseo_404_redirecturl) ? $wdseo_404_monitoring->wdseo_404_redirecturl : ''; ?>" />
            </span>

            <span class="wd-group">
              <label class="wd-label" for="wdseo_404_status_code"><?php esc_html_e('Status Code of Redirections', 'WD_SEO_PREFIX'); ?></label>
              <select id="wdseo_404_status_code" name="wd_settings[wdseo_404_monitoring][wdseo_404_status_code]">
                <?php
                $redirect_types = WD_SEO_Library::get_redirect_types();
                foreach ( $redirect_types as $key => $val ) {
                  $selected = ($wdseo_404_monitoring->wdseo_404_status_code == $key ) ? 'selected="selected"':'';
                  echo '<option value="' . $key . '" '. $selected . '>' . $val . '</option>';
                }
                ?>
              </select>
            </span>

            <span class="wd-group">
              <label class="wd-label"><?php _e('Email Notifications', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($wdseo_404_monitoring->wdseo_404_enable_email, 1); ?> id="wdseo_404_enable_email" value="1" name="wd_settings[wdseo_404_monitoring][wdseo_404_enable_email]" type="checkbox" />
              <p class="description"><?php _e('Receive email each time a new 404 is created.', WD_SEO_PREFIX); ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wdseo_404_email_to"><?php esc_html_e( 'Send Email Notifications to', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="wdseo_404_email_to" class="location-input" name="wd_settings[wdseo_404_monitoring][wdseo_404_email_to]" placeholder="<?php esc_html_e('', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($wdseo_404_monitoring->wdseo_404_email_to) ? $wdseo_404_monitoring->wdseo_404_email_to : ''; ?>" />
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php
    echo ob_get_clean();
  }
}

add_action('wdseo_init_after', function() {
  new WDSeoNotFoundMonitoringView();
});