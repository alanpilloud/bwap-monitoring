<?php

class Sphinge_Report
{

    public function __construct()
    {
        // start a new output buffer to catch all warnings or notices to be able to
        // clean the buffer and avoid unwanted strings to be sent.
        ob_start();

        $data = array(
            'system' => array(
                'type' => 'WordPress',
                'wp_version' => get_bloginfo('version'),
                'php_version' => phpversion(),
                'mysql_version' => $GLOBALS['wpdb']->db_version()
            ),
            'extensions' => array_merge(
                $this->getThemes(),
                $this->getPlugins(),
                $this->getMuPlugins()
            ),
            'users' => get_users(array('fields' => array('id', 'user_login', 'user_registered', 'user_email')))
        );

        ob_end_clean();
        echo json_encode($data); exit();
    }

    /**
     * Gets the list of themes installed on this WP instance
     *
     * @return  Array  Array of themes
     */
    private function getThemes()
    {
        return array_values(array_map(function($theme){
            return array(
                'Type' => 'theme',
                'Name' => $theme->get('Name'),
                'Version' => $theme->get('Version')
            );
        }, wp_get_themes()));
    }

    /**
     * Gets the list of plugins installed on this WP instance
     *
     * @return  Array  Array of plugins
     */
    private function getPlugins()
    {
        // this needs to be loaded
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        return array_values(array_map(function($plugin){
            return array_merge(array('Type' => 'plugin'), $this->filter($plugin, array('Name', 'Version')));
        }, get_plugins()));
    }

    /**
     * Gets the list of MUPlugins installed on this WP instance
     *
     * @return  Array  Array of MUPlugins
     */
    private function getMuPlugins()
    {
        return array_values(array_map(function($plugin){
            return array_merge(array('Type' => 'muplugin'), $this->filter($plugin, array('Name', 'Version')));
        }, get_mu_plugins()));
    }

    /**
     * Filters a list by keys
     *
     * @param  Array $list    [description]
     * @param  Array $allowed [description]
     *
     * @return Array          [description]
     */
    private function filter($list, $allowed) {
        foreach($list as $k => $v) {
            if (!in_array($k, $allowed)) {
                unset($list[$k]);
            }
        }
        return $list;
    }
}
