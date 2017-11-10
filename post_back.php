<?php

include( 'wp-load.php' );

global $woocommerce;
$order = new WC_Order($_REQUEST["refno"]);
$order->payment_complete();
