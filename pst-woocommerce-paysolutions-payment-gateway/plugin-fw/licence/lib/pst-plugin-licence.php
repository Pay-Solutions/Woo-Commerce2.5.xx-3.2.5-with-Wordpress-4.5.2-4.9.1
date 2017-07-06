<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'PST_Plugin_Licence' ) ) {
    /**
     * PST Plugin Licence Panel
     *
     * Setting Page to Manage Plugins
     *
     * @class      PST_Plugin_Licence
     * @package    Thaiepay
     * @since      1.0

     */

    class PST_Plugin_Licence extends PST_Licence {

        /**
         * @var array The settings require to add the submenu page "Activation"
         * @since 1.0
         */
        protected $_settings = array();

        /**
         * @var object The single instance of the class
         * @since 1.0
         */
        protected static $_instance = null;

        /**
         * @var string Option name
         * @since 1.0
         */
        protected $_licence_option = 'pst_plugin_licence_activation';

        /**
         * @var string product type
         * @since 1.0
         */
        protected $_product_type = 'plugin';

        /**
         * Constructor
         *
         * @since    1.0
         * @author   Wilawan Onnom' <wilawan@efrainc.com>
         */
        public function __construct() {

            $this->_settings = array(
                'parent_page' => 'pst_plugin_panel',
                'page_title'  => __( 'Licence Activation', 'pst' ),
                'menu_title'  => __( 'Licence Activation', 'pst' ),
                'capability'  => 'manage_options',
                'page'        => 'payh_plugins_activation',
            );

            add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 99 );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 15 );
            add_action( "wp_ajax_activate-{$this->_product_type}", array( $this, 'activate' ) );
            add_action( "wp_ajax_nopriv_activate-{$this->_product_type}", array( $this, 'activate' ) );
            add_action( "wp_ajax_update_licence_information-{$this->_product_type}", array( $this, 'update_licence_information' ) );
            add_action( "wp_ajax_nopriv_update_licence_information-{$this->_product_type}", array( $this, 'update_licence_information' ) );
            add_action( 'pst_licence_after_check', array( $this, 'licence_after_check' ) );
        }

        
        public function licence_after_check() {
            /* === Regenerate Update Plugins Transient === */
            PST_Upgrade()->force_regenerate_update_transient();
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return object Main instance
         *
         * @since  1.0
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Add "Activation" submenu page under PST Plugins
         *
         * @return void
         * @since  1.0
         */
        public function add_submenu_page() {
            add_submenu_page(
                $this->_settings['parent_page'],
                $this->_settings['page_title'],
                $this->_settings['menu_title'],
                $this->_settings['capability'],
                $this->_settings['page'],
                array( $this, 'show_activation_panel' )
            );
        }

        /**
         * Premium plugin registration
         *
         * @param $plugin_init | string | The plugin init file
         * @param $secret_key  | string | The product secret key
         * @param $product_id  | string | The plugin slug (product_id)
         *
         * @return void
         *
         * @since    1.0
         */
        public function register( $plugin_init, $secret_key, $product_id ) {
            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $plugins                             = get_plugins();
            $plugins[$plugin_init]['secret_key'] = $secret_key;
            $plugins[$plugin_init]['product_id'] = $product_id;
            $this->_products[$plugin_init]        = $plugins[$plugin_init];
        }
}
}

/**
 * Main instance of plugin
 *
 * @return object
 * @since  1.0
 */
if( ! function_exists( 'PST_Plugin_Licence' ) ){
    function PST_Plugin_Licence() {
        return PST_Plugin_Licence::instance();
    }
}