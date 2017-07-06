<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

global $post;

do_action( 'pst_before_metaboxes_tab' ) ?>
<div class="metaboxes-tab">
    <?php do_action( 'pst_before_metaboxes_labels' ) ?>
    <ul class="metaboxes-tabs clearfix"<?php if ( count( $tabs ) <= 1 ) : ?> style="display:none;"<?php endif; ?>>
        <?php
        $i = 0;
        foreach ( $tabs as $tab ) :
            if ( ! isset( $tab['fields'] ) || empty( $tab['fields'] ) ) {
                continue;
            }
            ?>
            <li<?php if ( ! $i ) : ?> class="tabs"<?php endif ?>>
            <a href="#<?php echo urldecode( sanitize_title( $tab['label'] ) ) ?>"><?php echo $tab['label'] ?></a></li><?php
            $i ++;
        endforeach;
        ?>
    </ul>
    <?php do_action( 'pst_after_metaboxes_labels' ) ?>
    <?php if( isset(  $tab['label'] ) ) : ?>
        <?php do_action( 'pst_before_metabox_option_' . urldecode( sanitize_title( $tab['label'] ) ) ); ?>
    <?php endif ?>

    <?php
    // Use nonce for verification
    wp_nonce_field( 'metaboxes-fields-nonce', 'pst_metaboxes_nonce' );
    ?>
    <?php foreach ( $tabs as $tab ) :

        ?>
        <div class="tabs-panel" id="<?php echo urldecode( sanitize_title( $tab['label'] ) ) ?>">
            <?php
            if ( ! isset( $tab['fields'] ) ) {
                continue;
            }

            $tab['fields'] = apply_filters( 'pst_metabox_' . sanitize_title( $tab['label'] ) . '_tab_fields', $tab['fields'] );

            foreach ( $tab['fields'] as $id_tab=>$field ) :
                $value           = pst_get_post_meta( $post->ID, $field['id'] );
                $field['value'] = $value != '' ? $value : ( isset( $field['std'] ) ? $field['std'] : '' );
                ?>
                <div class="the-metabox <?php echo $field['type'] ?> clearfix<?php if ( empty( $field['label'] ) ) : ?> no-label<?php endif; ?>">
                    <?php pst_plugin_get_template( PST_CORE_PLUGIN_PATH, '/metaboxes/types/' . $field['type'] . '.php', array( 'args' => $field ) ) ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>
</div>