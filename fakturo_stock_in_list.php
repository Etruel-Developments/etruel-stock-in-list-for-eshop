<?php
 /*
        * Plugin Name:    Fakturo Stock in List
        * Plugin URI:     http://www.fakturo.org
        * Description:    Add a column specifying the stock of each product plus other features like print or export Products list.
        * Version:        1.0.0
        * Author:         Etruel Developments LLC
        * Author URI:     https://etruel.com
        * License:        GPL-2.0+
        * Text Domain:    etruel-stock-in-list-for-eshop
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
				define('FKTR_STOCK_IN_LIST_TEXT_DOMAIN', 'etruel-stock-in-list-for-eshop');
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
            $lang_dir = apply_filters( 'etruel-stock-in-list-for-eshop_languages_directory', $lang_dir );

            // Load the default language files
            load_plugin_textdomain( 'etruel-stock-in-list-for-eshop', false, $lang_dir );
        }

        
        /** 
         * Register the stylesheets for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_styles() {

            //wp_enqueue_style( 'fktr-stock-in-list-css', plugin_dir_url( __FILE__ ) . 'assets/css/fktr_stock_in_list.css', array(), '1.0.0', 'all' );

        }

        /**
         * Register the JavaScript for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts() {

            wp_enqueue_script( 'fktr-stock-in-list-js', plugin_dir_url( __FILE__ ) . 'assets/js/fktr_stock_in_list.js', array( 'jquery' ), '1.0.0', false );

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
        $link = '<a href="' . admin_url('admin.php?page=fktr-stock-in-list-extension-page') . '">'.__('Fakturo Stock in List Plugin Settings.',  'etruel-stock-in-list-for-eshop').'</a>';
        $notice = __('Fakturo Stock in List Activated.  Please check the fields on', 'etruel-stock-in-list-for-eshop').' '. $link;
        fktrNotices::add( array('text' => $notice , 'below-h2' => false , 'screen' => 'plugins_page_fakturo') );
    }
}

/** * Deactivate fkrt_stock_in_liste on Deactivate Plugin  */
register_deactivation_hook( plugin_basename( __FILE__ ), 'fkrt_stock_in_list_deactivate' );
function fkrt_stock_in_list_deactivate() {
    if(class_exists('fakturo')) {
        $notice = __('Fakturo Stock in List DEACTIVATED.',  'etruel-stock-in-list-for-eshop');
        fktrNotices::add( array('text' => $notice , 'below-h2' => false, 'screen' => 'plugins_page_fakturo' ) );
    }
}