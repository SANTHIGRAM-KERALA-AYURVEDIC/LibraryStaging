<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeorobotsView extends WDSeoAdminView {
  public function __construct() {
    if ( WDSeo()->options->google_site_verification != '' ) {
      add_action('wdseo_options_tabs', array($this, 'tab_title'));
      add_action('wdseo_options_tabs_content', array($this, 'body'));
    }
  }

  public function tab_title() {
    ?>
    <li class="tabs">
      <a href="#wdseo_tab_robotstxt" class="wdseo-tablink"><?php _e( 'robots.txt', WD_SEO_PREFIX ); ?></a>
    </li>
    <?php
  }

  /**
   * Generate page body.
   *
   * @param object $options
   *
   * @return string Body html.
   */
  public function body($options) {
    ob_start();
    ?>
      <div id="wdseo_tab_robotstxt" class="wd-table">
          <div class="wd-table-col wd-table-col-50 wd-table-col-right">
              <div class="wd-box-section">
                  <div class="wd-box-title">
                      <strong><?php _e('robots.txt', WD_SEO_PREFIX); ?></strong>
                  </div>
                  <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Enable Robots', WD_SEO_PREFIX); ?></label>
			  <input value="0" name="wd_settings[enable_robots]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
                <input <?php checked($options->enable_robots, 1); ?> id="wd-enable_robots" class="wd-radio" value="1" name="wd_settings[enable_robots]" type="checkbox" />
              <label class="wd-label-radio" for="wd-enable_robots"><?php _e('Enable robots.txt virtual file', WD_SEO_PREFIX); ?></label>
            </span>
                      <span class="wd-group">
              <button id="wdseo_flush_permalinks" type="button" class="button"><?php _e('Flush permalinks', WD_SEO_PREFIX); ?></button>
            </span>
                      <span class="wd-group">
              <label class="wd-label"><?php _e('Virtual Robots.txt file', WD_SEO_PREFIX); ?></label>
              <textarea id="wd-robots_file" name="wd_settings[robots_file]" class="wd-textarea"><?php echo $options->robots_file; ?></textarea>
			  <div class="wrap-tags">
				  <span class="tag-title" data-tag="User-agent: SemrushBot
    Disallow: /
User-agent: SemrushBot-SA
    Disallow: /"><span class="dashicons dashicons-plus"></span><?php _e('Block SemrushBot', WD_SEO_PREFIX); ?></span>
				  <span class="tag-title" data-tag="User-agent: MJ12bot
    Disallow: /"><span class="dashicons dashicons-plus"></span><?php _e('Block MajesticSEOBot', WD_SEO_PREFIX); ?></span>
				  <span class="tag-title" data-tag="User-agent: AhrefsBot
    Disallow: /"><span class="dashicons dashicons-plus"></span><?php _e('Block AhrefsBot', WD_SEO_PREFIX); ?></span>
				  <span class="tag-title" data-tag="Sitemap: <?php echo site_url(); ?>/sitemaps.xml"><span class="dashicons dashicons-plus"></span><?php _e('Link to your sitemap', WD_SEO_PREFIX); ?></span>
				  <span class="tag-title" data-tag="User-agent: Mediapartners-Google
    Disallow: "><span class="dashicons dashicons-plus"></span><?php _e('Allow Google AdSense bot', WD_SEO_PREFIX); ?></span>
				  <span class="tag-title" data-tag="User-agent: Googlebot-Image
    Disallow: "><span class="dashicons dashicons-plus"></span><?php _e('Allow Google Image bot', WD_SEO_PREFIX); ?></span>
				  <span class="tag-title" data-tag="User-agent: *
    Disallow: /wp-admin/
    Allow: /wp-admin/admin-ajax.php "><span class="dashicons dashicons-plus"></span><?php _e('Default WP rules', WD_SEO_PREFIX); ?></span>
			  </div>
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
  new WDSeorobotsView();
});
