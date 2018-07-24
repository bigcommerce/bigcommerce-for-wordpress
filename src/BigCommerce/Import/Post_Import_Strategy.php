<?php

namespace BigCommerce\Import;

interface Post_Import_Strategy {
	const VERSION = '0.9.1';

	/**
	 * @return int The imported post ID
	 */
	public function do_import();
}
