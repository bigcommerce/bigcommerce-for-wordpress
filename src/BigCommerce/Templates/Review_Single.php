<?php

namespace BigCommerce\Templates;

class Review_Single extends Controller {
	const PRODUCT = 'product';

	const REVIEW_ID  = 'review_id';
	const POST_ID    = 'post_id';
	const BC_ID      = 'bc_id';
	const TITLE      = 'title';
	const CONTENT    = 'content';
	const STATUS     = 'status';
	const RATING     = 'rating';
	const PERCENTAGE = 'percentage';
	const EMAIL      = 'author_email';
	const NAME       = 'author_name';
	const DATE       = 'date_reviewed';
	const TIMESTAMP  = 'timestamp';

	protected $template = 'components/reviews/review-single.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT   => null,
			self::REVIEW_ID => 0,
			self::BC_ID     => 0,
			self::TITLE     => '',
			self::CONTENT   => '',
			self::STATUS    => 'approved',
			self::RATING    => 0,
			self::EMAIL     => '',
			self::NAME      => '',
			self::DATE      => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = $this->options;

		$data[ self::DATE ]      = get_date_from_gmt( $this->options[ self::DATE ] );
		$data[ self::TIMESTAMP ] = strtotime( $data[ self::DATE ] );
		$data[ self::CONTENT ]   = $this->format_content( $this->options[ self::CONTENT ] );
		$data[ self::PERCENTAGE ] = $this->options[ self::RATING ] * 20;

		return $data;
	}

	private function format_content( $content ) {
		$content = wpautop( wptexturize( wp_strip_all_tags( $content ) ) );

		return $content;
	}

}