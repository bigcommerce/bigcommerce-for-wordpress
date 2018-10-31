<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections;
use BigCommerce\Post_Types\Product\Product;

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
			self::SKU         => $product->sku(),
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
		$component = Product_Price::factory( [
			Product_Price::PRODUCT => $product,
		] );

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

		/**
		 * Filter the number of product reviews to show per page.
		 *
		 * @param int $per_page The number of reviews to show per page
		 * @param int $post_id  The ID of the product post
		 */
		$per_page = absint( apply_filters( 'bigcommerce/products/reviews/per_page', 12, $product->post_id() ) );

		if ( empty( $per_page ) ) {
			return '';
		}

		$reviews       = $product->get_reviews( [
			'per_page' => $per_page,
		] );
		$total_reviews = $product->get_review_count();
		$total_pages   = empty( $total_reviews ) ? 0 : ceil( $total_reviews / $per_page );
		$next_page_url = $this->next_page_url( $product->post_id(), $per_page, 1, $total_pages );

		$reviews = array_map( function ( $review ) use ( $product ) {
			$controller = Review_Single::factory( array_merge( [
				Review_Single::PRODUCT => $product,
			], $review ) );

			return $controller->render();
		}, $reviews );

		$controller = Product_Reviews::factory( [
			Product_Reviews::PRODUCT       => $product,
			Product_Reviews::REVIEWS       => $reviews,
			Product_Reviews::NEXT_PAGE_URL => $next_page_url,
		] );

		return $controller->render();
	}

	private function next_page_url( $post_id, $per_page, $current_page, $max_pages ) {
		if ( $current_page >= $max_pages ) {
			return '';
		}

		$base_url = apply_filters( 'bigcommerce/product/reviews/rest_url', '', $post_id );

		$attr = [
			'per_page' => $per_page,
			'paged' => $current_page + 1,
			'ajax'  => 1,
		];

		$url = add_query_arg( $attr, $base_url );
		$url = wp_nonce_url( $url, 'wp_rest' );

		return $url;
	}

}