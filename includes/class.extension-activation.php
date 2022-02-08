<?php
/**
 * Activation handler
 *
 * @package     WPeMatico\ActivationHandler
 * @since       1.0.0
 */


// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Fakturo Extension Activation Handler Class
 *
 * @since       1.0.0
 */
class Fakturo_Extension_Activation {

    public $plugin_name, $plugin_path, $plugin_file, $has_wpematico, $wpematico_base;

    /**
     * Setup the activation class
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        if( isset( $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] ) ) {
            $this->plugin_name = str_replace( 'Fakturo - ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );
        } else {
            $this->plugin_name = __( 'This plugin', 'fkrt_Stock_Product');
        }

        // Is WPeMatico installed?
        foreach( $plugins as $plugin_path => $plugin ) {
            if( $plugin['Name'] == 'Fakturo' ) {
                $this->has_wpematico = true;
                $this->wpematico_base = $plugin_path;
                break;
            }
        }
    }


    /**
     * Process plugin deactivation
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function run() {
        // Display notice
        add_action( 'admin_notices', array( $this, 'missing_wpematico_notice' ) );
    }


    /**
     * Display notice if WPeMatico isn't installed
     *
     * @access      public
     * @since       1.0.0
     * @return      string The notice to display
     */
    public function missing_wpematico_notice() {
        if( $this->has_wpematico ) {
            $url  = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $this->wpematico_base ), 'activate-plugin_' . $this->wpematico_base ) );
            $link = '<a href="' . $url . '">' . __( 'activate it', 'fkrt_Stock_Product') . '</a>';
        } else {
            $url  = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wpematico' ), 'install-plugin_wpematico' ) );
            $link = '<a href="' . $url . '">' . __( 'install it', 'fkrt_Stock_Product' ) . '</a>';
        }
        
        echo '<div class="error"><p>' . $this->plugin_name . sprintf( __( ' requires Fakturo! Please %s to continue!', 'fkrt_Stock_Product' ), $link ) . '</p></div>';
    }
}
