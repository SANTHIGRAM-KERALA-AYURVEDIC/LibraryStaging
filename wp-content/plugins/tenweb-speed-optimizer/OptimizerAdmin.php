<?php

namespace TenWebOptimizer;

use TenWebIO\CompressService;
use TenWebOptimizer\WebPageCache\OptimizerWebPageCache;

/**
 * Class OptimizerAdmin
 */
class OptimizerAdmin
{
    const TWO_DELAYED_DEFAULT_LIST = "getbutton.io,//a.omappapi.com/app/js/api.min.js," .
    "feedbackcompany.com/includes/widgets/feedback-company-widget.min.js,snap.licdn.com/li.lms-analytics/insight.min.js," .
    "static.ads-twitter.com/uwt.js,platform.twitter.com/widgets.js,twq(,/sdk.js#xfbml,static.leadpages.net/leadbars/current/embed.js," .
    "translate.google.com/translate_a/element.js,widget.manychat.com,xfbml.customerchat.js,static.hotjar.com/c/hotjar-," .
    "smartsuppchat.com/loader.js,grecaptcha.execute,Tawk_API,shareaholic,sharethis,simple-share-buttons-adder,addtoany," .
    "font-awesome,wpdiscuz,cookie-law-info,pinit.js,/gtag/js,gtag(,/gtm.js,/gtm-,fbevents.js,fbq(," .
    "google-analytics.com/analytics.js,ga( ',ga(',adsbygoogle,ShopifyBuy,widget.trustpilot.com/bootstrap," .
    "ft.sdk.min.js,apps.elfsight.com/p/platform.js,livechatinc.com/tracking.js,LiveChatWidget,/busting/facebook-tracking/," .
    "olark,pixel-caffeine/build/frontend.js,wp-emoji-release.min.js";

    const TWO_CLEAR_CACHE_LOG_OPTION_NAME = "two_clear_cache_logs";

    protected static $instance = null;

    private $page_url;
    private $TwoSettings;
    function __construct()
    {
        global $TwoSettings;
        $this->TwoSettings = $TwoSettings;
        $this->init_admin();
        $this->page_url = OptimizerUtils::get_page_url();
        $two_triggerPostOptimizationTasks = get_option("two_triggerPostOptimizationTasks");
        if (!empty($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'two_10web_connection')) {
          add_action('in_admin_header', array($this, 'connect_to_tenweb'));
        }else if(isset($_GET["disconnect"])){
          add_action('in_admin_header', array('\TenWebOptimizer\OptimizerAdmin', 'disconnect_from_tenweb'));
        }
        if(isset($_GET["two_export"])){
            $fileName =  "Optimizer_settings_".TENWEB_SO_VERSION."_".date("Y-m-d_H:i:s");
            $data = $TwoSettings->export_settings();
            header('Content-disposition: attachment; filename='.$fileName.'.json');
            header('Content-type: application/json');
            echo esc_html( $data );
            die;
        }
        if(isset($_POST["two_import_settings"])){
            $filePath = $_FILES['two_import']['tmp_name'];
            $TwoSettings->import_settings($filePath);
            header("Refresh:0");
        }
        add_action( 'permalink_structure_changed', array($this, 'wp_permalink_structure_changed_'), 10, 2 );

    }


    public function wp_permalink_structure_changed_( $old_permalink_structure, $permalink_structure ){
        $no_optimize_pages = get_option("no_optimize_pages");
        if(is_array($no_optimize_pages)){
            foreach ($no_optimize_pages as $key=>$val){
                if($key != "front_page") {
                    $post_data = OptimizerUtils::get_permalink_name_by_id( $key );
                    $no_optimize_pages[$key] = $post_data[ 'url' ];
                }
            }
            update_option("no_optimize_pages", $no_optimize_pages);
        }

    }

    public function init_admin()
    {
        ob_start();
        if( !isset( $_GET[ "two_nooptimize" ] ) && !isset( $_GET[ "two_action" ] ) && current_user_can( 'manage_options' ) ) {
            add_action( 'admin_init', array( $this, 'update' ) );
            add_action('admin_init', array($this, 'redirect_after_activation'), 20);
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( '\TenWebOptimizer\OptimizerAdmin', 'two_enqueue_admin_assets' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'two_enqueue_assets' ) );

        add_action( 'wp_ajax_two_settings', array( $this, 'ajax_two_settings' ) );
        add_action( 'wp_ajax_two_update_setting', array( $this, 'ajax_two_update_setting' ) );
        add_action( 'wp_ajax_nopriv_two_manager_clear_cache', array( $this, 'manager_clear_cache' ) );
        add_action( 'wp_ajax_two_critical', array( $this, 'two_critical' ) );
        add_action( 'wp_ajax_two_critical_statuses', array( $this, 'two_critical_statuses' ) );
        add_action( 'wp_ajax_two_deactivate_plugins', array( $this, 'two_deactivate_plugin' ) );
        add_action( 'wp_ajax_two_white_label', array( $this, 'two_white_label' ) );

        add_filter( 'plugin_action_links_' . TENWEB_SO_BASENAME, array( $this, 'add_action_link' ), 10, 2 );
        if ( !is_admin() && !isset( $_GET[ "elementor-preview" ] ) && isset( $_GET[ 'two-menu' ] ) ) {
          add_action( 'admin_bar_menu', array( $this, 'two_admin_bar_menu' ), 99999 );
        }

            add_action( 'wp_ajax_two_css_options', array( $this, 'save_css_options' ) );
            add_action( 'current_screen', array( $this, 'get_plugins_state' ) );

            add_action( 'wp_ajax_two_get_posts_for_critical', array( $this, 'get_posts_for_critical' ) );

            add_action('save_post', array($this, 'post_clear_cache'), 10, 3); // Clearing all the caches to handle templates. Editing a template will clear entire cache.
            add_action('switch_theme', array($this, 'clear_cache'), 10, 0);  // When user change theme.
            add_action('wp_update_nav_menu', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0);  // When a custom menu is update.
            add_action('update_option_sidebars_widgets', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0);  // When you change the order of widgets.
            add_action('update_option_category_base', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0);  // When category permalink is updated.
            add_action('update_option_tag_base', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0);  // When tag permalink is updated.
            add_action('permalink_structure_changed', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0);  // When permalink structure is update.
            add_action('add_link', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0);  // When a link is added.
            add_action('edit_link', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0);  // When a link is updated.
            add_action('delete_link', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0);  // When a link is deleted.
            add_action('customize_save', array($this, 'clear_cache'), 10, 0);  // When customizer is saved.
            add_action('update_option_theme_mods_' . get_option('stylesheet'), array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0); // When location of a menu is updated.
            add_action( 'sidebar_admin_setup', array($this, 'clear_cache'), 10, 0 );
            add_action( 'activated_plugin', array($this, 'clear_cache_conditionally_activate'), 10, 1 );
            add_action( 'upgrader_process_complete', array($this, 'clear_cache_conditionally_update'), 10, 2 );
            add_action( 'deactivated_plugin', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0 );
            add_action( '_core_updated_successfully', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0 );

            //detect ContactForm7 changes
            add_action( 'wpcf7_save_contact_form', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0 );

            //detect WooThemes settings changes
            add_action( 'update_option_woo_options', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0 );

            // Disabled temporarily as ACF triggers save_post from front.
            /*if ( class_exists( 'ACF' ) ) {
              add_action( 'save_post', array('\TenWebOptimizer\OptimizerAdmin', 'acf_update_fields'), 10, 2 );
            }*/

            //detect Formidable changes
            add_action( 'frm_update_form', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0 );

            //detect Contact Form by WP Forms changes
            add_action( 'wpforms_builder_save_form', array($this, 'clear_cache_without_critical_css_regeneration'), 10, 0 );
      }
      add_action( 'wp_ajax_two_flow_set_mode', array( $this, 'two_flow_set_mode' ) );
      add_action( 'wp_ajax_two_update_flow_status', array( $this, 'two_update_flow_status' ) );
      add_action( 'wp_ajax_two_finish_flow', array( $this, 'two_finish_flow' ) );
      add_action( 'wp_ajax_two_flow_incompatible_plugins', array( $this, 'two_flow_incompatible_plugins' ) );
        add_action( 'wp_ajax_two_clear_cloudflare_cache', array( $this, 'two_clear_cloudflare_cache' ));
      add_filter( 'two_clear_cache_action', array( $this, "clear_cache" ), 10, 0 );
      add_action( 'two_clear_cache', array( $this, 'clear_cache' ), 10, 0 );
      add_action( 'pre_current_active_plugins', array( $this, 'add_deactivation_popup' ) );

        /* show custom columns only for administrators */
      if ( strtolower(TWO_SO_ORGANIZATION_NAME) == '10web' && current_user_can('administrator') ) {
        add_action('manage_post_posts_custom_column', array( $this, 'two_manage_posts_column'), 10, 2);
        add_action('manage_page_posts_custom_column', array( $this, 'two_manage_posts_column'), 10, 2);
      }

    }

    public function two_manage_posts_column( $column_key, $post_id ) {
      if ( $column_key == 'two-speed' ) {
        if ( get_post_status($post_id) != 'publish' ) {
          return;
        }
        require 'views/post_custom_column.php';
      }
    }

    public function two_flow_set_mode(){
        if (isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce") && isset($_POST["mode"])) {
            $two_conflicting_plugins = OptimizerUtils::get_conflicting_plugins();
            $two_triggerPostOptimizationTasks = get_option("two_triggerPostOptimizationTasks");
            $mode = sanitize_text_field($_POST["mode"]);
            global $TwoSettings;
            $this->set_global_mode($mode);
            if(isset($_POST["test_mode"]) && $_POST["test_mode"] === "1"){ //if clicked on contact us we don't disable test mode
                $TwoSettings->update_setting("two_test_mode", "on", true);
            } else {
                $TwoSettings->update_setting("two_test_mode", "off", true);
                OptimizerUtils::update_connection_flow_progress("running", "mode_apply_and_test_mode_disable", [$mode]);
            }

            if(empty($two_conflicting_plugins) && $two_triggerPostOptimizationTasks !== "1"){
                update_option("two_triggerPostOptimizationTasks", "1" ,false);
               // OptimizerUtils::triggerPostOptimizationTasks();
            }
            if(isset($_POST["redirect"]) && $_POST["redirect"] === "1"){
                OptimizerUtils::two_redirect(TENWEB_DASHBOARD."?flow_success=1&optimizing_website=".get_site_option(TENWEB_PREFIX . '_domain_id'));
            }

        }
    }
    public function two_update_flow_status(){
        $return_data = array(
            "success" => false,
        );
        if (isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce") && isset($_POST["status"]) && !empty($_POST["status"])) {
            // 1 in-progress
            // 2 finished (Looks good)
            // 3 contact-us
            // 4 contact-us button clicked (new step contact-us clicked)
            update_option("two_flow_status" ,$_POST["status"]);
            if($_POST["status"] === "4"){
                OptimizerUtils::update_connection_flow_progress("running", "contact_us_clicked");
            }elseif ($_POST["status"] === "3"){
                OptimizerUtils::update_connection_flow_progress("running", "contact_us");
            }
            $return_data["success"] = true;
        }
        echo json_encode($return_data);die;
    }
    public function two_finish_flow(){
        $return_data = array(
            "success" => false,
        );
        if (isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce")) {
            update_option("two_flow_status" ,"2");
            OptimizerUtils::update_connection_flow_progress("done", "connection_flow_finish");
            $return_data["success"] = true;
        }
        echo json_encode($return_data);die;
    }

    public function two_flow_incompatible_plugins(){
        if (isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce")) {
            $incompatible_plugins = array();
            $two_triggerPostOptimizationTasks = get_option("two_triggerPostOptimizationTasks");

            if(isset($_POST["two_disable_incompatible_plugins"])&& isset($_POST["incompatible_plugins"]) && is_array($_POST["incompatible_plugins"]) && !empty($_POST["incompatible_plugins"])){
                $incompatible_plugins = sanitize_text_field( $_POST["incompatible_plugins"] );
                $two_conflicting_plugins = OptimizerUtils::get_conflicting_plugins();
                $incompatible_plugins_inactive = array();
                foreach ($incompatible_plugins as $incompatible_plugin){
                    if(isset($two_conflicting_plugins[$incompatible_plugin])){
                        $incompatible_plugins_inactive[] = $two_conflicting_plugins[$incompatible_plugin];
                    }
                }
                if(!empty($incompatible_plugins_inactive) && $two_triggerPostOptimizationTasks !== "1"){
                    OptimizerUtils::update_connection_flow_progress("running", "incompatible_plugins_inactive", $incompatible_plugins_inactive);
                }
                deactivate_plugins($incompatible_plugins);
            }
            if($two_triggerPostOptimizationTasks !== "1"){
                update_option("two_triggerPostOptimizationTasks", "1" ,false);
             //   OptimizerUtils::triggerPostOptimizationTasks($incompatible_plugins);
            }
            OptimizerUtils::two_redirect(TENWEB_DASHBOARD."?flow_success=1&optimizing_website=".get_site_option(TENWEB_PREFIX . '_domain_id'));
        }
    }


    public function add_deactivation_popup() {
      if ( !TENWEB_SO_HOSTED_ON_10WEB && OptimizerUtils::is_tenweb_booster_connected() ) {
        include "views/deactivation_popup.php";
      }
    }

    public function clear_cache_conditionally_activate( $plugin ) {
        $exclude_critical_regeneration = true;
        if ( $plugin ) {
          $plugins_requiring_critical_regeneration = [
            'elementor/elementor.php',
            'elementor-pro/elementor-pro.php',
            'beaver-builder-lite-version/fl-builder.php',
            'siteorigin-panels/siteorigin-panels.php',
            'revslider/revslider.php'
          ];
          // Regenerate criticals only if an allowed plugin is actived.
          if ( in_array( $plugin, $plugins_requiring_critical_regeneration ) ) {
            $exclude_critical_regeneration = false;
          }
        }
        self::clear_cache( false, $exclude_critical_regeneration );
    }

    public function clear_cache_conditionally_update( $upgrader, $hook_extra ) {
        $exclude_critical_regeneration = true;
        if ( $upgrader instanceof \Theme_Upgrader) {
          // Regenerate citicals only if active theme is updated.
          if ( isset ( $hook_extra['themes'] ) && ( is_array( $hook_extra['themes'] ) && in_array( get_option('stylesheet'), $hook_extra['themes'] ) ) ) {
            $exclude_critical_regeneration = false;
          }
        }
        else if ( $upgrader instanceof \Plugin_Upgrader ) {
          $plugins_requiring_critical_regeneration = [
            'elementor/elementor.php',
            'elementor-pro/elementor-pro.php',
            'beaver-builder-lite-version/fl-builder.php',
            'siteorigin-panels/siteorigin-panels.php',
            'revslider/revslider.php'
          ];
          // Regenerate criticals only if an allowed plugin is active and updated.
          if ( isset ( $upgrader->skin->plugin_active ) && $upgrader->skin->plugin_active &&
            isset ( $hook_extra['plugins'] ) && ( is_array( $hook_extra['plugins'] ) && array_intersect( $plugins_requiring_critical_regeneration, $hook_extra['plugins'] ) ) ) {
            $exclude_critical_regeneration = false;
          }
        }
        self::clear_cache( false, $exclude_critical_regeneration );
    }

    public function clear_cache_without_critical_css_regeneration(){
        self::clear_cache(false, true, true, true, 'front_page', false, false);
    }


    public function post_clear_cache($post_ID, $post, $update){
        if(isset($post->post_status) && $post->post_status==="publish" && $update){
            $permalink = sanitize_url(get_permalink( $post_ID ));
            OptimizerWebPageCache::delete_cache_by_url($permalink);
            remove_action( 'save_post', array($this, 'post_clear_cache'), 10, 2 );
        }
    }

    public static function acf_update_fields( $post_id, $post ) {
      if ( $post->post_type == 'acf-field-group' || $post->post_type == 'acf-field' ) {
        self::clear_cache(false, true);
        remove_action( 'save_post', array('\TenWebOptimizer\OptimizerAdmin', 'acf_update_fields'), 10, 2 );
      }
    }

    private static function fix_delayed_list_slashes()
    {
        if (empty(get_option('two_delayed_js_execution_list_updated_fix_slashes'))) {
            global $TwoSettings;
            $option = $TwoSettings->get_settings("two_delayed_js_execution_list");
            if (!empty($option)) {
                $option = implode("",explode("\\",$option));
                $TwoSettings->update_setting("two_delayed_js_execution_list", stripslashes(trim($option)));
            }
        }
        update_option('two_delayed_js_execution_list_updated_fix_slashes', 1);
    }

    public function connect_to_tenweb(){

      if(!empty($_GET['email']) && !empty($_GET['token'])) {

        $email = sanitize_email($_GET['email']);
        $token = sanitize_text_field($_GET['token']);
        $pwd = md5($token);
        $class_login = \Tenweb_Authorization\Login::get_instance();
        $args = [ 'connected_from'=> TENWEB_SO_CONNECTED_FROM ];
        if($class_login->login($email, $pwd, $token, $args) == true && $class_login->check_logged_in()) {
          $two_first_connect = get_option("two_first_connect", false);
          $date = time();
          if(!$two_first_connect){
              update_option("two_first_connect", $date);
          }
          delete_option("two_triggerPostOptimizationTasks");
          delete_option("incompatible_plugins_active_send");
          global $TwoSettings;

          \Tenweb_Authorization\Helper::remove_error_logs();

          $TwoSettings->update_setting("two_connected", "1");
          $TwoSettings->sync_configs_with_plugin_state();
          //todo new_flow_process use get_site_option(TENWEB_PREFIX . '_flow_id') and get_site_option(TENWEB_PREFIX . '_notification_id')
          /*set test mode and set global mode extreme*/
          $this->set_global_mode("extreme");
          /*-----------------------------*/
          self::generateCriticalCssOnInit(true);
          $domain_id = get_site_option(TENWEB_PREFIX . '_domain_id');
          $url = TENWEB_DASHBOARD . '/websites?optimizing_website=' . $domain_id.'&from_plugin='.OptimizerUtils::FROM_PLUGIN;
          if (!empty($_GET['sign_up_from_free_plugin'])) {
                $url .= '&from_free_plugin=1';
          }
          OptimizerUtils::two_redirect( $url );
        } else {
          $errors = $class_login->get_errors();
          $err_msg = (!empty($errors)) ? $errors['message'] : 'Something went wrong. ' .
              'If this website is already connected to the 10Web dashboard via the manager plugin, ' .
              'please disconnect it from the manager plugin to be able to use 10Web Booster.';
          set_site_transient( 'tenweb_so_auth_error_logs', $err_msg, MINUTE_IN_SECONDS );
        }

      }

      if(is_multisite()) {
        OptimizerUtils::two_redirect( network_admin_url() . 'admin.php?page=two_settings_page' );
      }
      OptimizerUtils::two_redirect( get_admin_url() . 'admin.php?page=two_settings_page' );
    }

    public static function disconnect_from_tenweb( $silent = false ){
      global $TwoSettings;
      $TwoSettings->update_setting("two_connected", "0");
      $TwoSettings->sync_configs_with_plugin_state( 'inactive' );
      delete_option("two_flow_status");
      delete_option("two_triggerPostOptimizationTasks");
      delete_option("incompatible_plugins_active_send");
      $class_login = \Tenweb_Authorization\Login::get_instance();
      \Tenweb_Authorization\Helper::remove_error_logs();
      $class_login->logout(false);
      if ( !$silent ) {
        self::clear_cache( false, true );
        if ( is_multisite() ) {
          OptimizerUtils::two_redirect( network_admin_url() . 'admin.php?page=two_settings_page' );
        }
        OptimizerUtils::two_redirect( get_admin_url() . 'admin.php?page=two_settings_page' );
      }
    }

    public static function get_incompatible_active_plugins() {
      $incompatiblePluginList = [];
      foreach (OptimizerUtils::TWO_INCOMPATIBLE_PLUGIN_LIST as $pluginSlug => $pluginName) {
        if (is_plugin_active($pluginSlug)) {
          $incompatiblePluginList[] = $pluginName;
        }
      }

      return $incompatiblePluginList;
    }

    /*
    *  check state activate and deactivate plugin
    */
    public function get_plugins_state(){
        $screen = get_current_screen();
        if($screen->id === "plugins"){
            $two_active_plugins_list = get_option("two_active_plugins_list");
            $active_plugins_current = get_option('active_plugins');
            if(is_array($two_active_plugins_list) && is_array($active_plugins_current)){
                $diff = array_merge(array_diff($active_plugins_current, $two_active_plugins_list), array_diff($two_active_plugins_list, $active_plugins_current));
                if(!empty($diff)){
                    self::clear_cache(false, true);
                    update_option("two_active_plugins_list", $active_plugins_current);
                }
            }else{
                update_option("two_active_plugins_list", $active_plugins_current);
            }
        }
    }

    public function two_admin_bar_menu($wp_admin_bar)
    {
        $wp_admin_bar->add_menu(array(
            'id'    => 'two_options',
            'title' => '10Web Booster',
        ));
    }

    public function two_enqueue_assets()
    {
        wp_register_style('two-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800&display=swap');
        $two_exclude_css = $this->TwoSettings->get_settings("two_exclude_css");
        $two_async_css = $this->TwoSettings->get_settings("two_async_css");
        $two_disable_css = $this->TwoSettings->get_settings("two_disable_css");
        $two_async_page = $this->TwoSettings->get_settings("two_async_page");
        $two_disable_page = $this->TwoSettings->get_settings("two_disable_page");
        $two_async_all = $this->TwoSettings->get_settings("two_async_all");
        $two_disable_css_page = array();
        $two_async_css_page = array();
        if (is_array($two_disable_page) && isset($two_disable_page[$this->page_url])) {
            $two_disable_css_page = explode(",", $two_disable_page[$this->page_url]);
        }
        if (is_array($two_async_page) && isset($two_async_page[$this->page_url])) {
            $two_async_css_page = explode(",", $two_async_page[$this->page_url]);
        }

        $two_async_css = explode(",", $two_async_css);
        $two_disable_css = explode(",", $two_disable_css);
        $two_exclude_css = explode(",", $two_exclude_css);


        wp_enqueue_script('two_admin_bar_js', TENWEB_SO_URL . '/assets/js/two_admin_bar.js', array('jquery'), TENWEB_SO_VERSION);
        wp_enqueue_style('two_admin_bar_css', TENWEB_SO_URL . '/assets/css/two_admin_bar.css', array(), TENWEB_SO_VERSION);
        wp_localize_script('two_admin_bar_js', 'two_admin_vars', array(
            'ajaxurl'              => admin_url('admin-ajax.php'),
            'ajaxnonce'            => wp_create_nonce('two_ajax_nonce'),
            'two_async_css'        => json_encode($two_async_css),
            'two_disable_css'      => json_encode($two_disable_css),
            'two_disable_css_page' => json_encode($two_disable_css_page),
            'two_async_css_page'   => json_encode($two_async_css_page),
            'two_async_all'        => $two_async_all,
            'two_exclude_css'      => $two_exclude_css,
        ));
    }

    public function save_css_options()
    {
        if (isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce")) {
            $page_url_without_pagespeed = '';
            $two_async_css = $this->TwoSettings->get_settings("two_async_css");
            $two_disable_css = $this->TwoSettings->get_settings("two_disable_css");
            $two_async_page = $this->TwoSettings->get_settings("two_async_page");
            $two_disable_page = $this->TwoSettings->get_settings("two_disable_page");
            $two_exclude_css = $this->TwoSettings->get_settings("two_exclude_css");
            $page_url = sanitize_url( $_POST["page_url"] );
            if (OptimizerUtils::get_url_query($page_url, 'PageSpeed') === 'off') {
                $page_url_without_pagespeed = str_replace(array('?PageSpeed=off', '&PageSpeed=off'), '', $page_url);
            }
            $page_url = OptimizerUtils::remove_domain_part($page_url);
            $page_url_without_pagespeed = OptimizerUtils::remove_domain_part($page_url_without_pagespeed);
            $el_id = sanitize_text_field( $_POST["el_id"] );
            $task = sanitize_text_field( $_POST["task"] );
            $state = sanitize_text_field( $_POST["state"] );
            if (!is_array($two_disable_page)) {
                $two_disable_page = array();
            }
            if (!is_array($two_async_page)) {
                $two_async_page = array();
            }
            $two_disable_page[$page_url] = sanitize_text_field($_POST["two_disable_page"]);
            $two_async_page[$page_url] = sanitize_text_field($_POST["two_async_page"]);
            if (!empty($page_url_without_pagespeed)) {
                $two_disable_page[$page_url_without_pagespeed] = sanitize_text_field($_POST["two_disable_page"]);
                $two_async_page[$page_url_without_pagespeed] = sanitize_text_field($_POST["two_async_page"]);
            }
            $this->TwoSettings->update_setting("two_disable_page", $two_disable_page);
            $this->TwoSettings->update_setting("two_async_page", $two_async_page);

            if ($task == "two_async") {
                if ($state === "1") {
                    $this->TwoSettings->update_setting("two_async_css", $two_async_css . "," . $el_id);
                    $this->TwoSettings->update_setting("two_disable_css", str_replace("," . $el_id, "", $two_disable_css));
                } else {
                    $this->TwoSettings->update_setting("two_async_css", str_replace($el_id, "", $two_async_css));
                }
            } else if ($task == "two_disable") {
                if ($state === "1") {
                    $this->TwoSettings->update_setting("two_disable_css", $two_disable_css . "," . $el_id);
                    $this->TwoSettings->update_setting("two_async_css", str_replace("," . $el_id, "", $two_async_css));
                } else {
                    $this->TwoSettings->update_setting("two_disable_css", str_replace($el_id, "", $two_disable_css));
                }
            } else if ($task == "two_exclude_css") {
                if ($state === "1") {
                    $this->TwoSettings->update_setting("two_exclude_css", $two_exclude_css . "," . $el_id);
                    $this->TwoSettings->update_setting("two_async_css", str_replace("," . $el_id, "", $two_async_css));
                    $this->TwoSettings->update_setting("two_disable_css", str_replace("," . $el_id, "", $two_disable_css));
                } else {
                    $this->TwoSettings->update_setting("two_exclude_css", str_replace($el_id, "", $two_disable_css));
                }
            } else {
                $this->TwoSettings->update_setting("two_async_css", str_replace("," . $el_id, "", $two_async_css));
                $this->TwoSettings->update_setting("two_disable_css", str_replace("," . $el_id, "", $two_disable_css));
            }
        }
    }


    public static function two_enqueue_admin_assets($hook_suffix)
    {
      wp_register_style('two-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800&display=swap');
      if ( $hook_suffix == 'toplevel_page_two_settings_page' ) {
          wp_enqueue_script( 'two_deactivate_plugin', TENWEB_SO_URL . '/assets/js/two_deactivate_plugin.js', array( 'jquery' ), TENWEB_SO_VERSION );
          wp_localize_script( 'two_deactivate_plugin', 'two_admin_vars', array(
              'ajaxurl' => admin_url( 'admin-ajax.php' ),
              'ajaxnonce' => wp_create_nonce( 'two_ajax_nonce' )
          ) );
          $two_admin_vars = array(
              'ajaxurl' => admin_url( 'admin-ajax.php' ),
              'ajaxnonce' => wp_create_nonce( 'two_ajax_nonce' )
          );
          if ( isset( $_GET[ 'mode' ] ) && 'advanced' === $_GET[ 'mode' ] && (!defined('TWO_INCOMPATIBLE_ERROR') || !TWO_INCOMPATIBLE_ERROR) ) {
          wp_enqueue_script( 'two_tagsinput_js', TENWEB_SO_URL . '/assets/js/jquery.tagsinput.min.js', array( 'jquery' ), TENWEB_SO_VERSION );
          wp_enqueue_script( 'two_admin_js', TENWEB_SO_URL . '/assets/js/two_admin.js', array( 'jquery' ), TENWEB_SO_VERSION );
          wp_enqueue_script( 'datatables_js', TENWEB_SO_URL . '/assets/js/datatables.min.js', array( 'jquery' ), TENWEB_SO_VERSION );
          wp_enqueue_script( 'two_jquery_multi-select_js', TENWEB_SO_URL . '/assets/js/jquery.multi-select.js', array( 'jquery' ), TENWEB_SO_VERSION );
          wp_enqueue_style( 'two_admin_css', TENWEB_SO_URL . '/assets/css/two_admin.css', "", TENWEB_SO_VERSION );
          wp_enqueue_style( 'two_multi-select_css', TENWEB_SO_URL . '/assets/css/multi-select.css', "", TENWEB_SO_VERSION );
          wp_enqueue_style( 'jquery_tagsinput_css', TENWEB_SO_URL . '/assets/css/jquery.tagsinput.min.css', "", TENWEB_SO_VERSION );
          wp_enqueue_style( 'datatables_min_css', TENWEB_SO_URL . '/assets/css/datatables.min.css', "", TENWEB_SO_VERSION );

          wp_localize_script( 'two_admin_js', 'two_admin_vars', $two_admin_vars );
          wp_enqueue_script( 'two_select2', TENWEB_SO_URL . '/assets/lib/select2/js/select2.min.js', array( 'jquery' ), TENWEB_SO_VERSION );
          wp_enqueue_style( 'two_select2', TENWEB_SO_URL . '/assets/lib/select2/css/select2.min.css', "", TENWEB_SO_VERSION );
        }
        else {
          wp_enqueue_style( 'two_settings_basic_css', TENWEB_SO_URL . '/assets/css/settings_basic.css', array('two-open-sans'), TENWEB_SO_VERSION );
        }
      }
      // hidden page for white label
      if ( $hook_suffix == 'dashboard_page_two_white_label' ) {
          wp_enqueue_style( 'two_google-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap',  array(), TENWEB_SO_VERSION);
          wp_enqueue_style( 'two_admin_css', TENWEB_SO_URL . '/assets/css/settings_basic.css', "", TENWEB_SO_VERSION );
          wp_enqueue_style( 'two_white_label_css', TENWEB_SO_URL . '/assets/css/white_label.css', "", TENWEB_SO_VERSION );
          wp_enqueue_script( 'two_white_label_js', TENWEB_SO_URL . '/assets/js/two_white_label.js', array( 'jquery' ), TENWEB_SO_VERSION );
          wp_localize_script('two_white_label_js', 'two_admin_vars', array(
              'ajaxurl'              => admin_url('admin-ajax.php'),
              'ajaxnonce'            => wp_create_nonce('two_ajax_nonce'),
          ));
      }
      if ($hook_suffix != 'toplevel_page_two_settings_page') {
        // Not add the css to the 10Web Booster page.
        wp_enqueue_style('two_speed_css', TENWEB_SO_URL . '/assets/css/speed.css', array('two-open-sans'), TENWEB_SO_VERSION);
      }
      wp_enqueue_script( 'two_circle_js', TENWEB_SO_URL . '/assets/js/circle-progress.js', array('jquery'), TENWEB_SO_VERSION );
      $is_two_advanced = isset($_GET["page"]) && $_GET["page"] == "two_settings_page" && isset($_GET["mode"]) && $_GET["mode"] == "advanced";
      if(!$is_two_advanced) {
        wp_enqueue_script('two_speed_js', TENWEB_SO_URL . '/assets/js/speed.js', array('jquery', 'two_circle_js'), TENWEB_SO_VERSION);
        wp_localize_script('two_speed_js', 'two_speed', array(
          'nonce' => wp_create_nonce('two_ajax_nonce'),
          'ajax_url' => admin_url('admin-ajax.php'),
          'clearing' => __('Clearing...', 'tenweb-speed-optimizer'),
          'cleared' => __('Cleared cache', 'tenweb-speed-optimizer'),
          'clear' => __('Clear cache', 'tenweb-speed-optimizer'),
          'optimize_entire_website' => two_reached_limit(),
          'critical_pages' => \TenWebOptimizer\OptimizerUtils::getCriticalPages(),
        ));
      }
    }

    public function admin_menu()
    {
        add_menu_page(
            TWO_SO_ORGANIZATION_NAME . ' Booster',
            TWO_SO_ORGANIZATION_NAME . ' Booster',
            'manage_options',
            'two_settings_page',
            array(
                '\TenWebOptimizer\OptimizerAdmin',
                'settings_page',
            ),
            strtolower(TWO_SO_ORGANIZATION_NAME) == '10web' ? TENWEB_SO_URL . '/assets/images/logo_green.svg' : '', 30);
        add_submenu_page(
            null,
            __( 'White Label', 'tenweb-speed-optimizer' ),
            __( 'White Label', 'tenweb-speed-optimizer' ),
            'manage_options',
            'two_white_label',
            array('\TenWebOptimizer\OptimizerAdmin', 'white_label_view')
        );
    }

    public static function white_label_view() {
        require_once("views/white_label_view.php");
    }

    public static function settings_page() {
      if ( isset($_GET['mode']) && 'advanced' === $_GET['mode'] && (!defined('TWO_INCOMPATIBLE_ERROR') || !TWO_INCOMPATIBLE_ERROR) ) {
        if ( OptimizerUtils::is_wpml_active() && (empty($_GET['lang']) || $_GET['lang'] !== 'all') ) {
          $baseUrl = sanitize_text_field($_SERVER['REQUEST_SCHEME']) . '://' . sanitize_text_field($_SERVER['SERVER_NAME']) . sanitize_text_field($_SERVER['REQUEST_URI']);
          $location = add_query_arg(sanitize_text_field($_SERVER['QUERY_STRING']), '', $baseUrl);
          $location = add_query_arg('lang', 'all', $location);
          OptimizerUtils::two_redirect($location);
        }
        require_once("views/settings_view.php");
      }
      else {
        if ((!defined('TWO_INCOMPATIBLE_ERROR') || !TWO_INCOMPATIBLE_ERROR) && OptimizerUtils::is_tenweb_booster_connected() ) {
          require_once "views/settings_basic.php";
        }
        else {
          require_once "views/settings_connect.php";
        }
      }
    }

    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function ajax_two_settings()
    {
        if (isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce") && isset($_POST["task"])) {
            $ajax_task = sanitize_text_field($_POST["task"]);
            if ($ajax_task === "clear_cache") {
                self::clear_cache(true, true);
            } else if ($ajax_task === "regenerate_critical") {
                self::clear_cache( true, false, true, true, 'all' );
            } else if ($ajax_task === "settings") {
                if(isset($_POST["two_critical_pages"])){
                    $two_critical_pages = OptimizerUtils::getCriticalPages();
                    foreach ($_POST["two_critical_pages"] as $key=>$val){
                        if(isset($two_critical_pages[$key]) && isset($_POST["two_critical_pages"][$key])){
                            if(isset($two_critical_pages[$key]["critical_css"])){
                                $_POST["two_critical_pages"][$key]["critical_css"] = $two_critical_pages[$key]["critical_css"];
                            }
                            if(isset($two_critical_pages[$key]["uncritical_css"])){
                                $_POST["two_critical_pages"][$key]["uncritical_css"] = $two_critical_pages[$key]["uncritical_css"];
                            }
                            if(isset($two_critical_pages[$key]["critical_fonts"])){
                                $_POST["two_critical_pages"][$key]["critical_fonts"] = $two_critical_pages[$key]["critical_fonts"];
                            }
                            if(isset($two_critical_pages[$key]["critical_bg"])){
                                $_POST["two_critical_pages"][$key]["critical_bg"] = $two_critical_pages[$key]["critical_bg"];
                            }
                            if(isset($two_critical_pages[$key]["status"])){
                                $_POST["two_critical_pages"][$key]["status"] = $two_critical_pages[$key]["status"];
                            }
                            if(isset($two_critical_pages[$key]["critical_date"])){
                                $_POST["two_critical_pages"][$key]["critical_date"] = $two_critical_pages[$key]["critical_date"];
                            }
                        }
                    }
                }
                $this->TwoSettings->set_settings($_POST);
            } else if ( $ajax_task == "regenerate_webp" ) {
              $image_list = sanitize_text_field( $_POST[ "image_list" ] );
              $url_list = sanitize_text_field( $_POST[ "url_list" ] );
              self::request_webp_action( 'regenerate', $url_list );
            } else if ( $ajax_task == "delete_webp" ) {
              self::request_webp_action( 'delete' );
            }else if($ajax_task == "delete_clear_cache_logs"){
              delete_option(self::TWO_CLEAR_CACHE_LOG_OPTION_NAME);
            } else if($ajax_task == "delete_critical_css_logs") {
              delete_option(OptimizerCriticalCss::LOG_OPTION_NAME);
            }
            // Purge 10Web cache.
            do_action('tenweb_purge_all_caches');
            $message = apply_filters( 'two_save_settings_message', __('Success!', 'tenweb-speed-optimizer') );
            $code = apply_filters( 'two_save_settings_code', 0 );
            $two_webp_delivery_working = OptimizerUtils::testWebPDelivery();
            echo json_encode( array( "success" => true, "message" => $message, 'code' => $code, 'webp_delivery_status' => $two_webp_delivery_working ) );
            die;
        }
        echo json_encode(array("success" => false));
        die;
    }
    public function ajax_two_update_setting() {
        if ( isset( $_POST[ "nonce" ] ) && wp_verify_nonce( $_POST["nonce"], "two_ajax_nonce" ) ) {
            $name = sanitize_text_field( $_POST["name"] );
            $value = sanitize_text_field( $_POST["value"] );
            $this->TwoSettings->update_setting( $name, $value );
            echo json_encode( array( "success" => true ) );
            die;
        }
        echo json_encode( array( "success" => false ) );
        die;
    }
    public static function request_webp_action( $task, $url_list = '' ) {
      try {
        $tenweb_domain_id = get_option('tenweb_domain_id');
        $request_data = null;
        $method = null;
        $endpoint = null;
        $compressService = null;
        if ( 'regenerate' === $task ) {
          $image_list = array();
          $page_list = array();
          foreach ( explode( ' ', $url_list ) as $url ) {
            if ( 0 === strpos( $url, site_url() ) ) {
              if ( preg_match( '/\.(jpg|png|jpeg)$/', $url ) ) {
                $image_list[] = $url;
              }
              else {
                $page_list[] = $url . ( strpos( $url, '?' ) > -1 ? '&' : '?' ) . 'two_nooptimize=1';
              }
            }
          }
          if (!empty($image_list) && !TENWEB_SO_HOSTED_ON_10WEB) {
              //if we have array of urls, and website is not hosted on 10Web call internal IO classes to optimize them
              $compressService = new CompressService();
          } else {
              $request_data = array(
                  'force_convert' => 0,
                  'quality' => 50,
                  'image_list' => implode( ',', $image_list ),
                  'url_list' => implode( ',', $page_list ),
                  'site_url' => site_url(),
              );
              $method = 'POST';
              $endpoint = \TenWebIO\Api::API_WEBP_CONVERT;
          }
        }
        else if ( 'delete' === $task ) {
          $request_data = array();
          $method = 'POST';
          $endpoint = \TenWebIO\Api::API_DELETE_WEBP_CONVERTED;
        }
        if ( $method ) {
          $api_instance = new \TenWebIO\Api( $endpoint );
          $response = $api_instance->apiRequest( $method, $request_data );
          if ( false !==  $response ) {
            $response_data = array(
              "status" => "success",
            );
          } else {
            $response_data = array(
              "status" => "error",
              "error" => $response
            );
          }
        } else if ($compressService && !empty($image_list)) {
            $compressService->compressCustom($image_list, 'front_page',1);
            $response_data = array(
                "status" => "success",
            );
        }
        else {
          $response_data = array(
            "status" => "error",
            "error" => "Invalid Task"
          );
        }
      } catch (\Exception $e) {
        $response_data = array(
          "status" => "error",
          "error"  => $e->getMessage()
        );
      }
      echo json_encode( $response_data );
      die;
    }

    public function two_critical(){
      $return_data = array(
        "success"=>false,
      );
      if(isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce")) {
        set_transient("two_critical_in_process", "1" , 360);
        if(isset($_POST["data"]["task"])){
            $task = sanitize_text_field( $_POST["data"]["task"] );
            if($task === "generate"){
                if(isset($_POST["data"])){
                    $_POST["data"]["initiator"] = "manual_from_ccss_tab";
                }
                $return_data = OptimizerCriticalCss::generateCriticalCSS($_POST);
            }
            elseif($task === "delete" && isset($_POST["data"]["page_id"])){
              $page_id = sanitize_text_field( $_POST["data"]["page_id"] );
              $critical_key = "two_critical_".$page_id;
              $critical_in_progress_key = "two_critical_in_progress_" . $page_id;
              delete_transient($critical_key);
              delete_transient($critical_in_progress_key);
              if ( 'front_page' == $page_id || false !== strpos( $page_id, 'term_' ) ) {
                $two_critical_pages = $this->TwoSettings->get_settings("two_critical_pages");
                unset($two_critical_pages[$page_id]);
                unset($two_critical_pages[""]);
                $this->TwoSettings->update_setting("two_critical_pages", $two_critical_pages);
              }
              else {
                delete_post_meta( $page_id, 'two_critical_pages' );
              }
              $prefix = "critical/two_".$page_id."_*.*";
              OptimizerUtils::delete_files_by_prefix($prefix);
              self::clear_cache(false, true);
              if ( has_action( "two_page_optimized_removed" ) ) {
                do_action( 'two_page_optimized_removed', $page_id );
              }
            }
            elseif ( 'insert/update' === $task && isset( $_POST["data"]["page_id"] ) ) {
              $page_id = sanitize_text_field( $_POST["data"]["page_id"] );
              $two_critical_pages = $this->TwoSettings->get_settings("two_critical_pages");


              $update_data = map_deep( $_POST["data"]["two_critical_pages"][$page_id], 'sanitize_text_field' );
              if(isset($two_critical_pages[$page_id])){
                  if(isset($two_critical_pages[$page_id]["critical_css"])){
                      $update_data["critical_css"] = $two_critical_pages[$page_id]["critical_css"];
                  }
                  if(isset($two_critical_pages[$page_id]["uncritical_css"])){
                      $update_data["uncritical_css"] = $two_critical_pages[$page_id]["uncritical_css"];
                  }
                  if(isset($two_critical_pages[$page_id]["critical_fonts"])){
                      $update_data["critical_fonts"] = $two_critical_pages[$page_id]["critical_fonts"];
                  }
                  if(isset($two_critical_pages[$page_id]["critical_bg"])){
                      $update_data["critical_bg"] = $two_critical_pages[$page_id]["critical_bg"];
                  }
                  if(isset($two_critical_pages[$page_id]["critical_date"])){
                      $update_data["critical_date"] = $two_critical_pages[$page_id]["critical_date"];
                  }
              }
              if ( !is_array( $two_critical_pages ) ) {
                $two_critical_pages = array();
              }
              $two_critical_pages[$page_id] = $update_data;
              $this->TwoSettings->update_setting("two_critical_pages", $two_critical_pages);
            }
        }
      }
      echo json_encode($return_data);die;
    }
    public function two_critical_statuses(){
        if(isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce")) {
            $two_critical_pages = OptimizerUtils::getCriticalPages();
            $two_critical_in_process = get_transient("two_critical_in_process");
            $return_data = array(
                'pages' => array(),
                'status' => $two_critical_in_process,
            );
            if ( is_array( $two_critical_pages ) ) {
              foreach ( $two_critical_pages as $page_id => $critical_page ) {
                $critical_page_status = $critical_page[ "status" ];
                if ( $critical_page_status == "success" ) {
                  if ( !isset( $critical_page[ "critical_css" ] ) || empty( $critical_page[ "critical_css" ] ) ) {
                    $critical_page_status = "not_started";
                    $two_critical_pages[ $page_id ][ "status" ] = "not_started";
                  }
                }
                $return_data[ "pages" ][] = array(
                  'page_id' => $critical_page[ "id" ],
                  'status' => $critical_page_status,
                );
              }
            }
            $this->TwoSettings->update_setting("two_critical_pages", $two_critical_pages);
            echo json_encode($return_data, true);die;
        }
    }
    public function add_action_link($links, $file)
    {
        if (TENWEB_SO_BASENAME === $file) {
            $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=two_settings_page')) . '">' . __('Settings') . '</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    public function manager_clear_cache(){
        $two_token_clear_cache = get_transient("two_token_clear_cache");
        if(isset($_POST["two_token"]) && $two_token_clear_cache === $_POST["two_token"]){
            delete_transient("two_token_clear_cache");
            self::clear_cache(false, !$_POST['regenerate_critical_css']);
        }
    }


    public function two_clear_cloudflare_cache(){
        if (isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce")) {
            if(isset($_POST["page_url"])){
                OptimizerUtils::clear_cloudflare_cache(array(sanitize_url($_POST["page_url"])));
            }
        }
    }

    public static function clear_cache(
        $is_json = false,
        $excludeCriticalRegeneration = false,
        $delete_tenweb_manager_cache = true,
        $delete_cloudflare_cache = true,
        $critical_regeneration_mode = 'front_page',
        $clear_critical = false,
        $clear_two_cloudflare_cache = true
    )
    {

        $date = time();
        global $TwoSettings;
        $TwoSettings->update_setting("two_clear_cache_date", $date );
        $TwoSettings->update_setting("tenweb_so_version", TENWEB_SO_VERSION );
        // We do not want to clear the cache during template import.
        if ( get_option(TENWEB_PREFIX."_import_in_progress") == 1 ) {
          return false;
        }
        $dir = OptimizerCache::get_path();
        $delete_cache_db = OptimizerUtils::delete_all_cache_db();
        OptimizerCacheStructure::flushAllCache();
        $exclude_dir = null;
        $two_critical_status = $TwoSettings->get_settings("two_critical_status");
        if($excludeCriticalRegeneration){
            $exclude_dir = "critical";
        }
        $delete_cache_file = OptimizerUtils::delete_all_cache_file($dir, [$dir, $dir . 'css', $dir . 'js', $dir . 'critical'],$exclude_dir);
        OptimizerUtils::purge_pagespeed_cache();
        if ($delete_tenweb_manager_cache) {
            do_action('tenweb_purge_all_caches', false);
        }
        if ($delete_cloudflare_cache) {
            OptimizerUtils::flushCloudflareCache();
        }
        wp_cache_flush();

        $success = false;

        if ($delete_cache_file && $delete_cache_db) {
            $success = true;
        }

        OptimizerUtils::clear_third_party_cache();

        if(!$excludeCriticalRegeneration && $two_critical_status === "true") {
            OptimizerUtils::regenerate_critical( $critical_regeneration_mode );
        }
        if($clear_critical){
            self::clear_critical_cache();
        }

        $log_data = [
          "is_json" => $is_json,
          "excludeCriticalRegeneration" => $excludeCriticalRegeneration,
          "delete_tenweb_manager_cache" => $delete_tenweb_manager_cache,
          "delete_cloudflare_cache" => $delete_cloudflare_cache,
          "critical_regeneration_mode" => $critical_regeneration_mode,
          "clear_critical" => boolval($clear_critical),
        ];

        self::add_log(self::TWO_CLEAR_CACHE_LOG_OPTION_NAME, $log_data);
        if($clear_two_cloudflare_cache && !TENWEB_SO_HOSTED_ON_10WEB){
            OptimizerUtils::clear_cloudflare_cache();
        }
        if ($is_json) {
            echo json_encode(array("success" => $success));
            die;
        }
        return $success;
    }
    public static function clear_critical_cache(){
        global $TwoSettings;
        $two_critical_pages = OptimizerUtils::getCriticalPages();
        $home_critical = false;
        if(is_array($two_critical_pages)){
            foreach ($two_critical_pages as $id=> $page){
                if(!$home_critical && $id === "front_page"){
                    $home_critical = true;
                }
                $two_critical_pages[$id]["status"] = "not_started";
                unset($two_critical_pages[$id]["critical_css"], $two_critical_pages[$id]["uncritical_css"], $two_critical_pages[$id]["critical_fonts"], $two_critical_pages[$id]["critical_bg"], $two_critical_pages[$id]["critical_date"]);
            }
            $TwoSettings->update_setting("two_critical_pages", $two_critical_pages);
        }
        $prefix = "critical/two_*.*";
        OptimizerUtils::delete_files_by_prefix($prefix);
        if($home_critical){
            OptimizerCriticalCss::generate_critical_css_by_id("front_page");
        }
    }

    public static function two_activate($networkwide)
    {

        if (function_exists('is_multisite') && is_multisite()) {
            // Check if it is a network activation - if so, run the activation function for each blog id.
            if ($networkwide) {
                global $wpdb;
                // Get all blog ids.
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    self::activate();
                    restore_current_blog();
                }

                return;
            }
        }
        add_option('redirect_after_activation_option', true);
        self::activate();
    }

    public static function activate()
    {
       global $TwoSettings;
       $two_version = get_option("tw_optimize_version");
       if($two_version === false) {
           $TwoSettings->set_default_settings();
       }
       if(\Tenweb_Authorization\Login::get_instance()->check_logged_in()){
           $TwoSettings->update_setting("two_connected", "1");

           $habit_version = "2.8.1";
           if ( version_compare($two_version, $habit_version, "<") ) {
             // Check already optimized pages scores before and after optimize.
             $optimized_pages = array_keys(\TenWebOptimizer\OptimizerUtils::getCriticalPages());
             foreach ( $optimized_pages as $optimizedPageID ) {
               if ( $optimizedPageID != 'front_page' ) {
                 two_check_score($optimizedPageID, TRUE, TRUE); /* Not optimized.*/
                 two_check_score($optimizedPageID); /* Optimized.*/
               }
             }
           }
       }else{
           $TwoSettings->update_setting("two_connected", "0");
       }
       $TwoSettings->update_setting("two_critical_url_args", "PageSpeed=off&two_nooptimize=1&two_action=generating_critical_css");
       OptimizerUtils::testWebPDelivery();
       self::add_two_delayed_js_execution_list();
       $tenweb_so_regenerate_critical_on_update = false;
       if (TENWEB_SO_HOSTED_ON_10WEB && strpos(get_site_url(), 'TENWEBLXC') === false) { //if hosted on 10web
           // Set WebP delivery to on by default.
           if ( false === $TwoSettings->get_settings( 'two_enable_nginx_webp_delivery' ) ) {
               $TwoSettings->update_setting( "two_enable_nginx_webp_delivery", 'on' );
           }
           if ( !$two_version || $tenweb_so_regenerate_critical_on_update ) {
             self::generateCriticalCssOnInit();
           }
       } elseif (!TENWEB_SO_HOSTED_ON_10WEB) { //connected website
           if ( \Tenweb_Authorization\Login::get_instance()->check_logged_in() && ( !$two_version || $tenweb_so_regenerate_critical_on_update ) ) {
               self::generateCriticalCssOnInit();
           }
       }
    }

    public static function generateCriticalCssOnInit($rightAfterConnect = false) {
      $two_version = get_option( "tw_optimize_version" );
      $two_critical_pages = OptimizerUtils::getCriticalPages();

      if ( empty( $two_critical_pages ) ) {
          OptimizerCriticalCss::generate_critical_css_by_id("front_page", $rightAfterConnect);
      }
      else {

        if ( $two_version === false || version_compare( $two_version, "1.54.6", "<" ) ) {
          if ( OptimizerUtils::is_wpml_active() ) {
            OptimizerUtils::add_wpml_home_pages_into_critical_pages( $two_critical_pages, $two_critical_pages[ "front_page" ][ "url" ] );
          }
        }
        if(TENWEB_SO_HOSTED_ON_10WEB){
            OptimizerUtils::regenerate_critical( 'all', $rightAfterConnect );
        }elseif (\Tenweb_Authorization\Login::get_instance()->check_logged_in()){
            OptimizerUtils::regenerate_critical( 'front_page', $rightAfterConnect );
        }
      }
    }

    public function update()
    {
        $version = get_option('tw_optimize_version');
        $new_version = TENWEB_SO_VERSION;
        if (version_compare($version, $new_version, '<')) {

            global $TwoSettings;
            /* Update TW optimize version */
            update_option("tw_optimize_version", $new_version);
            self::add_two_delayed_js_execution_list();
            self::fix_delayed_list_slashes();
            $two_critical_sizes = $TwoSettings->get_settings("two_critical_sizes");
            if($two_critical_sizes === false){
                $TwoSettings->set_critical_defaults();
            }
            if($TwoSettings->get_settings("two_critical_status") === "true" && $TwoSettings->get_settings("two_critical_font_status", null) === null) {
              $TwoSettings->update_setting("two_critical_font_status", "true");
            }
            if(TENWEB_SO_HOSTED_ON_10WEB || \Tenweb_Authorization\Login::get_instance()->check_logged_in()){
                self::clear_cache();
            }
        }

    }

    public static function two_uninstall()
    {
        delete_option("two_first_connect");
        delete_option("two_include_inline_js");
        delete_option("two_include_inline_css");
        delete_option("two_dequeue_jquery_migrate");
        delete_option("two_delay_js_execution");
        delete_option("two_exclude_js");
        delete_option("two_exclude_css");
        delete_option("two_async_css");
        delete_option("two_async_all");
        delete_option("two_async_font");

        delete_option("two_do_not_optimize_images");
        delete_option("two_enable_nginx_webp_delivery");

        delete_option("two_lazyload");
        delete_option("two_youtube_vimeo_iframe_lazyload");
        delete_option("two_iframe_lazyload");
        delete_option("two_video_lazyload");

        delete_option("two_bg_lazyload");
        delete_option("two_gzip");
        delete_option("two_page_cache");
        delete_option("two_disable_css");
        delete_option("two_fonts_to_preload");
        delete_option("two_exclude_lazyload");
        delete_option("two_do_not_optimize_images");
        delete_option("two_enable_nginx_webp_delivery");
        delete_option("two_exclude_images_for_optimize");
        delete_option("two_serve_optimized_bg_image");


        delete_option("two_async_page");
        delete_option("two_disable_page");
        delete_option("two_change_minify");

        delete_option("two_aggregate_js");
        delete_option("two_aggregate_css");
        delete_option("two_minify_js");
        delete_option("two_minify_css");
        delete_option("two_use_font_ready");
    }

    public static function two_deactivate()
    {
        // Disable WebP delivery on plugin deactivation.
        global $TwoSettings;

        $two_critical_pages = OptimizerUtils::getCriticalPages();
        if(is_array($two_critical_pages)) {
            foreach ($two_critical_pages as $id => $page) {
                if(isset($page["status"]) && $page["status"] == "in_progress"){
                    $page["status"] = "not_started";
                }
                $critical_key = "two_critical_".$id;
                $critical_in_progress_key = "two_critical_in_progress_" . $id;
                delete_transient($critical_key);
                delete_transient($critical_in_progress_key);
            }
            $TwoSettings->update_setting("two_critical_pages", $two_critical_pages);
        }

        $timestamp = wp_next_scheduled('two_daily_cron_hook');
        if($timestamp) {
          wp_unschedule_event($timestamp, 'two_daily_cron_hook');
        }

        $TwoSettings->update_setting( "two_enable_nginx_webp_delivery", '' );
        if ( isset($_GET['disconnect']) ) {
          self::disconnect_from_tenweb( true );
        }
        self::clear_cache(false, true);
    }

    private static function add_two_delayed_js_execution_list()
    {
        global $TwoSettings;
        $two_delayed_js_execution_list_updated = get_option("two_delayed_js_execution_list_updated");
        if ($two_delayed_js_execution_list_updated != "1") {
            $two_delayed_js_execution_list = $TwoSettings->get_settings("two_delayed_js_execution_list");
            $default_data = self::TWO_DELAYED_DEFAULT_LIST;
            if (isset($two_delayed_js_execution_list) && $two_delayed_js_execution_list && !empty($two_delayed_js_execution_list)) {
                $default_data = $two_delayed_js_execution_list . "," . $default_data;
            }

            $TwoSettings->update_setting(
                "two_delayed_js_execution_list",
                $default_data
            );
            update_option("two_delayed_js_execution_list_updated", "1");
        }
    }

    public function get_posts_for_critical() {
      if (isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce")) {
        die('Invalid nonce');
      }
      $return = array();
      $two_critical_pages = OptimizerUtils::getCriticalPages();
      $is_wpml_active = OptimizerUtils::is_wpml_active();

      if ( !isset( $two_critical_pages[ 'front_page' ] ) ) {
        $flag_url = null;
        if($is_wpml_active){
          $flag_url = OptimizerUtils::get_wpml_post_flag_url('front_page');
        }
        $return[] = array( 'front_page', 'Home', site_url(), $flag_url);
      }


      if($is_wpml_active) {
        do_action('wpml_switch_language', "all"); // to get translated posts to
      }

      add_filter( 'posts_where', array( $this, 'title_filter' ), 10, 2 );
      $search_params = array(
        'post_type' => 'any',
        'post_status' => 'publish',
        'posts_per_page' => 50
      );
      if ( isset( $_GET['q'] ) ) {
        $search_params[ 'search_post_title' ] = sanitize_text_field( $_GET['q'] );
      }
      $search_results = new \WP_Query( $search_params );

      if ( $search_results->have_posts() ) :
        while ( $search_results->have_posts() ) : $search_results->the_post();
          if ( !isset( $two_critical_pages[$search_results->post->ID] ) ) {
              if ( 'page' !== get_option( 'show_on_front' )
                || !get_option( 'page_on_front' ) || get_option( 'page_on_front' ) != $search_results->post->ID
              ) {
                // shorten the title a little
                $title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
                $flag_url = null;
                if($is_wpml_active){
                  $flag_url = OptimizerUtils::get_wpml_post_flag_url($search_results->post->ID);
                }
                $return[] = array( $search_results->post->ID, $title, get_permalink( $search_results->post->ID ), $flag_url );
              }
          }
        endwhile;
      endif;
      remove_filter( 'posts_where', array( $this, 'title_filter' ) );
      echo json_encode( $return );
      die;
    }

    public static function title_filter( $where, $wp_query ) {
      global $wpdb;
      if ( $search_term = $wp_query->get( 'search_post_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term ) . '%\'';
      }
      return $where;
    }

    function redirect_after_activation() {
        if (!TENWEB_SO_HOSTED_ON_10WEB && !\Tenweb_Authorization\Login::get_instance()->check_logged_in() && get_option('redirect_after_activation_option', false)) {
            delete_option('redirect_after_activation_option');
            OptimizerUtils::two_redirect( admin_url( 'admin.php?page=two_settings_page&two_after_activation=true' ) );
        }
    }

    public static function two_deactivate_plugin()
    {
        if(isset($_POST["nonce"], $_POST['plugin_slug']) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce") && current_user_can("deactivate_plugins")) {
            $two_incompatible_plugins =  OptimizerUtils::get_conflicting_plugins();
            $plugin_slug = sanitize_text_field( $_POST['plugin_slug'] );
            if(array_key_exists($plugin_slug ,$two_incompatible_plugins)){
                deactivate_plugins($plugin_slug);
            }
        }

    }

    public static function two_white_label()
    {
        if(isset($_POST["nonce"], $_POST['company_name']) && wp_verify_nonce($_POST["nonce"], "two_ajax_nonce")) {
            $company_name = trim( sanitize_text_field( $_POST['company_name'] ) );
            update_option('two_so_organization_name', $company_name );
        }
    }

    private function set_global_mode($mode){
        global $TwoSettings;
        $mode_list = OptimizerUtils::get_modes();
        $settings_names = $TwoSettings->settings_names;
        foreach ($mode_list[$mode] as $key => $val) {
            if($key === "two_delay_all_js_execution"){
                if ($val) {
                    $TwoSettings->update_setting("two_delay_all_js_execution", "on");
                } else {
                    $TwoSettings->update_setting("two_delay_all_js_execution", "");
                }
            }
            elseif (isset($settings_names[$key])) {
                $TwoSettings->update_setting($key, $val);
            } elseif ($key === "critical_enabled") {
                if ($val) {
                    $TwoSettings->update_setting("two_critical_status", "true");
                } else {
                    $TwoSettings->update_setting("two_critical_status", "");
                }
            }
        }
        update_option("two_default_mode", $mode_list[$mode]);
    }

  public static function add_log($option_name, $log_data){

    foreach($log_data as $key => $value) {
      if(!is_bool($value)){
        $log_data[$key] = sanitize_text_field($value);
      }
    }

    $log_data["stack_trace"] = self::get_stack_trace();
    $log_data["date"] = time();

    $clear_cache_log = get_option($option_name, []);
    $clear_cache_log[] = $log_data;
    update_option($option_name, $clear_cache_log, false);
  }

  public static function get_stack_trace($limit = 10, $remove_first_n=2){
    $stack = [];
    foreach(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit + $remove_first_n) as $num => $frame) {

      // remove first n frames. This will remove get_stack_trace and add_log functions
      if($num < $remove_first_n) {
        continue;
      }

      $file = str_replace(ABSPATH, "", $frame["file"]);
      $line = $frame["line"];
      $func = $frame["function"];
      $class = (isset($frame["class"])) ? $frame["class"] : null;
      $type = (isset($frame["type"])) ? $frame["type"] : null;

      $stack_msg = '#' . $num . ' ' . $file . '(' . $line . "): ";

      if($class) {
        $stack_msg .= $class . $type;
      }

      $stack[] = $stack_msg . $func . '()';
    }

    return $stack;
  }
}
