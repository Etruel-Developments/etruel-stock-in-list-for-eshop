<?php
/** 
 *  @package Fakturo fkrt_stock_in_list
 *	functions to add a tab with custom options in fakturo settings 
**/

if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
class fkrt_stock_in_list_page_extension {

	public static function hooks() {
		
		add_filter( 'ftkr_tabs_sections', array(__CLASS__, 'settings_tab' ), 1 );
		add_action( 'admin_post_save_fkrt_stock_in_list', array(__CLASS__, 'save'));
	}
	 /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing Fakturo settings array
         * @return      array The modified Fakturo settings array
    */
    public static function settings_tab( $tabs ) {
        if (!isset( $tabs['extensions'])) {
            $tabs['extensions'] = array();
        }
        if (!isset( $tabs['extensions']['fkrt_stock_in_list'])) {
           	$tabs['extensions']['fkrt_stock_in_list'] = array('text' => __( 'fkrt_stock_in_list', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ), 'url' => admin_url('admin.php?page=fktr-stock-product-extension-page'), 'screen' => 'admin_page_fktr-stock-product-extension-page');
        }
        if (!isset( $tabs['extensions']['default'])) {
           	$tabs['extensions']['default'] = array('text' => __( 'Extensions', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ), 'url' => '', 'screen' => '');
        }
        if (empty($tabs['extensions']['default']['screen']) && empty($tabs['extensions']['default']['url'])) {
           	$tabs['extensions']['default'] = array('text' => __( 'Extensions', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ), 'url' => admin_url('admin.php?page=fktr-stock-product-extension-page'), 'screen' => 'admin_page_fktr-stock-product-extension-page');
        }
        return $tabs;
    }	
 

	public static function page() {
		global $current_screen;
		$values = get_option('fktr_fkrt_stock_in_list_settings', array());
		echo '<div id="tab_container">
			<br/>
			<h1>'.__( 'fkrt_stock_in_list Settings', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ).'</h1>
			<form action="'.admin_url( 'admin-post.php' ).'" id="form_fkrt_stock_in_list" method="post">
				<input type="hidden" name="action" value="save_fkrt_stock_in_list"/>';
				wp_nonce_field('save_fkrt_stock_in_list');
				echo '<table class="form-table">
						<tr valign="top">
							<th scope="row">'.__( 'Field', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ).'</th>
							<td>
								<input type="text" name="fktr_fkrt_stock_in_list[field]" id="fktr_fkrt_stock_in_list_field" value="'.$values['field'].'"/>
								<p class="description">'.__( 'Description of field', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ).'</p>
							</td>
						</tr>
					</table>';
				submit_button();
			echo '</form>
		</div>';
	}
	public static function save() {
		if ( ! wp_verify_nonce($_POST['_wpnonce'], 'save_fkrt_stock_in_list' ) ) {
		    wp_die(__( 'Security check', FKTR_STOCK_IN_LIST_TEXT_DOMAIN )); 
		}
		update_option('fktr_fkrt_stock_in_list_settings', $_POST['fktr_fkrt_stock_in_list']);
		fktrNotices::add(__( 'Settings updated', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ));
		wp_redirect($_POST['_wp_http_referer']);
		exit;
	}
}
fkrt_stock_in_list_page_extension::hooks();
?>