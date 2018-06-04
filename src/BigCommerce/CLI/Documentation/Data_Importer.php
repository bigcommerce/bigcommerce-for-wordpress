<?php


namespace BigCommerce\CLI\Documentation;


use WP_Parser\Importer;

class Data_Importer extends Importer {


	/**
	 * Create a post for a class method.
	 *
	 * @param array $data           Method.
	 * @param int   $parent_post_id Optional; post ID of the parent (class) this
	 *                              method belongs to. Defaults to zero (no parent).
	 * @param bool  $import_ignored Optional; defaults to false. If true, functions
	 *                              marked `@ignore` will be imported.
	 * @return bool|int Post ID of this function, false if any failure.
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