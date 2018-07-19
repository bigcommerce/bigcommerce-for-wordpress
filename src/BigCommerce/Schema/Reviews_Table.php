<?php


namespace BigCommerce\Schema;


class Reviews_Table extends Table_Maker {
	const NAME = 'bc_reviews';

	protected $schema_version = '0.1.6';

	protected $tables = [ self::NAME ];

	protected function get_table_definition( $table ) {

		global $wpdb;
		$table_name       = $wpdb->$table;
		$charset_collate  = $wpdb->get_charset_collate();
		switch ( $table ) {
			case self::NAME:
				return "CREATE TABLE {$table_name} (
				        review_id BIGINT(20) unsigned NOT NULL,
				        post_id BIGINT(20) unsigned NOT NULL,
				        bc_id BIGINT(20) unsigned NOT NULL,
				        title VARCHAR(255) NOT NULL DEFAULT '',
				        content LONGTEXT NOT NULL DEFAULT '',
				        status ENUM( 'approved', 'disapproved', 'pending' ) NOT NULL,
				        rating TINYINT(1) unsigned NOT NULL DEFAULT 0,
				        author_email VARCHAR(255) NOT NULL DEFAULT '',
				        author_name VARCHAR(255) NOT NULL DEFAULT '',
				        date_reviewed DATETIME NOT NULL,
				        date_created DATETIME NOT NULL,
				        date_modified DATETIME NOT NULL,
				        PRIMARY KEY  (review_id),
				        KEY post_id (post_id),
				        KEY bc_id (bc_id),
				        KEY status_date (status, date_reviewed, post_id),
				        KEY rating (rating)
				        ) $charset_collate";
		}
	}
}