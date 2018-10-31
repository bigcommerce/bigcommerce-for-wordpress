<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Rating extends Controller {
	const PRODUCT    = 'product';
	const STARS      = 'stars';
	const PERCENTAGE = 'percentage';
	const COUNT      = 'review_count';
	const LINK       = 'link';

	protected $template = 'components/reviews/product-rating.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT => null,
			self::LINK    => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product    = $this->options[ self::PRODUCT ];
		$sum        = $product->reviews_rating_sum;
		$count      = $product->reviews_count;
		$percentage = $this->get_percentage( $sum, $count );
		$stars      = $this->get_stars( $sum, $count );
		$permalink  = $this->options[ self::LINK ];

		return [
			self::PRODUCT    => $product,
			self::STARS      => $stars,
			self::PERCENTAGE => $percentage,
			self::COUNT      => $count,
			self::LINK       => $permalink,
		];
	}

	private function get_percentage( $sum, $count ) {
		if ( $count < 1 ) {
			return 0;
		}

		return (int) ( $sum / $count * 20 );
	}

	private function get_stars( $sum, $count ) {
		if ( $count < 1 ) {
			return 0;
		}

		return $sum / $count;
	}


}