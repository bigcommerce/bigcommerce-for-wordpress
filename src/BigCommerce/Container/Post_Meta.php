<?php

namespace BigCommerce\Container;

use Pimple\Container;

class Post_Meta extends Provider {

	public function register( Container $container ) {
		$this->import( $container );
	}

	private function import( Container $container ) {

	}
}
