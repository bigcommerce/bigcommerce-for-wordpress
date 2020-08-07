<?php

namespace BigCommerce\Import;

interface Import_Strategy {
	const VERSION = '4.0.0';

	/**
	 * @return int The imported post ID
	 */
	public function do_import();
}
