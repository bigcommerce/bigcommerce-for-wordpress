<?php


namespace BigCommerce\Import;

/**
 * Class Cache_Cleanup
 *
 * Clears out values in cache that may be corrupted
 * due to long-running import processes
 */
class Cache_Cleanup {
	public function flush_caches() {
		if ( wp_using_ext_object_cache() ) {
			wp_cache_delete( 'alloptions', 'options' );
			wp_cache_delete( 'notoptions', 'options' );
		}
	}
}
