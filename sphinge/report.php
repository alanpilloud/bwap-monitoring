<?php
include ('config.php');

/**
*  Just ensure that there is a "secured" call
*/
$_GET['key'] === KEY or die;
$_SERVER['HTTP_MONITORING_AGENT'] == 'sphinge-monitoring' or die;

/**
*  Now, let's go !
*/
define('SPHINGE_VERSION', '1.6.1');

include('../wp-load.php');

if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

global $wpbd;

/**
 * Filters a list by keys
 *
 * @param  Array $list    [description]
 * @param  Array $allowed [description]
 *
 * @return Array          [description]
 */
function filter($list, $allowed) {
    foreach($list as $k => $v) {
        if (!in_array($k, $allowed)) {
            unset($list[$k]);
        }
    }
    return $list;
}

// Get themes
$themes = array_values(array_map(function($theme){
    return array(
        'Type' => 'theme',
        'Name' => $theme->get('Name'),
        'Version' => $theme->get('Version')
    );
}, wp_get_themes()));

// Get Plugins
$plugins = array_values(array_map(function($plugin){
    return array_merge(array('Type' => 'plugin'), filter($plugin, array('Name', 'Version')));
}, get_plugins()));

// Get MU Plugins
$muplugins = array_values(array_map(function($plugin){
    return array_merge(array('Type' => 'muplugin'), filter($plugin, array('Name', 'Version')));
}, get_mu_plugins()));

$extensions = array_merge($themes, $plugins, $muplugins);

$data = array(
    'system' => array(
        'sphinge_version' => SPHINGE_VERSION,
        'wp_version' => get_bloginfo('version'),
        'php_version' => phpversion(),
        'mysql_version' => $wpdb->db_version()
    ),
    'extensions' => $extensions,
    'users' => get_users(array('fields' => array('id', 'user_login', 'user_registered', 'user_email')))
);

echo json_encode($data); exit();
