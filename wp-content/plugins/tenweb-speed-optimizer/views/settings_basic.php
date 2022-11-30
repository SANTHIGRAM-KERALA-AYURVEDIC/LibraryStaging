<?php
$two_domain_id = get_site_option('tenweb_domain_id');
$two_manage_url = trim(TENWEB_DASHBOARD, '/' ) . '/websites/'. $two_domain_id . '/booster/frontend' . '?from_plugin=' . \TenWebOptimizer\OptimizerUtils::FROM_PLUGIN;
$two_upgrade_link = trim(TENWEB_DASHBOARD, '/' ) . '/upgrade-plan' . '?from_plugin=' . \TenWebOptimizer\OptimizerUtils::FROM_PLUGIN;
$two_upgrade_link_pricing = trim(TENWEB_DASHBOARD, '/' ) . '/upgrade-plan' . '?from_plugin=' . \TenWebOptimizer\OptimizerUtils::FROM_PLUGIN;
$two_wp_plugin_url = 'https://wordpress.org/support/plugin/tenweb-speed-optimizer/';
$two_disconnect_link = get_admin_url() . 'admin.php?page=two_settings_page&disconnect=1';
$two_current_user = wp_get_current_user();
$username = get_site_option(TENWEB_PREFIX . '_user_info') ? get_site_option(TENWEB_PREFIX . '_user_info')['client_info']['name'] : $two_current_user->display_name;
$two_flow_finished = get_option("two_flow_status") != 1 ? TRUE : FALSE;

if ( \TenWebOptimizer\OptimizerUtils::is_paid_user() ) {
  $two_plan_name = __('Paid Plan', 'tenweb-speed-optimizer');
  if (TENWEB_SO_HOSTED_ON_10WEB) {
      $two_plan_description_1 = __('The plugin is now optimizing your website.', 'tenweb-speed-optimizer');
      $two_plan_description_2 = __('', 'tenweb-speed-optimizer');
  } else {
      $two_plan_description_1 = __('The plugin is now optimizing your website.', 'tenweb-speed-optimizer');
      $two_plan_description_2 = __('Manage optimization settings and assign custom rules from the ' . TWO_SO_ORGANIZATION_NAME . ' dashboard.', 'tenweb-speed-optimizer');
      if(strtolower(TWO_SO_ORGANIZATION_NAME) != '10web'){
          $two_plan_description_2 = "";
      }
  }
  $two_contact_text = __('Please contact our support via', 'tenweb-speed-optimizer');
  $two_contact_link_text = __('Live Chat', 'tenweb-speed-optimizer');
  $two_contact_link = $two_manage_url . '&open=livechat';
  $full_cont = "two-full-width";
  $half_cont = "two-half-width";
}
else {
  $two_plan_name = __('Free Plan', 'tenweb-speed-optimizer');
  $two_plan_description_1 = __('The plugin is now optimizing your website.', 'tenweb-speed-optimizer');
  $two_plan_description_2 = __('Manage optimization settings from the ' . TWO_SO_ORGANIZATION_NAME . ' dashboard.', 'tenweb-speed-optimizer');
  if (strtolower(TWO_SO_ORGANIZATION_NAME) != '10web' ){
        $two_plan_description_2 = "";
  }
  $two_contact_text = __('Please create a topic in', 'tenweb-speed-optimizer');
  $two_contact_link_text = __('WordPress.org', 'tenweb-speed-optimizer');
  $two_contact_link = $two_wp_plugin_url;
  $full_cont = "";
  $half_cont = "";
}
$two_finish_opt_url = add_query_arg(array('two_setup' => 1), get_home_url());
$compressed_pages = count(\TenWebOptimizer\OptimizerUtils::getCriticalPages());
$images_count = get_transient("two_images_count");
$compressed_iamges = ( !empty($images_count) && isset($images_count['optimized_images_count']) ) ? (int) $images_count['optimized_images_count'] : '';
$empty_images_count_transient = empty($images_count) ? 'two-settings-basic two_empty_images_count' : '';
$free_plan_limit = 6;
if ( $compressed_pages >= $free_plan_limit ) {
  $reached_the_limit = 'two-reached-limit';
  $limit_text = __('You have reached the Free plan limit.', 'tenweb-speed-optimizer');
}
else {
  $reached_the_limit = '';
  $left_pages = $free_plan_limit - $compressed_pages;
  $single = __('You can optimize %d more page within%sthe Free Plan limit.', 'tenweb-speed-optimizer');
  $plural = __('You can optimize %d more pages within%sthe Free Plan limit.', 'tenweb-speed-optimizer');
  $limit_text =  wp_sprintf(_n($single, $plural, $left_pages, 'photo-gallery'), $left_pages, '<br />');
}
?>
<script>
  jQuery(document).ready(function() {
    jQuery('.two-faq-item').on('click', function() {
      jQuery(this).toggleClass('active');
    });
    jQuery('.two-disconnect-link a').on('click', function() {
      jQuery('.two-disconnect-popup').appendTo('body').addClass('open');
      return false;
    });
    jQuery('.two-button-cancel, .two-close-img').on('click', function() {
      jQuery('.two-disconnect-popup').removeClass('open');
      return false;
    });
  });
</script>
<div class="two-container connected" dir="ltr">
  <?php include_once ('two_header.php'); ?>
  <div class="two-body-container">
    <?php
    if ( $two_flow_finished ) {
      ?>
      <div class="two-body">
        <div class="two-greeting">
          <img src="<?php echo TENWEB_SO_URL; ?>/assets/images/waving_hand.png" alt="Hey" class="two-waving-hand"/>
          <?php if ( TENWEB_SO_HOSTED_ON_10WEB ) { ?>
            <?php _e( 'Hey there!', 'tenweb-speed-optimizer' ); ?>
          <?php } else {?>
              <?php if (strtolower(TWO_SO_ORGANIZATION_NAME) != '10web' ):?>
                  <?php echo esc_html( sprintf( __( 'Hey %s!', 'tenweb-speed-optimizer' ), $username, $two_plan_name ) ); ?>
              <?php else:?>
                  <?php echo esc_html( sprintf( __( 'Hey %s! You are on a %s.', 'tenweb-speed-optimizer' ), $username, $two_plan_name ) ); ?>
              <?php endif?>
          <?php } ?>
        </div>
        <div class="two-plugin-status">
          <?php if ( TENWEB_SO_HOSTED_ON_10WEB ) { ?>
              <?php _e(TWO_SO_ORGANIZATION_NAME .  ' Booster is Active', 'tenweb-speed-optimizer'); ?>
          <?php } else {?>
              <?php _e(TWO_SO_ORGANIZATION_NAME .  ' Booster is Active', 'tenweb-speed-optimizer'); ?>
          <?php } ?>
        </div>
        <div class="two-plugin-description">
          <?php echo esc_html($two_plan_description_1); ?>
          <br/>
          <?php echo esc_html($two_plan_description_2); ?>
        </div>
        <?php if ( !TENWEB_SO_HOSTED_ON_10WEB && strtolower(TWO_SO_ORGANIZATION_NAME) === '10web' ) { ?>
          <a href="<?php echo esc_url($two_manage_url); ?>" target="_blank"
             class="two-button two-button-green"><?php _e('MANAGE', 'tenweb-speed-optimizer'); ?></a>
        <?php } ?>
      </div>
      <?php
    }
    else {
      ?>
      <div class="two-body">
        <div class="two-not-finished-notice">
          <?php _e('Optimization not finished', 'tenweb-speed-optimizer'); ?>
        </div>
        <div class="two-plugin-status">
          <?php _e('Complete your website optimization.', 'tenweb-speed-optimizer'); ?>
        </div>
        <div class="two-plugin-description">
          <?php _e('Your website wasn’t optimized.', 'tenweb-speed-optimizer'); ?>
          <br/>
          <?php _e('Please <a href="' . TENWEB_DASHBOARD . '?flow_contact_us=1' . '">contact our support team</a> to complete the optimization.', 'tenweb-speed-optimizer'); ?>
          <br/>
          <?php _e('If you already have, we’ll be in touch soon.', 'tenweb-speed-optimizer'); ?>
        </div>
        <?php if ( !TENWEB_SO_HOSTED_ON_10WEB ) { ?>
          <a href="<?php echo esc_url($two_finish_opt_url); ?>" target="_blank" class="two-button two-button-green">
            <?php _e('FINISH OPTIMIZATION', 'tenweb-speed-optimizer'); ?>
          </a>
        <?php } ?>
      </div>
      <?php
    }
    if ( (!TENWEB_SO_HOSTED_ON_10WEB && strtolower(TWO_SO_ORGANIZATION_NAME) === '10web') ) {
      if ( $two_flow_finished ) {
        $score = get_option('two-front-page-speed');
        if ( empty($score) ) {
          $score = array();
        }
        if ( empty($score['previous_score']) ) {
          $score['previous_score'] = array(
            'desktop_score' => 0,
            'desktop_tti' => '',
            'mobile_score' => 0,
            'mobile_tti' => '',
            'date' => '',
          );
        }
        if ( empty($score['current_score']) ) {
          $score['current_score'] = array(
            'desktop_score' => 0,
            'desktop_tti' => '',
            'mobile_score' => 0,
            'mobile_tti' => '',
            'date' => '',
          );
        }
      ?>
      <div class="two-optimized-homepage-and-available-pro-container <?php echo esc_attr($full_cont); ?>">
        <div class="two-section-with-border two-optimized-homepage-container <?php echo esc_attr($full_cont); ?>">
          <div class="two-optimized-homepage-header">
            <p class="two-settings_title">
              <?php _e('Your optimized homepage score:', 'tenweb-speed-optimizer'); ?>
            </p>
            <p class="two-settings_title two-cache-link two_clear_cache">
              <?php _e('Clear cache', 'tenweb-speed-optimizer'); ?>
            </p>
          </div>
          <div class="two-homepage-scores">
            <div class="two-before-score-section <?php echo esc_attr($half_cont); ?>">
              <p class="two-homepage-score_title">
                <?php _e('Before optimization', 'tenweb-speed-optimizer'); ?>
              </p>
              <div class="two-homepage-score-each">
                <div class="two-homepage-score">
                  <div class="two-score-circle circle two_circle_with_bg"
                       data-size="40"
                       data-thickness="2"
                       data-score="<?php echo esc_attr( $score['previous_score']['mobile_score'] ); ?>"
                       data-loading-time="<?php echo esc_attr( $score['previous_score']['mobile_tti'] ); ?>">
                    <p class="two-score-circle-animated circle_animated"></p>
                  </div>
                </div>
                <div class="two-homepage-score-text">
                  <p class="two-homepage-score_title two-homepage-score_view">
                    <?php _e('Mobile score', 'tenweb-speed-optimizer'); ?>
                  </p>
                  <p class="two-homepage-score_title two-homepage-score_time">
                    <?php _e('Load time: ', 'tenweb-speed-optimizer'); ?>
                    <span class="two-load-time"><?php echo esc_html( $score['previous_score']['mobile_tti'] ); ?></span>s
                  </p>
                </div>
              </div>
              <div class="two-homepage-score-each">
                <div class="two-homepage-score">
                  <div class="two-score-circle circle two_circle_with_bg"
                       data-size="40"
                       data-thickness="2"
                       data-score="<?php echo esc_attr( $score['previous_score']['desktop_score'] ); ?>"
                       data-loading-time="<?php echo esc_attr( $score['previous_score']['desktop_tti'] ); ?>">
                    <p class="two-score-circle-animated circle_animated"></p>
                  </div>
                </div>
                <div class="two-homepage-score-text">
                  <p class="two-homepage-score_title  two-homepage-score_view">
                    <?php _e('Desktop score', 'tenweb-speed-optimizer'); ?>
                  </p>
                  <p class="two-homepage-score_title two-homepage-score_time">
                    <?php _e('Load time: ', 'tenweb-speed-optimizer'); ?>
                    <span class="two-load-time"><?php echo esc_html( $score['previous_score']['desktop_tti'] ); ?></span>s
                  </p>
                </div>
              </div>
            </div>
            <div class="two-after-score-section <?php echo esc_attr($half_cont); ?>">
              <p class="two-homepage-score_title">
                <?php _e('After 10Web Booster optimization', 'tenweb-speed-optimizer'); ?>
              </p>
              <div class="two-homepage-score-each">
                <div class="two-homepage-score">
                  <div class="two-score-circle circle two_circle_with_bg"
                       data-size="40"
                       data-thickness="2"
                       data-score="<?php echo esc_attr( $score['current_score']['mobile_score'] ); ?>"
                       data-loading-time="<?php echo esc_attr( $score['current_score']['mobile_tti'] ); ?>">
                    <p class="two-score-circle-animated circle_animated"></p>
                  </div>
                </div>
                <div class="two-homepage-score-text">
                  <p class="two-homepage-score_title two-homepage-score_view">
                    <?php _e('Mobile score', 'tenweb-speed-optimizer'); ?>
                  </p>
                  <p class="two-homepage-score_title two-homepage-score_time">
                    <?php _e('Load time: ', 'tenweb-speed-optimizer'); ?>
                    <span class="two-load-time"><?php echo esc_html( $score['current_score']['mobile_tti'] ); ?></span>s
                  </p>
                </div>
              </div>
              <div class="two-homepage-score-each">
                <div class="two-homepage-score">
                  <div class="two-score-circle circle two_circle_with_bg"
                       data-size="40"
                       data-thickness="2"
                       data-score="<?php echo esc_attr( $score['current_score']['desktop_score'] ); ?>"
                       data-loading-time="<?php echo esc_attr( $score['current_score']['desktop_tti'] ); ?>">
                    <p class="two-score-circle-animated circle_animated"></p>
                  </div>
                </div>
                <div class="two-homepage-score-text">
                  <p class="two-homepage-score_title two-homepage-score_view">
                    <?php _e('Desktop score', 'tenweb-speed-optimizer'); ?>
                  </p>
                  <p class="two-homepage-score_title two-homepage-score__time">
                    <?php _e('Load time: ', 'tenweb-speed-optimizer'); ?>
                    <span class="two-load-time"><?php echo esc_html( $score['current_score']['desktop_tti'] ); ?></span>s
                  </p>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (!\TenWebOptimizer\OptimizerUtils::is_paid_user()) {
            ?>
          <div class="two-optimized-homepage-notice <?php echo esc_attr($reached_the_limit); ?>">
            <?php echo wp_kses_post( $limit_text ); ?>
          </div>
            <?php
          }
          ?>
          <div class="two-optimiziation-info">
            <div class="two-optimized-page">
              <p class="two-settings_title two-title-with-dot">
                <?php _e('Optimized pages', 'tenweb-speed-optimizer'); ?>
              </p>
              <p class="two-settings_title">
                <?php
                $terms_count = (int)get_terms(array('fields' => 'count', 'hide_empty' => false));
                $total_pages = wp_count_posts( 'page' )->publish + wp_count_posts( 'post' )->publish + $terms_count;
                $total_pages_all = wp_count_posts( 'page' )->publish + wp_count_posts( 'post' )->publish + $terms_count;
                if (!\TenWebOptimizer\OptimizerUtils::is_paid_user()) {
                  if ( $total_pages >= 6 ) {
                    $total_pages = 6;
                  }
                  if ( $compressed_pages >= 6 ) {
                    $compressed_pages = 6;
                  }
                }
                echo esc_html( sprintf(__('%d of %d', 'tenweb-speed-optimizer'), $compressed_pages, $total_pages) ); ?>
              </p>
            </div>
            <?php
            if (!\TenWebOptimizer\OptimizerUtils::is_paid_user()) {
              ?>
            <div class="two-line_container"><span class="two-size_<?php echo esc_attr($compressed_pages); ?>"></span></div>
              <?php
            }
            ?>
            <div class="two-optimized-images">
              <p class="two-settings_title two-title-with-dot">
                <?php _e('Optimized images', 'tenweb-speed-optimizer'); ?>
              </p>
              <p class="two-settings_title <?php echo esc_attr( $empty_images_count_transient ); ?>">
                <?php echo esc_html( $compressed_iamges ); ?>
              </p>
            </div>
          </div>
        </div>
        <?php
       if ( !\TenWebOptimizer\OptimizerUtils::is_paid_user() ) {
        $black_friday_on = true;
        if ($black_friday_on){
            $black_friday_upgrade_button = $two_upgrade_link_pricing . '?two_comes_from=MainPageUpgradeButton';
            $black_friday_total_pages = (int)$total_pages_all;
            $black_friday_total_images = (int)$images_count['total_images_count'];?>
            <div class="two-section-with-border two-get-pro-container two_black_friday_offer">
                <?php require 'two_black_friday.php'; ?>
            </div>
        <?php } else { ?>
            <div class="two-section-with-border two-get-pro-container">
              <p class="two-settings_title">
                <?php _e('Automatically optimize all pages with Booster Pro:', 'tenweb-speed-optimizer'); ?>
              </p>
              <div class="two-available-pro-listing">
                <ul class="two-available-pro-list">
                  <li class="two-available-pro-list-each-diamond two-settings_title">
                    <?php _e('Entire website optimization', 'tenweb-speed-optimizer'); ?>
                  </li>
                  <li class="two-available-pro-list-each-point two-settings_title">
                    <?php sprintf(__('%s pages and all %s images', 'tenweb-speed-optimizer'),(int)$total_pages_all, (int)$images_count['total_images_count']); ?>
                  </li>
                  <li class="two-available-pro-list-each-diamond two-settings_title">
                    <?php _e('Optimization of unlimited new pages and images', 'tenweb-speed-optimizer'); ?>
                  </li>
                  <li class="two-available-pro-list-each-diamond two-settings_title">
                    <?php _e('Cloudflare Enterprise (soon)', 'tenweb-speed-optimizer'); ?>
                  </li>
                  <li class="two-available-pro-list-each-point two-settings_title">
                    <?php _e('Enterprise CDN', 'tenweb-speed-optimizer'); ?>
                  </li>
                  <li class="two-available-pro-list-each-point two-settings_title">
                    <?php _e('DDoS protection and WAF', 'tenweb-speed-optimizer'); ?>
                  </li>
                  <li class="two-available-pro-list-each-diamond two-settings_title">
                    <?php _e('Automatic caching and cache warmup', 'tenweb-speed-optimizer'); ?>
                  </li>
                  <li class="two-available-pro-list-each-diamond two-settings_title">
                    <?php _e('24/7 priority customer support', 'tenweb-speed-optimizer'); ?>
                  </li>
                </ul>
              </div>
              <div class="two-available-pro-button-container">
                <a href="<?php echo esc_url($two_upgrade_link) . '&two_comes_from=MainPageUpgradeButton'; ?>"
                   class="two-button two-button-green two-available-pro-button"><?php _e('UPGRADE', 'tenweb-speed-optimizer'); ?></a>
              </div>
            </div>
            <?php
            }
        }
        ?>
      </div>
      <?php
      }
      ?>
      <div class="two-disconnect-link">
        <img src="<?php echo TENWEB_SO_URL; ?>/assets/images/check_solid.svg" alt="Connected" class="two-connected-img" />
        <b><?php _e('Site is connected', 'tenweb-speed-optimizer'); ?></b>
        <a href="<?php echo esc_url( $two_disconnect_link ); ?>"><?php _e('Disconnect from ' . TWO_SO_ORGANIZATION_NAME, 'tenweb-speed-optimizer'); ?></a>
      </div>
      <div class="two-wp-link">
        <b><?php _e('Have a question?', 'tenweb-speed-optimizer'); ?></b>
        <span><?php echo esc_html( $two_contact_text ); ?> <a href="<?php echo esc_url( $two_contact_link ); ?>" target="_blank"><?php echo esc_html( $two_contact_link_text ); ?></a></span>
      </div>
      <div class="two-disconnect-popup">
        <div class="two-disconnect-popup-body">
          <div class="two-disconnect-popup-title">
            <?php _e('Disconnect Website', 'tenweb-speed-optimizer'); ?>
          </div>
          <div class="two-disconnect-popup-content">
            <p>
              <?php _e('Disconnecting a website from ' . TWO_SO_ORGANIZATION_NAME . ' will rollback all optimization and caching configurations and negatively affect your PageSpeed.', 'tenweb-speed-optimizer'); ?>
            </p>
            <p>
              <?php _e('By disconnecting you will revert the following:', 'tenweb-speed-optimizer'); ?>
            </p>
            <div class="two-disconnect-popup-list">
              <p>
                <?php _e('PageSpeed score', 'tenweb-speed-optimizer'); ?>
              </p>
              <p>
                <?php _e('Improved Core Web Vitals', 'tenweb-speed-optimizer'); ?>
              </p>
              <p>
                <?php _e('Image optimization', 'tenweb-speed-optimizer'); ?>
              </p>
              <p>
                <?php _e('Caching', 'tenweb-speed-optimizer'); ?>
              </p>
            </div>
          </div>
          <div class="two-disconnect-popup-button-container">
            <a href="#" class="two-button two-disconnect-popup-button two-button-cancel"><?php _e('STAY CONNECTED', 'tenweb-speed-optimizer'); ?></a>
            <a href="<?php echo esc_url( $two_disconnect_link ); ?>" class="two-button two-disconnect-popup-button two-button-disconnect"><?php _e('DISCONNECT', 'tenweb-speed-optimizer'); ?></a>
          </div>
          <img src="<?php echo TENWEB_SO_URL; ?>/assets/images/close.svg" alt="Close" class="two-close-img" />
        </div>
      </div>
      <?php
    }
    ?>
  </div>
</div>
