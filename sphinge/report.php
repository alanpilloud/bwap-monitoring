<?php
// start a new output buffer to catch all warnings or notices to be able to
// clean the buffer and avoid unwanted strings to be sent.
ob_start();
try {
    if (!include('config.php')) {
        throw new Exception('config.php file is missing');
    }

    /**
    *  Just ensure that there is a "secured" call
    */
    if ($_GET['key'] !== SPHINGE_KEY) {
        throw new Exception('invalid secret key');
    }

    if ($_SERVER['HTTP_MONITORING_AGENT'] != 'sphinge-monitoring') {
        throw new Exception('invalid request initiator');
    }

    if (!include(SPHINGE_WP_LOAD_PATH.'/wp-load.php')) {
        throw new Exception('wp-load.php file is missing');
    }

    if (!function_exists('get_plugins')) {
        if (!include(ABSPATH.SPHINGE_WP_ADMIN_PATH.'/includes/plugin.php')) {
            throw new Exception('includes/plugin.php file is missing');
        }
    }
} catch (Exception $e) {
    ob_end_clean();
    die; // return an empty response
}

/**
*  Now, let's go !
*/
define('SPHINGE_VERSION', '1.0.1-beta');

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

ob_end_clean();
echo json_encode($data); exit();
