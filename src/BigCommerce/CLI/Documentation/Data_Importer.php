<?php


namespace BigCommerce\CLI\Documentation;


use WP_Parser\Importer;

/**
 * Extends WP_Parser's Importer class to modify the import behavior for class methods and hooks.
 *
 * @package BigCommerce\CLI\Documentation
 */
class Data_Importer extends Importer {

    /**
     * Create a post for a class method, selectively importing hooks for protected or private methods.
     *
     * @param array $data           The method data to import.
     * @param int   $parent_post_id Optional; post ID of the parent (class) this
     *                              method belongs to. Defaults to zero (no parent).
     * @param bool  $import_ignored Optional; if true, functions marked `@ignore`
     *                              will also be imported. Defaults to false.
     *
     * @return bool|int Post ID of the method if successfully imported, false otherwise.
     */
	protected function import_method( array $data, $parent_post_id = 0, $import_ignored = false ) {
		if ( in_array( $data[ 'visibility' ], [ 'private', 'protected' ] ) ) {
			// import the hooks
			if ( ! empty( $data['hooks'] ) ) {
				foreach ( $data['hooks'] as $hook ) {
					$this->import_hook( $hook, 0, $import_ignored );
				}
			}
			// but not the method itself
			return false;
		}
		return parent::import_method( $data, $parent_post_id, $import_ignored );
	}
}