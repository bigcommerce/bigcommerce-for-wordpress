<?php


namespace BigCommerce\Schema;


class Variants_Table extends Table_Maker  {
	const NAME = 'bc_variants';

	protected $schema_version = '0.1';

	protected $tables = [ self::NAME ];

	protected function get_table_definition( $table ) {

		global $wpdb;
		$table_name       = $wpdb->$table;
		$charset_collate  = $wpdb->get_charset_collate();
		switch ( $table ) {
			case self::NAME:
				return "CREATE TABLE {$table_name} (
				        variant_id BIGINT(20) unsigned NOT NULL,
				        bc_id BIGINT(20) unsigned NOT NULL,
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
				        calculated_price DOUBLE,
				        purchasing_disabled TINYINT DEFAULT 0,
				        inventory_level BIGINT,
				        PRIMARY KEY  (variant_id),
				        KEY bc_id (bc_id),
				        KEY sku (sku(50)),
				        KEY calculated_price (calculated_price),
				        KEY purchasing_disabled (purchasing_disabled),
				        KEY inventory_level (inventory_level)
				        ) $charset_collate";
		}
	}
}