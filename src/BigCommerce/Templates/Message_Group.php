<?php


namespace BigCommerce\Templates;


class Message_Group extends Controller {
	const MESSAGES = 'messages';
	const TYPE    = 'type';

	const NOTICE  = 'notice';
	const ERROR   = 'error';
	const WARNING = 'warning';
	const SUCCESS = 'success';

	protected $template = 'components/message-group.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::MESSAGES => [],
			self::TYPE    => self::NOTICE,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::MESSAGES => $this->options[ self::MESSAGES ],
			self::TYPE    => $this->options[ self::TYPE ],
		];
	}

}