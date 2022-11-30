<?php

$two_clear_cache_logs = get_option(\TenWebOptimizer\OptimizerAdmin::TWO_CLEAR_CACHE_LOG_OPTION_NAME, []);
$two_clear_cache_headlines = [
  "is_json" => "Ajax call",
  "excludeCriticalRegeneration" => "Exclude crit. regen.",
  "delete_tenweb_manager_cache" => "Delete manager cache",
  "delete_cloudflare_cache" => "Delete cloudflare cache",
  "critical_regeneration_mode" => "Crit. regen. mode",
  "clear_critical" => "Clear crit.",
  "stack_trace" => "Stack",
  "date" => "Date"
];

$two_critical_css_logs = get_option(\TenWebOptimizer\OptimizerCriticalCss::LOG_OPTION_NAME, []);
$two_critical_css_headlines = [
  "domain_id" => "Domain ID",
  "notification_id" => "Notif. ID",
  "newly_connected_website" => "Newly conn.",
  "flow_id" => "Flow ID",
  "page_id" => "Page ID",
  "status_code" => "Status Code",
  "stack_trace" => "Stack",
  "date" => "Date"
];

usort($two_clear_cache_logs, function($item1, $item2){
  return $item2['date'] <=> $item1['date'];
});

usort($two_critical_css_logs, function($item1, $item2){
  return $item2['date'] <=> $item1['date'];
});

?>
<div class="two_settings_tab two_tab_two_logs">
    <div>
        <h3 style="display: inline-block">Clear cache logs</h3>
        <span style="float: right;" class="button two_delete_clear_cache_logs">Delete clear cache logs</span>
    </div>
    <table class="display two_clear_cache_logs" style="width:100%">
        <thead>
        <tr>
          <?php foreach($two_clear_cache_headlines as $headline): ?>
              <th><?php echo esc_html( $headline ); ?></th>
          <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach($two_clear_cache_logs as $log_info): ?>
            <tr>
              <?php foreach($two_clear_cache_headlines as $headline => $title) {
                if(isset($log_info[$headline])) {
                  $val = $log_info[$headline];
                  if(is_bool($val)) {
                    $msg = $val ? 'true' : 'false';
                  } else if($headline == "date") {
                    $msg = date('Y-m-d H:i:s', $val);
                  } else {
                    $msg = $val;
                  }
                } else {
                  $msg = "-";
                }

                if($headline == "stack_trace") {
                  echo "<th><code class='two_clear_cache_stack_trace'>";
                  foreach($val as $frame) {
                    echo "<div>" . esc_html($frame) . "</div>";
                  }
                  echo "</code></th>";

                } else {
                  echo "<th>" . esc_html($msg) . "</th>";
                }
              } ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
          <?php foreach($two_clear_cache_headlines as $headline): ?>
              <th><?php echo esc_html( $headline ); ?></th>
          <?php endforeach; ?>
        </tr>
        </tfoot>
    </table>
    <hr/>
    <div style="margin-top: 40px;">
        <h3 style="display: inline-block">Generate critical CSS Logs</h3>
        <span style="float: right;" class="button two_delete_critical_css_logs">Delete critical CSS logs</span>
    </div>
    <table class="display two_critical_css_logs" style="width:100%">
        <thead>
        <tr>
          <?php foreach($two_critical_css_headlines as $headline): ?>
              <th><?php echo $headline; ?></th>
          <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach($two_critical_css_logs as $log_info): ?>
            <tr>
              <?php foreach($two_critical_css_headlines as $headline => $title) {
                if(isset($log_info[$headline])) {
                  $val = $log_info[$headline];
                  if(is_bool($val)) {
                    $msg = $val ? 'true' : 'false';
                  } else if($headline == "date") {
                    $msg = date('Y-m-d H:i:s', $val);
                  } else {
                    $msg = $val;
                  }
                } else {
                  $msg = "-";
                }

                if($headline == "stack_trace") {
                  echo "<th><code class='two_clear_cache_stack_trace'>";
                  foreach($val as $frame) {
                    echo "<div>" . esc_html($frame) . "</div>";
                  }
                  echo "</code></th>";

                } else {
                  echo "<th>" . esc_html($msg) . "</th>";
                }
              } ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
          <?php foreach($two_critical_css_headlines as $headline): ?>
              <th><?php echo esc_html( $headline ); ?></th>
          <?php endforeach; ?>
        </tr>
        </tfoot>
    </table>
</div>
