<?php


namespace BigCommerce\Api;

/**
 * Handle tax data requests.
 *
 * This class provides methods for interacting with the BigCommerce API to retrieve tax class data.
 *
 * @package BigCommerce\Api
 */
class Tax_Class_Api extends v2ApiAdapter {
    
    /**
     * Retrieve tax classes from BigCommerce.
     *
     * This method fetches a collection of tax classes available in the BigCommerce store.
     *
     * @return array A list of tax classes.
     */
	public function get_tax_classes() {
		return $this->getCollection( '/tax_classes', 'TaxClass' );
	}
}
