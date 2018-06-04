<?php


namespace BigCommerce\Import;


class Product_Ignorer implements Post_Import_Strategy {
	private $data;
	private $post_id;
	private $api;

	public function __construct( $data, $api, $post_id ) {
		$this->data    = $data;
		$this->post_id = $post_id;
		$this->api     = $api;
	}

	public function do_import() {
		do_action( 'bigcommerce/import/product/skipped', $this->post_id, $this->data, $this->api );

		return $this->post_id;
	}
}