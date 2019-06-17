<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Flag\Flag;

class Product_Single extends Controller {
	const PRODUCT = 'product';

	const IMAGES      = 'images';
	const TITLE       = 'title';
	const PRICE       = 'price';
	const BRAND       = 'brand';
	const SKU         = 'sku';
	const RATING      = 'rating';
	const DESCRIPTION = 'description';
	const FORM        = 'form';
	const SPECS       = 'specs';
	const RELATED     = 'related';
	const REVIEWS     = 'reviews';

	protected $template = 'components/products/product-single.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-product-single' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-product-single' ];

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
			self::PRODUCT     => $product,
			self::TITLE       => $this->get_title( $product ),
			self::IMAGES      => $this->get_images( $product ),
			self::PRICE       => $this->get_price( $product ),
			self::RATING      => $this->get_rating( $product ),
			self::SKU         => $this->get_sku( $product ),
			self::BRAND       => $this->get_brand( $product ),
			self::DESCRIPTION => $this->get_description( $product ),
			self::FORM        => $this->get_form( $product ),
			self::SPECS       => $this->get_specs( $product ),
			self::RELATED     => $this->get_related( $product ),
			self::REVIEWS     => $this->get_reviews( $product ),
		];
	}

	protected function get_title( Product $product ) {
		$component = Product_Title::factory( [
			Product_Title::PRODUCT       => $product,
			Product_Title::USE_PERMALINK => false,
			Product_Title::HEADER_LEVEL  => 1,
		] );

		return $component->render();
	}

	protected function get_images( Product $product ) {
		$component = Product_Gallery::factory( [
			Product_Gallery::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_price( Product $product ) {
		if ( has_term( Flag::HIDE_PRICE, Flag::NAME, $product->post_id() ) ) {
			$component = Product_Hidden_Price::factory( [
				Product_Hidden_Price::PRODUCT => $product,
			] );
		} else {
			$component = Product_Price::factory( [
				Product_Price::PRODUCT => $product,
			] );
		}

		return $component->render();
	}

	protected function get_rating( Product $product ) {
		$component = Product_Rating::factory( [
			Product_Rating::PRODUCT => $product,
			Product_Rating::LINK    => get_the_permalink( $product->post_id() ) . '#bc-single-product__reviews',
		] );

		return $component->render();
	}

	protected function get_brand( Product $product ) {
		$component = Product_Brand::factory( [
			Product_Brand::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_description( Product $product ) {
		$component = Product_Description::factory( [
			Product_Description::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_form( Product $product ) {
		$component = Product_Form::factory( [
			Product_Form::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_specs( Product $product ) {
		$component = Product_Specs::factory( [
			Product_Specs::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_related( Product $product ) {
		$max_posts = absint( get_option( Sections\Product_Single::RELATED_COUNT, 4 ) );
		if ( $max_posts < 1 ) {
			return '';
		}

		$related_ids = $product->related_products( [
			'posts_per_page' => $max_posts,
		] );

		if ( empty( $related_ids ) ) {
			return '';
		}

		$related = array_map( function ( $post_id ) {
			$component = Related_Product::factory( [
				Related_Product::PRODUCT => new Product( $post_id ),
			] );

			return $component->render();
		}, $related_ids );

		$component = Related_Products_Grid::factory( [
			Related_Products_Grid::PRODUCTS => $related,
		] );

		return $component->render();
	}

	protected function get_reviews( Product $product ) {
		/**
		 * Filter whether to show product reviews for a product.
		 *
		 * @param bool $show    Whether to show reviews. Defaults to true (i.e., reviews are displayed)
		 * @param int  $post_id The ID of the product post
		 */
		if ( ! apply_filters( 'bigcommerce/product/reviews/show_reviews', true, $product->post_id() ) ) {
			return '';
		}

		$shortcode = sprintf( '[%s post_id="%s"]', \BigCommerce\Shortcodes\Product_Reviews::NAME, $product->post_id() );
		return do_shortcode( $shortcode );
	}

	protected function get_sku( Product $product ) {
		$component = Product_Sku::factory( [
			Product_Sku::PRODUCT         => $product,
		] );

		return $component->render();
	}
}