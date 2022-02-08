<?php

 /*
        * Plugin Name:    Fakturo Stocks Products
        * Plugin URI:     http://www.fakturo.org
        * Description:    Add a column specifying the stock of each product
        * Version:        1.0.0
        * Author:         etruel
        * Author URI:     https://etruel.com
        * License:        GPL-2.0+
        * Text Domain:    fktr_stock_products
        * Domain Path:    /languages
    */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'fkrt_Stock_Product' ) ) {

    /**
     * Main fkrt_Stock_Product class
     *
     * @since       1.0.0
     */
    class fkrt_Stock_Product {

        /**
         * @var         fkrt_Stock_Product $instance The one true fkrt_Stock_Product
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true fkrt_Stock_Product
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
			if(!defined('FKTR_STOCK_PRODUCT_VER')) {
				define('FKTR_STOCK_PRODUCT_VER', '1.0.0' );
			}
			// Plugin root file
			if(!defined('FKTR_STOCK_PRODUCT_ROOT_FILE')) {
				define('FKTR_STOCK_PRODUCT_ROOT_FILE', __FILE__ );
			}
            // Plugin path
			if(!defined('FKTR_STOCK_PRODUCT_DIR')) {
				define('FKTR_STOCK_PRODUCT_DIR', plugin_dir_path( __FILE__ ) );
			}
            // Plugin URL
			if(!defined('FKTR_STOCK_PRODUCT_URL')) {
				define('FKTR_STOCK_PRODUCT_URL', plugin_dir_url( __FILE__ ) );
			}
			if(!defined('FKTR_STOCK_PRODUCT_STORE_URL')) {
				define('FKTR_STOCK_PRODUCT_STORE_URL', 'https://etruel.com'); 
			} 
			if(!defined('FKTR_STOCK_PRODUCT_ITEM_NAME')) {
				define('FKTR_STOCK_PRODUCT_ITEM_NAME', 'Fakturo Fakturo Stocks Products'); 
			} 
			// Plugin text domain
			if (!defined('FKTR_STOCK_PRODUCT_TEXT_DOMAIN')) {
				define('FKTR_STOCK_PRODUCT_TEXT_DOMAIN', 'fkrt_Stock_Product');
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
			
			
			require_once FKTR_STOCK_PRODUCT_DIR . 'includes/settings.php';
            require_once FKTR_STOCK_PRODUCT_DIR . 'includes/plugin_functions.php';
            require_once FKTR_STOCK_PRODUCT_DIR . 'includes/proccess.php';

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
            $lang_dir = FKTR_STOCK_PRODUCT_DIR . '/languages/';
            $lang_dir = apply_filters( 'fkrt_Stock_Product_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'fkrt_Stock_Product' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'fkrt_Stock_Product', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/fkrt_Stock_Product/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/fkrt_Stock_Product/ folder
                load_textdomain( 'fkrt_Stock_Product', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/fkrt_Stock_Product/languages/ folder
                load_textdomain( 'fkrt_Stock_Product', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'fkrt_Stock_Product', false, $lang_dir );
            }
        }


       
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true Fakturo Stocks Products
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Fakturo Stocks Products The one true Fakturo Stocks Products
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */
function fkrt_Stock_Product_load() {
    if( !class_exists( 'fakturo' ) ) {
        if( !class_exists( 'Fakturo_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }

        $activation = new Fakturo_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return fkrt_Stock_Product::instance();
    }
}

add_action( 'plugins_loaded', 'fkrt_Stock_Product_load');

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
register_activation_hook( plugin_basename( __FILE__ ), 'fkrt_Stock_Product_activate' );

function fkrt_Stock_Product_activate() {
    if(class_exists('fakturo')) {
        $link = '<a href="' . admin_url('admin.php?page=fktr-stock-product-extension-page') . '">'.__('Fakturo Stocks Products Plugin Settings.',  'fkrt_Stock_Product').'</a>';
        $notice = __('Fakturo Stocks Products Activated.  Please check the fields on', 'fkrt_Stock_Product').' '. $link;
        fktrNotices::add( array('text' => $notice , 'below-h2' => false , 'screen' => 'plugins_page_fakturo') );
    }
}

/** * Deactivate fkrt_Stock_Producte on Deactivate Plugin  */
register_deactivation_hook( plugin_basename( __FILE__ ), 'fkrt_Stock_Product_deactivate' );
function fkrt_Stock_Product_deactivate() {
    if(class_exists('fakturo')) {
        $notice = __('Fakturo Stocks Products DEACTIVATED.',  'fkrt_Stock_Product');
        fktrNotices::add( array('text' => $notice , 'below-h2' => false, 'screen' => 'plugins_page_fakturo' ) );
    }
}