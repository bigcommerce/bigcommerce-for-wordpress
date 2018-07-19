<?php


namespace BigCommerce\Templates;


class Sub_Nav_Links extends Controller {
	const LINKS = 'links';

	protected $template = 'components/sub-nav-links.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::LINKS => [], // an array of associative arrays with 'url' and 'label' keys
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::LINKS => $this->links(),
		];
	}

	private function links() {
		$links = array_map( function ( $link ) {
			return wp_parse_args( $link, [ 'url' => '', 'label' => '', 'current' => false ] );
		}, $this->options[ self::LINKS ] );

		return array_filter( $links, function ( $link ) {
			return ( ! empty( $link[ 'url' ] ) && ! empty( $link[ 'label' ] ) );
		} );
	}

}