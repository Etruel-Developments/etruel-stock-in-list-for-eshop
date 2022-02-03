<?php
/** 
 *  @package Fakturo boilerplate
 *	functions to add a tab with custom options in fakturo settings 
**/

if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
class boilerplate_page_extension {

	public static function hooks() {
		add_action('admin_menu', array(__CLASS__, 'admin_menu'), 99);
		add_filter( 'ftkr_tabs_sections', array(__CLASS__, 'settings_tab' ), 1 );
		add_action( 'admin_post_save_boilerplate', array(__CLASS__, 'save'));
	}
	 /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
    */
    public static function settings_tab( $tabs ) {
        if (!isset( $tabs['extensions'])) {
            $tabs['extensions'] = array();
        }
        if (!isset( $tabs['extensions']['boilerplate'])) {
           	$tabs['extensions']['boilerplate'] = array('text' => __( 'Boilerplate', BOILERPLATE_TEXT_DOMAIN ), 'url' => admin_url('admin.php?page=fktr-boilerplate-extension-page'), 'screen' => 'admin_page_fktr-boilerplate-extension-page');
        }
        if (!isset( $tabs['extensions']['default'])) {
           	$tabs['extensions']['default'] = array('text' => __( 'Extensions', FAKTURO_TEXT_DOMAIN ), 'url' => '', 'screen' => '');
        }
        if (empty($tabs['extensions']['default']['screen']) && empty($tabs['extensions']['default']['url'])) {
           	$tabs['extensions']['default'] = array('text' => __( 'Extensions', FAKTURO_TEXT_DOMAIN ), 'url' => admin_url('admin.php?page=fktr-boilerplate-extension-page'), 'screen' => 'admin_page_fktr-boilerplate-extension-page');
        }
        return $tabs;
    }	
	public static function admin_menu() {
		$page = add_submenu_page(
			null,
			__( 'Settings', BOILERPLATE_TEXT_DOMAIN ), 
			__( 'Settings', BOILERPLATE_TEXT_DOMAIN ), 
			'edit_fakturo_settings', 
			'fktr-boilerplate-extension-page',
			array(__CLASS__,'page'), 
			'dashicons-tickets', 27 
			);	
	}

	public static function page() {
		global $current_screen;
		$values = get_option('fktr_boilerplate_settings', array());
		echo '<div id="tab_container">
			<br/>
			<h1>'.__( 'Boilerplate Settings', BOILERPLATE_TEXT_DOMAIN ).'</h1>
			<form action="'.admin_url( 'admin-post.php' ).'" id="form_boilerplate" method="post">
				<input type="hidden" name="action" value="save_boilerplate"/>';
				wp_nonce_field('save_boilerplate');
				echo '<table class="form-table">
						<tr valign="top">
							<th scope="row">'.__( 'Field', BOILERPLATE_TEXT_DOMAIN ).'</th>
							<td>
								<input type="text" name="fktr_boilerplate[field]" id="fktr_boilerplate_field" value="'.$values['field'].'"/>
								<p class="description">'.__( 'Description of field', BOILERPLATE_TEXT_DOMAIN ).'</p>
							</td>
						</tr>
					</table>';
				submit_button();
			echo '</form>
		</div>';
	}
	public static function save() {
		if ( ! wp_verify_nonce($_POST['_wpnonce'], 'save_boilerplate' ) ) {
		    wp_die(__( 'Security check', BOILERPLATE_TEXT_DOMAIN )); 
		}
		update_option('fktr_boilerplate_settings', $_POST['fktr_boilerplate']);
		fktrNotices::add(__( 'Settings updated', BOILERPLATE_TEXT_DOMAIN ));
		wp_redirect($_POST['_wp_http_referer']);
		exit;
	}
}
boilerplate_page_extension::hooks();
?>