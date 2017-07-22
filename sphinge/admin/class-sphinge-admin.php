<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://bwap.ch
 * @since      2.0.0
 *
 * @package    Sphinge
 * @subpackage Sphinge/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sphinge
 * @subpackage Sphinge/admin
 * @author     Team @ BWAP <alan@bwap.ch>
 */
class Sphinge_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sphinge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sphinge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sphinge-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sphinge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sphinge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sphinge-admin.js', array( 'jquery' ), $this->version, false );

	}

        /**
         * Manage menu entries
         *
         * @since   2.0.0
         */
        public function manage_menu() {
            add_submenu_page(
                'options-general.php', // parent slug
                'Sphinge', // page title
                'Sphinge', // menu title
                'manage_options', //capability
                'sphinge', // menu slug
                [$this, 'tmpl_main_settings'] // output function, calls the page template
            );
        }

        /**
         * Make sections available to insert fields later
         *
         * @since   2.0.0
         */
         public function setup_sections() {
             add_settings_section( 'section_main', __('Main settings', 'sphinge'), false, 'sphinge_fields' );
         }

        /**
         * Make fields available to display them in the template later
         *
         * @since   2.0.0
         */
         public function setup_fields() {
            $fields = [
                [
                    'uid' => 'sphinge_secret_key',
                    'label' => __('Secret Key', 'sphinge'),
                    'section' => 'section_main',
                    'type' => 'text',
                    'options' => false,
                    'placeholder' => '',
                    'helper' => '',
                    'supplemental' => __('Get this value from your Sphinge dashboard', 'sphinge'),
                    'default' => ''
                ],
            ];

            // add fields
            foreach( $fields as $field ){
                add_settings_field(
                    $field['uid'], // unique identifier
                    $field['label'], // displayed label
                    [$this, 'field_callback'], // field callback function
                    'sphinge_fields', // section slug from do_settings_section()
                    $field['section'], // section identifier
                    $field // args to be passed to the callbadk function
                );
                register_setting( 'sphinge_fields', $field['uid'] );
            }
         }

        /**
         * Displays the settings template
         *
         * @since   2.0.0
         */
         public function tmpl_main_settings() {
             include 'partials/sphinge-admin-display.php';
         }

        /**
         * Displays the fields
         *
         * @since   2.0.0
         */
         public function field_callback($arguments) {
            $value = get_option( $arguments['uid'] ); // Get the current value, if there is one
            if( ! $value ) { // If no value exists
                $value = $arguments['default']; // Set to our default
            }

            // Check which type of field we want
            switch( $arguments['type'] ){
                case 'text': // If it is a text field
                    printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                    break;
                case 'textarea': // If it is a textarea
                    printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                    break;
                case 'select': // If it is a select dropdown
                    if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                        $options_markup = '';
                        foreach( $arguments['options'] as $key => $label ){
                            $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value, $key, false ), $label );
                        }
                        printf( '<select name="%1$s" id="%1$s">%2$s</select>', $arguments['uid'], $options_markup );
                    }
                    break;
            }

            // If there is help text
            if( $helper = $arguments['helper'] ){
                printf( '<span class="helper"> %s</span>', $helper ); // Show it
            }

            // If there is supplemental text
            if( $supplimental = $arguments['supplemental'] ){
                printf( '<p class="description">%s</p>', $supplimental ); // Show it
            }
         }
}
