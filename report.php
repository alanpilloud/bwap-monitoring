<?php
include ('config.php');
define('AUTHORIZED_UA', 'bwap-monitoring');

/**
*  Just ensure that there is a "secured" call
*/
$_GET['key'] === KEY or die;
$_SERVER['HTTP_USER_AGENT'] == AUTHORIZED_UA or die;

/**
*  Now, let's go !
*/
$tool_info = array(
    'version' => 1.0
);
 
include('../wp-load.php');
 
if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
 
$data = array(
    'bwap-monitoring' => $tool_info,
    'system' => array(
        'version' => get_bloginfo('version')
    ),
    'themes' => wp_get_themes(),
    'plugins' => get_plugins()
);
 
 
echo json_encode($data);