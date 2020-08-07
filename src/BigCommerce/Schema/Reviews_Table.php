<?php


namespace BigCommerce\Schema;


/**
 * Class Reviews_Table
 *
 * @deprecated since 4.0. Reviews are fetched dynamically with a cache stored in post meta
 */
class Reviews_Table extends Table_Maker {
	const NAME = 'bc_reviews';

	protected $schema_version = '4.0';

	protected $tables = [ self::NAME ];

	/**
	 * Override for the table registration,
	 * as it was removed in version 4.0
	 *
	 * @return void
	 */
	public function register_tables() {
		// drop the tables
		if ( $this->schema_update_required() ) {
			foreach ( $this->tables as $table ) {
				$this->drop_table( $table );
			}
			$this->mark_schema_update_complete();
		}
	}

	private function drop_table( $table ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$blog_table = $wpdb->prefix . $table;
		$wpdb->query( "DROP TABLE IF EXISTS `$blog_table`" );
	}

	protected function get_table_definition( $table ) {
		return '';
	}
}
