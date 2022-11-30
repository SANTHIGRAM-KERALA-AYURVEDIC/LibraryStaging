<?php

use TenWebOptimizer\OptimizerUtils;

$page_score = get_post_meta($post_id, 'two_page_speed', TRUE);
$page_url = get_permalink( $post_id );
// The page is optimized if there is a new score data after optimization in DB or is in progress if there is an old score data.
$status = 'optimized';
$critical_pages = \TenWebOptimizer\OptimizerUtils::getCriticalPages();
if (array_key_exists($post_id, $critical_pages)) {
  if ( isset($critical_pages[$post_id]['status']) && $critical_pages[$post_id]['status'] == 'in_progress' ) {
      $status = 'optimizing';
  }
} else {
     $status = 'notOptimized';
}

$disconnect = true;
if( OptimizerUtils::is_tenweb_booster_connected() ) {
  $disconnect = false;
}
?>
<?php if(\TenWebOptimizer\OptimizerUrl::urlIsOptimizable($page_url)):?>
<span class="two-page-speed two-optimized <?php echo $status == 'optimized' ? '' : ' two-hidden'; ?>">
  <a><?php _e('Optimized', 'tenweb-speed-optimizer'); ?></a>
</span>

<span class="two-page-speed two-notoptimized <?php echo $status == 'notOptimized' ? '' : ' two-hidden'; ?>">
  <a <?php echo $disconnect ? 'href="'.get_admin_url() . 'admin.php?page=two_settings_page"' : '' ?> data-post-id="<?php echo esc_attr( $post_id ) ?>" data-initiator="post-list"><?php _e('Optimize now', 'tenweb-speed-optimizer'); ?></a>
</span>

<span class="two-page-speed two-optimizing <?php echo $status == 'optimizing' ? '' : 'two-hidden'; ?>">
  <?php _e('Optimizing...', 'tenweb-speed-optimizer'); ?>
  <p class="two-description">
    <?php _e('Please refresh the page in 2 minutes.', 'tenweb-speed-optimizer'); ?>
  </p>
</span>
<?php endif;?>
<?php
if ( isset($page_score['current_score']) ) {
  $score = $page_score['current_score'];
}
else {
  $score = array(
    'desktop_score' => 0,
    'desktop_tti' => '',
    'mobile_score' => 0,
    'mobile_tti' => '',
    'date' => '',
  );
}
?>
<div class="two-score-container two-hidden" data-id="<?php echo esc_attr( $post_id ) ?>">
  <div class="two-score-mobile">
    <div class="two-score-circle"
         data-id="mobile"
         data-thickness="2"
         data-size="30"
         data-score="<?php echo esc_attr( $score['mobile_score'] ) ?>"
         data-loading-time="<?php echo esc_attr( $score['mobile_tti'] ) ?>">
      <span class="two-score-circle-animated"></span>
    </div>
    <div class="two-score-text">
      <span class="two-score-text-name"><?php echo __('Mobile score', 'tenweb-speed-optimizer') ?></span>
      <span class="two-load-text-time"><?php echo __('Load time:', 'tenweb-speed-optimizer') ?><span class="two-load-time"></span>s</span>
    </div>
  </div>
  <div class="two-score-desktop">
    <div class="two-score-circle"
         data-id="desktop"
         data-thickness="2"
         data-size="30"
         data-score="<?php echo esc_attr( $score['desktop_score'] ) ?>"
         data-loading-time="<?php echo esc_attr( $score['desktop_tti'] ) ?>">
      <span class="two-score-circle-animated"></span>
    </div>
    <div class="two-score-text">
      <span class="two-score-text-name"><?php echo __('Desktop score', 'tenweb-speed-optimizer') ?></span>
      <span class="two-load-text-time"><?php echo __('Load time:', 'tenweb-speed-optimizer') ?><span class="two-load-time"></span>s</span>
    </div>
  </div>
</div>



