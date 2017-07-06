<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

extract( $args );

$layout = ! isset( $value['layout'] ) ? 'sidebar-right' : $value['layout'];
$sidebar = ! isset( $value['sidebar'] ) ? '' : $value['sidebar'];
?>
<div id="<?php echo $id ?>-container" <?php if ( isset( $deps ) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>
    <label for="<?php echo $id ?>"><?php echo $label ?></label>

    <p class="pst-sidebar-layout">
        <input type="radio" name="<?php echo $name ?>[layout]" id="<?php echo $id . '-left' ?>" value="sidebar-left" <?php checked( $layout, 'sidebar-left' ) ?> />
        <img src="<?php echo PST_CORE_ASSETS_URL ?>/images/sideleft.png" title="<?php _e( 'Left sidebar', 'pst' ) ?>" alt="<?php _e( 'Left sidebar', 'pst' ) ?>" />

        <input type="radio" name="<?php echo $name ?>[layout]" id="<?php echo $id . '-no' ?>" value="sidebar-no" <?php checked( $layout, 'sidebar-no' ) ?> />
        <img src="<?php echo PST_CORE_ASSETS_URL ?>/images/noside.png" title="<?php _e( 'No sidebar', 'pst' ) ?>" alt="<?php _e( 'No sidebar', 'pst' ) ?>" />

        <input type="radio" name="<?php echo $name ?>[layout]" id="<?php echo $id . '-right' ?>" value="sidebar-right" <?php checked( $layout, 'sidebar-right' ) ?> />
        <img src="<?php echo PST_CORE_ASSETS_URL ?>/images/sideright.png" title="<?php _e( 'Right sidebar', 'pst' ) ?>" alt="<?php _e( 'Right sidebar', 'pst' ) ?>" />

        <select name="<?php echo $name ?>[sidebar]" id="<?php echo $id ?>-sidebar">
            <option value="-1"><?php _e( 'Choose a sidebar', 'pst' ) ?></option>
            <?php foreach ( pst_registered_sidebars() as $val => $option ) { ?>
                <option value="<?php echo esc_attr( $val ) ?>" <?php selected( $sidebar, $val ) ?>><?php echo $option; ?></option>
            <?php } ?>
        </select>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.pst-sidebar-layout img').click(function () {
                    $(this).parent().children(':radio').attr('checked', false);
                    $(this).prev(':radio').attr('checked', true);
                });

                if ($('#<?php echo $id . '-no' ?>').attr('checked')) {
                    $('#<?php echo $id ?>-sidebar').hide();
                }

                $('.pst-sidebar-layout :radio').next('img').click(function () {

                    if ($(this).prev(':radio').val() == 'sidebar-no') {
                        $('#<?php echo $id ?>-sidebar').fadeOut();
                    } else {
                        $('#<?php echo $id ?>-sidebar').fadeIn();
                    }
                });
            });
        </script>
    </p>
</div>