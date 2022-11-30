<?php

namespace TenWebOptimizer;

/**
 * Class OptimizerElementor
 */
class OptimizerElementor {
  function __construct() {
    add_action('elementor/editor/after_enqueue_scripts', array( $this, 'two_scripts_styles' ));
    add_action('elementor/init', array( $this,'two_add_panel_tab' ));
    add_action('elementor/documents/register_controls', array( $this,'two_register_document_controls' ));
  }

  /* Enque scripts/styles for Elementor editor */
  public function two_scripts_styles() {
    global $post;
    if ( !current_user_can('administrator') ) {
      return;
    }
    wp_register_style('two-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800&display=swap');
    wp_enqueue_style('two_speed_css', TENWEB_SO_URL . '/assets/css/speed.css', array( 'two-open-sans' ), TENWEB_SO_VERSION);
    wp_enqueue_style('two_speed_dark_css', TENWEB_SO_URL . '/assets/css/speed_elementor_dark.css', array( 'two-open-sans', 'elementor-editor-dark-mode' ), TENWEB_SO_VERSION);
    wp_enqueue_script('two_circle_js', TENWEB_SO_URL . '/assets/js/circle-progress.js', array( 'jquery' ), TENWEB_SO_VERSION);
    wp_enqueue_script('two_speed_js', TENWEB_SO_URL . '/assets/js/speed.js', array(
      'jquery',
      'two_circle_js'
    ),                TENWEB_SO_VERSION);
    wp_localize_script('two_speed_js', 'two_speed', array(
      'nonce' => wp_create_nonce('two_ajax_nonce'),
      'ajax_url' => admin_url('admin-ajax.php'),
      'clearing' => __('Clearing...', 'tenweb-speed-optimizer'),
      'cleared' => __('Cleared cache', 'tenweb-speed-optimizer'),
      'clear' => __('Clear cache', 'tenweb-speed-optimizer'),
      'title' => __('10Web Booster', 'tenweb-speed-optimizer'),
      'optimize_entire_website' => two_reached_limit(),
      'post_type' => $post->post_type,
      'post_status' => get_post_status($post->ID),
    ));
  }

  /* Register new tab in page settings */
  public function two_add_panel_tab() {
    if ( !current_user_can('administrator') ) {
      return;
    }
    \Elementor\Controls_Manager::add_tab(
      'two_optimize',
      esc_html__('10Web Booster', 'tenweb-speed-optimizer')
    );
  }


  /**
   * Register additional document controls.
   *
   * @param \Elementor\Core\DocumentTypes\PageBase $document The PageBase document instance.
   */
  public function two_register_document_controls( $document ) {
    if ( !current_user_can('administrator') ) {
      return;
    }
    global $post;
    $post_id = $post->ID;
    if ( 'publish' !== get_post_status( $post_id ) ) {
      return;
    }

    if ( ! $document instanceof \Elementor\Core\DocumentTypes\PageBase || ! $document::get_property( 'has_elements' ) ) {
      return;
    }

    $document->start_controls_section(
      'two_optimize_section',
      [
        'tab' => 'two_optimize',
      ]
    );
    $page_score = get_post_meta( $post_id, 'two_page_speed' );
    $page_score = end($page_score);

    $status = 'optimized';
    $critical_pages = \TenWebOptimizer\OptimizerUtils::getCriticalPages();
    if (array_key_exists($post_id, $critical_pages)) {
      if ( isset($critical_pages[$post_id]['status']) && $critical_pages[$post_id]['status'] == 'in_progress' ) {
          $status = 'optimizing';
      }
    } else {
      $status = 'notOptimized';
    }

    if ( $status != 'optimized' ) {
      $label = '<p class="two_elementor_control_title' . ($status == "optimizing" ? " two-hidden" : "") . '">'.esc_html__( 'Optimize with 10Web Booster', 'tenweb-speed-optimizer' ).'</p>';
      $content = $this->two_elementor_not_optimized_content( $status, $post_id );
      $classname = 'two_elementor_settings_content'.($status == "optimizing" ? ' two-optimizing' : '');
    } else {
      $page_title = get_the_title( $post_id );
      $label =  '<p class="two_elementor_control_title two_congrats">' . esc_html__('Congrats!', 'tenweb-speed-optimizer') . '</p>';
      $content = $this->two_elementor_optimized_content( $page_title, $page_score, $status, $post_id  );
      $classname = 'two_elementor_settings_content two_optimized';
    }

    $document->add_control(
      'raw_html',
      [
        'label' => $label,
        'type' => \Elementor\Controls_Manager::RAW_HTML,
        'raw' => $content,
        'content_classes' => $classname,
      ]
    );

    $document->end_controls_section();
  }

  /**
   * Elementor editor booster info in case of page not optimized
   *
   * @param $status bool
   * @param $post_id integer
   *
   * @return string html data
   */
  public function two_elementor_not_optimized_content( $status, $post_id ) {
    $reach_limit = two_reached_limit();
    ob_start();
    ?>
    <div class="two_elementor_control_container<?php echo ($status == 'optimizing' ? ' two-hidden' : '') ?>">
      <p><?php echo esc_html__('Optimize now to get a 90+ PageSpeed score.', 'textdomain') ?></p>
      <a <?php echo $reach_limit != false ? 'href="'.esc_url($reach_limit . '?two_comes_from=ElementorAfterLimit').'" target="_blank"': ''?>
        onclick="<?php echo $reach_limit != false ? '' : 'two_optimize_page(this)'?>" data-post-id="<?php echo esc_attr($post_id) ?>" target="_blank"
        data-initiator="elementor" class="two_optimize_button_elementor"><?php _e("Optimize", "tenweb-speed-optimizer") ?>
      </a>
    </div>
    <span class="two-page-speed two-optimizing <?php echo ($status == 'optimizing' ? '' : ' two-hidden'); ?>">
    <?php _e('Optimizing...', 'tenweb-speed-optimizer'); ?>
    <p class="two-description"><?php _e('Please refresh the page in 2 minutes.', 'tenweb-speed-optimizer'); ?></p>
  </span>
    <?php
    return ob_get_clean();
  }

  /**
   * Elementor editor booster info in case of page optimized
   *
   * @param $page_title string
   * @param $score_data array
   *
   * @return string html data
   */
  public function two_elementor_optimized_content( $page_title, $score_data, $status, $post_id  ) {
//      $date = 0;
//      if ( !empty($score_data) && !isset($score_data['previous_score']) ) {
//          return false;
//      } elseif ( !empty($score_data) && isset($score_data['current_score']) ) {
//          $optimized_pages = \TenWebOptimizer\OptimizerUtils::getCriticalPages();
//          if( isset($optimized_pages[$post_id]) && isset($optimized_pages[$post_id]['critical_date']) ) {
//              $date = $optimized_pages[$post_id]['critical_date'];
//          } elseif( isset($score_data['current_score']['date']) ) {
//              $date = strtotime($score_data['current_score']['date']);
//          }
//      }
//      $modified_date = get_the_modified_date( 'd.m.Y h:i:s a', $post_id );
//      $modified_date = strtotime( $modified_date );
    ob_start();
    ?>
    <script>
      jQuery('.two-score-circle').each(function () {
        two_draw_score_circle(this);
      });
    </script>
    <p class="two-elementor-container-title"><?php echo '<span>' . esc_html__($page_title) . '</span>' . esc_html__(' page is successfully optimized', 'textdomain') ?></p>
    <hr>
    <div class="two-score-section">
      <div class="two-score-container-title"><?php echo sprintf(__('Overview of %s page performance:', 'tenweb-speed-optimizer'), '<span>'.esc_html__($page_title).'</span>') ?></div>
      <div class="two-score-container-both">
        <div class="two-score-container-old">
          <div class="two-score-header"><?php _e('Before optimization', 'tenweb-speed-optimizer'); ?></div>
          <div class="two-score-mobile">
            <div class="two-score-circle two_circle_with_bg"
                 data-id="mobile"
                 data-thickness="2"
                 data-size="40"
                 data-score="<?php echo intval($score_data['previous_score']['mobile_score']); ?>"
                 data-loading-time="<?php echo esc_attr($score_data['previous_score']['mobile_tti']); ?>">
              <span class="two-score-circle-animated"></span>
            </div>
            <div class="two-score-text">
              <span class="two-score-text-name"><?php _e('Mobile score', 'tenweb-speed-optimizer'); ?></span>
              <span class="two-load-text-time"><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?><span class="two-load-time"></span><?php _e("s", "tenweb-speed-optimizer") ?></span>
            </div>
          </div>
          <div class="two-score-desktop">
            <div class="two-score-circle two_circle_with_bg"
                 data-id="desktop"
                 data-thickness="2"
                 data-size="40"
                 data-score="<?php echo intval($score_data['previous_score']['desktop_score']); ?>"
                 data-loading-time="<?php echo esc_attr($score_data['previous_score']['desktop_tti']); ?>">
              <span class="two-score-circle-animated"></span>
            </div>
            <div class="two-score-text">
              <span class="two-score-text-name"><?php _e('Desktop score', 'tenweb-speed-optimizer'); ?></span>
              <span class="two-load-text-time"><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?><span class="two-load-time"></span><?php _e("s", "tenweb-speed-optimizer") ?></span>
            </div>
          </div>
        </div>
        <div class="two-score-container-new">
          <div class="two-score-header"><?php _e('After optimization', 'tenweb-speed-optimizer') ?></div>
          <div class="two-score-mobile">
            <div class="two-score-circle two_circle_with_bg"
                 data-id="mobile"
                 data-thickness="2"
                 data-size="40"
                 data-score="<?php echo intval($score_data['current_score']['mobile_score']); ?>"
                 data-loading-time="<?php echo esc_attr($score_data['current_score']['mobile_tti']); ?>">
              <span class="two-score-circle-animated"></span>
            </div>
            <div class="two-score-text">
              <span class="two-score-text-name"><?php _e('Mobile score', 'tenweb-speed-optimizer') ?></span>
              <span class="two-load-text-time"><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?><span class="two-load-time"></span><?php _e("s", "tenweb-speed-optimizer") ?></span>
            </div>
          </div>
          <div class="two-score-desktop">
            <div class="two-score-circle two_circle_with_bg"
                 data-id="desktop"
                 data-thickness="2"
                 data-size="40"
                 data-score="<?php echo intval($score_data['current_score']['desktop_score']); ?>"
                 data-loading-time="<?php echo esc_attr($score_data['current_score']['desktop_tti']); ?>">
              <span class="two-score-circle-animated"></span>
            </div>
            <div class="two-score-text">
              <span class="two-score-text-name"><?php _e('Desktop score', 'tenweb-speed-optimizer'); ?></span>
              <span class="two-load-text-time"><?php _e('Load time: ', 'tenweb-speed-optimizer'); ?><span class="two-load-time"></span><?php _e("s", "tenweb-speed-optimizer") ?></span>
            </div>
          </div>
        </div>
      </div>
<!--        button is currently not used-->
<!--        <div class="two_elementor_control_container--><?php //echo (( $modified_date > $date && $date != 0 ) ? '' : ' two-hidden') ?><!--">-->
<!--            <a onclick="--><?php //echo 'two_optimize_page(this)';?><!--" data-post-id="--><?php //echo esc_attr($post_id) ?><!--" target="_blank"-->
<!--                    data-initiator="elementor" class="two_optimize_button_elementor">--><?php //_e("Re-optimize", "tenweb-speed-optimizer") ?>
<!--            </a>-->
<!--        </div>-->
    </div>
      <span class="two-page-speed two-optimizing <?php echo ($status == 'optimizing' ? '' : ' two-hidden'); ?>">
    <?php _e('Optimizing...', 'tenweb-speed-optimizer'); ?>
    <p class="two-description"><?php _e('Please refresh the page in 2 minutes.', 'tenweb-speed-optimizer'); ?></p>
  </span>
    <?php
    return ob_get_clean();
  }
}
