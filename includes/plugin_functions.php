<?php
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
class boilerplate_plugin_functions {

	public static function hooks() {
		add_filter(	'plugin_row_meta',	array(__CLASS__, 'row_meta') ,10,2);
		//add_filter(	'plugin_action_links_' . plugin_basename( FKTR_STOCK_PRODUCT_ROOT_FILE ), array(__CLASS__, 'action_links'));

	}
	/**
	* Meta-Links del Plugin
	*
	* @param   array   $data  Original Links
	* @param   string  $page  plugin actual
	* @return  array   $data  modified Links
	*/

	public static function row_meta($data, $page)	{
		if ( basename($page) != basename(FKTR_STOCK_PRODUCT_ROOT_FILE)) {
			return $data;
		}
		return array_merge(
			$data,
			array(
			'<a href="https://etruel.com/" target="_blank">' . __('etruel Store') . '</a>',
			'<a href="https://etruel.com/my-account/support/" target="_blank">' . __('Support') . '</a>',
			'<a href="https://wordpress.org/support/view/plugin-reviews/fakturo?filter=5&rate=5#postform" target="_Blank" title="Rate 5 stars on Wordpress.org">' . __('Rate Plugin' ) . '</a>'
			)
		);	

	}
	/**
	* Actions-Links del Plugin
	*
	* @param   array   $data  Original Links
	* @return  array   $data  modified Links
	*/
	public static function action_links($data) {
		//die(var_export($data));
		if ( !current_user_can('manage_options') ) {
			return $data;
		}
		return array_merge(
			$data,
			array(
				'<a href="'.  admin_url('admin.php?page=fktr-stock-product-extension-page').'" title="' . __('Go to Fakturo Stocks Products Settings Page') . '">' . __('Settings') . '</a>',
			)
		);
	}
}
boilerplate_plugin_functions::hooks();





?>