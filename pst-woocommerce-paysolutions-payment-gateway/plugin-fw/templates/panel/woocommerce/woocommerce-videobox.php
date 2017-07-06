<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
*  Example to call this template
*
*  'section_general_settings_videobox'         => array(
*      'name' => __( 'Title of box', 'pst' ),
*      'type' => 'videobox',
*      'default' => array(
*          'plugin_name'        => __( 'Plugin Name', 'pst' ),
*          'title_first_column' => __( 'Title first column', 'pst' ),
*          'description_first_column' => __('Lorem ipsum ... ', 'pst'),
*          'video' => array(
*              'video_id'           => 'vimeo_code',
*              'video_image_url'    => '#',
*              'video_description'  => __( 'Lorem ipsum dolor sit amet....', 'pst' ),
*          ),
*          'title_second_column' => __( 'Title first column', 'pst' ),
*          'description_second_column' => __('Lorem ipsum dolor sit amet.... ', 'pst'),
*          'button' => array(
*              'href' => 'http://www.thaiepay.com',
*              'title' => 'Get Support and Pro Features'
*          )
*      ),
*      'id'   => 'payh_wcas_general_videobox'
*  ),
*/
?>
<div id="normal-sortables" class="meta-box-sortables">
    <div id="<?php echo $id ?>" class="postbox ">
        <h3><span><?php echo $name ?></span></h3>
        <div class="inside">
            <div class="payh_videobox">
                <div class="column"><h2><?php echo $default['title_first_column'] ?></h2>
                    <?php if ( isset( $default['video'] ) && !empty( $default['video'] ) ): ?>
                        <a class="payh-video-link" href="#" data-video-id="payh-video-iframe">
                            <img src="<?php echo $default['video']['video_image_url'] ?>">
                        </a>

                        <p class="pst-video-description">
                            <?php echo $default['video']['video_description'] ?>
                        </p>

                        <p class="payh-video-iframe">
                            <iframe src="//player.vimeo.com/video/<?php echo $default['video']['video_id'] ?>?title=0&amp;byline=0&amp;portrait=0" width="853" height="480" frameborder="0"></iframe>
                        </p>
                    <?php endif ?>
                    <?php if ( isset( $default['image'] ) && !empty( $default['image'] ) ): ?>
                        <a href="<?php echo $default['image']['image_link']  ?>" target="_blank" class="payh-image-frame">
                            <img src="<?php echo $default['image']['image_url'] ?>">
                        </a>
                    <?php endif ?>
                    <?php if ( isset( $default['description_first_column'] ) && $default['description_first_column'] != '' ): ?>
                        <p><?php echo $default['description_first_column'] ?></p>
                    <?php endif ?>
                </div>
                <div class="column two">
                    <h2><?php echo $default['title_second_column'] ?>?</h2>

                    <p><?php echo $default['description_second_column'] ?></p>

                    <?php if ( isset( $default['button'] ) && !empty( $default['button'] ) ): ?>
                        <p>
                            <a class="button-primary" href="<?php echo $default['button']['href'] ?>" target="_blank"><?php echo $default['button']['title'] ?></a>
                        </p>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>