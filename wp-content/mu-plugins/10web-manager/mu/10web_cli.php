<?php

use Tenweb_Authorization\Login;

function tenweb_cli_site_state($args, $assoc_args)
{
    \Tenweb_Manager\Helper::check_site_state(true);
    WP_CLI::success('Success');
}

function tenweb_cli_login($args, $assoc_args)
{
    $login = Login::get_instance();
    $login_args = array(
        'domain_hash'       => $args[2],
        'type'              => $args[3],
        'is_10web'          => 1,
        'multisite_type'    => $args[4],
        'migrated_site_url' => $args[5],
        'workspace_id'      => $args[6],

    );
    if (!$login->login($args[0], '10webManager', $args[1], $login_args)) {
        $errors = $login->get_errors();
        WP_CLI::error('Cannot Login, Errors: ' . json_encode($errors));
    }
    WP_CLI::log(get_site_option('tenweb_domain_id'));
}

function tenweb_cli_install_template($args, $assoc_args)
{
    if (!defined('TENWEB_INCLUDES_DIR')) {
        WP_CLI::error('Manager plugin not installed');
    }
    require_once(TENWEB_INCLUDES_DIR . "/class-rest-api.php");
    $rest_api = \Tenweb_Manager\RestApi::get_instance();
    $template_id = $args[1];
    $type = $args[2];
    $action = $args[0];
    $template_import_actions = array('install', 'start-import', 'import-plugins', 'import-site', 'finalize-import');
    $template_url = isset($args[3]) ? $args[3] : ''; //10webX

    foreach ($template_import_actions as $import_action) {

        $response = $rest_api->install_template($template_id, $template_url, $type, $action);

        if (!isset($response['status'])) {
            WP_CLI::error('Error has occurred');
        }

        if ((int)$response['status'] != 200) {
            WP_CLI::error(json_encode($response['data_for_response']));
        }
    }


    WP_CLI::success('Successfully installed.');

}

function tenweb_cli_add_sub_site($args, $assoc_args)
{
    $blog_id = $args[0];
    $multi_site = \Tenweb_Manager\Multisite::get_instance();
    $multi_site->blog_activated($blog_id);

    WP_CLI::success('Successfully added site.');
}

function tenweb_cli_delete_sub_site($args, $assoc_args)
{
    $blog_id = $args[0];
    $multi_site = \Tenweb_Manager\Multisite::get_instance();
    $multi_site->blog_deleted($blog_id);

    WP_CLI::success('Successfully deleted site.');
}

if (class_exists('WP_CLI')) {
    WP_CLI::add_command('10web-login', 'tenweb_cli_login');
    WP_CLI::add_command('10web-state', 'tenweb_cli_site_state');
    WP_CLI::add_command('10web-template', 'tenweb_cli_install_template');
    WP_CLI::add_command('10web-add-sub-site', 'tenweb_cli_add_sub_site');
    WP_CLI::add_command('10web-delete-sub-site', 'tenweb_cli_delete_sub_site');
}
