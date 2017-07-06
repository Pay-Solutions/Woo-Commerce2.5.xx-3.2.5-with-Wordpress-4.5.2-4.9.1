<?php
/**
 * Admin class
 *
 * @package Paysolutions for WooCommerce
 * @version 1.0.0
 */

if ( ! defined( 'PAY_SOLUTIONS' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'PAY_SOLUTIONS_Admin' ) ) {
	
	class PAY_SOLUTIONS_Admin{

		
		protected static $instance;

	//	protected $_premium_landing = 'www.thaiepay.com';

		protected $_official_documentation = 'http://paysolutions.asia/';

		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self;
			}

			return self::$instance;
		}

		
		public function __construct() {
			$this->admin_tabs = array(
				'credit_card' => __( 'Credit Card', 'pay-solutions' )
			);

			// register gateway panel
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// register panel
			add_action( 'pay_solutions_payment_credit_card_gateway_settings_tab', array( $this, 'print_credit_card_panel' ) );

			// register pointer
			add_action( 'admin_init', array( $this, 'register_pointer' ) );

			//Add action links
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			add_filter( 'plugin_action_links_' . plugin_basename( PAY_SOLUTIONS_DIR . '/' . basename( PAY_SOLUTIONS_FILE ) ), array( $this, 'action_links' ) );

            //  Show plugin premium tab
       //     add_action( 'pay_thaiepay_premium', array( $this, 'premium_tab' ) );
		}

		public function get_premium_landing_uri(){
			return defined( 'PAY_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . PAY_REFER_ID : $this->_premium_landing;
		}

		public function register_panel() {
			$args = array(
				'create_menu_page' => true,
				'parent_slug'   => '',
				'page_title'    => __( 'Paysolutions.asia', 'pay-solutions' ),
				'menu_title'    => __( 'Paysolutions.asia', 'pay-solutions' ),
				'capability'    => 'manage_options',
				'parent'        => '',
				'parent_page'   => 'pst_plugin_panel',
				'page'          => 'pay_solutions_panel',
				'admin-tabs'    => apply_filters( 'pay_solutions_available_tabs', $this->admin_tabs ),
				'options-path'  => PAY_SOLUTIONS_DIR . 'plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if( ! class_exists( 'PST_Plugin_Panel_WooCommerce' ) ) {
				require_once( PAY_SOLUTIONS_DIR . 'plugin-fw/lib/pst-plugin-panel-wc.php' );
			}

			$this->_panel = new PST_Plugin_Panel_WooCommerce( $args );
		}

		public function print_credit_card_panel() {
			if( file_exists( PAY_SOLUTIONS_DIR . '/templates/admin/settings-tab.php' ) ){

				global $current_section;
				$current_section = 'pay_solutions_credit_card_gateway';

				WC_Admin_Settings::get_settings_pages();

				if( ! empty( $_POST ) ) {
					PAY_SOLUTIONS_Credit_Card_Gateway()->process_admin_options();
				}

				include_once( PAY_SOLUTIONS_DIR . '/templates/admin/settings-tab.php' );
			}
		}

		public function action_links( $links ) {

			$links[] = '<a href="' . admin_url( "admin.php?page=pay_solutions_panel" ) . '">' . __( 'Settings', 'pay-solutions' ) . '</a>';

			/*if ( ! ( defined( 'PAY_SOLUTIONS_PREMIUM' ) && PAY_SOLUTIONS_PREMIUM ) ) {
				$links[] = '<a href="' . $this->get_premium_landing_uri() . '" target="_blank">' . __( 'Premium Version', 'pay-solutions' ) . '</a>';
			}
*/
			return $links;
		}

		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			$plugin_meta[] = '<a href="' . $this->_official_documentation . '" target="_blank">' . __( 'Plugin Documentation', 'pay-solutions' ) . '</a>';

			return $plugin_meta;
		}

		/**
		 * Register the pointer for the settings page
		 *
		 * @since 1.0.0
		 */
		public function register_pointer() {

			if( ! class_exists( 'PST_Pointers' ) ){
				include_once( '../plugin-fw/lib/pst-pointers.php' );
			}

			$args[] = array(
				'screen_id'     => 'plugins',
				'pointer_id' => 'pay_solutions_panel',
				'target'     => '#toplevel_page_pst_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'PAY Paysolutions.com', 'pst' ),
					apply_filters( 'pay_solutions_activated_pointer_content', sprintf( __( 'In the PAYSOLUTIONS Plugins tab you can find the PAYSOLUTIONS WooCommerce options. From this menu, you can access all the settings of the Paysolutions plugins activated. Wishlist is available in an outstanding PREMIUM version with many new options, <a href="%s">discover it now</a>.', 'pay-solutions' ), $this->get_premium_landing_uri() ) )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'  => PAY_SOLUTIONS_INIT
			);

			PST_Pointers()->register( $args );
		}

        public function premium_tab() {
            $premium_tab_template = PAY_SOLUTIONS_DIR . 'templates/admin/premium.php';
            if ( file_exists( $premium_tab_template ) ) {
                include_once( $premium_tab_template );
            }
        }
	}
}

/**
 * Unique access to instance of PAY_SOLUTIONS_Admin class
 *
 * @return \PAY_SOLUTIONS_Admin
 * @since 1.0.0
 */
function PAY_SOLUTIONS_Admin(){
	return PAY_SOLUTIONS_Admin::get_instance();
}