<?php


namespace BigCommerce\Templates;


class Search_Box extends Controller {
	const NAME           = 'name';
	const VALUE          = 'value';
	const ACTION         = 'action';
	const PLACEHOLDER    = 'placeholder';
	const SEARCH_LABEL   = 'search_label';
	const BUTTON_CLASSES = 'button_classes';

	protected $template = 'components/catalog/search-box.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::NAME           => 's',
			self::VALUE          => '',
			self::ACTION         => '',
			self::PLACEHOLDER    => __( 'Search for', 'bigcommerce' ),
			self::SEARCH_LABEL   => __( 'Search', 'bigcommerce' ),
			self::BUTTON_CLASSES => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::NAME           => $this->options[ self::NAME ],
			self::VALUE          => $this->options[ self::VALUE ],
			self::ACTION         => $this->options[ self::ACTION ],
			self::PLACEHOLDER    => $this->options[ self::PLACEHOLDER ],
			self::SEARCH_LABEL   => $this->options[ self::SEARCH_LABEL ],
			self::BUTTON_CLASSES => $this->merge_classes( [ 'bc-product-archive__search-submit' ], $this->options[ static::BUTTON_CLASSES ], true ),
		];
	}

	protected function merge_classes( $default, $custom, $to_string = false ) {
		$classes = ! empty( $custom ) ? array_merge( $default, $custom ) : $default;

		if ( $to_string ) {
			$classes = implode( ' ', $classes );
		}

		return $classes;
	}

}