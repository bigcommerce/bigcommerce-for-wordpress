<?php


namespace BigCommerce\Schema;


abstract class Schema {
	/**
	 * @var int Increment this value to trigger a schema update
	 */
	protected $schema_version = 0;

	/**
	 * Determine if the database schema is out of date
	 * by comparing the integer found in $this->schema_version
	 * with the option set in the WordPress options table
	 *
	 * @return bool
	 */
	protected function schema_update_required() {
		$option_name         = 'schema-' . static::class;
		$version_found_in_db = get_option( $option_name, 0 );

		return version_compare( $version_found_in_db, $this->schema_version, '<' );
	}

	/**
	 * Update the option in WordPress to indicate that
	 * our schema is now up to date
	 *
	 * @return void
	 */
	protected function mark_schema_update_complete() {
		$option_name = 'schema-' . static::class;

		// work around race conditions and ensure that our option updates
		$value_to_save = (string) $this->schema_version . '.0.' . time();

		update_option( $option_name, $value_to_save );
	}
}