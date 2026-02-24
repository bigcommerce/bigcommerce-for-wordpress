<?php

namespace BigCommerce\Compatibility;

/**
 * Defines the contract for a spam checker, which can be used to determine whether a given content is considered spam.
 *
 * @package BigCommerce
 * @subpackage Compatibility
 */
interface Spam_Checker {

	/**
	 * Checks whether the given content is spam.
	 *
	 * @param array $content The content to check for spam, typically containing fields like author name, email, etc.
	 *
	 * @return boolean True if the content is considered spam, otherwise false.
	 */
	public function is_spam( array $content );

}
