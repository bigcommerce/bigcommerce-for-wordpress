<?php

namespace BigCommerce\Import;

interface Import_Strategy {
	const VERSION = '0.13.0';

	/**
	 * @return int The imported post ID
	 */
	public function do_import();
}
