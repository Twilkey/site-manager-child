<?php
/*
Plugin Name: Catena Site Manager Child
description: This is a plugin that stores functions used by the DevOps team.
Version: 1.0.0
Author: Catena Devs
Author URI:
*/
defined( 'ABSPATH' ) or die();

define('CSM_PLUGIN_PATH', __FILE__);
define('CSM_PLUGIN_DIR', __DIR__);
define('CSM_PLUGIN_URL', plugins_url().'/catena-site-manager-child/');
define('CSM_PLUGIN_VERSION', 1);
define('CSM_PLUGIN_NAMESPACE', 'catena/v'.CSM_PLUGIN_VERSION.'/');

if (!function_exists('is_plugin_active')) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

include_once(CSM_PLUGIN_DIR . "/helpers.php");

if(is_admin()) {
    if(current_user_can( 'manage_options' )) {
        include_once(CSM_PLUGIN_DIR . "/admin/options.php");
    }
} else {
    include_once(CSM_PLUGIN_DIR . "/features/theme-list.php");
    include_once(CSM_PLUGIN_DIR . "/features/plugin-list.php");
    include_once(CSM_PLUGIN_DIR . "/features/users-list.php");
    include_once(CSM_PLUGIN_DIR . "/features/user-check.php");
}