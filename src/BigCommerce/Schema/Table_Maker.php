<?php


namespace BigCommerce\Schema;

/**
 * Class Table_Maker
 *
 * Utility class for creating/updating custom tables
 */
abstract class Table_Maker extends Schema {

	/**
	 * @var array Names of tables that will be registered by this class
	 */
	protected $tables = [];

	/**
	 * Register tables with WordPress, and create them if needed
	 *
	 * @return void
	 *
	 * @action init
	 */
	public function register_tables() {
		global $wpdb;

		// make WP aware of our tables
		foreach ( $this->tables as $table ) {
			$wpdb->tables[] = $table;
			$name           = $this->get_full_table_name( $table );
			$wpdb->$table   = $name;
		}

		// create the tables
		if ( $this->schema_update_required() ) {
			foreach ( $this->tables as $table ) {
				$this->update_table( $table );
			}
			$this->mark_schema_update_complete();
		}
	}

	/**
	 * @param string $table The name of the table
	 *
	 * @return string The CREATE TABLE statement, suitable for passing to dbDelta
	 */
	abstract protected function get_table_definition( $table );


	/**
	 * Update the schema for the given table
	 *
	 * @param string $table The name of the table to update
	 *
	 * @return void
	 */
	private function update_table( $table ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$definition = $this->get_table_definition( $table );
		if ( $definition ) {
			$updated = dbDelta( $definition );
			foreach ( $updated as $updated_table => $update_description ) {
				if ( strpos( $update_description, 'Created table' ) === 0 ) {
					do_action( 'bigcommerce/table_maker/created_table', $updated_table, $table );
				}
			}
		}
	}

	/**
	 * @param string $table
	 *
	 * @return string The full name of the table, including the
	 *                table prefix for the current blog
	 */
	protected function get_full_table_name( $table ) {
		return $GLOBALS[ 'wpdb' ]->prefix . $table;
	}
}