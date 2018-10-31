<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Description extends Controller {
	const PRODUCT = 'product';
	const CONTENT = 'content';

	protected $template = 'components/products/product-description.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT => $product,
			self::CONTENT => $this->get_the_content( $product->post_id() ),
		];
	}

	private function get_the_content( $post_id ) {
		$backup_post       = isset( $GLOBALS[ 'post' ] ) ? $GLOBALS[ 'post' ] : null;
		$post              = get_post( $post_id );
		$GLOBALS[ 'post' ] = $post;
		setup_postdata( $post );

		ob_start();
		the_content();
		$content = ob_get_clean();

		$GLOBALS[ 'post' ] = $backup_post;
		wp_reset_postdata();

		return $content;
	}


}