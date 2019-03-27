<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bwap.ch
 * @since      2.0.0
 *
 * @package    Sphinge
 * @subpackage Sphinge/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sphinge
 * @subpackage Sphinge/public
 * @author     Team @ BWAP <alan@bwap.ch>
 */
class Sphinge_Public {

    /**
     * The ID of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The website secret key
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $secret_key   The website secret key
     */
    private $secret_key;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->secret_key = get_option('sphinge_secret_key');
    }

    /**
     * Tries to authenticate the Sphinge client or fail
     *
     * @return  bool  whether the autentication is successful or not
     */
    public function authenticateOrFail() {
        try {
            if ($_SERVER['HTTP_SPHINGE_KEY'] !== $this->secret_key) {
                throw new Exception('invalid secret key');
            }

            if ($_SERVER['HTTP_MONITORING_AGENT'] != 'sphinge-monitoring') {
                throw new Exception('invalid request initiator');
            }

            if (!in_array($_SERVER['HTTP_SPHINGE_ACTION'], ['get_report', 'get_homepage'])) {
                throw new Exception('invalid sphinge action');
            }

            return true;
        } catch (Exception $e) {
            ob_end_clean();
            die; // return an empty response
        }

        return true;
    }

    public function action_selector() {
        if (is_admin()) {
            return false;
        }

        // does the request comes from Sphinge ?
        if (empty($_SERVER['HTTP_SPHINGE_ACTION'])) {
            return false;
        }

        $this->authenticateOrFail();

        switch ($_SERVER['HTTP_SPHINGE_ACTION']) {
            case 'get_report':
                require_once plugin_dir_path( __FILE__ ) . '../includes/class-sphinge-report.php';
                $report = new Sphinge_Report();

            case 'get_homepage':
            default:
                return false;
        }
    }

}
