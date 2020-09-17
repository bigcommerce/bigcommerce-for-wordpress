<?php

namespace BigCommerce\Compatibility;

interface Spam_Checker {

    /**
	 * @param array $content
	 *
	 * @return boolean
	 */
	public function is_spam( array $content );

}
