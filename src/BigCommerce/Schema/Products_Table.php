<?php


namespace BigCommerce\Schema;


class Products_Table extends Table_Maker {
	const NAME = 'bc_products';

	protected $schema_version = '0.1.1';

	protected $tables = [ self::NAME ];

	protected function get_table_definition( $table ) {

		global $wpdb;
		$table_name       = $wpdb->$table;
		$charset_collate  = $wpdb->get_charset_collate();
		switch ( $table ) {
			case self::NAME:
				return "CREATE TABLE {$table_name} (
				        post_id BIGINT(20) unsigned NOT NULL,
				        bc_id BIGINT(20) unsigned NOT NULL,
				        is_featured TINYINT DEFAULT 0,
				        sku VARCHAR(255),
				        upc VARCHAR(255),
				        mpn VARCHAR(255),
				        gtin VARCHAR(255),
				        weight DOUBLE,
				        width DOUBLE,
				        depth DOUBLE,
				        height DOUBLE,
				        price DOUBLE,
				        cost_price DOUBLE,
				        retail_price DOUBLE,
				        sale_price DOUBLE,
				        product_tax_code VARCHAR(255) DEFAULT '',
				        calculated_price DOUBLE,
				        inventory_level BIGINT,
				        inventory_tracking ENUM('none', 'product', 'variant'),
				        PRIMARY KEY  (post_id),
				        KEY bc_id (bc_id),
				        KEY is_featured (is_featured),
				        KEY sku (sku(50)),
				        KEY calculated_price (calculated_price),
				        KEY sale_price (sale_price),
				        KEY inventory (inventory_tracking,inventory_level)
				        ) $charset_collate";
		}
	}
}