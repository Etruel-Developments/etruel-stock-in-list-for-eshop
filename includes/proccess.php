<?php

if (!class_exists('fkrt_stock_in_list_proccess') ) {

    class fkrt_stock_in_list_proccess {
        
		function __construct() {
			add_filter('manage_fktr_product_posts_columns', array(__CLASS__, 'columns'));
			add_filter('manage_fktr_product_posts_custom_column', array(__CLASS__, 'manage_columns'), 10, 2);
			add_filter('manage_edit-fktr_product_sortable_columns', array(__CLASS__, 'sortable_columns'));

			add_action('parse_query', array(__CLASS__, 'column_orderby'));
		}

		public static function sortable_columns($columns) {
			$custom = array(
				'Stocks' => 'reference',
			);
			return wp_parse_args($custom, $columns);
		}

		public static function column_orderby($query) {
			global $pagenow, $post_type;
			$orderby = $query->get('orderby');
			if ('edit.php' != $pagenow || empty($orderby) || $post_type != 'fktr_product')
				return;
			switch ($orderby) {
				case 'reference':
					$meta_group = array('key' => 'reference', 'type' => 'string');
					$query->set('meta_query', array('sort_column' => 'reference', $meta_group));
					$query->set('meta_key', 'reference');
					$query->set('orderby', 'meta_value');

					break;

				default:
					break;
			}
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


					if($total_stock == 0){
						echo '<span style="color:'.$data_color_stock['out_stock'].';">Agotado ('.$total_stock.') </span>';
					}else if($total_stock >= 1 && $total_stock <= 5 ){
							echo '<span style="color:'.$data_color_stock['low_stock'].';">Hay existencia ('.$total_stock.') </span>';
					}else {
						echo '<span style="color:'.$data_color_stock['in_stock'].';">Hay existencia ('.$total_stock.') </span>';
					}
				
				break;
			}			
		}
			
    }
}
$fkrt_stock_in_list_proccess = new fkrt_stock_in_list_proccess();


?>
