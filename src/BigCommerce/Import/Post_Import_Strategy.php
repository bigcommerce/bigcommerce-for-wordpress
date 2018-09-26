<?php

namespace BigCommerce\Import;

interface Post_Import_Strategy {
	const VERSION = '0.12.0';

	/**
	 * @return int The imported post ID
	 */
	public function do_import();
}
