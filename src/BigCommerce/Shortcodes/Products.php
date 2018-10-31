<?php


namespace BigCommerce\Shortcodes;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Post_Types\Product\Query_Mapper;
use BigCommerce\Rest\Shortcode_Controller;
use BigCommerce\Templates\Product_Card;
use BigCommerce\Templates\Product_Card_Preview;
use BigCommerce\Templates\Product_Shortcode_Grid;
use BigCommerce\Templates\Product_Shortcode_Single;
use BigCommerce\Templates\Product_Shortcode_Single_Preview;

class Products implements Shortcode {
	const NAME = 'bigcommerce_product';

	/**
	 * @var Shortcode_Controller
	 */
	private $shortcode_rest_controller;

	public static function default_attributes() {
		return [
			'id'       => '', // BigCommerce product IDs, comma delimited
			'post_id'  => '', // WordPress post IDs, comma delimited
			'sku'      => '', // BigCommerce SKUs, comma delimited
			'category' => '', // Product Category slugs, comma delimited
			'brand'    => '', // Brand slugs, comma delimited
			'featured' => 0, // 1 to limit to featured products
			'sale'     => 0, // 1 to limit to sale products
			'recent'   => 0, // 1 to limit to products imported in the last 48 hours
			'search'   => '', // search titles, BigCommerce IDs, or SKUs
			'paged'    => 1, // 1 to enable pagination
			'per_page' => 0, // number of products to show at a time
			'order'    => 'ASC', // ASC or DESC,
			'orderby'  => 'title', // title, date, or any other arg accepted by WP_Query
			'ajax'     => 0, // internal use: set to 1 for ajax pagination requests
			'preview'  => 0, // internal use: set to 1 to remove interactive elements
		];
	}

	public function __construct( $shortcode_controller ) {
		$this->shortcode_rest_controller = $shortcode_controller;
	}

	public function render( $attr, $instance ) {
		$attr = shortcode_atts( self::default_attributes(), $attr, self::NAME );

		$mapper     = new Query_Mapper();
		$query_args = $mapper->map_shortcode_args_to_query( $attr );

		$current_post = get_the_ID();
		if ( $current_post ) {
			$query_args[ 'post__not_in' ] = [ $current_post ];
		}

		$query   = new \WP_Query( $query_args );
		$results = $query->posts;

		$products = array_map( function ( $post_id ) {
			return new Product( $post_id );
		}, $results );

		$count = count( $products );
		if ( $count < 1 ) {
			return ''; // TODO: something nicer?
		}

		if ( count( $products ) > 1 || $attr[ 'paged' ] > 1 ) {
			$cards = array_map( function ( Product $product ) use ( $attr ) {
				if ( empty( $attr[ 'preview' ] ) ) {
					$card = Product_Card::factory( [
						Product_Card::PRODUCT => $product,
					] );
				} else {
					$card = Product_Card_Preview::factory( [
						Product_Card::PRODUCT => $product,
					] );
				}

				return $card->render();
			}, $products );
			$grid  = Product_Shortcode_Grid::factory( [
				Product_Shortcode_Grid::CARDS         => $cards,
				Product_Shortcode_Grid::NEXT_PAGE_URL => $this->next_page_url( $attr, $query->max_num_pages ),
				Product_Shortcode_Grid::WRAP          => intval( $attr[ 'ajax' ] ) !== 1,
			] );

			return $grid->render();
		} else {
			if ( empty( $attr[ 'preview' ] ) ) {
				$single = Product_Shortcode_Single::factory( [
					Product_Shortcode_Single::PRODUCT => reset( $products ),
				] );
			} else {
				$single = Product_Shortcode_Single_Preview::factory( [
					Product_Shortcode_Single::PRODUCT => reset( $products ),
				] );
			}

			return $single->render();
		}
	}

	private function next_page_url( array $attr, $max_pages ) {
		if ( empty( $attr[ 'paged' ] ) ) {
			return '';
		}
		$page = (int) $attr[ 'paged' ];
		if ( $page >= $max_pages ) {
			return '';
		}

		$base_url = trailingslashit( $this->shortcode_rest_controller->get_base_url() ) . 'html';

		$attr[ 'paged' ] = $page + 1;
		$attr[ 'ajax' ]  = 1;

		return add_query_arg( array_filter( $attr ), $base_url );
	}
}