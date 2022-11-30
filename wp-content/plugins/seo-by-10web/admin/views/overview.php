<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeooverviewView extends WDSeoAdminView {
  /**
   * Display page.
   *
   * @param $options
   * @param $authorization_url
   * @param $issues
   * @param $moz_url_metrics
   * @param $recommends_problems
   */
  public function display($options, $authorization_url, $issues, $moz_url_metrics, $recommends_problems) {
    ob_start();
    echo $this->body($options, $authorization_url, $issues, $moz_url_metrics, $recommends_problems);

    // Pass the content to form.
    echo $this->form(ob_get_clean());
  }

  /**
   * Generate page body.
   *
   * @param $options
   * @param $authorization_url
   * @param $issues
   * @param $moz_url_metrics
   * @param $recommends_problems
   *
   * @return string Body html.
   */
  private function body($options, $authorization_url, $issues, $moz_url_metrics, $recommends_problems) {
    $fix_url = add_query_arg(array('page' => WD_SEO_PREFIX . '_search_console'), admin_url('admin.php'));
    $search_analytics_url = add_query_arg(array('page' => WD_SEO_PREFIX . '_search_analytics'), admin_url('admin.php'));
    ob_start();
    ?>
    <div class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Run SEO Analysis of Your Site', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            if ( isset($issues['error']) ) {
              if ( isset($issues['message']) ) {
                echo WD_SEO_HTML::message(0, $issues['message'], 'error');
              }
              if ( !isset($issues['interrupt']) ) {
                ?>
                <span class="wd-group">
                  <a href="<?php echo esc_url($authorization_url); ?>" class="button-primary"><?php _e('Authenticate with Google', 'wdseo'); ?></a>
                  <p class="description"><?php _e('To allow to fetch your Google Search Console information, please Authenticate with Google.', WD_SEO_PREFIX); ?></p>
                </span>
                <?php
              }
            }
            else {
              ?>
            <span class="wd-group">
              <?php
              $buttons = array(
                'reauthenticate' => array(
                  'title' => __('Reauthenticate with Google', WD_SEO_PREFIX),
                  'value' => 'reauthenticate',
                  'name' => 'task',
                  'class' => 'button-secondary',
                ),
              );
              echo $this->buttons($buttons, TRUE);
              ?>
              <span class="wd-float-right">
                <a class="button-primary wd-left" href="<?php echo $search_analytics_url; ?>">
                  <?php _e('Search analytics', WD_SEO_PREFIX); ?>
                </a>
              </span>
            </span>
              <?php
            }
            ?>
          </div>
        </div>
        <?php
        if ( $moz_url_metrics === FALSE ) {
          ?>
          <div class="wd-box-section">
            <div class="wd-box-title">
              <strong><?php _e('SEO Moz Account', WD_SEO_PREFIX); ?></strong>
            </div>
            <div class="wd-box-content">
              <span class="wd-group">
                <?php echo sprintf(__('%s to gain access to reports that will tell you how your site stacks up against the competition with all of the important SEO measurement tools - ranking, links, and much more.', WD_SEO_PREFIX), '<a href="http://moz.com/products/api" target="_blank">' . __('Sign-up', WD_SEO_PREFIX) . '</a>'); ?>
              </span>
              <span class="wd-group">
                <?php echo sprintf(__('Register or login to %s, then navigate to %s page of your account. If you do not have API credentials, firstly, verify reCAPTCHA, then press %s from %s tab.', WD_SEO_PREFIX), '<a href="http://moz.com/products/api" target="_blank">MOZScape</a>', '<b>Dashboard</b>', '<b>Generate</b>', '<b>Access</b>'); ?>
              </span>
              <span class="wd-group">
                <?php echo sprintf(__('If it takes longer than a couple of minutes to generate the keys, you can refresh the page. As the keys are generated, they will be available on the same %s section. Copy them and fill in %s and %s options correspondingly.', WD_SEO_PREFIX), '<b>Access</b>', '<b>' . __('Access ID', WD_SEO_PREFIX) . '</b>', '<b>' . __('Secret Key', WD_SEO_PREFIX) . '</b>'); ?>
              </span>
              <span class="wd-group">
                <label class="wd-label" for="access-id"><?php _e('Access ID', WD_SEO_PREFIX); ?></label>
                <input id="access-id" name="wd_settings[moz_access_id]" value="<?php echo $options->moz_access_id; ?>" type="text" />
              </span>
              <span class="wd-group">
                <label class="wd-label" for="secret-key"><?php _e('Secret Key', WD_SEO_PREFIX); ?></label>
                <input id="secret-key" name="wd_settings[moz_secret_id]" value="<?php echo $options->moz_secret_id; ?>" type="text" />
              </span>
              <span class="wd-group wd-right">
                <?php
                $buttons = array(
                  'save' => array(
                    'title' => __('Authenticate', WD_SEO_PREFIX),
                    'value' => 'save',
                    'name' => 'task',
                    'class' => 'button-primary',
                  ),
                );
                echo $this->buttons($buttons, TRUE);
                ?>
              </span>
            </div>
          </div>
          <?php
        }
        else {
          ?>
          <div class="wd-box-section">
            <div class="wd-box-title">
              <strong><?php _e('SEO MOZ statistics', WD_SEO_PREFIX); ?></strong>
            </div>
            <div class="wd-box-content">
            <span class="wd-group">
              <input type="hidden" name="wd_settings[moz_access_id]" value="" />
              <input type="hidden" name="wd_settings[moz_secret_id]" value="" />
              <?php
              $buttons = array(
                'save' => array(
                  'title' => __('Reauthenticate with MOZ', WD_SEO_PREFIX),
                  'value' => 'save',
                  'name' => 'task',
                  'class' => 'button-secondary',
                ),
              );
              echo $this->buttons($buttons, TRUE);
              ?>
            </span>
              <?php
              if ( isset($moz_url_metrics['error']) ) {
                if ( isset($moz_url_metrics['message']) ) {
                  echo WD_SEO_HTML::message(0, $moz_url_metrics['message'], 'error');
                }
              }
              else {
                foreach ( $moz_url_metrics as $response_field => $urlMetric ) {
                  $alternate = (!isset($alternate) || $alternate == 'alternate') ? '' : 'alternate';
                  ?>
                  <span class="wd-group wd-moz-metric <?php echo $alternate; ?>">
              <label class="wd-label">
                <span><?php echo $urlMetric['title']; ?></span>
                <span class="wd-float-right wd-font-weight-normal"><?php echo $urlMetric['value']; ?></span>
              </label>
              <p class="description"><?php echo $urlMetric['description']; ?></p>
            </span>
                  <?php
                }
              }
              ?>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
      <div class="wd-table-col wd-table-col-50 wd-table-col-right">
        <?php
        // Problems box.
        ?>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Problems', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            if ( !empty($recommends_problems['problems']) ) {
              foreach ( $recommends_problems['problems'] as $key => $values ) {
                foreach ( $values as $val ) {
                  ?>
                  <span class="wd-group">
              <div class="error inline notice notice-error">
                <p><?php echo $val['message']; ?></p>
              </div>
            </span>
                  <?php
                }
              }
            }
            else {
              ?>
              <span class="wd-group wd-center">
              <div class="wd-overview-item wd-full-width">
                <strong><?php _e('No problems found', WD_SEO_PREFIX); ?></strong>
              </div>
            </span>
              <?php
            }
            ?>
          </div>
        </div>
        <?php
        // Recommendations box.
        if ( !empty($recommends_problems['recommends']) ) {
          ?>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Recommendations', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            foreach ( $recommends_problems['recommends'] as $key => $values ) {
              foreach ( $values as $val ) {
                ?>
            <span class="wd-group">
              <div class="notice inline notice-warning is-dismissible" data-value="<?php echo $val['key']; ?>">
                <p><?php echo $val['message']; ?></p>
              </div>
            </span>
                <?php
              }
            }
            ?>
          </div>
        </div>
          <?php
        }
        ?>
      </div>
    </div>
    <?php

    return ob_get_clean();
  }
}
