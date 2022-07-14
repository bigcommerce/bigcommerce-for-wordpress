<?php


namespace BigCommerce\Templates;


class Shipping_Methods extends Controller {

	const METHODS         = 'methods';
	const COUNTRIES       = 'countries';

	protected $template = 'components/cart/shipping-methods.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-shipping-methods' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-shipping-methods' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::METHODS => [],
		];

		return wp_parse_args( $options, $defaults );
    }

	protected function get_countries() {
		$countries = get_site_transient( self::COUNTRIES );

		if ( ! empty( $countries ) ) {
			return  $countries;
		}

		try {
			$result = apply_filters( 'bigcommerce/countries/data', [] );

			set_site_transient( self::COUNTRIES, $result, HOUR_IN_SECONDS );
			return $result;
		} catch (\Throwable $exception ) {
			return [];
		}
	}

    public function get_data() {
		return [
			self::METHODS   => $this->options[ self::METHODS ],
			self::COUNTRIES => $this->get_countries(),
		];
	}

}
