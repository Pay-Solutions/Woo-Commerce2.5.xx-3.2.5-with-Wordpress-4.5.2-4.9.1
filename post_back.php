<?php

include( 'wp-load.php' );

global $woocommerce;
$order = new WC_Order(15);
$order->payment_complete();
