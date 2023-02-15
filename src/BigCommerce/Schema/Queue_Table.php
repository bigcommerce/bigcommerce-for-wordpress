<?php

namespace BigCommerce\Schema;

/**
 * Class Queue_Table
 */
class Queue_Table extends Table_Maker {

	const NAME = 'bc_queue';

	const STATUS_DONE = 1;
	const STATUS_NEW  = 0;

	protected $schema_version = '1.0';

	protected $tables = [ self::NAME ];

	protected function get_table_definition( $table ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$blog_table = $wpdb->prefix . $table;

		return "CREATE TABLE if not exists `$blog_table` (
    		id INT NOT NULL AUTO_INCREMENT,
    		handler VARCHAR (255),
    		args MEDIUMTEXT,
    		status INT DEFAULT 0,
    		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    		PRIMARY KEY(id)
       );";
	}

}
