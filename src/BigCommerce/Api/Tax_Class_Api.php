<?php


namespace BigCommerce\Api;

/**
 * Class Tax_Class_Api
 *
 * Handle tax data requests
 *
 * @package BigCommerce\Api
 */
class Tax_Class_Api extends v2ApiAdapter {
    /**
     * Retrieve tax classes from BC
     *
     * @return array
     */
	public function get_tax_classes() {
		return $this->getCollection( '/tax_classes', 'TaxClass' );
	}
}
