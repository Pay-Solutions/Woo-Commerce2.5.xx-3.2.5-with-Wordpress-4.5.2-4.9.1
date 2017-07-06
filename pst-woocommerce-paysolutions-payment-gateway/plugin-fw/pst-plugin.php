<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


if( !defined('PST_CORE_PLUGIN')) {
    define( 'PST_CORE_PLUGIN', true);
}

if( !defined('PST_CORE_PLUGIN_PATH')) {
    define( 'PST_CORE_PLUGIN_PATH', dirname(__FILE__));
}

if( !defined('PST_CORE_PLUGIN_URL')) {
    define( 'PST_CORE_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ));
}

if( ! defined( 'PST_CORE_PLUGIN_TEMPLATE_PATH' ) ){
    define ( 'PST_CORE_PLUGIN_TEMPLATE_PATH', PST_CORE_PLUGIN_PATH .  '/templates' );
}


include_once( 'pst-functions.php' );
include_once( 'pst-plugin-registration-hook.php' );
include_once( 'lib/pst-metabox.php' );
include_once( 'lib/pst-plugin-panel.php' );
include_once( 'lib/pst-plugin-panel-wc.php' );
include_once( 'lib/pst-plugin-subpanel.php' );
include_once( 'lib/pst-plugin-common.php' );
include_once( 'lib/pst-plugin-gradients.php');
include_once( 'licence/lib/pst-licence.php');
include_once( 'licence/lib/pst-plugin-licence.php');
include_once( 'licence/lib/pst-theme-licence.php');
include_once( 'lib/pst-upgrade.php');
include_once( 'lib/pst-pointers.php');
