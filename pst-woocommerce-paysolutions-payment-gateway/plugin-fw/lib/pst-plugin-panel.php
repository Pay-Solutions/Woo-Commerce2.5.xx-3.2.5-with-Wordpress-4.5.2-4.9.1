<?php
/**
 * This file belongs to the PAYSOLUTIONS Plugin Framework.
 *

 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'PST_Plugin_Panel' ) ) {
    /**
     * PST Plugin Panel
     *
     * Setting Page to Manage Plugins
     *
     * @class PST_Plugin_Panel
     * @package    Thaiepay
     * @since      1.0
     */

    class PST_Plugin_Panel {

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
        protected $_tabs_path_files;

        /**
         * @var array
         */
        private $_main_array_options = array();

        /**
         * Constructor
         *
         * @since  1.0
         */
        public function __construct( $args = array() ) {

            if ( ! empty( $args ) ) {

                $default_args = array(
                    'parent_slug' => 'edit.php?',
                    'page_title'  => __( 'Plugin Settings', 'PST' ),
                    'menu_title'  => __( 'Settings', 'PST' ),
                    'capability'  => 'manage_options'
                );

                $this->settings         = wp_parse_args( $args, $default_args );
                $this->_tabs_path_files = $this->get_tabs_path_files();

                if ( isset( $this->settings['create_menu_page'] ) && $this->settings['create_menu_page'] ) {
                    $this->add_menu_page();
                }

                add_action( 'admin_init', array( &$this, 'register_settings' ) );
                add_action( 'admin_menu', array( &$this, 'add_setting_page' ) );
                add_action( 'admin_bar_menu', array( &$this, 'add_admin_bar_menu' ), 100 );
                add_action( 'admin_init', array( &$this, 'add_fields' ) );


            }

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        }

        /**
         * Add Menu page link
         *
         * @return void
         * @since    1.0
         */
        public function add_menu_page() {
            add_menu_page( 'pst_plugin_panel', __( 'PST Plugins', 'pst' ), 'manage_options', 'pst_plugin_panel', NULL, PST_CORE_PLUGIN_URL . '/assets/images/paysolutions-icon.png', 62 );
        }

        /**
         * Remove duplicate submenu
         *
         * Submenu page hack: Remove the duplicate PST Plugin link on subpages
         *
         * @return void
         * @since    1.0
         */
        public function remove_duplicate_submenu_page() { 
            /* === Duplicate Items Hack === */
            remove_submenu_page( 'pst_plugin_panel', 'pst_plugin_panel' );
        }

        /**
         * Enqueue script and styles in admin side
         *
         * Add style and scripts to administrator
         *
         * @return void
         * @since    1.0
         */
        public function admin_enqueue_scripts() {
            //scripts
            wp_enqueue_media();
            wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-slider' );
            wp_enqueue_style( 'jquery-chosen', PST_CORE_PLUGIN_URL . '/assets/css/chosen/chosen.css' );
            wp_enqueue_script( 'jquery-chosen', PST_CORE_PLUGIN_URL . '/assets/js/chosen/chosen.jquery.js', array( 'jquery' ), '1.1.0', true );
            wp_enqueue_script( 'pst-plugin-panel', PST_CORE_PLUGIN_URL . '/assets/js/pst-plugin-panel.min.js', array( 'jquery', 'jquery-chosen' ), $this->version, true );
            wp_register_script( 'codemirror', PST_CORE_PLUGIN_URL . '/assets/js/codemirror/codemirror.js', array( 'jquery' ), $this->version, true );
            wp_register_script( 'codemirror-javascript', PST_CORE_PLUGIN_URL . '/assets/js/codemirror/javascript.js', array( 'jquery', 'codemirror' ), $this->version, true );

            
            wp_register_style( 'codemirror', PST_CORE_PLUGIN_URL . '/assets/css/codemirror/codemirror.css' );

            //styles
            wp_enqueue_style( 'jquery-ui-overcast', PST_CORE_PLUGIN_URL . '/assets/css/overcast/jquery-ui-1.8.9.custom.css', false, '1.8.9', 'all' );
            wp_enqueue_style( 'pst-plugin-style', PST_CORE_PLUGIN_URL . '/assets/css/pst-plugin-panel.css', $this->version );
            wp_enqueue_style( 'raleway-font', '//fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,100,200,300,900' );
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
            register_setting( 'pst_' . $this->settings['parent'] . '_options', 'pst_' . $this->settings['parent'] . '_options', array( &$this, 'options_validate' ) );
        }

        /**
         * Options Validate
         *
         * a callback function called by Register Settings function
         *
         * @param $input
         *
         * @return array validate input fields
         * @since    1.0
         */
        public function options_validate( $input ) {

            $current_tab = ! empty( $input['current_tab'] ) ? $input['current_tab'] : 'general';

            $pst_options = $this->get_main_array_options();

            // default
            $valid_input = $this->get_options();

            $submit = ( ! empty( $input['submit-general'] ) ? true : false );
            $reset  = ( ! empty( $input['reset-general'] ) ? true : false );

            foreach ( $pst_options[$current_tab] as $section => $data ) {
                foreach ( $data as $option ) {
                    if ( isset( $option['sanitize_call'] ) && isset( $option['id'] ) ) { //yiw_debug($option, false);
                        if ( is_array( $option['sanitize_call'] ) ) :
                            foreach ( $option['sanitize_call'] as $callback ) {
                                if ( is_array( $input[$option['id']] ) ) {
                                    $valid_input[$option['id']] = array_map( $callback, $input[$option['id']] );
                                }
                                else {
                                    $valid_input[$option['id']] = call_user_func( $callback, $input[$option['id']] );
                                }
                            }
                        else :
                            if ( is_array( $input[$option['id']] ) ) {
                                $valid_input[$option['id']] = array_map( $option['sanitize_call'], $input[$option['id']] );
                            }
                            else {
                                $valid_input[$option['id']] = call_user_func( $option['sanitize_call'], $input[$option['id']] );
                            }
                        endif;
                    }
                    else {
                        if ( isset( $option['id'] ) ) {
                            if ( isset( $input[$option['id']] ) ) {
                                $valid_input[$option['id']] = $input[$option['id']];
                            }
                            else {
                                $valid_input[$option['id']] = 'no';
                            }

                        }
                    }

                }
            }

            return $valid_input;
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
            add_submenu_page( $this->settings['parent_slug'] . $this->settings['parent_page'], $this->settings['page_title'], $this->settings['menu_title'], $this->settings['capability'], $this->settings['page'], array( &$this, 'pst_panel' ) );
            /* === Duplicate Items Hack === */
            $this->remove_duplicate_submenu_page();
            do_action( 'pst_after_add_settings_page' );
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
            $pst_options = $this->get_main_array_options();

            // tabs
            foreach ( $this->settings['admin-tabs'] as $tab => $tab_value ) {
                $active_class = ( $current_tab == $tab ) ? ' nav-tab-active' : '';
                $tabs .= '<a class="nav-tab' . $active_class . '" href="?' . $this->settings['parent_page'] . '&page=' . $this->settings['page'] . '&tab=' . $tab . '">' . $tab_value . '</a>';
            }
            ?>
            <div id="icon-themes" class="icon32"><br /></div>
            <h2 class="nav-tab-wrapper">
                <?php echo $tabs ?>
            </h2>
            <?php
            $custom_tab_action = $this->is_custom_tab( $pst_options, $current_tab );
            if ( $custom_tab_action ) {
                $this->print_custom_tab( $custom_tab_action );
                return;
            }
            ?>
            <div id="wrap" class="plugin-option">
                <?php $this->message(); ?>
                <h2><?php echo $this->get_tab_title() ?></h2>
                <?php if ( $this->is_show_form() ) : ?>
                    <form method="post" action="options.php">
                        <?php do_settings_sections( 'pst' ); ?>
                        <p>&nbsp;</p>
                        <?php settings_fields( 'pst_' . $this->settings['parent'] . '_options' ); ?>
                        <input type="hidden" name="<?php echo $this->get_name_field( 'current_tab' ) ?>" value="<?php echo esc_attr( $current_tab ) ?>" />
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'pst' ) ?>" style="float:left;margin-right:10px;" />
                    </form>
                    <form method="post">
                        <?php $warning = __( 'If you continue with this action, you will reset all options in this page.', 'pst' ) ?>
                        <input type="hidden" name="pst-action" value="reset" />
                        <input type="submit" name="pst-reset" class="button-secondary" value="<?php _e( 'Reset Defaults', 'pst' ) ?>" onclick="return confirm('<?php echo $warning . '\n' . __( 'Are you sure?', 'pst' ) ?>');" />
                    </form>
                    <p>&nbsp;</p>
                <?php endif ?>
            </div>
        <?php
        }

        public function is_custom_tab( $options, $current_tab ) {
            foreach ( $options[$current_tab] as $section => $option ) {
                if ( isset( $option['type'] ) && isset( $option['action'] ) && 'custom_tab' == $option['type'] && ! empty( $option['action'] ) ) {
                    return $option['action'];
                }
                else {
                    return false;
                }
            }
        }

        /**
         * Fire the action to print the custom tab
         *
         *
         * @param $action Action to fire
         *
         * @return void
         * @since    1.0
         */
        public function print_custom_tab( $action ) {
            do_action( $action );
        }

        /**
         * Add sections and fields to setting panel
         *
         * read all options and show sections and fields
         *
         * @return void
         * @since    1.0
         */
        public function add_fields() {
            $pst_options = $this->get_main_array_options();
            $current_tab = $this->get_current_tab();

            if ( ! $current_tab ) {
                return;
            }
            foreach ( $pst_options[$current_tab] as $section => $data ) {
                add_settings_section( "pst_settings_{$current_tab}_{$section}", $this->get_section_title( $section ), $this->get_section_description( $section ), 'pst' );
                foreach ( $data as $option ) {
                    if ( isset( $option['id'] ) && isset( $option['type'] ) && isset( $option['name'] ) ) {
                        add_settings_field( "pst_setting_" . $option['id'], $option['name'], array( $this, 'render_field' ), 'pst', "pst_settings_{$current_tab}_{$section}", array( 'option' => $option, 'label_for' => $this->get_id_field( $option['id'] ) ) );
                    }
                }
            }
        }


        /**
         * Add the tabs to admin bar menu
         *
         * set all tabs of settings page on wp admin bar
         *
         * @return void|array return void when capability is false
         * @since  1.0
         */
        public function add_admin_bar_menu() {

            global $wp_admin_bar;

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! empty( $this->settings['admin_tabs'] ) ) {
                foreach ( $this->settings['admin-tabs'] as $item => $title ) {

                    $wp_admin_bar->add_menu( array(
                        'parent' => $this->settings['parent'],
                        'title'  => $title,
                        'id'     => $this->settings['parent'] . '-' . $item,
                        'href'   => admin_url( 'themes.php' ) . '?page=' . $this->settings['parent_page'] . '&tab=' . $item
                    ) );
                }
            }
        }


        /**
         * Get current tab
         *
         * get the id of tab showed, return general is the current tab is not defined
         *
         * @return string
         * @since  1.0
         */
        function get_current_tab() {
            $admin_tabs = array_keys( $this->settings['admin-tabs'] );

            if ( ! isset( $_GET['page'] ) || $_GET['page'] != $this->settings['page'] ) {
                return false;
            }
            if ( isset( $_REQUEST['pst_tab_options'] ) ) {
                return $_REQUEST['pst_tab_options'];
            }
            elseif ( isset( $_GET['tab'] ) && isset( $this->_tabs_path_files[$_GET['tab']] ) ) {
                return $_GET['tab'];
            }
            elseif ( isset( $admin_tabs[0] ) ) {
                return $admin_tabs[0];
            }
            else {
                return 'general';
            }
        }


        /**
         * Message
         *
         * define an array of message and show the content od message if
         * is find in the query string
         *
         * @return void
         * @since  1.0
         */
        public function message() {

            $message = array(
                'element_exists'   => $this->get_message( '<strong>' . __( 'The element you have entered already exists. Please, enter another name.', 'pst' ) . '</strong>', 'error', false ),
                'saved'            => $this->get_message( '<strong>' . __( 'Settings saved', 'pst' ) . '.</strong>', 'updated', false ),
                'reset'            => $this->get_message( '<strong>' . __( 'Settings reset', 'pst' ) . '.</strong>', 'updated', false ),
                'delete'           => $this->get_message( '<strong>' . __( 'Element deleted correctly.', 'pst' ) . '</strong>', 'updated', false ),
                'updated'          => $this->get_message( '<strong>' . __( 'Element updated correctly.', 'pst' ) . '</strong>', 'updated', false ),
                'settings-updated' => $this->get_message( '<strong>' . __( 'Element updated correctly.', 'pst' ) . '</strong>', 'updated', false ),
                'imported'         => $this->get_message( '<strong>' . __( 'Database imported correctly.', 'pst' ) . '</strong>', 'updated', false ),
                'no-imported'      => $this->get_message( '<strong>' . __( 'An error has occurred during import. Please try again.', 'pst' ) . '</strong>', 'error', false ),
                'file-not-valid'   => $this->get_message( '<strong>' . __( 'The added file is not valid.', 'pst' ) . '</strong>', 'error', false ),
                'cant-import'      => $this->get_message( '<strong>' . __( 'Sorry, import is disabled.', 'pst' ) . '</strong>', 'error', false ),
                'ord'              => $this->get_message( '<strong>' . __( 'Sorting successful.', 'pst' ) . '</strong>', 'updated', false )
            );

            foreach ( $message as $key => $value ) {
                if ( isset( $_GET[$key] ) ) {
                    echo $message[$key];
                }
            }

        }

        /**
         * Get Message
         *
         * return html code of message
         *
         * @param        $message
         * @param string $type can be 'error' or 'updated'
         * @param bool   $echo
         *
         * @return void|string
         * @since  1.0
         */
        public function get_message( $message, $type = 'error', $echo = true ) {
            $message = '<div id="message" class="' . $type . ' fade"><p>' . $message . '</p></div>';
            if ( $echo ) {
                echo $message;
            }
            return $message;
        }


        /**
         * Get Tab Path Files
         *
         * return an array with filenames of tabs
         *
         * @return array
         * @since    1.0
         */
        function get_tabs_path_files() {

            $option_files_path = $this->settings['options-path'] . '/';

            $tabs = array();

            foreach ( ( array ) glob( $option_files_path . '*.php' ) as $filename ) {
                preg_match( '/(.*)-options\.(.*)/', basename( $filename ), $filename_parts );

	            if ( ! isset( $filename_parts[1] ) ) {
		            continue;
	            }

                $tab = $filename_parts[1];

                $tabs[$tab] = $filename;
            }

            return $tabs;
        }

        /**
         * Get main array options
         *
         * return an array with all options defined on options-files
         *
         * @return array
         * @since    1.0
         */
        function get_main_array_options() {
            if ( ! empty( $this->_main_array_options ) ) {
                return $this->_main_array_options;
            }

            foreach ( $this->settings['admin-tabs'] as $item => $v ) {
                $path = $this->settings['options-path'] . '/' . $item . '-options.php';
                if ( file_exists( $path ) ) {
                    $this->_main_array_options = array_merge( $this->_main_array_options, include $path );
                }
            }

            return $this->_main_array_options;
        }


        /**
         * Set an array with all default options
         *
         * put default options in an array
         *
         * @return array
         * @since  1.0
         */
        public function get_default_options() {
            $pst_options     = $this->get_main_array_options();
            $default_options = array();

            foreach ( $pst_options as $tab => $sections ) {
                foreach ( $sections as $section ) {
                    foreach ( $section as $id => $value ) {
                        if ( isset( $value['std'] ) && isset( $value['id'] ) ) {
                            $default_options[$value['id']] = $value['std'];
                        }
                    }
                }
            }

            unset( $pst_options );
            return $default_options;
        }


        /**
         * Get the title of the tab
         *
         * return the title of tab
         *
         * @return string
         * @since    1.0
         */
        function get_tab_title() {
            $pst_options = $this->get_main_array_options();
            $current_tab = $this->get_current_tab();

            foreach ( $pst_options[$current_tab] as $sections => $data ) {
                foreach ( $data as $option ) {
                    if ( isset( $option['type'] ) && $option['type'] == 'title' ) {
                        return $option['name'];
                    }
                }
            }
        }

        /**
         * Get the title of the section
         *
         * return the title of section
         *
         * @param $section
         *
         * @return string
         * @since    1.0
         */
        function get_section_title( $section ) {
            $pst_options = $this->get_main_array_options();
            $current_tab = $this->get_current_tab();

            foreach ( $pst_options[$current_tab][$section] as $option ) {
                if ( isset( $option['type'] ) && $option['type'] == 'section' ) {
                    return $option['name'];
                }
            }
        }

        /**
         * Get the description of the section
         *
         * return the description of section if is set
         *
         * @param $section
         *
         * @return string
         * @since    1.0
         */
        function get_section_description( $section ) {
            $pst_options = $this->get_main_array_options();
            $current_tab = $this->get_current_tab();

            foreach ( $pst_options[$current_tab][$section] as $option ) {
                if ( isset( $option['type'] ) && $option['type'] == 'section' && isset( $option['desc'] ) ) {
                    return '<p>' . $option['desc'] . '</p>';
                }
            }
        }


        /**
         * Show form when necessary
         *
         * return true if 'showform' is not defined
         *
         * @return bool
         * @since  1.0
         */
        function is_show_form() {
            $pst_options = $this->get_main_array_options();
            $current_tab = $this->get_current_tab();

            foreach ( $pst_options[$current_tab] as $sections => $data ) {
                foreach ( $data as $option ) {
                    if ( ! isset( $option['type'] ) || $option['type'] != 'title' ) {
                        continue;
                    }
                    if ( isset( $option['showform'] ) ) {
                        return $option['showform'];
                    }
                    else {
                        return true;
                    }
                }
            }
        }

        /**
         * Get name field
         *
         * return a string with the name of the input field
         *
         * @param string $name
         *
         * @return string
         * @since  1.0
         */
        function get_name_field( $name = '' ) {
            return 'pst_' . $this->settings['parent'] . '_options[' . $name . ']';
        }

        /**
         * Get id field
         *
         * return a string with the id of the input field
         *
         * @param string $id
         *
         * @return string
         * @since  1.0
         */
        function get_id_field( $id ) {
            return 'pst_' . $this->settings['parent'] . '_options_' . $id;
        }


        /**
         * Render the field showed in the setting page
         *
         * include the file of the option type, if file do not exists
         * return a text area
         *
         * @param array $param
         *
         * @return void
         * @since  1.0
         */
        function render_field( $param ) {

            if ( ! empty( $param ) && isset( $param ['option'] ) ) {
                $option     = $param ['option'];
                $db_options = $this->get_options();

                $db_value = ( isset( $db_options[$option['id']] ) ) ? $db_options[$option['id']] : '';
                if ( isset( $option['deps'] ) ) {
                    $deps = $option['deps'];
                }
                $type = PST_CORE_PLUGIN_PATH . '/templates/panel/types/' . $option['type'] . '.php';
                if ( file_exists( $type ) ) {
                    include $type;
                }
                else {
                    do_action( "pst_panel_{$option['type']}" );
                }
            }
        }

        /**
         * Get options from db
         *
         * return the options from db, if the options aren't defined in the db,
         * get the default options ad add the options in the db
         *
         * @return array
         * @since  1.0
         */
        public function get_options() {
            $options = get_option( 'pst_' . $this->settings['parent'] . '_options' );
            if ( $options === false || ( isset( $_REQUEST['pst-action'] ) && $_REQUEST['pst-action'] == 'reset' ) ) {
                $options = $this->get_default_options();
            }
            return $options;
        }


    }

}