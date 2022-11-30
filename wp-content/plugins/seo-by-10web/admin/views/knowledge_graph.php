<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeoknowledge_graphView extends WDSeoAdminView {

  public function __construct() {
    add_action('wdseo_options_tabs', array($this, 'tab_title'));
    add_action('wdseo_options_tabs_content', array($this, 'body'));
  }

  public function tab_title() {
    ?>
    <li class="tabs">
      <a href="#wdseo_tab_knowledge_graph" class="wdseo-tablink"><?php _e( 'Knowledge Graph', WD_SEO_PREFIX ); ?></a>
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
    wp_enqueue_style(WD_SEO_PREFIX . '_select2');
    wp_enqueue_script(WD_SEO_PREFIX . '_select2');
    // Add all scripts, styles necessary to use media library.
    wp_enqueue_media();
    ob_start();
    ?>
    <div id="wdseo_tab_knowledge_graph" class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php echo 'Social' ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Enable Feature', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->knowledge_check, 1); ?> id="wd-knowledge_check-1" class="wd-radio" value="1" name="wd_settings[knowledge_check]" type="radio" />
              <label class="wd-label-radio" for="wd-remove-cat-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->knowledge_check, 0); ?> id="wd-knowledge_check-0" class="wd-radio" value="0" name="wd_settings[knowledge_check]" type="radio" />
              <label class="wd-label-radio" for="wd-knowledge_check-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_type"><?php esc_html_e('Person or Organization','WD_SEO_PREFIX'); ?></label>

              <?php
              $selected = isset( $options->knowledge->knowledge_type ) ? $options->knowledge->knowledge_type : NULL;
              echo '<select id="knowledge_type" name="wd_settings[knowledge][knowledge_type]" data-placeholder="'.esc_attr__( 'Choose a knowledge type', 'WD_SEO_PREFIX' ).'"	class="location-input wc-enhanced-select dropdown">';
              echo ' <option ';
              if ('None' == $selected ) echo 'selected="selected"';
              echo ' value="none">'. __("None (will disable this feature)","WD_SEO_PREFIX") .'</option>';
              echo ' <option ';
              if ('Person' == $selected ) echo 'selected="selected"';
              echo ' value="Person">'. __("Person","WD_SEO_PREFIX") .'</option>';
              echo '<option ';
              if ('Organization' == $selected ) echo 'selected="selected"';
              echo ' value="Organization">'. __("Organization","WD_SEO_PREFIX") .'</option>';
              echo '</select>';
              ?>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_name"><?php esc_html_e( 'Your Name/Organization', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_name" class="location-input" name="wd_settings[knowledge][knowledge_name]" value="<?php echo isset($options->knowledge->knowledge_name) ? $options->knowledge->knowledge_name : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_img"><?php esc_html_e( 'Your Photo/Organization Logo', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_img" class="location-input" name="wd_settings[knowledge][knowledge_img]" placeholder="<?php esc_html_e('eg: https://www.your-site.com/logo.png', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_img) ? $options->knowledge->knowledge_img : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_fb"><?php esc_html_e( 'Facebook Page URL', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_fb" class="location-input" name="wd_settings[knowledge][knowledge_fb]" placeholder="<?php esc_html_e('eg: https://www.facebook.com/your-page','WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_fb) ? $options->knowledge->knowledge_fb : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_tw"><?php esc_html_e( 'Twitter Username', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_tw" class="location-input" name="wd_settings[knowledge][knowledge_tw]" placeholder="<?php esc_html_e('eg: @your-username', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_tw) ? $options->knowledge->knowledge_tw : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_pin"><?php esc_html_e( 'Pinterest URL', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_pin" class="location-input" name="wd_settings[knowledge][knowledge_pin]" placeholder="<?php esc_html_e('eg: https://pinterest.com/your-url/', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_pin) ? $options->knowledge->knowledge_pin : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_insta"><?php esc_html_e( 'Instagram URL', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_insta" class="location-input" name="wd_settings[knowledge][knowledge_insta]" placeholder="<?php esc_html_e('eg: https://www.instagram.com/your-url/', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_insta) ? $options->knowledge->knowledge_insta : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_yt"><?php esc_html_e( 'YouTube URL', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_yt" class="location-input" name="wd_settings[knowledge][knowledge_yt]" placeholder="<?php esc_html_e('eg: https://www.youtube.com/your-url/', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_yt) ? $options->knowledge->knowledge_yt : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_li"><?php esc_html_e( 'LinkedIn URL', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_li" class="location-input" name="wd_settings[knowledge][knowledge_li]" placeholder="<?php esc_html_e('eg: http://linkedin.com/company/your-url/', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_li) ? $options->knowledge->knowledge_li : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_sound"><?php esc_html_e( 'Soundcloud URL', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_sound" class="location-input" name="wd_settings[knowledge][knowledge_sound]" placeholder="<?php esc_html_e('eg: https://soundcloud.com/your-url', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_sound) ? $options->knowledge->knowledge_sound : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_tu"><?php esc_html_e( 'Tumblr URL', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_tu" name="wd_settings[knowledge][knowledge_tu]" placeholder="<?php esc_html_e('eg: https://your-site.tumblr.com/', 'WD_SEO_PREFIX'); ?>" value="<?php echo isset($options->knowledge->knowledge_tu) ? $options->knowledge->knowledge_tu : ''; ?>" />
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_phone"><?php esc_html_e( 'Phone Number (for Organizations Only)', 'WD_SEO_PREFIX' ); ?></label>
              <input type="text" id="knowledge_phone" class="location-input" name="wd_settings[knowledge][knowledge_phone]" value="<?php echo isset($options->knowledge->knowledge_phone) ? $options->knowledge->knowledge_phone : ''; ?>" />
              <p class="description"><?php esc_html_e('Internationalized version required.', 'WD_SEO_PREFIX'); ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_contact_type"><?php esc_html_e('Contact Type (for Organizations Only','WD_SEO_PREFIX'); ?></label>
              <?php
              $selected = isset( $options->knowledge->knowledge_contact_type ) ? $options->knowledge->knowledge_contact_type : NULL;

              echo '<select id="knowledge_contact_type" name="wd_settings[knowledge][knowledge_contact_type]">';
                echo ' <option ';
                if ('customer support' == $selected) echo 'selected="selected"';
                echo ' value="customer support">'. __("Customer support","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('technical support' == $selected) echo 'selected="selected"';
                echo ' value="technical support">'. __("Technical support","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('billing support' == $selected) echo 'selected="selected"';
                echo ' value="billing support">'. __("Billing support","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('bill payment' == $selected) echo 'selected="selected"';
                echo ' value="bill payment">'. __("Bill payment","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('sales' == $selected) echo 'selected="selected"';
                echo ' value="sales">'. __("Sales","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('credit card support' == $selected) echo 'selected="selected"';
                echo ' value="credit card support">'. __("Credit card support","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('emergency' == $selected) echo 'selected="selected"';
                echo ' value="emergency">'. __("Emergency","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('baggage tracking' == $selected) echo 'selected="selected"';
                echo ' value="baggage tracking">'. __("Baggage tracking","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('roadside assistance' == $selected) echo 'selected="selected"';
                echo ' value="roadside assistance">'. __("Roadside assistance","WD_SEO_PREFIX") .'</option>';
                echo '<option ';
                if ('package tracking' == $selected) echo 'selected="selected"';
                echo ' value="package tracking">'. __("Package tracking","WD_SEO_PREFIX") .'</option>';
                echo '</select>';
              ?>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="knowledge_contact_option"><?php esc_html_e('Contact Options (for Organizations Only)','WD_SEO_PREFIX'); ?></label>
              <?php
              $selected = isset($options->knowledge->knowledge_contact_option) ? $options->knowledge->knowledge_contact_option : NULL;

              echo '<select id="knowledge_contact_option" name="wd_settings[knowledge][knowledge_contact_option]">';
              echo ' <option ';
                if ('None' == $selected) echo 'selected="selected"';
                echo ' value="None">'. __("None","WD_SEO_PREFIX") .'</option>';
              echo ' <option ';
                if ('TollFree' == $selected) echo 'selected="selected"';
                echo ' value="TollFree">'. __("Toll Free","WD_SEO_PREFIX") .'</option>';
              echo '<option ';
                if ('HearingImpairedSupported' == $selected) echo 'selected="selected"';
                echo ' value="HearingImpairedSupported">'. __("Hearing impaired supported","WD_SEO_PREFIX") .'</option>';
              echo '</select>';
              ?>
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php
    echo ob_get_clean();
  }
}

new WDSeoknowledge_graphView();