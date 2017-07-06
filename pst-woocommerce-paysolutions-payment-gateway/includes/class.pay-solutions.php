<?php

if ( ! defined( 'PAY_SOLUTIONS' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'PAY_SOLUTIONS' ) ){

	class PAY_SOLUTIONS {
		/**
		 * Single instance of the class
		 *
		 * @var \PAY_SOLUTIONS
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \PAY_SOLUTIONS
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @param array $details
		 * @return \PAY_SOLUTIONS
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );

			// enqueue assets
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			// add filter to append wallet as payment gateway
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_to_gateways' ) );

			if( defined( 'PAY_SOLUTIONS_PREMIUM' ) && PAY_SOLUTIONS_PREMIUM ){
				PAY_SOLUTIONS_Premium();
			}
		}
		
		/**
		 * Enqueue scripts
		 *
		 * @return void
		 */
		public function enqueue() {
			$path = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'unminified/' : '';
			$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

			if( is_checkout() ){
				wp_enqueue_script( 'pay-solutions-form-handler', PAY_SOLUTIONS_URL . 'assets/js/' . $path . 'thaiepay-net' . $suffix . '.js', array( 'jquery' ), false, true );
			}
		}

		/**
		 * Adds Paysolutions.com Gateway to payment gateways available for woocommerce checkout
		 *
		 * @param $methods array Previously available gataways, to filter with the function
		 *
		 * @return array New list of available gateways
		 * @since 1.0.0
		 * @author Programmer<wilawan@efrainc.com>
		 */
		public function add_to_gateways( $methods ) {
			if( defined( 'PAY_SOLUTIONS_PREMIUM' ) && PAY_SOLUTIONS_PREMIUM ){
				$methods[] = 'PAY_SOLUTIONS_Credit_Card_Gateway_Premium';
				$methods[] = 'PAY_SOLUTIONS_eCheck_Gateway';
			}
			else{
				$methods[] = 'PAY_SOLUTIONS_Credit_Card_Gateway';
			}
			return $methods;
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'PST' ) || ! defined( 'PST_CORE_PLUGIN' ) ) {
				require_once( PAY_SOLUTIONS_DIR . '/plugin-fw/pst-plugin.php' );
			}
		}
	}
}

function PAY_SOLUTIONS(){
	return PAY_SOLUTIONS::get_instance();
}

// Let's start the game!
// Create unique instance of the class
PAY_SOLUTIONS();