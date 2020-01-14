<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Shortcodes;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Query_Mapper {
	/**
	 * @param array $args
	 *
	 * @return array Args to pass to a WP Query based on the shortcode attributes
	 */
	public function map_shortcode_args_to_query( $args ) {
		$query_args = [
			'post_type'      => Product::NAME,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'order'          => strtoupper( $args[ 'order' ] ) === 'DESC' ? 'DESC' : 'ASC',
			'orderby'        => $args[ 'orderby' ],
			'posts_per_page' => ( absint( $args[ 'per_page' ] ) == 0 ) ? $this->per_page_default() : absint( $args[ 'per_page' ] ),
		];

		if ( ! empty( $args[ 'post_id' ] ) ) {
			$post_ids = array_filter( array_map( 'absint', explode( ',', $args[ 'post_id' ] ) ) );
			if ( ! empty( $post_ids ) ) {
				$query_args[ 'post__in' ] = $post_ids;
			}
		}

		if ( ! empty( $args[ 'id' ] ) ) {
			$bc_ids = array_filter( array_map( 'absint', is_array( $args[ 'id' ] ) ? $args[ 'id' ] : explode( ',', $args[ 'id' ] ) ) );
			if ( ! empty( $bc_ids ) ) {
				$query_args[ 'bigcommerce_id__in' ] = $bc_ids;
			}
			if ( $query_args['orderby'] === 'post__in' ) {
				$query_args['orderby'] = '';
				$query_args['bc-sort'] = 'bigcommerce_id__in';
			}
		}

		if ( ! empty( $args[ 'sku' ] ) ) {
			$skus = array_filter( explode( ',', $args[ 'sku' ] ) );
			if ( ! empty( $skus ) ) {
				$query_args[ 'bigcommerce_sku__in' ] = $skus;
			}
		}

		if ( ! empty( $args[ 'category' ] ) ) {
			$categories = array_filter( explode( ',', $args[ 'category' ] ) );
			if ( ! empty( $categories ) ) {
				$query_args[ 'tax_query' ][] = [
					'taxonomy'         => Product_Category::NAME,
					'field'            => 'slug',
					'terms'            => $categories,
					'include_children' => false,
					'operator'         => 'IN',
				];
			}
		} elseif ( ! empty( $args[ Product_Category::NAME ] ) && is_array( $args[ Product_Category::NAME ] ) ) {
			// we have term IDs passed from the REST API
			$query_args[ 'tax_query' ][] = [
				'taxonomy'         => Product_Category::NAME,
				'field'            => 'term_id',
				'terms'            => $args[ Product_Category::NAME ],
				'include_children' => false,
				'operator'         => 'IN',
			];
		}

		if ( ! empty( $args[ 'brand' ] ) ) {
			$brands = array_filter( explode( ',', $args[ 'brand' ] ) );
			if ( ! empty( $brands ) ) {
				$query_args[ 'tax_query' ][] = [
					'taxonomy'         => Brand::NAME,
					'field'            => 'slug',
					'terms'            => $brands,
					'include_children' => false,
					'operator'         => 'IN',
				];
			}
		} elseif ( ! empty( $args[ Brand::NAME ] ) && is_array( $args[ Brand::NAME ] ) ) {
			// we have term IDs passed from the REST API
			$query_args[ 'tax_query' ][] = [
				'taxonomy'         => Brand::NAME,
				'field'            => 'term_id',
				'terms'            => $args[ Brand::NAME ],
				'include_children' => false,
				'operator'         => 'IN',
			];
		}

		$flags = [];
		if ( ! empty( $args[ 'featured' ] ) ) {
			$flags[] = Flag::FEATURED;
		}
		if ( ! empty( $args[ 'sale' ] ) ) {
			$flags[] = Flag::SALE;
		}
		if ( ! empty( $flags ) ) {
			$query_args[ 'tax_query' ][] = [
				'taxonomy' => Flag::NAME,
				'field'    => 'slug',
				'terms'    => $flags,
				'operator' => 'AND',
			];
		} elseif ( ! empty( $args[ Flag::NAME ] ) && is_array( $args[ Flag::NAME ] ) ) {
			// we have term IDs passed from the REST API
			$query_args[ 'tax_query' ][] = [
				'taxonomy'         => Flag::NAME,
				'field'            => 'term_id',
				'terms'            => $args[ Flag::NAME ],
				'operator'         => 'AND',
			];
		}

		if ( ! empty( $args[ 'search' ] ) ) {
			$query_args[ 's' ] = $args[ 'search' ];
		}

		if ( ! empty( $args[ 'recent' ] ) ) {
			$query_args[ 'date_query' ] = [
				/**
				 * Filter how long a product is considered recent
				 *
				 * @param int $days How long a product is recent, in days
				 */
				'after'     => sprintf( '%d days ago', apply_filters( 'bigcommerce/query/recent_days', 2 ) ),
				'column'    => 'post_modified',
				'inclusive' => true,
			];
		}

		if ( ! empty( $args[ 'paged' ] ) ) {
			$query_args[ 'paged' ] = (int) $args[ 'paged' ];
		}

		return apply_filters( 'bigcommerce/shortcode/products/query_args', $query_args, $args );
	}

	private function per_page_default() {
		$default = get_option( Product_Archive::PER_PAGE, Product_Archive::PER_PAGE_DEFAULT );

		return absint( $default ) ?: Product_Archive::PER_PAGE_DEFAULT;
	}

	public function map_rest_args_to_query( $args ) {
		$parameter_mappings = [
			'page'                 => 'paged',
			'bcid'                 => 'id',
		];
		foreach ( $parameter_mappings as $key => $new_key ) {
			if ( array_key_exists( $key, $args ) && ! array_key_exists( $new_key, $args ) ) {
				$args[ $new_key ] = $args[ $key ];
				unset( $args[ $key ] );
			}
		}
		$args = wp_parse_args( $args, Shortcodes\Products::default_attributes() );

		return $this->map_shortcode_args_to_query( $args );
	}
}
