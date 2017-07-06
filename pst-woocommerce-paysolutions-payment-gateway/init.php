<?php
/**
 * Plugin Name: PAYSOLUTIONS WooCommerce Payment Gateway
 * Plugin URI: http://thaiepay.com/download.aspx
 * Description: PAYSOLUTIONS WooCommerce allows you to add Authorize.net payment gateway to WooCommerce
 * Version: 1.0.1
 * Author: Thaiepay
 * Author URI: http://thaiepay.com/
 * Text Domain: pay-solutions
 * Domain Path: /languages/
 *
 * @package PAYSOLUTIONS WooCommerce
 * @version 1.0.0
 */


if( ! defined( 'ABSPATH' ) ){
	exit;
}

// Register WP_Pointer Handling
if ( ! function_exists( 'payh_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/pst-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'payh_plugin_registration_hook' );

if ( ! defined( 'PAY_SOLUTIONS' ) ) {
	define( 'PAY_SOLUTIONS', true );
}

if ( ! defined( 'PAY_SOLUTIONS_URL' ) ) {
	define( 'PAY_SOLUTIONS_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PAY_SOLUTIONS_DIR' ) ) {
	define( 'PAY_SOLUTIONS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'PAY_SOLUTIONS_INIT' ) ) {
	define( 'PAY_SOLUTIONS_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'PAY_SOLUTIONS_FREE_INIT' ) ) {
	define( 'PAY_SOLUTIONS_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'PAY_SOLUTIONS_FILE' ) ) {
	define( 'PAY_SOLUTIONS_FILE', __FILE__ );
}

if ( ! defined( 'PAY_SOLUTIONS_INC' ) ) {
	define( 'PAY_SOLUTIONS_INC', PAY_SOLUTIONS_DIR . 'includes/' );
}

if( ! function_exists( 'pay_solutions_constructor' ) ) {
	function pay_solutions_constructor(){
		load_plugin_textdomain( 'pst', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Load required classes and functions
		require_once( PAY_SOLUTIONS_INC . 'class.pay-solutions-credit-card-gateway.php' );
		require_once( PAY_SOLUTIONS_INC . 'class.pay-solutions.php' );

		if( is_admin() ){
			require_once( PAY_SOLUTIONS_INC . 'class.pay-solutions-admin.php' );

			PAY_SOLUTIONS_Admin();
		}
	}
}
add_action( 'pay_solutions_init', 'pay_solutions_constructor' );

if( ! function_exists( 'pay_solutions_install' ) ) {
	function pay_solutions_install() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'pay_solutions_install_woocommerce_admin_notice' );
		}
		else {
			do_action( 'pay_solutions_init' );
		}
	}
}
add_action( 'plugins_loaded', 'pay_solutions_install', 11 );

if( ! function_exists( 'pay_solutions_install_woocommerce_admin_notice' ) ) {
	function pay_solutions_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'PAYSOLUTIONS WooCommerce Payment Gateway is enabled but not effective. It requires Woocommerce in order to work.', 'pay-solutions' ); ?></p>
		</div>
	<?php
	}
}