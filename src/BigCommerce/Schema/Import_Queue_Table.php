<?php


namespace BigCommerce\Schema;


class Import_Queue_Table extends Table_Maker {
	const NAME = 'bc_import_queue';

	protected $schema_version = '0.6.0';

	protected $tables = [ self::NAME ];

	protected function get_table_definition( $table ) {

		global $wpdb;
		$table_name       = $wpdb->$table;
		$charset_collate  = $wpdb->get_charset_collate();
		switch ( $table ) {
			case self::NAME:
				return "CREATE TABLE {$table_name} (
				        bc_id BIGINT(20) unsigned NOT NULL,
				        date_modified DATETIME NOT NULL,
				        import_action ENUM( 'update', 'delete', 'ignore' ) NOT NULL,
				        date_created DATETIME NOT NULL,
				        priority INT(10) unsigned NOT NULL DEFAULT 0,
				        attempts INT(10) unsigned NOT NULL DEFAULT 0,
				        last_attempt DATETIME,
				        PRIMARY KEY  (bc_id),
				        KEY date_modified (date_modified),
				        KEY import_action (import_action),
				        KEY attempts (attempts,priority)
				        ) $charset_collate";
		}
	}
}