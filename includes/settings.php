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
		add_action('admin_menu', array(__CLASS__, 'admin_menu'), 99);
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
        $tabs['extensions']['default'] = array('text' => __( 'Extensions', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ), 'url' => '');
		
        if (!isset( $tabs['extensions']['fkrt_stock_in_list'])) {
           	$tabs['extensions']['fkrt_stock_in_list'] = array('text' => __( 'Fakturo Stock in List', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ), 'url' => admin_url('admin.php?page=fktr-stock-in-list-extension-page'), 'screen' => 'admin_page_fktr-stock-in-list-extension-page');
        }
        if (empty($tabs['extensions']['default']['screen']) && empty($tabs['extensions']['default']['url'])) {
           	$tabs['extensions']['default'] = array('text' => __( 'Extensions', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ), 'url' => admin_url('admin.php?page=fktr-stock-in-list-extension-page'), 'screen' => 'admin_page_fktr-stock-in-list-extension-page');
        }
        return $tabs;
    }	
 
	public static function admin_menu() {
		$page = add_submenu_page(
			'',
			__( 'Settings', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ), 
			__( 'Settings', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ), 
			'edit_fakturo_settings', 
			'fktr-stock-in-list-extension-page',
			array(__CLASS__,'page'), 2, 
			'dashicons-tickets', 
			);	
	}

	public static function page() {
		global $current_screen;
		//$values = ( get_option('fkrt_stock_in_list_settings', array()) !== '' || !empty(get_option('fkrt_stock_in_list_settings', array()))) ? get_option('fkrt_stock_in_list_settings', array()) : '';
		//delete_option('fkrt_stock_in_list_settings');
		$values = get_option('fkrt_stock_in_list_settings', array() );
		//var_dump($values);
		if(!is_array($values) ) {
			$values = array();
		}
		if(!empty($values)){
			//die(var_export($values));
			if(empty($values['in_stock']))
				$values['in_stock'] = "#008000";
			if(empty($values['out_stock']))
				$values['out_stock'] = "#FF0000";
			if(empty($values['low_stock']))
				$values['low_stock'] = "#FF8040";
		}else{
			$values['in_stock'] = "#008000";
			$values['out_stock'] = "#FF0000";
			$values['low_stock'] = "#FF8040";
		}

		echo '<div id="tab_container">
			<br/>
			<h1>'.__( 'General Settings', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ).'</h1>
			<p class="description">'.__( 'Set the color to differentiate the existence of each product.', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ).'</p>
			<form action="'.admin_url( 'admin-post.php' ).'" id="form_fkrt_stock_in_list" method="post">
				<input type="hidden" name="action" value="save_fkrt_stock_in_list"/>';
				wp_nonce_field('save_fkrt_stock_in_list');
				echo '<table class="form-table">
						<tr valign="top">
							<th scope="row">'.__( 'In Stock Color', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ).'</th>
							<td>
								<input type="color" name="fkrt_stock_in_list[in_stock]" id="fkrt_stock_in_list_field" value="'.$values['in_stock'].'"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">'.__( 'Out of Stock Color', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ).'</th>
							<td>
								<input type="color" name="fkrt_stock_in_list[out_stock]" id="fkrt_stock_in_list_field" value="'.$values['out_stock'].'"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">'.__( 'Low Stock Color', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ).'</th>
							<td>
								<input type="color" name="fkrt_stock_in_list[low_stock]" id="fkrt_stock_in_list_field" value="'.$values['low_stock'].'"/>
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
		$settings = isset( $_POST['fkrt_stock_in_list'] ) ? (array) $_POST['fkrt_stock_in_list'] : array();
		$settings = array_map( 'esc_attr', $settings ); // Replace esc_attr with your desire sanitization	
		
		update_option('fkrt_stock_in_list_settings', $settings );
		fktrNotices::add(__( 'Settings updated', FKTR_STOCK_IN_LIST_TEXT_DOMAIN ));
		wp_redirect($_POST['_wp_http_referer']);
		exit;
	}
}
fkrt_stock_in_list_page_extension::hooks();
?>