<?php

namespace TenWebOptimizer;

use TenWebOptimizer\WebPageCache\OptimizerWebPageCache;

/**
 * Class OptimizerAdminBar
 */
class OptimizerAdminBar
{

    /* 1 - active and connected, 2 - test mode, 3 - disconnected, 4 - pro case, 5 - abandoned  */
    public $two_booster_status = 1;

    /* Total pages count */
    public $total_pages_count;

    /* Not optimized pages count */
    public $notoptimized_pages_count;

    /* Optimized pages count */
    public $optimized_pages_count;

    /* Total count of optimized images */
    public $optimized_images_count;

    /* Total count of images */
    public $total_images_count;

    /* Domain Id */
    private $domain_id;

    /* Workspace Id */
    private $workspace_id;

    /* Booster active and connected */
    public const TWO_CONNECTED = 1;

    /* Booster active and in test mode */
    public const TWO_TEST_MODE = 2;

    /* Booster disconnected */
    public const TWO_DISCONNECTED = 3;

    /* Booster is PRO */
    public const TWO_PRO_CONNECTED = 4;

  /* Booster is ABANDONED */
    public const TWO_ABANDONED = 5;

    private $current_plan;

    private $empty_images_count_transient;

    function __construct( $wp_admin_bar ) {
      global $tenweb_plan_title;
      $tenweb_plan_title = strtolower( $tenweb_plan_title ) == 'speed' ? 'Free' : $tenweb_plan_title;
      $this->current_plan = $tenweb_plan_title;
      if(!OptimizerUrl::urlIsOptimizable(null, true) && !is_admin()){
            return;
      }
      $this->two_set_data();
      /* Case when page is frontend and user is Pro*/
      if( !is_admin() && $this->two_booster_status == 4 ) {
        return;
      }

      wp_enqueue_style( 'two_speed_css', TENWEB_SO_URL . '/assets/css/speed.css', array('two-open-sans'), TENWEB_SO_VERSION );
      wp_enqueue_script( 'two_circle_js', TENWEB_SO_URL . '/assets/js/circle-progress.js', array('jquery'), TENWEB_SO_VERSION );
      wp_enqueue_script( 'two_speed_js', TENWEB_SO_URL . '/assets/js/speed.js', array('jquery', 'two_circle_js'), TENWEB_SO_VERSION );
      wp_localize_script( 'two_speed_js', 'two_speed', array(
        'nonce' => wp_create_nonce('two_ajax_nonce'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'clearing' => __('Clearing...', 'tenweb-speed-optimizer'),
        'cleared' => __('Cleared cache', 'tenweb-speed-optimizer'),
        'clear' => __('Clear cache', 'tenweb-speed-optimizer'),
      ));

      $wp_admin_bar->add_menu(array(
                                'id' => 'two_adminbar_info',
                                'title' => $this->two_admin_menu(),
                                'meta' => array(
                                  'html' => $this->two_admin_bar_menu_content(),
                                ),
                              ));
    }

    /**
     * Set values to class variables.
    */
    public function two_set_data() {
        if ( is_admin() ) {
          $this->two_frontend = 0;
        }
        $this->optimized_pages_count = count(\TenWebOptimizer\OptimizerUtils::getCriticalPages());
        $count_pages = wp_count_posts('page');
        $count_posts = wp_count_posts( 'post' );
        $terms_count = (int)get_terms(array('fields' => 'count', 'hide_empty' => false));
        $this->total_pages_count = $count_pages->publish + $count_posts->publish + $terms_count;
        if ( $this->optimized_pages_count > $this->total_pages_count ) {
          $this->optimized_pages_count = $this->total_pages_count;
        }
        $this->notoptimized_pages_count = $this->total_pages_count - $this->optimized_pages_count;
        $this->workspace_id = (int)get_site_option(TENWEBIO_MANAGER_PREFIX . '_workspace_id', 0);
        $this->domain_id = (int)get_option(TENWEBIO_MANAGER_PREFIX . '_domain_id', 0);

        $two_settings = get_option("two_settings");
        $two_settings = json_decode($two_settings, 1);
        $this->two_booster_status = self::TWO_DISCONNECTED;
        if ( (defined('TWO_INCOMPATIBLE_ERROR') && TWO_INCOMPATIBLE_ERROR) || !OptimizerUtils::is_tenweb_booster_connected() ) {
            $this->two_booster_status = self::TWO_DISCONNECTED;
        } elseif ( \TenWebOptimizer\OptimizerUtils::is_paid_user() ) {
            $this->two_booster_status = self::TWO_PRO_CONNECTED;
        } elseif ( !empty($two_settings) ) {
            if ( isset($two_settings['two_test_mode']) && $two_settings['two_test_mode'] == 'on' ) {
                $this->two_booster_status = self::TWO_TEST_MODE;
            } elseif ( isset($two_settings['two_connected']) && $two_settings['two_connected'] == 1 ) {
                $two_flow_finished = get_option("two_flow_status") != 1 ? TRUE : FALSE;
                if( !$two_flow_finished ) {
                    $this->two_booster_status = self::TWO_ABANDONED;
                } else {
                    $this->two_booster_status = self::TWO_CONNECTED;
                }
            }
        }
        if ( $this->two_booster_status != 3 && $this->two_booster_status != 2 ) {
            $this->get_images_data_api();
        }
      }

    /**
     * Admin bar menu.
     *
     * @return string
    */
    public function two_admin_menu() {
        if ( !is_admin() && $this->two_booster_status != 3 && $this->two_booster_status != 2 ) {
            $img = '<img src="'.TENWEB_SO_URL.'/assets/images/logo_green.svg" />';
            $className = ' two_frontpage_not_optimized';
            if ( $this->two_is_page_optimized() ) {
              $className = ' two_frontpage_optimized';
            }
            $two_admin_bar_menu = '<div class="two_admin_bar_menu two_frontend"><div class="two_admin_bar_menu_header' . $className . '"><span class="two_hidden"></span>' . $img . TWO_SO_ORGANIZATION_NAME . " Booster" . '</div></div>';
        } else {
            if ( $this->two_booster_status == 1 ) {
                $img = '<img src="'.TENWEB_SO_URL.'/assets/images/logo_green.svg" />' . TWO_SO_ORGANIZATION_NAME . " Booster" . '<p class="two_page_count">' . $this->notoptimized_pages_count . '</p>';
            } elseif ( $this->two_booster_status == 4 ) {
                $img = '<img src="'.TENWEB_SO_URL.'/assets/images/logo_green.svg" />' . TWO_SO_ORGANIZATION_NAME . " Booster";
            } else {
                $img = '<img src="'.TENWEB_SO_URL.'/assets/images/logo_disconnect.svg" />' . TWO_SO_ORGANIZATION_NAME . " Booster";
            }
            $two_admin_bar_menu = '<div class="two_admin_bar_menu two_backend"><div class="two_admin_bar_menu_header">' . $img . '</div></div>';
        }
        return $two_admin_bar_menu;
    }

    /**
     * Adminbar menu content.
     *
     * @return string
    */
    public function two_admin_bar_menu_content()
    {
      $front_score_data = get_option("two-front-page-speed");
      $is_homepage_score = FALSE;
      if ( !empty($front_score_data) && isset($front_score_data['current_score']) ) {
        $is_homepage_score = TRUE;
        $mobile_score = $front_score_data['current_score']['mobile_score'];
        $mobile_score_duration = $front_score_data['current_score']['mobile_tti'].'s';
        $desktop_score = $front_score_data['current_score']['desktop_score'];
        $desktop_score_duration = $front_score_data['current_score']['desktop_tti'].'s';
      }

      $optimized_images_count = $this->optimized_images_count;
      $total_images_count = $this->total_images_count;
      $rest_page_count = (int)(6 - $this->optimized_pages_count);

      $free_reached = 1;
      if ( $this->optimized_pages_count < 6 ) {
        $free_reached = 0;
      }
      ob_start();
      /* Notification for in progress optimizing */
      $this->two_optimize_notification();
      ?>
      <div class="two_admin_bar_menu_main two_hidden">
        <?php
        /* Frontend and bosster is not disconnected or in test mode */
        if ( !is_admin() && $this->two_booster_status != 3 && $this->two_booster_status != 2 ) {
            if ( !$this->two_is_page_optimized() ) {
              $this->two_front_not_optimized_content();
            } else {
              $this->two_front_optimized_content();
            }
        } else {
            if ( $this->two_booster_status == 1 ) {
              ?>
              <div class="two_admin_bar_menu_content two_booster_on_free">
                <p class="two_info_row"><?php echo sprintf(__('Not optimized pages: %s', 'tenweb-speed-optimizer'), (int)$this->notoptimized_pages_count) ?></p>
                <p class="two_status_title"><?php echo sprintf(__('%s is ON', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>
                <div class="two_plan_container">
                  <p><?php echo sprintf(__('Current Plan: %s', 'tenweb-speed-optimizer'), esc_html($this->current_plan)) ?></p>
                  <a href="#" class="two_clear_cache"><?php _e('Clear cache', 'tenweb-speed-optimizer') ?></a>
                </div>
                <hr>

                <div class="two_score_success_container <?php echo $is_homepage_score ? '' : 'two_hidden'; ?>">
                <p class="two_score_title"><?php _e('Your optimized homepage score:', 'tenweb-speed-optimizer') ?></p>
                <div class="two_score_container">
                  <div class="two_score_container_mobile">
                    <div class="two-score-circle" data-score="<?php echo (int)$mobile_score; ?>" data-size="40" data-thickness="2" data-id="mobile">
                      <span class="two-score-circle-animated"></span>
                    </div>
                    <div class="two_score_info">
                      <p><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></p>
                      <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?> <span class="two_load_time"><?php echo esc_html($mobile_score_duration); ?></span></p>
                    </div>
                  </div>
                  <div class="two_score_container_desktop">
                    <div class="two-score-circle" data-size="40" data-score="<?php echo (int)$desktop_score; ?>" data-thickness="2" data-id="desktop">
                      <span class="two-score-circle-animated"></span>
                    </div>
                    <div class="two_score_info">
                      <p><?php _e('Desktop score', 'tenweb-speed-optimizer') ?></p>
                      <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?> <span class="two_load_time"><?php echo esc_html($desktop_score_duration); ?></span></p>
                    </div>

                  </div>
                </div>
                <div class="two_pages_count_info <?php echo esc_attr($free_reached) ? 'two_free_reached' : ''; ?>">
                  <?php
                  if ( !$free_reached ) {
                    echo sprintf(__('Optimize %s more pages within the Free Plan limit.', 'tenweb-speed-optimizer'), (int)$rest_page_count);
                  } else {
                    _e('You have reached the Free plan limit.', 'tenweb-speed-optimizer');
                  }
                  ?>
                </div>
                </div>
                <?php if ( !$is_homepage_score ) { ?>
                <div class="two_home_score_error">
                  <div class="two_recount_home_score_cont">
                    <p class="two_score_title"><?php _e('Your optimized homepage score:', 'tenweb-speed-optimizer') ?></p>
                    <span class="two_recount_score" data-post_id="front_page"></span>
                  </div>
                  <div class="two_home_score_error_info">
                    <?php echo sprintf(__('%sSomething Went Wrong.%s Please check if your website works properly & try again or contact us.', 'tenweb-speed-optimizer'), '<b>', '</b>'); ?>
                  </div>
                </div>
                <?php } ?>
                <div class="two_optimized_pages_info">
                  <p><?php _e('Optimized pages', 'tenweb-speed-optimizer') ?></p>
                  <?php if ( $free_reached ) { ?>
                    <p><?php _e('6 of 6', 'tenweb-speed-optimizer'); ?></p>
                  <?php } else { ?>
                    <p><?php echo sprintf(__('%s of %s', 'tenweb-speed-optimizer'), (int)$this->optimized_pages_count, (int)$this->total_pages_count); ?></p>
                  <?php } ?>
                </div>
                <?php if ( $free_reached ) { ?>
                  <div class="two_green_counter_line"></div>
                <?php } ?>
                <div class="two_optimized_images_info">
                  <p><?php _e('Optimized images', 'tenweb-speed-optimizer') ?></p>
                  <?php if( empty($optimized_images_count) && empty($total_images_count) ) { ?>
                    <p class="<?php echo esc_html( $this->empty_images_count_transient ); ?>">-</p>
                  <?php } else { ?>
                    <p><?php echo sprintf(__('%s of %s', 'tenweb-speed-optimizer'), (int)$optimized_images_count, (int)$total_images_count); ?></p>
                  <?php } ?>
                </div>
                <?php if ( !$free_reached ) {
                  $url = admin_url( 'edit.php?post_type=page' );
                  ?>
                  <a href="<?php echo esc_url($url); ?>" class="two_add_page_button"><?php _e('Optimize more pages', 'tenweb-speed-optimizer') ?></a>
                <?php } ?>
              </div>
              <?php
                $checkout_url = TENWEB_DASHBOARD . "/websites/".$this->domain_id."/booster/pro";
                $black_friday_on = true;
                if ($black_friday_on){
                    $black_friday_upgrade_button = trim(TENWEB_DASHBOARD, '/' ) . '/upgrade-plan'
                        . '?from_plugin=' . \TenWebOptimizer\OptimizerUtils::FROM_PLUGIN . '?two_comes_from=adminBarAfterLimit';
                    $black_friday_total_pages = (int)$this->total_pages_count;
                    $black_friday_total_images = (int)$total_images_count; ?>
                        <div class="two_pro_container two_black_friday_offer">
                    <?php require 'views/two_black_friday.php'; ?>
                        </div>
                <?php } else { ?>
                <div class="two_pro_container">
                  <p class="two_pro_container_title"><?php _e('Achieve more with Booster Pro:', 'tenweb-speed-optimizer') ?></p>
                  <p class="two_pro_option"><?php echo sprintf(__('Optimize all %s pages', 'tenweb-speed-optimizer'), (int)$this->total_pages_count); ?></p>
                  <p class="two_pro_option"><?php echo sprintf(__('Optimize all %s images', 'tenweb-speed-optimizer'), (int)$total_images_count); ?></p>
                  <p class="two_pro_option"><?php _e('Cloudflare CDN (coming soon)', 'tenweb-speed-optimizer') ?></p>
                  <a href="<?php echo esc_url($checkout_url . '?two_comes_from=adminBarAfterLimit'); ?>" target="_blank" class="two_add_page_button"><?php _e('Optimize entire website', 'tenweb-speed-optimizer') ?></a>
                </div>
                <?php
                }
            }
            elseif ( $this->two_booster_status == 2 ) {
              $this->two_booster_testmode_content();
            }
            elseif ( $this->two_booster_status == 3 ) {
              $this->two_booster_disconnect_content();
            }
            elseif ( $this->two_booster_status == 4 ) {
              $this->two_booster_pro_content();
            }
            elseif ( $this->two_booster_status == 5 ) {
              $this->two_booster_abandoned_content();
            }
        }
        ?>
      </div>
      <?php
      return ob_get_clean();
    }

    /* Adminbar menu content in case of booster disconnected */
    public function two_booster_disconnect_content() {
      $dashboard_url = TENWEB_DASHBOARD . "/websites/?open=livechat";
      ?>
      <div class="two_admin_bar_menu_content two_booster_disconnect">
        <p class="two_status_title"><?php echo sprintf(__('%s is disconnected', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>
        <p><?php echo sprintf(__('Your website is disconnected from %s service.', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>
        <p><?php echo sprintf(__('Please reconnect your website or %s for further assistance.', 'tenweb-speed-optimizer'), '<a href="' . esc_url($dashboard_url) . '" target="_blank">' . __("contact our Customer Care Team", "tenweb-speed-optimizer") . '</a>') ?></p>
      </div>
      <?php
    }

    /* Adminbar menu content in case of booster active in test mode */
    public function two_booster_testmode_content() {
      $settings_url = TENWEB_DASHBOARD . "/websites/" . $this->domain_id . "/booster/frontend?tab=settings";
      ?>
      <div class="two_admin_bar_menu_content two_booster_test">
        <p class="two_status_title"><?php echo sprintf(__('%s is in Test mode', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>
        <p><?php echo sprintf(__('Test mode temporarily disables %s <br>for website visitors.', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>
        <p><?php _e('Go to 10Web dashboard to manage Test mode settings and preview optimization levels.', 'tenweb-speed-optimizer') ?></p>
        <a href="<?php echo esc_url($settings_url); ?>" target="_blank" class="two_add_page_button"><?php _e('Manage settings', 'tenweb-speed-optimizer') ?></a>
      </div>
      <?php
    }

    /* Adminbar if booster plugin is PRO content */
    public function two_booster_pro_content() {
      $front_score_data = get_option("two-front-page-speed");

      $is_homepage_score = FALSE;
      if ( !empty($front_score_data) && isset($front_score_data['current_score']) ) {
        $is_homepage_score = TRUE;
      }
      ?>
      <div class="two_admin_bar_menu_content two_booster_on_free">
        <?php if ( !$is_homepage_score ) { ?>
        <p class="two_info_row two_success"><?php echo __('All pages are optimized', 'tenweb-speed-optimizer'); ?></p>
        <?php } ?>
        <p class="two_status_title"><?php echo sprintf(__('%s is ON', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>
        <div class="two_plan_container">
          <p><?php echo __('Current Plan: ' . $this->current_plan, 'tenweb-speed-optimizer'); ?></p>
          <a href="#" class="two_clear_cache"><?php _e('Clear cache', 'tenweb-speed-optimizer') ?></a>
        </div>
        <hr>
        <?php if ( $is_homepage_score ) { ?>
          <p class="two_score_title"><?php _e('Your optimized homepage score:', 'tenweb-speed-optimizer') ?></p>
          <div class="two_score_container">
            <div class="two_score_container_mobile">
              <div class="two-score-circle" data-score="<?php echo (int)$front_score_data['current_score']['mobile_score']; ?>" data-size="40" data-thickness="2" data-id="mobile">
                <span class="two-score-circle-animated"></span>
              </div>
              <div class="two_score_info">
                <p><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></p>
                <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); echo esc_html($front_score_data['current_score']['mobile_tti'].'s'); ?></p>
              </div>
            </div>
            <div class="two_score_container_desktop">
              <div class="two-score-circle" data-size="40" data-score="<?php echo (int)$front_score_data['current_score']['desktop_score']; ?>" data-thickness="2" data-id="desktop">
                <span class="two-score-circle-animated"></span>
              </div>
              <div class="two_score_info">
                <p><?php _e('Desktop score', 'tenweb-speed-optimizer') ?></p>
                <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); echo esc_html($front_score_data['current_score']['desktop_tti'].'s'); ?></p>
              </div>

            </div>
          </div>
          <div class="two_pages_count_info">
            <?php
              echo __('All pages are optimized', 'tenweb-speed-optimizer');
            ?>
          </div>
        <?php
        }
        else { ?>
          <div class="two_home_score_error">
            <div class="two_recount_home_score_cont">
              <p class="two_score_title"><?php _e('Your optimized homepage score:', 'tenweb-speed-optimizer') ?></p>
              <span class="two_recount_score" data-post_id="front_page"></span>
            </div>
            <div class="two_home_score_error_info">
              <?php echo sprintf(__('%sSomething Went Wrong.%s Please check if your website works properly & try again or contact us.', 'tenweb-speed-optimizer'), '<b>', '</b>'); ?>
            </div>
          </div>
        <?php } ?>
        <div class="two_optimized_pages_info">
          <p><?php _e('Optimized pages', 'tenweb-speed-optimizer') ?></p>
          <p><?php echo (int)$this->total_pages_count; ?></p>
        </div>
      </div>
      <?php
    }

    /* Frontend Adminbar menu content in case of page not optimized */
    public function two_front_not_optimized_content() {
      global $post;
      if ( empty($post) ) {
        return false;
      }

      $post_id = $post->ID;

      $posts_in_progress = $this->two_is_optimize_inprogress( $post_id );
      if ( $posts_in_progress ) {
          $this->two_front_optimize_in_progress_content( $post_id, TRUE );
      } else {
        $checkout_url = TENWEB_DASHBOARD . "/websites/".$this->domain_id."/booster/pro";
        ?>
          <div class="two_admin_bar_menu_content two_not_optimized_content">
            <p class="two_status_title"><?php echo sprintf(__('Optimize this page with %s', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>
            <p><?php echo sprintf(__('We found that this page isn’t optimized with %s.', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>
            <p><?php _e('Get a 90+ PageSpeed score, faster load times and smoother user experience by optimizing this page.', 'tenweb-speed-optimizer') ?></p>
            <a <?php echo ( $this->optimized_pages_count >= 6 ) ? 'href="'.esc_url($checkout_url).'"' : 'id="two_optimize_now_button"'; ?> data-post-id="<?php echo esc_attr($post_id); ?>" data-initiator="admin-bar" target="_blank"
               class="two_add_page_button"><?php _e('Optimize', 'tenweb-speed-optimizer') ?></a>
          </div>
          <?php
          $this->two_front_optimize_in_progress_content($post_id);
      }
    }

    /* Frontend Adminbar menu content in case of page is optimizing */
    public function two_front_optimize_in_progress_content( $post_id, $optimize_inprogress = FALSE ) {
      $page_title = get_the_title( $post_id );
      ?>
      <div class="two_in_progress_cont <?php echo !$optimize_inprogress ? 'two_hidden' : ''; ?>">
        <p class="two_status_title"><?php _e('Optimization in progress…', 'tenweb-speed-optimizer') ?></p>
        <p><?php echo sprintf( __('Your %s page is currently being optimized.', 'tenweb-speed-optimizer'), '<span>'.esc_html($page_title).'</span>' ) ?></p>
        <p><?php _e('You will receive a notification once optimization is completed.', 'tenweb-speed-optimizer') ?></p>
      </div>
      <?php
      $this->two_empty_front_optimized_content_template( $post_id );
    }

    /* Adminbar menu content in case of abandoned optimization */
    public function two_booster_abandoned_content() {
      $abandon_url = home_url()."?two_setup=1";
      ?>
      <div class="two_admin_bar_menu_content two_not_optimized_content">
        <p class="two_status_title"><?php _e('Optimization not finished', 'tenweb-speed-optimizer') ?></p>
        <p><?php _e('You haven’t finished optimizing your website,<br> which means no changes were applied to your live site.', 'tenweb-speed-optimizer') ?></p>
        <p><?php _e('Return to the flow to finish the optimization.', 'tenweb-speed-optimizer') ?></p>
        <a href="<?php echo esc_url($abandon_url); ?>" target="_blank" class="two_add_page_button"><?php _e('Finish optimization', 'tenweb-speed-optimizer') ?></a>
      </div>
      <?php
    }

    /* Frontend Adminbar menu content in case of page is already optimized */
    public function two_front_optimized_content() {
      global $post;
      if ( empty($post) ) {
        return false;
      }

      $post_id = $post->ID;
      if ( is_front_page() ) {
        $page_title = __('Hompage', 'tenweb-speed-optimizer');
      } else {
        $page_title = get_the_title($post_id);
      }

      /* Check home page */
      if ( is_front_page() ) {
          $score_data = get_option("two-front-page-speed");
      } else {
          $score_data = get_post_meta( $post_id, 'two_page_speed' );
          $score_data = end($score_data);
      }
      $date = 0;
      if ( !empty($score_data) && !isset($score_data['previous_score']) ) {
          return false;
      } elseif ( !empty($score_data) && isset($score_data['current_score']) ) {
          $is_page_score = TRUE;
          $optimized_pages = \TenWebOptimizer\OptimizerUtils::getCriticalPages();
          if( isset($optimized_pages[$post_id]) && isset($optimized_pages[$post_id]['critical_date']) ) {
              $date = $optimized_pages[$post_id]['critical_date'];
          } elseif( isset($score_data['current_score']['date']) ) {
              $date = strtotime($score_data['current_score']['date']);
          }
      } else {
          $is_page_score = FALSE;
      }
      $modified_date = get_the_modified_date( 'd.m.Y h:i:s a', $post_id );
      $modified_date = strtotime( $modified_date );
      $posts_in_progress = $this->two_is_optimize_inprogress( $post_id );
      if ( $posts_in_progress ) {
          $this->two_front_optimize_in_progress_content( $post_id, TRUE );
      } else { ?>
          <div class="two_admin_bar_menu_content two_optimized">
            <p class="two_status_title"><?php _e('Congrats!', 'tenweb-speed-optimizer') ?></p>
            <p
              class="two_optimized_congrats_subtitle"><?php echo sprintf(__('%s %s is successfully optimized.', 'tenweb-speed-optimizer'), esc_html($page_title), (!is_front_page() ? 'page' : '')) ?></p>
            <hr>

            <div class="two_score_success_container <?php echo $is_page_score ? '' : 'two_hidden'; ?>">
              <p class="two_score_success_container_title"><?php echo sprintf(__('Overview of %s %s performance:', 'tenweb-speed-optimizer'), esc_html($page_title), (!is_front_page() ? 'page' : '')) ?></p>
              <div class="two_score_block">
                <div class="two_score_block_left">
                  <p class="two_score_block_title"><?php _e('Before optimization', 'tenweb-speed-optimizer'); ?></p>

                  <div class="two_score_container two_score_container_mobile_old">
                    <div class="two-score-circle" data-score="<?php echo (int)$score_data['previous_score']['mobile_score']; ?>" data-size="40"
                         data-thickness="2" data-id="mobile">
                      <span class="two-score-circle-animated"></span>
                    </div>
                    <div class="two_score_info">
                      <p><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></p>
                      <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?>
                        <span class="two_load_time"><?php echo esc_html($score_data['previous_score']['mobile_tti'] . __('s', 'tenweb-speed-optimizer')); ?></span></p>
                    </div>
                  </div>
                  <div class="two_score_container two_score_container_desktop_old">
                    <div class="two-score-circle" data-score="<?php echo (int)$score_data['previous_score']['desktop_score']; ?>" data-size="40"
                         data-thickness="2" data-id="desktop">
                      <span class="two-score-circle-animated"></span>
                    </div>
                    <div class="two_score_info">
                      <p><?php _e('Desktop score', 'tenweb-speed-optimizer') ?></p>
                      <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?>
                        <span class="two_load_time"><?php echo esc_html($score_data['previous_score']['desktop_tti'] . __('s', 'tenweb-speed-optimizer')); ?></span></p>
                    </div>
                  </div>

                </div>

                <div class="two_score_block_right">
                  <p class="two_score_block_title"><?php echo sprintf(__('After %s optimization', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>

                  <div class="two_score_container two_score_container_mobile">
                    <div class="two-score-circle" data-score="<?php echo (int)$score_data['current_score']['mobile_score']; ?>" data-size="40"
                         data-thickness="2" data-id="mobile">
                      <span class="two-score-circle-animated"></span>
                    </div>
                    <div class="two_score_info">
                      <p><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></p>
                      <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?>
                        <span class="two_load_time"><?php echo esc_html($score_data['current_score']['mobile_tti'] . __('s', 'tenweb-speed-optimizer')); ?></span></p>
                    </div>
                  </div>
                  <div class="two_score_container two_score_container_desktop">
                    <div class="two-score-circle" data-score="<?php echo (int)$score_data['current_score']['desktop_score']; ?>" data-size="40"
                         data-thickness="2" data-id="desktop">
                      <span class="two-score-circle-animated"></span>
                    </div>
                    <div class="two_score_info">
                      <p><?php _e('Desktop score', 'tenweb-speed-optimizer') ?></p>
                      <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?>
                        <span class="two_load_time"><?php echo esc_html($score_data['current_score']['desktop_tti'] . __('s', 'tenweb-speed-optimizer')); ?></span></p>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <?php if ( !$is_page_score ) { ?>
            <div class="two_home_score_error">
              <div class="two_recount_home_score_cont">
                <p class="two_score_title"><?php _e('Your optimized page score:', 'tenweb-speed-optimizer') ?></p>
                <span class="two_recount_score" data-post_id="<?php echo esc_attr($post_id); ?>"></span>
              </div>
              <div class="two_home_score_error_info">
                <?php echo sprintf(__('%sSomething Went Wrong.%s Please check if your website works properly & try again or contact us.', 'tenweb-speed-optimizer'), '<b>', '</b>'); ?>
              </div>
            </div>
            <?php } ?>

            <?php if ( $modified_date > $date && $date != 0 ) { ?>
              <a id="two_optimize_now_button" data-post-id="<?php echo esc_attr($post_id); ?>" data-initiator="admin-bar" target="_blank"
                 class="two_add_page_button"><?php _e('Re-optimize', 'tenweb-speed-optimizer') ?></a>
            <?php } ?>
          </div>
          <?php
          $this->two_front_optimize_in_progress_content( $post_id );
      }
    }

    public function two_empty_front_optimized_content_template( $post_id ) {
      $page_title = get_the_title( $post_id );
      ?>
      <div class="two_admin_bar_menu_content two_empty_front_optimized_content two_hidden">
        <p class="two_status_title"><?php _e('Congrats!', 'tenweb-speed-optimizer') ?></p>
        <p
          class="two_optimized_congrats_subtitle"><?php echo sprintf(__('%s page is successfully optimized.', 'tenweb-speed-optimizer'), esc_html($page_title)) ?></p>
        <hr>
          <p class="two_score_success_container_title"><?php echo sprintf(__('Overview of %s page performance:', 'tenweb-speed-optimizer'), esc_html($page_title)) ?></p>
          <div class="two_score_block">
            <div class="two_score_block_left">
              <p class="two_score_block_title"><?php _e('Before optimization', 'tenweb-speed-optimizer'); ?></p>

              <div class="two_score_container two_score_container_mobile_old">
                <div class="two-score-circle_temp" data-score="" data-size="40" data-thickness="2" data-id="mobile">
                  <span class="two-score-circle-animated"></span>
                </div>
                <div class="two_score_info">
                  <p><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></p>
                  <p class="two_load_time"><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?></p>
                </div>
              </div>
              <div class="two_score_container two_score_container_desktop_old">
                <div class="two-score-circle_temp" data-score="" data-size="40" data-thickness="2" data-id="desktop">
                  <span class="two-score-circle-animated"></span>
                </div>
                <div class="two_score_info">
                  <p><?php _e('Desktop score', 'tenweb-speed-optimizer') ?></p>
                  <p class="two_load_time"><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?></p>
                </div>
              </div>

            </div>

            <div class="two_score_block_right">
              <p
                class="two_score_block_title"><?php echo sprintf(__('After %s optimization', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>

              <div class="two_score_container two_score_container_mobile">
                <div class="two-score-circle_temp" data-score="" data-size="40" data-thickness="2" data-id="mobile">
                  <span class="two-score-circle-animated"></span>
                </div>
                <div class="two_score_info">
                  <p><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></p>
                  <p class="two_load_time"><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?></p>
                </div>
              </div>
              <div class="two_score_container two_score_container_desktop">
                <div class="two-score-circle_temp" data-score="" data-size="40" data-thickness="2" data-id="desktop">
                  <span class="two-score-circle-animated"></span>
                </div>
                <div class="two_score_info">
                  <p><?php _e('Desktop score', 'tenweb-speed-optimizer') ?></p>
                  <p class="two_load_time"><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?></p>
                </div>
              </div>

            </div>
          </div>
      </div>
      <?php
    }

    /* Show notification during the page load if there is optimizing page in progress */
    public function two_optimize_notification() {
      if ( $this->two_booster_status == 3 || $this->two_booster_status == 2 || $this->two_booster_status == 5 ) {
        return;
      }
      $data = array('optimized'=>array(), 'optimizing' => array());
      $post_ids = new \WP_Query( array(
                                   'post_type'      => array('page','post'),
                                   'fields'         => 'ids',
                                   'meta_query' => array(
                                     array(
                                       'key'   => 'two_page_speed',
                                     ),
                                   )
                                 ));
      $post_ids = isset($post_ids->posts) ? $post_ids->posts : array();

      $show_popup = 0;
      $two_optimization_notif_status = get_option('two_optimization_notif_status');
      foreach ( $post_ids as $post_id ) {
        $page_score = get_post_meta( $post_id, 'two_page_speed' );
        $page_title = get_the_title($post_id);
        $status = 'optimized';
        $critical_pages = \TenWebOptimizer\OptimizerUtils::getCriticalPages();
        if (array_key_exists($post_id, $critical_pages)) {
          if ( isset($critical_pages[$post_id]['status']) && $critical_pages[$post_id]['status'] == 'in_progress' ) {
              $status = 'optimizing';
          }
        } else {
          $status = 'notOptimized';
        }

        if ( !empty($page_score) ) {

          $page_score = end($page_score);
          if ( !empty($page_score['current_score']) && !empty($page_score['previous_score']) ) {

            /* Show notif popup if in progress already showed(=1) or not showed but done(=0) */
            if ( isset($two_optimization_notif_status[$post_id]) && $two_optimization_notif_status[$post_id] !== 2 ) {
              $two_optimization_notif_status[$post_id] = 2;
              $show_popup = 1;
            }

            $data['optimized'][] = array(
              'status' => $status,
              'post_id' => $post_id,
              'post_title' => $page_title,
              'mobile_new' => $page_score['current_score']['mobile_score'],
              'mobile_loading_time_new' => $page_score['current_score']['mobile_tti'],
              'desktop_new' => $page_score['current_score']['desktop_score'],
              'desktop_loading_time_new' => $page_score['current_score']['desktop_tti'],
              'mobile_old' => $page_score['previous_score']['mobile_score'],
              'mobile_loading_time_old' => $page_score['previous_score']['mobile_tti'],
              'desktop_old' => $page_score['previous_score']['desktop_score'],
              'desktop_loading_time_old' => $page_score['previous_score']['desktop_tti'],
            );
          }
          elseif (!empty($page_score['previous_score'])) {
            /* Show notif popup if in progress not shown yet(=empty) or not showed but done(=0) */
            if( empty($two_optimization_notif_status[$post_id]) || (isset($two_optimization_notif_status[$post_id]) && $two_optimization_notif_status[$post_id] === 0) ) {
              $two_optimization_notif_status[$post_id] = 1;
              $show_popup = 1;
            }

            $data['optimizing'][] = array(
              'status' => $status,
              'post_id' => $post_id,
              'post_title' => $page_title,
            );
          }
        } else {
          continue;
        }
        /* Keeping all posts statuses which is in progress or optimized to manage notif popup view one time for each case */
        update_option('two_optimization_notif_status', $two_optimization_notif_status, 1);
      }

      if ( !$show_popup ) {
        return;
      }
      ?>
      <div class="two_admin_bar_menu_main two_admin_bar_menu_main_notif">
        <div class="two_admin_bar_menu_content two_optimized">
          <?php
          $i = 1;
          foreach ( $data['optimized'] as $optimized ) { ?>
            <div class="two_optimized_cont">
              <div class="two_optimized_congrats_row">
                <p class="two_status_title"><?php _e('Congrats!', 'tenweb-speed-optimizer') ?></p>
                <span class="two_arrow <?php echo ($i == 1) ? 'two_up_arrow' : 'two_down_arrow'?>"></span>
              </div>
              <p class="two_optimized_congrats_subtitle"><?php echo sprintf( __('%s page is successfully optimized.', 'tenweb-speed-optimizer'), '<span>' . esc_html($optimized['post_title']) . '</span>' ) ?></p>
              <hr>
              <div class="two_score_block_container <?php echo ($i == 1) ? '' : 'two_hidden'; ?>">
                <p class="two_score_success_container_title"><?php echo sprintf( __('Overview of %s page performance:', 'tenweb-speed-optimizer'), esc_html($optimized['post_title']) ) ?></p>
                <div class="two_score_block">
                  <div class="two_score_block_left">
                    <p class="two_score_block_title"><?php _e('Before optimization', 'tenweb-speed-optimizer'); ?></p>

                    <div class="two_score_container two_score_container_mobile_old">
                      <div class="two-score-circle" data-score="<?php echo (int)$optimized['mobile_old']; ?>" data-size="40" data-thickness="2" data-id="mobile">
                        <span class="two-score-circle-animated"></span>
                      </div>
                      <div class="two_score_info">
                        <p><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></p>
                        <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); echo esc_html($optimized['mobile_loading_time_old']); ?></p>
                      </div>
                    </div>
                    <div class="two_score_container two_score_container_desktop_old">
                      <div class="two-score-circle" data-score="<?php echo (int)$optimized['desktop_old']; ?>" data-size="40" data-thickness="2" data-id="desktop">
                        <span class="two-score-circle-animated"></span>
                      </div>
                      <div class="two_score_info">
                        <p><?php _e('Desktop score', 'tenweb-speed-optimizer') ?></p>
                        <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); echo esc_html($optimized['desktop_loading_time_old']); ?></p>
                      </div>
                    </div>

                  </div>
                  <div class="two_score_block_right">
                    <p class="two_score_block_title"><?php echo sprintf(__('After %s optimization', 'tenweb-speed-optimizer'), TWO_SO_ORGANIZATION_NAME . " Booster"); ?></p>

                    <div class="two_score_container two_score_container_mobile">
                      <div class="two-score-circle" data-score="<?php echo (int)$optimized['mobile_new']; ?>" data-size="40" data-thickness="2" data-id="mobile">
                        <span class="two-score-circle-animated"></span>
                      </div>
                      <div class="two_score_info">
                        <p><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></p>
                        <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); echo esc_html($optimized['mobile_loading_time_new']); ?></p>
                      </div>
                    </div>
                    <div class="two_score_container two_score_container_desktop_old">
                      <div class="two-score-circle" data-score="<?php echo (int)$optimized['desktop_new'];; ?>" data-size="40" data-thickness="2" data-id="desktop">
                        <span class="two-score-circle-animated"></span>
                      </div>
                      <div class="two_score_info">
                        <p><?php _e('Desktop score', 'tenweb-speed-optimizer') ?></p>
                        <p><?php _e('Load time: ', 'tenweb-speed-optimizer'); echo esc_html($optimized['desktop_loading_time_new']); ?></p>
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
              </div>
            </div>
            <?php
            $i++;
          }
          ?>

          <?php foreach ( $data['optimizing'] as $optimizing ) { ?>
            <div class="two_optimizing_container">
              <p><span></span><?php _e('Optimization in progress…', 'tenweb-speed-optimizer'); ?></p>
              <p><?php echo sprintf(__('Your %s page is currently being optimized.', 'tenweb-speed-optimizer'), '<span>'.esc_html($optimizing['post_title']).'</span>'); ?></p>
            </div>
          <?php } ?>
        </div>
      </div>
      <?php
    }

    /* Get website images total count and optimized images count from endpoint */
    public function get_images_data_api() {
      $two_images_count = get_transient("two_images_count");

      if ( !empty($two_images_count) ) {
        $this->optimized_images_count = $two_images_count['optimized_images_count'];
        $this->total_images_count = $two_images_count['total_images_count'];
        $this->empty_images_count_transient = '';
      } else {
          $this->empty_images_count_transient = 'two-adminBar two_empty_images_count';
      }
    }

    /* Check if current frontend page is optimized */
    public function two_is_page_optimized() {
      global $post;
      if ( empty($post) ) {
        return false;
      }
      $post_id = $post->ID;

      if ( is_front_page() ) {
        $post_id = 'front_page';
      }
      $optimized_pages = \TenWebOptimizer\OptimizerUtils::getCriticalPages();
      if( isset($optimized_pages[$post_id]) ) {
        return true;
      }

      return false;
    }

    /* Check current page optimization in progress */
    public function two_is_optimize_inprogress( $post_id ) {
      if ( is_front_page() ) {
          $page_score = get_option("two-front-page-speed");
      } else {
          $page_score = get_post_meta($post_id, 'two_page_speed');
      }

      if ( !empty($page_score) ) {
        $page_score = end($page_score);
        if ( !empty($page_score['previous_score']) && empty($page_score['current_score']) ) {
          return TRUE;
        }
      }
      return FALSE;
    }
}
