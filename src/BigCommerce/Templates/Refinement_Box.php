<?php


namespace BigCommerce\Templates;


class Refinement_Box extends Controller {
	const NAME    = 'name';
	const VALUE   = 'value';
	const ACTION  = 'action';
	const CHOICES = 'choices';
	const LABEL   = 'label';
	const TYPE    = 'type';

	protected $template = 'components/catalog/refinement-box.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::NAME    => '',
			self::VALUE   => '',
			self::ACTION  => '',
			self::CHOICES => [],
			self::LABEL   => '',
			self::TYPE    => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::NAME    => $this->options[ self::NAME ],
			self::VALUE   => $this->options[ self::VALUE ],
			self::ACTION  => $this->options[ self::ACTION ],
			self::CHOICES => $this->options[ self::CHOICES ],
			self::LABEL   => $this->options[ self::LABEL ],
			self::TYPE    => ! empty( $this->options[ self::TYPE ] ) ? $this->options[ self::TYPE ] : 'sort',
		];
	}

}