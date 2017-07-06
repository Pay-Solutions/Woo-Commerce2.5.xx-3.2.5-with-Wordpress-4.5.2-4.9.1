<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly



return array(
    'premium' => array(
        'home' => array(
            'type'   => 'custom_tab',
            'action' => 'pay_thaiepay_premium'
        )
    )
);