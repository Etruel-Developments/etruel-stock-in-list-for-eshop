<?php
/**
 * Plugin Name:     Fakturo Boilerplate
 * Plugin URI:      @todo
 * Description:     Fakturo Add-on starter point Boilerplate plugin 
 * Version:         1.0.0
 * Author:          etruel
 * Author URI:      http://www.netmdp.com
 * Text Domain:     fktr_boilerplate
 *
 * @package         etruel\Boilerplate
 * @author          Esteban Truelsegaard
 * @copyright       Copyright (c) 2016
 *
 *
 * - Find all instances of @todo in the plugin and update the relevant
 *   areas as necessary.
 *
 * - All functions that are not class methods MUST be prefixed with the
 *   plugin name, replacing spaces with underscores. NOT PREFIXING YOUR
 *   FUNCTIONS CAN CAUSE PLUGIN CONFLICTS!
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Fakturo_Boilerplate' ) ) {

    /**
     * Main Boilerplate class
     *
     * @since       1.0.0
     */
    class Fakturo_Boilerplate {

        /**
         * @var         Boilerplate $instance The one true Boilerplate
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true Boilerplate
         */
        public static function instance() {
            if(!self::$instance) {
                self::$instance = new self();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
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
			if(!defined('BOILERPLATE_VER')) {
				define('BOILERPLATE_VER', '1.0.0' );
			}
			// Plugin root file
			if(!defined('BOILERPLATE_ROOT_FILE')) {
				define('BOILERPLATE_ROOT_FILE', __FILE__ );
			}
            // Plugin path
			if(!defined('BOILERPLATE_DIR')) {
				define('BOILERPLATE_DIR', plugin_dir_path( __FILE__ ) );
			}
            // Plugin URL
			if(!defined('BOILERPLATE_URL')) {
				define('BOILERPLATE_URL', plugin_dir_url( __FILE__ ) );
			}
			if(!defined('BOILERPLATE_STORE_URL')) {
				define('BOILERPLATE_STORE_URL', 'https://etruel.com'); 
			} 
			if(!defined('BOILERPLATE_ITEM_NAME')) {
				define('BOILERPLATE_ITEM_NAME', 'Fakturo Boilerplate'); 
			} 
			// Plugin text domain
			if (!defined('BOILERPLATE_TEXT_DOMAIN')) {
				define('BOILERPLATE_TEXT_DOMAIN', 'boilerplate');
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
			
			
			require_once BOILERPLATE_DIR . 'includes/settings.php';
            require_once BOILERPLATE_DIR . 'includes/plugin_functions.php';
            require_once BOILERPLATE_DIR . 'includes/functions.php';

        }


        /**
         * Run action and filter hooks
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         *
         */
         public static function hooks() {
            // Register settings
            
			add_filter( 'fktr_plugins_updater_args', array(__CLASS__, 'add_updater'), 10, 1);
           
        }
		
		public static function add_updater($args) {
			if (empty($args['boilerplate'])) {
				$args['boilerplate'] = array();
				$args['boilerplate']['api_url'] = BOILERPLATE_STORE_URL;
				$args['boilerplate']['plugin_file'] = BOILERPLATE_ROOT_FILE;
				$args['boilerplate']['api_data'] = array(
														'version' 	=> BOILERPLATE_VER, 				// current version number
														'item_name' => BOILERPLATE_ITEM_NAME, 	// name of this plugin
														'author' 	=> 'Esteban Truelsegaard'  // author of this plugin
													);
					
			}
			return $args;
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
            $lang_dir = BOILERPLATE_DIR . '/languages/';
            $lang_dir = apply_filters( 'boilerplate_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'boilerplate' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'boilerplate', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/boilerplate/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/boilerplate/ folder
                load_textdomain( 'boilerplate', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/boilerplate/languages/ folder
                load_textdomain( 'boilerplate', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'boilerplate', false, $lang_dir );
            }
        }


       
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true Boilerplate
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Boilerplate The one true Boilerplate
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */
function Boilerplate_load() {
    if( !class_exists( 'fakturo' ) ) {
        if( !class_exists( 'Fakturo_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }

        $activation = new Fakturo_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return Fakturo_Boilerplate::instance();
    }
}

add_action( 'plugins_loaded', 'Boilerplate_load');

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
register_activation_hook( plugin_basename( __FILE__ ), 'boilerplate_activate' );
function boilerplate_activate() {
    if(class_exists('fakturo')) {
        $link = '<a href="' . admin_url('admin.php?page=fktr-boilerplate-extension-page') . '">'.__('Boilerplate Plugin Settings.',  'boilerplate').'</a>';
        $notice = __('Boilerplate Activated.  Please check the fields on', 'boilerplate').' '. $link;
        fktrNotices::add( array('text' => $notice , 'below-h2' => false , 'screen' => 'plugins_page_fakturo') );
    }
}
/** * Deactivate Boilerplate on Deactivate Plugin  */
register_deactivation_hook( plugin_basename( __FILE__ ), 'boilerplate_deactivate' );
function boilerplate_deactivate() {
    if(class_exists('fakturo')) {
        $notice = __('Boilerplate DEACTIVATED.',  'boilerplate');
        fktrNotices::add( array('text' => $notice , 'below-h2' => false, 'screen' => 'plugins_page_fakturo' ) );
    }
}