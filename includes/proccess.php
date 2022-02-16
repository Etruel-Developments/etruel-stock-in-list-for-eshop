<?php
	
	if (!class_exists('fkrt_stock_in_list_proccess') ) {
		
		class fkrt_stock_in_list_proccess {
			
			function __construct() {
				
				add_filter('manage_fktr_product_posts_columns', array(__CLASS__, 'columns'));
				
				add_filter('manage_fktr_product_posts_custom_column', array(__CLASS__, 'manage_columns'), 10, 2);
				
				add_filter('manage_edit-fktr_product_sortable_columns', array(__CLASS__, 'sortable_columns'));
				
				add_action('restrict_manage_posts', array(__CLASS__, 'print_buttons_reports'));
				
				add_filter('get_objects_reports_fktr_product', array(__CLASS__, 'get_objects'), 10, 4);
				
				// export report		
				add_action('admin_post_products_print_pdf', array(__CLASS__, 'fkrt_stock_in_list_print_pdf'));
				
				add_action('admin_post_products_download_csv', array(__CLASS__, 'fkrt_stock_in_list_download_csv'));
				
			}
			
			public static function columns($columns) {
				
				$column_reference_pos = 6;
				$column_reference = ['stock' => __('Stock', 'fakturo')];
				// 6nd column
				$columns = array_slice($columns, 0, $column_reference_pos, true) + $column_reference + array_slice($columns, $column_reference_pos, NULL, true);
				//$columns		 = array_merge($columns, $column_reference);
				return $columns;
			}
			
			public static function manage_columns($column, $post_id) {
				$product_data = fktrPostTypeProducts::get_product_data($post_id);
				switch ($column) {
					case 'stock':
					
					$terms_stock = get_fakturo_terms(array(
					'taxonomy' => 'fktr_locations',
					'hide_empty' => false,
					));
					
					$total_stock = 0;
					foreach ($terms_stock as $stock) {
						$total_stock = $total_stock + (isset($product_data['stocks'][$stock->term_id]) ? $product_data['stocks'][$stock->term_id] : 0 );
					}
					
					$data_color_stock = get_option('fkrt_stock_in_list_settings', array());
					
					if($total_stock < 0){
						echo '<span style="color:'.$data_color_stock['out_stock'].';">Agotado ('.$total_stock.') </span>';
						}else if($total_stock == 0){
						echo '<span style="color:'.$data_color_stock['out_stock'].';">Agotado</span>';
						}else if($total_stock >= 1 && $total_stock <= 5 ){
						echo '<span style="color:'.$data_color_stock['low_stock'].';">Hay existencia ('.$total_stock.') </span>';
						}else {
						echo '<span style="color:'.$data_color_stock['in_stock'].';">Hay existencia ('.$total_stock.') </span>';
					}
					
					break;
				}			
			}
			
			public static function sortable_columns($columns) {
				$custom = array(
				'stocks' => 'stocks',
				);
				return wp_parse_args($custom, $columns);
			}
			
			public static function print_buttons_reports($request){
				global $pagenow, $post_type;
				
				$request = wp_parse_args($_REQUEST, apply_filters('report_default_requests_values', array()));
				
				if('edit.php' == $pagenow && $post_type == 'fktr_product' ){
				?>
				<a class="button-secondary right" title="Download CSV" href="<?php echo admin_url('admin-post.php?action=products_download_csv&'.http_build_query($request) ) ?>" >
					<?php echo __( 'Download CSV', 'fakturo' ) ?>
				</a>
				<a class="button-secondary right" title="Download PDF" style="margin-right:10px;" href="<?php echo admin_url('admin-post.php?action=products_print_pdf&'.http_build_query($request)) ?>">
					<?php echo __( 'Download PDF', 'fakturo' )?> 
				</a>
				<?php
				}
			}
			
			public static function get_objects($return, $request, $ranges, $limit) {
				
				global $wpdb;
				
				$sql = "SELECT p.ID, pm.meta_key, pm.meta_value as timestamp_value, p.post_type as post_type FROM {$wpdb->posts} as p
				LEFT JOIN {$wpdb->postmeta} as pm ON p.ID = pm.post_id
				WHERE pm.meta_key = 'post_title'
				AND p.post_status = 'publish'
				AND (p.post_type = 'fktr_product' OR p.post_type = 'fktr_receipt') 
				ORDER BY pm.meta_value ASC";
				
				$results = $wpdb->get_results($sql, OBJECT);
				if (!empty($results)) {
					$return = $results;
				}
				return $return;
			}
			
			/**
				* Get the defaults values of a request.
				* @return Array $array with default values.
			*/
			public static function fktr_default_request() {
				$array = array(
				'sec' => 'fktr_product',
				'range' => 'this_month',
				);
				$array = apply_filters('report_default_requests_values', $array);
				return $array;
			}
			
			/**
				* Static function download_csv
				* @access public
				* @return void
				* @since 0.6
			*/
			public static function fkrt_stock_in_list_download_csv() {
				$request = wp_parse_args($_REQUEST, self::fktr_default_request());
				
				$ranges = array();
				$ranges['from'] = 0;
				$ranges['to'] = 0;
				/*
					* This filter can be used to create or update timestamp ranges.
					* $ranges will be used by get_object_chart()
				*/
				$total_html_print = '';
				
				$setting_system = get_option('fakturo_system_options_group', false);
				$currencyDefault = get_fakturo_term($setting_system['currency'], 'fktr_currencies');
				if (is_wp_error($currencyDefault)) {
					$total_html_print .= '<p>'.__( 'Account Movements needs the default currency on system settings.', 'fakturo' ).'</p>';
					echo $total_html_print;
					return true;
				}
				$default_array_data = array('', '', '', '', '', '');
				$array_data = array();
				
				$new_array = $default_array_data;
				$html_client_data = __('Inventory', 'fakturo' );
			
				
				$objects_client = reports::get_objects($request, $ranges);
				
				//$new_array[5] = sprintf(__('Date: since %s til %s', 'fakturo' ), date_i18n($setting_system['dateformat'], $ranges['from']), date_i18n($setting_system['dateformat'], $ranges['to']));
				//$array_data[] = $new_array;
				//$new_array = $default_array_data;
				if (!empty($objects_client)) {
					
					$options = get_option('fakturo_system_options_group');
					
					$selectSearchCode = array();
					$selectSearchCode['reference'] = __( 'Reference', 'fakturo' );
					$selectSearchCode['internal_code'] = __( 'Internal code', 'fakturo' );
					$selectSearchCode['manufacturers_code'] = __( 'Manufacturers code', 'fakturo' );							
					$selectSearchCode = apply_filters('fktr_search_code_array', $selectSearchCode);
					
					$new_array[0] = __('Name', 'fakturo');
					foreach ($selectSearchCode as $key => $txt) {
						$new_array[1] .=  (array_search($key, $options['search_code'])!==false) ? $txt : '';
					}
					$new_array[2] = __('Scale', 'fakturo');
					$new_array[3] = __('Cost', 'fakturo');
					$new_array[4] = __('Price', 'fakturo');
					$new_array[5] = __('Inventory', 'fakturo');
					$array_data[] = $new_array;
					$new_array = $default_array_data;
					
					foreach ($objects_client as $obj => $testval) {
						
						$product_data = fktrPostTypeProducts::get_product_data($testval->ID);
						// Get Stock
						$total_stock = stock_products_report::get_stock_product_report($product_data);
						
						// Get Prices
						$product_prices = stock_products_report::get_prices_product_report($product_data);
						
						$new_array[0] = $testval->timestamp_value;
						
						foreach($options['search_code'] as $key => $value){
							if($value == 'reference'){
								$new_array[1] = $product_data['reference'];
							}
							if($value == 'internal_code'){
								$new_array[1] = $product_data['ID'];
							}
							if($value == 'manufacturers_code'){
								$new_array[1] = $product_data['manufacturers'];
							}
						}
						
						$new_array[2] = $product_prices['scale'];
						$new_array[3] = $product_prices['price_initial'];
						$new_array[4] = $product_prices['price_finally'];
						$new_array[5] = $total_stock;
						$array_data[] = $new_array;
						$new_array = $default_array_data;
					}
					
				}
				header('Content-Type: application/excel');
				header('Content-Disposition: attachment; filename="Products_Summary_'.date_i18n('Ymdhi').'.csv"');
				$out = fopen('php://output', 'w');
				foreach ($array_data as $k => $arr) {
					fputcsv($out, $arr, ';');
				}
				fclose($out);
				//print_r($array_data);
			}
			
			/**
				* Static function print_pdf used to print the report on PDF.
				* @access public
				* @return void
				* @since 0.6
			*/
			public static function fkrt_stock_in_list_print_pdf() {
				
				//$request = wp_parse_args($_REQUEST, apply_filters('report_default_requests_values', array()));
				$request = wp_parse_args($_REQUEST, self::fktr_default_request());
				
				$ranges = array();
				$ranges['from'] = 0;
				$ranges['to'] = 0;
				/*
					* This filter can be used to create or update timestamp ranges.
					* $ranges will be used by get_object_chart()
				*/
				$total_html_print = '';

				$setting_system = get_option('fakturo_system_options_group', false);
				$currencyDefault = get_fakturo_term($setting_system['currency'], 'fktr_currencies');
				/* 		var_export($currencyDefault);
				die(); */
				
				
				if (is_wp_error($currencyDefault)) {
					$total_html_print .= '<p>'.__( 'Account Movements needs the default currency on system settings.', 'fakturo' ).'</p>';
					echo $total_html_print;
					return true;
				}

				$total_html_print .= '
				<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
							<title>'. "Products_Summary_".date_i18n("Ymdhi").".pdf" . '</title>
							<link href="https://fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
						
							<style>
								* {
								margin:0;
								padding:0;
								font-family: "Muli" !important;
								}
								
								html{
								font-family: "Muli" !important;
								padding: 0;
								}
								body{
								padding: 10px;
								background-color: #fff;
								}
								
								.pagenum:before { content: counter(page); }
								
								.new-section{
								margin-top: 10px;
								margin-bottom: 10px;
								font-size: 12px;
								color: #1f1f1f;
								}
								.new-section .title {
								font-weight: 500;
								text-transform: uppercase;
								margin-top: 20px;
								padding: 8px 10px 10px 20px;
								background-color: #5b5b5b;
								color: #fff;
								}
								
								
								table{
								width: 100%;
								}
								
								table tbody tr:nth-child(even) {
								background-color: #f5f5f5;
								}
								
								table thead tr th{
								padding: 0px 5px;
								font-size: 14px;
								font-weight: 1;
								}
								
								table thead tr th:last-child{
								text-align: right;
								}
								
								table tr td{
								padding: 5px;
								vertical-align: top;
								border-top: 1px solid #b9b9b9;
								}
								
								table tr td:last-child{
								text-align: right;
								}
								
								table tr:last-child td{
								border-bottom: 1px solid #b9b9b9;
								}
								
								table tr.detail-section td {
								border: none;
								padding-top: 8px;
								padding-bottom: 8px;
								font-size: 12px;
								}
								table thead tr th{
								padding-top: 10px;
								}
							</style>
						</head>
						<body>
				';
				
				$total_html_print .= '<div class="new-section"><div class="title"><h3>'.__('Inventory', 'fakturo' ).'</h3></div></div>';
				
				$objects_client = reports::get_objects($request, $ranges,  $limit='');
				
				$html_objects = '';
				if (!empty($objects_client)) {
					
					$options = get_option('fakturo_system_options_group');
					
					$selectSearchCode = array();
					$selectSearchCode['reference'] = __( 'Reference', 'fakturo' );
					$selectSearchCode['internal_code'] = __( 'Internal code', 'fakturo' );
					$selectSearchCode['manufacturers_code'] = __( 'Manufacturers code', 'fakturo' );							
					$selectSearchCode = apply_filters('fktr_search_code_array', $selectSearchCode);
					
					//$html_objects .= '<table class="wp-list-table widefat fixed striped posts" id="table_report_product">
					$html_objects .= '
					
					
					<table cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th>'.__('Name', 'fakturo').'</th>';

								foreach ($selectSearchCode as $key => $txt) {
									$html_objects .=  (array_search($key, $options['search_code'])!==false) ? '<th >'.$txt.'</th>' : '';
								}

								$html_objects .= '<th >'.__('Scale', 'fakturo' ).'</th>
								<th >'.__('Cost', 'fakturo' ).'</th>
								<th >'.__('Price', 'fakturo').'</th>
								<th >'.__('Inventory', 'fakturo').'</th>
							</tr>
						</thead>
						<tbody id="the-list">';
					
					foreach ($objects_client as $obj => $testval) {
						
						$product_data = fktrPostTypeProducts::get_product_data($testval->ID);
						
						// Get Stock
						$total_stock = stock_products_report::get_stock_product_report($product_data);
						
						// Get Prices
						$product_prices = stock_products_report::get_prices_product_report($product_data);
						
						$obj_link = admin_url('post.php?post='.$testval->ID.'&action=edit');
						
						$html_objects .= '
						<tr class="detail-section">
							<td>
								<a href="'.$obj_link.'">'.$testval->timestamp_value.'</a>
							</td>
							';
							foreach($options['search_code'] as $key => $value){
								if($value == 'reference'){
									$html_objects .= '<td>'.$product_data['reference'].'</td>';
								}
								if($value == 'internal_code'){
									$html_objects .= '<td>'.$product_data['ID'].'</td>';
								}
								if($value == 'manufacturers_code'){
									$html_objects .= '<td>'.$product_data['manufacturers'].'</td>';
								}
							}
							$html_objects .= '
							<td>'.$product_prices['scale'].'</td>
							<td align="right">' . $currencyDefault->symbol . ' '. $product_prices['price_initial'].'</td>
							<td align="right">' . $currencyDefault->symbol . ' '. $product_prices['price_finally'].'</td>
							<td align="center">'. $total_stock .'</td>
						</tr>';
					}
					
					
					$html_objects .= '</tbody>
					</table>
					<div class="clear"></div>';
					}else{
					$html_objects = '<div style="clear: both;"><h2>No results with this filters</h2></div>';
				}
				
				$total_html_print .= '
				<div style="width: 100%; float: left;">
				'.($html_objects).'
				
				</div>';
				$total_html_print .= '</body>
				</html>';
				
				$pdf = fktr_pdf::getInstance();
				
				$pdf ->set_option('isRemoteEnabled', true);
				$pdf ->set_option('isHtml5ParserEnabled', true);
				$pdf ->set_option("isPhpEnabled", true);

				$pdf ->set_paper("A4", "portrait");
				$pdf ->load_html(utf8_decode($total_html_print));
				$pdf ->render();
				$pdf ->stream('Products_Summary_'.date_i18n('Ymdhi').'.pdf', array('Attachment'=>0));
				
			}
			
		}
		
	}
	$fkrt_stock_in_list_proccess = new fkrt_stock_in_list_proccess();
	
	
?>
