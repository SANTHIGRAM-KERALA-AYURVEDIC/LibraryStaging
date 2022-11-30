<?php
/**
 * Plugin Name: 10Web Booster
 * Plugin URI: https://10web.io/page-speed-booster/
 * Description: Optimize your website speed and performance with 10Web Booster by compressing CSS and JavaScript.
 * Version: 2.9.24
 * Author: 10Web - Website speed optimization team
 * Author URI: https://10web.io/
 * Text Domain: tenweb-speed-optimizer
 */

use TenWebOptimizer\OptimizerUtils;
use TenWebOptimizer\OptimizerWhiteLabel;

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('TWO_ALWAYS_CRITICAL')) {
    define('TWO_ALWAYS_CRITICAL', true);
}
if (!defined('TWO_PLUGIN_FILE')) {
    define( 'TWO_PLUGIN_FILE', __FILE__ );
}

if(isset($_GET["two_check_redirect"]) && $_GET["two_check_redirect"] === "1"){
    return;
}

global $two_incompatible_errors;
$two_incompatible_errors = array();
require_once 'config.php';
require_once TENWEB_SO_PLUGIN_DIR . '/includes/OptimizerUrl.php';
if ( PHP_MAJOR_VERSION < 7 || ( PHP_MAJOR_VERSION === 7 && PHP_MINOR_VERSION === 0 ) ) {
    if ( !defined( 'TWO_INCOMPATIBLE_ERROR' ) ) {
        define( 'TWO_INCOMPATIBLE_ERROR', true );
    }
    $two_incompatible_errors[] = array( 'title' => __('PHP compatibility error:', 'tenweb-speed-optimizer'),
                                        'message' => __('PHP 7.1 or a newer version is required for 10Web Booster. Please update your PHP version to proceed.', 'tenweb-speed-optimizer') );
}
if ( get_site_transient( 'tenweb_so_auth_error_logs' ) ) {
    if ( !defined( 'TWO_INCOMPATIBLE_WARNING' ) ) {
        define( 'TWO_INCOMPATIBLE_WARNING', true );
    }
    $two_incompatible_errors[] = array( 'title' => __('Trouble connecting your website to 10Web:', 'tenweb-speed-optimizer'),
                                        'message' => __(get_site_transient( 'tenweb_so_auth_error_logs' ), 'tenweb-speed-optimizer') );
    delete_site_transient( 'tenweb_so_auth_error_logs' );
}
if ( is_multisite() && !TENWEB_SO_HOSTED_ON_10WEB) {
    if ( !defined( 'TWO_HOSTED_MULTISITE' ) ) {
        define( 'TWO_HOSTED_MULTISITE', true );
    }
    if ( !defined( 'TWO_INCOMPATIBLE_ERROR' ) ) {
       define( 'TWO_INCOMPATIBLE_ERROR', true );
    }
  $two_incompatible_errors[] = array( 'title' => __('Multisite not supported:', 'tenweb-speed-optimizer'),
                                      'message' => __('This feature will be available soon.', 'tenweb-speed-optimizer') );
}

if ( defined( 'TWO_INCOMPATIBLE_ERROR' ) && TWO_INCOMPATIBLE_ERROR ) {
    add_action( 'admin_menu', function() {
        require_once TENWEB_SO_PLUGIN_DIR . 'OptimizerAdmin.php';
        require_once TENWEB_SO_PLUGIN_DIR . '/includes/OptimizerUtils.php';
        add_menu_page(
            TWO_SO_ORGANIZATION_NAME . ' Booster',
            TWO_SO_ORGANIZATION_NAME . ' Booster',
          'manage_options',
          'two_settings_page',
          array(
            '\TenWebOptimizer\OptimizerAdmin',
            'settings_page',
          ),
          TENWEB_SO_URL . '/assets/images/speed/logo.svg');
    } );
    add_action( 'admin_enqueue_scripts', array( '\TenWebOptimizer\OptimizerAdmin', 'two_enqueue_admin_assets' ) );
} else {
    include_files();

    global $tenweb_subscription_id;
    $tenweb_subscription_id = get_transient(TENWEB_PREFIX . '_subscription_id');
    if((!isset($tenweb_subscription_id)  || (int)$tenweb_subscription_id<1) && $tenweb_subscription_id !== "0"){
        if ( isset(\TenWebOptimizer\OptimizerUtils::two_update_subscription()['tenweb_subscription_id']) ) {
            $tenweb_subscription_id = \TenWebOptimizer\OptimizerUtils::two_update_subscription()['tenweb_subscription_id'];
        }
    } elseif ( $tenweb_subscription_id == "0" && !TENWEB_SO_HOSTED_ON_10WEB ){
        $tenweb_subscription_id = TENWEB_SO_FREE_SUBSCRIPTION_ID;
    }

    global $tenweb_plan_title;
    $tenweb_plan_title = get_transient(TENWEB_PREFIX . '_plan_title');
    if( !isset($tenweb_plan_title) && $tenweb_plan_title !== "" ){
        if ( isset(\TenWebOptimizer\OptimizerUtils::two_update_subscription()['tenweb_plan_title']) ) {
            $tenweb_plan_title = \TenWebOptimizer\OptimizerUtils::two_update_subscription()['tenweb_plan_title'];
        }
    } elseif ( $tenweb_plan_title == "" && !TENWEB_SO_HOSTED_ON_10WEB ){
        $tenweb_plan_title = 'FREE';
    }

    add_action('init', 'two_plugin_add_new_image_size');
    register_deactivation_hook(__FILE__, array('\TenWebOptimizer\OptimizerAdmin', 'two_deactivate'));
    register_uninstall_hook( __FILE__,  array('\TenWebOptimizer\OptimizerAdmin', 'two_uninstall') );

    global $TwoSettings;
    $TwoSettings =  \TenWebOptimizer\OptimizerSettings::get_instance();


    if (!isset($_GET['action']) || $_GET['action'] != 'deactivate') {
        register_activation_hook(__FILE__, array('\TenWebOptimizer\OptimizerAdmin', 'two_activate'));
        add_action("plugins_loaded", "two_init");
    }
    add_action('admin_bar_menu', 'two_admin_bar', 999999);
}

if( defined('TWO_SO_COMPANY_NAME') &&  get_option('two_so_organization_name') === false ) {
    update_option('two_so_organization_name', TWO_SO_COMPANY_NAME );
}
if ( get_option('two_so_organization_name') &&  get_option('two_so_organization_name') != '') {
    $organization_name = get_option('two_so_organization_name');
    define('TWO_SO_ORGANIZATION_NAME', $organization_name);
    $whiteLabel = OptimizerWhiteLabel::get_instance();
    $whiteLabel->register_hooks();
} else if( class_exists( '\Tenweb_Manager\Helper' ) && method_exists( '\Tenweb_Manager\Helper', 'get_company_name' ) && strtolower( \Tenweb_Manager\Helper::get_company_name() ) !== '10web' ){
    define('TWO_SO_ORGANIZATION_NAME', \Tenweb_Manager\Helper::get_company_name());
} else {
    define('TWO_SO_ORGANIZATION_NAME', '10Web');
}

function include_files() {
    require_once 'vendor/autoload.php';
}

function add_attr_to_script($tag, $handle){
    if($handle === "two_preview_js" || $handle === "jquery-core"){
        return str_replace("<script", "<script data-pagespeed-no-defer two-no-delay ", $tag);
    }
    return $tag;
}

if(isset($_GET["two_preview"]) && $_GET["two_preview"]==="1"){
    add_filter("determine_current_user" , function ($user_id){
        if($user_id){
            return 0;
        }
        return $user_id;
    },99);
}

function two_plugin_add_new_image_size()
{
    add_image_size('tenweb_optimizer_mobile', 600, 600, false);
    add_image_size('tenweb_optimizer_tablet', 768, 1024, false);
}

function two_init()
{
    if(isset($_GET["two_setup"]) && $_GET["two_setup"] === "1"){
        if(is_user_logged_in()){
            two_init_preview();
        }else{
            $two_preview_url = add_query_arg(array("two_setup"=>"1"), get_home_url() . "/");
            $two_preview_url = urlencode($two_preview_url);
            $two_preview_login_url = add_query_arg( array( 'redirect_to'=>$two_preview_url), wp_login_url() );
            OptimizerUtils::two_redirect($two_preview_login_url);
        }
        $two_conflicting_plugins = OptimizerUtils::get_conflicting_plugins();
        $two_triggerPostOptimizationTasks = get_option("two_triggerPostOptimizationTasks");
        if(empty($two_conflicting_plugins)){
            $two_conflicting_plugins = array();
        }
        $incompatible_plugins_active_send  = get_option("incompatible_plugins_active_send");
        if($two_triggerPostOptimizationTasks !== "1" && $incompatible_plugins_active_send !== "1"){
            update_option("incompatible_plugins_active_send" , "1");
            OptimizerUtils::update_connection_flow_progress("running", "incompatible_plugins_active", array_values($two_conflicting_plugins));
        }
    }


    if(isset($_GET["two_preview"]) && $_GET["two_preview"]==="1"){
        if(isset($_GET["two_level"])){
            add_filter( 'option_active_plugins', function( $plugins ){
                $two_plugin_filter_data = OptimizerUtils::filter_incompatible_plugins($plugins);
                if(isset($two_plugin_filter_data["compatible"])){
                    return $two_plugin_filter_data["compatible"];
                }
                return $plugins;
            });
        }
    }

    add_filter( 'wcml_user_store_strategy', function() {
      // wcml_client_currency should be kept in cookies (not in session), otherwise page cache will not work
      return 'cookie';
    } );

    add_action( 'wp_ajax_two_set_critical', 'two_set_critical' );
    add_action( 'wp_ajax_nopriv_two_set_critical', 'two_set_critical' );

    add_action( 'wp_ajax_two_optimize_page', 'two_optimize_page' );


    require __DIR__ . '/OptimizerApi.php';
    $OptimizerApi = new OptimizerApi();

    \TenWebIO\Init::getInstance();

    global $TwoSettings;
    if ( defined( 'WP_CLI' ) && WP_CLI ) { //Run only TWO CLI in WP_CLI mode
        require __DIR__ . '/OptimizerCli.php';
        return;
    }

    $two_disable_jetpack_optimization = $TwoSettings->get_settings("two_disable_jetpack_optimization");
    if ( 'on' === $two_disable_jetpack_optimization ) {
        add_filter( 'option_jetpack_active_modules', 'two_jetpack_module_override' );
        function two_jetpack_module_override( $modules ) {
            $disabled_modules = array(
                'lazy-images',
                'photon',
                'photon-cdn',
            );

            foreach ( $disabled_modules as $module_slug ) {
                $found = array_search( $module_slug, $modules );
                if ( false !== $found ) {
                    unset( $modules[ $found ] );
                }
            }

            return $modules;
        }
    }
    \TenWebOptimizer\OptimizerAdmin::get_instance();
    $global_mode = get_option("two_default_mode", OptimizerUtils::MODES["extreme"]);
    $global_mode_name = "";
    if(is_array($global_mode)){
        $global_mode_name = $global_mode["mode"];
    }
    if(\Tenweb_Authorization\Login::get_instance()->check_logged_in() && $global_mode_name!=="no_optimize"){
        \TenWebOptimizer\OptimizerMain::get_instance();
        \TenWebOptimizer\WebPageCache\OptimizerWebPageCacheWP::get_instance();
    }


    if(isset($_GET["two_action"]) && $_GET["two_action"] === 'generating_critical_css') {
        ob_start('two_critical', 0, PHP_OUTPUT_HANDLER_REMOVABLE);
    }
}

function two_init_preview(){
    $two_flow_mode_select = get_site_option("two_flow_mode_select");
    if($two_flow_mode_select !== "1"){
        update_site_option("two_flow_mode_select", "1");
        OptimizerUtils::update_connection_flow_progress("running", "mode_selection");
    }
    add_action( 'wp_enqueue_scripts', "two_preview_assets" );
}

function two_preview_assets(){
    $flow_id = get_site_option(TENWEB_PREFIX . '_flow_id');


    $two_conflicting_plugins = OptimizerUtils::get_conflicting_plugins();
    $two_first_optimization = get_option("two_first_optimization");
    wp_enqueue_style( 'two_google-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap',  array(), TENWEB_SO_VERSION);
    wp_enqueue_script('two_preview_js', TENWEB_SO_URL . '/assets/js/two_preview.js', array('jquery'), TENWEB_SO_VERSION);
    wp_enqueue_style('two_preview_css', TENWEB_SO_URL . '/assets/css/two_preview.css', array(), TENWEB_SO_VERSION);
    $two_preview_localize_data = array(
        'global_mode'        =>  "",
        'two_first_optimization'        =>  $two_first_optimization,
        'home_url'        =>  get_home_url() . "/",
        'flow_id'        =>  $flow_id,
        'skip_url'        =>  TENWEB_DASHBOARD."?flow_skip=1&optimizing_website=".get_site_option(TENWEB_PREFIX . '_domain_id'),
        'contact_us_url'        =>  TENWEB_DASHBOARD."?flow_contact_us=1&optimizing_website=".get_site_option(TENWEB_PREFIX . '_domain_id')."&open=livechat",
        'success_url'        =>  TENWEB_DASHBOARD."?flow_success=1&optimizing_website=".get_site_option(TENWEB_PREFIX . '_domain_id'),
        'two_modes'        =>  json_encode(\TenWebOptimizer\OptimizerUtils::get_modes(null, true)),
        'no_delay' =>'data-pagespeed-no-defer two-no-delay',
        'incompatible_plugins' => false,
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'ajaxnonce' => wp_create_nonce( 'two_ajax_nonce' ),
        'two_company_name' => TWO_SO_ORGANIZATION_NAME,
    );
    $two_default_mode = get_option("two_default_mode", OptimizerUtils::MODES["extreme"]);
    if(isset($two_default_mode) && is_array($two_default_mode)){
        $two_preview_localize_data["global_mode"] = $two_default_mode["mode"];
    }
    if(is_array($two_conflicting_plugins) && !empty($two_conflicting_plugins)){
        update_site_option("two_conflicting_plugins", $two_conflicting_plugins);
        $two_preview_localize_data["incompatible_plugins"] = json_encode($two_conflicting_plugins);
    }
    wp_localize_script('two_preview_js', 'two_preview_vars', $two_preview_localize_data);
    add_filter( 'script_loader_tag', 'add_attr_to_script', 10, 3 );
}

function two_critical($content){
    return \TenWebOptimizer\OptimizerUtils::clear_iframe_src($content);
}

function two_set_critical(){
   \TenWebOptimizer\OptimizerUtils::set_critical();
}

// Call the action on finishing the given page optimization.
add_action('two_page_optimized', 'two_page_optimized');
function two_page_optimized($post_id) {
  if ($post_id == 'front_page') {
    // If front page is a page and has ID, check and save the score also as post meta.
    $post_id = url_to_postid(get_home_url());
    if ($post_id) {
      two_check_score($post_id);
    }
  }
  else {
    two_check_score($post_id);
  }
}

// Call the action on removing the page critical CSS.
add_action('two_page_optimized_removed', 'two_page_optimized_removed');
function two_page_optimized_removed($post_id) {
  if ( $post_id == 'front_page' ) {
    delete_option('two-front-page-speed');
    // If front page is a page and has ID, check and save the score also as post meta.
    $post_id = url_to_postid(get_home_url());
    if ($post_id) {
      delete_post_meta($post_id, 'two_page_speed');
    }
  }
  else {
    delete_post_meta($post_id, 'two_page_speed');
  }
}

// Add Optimize column to the posts list table.
add_filter('manage_post_posts_columns', 'two_add_column_to_posts');
add_filter('manage_page_posts_columns', 'two_add_column_to_posts');
function two_add_column_to_posts( $columns ) {
  if ( \TenWebOptimizer\OptimizerUtils::is_paid_user() ) {
    return $columns;
  }

  $offset = array_search('author', array_keys($columns));

  return array_merge(array_slice($columns, 0, $offset), [ 'two-speed' => '<b>' . TWO_SO_ORGANIZATION_NAME . ' Booster' . '</b>' ], array_slice($columns, $offset, NULL));
}

// Check if optimized pages limit reached.
function two_reached_limit() {
  if ( count(\TenWebOptimizer\OptimizerUtils::getCriticalPages()) >= 6 ) {
    $domain_id = intval(get_option(TENWEBIO_MANAGER_PREFIX . '_domain_id', 0));
    return TENWEB_DASHBOARD . "/websites/" . $domain_id . "/booster/pro";
  }
  return false;
}

/**
 * Optimize the given page.
 *
 * @param $check_score
 *
 * @return void
 */
function two_optimize_page() {
  $nonce = isset($_GET["nonce"]) ? sanitize_text_field( $_GET["nonce"] ) : '';

  if ( !wp_verify_nonce($nonce, 'two_ajax_nonce') ) {
    die('Permission Denied.');
  }

  $post_id = isset($_GET["post_id"]) ? intval( $_GET["post_id"] ) : 0;
  if (!$post_id) {
    return;
  }
  $initiator = isset($_GET["initiator"]) ? sanitize_text_field( $_GET["initiator"] ) : '';
  // Get and score in DB the page speed score before optimize.
  two_check_score($post_id, TRUE);

  /* Keeping all posts statuses which is in progress or optimized to manage notif popup view one time for each case */
  $two_optimization_notif_status = get_option('two_optimization_notif_status');
  $two_optimization_notif_status[$post_id] = 0;
  update_option('two_optimization_notif_status', $two_optimization_notif_status, 1);

  \TenWebOptimizer\OptimizerCriticalCss::generate_critical_css_by_id( $post_id, false, $initiator );

  die;
}

/**
 * Save the page speed in the post meta.
 *
 * @param $post_id
 * @param $old
 * @param $no_optimized
 *
 * @return void
 */
function two_check_score($post_id, $old = FALSE, $no_optimized = FALSE) {
  // Getting front_page placeholder instead of page ID for Home page.
  $url = ($post_id == 'front_page') ? get_home_url() : get_permalink( $post_id );

  if (!$url) {
    return;
  }

  // To check the not optimized page score. This will need on the plugin update to have old scores for existing users.
  if ( $no_optimized ) {
    $url = add_query_arg(array('two_nooptimize' => 1), $url);
  }
  $desktop_score = two_google_check_score( $url, 'desktop' );
  if ( isset($desktop_score['error']) ) {
    return;
  }
  $score = $desktop_score;

  $mobile_score = two_google_check_score( $url, 'mobile' );
  if ( isset($mobile_score['error']) ) {
    return;
  }
  $score = array_merge($score, $mobile_score);
  $score['date'] = date('d.m.Y h:i:s a', strtotime(current_time( 'mysql' )));

  if ( $post_id == 'front_page' ) {
    $page_score = get_option('two-front-page-speed');
  }
  else {
    $page_score = get_post_meta($post_id, 'two_page_speed', TRUE);
  }
  if (empty($page_score)) {
    $page_score = array();
  }
  $key = $old ? 'previous_score' : 'current_score';
  $page_score[$key] = $score;

  if ( $post_id == 'front_page' ) {
    update_option('two-front-page-speed', $page_score);
  }
  else {
    update_post_meta($post_id, 'two_page_speed', $page_score);
  }
}

/**
 * Get the page speed from Google by URL.
 *
 * @param $page_url
 * @param $strategy
 *
 * @return array
 */
function two_google_check_score( $page_url, $strategy ) {
  $google_api_keys = array(
    'AIzaSyCQmF4ZSbZB8prjxci3GWVK4UWc-Yv7vbw',
    'AIzaSyAgXPc9Yp0auiap8L6BsHWoSVzkSYgHdrs',
    'AIzaSyCftPiteYkBsC2hamGbGax5D9JQ4CzexPU',
    'AIzaSyC-6oKLqdvufJnysAxd0O56VgZrCgyNMHg',
    'AIzaSyB1QHYGZZ6JIuUUce4VyBt5gF_-LwI5Xsk'
  );
  $random_index = array_rand( $google_api_keys );
  $key = $google_api_keys[$random_index];
  $url = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=" . $page_url . "&key=".$key;
  if ( $strategy == "mobile" ) {
    $url .= "&strategy=mobile";
  }

  $response = wp_remote_get($url, array('timeout' => 300));
  $data = array();
  if ( is_array($response) && !is_wp_error($response) ) {
    $body = $response['body'];
    $body = json_decode($body);
    if ( isset($body->error) ) {
      $data['error'] = 1;
    }
    else {
      $data[$strategy . '_score'] = 100 * $body->lighthouseResult->categories->performance->score;
      $data[$strategy . '_tti'] = rtrim($body->lighthouseResult->audits->interactive->displayValue, 's');
    }
  }
  else {
    $data['error'] = 1;
  }

  return $data;
}

add_action( 'wp_ajax_two_is_page_optimized', 'two_is_page_optimized' );
function two_is_page_optimized() {
  $nonce = isset($_POST["nonce"]) ? sanitize_text_field( $_POST["nonce"] ) : '';

  if ( !wp_verify_nonce($nonce, 'two_ajax_nonce') ) {
    die('Permission Denied.');
  }

  $post_id = isset($_POST["post_id"]) ? intval( $_POST["post_id"] ) : 0;

  $page_score = get_post_meta( $post_id, 'two_page_speed' );

  if ( !empty($page_score) ) {
    $page_score = end($page_score);
    if ( !empty($page_score['previous_score']) && !empty($page_score['current_score']) ) {
      wp_send_json_success($page_score);
    }
  }

  wp_send_json_error(array('status' => 'pending'));
}

add_action( 'wp_ajax_two_recount_score', 'two_recount_score' );
/* Recount page google score coming from admin bar */
function two_recount_score() {
  $nonce = isset($_POST["nonce"]) ? sanitize_text_field( $_POST["nonce"] ) : '';

  if ( !wp_verify_nonce($nonce, 'two_ajax_nonce') ) {
    die('Permission Denied.');
  }

  $post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : 0;
  two_check_score($post_id);
  if ( $post_id == 'front_page' ) {
    $page_score = get_option('two-front-page-speed');
  }
  else {
    $page_score = get_post_meta($post_id, 'two_page_speed', TRUE);
  }
  wp_send_json_success($page_score);
}

add_action( 'wp_ajax_two_get_optimized_images', 'two_get_optimized_images' );
/* Get website images total count and optimized images count from endpoint */
function two_get_optimized_images() {
    $nonce = isset($_POST["nonce"]) ? sanitize_text_field( $_POST["nonce"] ) : '';

    if ( !wp_verify_nonce($nonce, 'two_ajax_nonce') ) {
        die('Permission Denied.');
    }

    $two_images_count = get_transient("two_images_count");

    if ( !empty($two_images_count) ) {
        return;
    }
    $workspace_id = (int)get_site_option(TENWEBIO_MANAGER_PREFIX . '_workspace_id', 0);
    $domain_id = (int)get_option(TENWEBIO_MANAGER_PREFIX . '_domain_id', 0);
    $access_token = get_site_option('tenweb_access_token');
    $url = TENWEBIO_API_URL . "/compress/workspaces/" . $workspace_id . "/domains/" . $domain_id . "/stat";
    $args = array(
        'timeout' => 15,
        'headers' => array(
            "accept" => "application/x.10weboptimizer.v3+json",
            "authorization" => "Bearer " . $access_token,
        ),
    );
    $response = wp_remote_get($url, $args);
    $images_data = array();
    if ( is_array($response) && !is_wp_error($response) ) {
        $body = json_decode($response['body'], 1);
        if ( isset($body['status']) && $body['status'] == 200 ) {
            $data = $body['data'];

            $total_not_compressed_images_count = (int)($data['not_compressed']['full'] + $data['not_compressed']['thumbs'] + $data['not_compressed']['other']);
            $total_compressed_images_count = (int)($data['compressed']['full'] + $data['compressed']['thumbs'] + $data['compressed']['other']);
            $total_images_count = (int)($total_not_compressed_images_count + $total_compressed_images_count);
            $pages_compressed = $data['pages_compressed'];
            $count = 0;
            foreach ( $pages_compressed as $page ) {
                $count += $page['images_count'];
            }
            $images_data = array('total_images_count' => (int)$total_images_count, 'optimized_images_count' => (int)$count);
            set_transient( 'two_images_count', $images_data, DAY_IN_SECONDS );
        }
    } else {
        $images_data = array('total_images_count' => 0, 'optimized_images_count' => 0);
        set_transient( 'two_images_count', $images_data, DAY_IN_SECONDS );
    }

    wp_send_json_success($images_data);
}

/* Run admin bar functionality */
function two_admin_bar( $wp_admin_bar ) {
    /* post status not to show the admin bar */
    $post_status = array(
        'private',
        'future',
        'draft'
    );
  if ( (isset($_GET['post']) && isset($_GET['action']) && sanitize_text_field($_GET['action']) == 'edit')
  || ( strtolower(TWO_SO_ORGANIZATION_NAME) != '10web' )
  || ( get_the_ID() && array_search(get_post(get_the_ID())->post_status,$post_status) !== false )
      /* remove admin bar for mailpoet plugin(it is blocked all other plugins styles and scripts)*/
  || (isset($_GET['page']) && ( strpos( $_GET['page'], 'mailpoet' ) !== false) )
      // Do not show admin topbar on some pages that break it. (Gravity Forms)
  || ( isset($_GET['page']) && ( 0 === strpos( $_GET['page'], 'gf_' )))
  || !current_user_can('administrator') ) {
    // Do not show admin topbar on Booster page.
    return false;
  }

  require_once 'OptimizerAdminBar.php';
  new TenWebOptimizer\OptimizerAdminBar($wp_admin_bar);
}

if (strtolower(TWO_SO_ORGANIZATION_NAME) == '10web' && !\TenWebOptimizer\OptimizerUtils::is_paid_user()) {
    add_action('enqueue_block_editor_assets', function () {
        //check non-cached pages
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $non_cached_urls = [
                wc_get_page_id( 'shop' ),
                wc_get_page_id( 'cart' ),
                wc_get_page_id( 'myaccount' ),
                wc_get_page_id( 'checkout' ),
                wc_get_page_id( 'store' ),
                wc_get_page_id( 'view_order' ),
            ];
        } else {
            $non_cached_urls = [];
        }
        if ( get_the_ID() && !in_array( get_the_ID(), $non_cached_urls) ) {
            if (!current_user_can('administrator')) {
                return;
            }
            wp_enqueue_script('two-sidebar-plugin', TENWEB_SO_URL . '/assets/js/gutenberg/sidebar-plugin-compiled.js', array(
                'wp-plugins',
                'wp-edit-post'
            ), TENWEB_SO_VERSION);
            wp_localize_script('two-sidebar-plugin', 'two_speed', array(
                'nonce' => wp_create_nonce('two_ajax_nonce'),
                'ajax_url' => admin_url('admin-ajax.php'),
                'optimize_entire_website' => two_reached_limit(),
                'critical_pages' => \TenWebOptimizer\OptimizerUtils::getCriticalPages(),
            ));
            wp_enqueue_style('two_speed_css', TENWEB_SO_URL . '/assets/css/speed.css', array('two-open-sans'), TENWEB_SO_VERSION);
        }
    });

    require_once 'OptimizerElementor.php';
    new TenWebOptimizer\OptimizerElementor();
}

add_action( 'init', 'two_register_meta');
function two_register_meta() {
  $allowed_post_types = array('post', 'page');
  foreach ($allowed_post_types as $type) {
    register_post_meta($type, 'two_page_speed', [
                               'show_in_rest' => array(
                                 'schema' => array(
                                   'type' => 'object',
                                   'properties' => array(
                                     'previous_score' => array(
                                       'type' => 'object',
                                       'properties' => array(
                                         'desktop_score' => array(
                                           'type' => 'number',
                                         ),
                                         'desktop_tti' => array(
                                           'type' => 'string',
                                         ),
                                         'mobile_score' => array(
                                           'type' => 'number',
                                         ),
                                         'mobile_tti' => array(
                                           'type' => 'string',
                                         ),
                                         'date' => array(
                                           'type' => 'string',
                                         ),
                                       ),
                                     ),
                                     'current_score' => array(
                                       'type' => 'object',
                                       'properties' => array(
                                         'desktop_score' => array(
                                           'type' => 'number',
                                         ),
                                         'desktop_tti' => array(
                                           'type' => 'string',
                                         ),
                                         'mobile_score' => array(
                                           'type' => 'number',
                                         ),
                                         'mobile_tti' => array(
                                           'type' => 'string',
                                         ),
                                         'date' => array(
                                           'type' => 'string',
                                         ),
                                       ),
                                     ),
                                   ),
                                 ),
                               ),
                               'single' => TRUE,
                               'type' => 'object'
                             ]);
  }
}

if ( ! wp_next_scheduled( 'two_daily_cron_hook' ) ) {
  wp_schedule_event( time(), 'daily', 'two_daily_cron_hook' );
}

add_action('two_daily_cron_hook', 'two_daily_cron_hook');
function two_daily_cron_hook(){

  foreach([\TenWebOptimizer\OptimizerAdmin::TWO_CLEAR_CACHE_LOG_OPTION_NAME, \TenWebOptimizer\OptimizerCriticalCss::LOG_OPTION_NAME] as $option_name) {
    $logs = get_option($option_name);
    $filtered_logs = [];
    $three_days_in_seconds = 3 * 24 * 60 * 60;
    foreach($logs as $log) {
      if($log["date"] + $three_days_in_seconds > time()){
        $filtered_logs[] = $log;
      }
    }

    update_option($option_name, $filtered_logs, false);
  }


}