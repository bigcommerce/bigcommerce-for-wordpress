<?php


namespace BigCommerce\Api;


class Tax_Class_Api extends v2ApiAdapter {
	public function get_tax_classes() {
		return $this->getCollection( '/tax_classes', 'TaxClass' );
	}
}