<?php


namespace BigCommerce\Templates;



class Page_Wrapper extends Controller {
	const CONTENT     = 'content';

	protected $template = 'components/page-wrapper.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CONTENT     => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CONTENT     => $this->options[ self::CONTENT ],
		];
	}

}