<?php


namespace BigCommerce\Templates;


class Message extends Controller {
	const CONTENT    = 'content';
	const TYPE       = 'type';
	const KEY        = 'key';
	const ATTRIBUTES = 'attributes';

	const NOTICE  = 'notice';
	const ERROR   = 'error';
	const WARNING = 'warning';
	const SUCCESS = 'success';

	protected $template = 'components/message.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CONTENT    => '',
			self::TYPE       => self::NOTICE,
			self::KEY        => '',
			self::ATTRIBUTES => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CONTENT    => $this->sanitize_content( $this->options[ self::CONTENT ] ),
			self::TYPE       => $this->options[ self::TYPE ],
			self::KEY        => $this->options[ self::KEY ],
			self::ATTRIBUTES => $this->build_attribute_string( $this->options[ self::ATTRIBUTES ] ),
		];
	}

	private function sanitize_content( $content ) {
		return wp_kses_post( $content );
	}

}