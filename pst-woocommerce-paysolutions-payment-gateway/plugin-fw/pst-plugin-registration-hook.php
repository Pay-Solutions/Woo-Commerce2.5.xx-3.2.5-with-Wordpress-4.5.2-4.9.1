<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if( ! function_exists( 'payh_plugin_registration_hook' ) ){
    function payh_plugin_registration_hook(){

        /**
         * @use activate_PLUGINNAME hook
         */
        $hook = str_replace( 'activate_', '', current_filter() );

        $option   = get_option( 'pst_recently_activated', array() );
        $option[] = $hook;
        update_option( 'pst_recently_activated', $option );
    }
}
