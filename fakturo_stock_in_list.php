<?php

 /*
        * Plugin Name:    Fakturo Stock in List
        * Plugin URI:     http://www.fakturo.org
        * Description:    Add a column specifying the stock of each product
        * Version:        1.0.0
        * Author:         etruel
        * Author URI:     https://etruel.com
        * License:        GPL-2.0+
        * Text Domain:    fktr_stock_in_lists
        * Domain Path:    /languages
    */
//fakturo_stock_in_list
    
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'fkrt_stock_in_list' ) ) {

    /**
     * Main fkrt_stock_in_list class
     *
     * @since       1.0.0
     */
    class fkrt_stock_in_list {

        /**
         * @var         fkrt_stock_in_list $instance The one true fkrt_stock_in_list
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true fkrt_stock_in_list
         */
        public static function instance() {
            if(!self::$instance) {
                self::$instance = new self();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();

            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
       public static function setup_constants() {
            // Plugin version
			if(!defined('FKTR_STOCK_IN_LIST_VER')) {
				define('FKTR_STOCK_IN_LIST_VER', '1.0.0' );
			}
			// Plugin root file
			if(!defined('FKTR_STOCK_IN_LIST_ROOT_FILE')) {
				define('FKTR_STOCK_IN_LIST_ROOT_FILE', __FILE__ );
			}
            // Plugin path
			if(!defined('FKTR_STOCK_IN_LIST_DIR')) {
				define('FKTR_STOCK_IN_LIST_DIR', plugin_dir_path( __FILE__ ) );
			}
            // Plugin URL
			if(!defined('FKTR_STOCK_IN_LIST_URL')) {
				define('FKTR_STOCK_IN_LIST_URL', plugin_dir_url( __FILE__ ) );
			}
			if(!defined('FKTR_STOCK_IN_LIST_STORE_URL')) {
				define('FKTR_STOCK_IN_LIST_STORE_URL', 'https://etruel.com'); 
			} 
			if(!defined('FKTR_STOCK_IN_LIST_ITEM_NAME')) {
				define('FKTR_STOCK_IN_LIST_ITEM_NAME', 'Fakturo Stock in List'); 
			} 
			// Plugin text domain
			if (!defined('FKTR_STOCK_IN_LIST_TEXT_DOMAIN')) {
				define('FKTR_STOCK_IN_LIST_TEXT_DOMAIN', 'fkrt_stock_in_list');
			}
        }


        /**
         * Include necessary files
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
         public static function includes() {
            // Include scripts
			
			
			require_once FKTR_STOCK_IN_LIST_DIR . 'includes/settings.php';
            require_once FKTR_STOCK_IN_LIST_DIR . 'includes/plugin_functions.php';
            require_once FKTR_STOCK_IN_LIST_DIR . 'includes/proccess.php';

        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
         public static function load_textdomain() {
            // Set filter for language directory
            $lang_dir = FKTR_STOCK_IN_LIST_DIR . '/languages/';
            $lang_dir = apply_filters( 'fkrt_stock_in_list_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'fkrt_stock_in_list' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'fkrt_stock_in_list', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/fkrt_stock_in_list/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/fkrt_stock_in_list/ folder
                load_textdomain( 'fkrt_stock_in_list', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/fkrt_stock_in_list/languages/ folder
                load_textdomain( 'fkrt_stock_in_list', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'fkrt_stock_in_list', false, $lang_dir );
            }
        }


       
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true Fakturo Stock in List
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Fakturo Stock in List The one true Fakturo Stock in List
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */
function fkrt_stock_in_list_load() {
    if( !class_exists( 'fakturo' ) ) {
        if( !class_exists( 'Fakturo_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }

        $activation = new Fakturo_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return fkrt_stock_in_list::instance();
    }
}

add_action( 'plugins_loaded', 'fkrt_stock_in_list_load');

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
register_activation_hook( plugin_basename( __FILE__ ), 'fkrt_stock_in_list_activate' );

function fkrt_stock_in_list_activate() {
    if(class_exists('fakturo')) {
        $link = '<a href="' . admin_url('admin.php?page=fktr-stock-in-list-extension-page') . '">'.__('Fakturo Stock in List Plugin Settings.',  'fkrt_stock_in_list').'</a>';
        $notice = __('Fakturo Stock in List Activated.  Please check the fields on', 'fkrt_stock_in_list').' '. $link;
        fktrNotices::add( array('text' => $notice , 'below-h2' => false , 'screen' => 'plugins_page_fakturo') );
    }
}

/** * Deactivate fkrt_stock_in_liste on Deactivate Plugin  */
register_deactivation_hook( plugin_basename( __FILE__ ), 'fkrt_stock_in_list_deactivate' );
function fkrt_stock_in_list_deactivate() {
    if(class_exists('fakturo')) {
        $notice = __('Fakturo Stock in List DEACTIVATED.',  'fkrt_stock_in_list');
        fktrNotices::add( array('text' => $notice , 'below-h2' => false, 'screen' => 'plugins_page_fakturo' ) );
    }
}