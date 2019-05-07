<?php


namespace BigCommerce\Schema;

/**
 * Class Products_Table
 *
 * @deprecated since 3.0. Product data stored in post meta.
 */
class Products_Table extends Table_Maker {
	const NAME = 'bc_products';

	protected $schema_version = '3.0-dev1';

	protected $tables = [ self::NAME ];

	/**
	 * Override for the table registration,
	 * as it was removed in version 3.0
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