<?php

if (!class_exists('fkrt_stock_in_list_proccess') ) {

    class fkrt_stock_in_list_proccess {
        
		function __construct() {
			add_filter('manage_fktr_product_posts_columns', array(__CLASS__, 'columns_stock'));
			add_filter('manage_fktr_product_posts_custom_column', array(__CLASS__, 'manage_columns_stock'), 10, 2);
		}

		public static function columns_stock($columns) {

			$column_reference_pos = 6;
			$column_reference = ['stock' => __('Stock', 'fakturo')];
			// 6nd column
			$columns = array_slice($columns, 0, $column_reference_pos, true) + $column_reference + array_slice($columns, $column_reference_pos, NULL, true);
			return $columns;
		}

		public static function manage_columns_stock($column, $post_id) {
			$product_data = fktrPostTypeProducts::get_product_data($post_id);

			$terms_stock = get_fakturo_terms(array(
				'taxonomy' => 'fktr_locations',
				'hide_empty' => false,
			));

			$total_stock = 0;
			foreach ($terms_stock as $stock) {
				$total_stock = $total_stock + (isset($product_data['stocks'][$stock->term_id]) ? $product_data['stocks'][$stock->term_id] : 0 );
			}
			if($total_stock == 0){
				echo 'Agotado ('.$total_stock.')';
			}else{
				echo 'Hay existencia ('.$total_stock.')';
			}
		}
			
    }
}
$fkrt_stock_in_list_proccess = new fkrt_stock_in_list_proccess();


?>
