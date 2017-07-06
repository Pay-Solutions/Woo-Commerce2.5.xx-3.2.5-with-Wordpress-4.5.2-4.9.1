<?php
/**
 * Gateway class
 *
 * @author Your Inspiration Themes
 * @package Paysolutions for WooCommerce
 * @version 1.0.0
 */

if ( ! defined( 'PAY_SOLUTIONS' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'PAY_SOLUTIONS_Credit_Card_Gateway' ) ){

	class PAY_SOLUTIONS_Credit_Card_Gateway extends WC_Payment_Gateway {

		/**
		 * @const Public payment url
		 */
		const THAIEPAY_NET_PRODUCTION_PAYMENT_URL = 'https://www.thaiepay.com/epaylink/payment.aspx';

		public static $gateway_id = 'pay_solutions_credit_card_gateway';

		/**
		 * Single instance of the class
		 *
		 * @var \PAY_SOLUTIONS_Credit_Card_Gateway
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \PAY_SOLUTIONS_Credit_Card_Gateway
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
		 * @return \PAY_SOLUTIONS_Credit_Card_Gateway
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id = self::$gateway_id;
			$this->order_button_text  = apply_filters( 'pay_solutions_order_button_text', __( 'Proceed to Paysolutions.asia', 'pay-solutions' ) );
			$this->method_title       = apply_filters( 'pay_solutions_method_title', __( 'Paysolutions.asia', 'pay-solutions' ) );
			$this->method_description = apply_filters( 'pay_solutions_method_description', __( 'Pay with Paysolutions.asia', 'pay-solutions' ) );

			$this->init_form_fields();
			$this->init_settings();

			// retrieves gateway options
			$this->enabled = $this->get_option( 'enabled' );
			$this->title = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->card_types = $this->get_option( 'card_types' );
			$this->login_id = trim( $this->get_option( 'login_id' ) );

			// Logs
			if ( 'yes' == $this->debug ) {
				$this->log = new WC_Logger();
			}

			// gateway requires fields only if API methods are used
			$this->has_fields = false;

			// register payment form print
			add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'print_thaiepay_net_payment_form' ), 10, 1 );

			// register admin options
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

			// register ipn response handler
			add_action( 'woocommerce_api_' . $this->id, array( $this, 'handle_ipn_response' ) );

			// register admin notices
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		public function init_form_fields() {
			$this->form_fields = apply_filters( 'pay_solutions_credit_card_gateway_options', array(
				'enabled' => array(
					'title' => __( 'Enable/Disable', 'pay-solutions' ),
					'type' => 'checkbox',
					'label' => __( 'Enable Paysolutions.asia Payment', 'pay-solutions' ),
					'default' => 'no'
				),
				'login_id' => array(
					'title' => __( 'MerchantID', 'pay-solutions' ),
					'type' => 'text',
					'description' => __( 'This controls the merchantid which the user sees during checkout.', 'pay-solutions' )
				),
				'title' => array(
					'title' => __( 'Title', 'pay-solutions' ),
					'type' => 'text',
					'description' => __( 'This option lets you change the title that users see during the checkout.', 'pay-solutions' ),
					'default' => __( 'Paysolutions.asia Payment', 'pay-solutions' ),
					'desc_tip'      => true,
				),
				'description' => array(
					'title' => __( 'Description', 'pay-solutions' ),
					'type' => 'textarea',
					'description' => __( 'This option lets you change the description that users see during checkout.', 'pay-solutions' ),
					'default' => __( 'Accepts Payments. Anywhere', 'pay-solutions' )
				),
				'card_types' => array(
					'title'       => __( 'Acceptance logos', 'pay-solutions' ),
					'type'        => 'multiselect',
					'desc_tip'    => __( 'Select which credit card logo to display on your checkout page', 'pay-solutions' ),
					'default'     => array( 'visa', 'mastercard', 'amex', 'jcb' ),
					'class'       => 'chosen_select',
					'css'         => 'width: 370px;',
					'options'     => apply_filters( 'pay_solutions_card_types',
						array(
							'visa'   => __( 'Visa', 'pay-solutions' ),
							'mastercard' => __( 'MasterCard', 'pay-solutions' ),
							'amex' => __( 'American Express', 'pay-solutions' ),
							'jcb' => __( 'JCB', 'pay-solutions' ),
						)
					)
				)
			
			) );
		}

		/**
		 * Process payment
		 *
		 * @param $order_id int Current order id
		 *
		 * @return null|array Null on failure; array on success ( id provided: 'status' [string] textual status of the payment / 'redirect' [string] Url where to redirect user )
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );

			return $this->_process_external_payment( $order );
		}

		/**
		 * Add selected card icons to payment method label, defaults to Visa/MC/Amex/Discover
		 *
		 * @return string HTML to print in icon section
		 * @since 1.0.0
		 */
		public function get_icon() {
			$icon = '';

			if ( $this->icon ) {

				// use icon provided by filter
				$icon .= '<img src="' . esc_url( WC_HTTPS::force_https_url( $this->icon ) ) . '" alt="' . esc_attr( $this->title ) . '" />';

			}

			if ( ! empty( $this->card_types ) ) {

				// display icons for the selected card types
				foreach ( $this->card_types as $card_type ) {

					if ( file_exists( PAY_SOLUTIONS_DIR . 'assets/images/icons/credit-cards/' . strtolower( $card_type ) . '.png' ) ) {
						$icon .= '<img src="' . esc_url( WC_HTTPS::force_https_url( PAY_SOLUTIONS_URL ) . '/assets/images/icons/credit-cards/' . strtolower( $card_type ) . '.png' ) . '" alt="' . esc_attr( strtolower( $card_type ) ) . '" />';
					}

				}

			}

			return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
		}

		/**
		 * Advise if the plugin cannot be performed
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_notices() {
			if ( empty( $this->login_id ) ) {
				echo '<div class="error"><p>' . __( 'Please enter MerchantID for Paysolutions.asia gateway.', 'pay-solutions' ) . '</p></div>';
			}
		}

		/**
		 * Add banner on payment gateway page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_options() {
			?>
			<h3><?php echo ( ! empty( $this->method_title ) ) ? $this->method_title : __( 'Settings', 'woocommerce' ) ; ?></h3>

			<?php if( empty( $this->login_id ) ): ?>
				<div class="simplify-commerce-banner updated">
					<img src="<?php echo PAY_SOLUTIONS_URL . '/assets/images/logo.jpg'; ?>" style="width: 300px" />
					<p class="main"><strong><?php _e( 'Getting started', 'pay-solutions' ); ?></strong></p>
					<p><?php _e( 'An Paysolutions.asia Payment Gateway account allows you to accept credit cards and electronic checks from websites and Internet auction sites. Our solutions are designed to save time and money for small- to medium-sized businesses.', 'pay-solutions' ); ?></p>
				</div>
			<?php endif; ?>

			<?php echo ( ! empty( $this->method_description ) ) ? wpautop( $this->method_description ) : ''; ?>

			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table><?php
		}

		/* === DIRECT PAYMENT METHODS === */
		public function print_thaiepay_net_payment_form( $order_id ){
			$order = wc_get_order( $order_id );
			$order_number = $order->get_order_number();
			$order_total = $order->get_total();
			$order_currency = $order->get_order_currency();

			// Define variables to use in the template
			$login_id = $this->login_id;
			$amount = $order_total;
			$invoice = $order_id;
			$sequence = $order_id;
			$version = '1.1';
			$relay_response = 'TRUE';
			$type = 'AUTH_CAPTURE';
			$description = 'Order ' . $order_number;
			$show_form = 'PAYMENT_FORM';
			$currency_code = $order_currency;
			$first_name = $order->billing_first_name;
			$last_name = $order->billing_last_name;
			$company = $order->billing_company;
			$address = $order->billing_address_1 . ' ' . $order->billing_address_2;
			$country = $order->billing_country;
			$phone = $order->billing_phone;
			$state = $order->billing_state;
			$city = $order->billing_city;
			$zip = $order->billing_postcode;
			$email = $order->billing_email;
			$ship_to_first_name = $order->shipping_first_name;
			$ship_to_last_name = $order->shipping_last_name;
			$ship_to_address = $order->shipping_address_1;
			$ship_to_city = $order->shipping_city;
			$ship_to_zip = $order->shipping_postcode;
			$ship_to_state = $order->shipping_state;
			$cancel_url = WC()->cart->get_checkout_url();
			$cancel_button_label = apply_filters( 'pay_solutions_cancel_button_label', __( 'Cancel Payment', 'pay-solutions' ) );
			$relay_url = add_query_arg( 'wc-api', $this->id, home_url() );

			// Itemized request information
			$tax_info = array();
			$item_info = array();

			$process_url = self::THAIEPAY_NET_PRODUCTION_PAYMENT_URL;
			
			// Security params
			$timestamp = time();

			if( phpversion() >= '5.1.2' ) {
				$fingerprint = hash_hmac( "md5", $this->login_id . "^" . $order_id . "^" . $timestamp . "^" . $order_total . "^" . $order_currency , $this->transaction_key );
			}
			else {
				$fingerprint = bin2hex( mhash( MHASH_MD5, $this->login_id . "^" . $order_id . "^" . $timestamp . "^" . $order_total . "^" . $order_currency , $this->transaction_key ) );
			}

			// Include payment form template
			$template_name = 'thaiepay-net-payment-form.php';
			$locations = array(
				trailingslashit( WC()->template_path() ) . $template_name,
				$template_name
			);

			$template = locate_template( $locations );

			if( ! $template ){
				$template = PAY_SOLUTIONS_DIR . 'templates/' . $template_name;
			}

			include_once( $template );
		}

		protected function _process_external_payment( $order ){
			// Redirect to payment page, where payment form will be printed
			return array(
				'result' => 'success',
				'redirect' => $order->get_checkout_payment_url( true )
			);
		}

		/* === IPN RESPONSE HANDLER === */
			
			public function handle_ipn_response(){
			$order_id = isset( $_POST['x_invoice_num'] ) ? $_POST['x_invoice_num'] : false;
			$response = isset( $_POST['x_response_code'] ) ? $_POST['x_response_code'] : false;
			//$md5_hash = isset( $_POST['x_MD5_Hash'] ) ? $_POST['x_MD5_Hash'] : false;
			$trans_id  = isset( $_POST['x_trans_id'] ) ? $_POST['x_trans_id'] : false;
			$amount = isset( $_POST['x_amount'] ) ? $_POST['x_amount'] : false;
			$email = isset( $_POST['x_email'] ) ? $_POST['x_email'] : false;
			$trans_message = ! empty( $_POST['x_response_reason_text'] ) ? $_POST['x_response_reason_text'] : __( 'N/D', 'pay-solutions' );
			$trans_account_number = ! empty( $_POST['x_account_number'] ) ? $_POST['x_account_number'] : '';

			if( isset( $order_id ) ){
				$order = wc_get_order( $order_id );
			}

			if( ! $order_id || ! $response  || ! $trans_id || ! $amount || ! $email ){
				// Redirect to error page and set order as failed

				if( ! empty( $order ) ){
					$order->update_status( 'failed', __( 'Paysolutions.asia API error: unknown error.', 'pay-solutions' ) );
					wc_add_notice( __( 'Unknown error', 'pay-solutions' ), 'error' );
					$this->redirect_via_html( $order->get_checkout_order_received_url() );
					die();
				}
				else{
					$this->redirect_via_html( WC()->cart->get_checkout_url() );
					die();
				}
			}

			if( $response == 1 ){
				$valid_response = true;

				// Validate amount
				if ( $order->get_total() != $amount ) {
					if ( 'yes' == $this->debug ) {
						$this->log->add( 'Paysolutions.asia', 'Payment error: Amounts do not match (gross ' . $amount . ')' );
					}

					// Put this order on-hold for manual checking
					$order->update_status( 'on-hold', sprintf( __( 'Validation error: Authorize.net amounts do not match with (%s).', 'pay-solutions' ), $amount ) );

					wc_add_notice( sprintf( __( 'Validation error: Paysolutions.asia amounts do not match with (%s).', 'pay-solutions' ), $amount ), 'error' );
					$valid_response = false;
				}

				// Validate Email Address
				if ( strcasecmp( trim( $order->billing_email ), trim( $email ) ) != 0 ) {
					if ( 'yes' == $this->debug ) {
						$this->log->add( 'Paysolutions.asia', "Payment error: Paysolutions.asia email ({$email}) does not match our email ({$order->billing_email})" );
					}

					// Put this order on-hold for manual checking
					$order->update_status( 'on-hold', sprintf( __( 'Validation error: Paysolutions.asia responses from a different email address than (%s).', 'pay-solutions' ), $email ) );

					wc_add_notice( sprintf( __( 'Validation error: Paysolutions.asia responses from a different email address than (%s).', 'pay-solutions' ), $email ), 'error' );
					$valid_response = false;
				}


				if( $valid_response ) {
					// Mark as complete
					$order->add_order_note( sprintf( __( 'Paysolutions.asia payment completed (message: %s). Transaction ID: %s', 'pay-solutions' ), $trans_message, $trans_id ) );
					$order->payment_complete( $trans_id );

					if( ! empty( $trans_account_number ) ){
						update_post_meta( $order->id, 'x_card_num', $trans_account_number );
					}

					if ( 'yes' == $this->debug ) {
						$this->log->add( 'Paysolutions.asia', 'Payment Result: ' . print_r( $_POST, true ) );
					}

					// Remove cart
					WC()->cart->empty_cart();
				}
			}
			else{
				wc_add_notice( sprintf( __( 'Payment error: %s', 'pay-solutions' ), $trans_message ), 'error' );
			}

		    $this->redirect_via_html( $order->get_checkout_order_received_url() );
			die();
		}

		/**
		 * Print HTML code to redirect to a specific url
		 *
		 * @param $url string Url to redirect to
		 *
		 * @return void
		 */
		public function redirect_via_html( $url ) {
			?>
			<html>
			<head>
				<script language="javascript">
                <!--
                window.location="<?php echo $url ?>";
                //-->
                </script>
			</head>
			<body>
			<noscript>
				<meta http-equiv="refresh" content="0;url=<?php echo $url ?>">
			</noscript>
			</body>
			</html>
			<?php
		}
	}
}

/**
 * Unique access to instance of PAY_SOLUTIONS_Credit_Card_Gateway class
 *
 * @return \PAY_SOLUTIONS_Credit_Card_Gateway
 * @since 1.0.0
 */
function PAY_SOLUTIONS_Credit_Card_Gateway(){
	return PAY_SOLUTIONS_Credit_Card_Gateway::get_instance();
}