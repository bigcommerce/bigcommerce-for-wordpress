<?php


namespace BigCommerce\Shortcodes;


use BigCommerce\Customizer\Sections\Catalog;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Rest\Shortcode_Controller;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Templates\Product_Card;
use BigCommerce\Templates\Product_Card_Preview;
use BigCommerce\Templates\Product_Shortcode_Grid;
use BigCommerce\Templates\Product_Shortcode_Single;
use BigCommerce\Templates\Product_Shortcode_Single_Preview;

class Products implements Shortcode {
	const NAME             = 'bigcommerce_product';

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


		$query_args = $this->attributes_to_query_args( $attr );

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
					$card = new Product_Card( [
						Product_Card::PRODUCT => $product,
					] );
				} else {
					$card = new Product_Card_Preview( [
						Product_Card::PRODUCT => $product,
					]);
				}

				return $card->render();
			}, $products );
			$grid  = new Product_Shortcode_Grid( [
				Product_Shortcode_Grid::CARDS         => $cards,
				Product_Shortcode_Grid::NEXT_PAGE_URL => $this->next_page_url( $attr, $query->max_num_pages ),
				Product_Shortcode_Grid::WRAP          => intval( $attr[ 'ajax' ] ) !== 1,
			] );

			return $grid->render();
		} else {
			if ( empty( $attr[ 'preview' ] ) ) {
				$single = new Product_Shortcode_Single( [
					Product_Shortcode_Single::PRODUCT => reset( $products ),
				] );
			} else {
				$single = new Product_Shortcode_Single_Preview( [
					Product_Shortcode_Single::PRODUCT => reset( $products ),
				] );
			}

			return $single->render();
		}
	}

	/**
	 * @param array $attr
	 *
	 * @return array Args to pass to a WP Query based on the shortcode attributes
	 */
	private function attributes_to_query_args( array $attr ) {
		$query_args = [
			'post_type'      => Product::NAME,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'order'          => strtoupper( $attr[ 'order' ] ) === 'DESC' ? 'DESC' : 'ASC',
			'orderby'        => $attr[ 'orderby' ],
			'posts_per_page' => ( absint( $attr[ 'per_page' ] ) == 0 ) ? $this->per_page_default() : absint( $attr[ 'per_page' ] ),
		];

		if ( ! empty( $attr[ 'post_id' ] ) ) {
			$post_ids = array_filter( array_map( 'absint', explode( ',', $attr[ 'post_id' ] ) ) );
			if ( ! empty( $post_ids ) ) {
				$query_args[ 'post__in' ] = $post_ids;
			}
		}

		if ( ! empty( $attr[ 'id' ] ) ) {
			$bc_ids = array_filter( array_map( 'absint', explode( ',', $attr[ 'id' ] ) ) );
			if ( ! empty( $bc_ids ) ) {
				$query_args[ 'bigcommerce_id__in' ] = $bc_ids;
			}
		}

		if ( ! empty( $attr[ 'sku' ] ) ) {
			$skus = array_filter( explode( ',', $attr[ 'sku' ] ) );
			if ( ! empty( $skus ) ) {
				$query_args[ 'bigcommerce_sku__in' ] = $skus;
			}
		}

		if ( ! empty( $attr[ 'category' ] ) ) {
			$categories = array_filter( explode( ',', $attr[ 'category' ] ) );
			if ( ! empty( $categories ) ) {
				$query_args[ 'tax_query' ][] = [
					'taxonomy'         => Product_Category::NAME,
					'field'            => 'slug',
					'terms'            => $categories,
					'include_children' => false,
					'operator'         => 'IN',
				];
			}
		}

		if ( ! empty( $attr[ 'brand' ] ) ) {
			$brands = array_filter( explode( ',', $attr[ 'brand' ] ) );
			if ( ! empty( $brands ) ) {
				$query_args[ 'tax_query' ][] = [
					'taxonomy'         => Brand::NAME,
					'field'            => 'slug',
					'terms'            => $brands,
					'include_children' => false,
					'operator'         => 'IN',
				];
			}
		}

		$flags = [];
		if ( ! empty( $attr[ 'featured' ] ) ) {
			$flags[] = Flag::FEATURED;
		}
		if ( ! empty( $attr[ 'sale' ] ) ) {
			$flags[] = Flag::SALE;
		}
		if ( ! empty( $flags ) ) {
			$query_args[ 'tax_query' ][] = [
				'taxonomy' => Flag::NAME,
				'field'    => 'slug',
				'terms'    => $flags,
				'operator' => 'AND',
			];
		}

		if ( ! empty( $attr[ 'search' ] ) ) {
			$query_args[ 's' ] = $attr[ 'search' ];
		}

		if ( ! empty( $attr[ 'recent' ] ) ) {
			$query_args[ 'date_query' ] = [
				/**
				 * This filter is documented in src/BigCommerce/Rest/Products_Controller.php
				 */
				'after'     => sprintf( '%d days ago', apply_filters( 'bigcommerce/query/recent_days', 2 ) ),
				'column'    => 'post_modified',
				'inclusive' => true,
			];
		}

		if ( ! empty( $attr[ 'paged' ] ) ) {
			$query_args[ 'paged' ] = (int) $attr[ 'paged' ];
		}

		return apply_filters( 'bigcommerce/shortcode/products/query_args', $query_args, $attr );
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

	private function per_page_default() {
		$default = get_option( Catalog::PER_PAGE, Catalog::PER_PAGE_DEFAULT );
		return absint( $default ) ?: Catalog::PER_PAGE_DEFAULT;
	}
}