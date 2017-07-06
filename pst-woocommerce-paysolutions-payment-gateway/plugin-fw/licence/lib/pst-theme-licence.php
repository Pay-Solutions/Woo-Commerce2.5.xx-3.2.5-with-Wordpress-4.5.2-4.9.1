<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'PST_Theme_Licence' ) ) {
    /**
     * PST Plugin Licence Panel
     *
     * Setting Page to Manage Plugins
     *
     * @class      PST_Theme_Licence
     * @package    Thaiepay
     * @since      1.0
     */

    class PST_Theme_Licence extends PST_Licence {

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
        protected $_licence_option = 'pst_theme_licence_activation';

        /**
         * @var string product type
         * @since 1.0
         */
        protected $_product_type = 'theme';

        /**
         * Constructor
         *
         * @since    1.0
         * @author   Wilawan Onnom' <wilawan@efrainc.com>
         */
        public function __construct() {

            $this->_settings = array(
                'parent_page' => 'pst_product_panel',
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

            $admin_tree = array(
                    'parent_slug' => apply_filters( 'pst_licence_parent_slug', 'pst_panel'),
                    'page_title'  => __( 'Licence Activation', 'pst' ),
                    'menu_title'  => __( 'Licence Activation', 'pst' ),
                    'capability'  => 'manage_options',
                    'menu_slug'   => 'pst_panel_licence',
                    'function'    => 'show_activation_panel'
                );

             add_submenu_page( $admin_tree['parent_slug'],
                sprintf( __( '%s', 'pst' ), $admin_tree['page_title'] ),
                sprintf( __( '%s', 'pst' ), $admin_tree['menu_title'] ),
                $admin_tree['capability'],
                $admin_tree['menu_slug'],
                array( $this, $admin_tree['function'] )
            );
        }

        /**
         * Premium product registration
         *
         * @param $product_init | string | The product init file
         * @param $secret_key  | string | The product secret key
         * @param $product_id  | string | The product slug (product_id)
         *
         * @return void
         *
         * @since    1.0
         */
        public function register( $product_init, $secret_key, $product_id ) {
            $theme                                  = wp_get_theme();
            $products[$product_init]['Name']        = $theme->Name;
            $products[$product_init]['secret_key']  = $secret_key;
            $products[$product_init]['product_id']  = $product_id;
            $this->_products[$product_init]         = $products[$product_init];
        }
    }
}

/**
 * Main instance
 *
 * @return object
 * @since  1.0
 */
if( ! function_exists( 'PST_Theme_Licence' ) ){
    function PST_Theme_Licence() {
        return PST_Theme_Licence::instance();
    }
}