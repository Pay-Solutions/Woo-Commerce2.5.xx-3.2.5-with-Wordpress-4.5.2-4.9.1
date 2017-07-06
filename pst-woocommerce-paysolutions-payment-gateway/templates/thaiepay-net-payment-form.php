<?php

if ( ! defined( 'PAY_SOLUTIONS' ) ) {
	exit;
} // Exit if accessed directly
?>

<form id="thaiepay_net_payment_form" method='POST' action='<?php echo esc_url( $process_url ); ?>' >
	<input type="hidden" name="x_login" value="<?php echo esc_attr( $login_id )?>" />
	<input type="hidden" name="x_amount" value="<?php echo esc_attr( $amount )?>" />
	<input type="hidden" name="x_invoice_num" value="<?php echo esc_attr( $invoice )?>" />
	
	<input type="hidden" name="merchantid" value="<?php echo esc_attr( $login_id )?>" />
	<input type="hidden" name="refno" value="<?php echo esc_attr( $invoice )?>" />
	<input type="hidden" name="productdetail" value="<?php echo esc_attr( $description )?>" />
	<input type="hidden" name="total" value="<?php echo esc_attr( $amount )?>" />
	<input type="hidden" name="customeremail" value="<?php echo esc_attr( $email )?>" />

	<input type="hidden" name="x_fp_sequence" value="<?php echo esc_attr( $sequence )?>" />
	<input type="hidden" name="x_fp_hash" value="<?php echo esc_attr( $fingerprint )?>" />
	<input type="hidden" name="x_fp_timestamp" value="<?php echo esc_attr( $timestamp )?>" />
	<input type="hidden" name="x_version" value="<?php echo esc_attr( $version )?>" />
	<input type="hidden" name="x_relay_response" value="<?php echo esc_attr( $relay_response )?>" />
	<input type="hidden" name="x_type" value="<?php echo esc_attr( $type )?>" />
	<input type="hidden" name="x_description" value="<?php echo esc_attr( $description )?>" />
	<input type="hidden" name="x_show_form" value="<?php echo esc_attr( $show_form )?>" />
	<input type="hidden" name="x_currency_code" value="<?php echo esc_attr( $currency_code )?>" />
	<input type="hidden" name="x_first_name" value="<?php echo esc_attr( $first_name )?>" />
	<input type="hidden" name="x_last_name" value="<?php echo esc_attr( $last_name )?>" />
	<input type="hidden" name="x_company" value="<?php echo esc_attr( $company )?>" />
	<input type="hidden" name="x_address" value="<?php echo esc_attr( $address )?>" />
	<input type="hidden" name="x_country" value="<?php echo esc_attr( $country )?>" />
	<input type="hidden" name="x_phone" value="<?php echo esc_attr( $phone )?>" />
	<input type="hidden" name="x_state" value="<?php echo esc_attr( $state )?>" />
	<input type="hidden" name="x_city" value="<?php echo esc_attr( $city )?>" />
	<input type="hidden" name="x_zip" value="<?php echo esc_attr( $zip )?>" />
	<input type="hidden" name="x_email" value="<?php echo esc_attr( $email )?>" />
	<input type="hidden" name="x_ship_to_first_name" value="<?php echo esc_attr( $ship_to_first_name )?>" />
	<input type="hidden" name="x_ship_to_last_name" value="<?php echo esc_attr( $ship_to_last_name )?>" />
	<input type="hidden" name="x_ship_to_address" value="<?php echo esc_attr( $ship_to_address )?>" />
	<input type="hidden" name="x_ship_to_city" value="<?php echo esc_attr( $ship_to_city )?>" />
	<input type="hidden" name="x_ship_to_zip" value="<?php echo esc_attr( $ship_to_zip )?>" />
	<input type="hidden" name="x_ship_to_state" value="<?php echo esc_attr( $ship_to_state )?>" />
	<input type="hidden" name="x_cancel_url" value="<?php echo esc_url( $cancel_url )?>" />
	<input type="hidden" name="x_cancel_url_text" value="<?php echo esc_attr( $cancel_button_label )?>" />
	<input type="hidden" name="x_relay_url" value="<?php echo esc_url( $relay_url )?>" />

	<?php
	if( ! empty( $tax_info ) ):
		foreach( $tax_info as $tax ):
	?>
	<input type="hidden" name="x_tax" value="<?php echo esc_attr( $tax ) ?>" />
	<?php
		endforeach;
	endif;
	?>

	<?php
		if( ! empty( $item_info ) ):
			foreach( $item_info as $item ):
	?>
	<input type="hidden" name="x_line_item" value="<?php echo esc_attr( $item ) ?>" />
	<?php
			endforeach;
		endif;
	?>

	<input type="submit" value="<?php esc_attr_e( 'Pay on Paysolutions.com', 'pay-solutions' ) ?>" />
</form>