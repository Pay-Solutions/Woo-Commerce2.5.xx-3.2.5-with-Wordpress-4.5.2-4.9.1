<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'PST_Plugin_SubPanel' ) ) {
    /**
     * PST Plugin Panel
     *
     * Setting Page to Manage Plugins
     *
     * @class PST_Plugin_Panel
     * @package    Thaiepay
     * @since      1.0
     */

    class PST_Plugin_SubPanel extends PST_Plugin_Panel {

        /**
         * @var string version of class
         */
        public $version = '1.0.0';

        /**
         * @var array a setting list of parameters
         */
        public $settings = array();


        /**
         * @var array
         */
        private $_main_array_options = array();

        /**
         * Constructor
         *
         * @since  1.0
         * @author Wilawan Onnom' <wilawan@efrainc.com>
         */

        public function __construct( $args = array() ) {
            if ( ! empty( $args ) ) {
                $this->settings = $args;
                $this->settings['parent'] = $this->settings['page'];
                $this->_tabs_path_files = $this->get_tabs_path_files();

                add_action( 'admin_init', array( $this, 'register_settings' ) );
                add_action( 'admin_menu', array( &$this, 'add_setting_page' ) );
                add_action( 'admin_bar_menu', array( &$this, 'add_admin_bar_menu' ), 100 );
                add_action( 'admin_init', array( &$this, 'add_fields' ) );
                add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            }
        }


        /**
         * Register Settings
         *
         * Generate wp-admin settings pages by registering your settings and using a few callbacks to control the output
         *
         * @return void
         * @since    1.0
         */
        public function register_settings() {
            register_setting( 'pst_' . $this->settings['page'] . '_options', 'pst_' . $this->settings['page'] . '_options', array( &$this, 'options_validate' ) );
        }



        /**
         * Add Setting SubPage
         *
         * add Setting SubPage to wordpress administrator
         *
         * @return array validate input fields
         * @since    1.0
         */
          public function add_setting_page() {

                $logo = PST_CORE_PLUGIN_URL . '/assets/images/yithemes-icon.png';

                $admin_logo = function_exists( 'pst_get_option' ) ? pst_get_option( 'admin-logo-menu' ) : '';

                if ( isset( $admin_logo ) && ! empty( $admin_logo ) && $admin_logo != '' && $admin_logo) {
                    $logo = $admin_logo;
                }

                add_menu_page( 'pst_plugin_panel', __( 'PST Plugins', 'pst' ), 'nosuchcapability', 'pst_plugin_panel', NULL, $logo, 62 );
                add_submenu_page( 'pst_plugin_panel', $this->settings['label'], $this->settings['label'], 'manage_options', $this->settings['page'], array( $this, 'pst_panel' ) );
                remove_submenu_page( 'pst_plugin_panel', 'pst_plugin_panel' );

          }

        /**
         * Show a tabbed panel to setting page
         *
         * a callback function called by add_setting_page => add_submenu_page
         *
         * @return void
         * @since    1.0
         */
        public function pst_panel() {

            $tabs        = '';
            $current_tab = $this->get_current_tab();

            // tabs
            foreach ( $this->settings['admin-tabs'] as $tab => $tab_value ) {
                $active_class = ( $current_tab == $tab ) ? ' nav-tab-active' : '';
                $tabs .= '<a class="nav-tab' . $active_class . '" href="?page=' . $this->settings['page'] . '&tab=' . $tab . '">' . $tab_value . '</a>';
            }
            ?>
            <div id="icon-themes" class="icon32"><br /></div>
            <h2 class="nav-tab-wrapper">
                <?php echo $tabs ?>
            </h2>

            <div id="wrap" class="plugin-option">
                <?php $this->message(); ?>
                <h2><?php echo $this->get_tab_title() ?></h2>

                <?php if ( $this->is_show_form() ) : ?>
                    <form method="post" action="options.php">
                        <?php do_settings_sections( 'pst' ); ?>
                        <p>&nbsp;</p>
                        <?php settings_fields( 'pst_' . $this->settings['page'] . '_options' ); ?>
                        <input type="hidden" name="<?php echo $this->get_name_field( 'current_tab' ) ?>" value="<?php echo esc_attr( $current_tab ) ?>" />
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'pst' ) ?>" style="float:left;margin-right:10px;" />
                    </form>
                    <form method="post">
                        <?php $warning = __( 'If you continue with this action, you will reset all the options in this page.', 'pst' ) ?>
                        <input type="hidden" name="pst-action" value="reset" />
                        <input type="submit" name="pst-reset" class="button-secondary" value="<?php _e( 'Reset Defaults', 'pst' ) ?>" onclick="return confirm('<?php echo $warning . '\n' . __( 'Are you sure?', 'pst' ) ?>');" />
                    </form>
                    <p>&nbsp;</p>
                <?php endif ?>
            </div>
        <?php
        }



    }

}

