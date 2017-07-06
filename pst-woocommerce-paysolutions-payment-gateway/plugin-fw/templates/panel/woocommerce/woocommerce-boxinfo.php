<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 *        'section_general_settings_boxinfo'         => array(
 *            'name' => __( 'General information', 'pst' ),
 *            'type' => 'boxinfo',
 *            'default' => array(
 *                'plugin_name' => __( 'Plugin Name', 'pst' ),
 *                'buy_url' => 'http://www.thaiepay.com',
 *                'demo_url' => 'http://plugins.thaiepay.com'
 *            ),
 *            'id'   => 'payh_wcas_general_boxinfo'
 *        ),
 */
?>
<div id="<?php echo $id ?>" class="meta-box-sortables">
    <div id="<?php echo $id ?>-content-panel" class="postbox " style="display: block;">
        <h3><?php echo $name ?></h3>
        <div class="inside">
            <p>Lorem ipsum ... </p>
            <p class="submit"><a href="<?php echo $default['buy_url'] ?>" class="button-primary">Buy Plugin</a></p>
        </div>
    </div>
</div>