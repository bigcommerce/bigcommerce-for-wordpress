<?php


namespace BigCommerce\Templates;


class Message extends Controller {
	const CONTENT = 'content';
	const TYPE    = 'type';

	const NOTICE  = 'notice';
	const ERROR   = 'error';
	const WARNING = 'warning';
	const SUCCESS = 'success';

	protected $template = 'components/message.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CONTENT => '',
			self::TYPE    => self::NOTICE,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CONTENT => $this->sanitize_content( $this->options[ self::CONTENT ] ),
			self::TYPE    => $this->options[ self::TYPE ],
		];
	}

	private function sanitize_content( $content ) {
		return wp_kses_post( $content );
	}

}