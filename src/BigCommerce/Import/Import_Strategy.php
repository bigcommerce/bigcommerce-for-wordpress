<?php

namespace BigCommerce\Import;

interface Import_Strategy {
	const VERSION = '3.21.0';

	/**
	 * @return int The imported post ID
	 */
	public function do_import();
}
