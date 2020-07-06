<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Settings\Sections\Currency;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Query {
	const UNFILTERED_QUERY_FLAG = '_bigcommerce_unfiltered';

	/**
	 * @param \WP_Query $query
	 *
	 * @return void
	 * @action pre_get_posts
	 */
	public function filter_queries( \WP_Query $query ) {
		if ( $query->get( self::UNFILTERED_QUERY_FLAG ) ) {
			return;
		}

		if ( ! $query->get( 'posts_per_page' ) && $this->is_product_query( $query ) ) {
			$per_page = get_option( Product_Archive::PER_PAGE, Product_Archive::PER_PAGE_DEFAULT );
			if ( $per_page ) {
				$query->set( 'posts_per_page', $per_page );
			}
		}

		if ( $query->is_archive() && $this->is_product_query( $query ) && ! $query->get( 'bc-sort' ) && ! $query->get( 'orderby' ) ) {
			/**
			 * Filter the default sort order for product archives.
			 *
			 * @param string $sort The sorting method to use
			 */
			$default_sort = apply_filters( 'bigcommerce/query/default_sort', Product_Archive::SORT_FEATURED );
			$query->set( 'bc-sort', $default_sort );
		}

		if ( $query->get( 'bc-sort' ) && $this->is_product_query( $query ) && ! is_admin() ) {
			switch ( $query->get( 'bc-sort' ) ) {
				case Product_Archive::SORT_TITLE_ASC:
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'ASC' );
					break;
				case Product_Archive::SORT_TITLE_DESC:
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'DESC' );
					break;
				case Product_Archive::SORT_DATE:
					$query->set( 'orderby', [ 'date' => 'DESC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_FEATURED:
					// Product 'featured' tag in BC store has no effect on sorting
					$query->set( 'orderby', [ 'menu_order' => 'ASC', 'date' => 'DESC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_PRICE_ASC:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query['bigcommerce_price'] = [
						'key'     => Product::PRICE_META_KEY,
						'compare' => 'EXISTS',
						'type'    => 'DECIMAL' . $this->get_ordering_decimal_format(),
					];
					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_price' => 'ASC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_PRICE_DESC:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query['bigcommerce_price'] = [
						'key'     => Product::PRICE_META_KEY,
						'compare' => 'EXISTS',
						'type'    => 'DECIMAL' . $this->get_ordering_decimal_format(),
					];
					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_price' => 'DESC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_REVIEWS:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query['bigcommerce_rating'] = [
						'key'     => Product::RATING_META_KEY,
						'compare' => 'EXISTS',
					];
					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_rating' => 'DESC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_SALES:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query['bigcommerce_sales'] = [
						'key'     => Product::SALES_META_KEY,
						'compare' => 'EXISTS',
						'type'    => 'NUMERIC'
					];
					
					$meta_query['bigcommerce_id'] = [
						'key'  => Product::BIGCOMMERCE_ID,
						'type' => 'NUMERIC'
					];

					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_sales' => 'DESC', 'bigcommerce_id' => 'DESC' ] );
					break;
				case 'bigcommerce_id__in':
					$meta_query                   = $query->get( 'meta_query' ) ?: [];
					$meta_query['bigcommerce_id'] = [
						'key'     => 'bigcommerce_id',
						'compare' => 'EXISTS',
					];
					$query->set( 'meta_query', $meta_query );
					$orderby_filter = function ( $orderby, $wp_query ) use ( $query ) {
						if ( $wp_query !== $query || empty( $query->query_vars['bigcommerce_id__in'] ) ) {
							return $orderby;
						}
						$meta_clauses = $query->meta_query->get_clauses();
						if ( ! array_key_exists( 'bigcommerce_id', $meta_clauses ) ) {
							return $orderby;
						}
						$alias = $meta_clauses['bigcommerce_id']['alias'];

						return "FIELD({$alias}.meta_value," . implode( ',', array_map( 'absint', $query->query_vars['bigcommerce_id__in'] ) ) . ')';
					};
					add_filter( 'posts_orderby', $orderby_filter, 10, 2 );
					break;
				default:
					do_action( 'bigcommerce/query/sort', $query );
					break;
			}
		}

		$bcid_in     = $this->get_query_var_as_array( $query, 'bigcommerce_id__in' );
		$bcid_not_in = $this->get_query_var_as_array( $query, 'bigcommerce_id__not_in' );
		$sku_in      = $this->get_query_var_as_array( $query, 'bigcommerce_sku__in' );
		$sku_not_in  = $this->get_query_var_as_array( $query, 'bigcommerce_sku__not_in' );

		$in = [];
		if ( ! empty( $bcid_in ) ) {
			$post_ids = $this->bcids_to_post_ids( $bcid_in ) ?: [ 0 ];
			$in       = $post_ids;
		}
		if ( ! empty( $sku_in ) ) {
			$post_ids = $this->skus_to_post_ids( $sku_in ) ?: [ 0 ];
			$in       = $in ? array_intersect( $in, $post_ids ) : $post_ids; // intersect with bcids if both present
			$in       = $in ?: [ 0 ];
		}
		if ( $this->is_product_search( $query ) ) {
			$search_in = $this->search_to_post_ids( $query->get( 's' ) );
			$query->set( 's', '' ); // set 's' back to the default value so WP doesn't turn it into another search
			$in = $in ? array_intersect( $in, $search_in ) : $search_in;
			$in = $in ?: [ 0 ];
		}

		if ( $in ) {
			$post__in = $query->get( 'post__in', [] );
			$post__in = $post__in ? array_intersect( $post__in, $in ) : $in;
			$query->set( 'post__in', $post__in ?: [ 0 ] );

			return; // don't set not_in if we're setting in, as WP will ignore it
		}

		$out = [];
		if ( ! empty( $bcid_not_in ) ) {
			$post_ids = $this->bcids_to_post_ids( $bcid_not_in );
			$out      = array_merge( $out, $post_ids );
		}
		if ( ! empty( $sku_not_in ) ) {
			$post_ids = $this->skus_to_post_ids( $sku_not_in );
			$out      = array_merge( $out, $post_ids );
		}

		if ( $out ) {
			$post__not_in = $query->get( 'post__not_in', [] );
			$post__not_in = array_merge( $post__not_in, $out );
			$query->set( 'post__not_in', $post__not_in );
		}
	}

	/**
	 * Get the Decimal Data Type Characteristics for Decimal price ordering
	 *
	 * @return string
	 */
	private function get_ordering_decimal_format() {
		$decimals     = get_option( Currency::DECIMAL_UNITS ) ?: 2;
		$integer      = get_option( Currency::INTEGER_UNITS ) ?: 16;
		$max_length_m = $integer + $decimals;

		return "($max_length_m, $decimals)";
	}

	/**
	 * Remove empty query vars from request vars
	 *
	 * The main purpose here is to avoid WP thinking
	 * we're on a search page because the product
	 * archive filters left a "s=" in the URL.
	 *
	 * @param array $vars
	 *
	 * @return array
	 * @filter request
	 */
	public function filter_empty_query_vars( $vars ) {
		foreach ( [ 's', 'bc-sort', Brand::NAME, Product_Category::NAME ] as $query_var ) {
			if ( isset( $vars[ $query_var ] ) && ( empty( $vars[ $query_var ] ) || $query_var === '0' ) ) {
				unset( $vars[ $query_var ] );
			}
		}

		return $vars;
	}

	/**
	 * @param array $vars
	 *
	 * @return array
	 * @filter query_vars
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'bc-sort';

		return $vars;
	}

	private function get_query_var_as_array( \WP_Query $query, $var ) {
		$value = $query->get( $var, [] );
		if ( empty( $value ) ) {
			return [];
		}
		if ( ! is_array( $value ) ) {
			$value = explode( ',', $value );
		}

		return array_filter( $value );
	}

	/**
	 * Convert an array of BigCommerce IDs to post IDs
	 *
	 * @param int[] $bcids
	 *
	 * @return int[]
	 */
	private function bcids_to_post_ids( $bcids ) {
		if ( empty( $bcids ) ) {
			return [];
		}

		$post_ids = get_posts( [
			self::UNFILTERED_QUERY_FLAG => true,
			'post_type'                 => Product::NAME,
			'posts_per_page'            => - 1,
			'fields'                    => 'ids',
			'meta_query'                => [
				[
					'key'     => Product::BIGCOMMERCE_ID,
					'value'   => $bcids,
					'compare' => 'IN',
				],
			],
		] );

		return array_map( 'intval', $post_ids );
	}

	/**
	 * Convert an array of BigCommerce SKUs to post IDs
	 *
	 * @param string[] $skus
	 *
	 * @return int[]
	 */
	private function skus_to_post_ids( $skus ) {
		if ( empty( $skus ) ) {
			return [];
		}

		$post_ids = get_posts( [
			self::UNFILTERED_QUERY_FLAG => true,
			'post_type'                 => Product::NAME,
			'posts_per_page'            => - 1,
			'fields'                    => 'ids',
			'meta_query'                => [
				[
					'key'     => Product::SKU,
					'value'   => $skus,
					'compare' => 'IN',
				],
			],
		] );

		return array_map( 'intval', $post_ids );
	}

	/**
	 * @param \WP_Query $query
	 *
	 * @return bool
	 */
	private function is_product_search( \WP_Query $query ) {
		$search_phrase = $query->get( 's' );
		if ( empty( $search_phrase ) ) {
			return false;
		}
		$search_terms = explode( ' ', $search_phrase );
		if ( count( $search_terms ) !== 1 ) {
			return false;
		}

		return $this->is_product_query( $query );
	}

	private function is_product_query( \WP_Query $query ) {
		$post_type = $query->get( 'post_type' );
		if ( ! empty( $post_type ) ) {
			if ( is_array( $post_type ) ) {
				if ( count( $post_type ) > 1 ) {
					return false;
				}
				$post_type = reset( $post_type );
			}

			return $post_type == Product::NAME;
		}

		if ( $query->is_tax( [ Brand::NAME, Product_Category::NAME ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Product search should match title, BigCommerce ID, or SKU
	 *
	 * @param string $search_phrase
	 *
	 * @return array
	 */
	private function search_to_post_ids( $search_phrase ) {
		$query = new \WP_Query();

		$search_query_args = [
			'fields'                => 'ids',
			'posts_per_page'        => - 1,
			'post_type'             => Product::NAME,
			'_bigcommerce_internal' => true,
			'meta_query'            => [
				'relation'        => 'OR',
				'bigcommerce_id'  => [
					'key'     => Product::BIGCOMMERCE_ID,
					'value'   => $search_phrase,
					'compare' => '=',
				],
				'bigcommerce_sku' => [
					'key'     => Product::SKU,
					'value'   => $search_phrase,
					'compare' => '=',
				],
			],
		];

		$matches = $query->query( $search_query_args );

		if ( empty( $matches ) ) {
			$search_query_args = [
				's'                         => $search_phrase,
				'fields'                    => 'ids',
				'post_type'                 => Product::NAME,
				'posts_per_page'            => - 1,
				self::UNFILTERED_QUERY_FLAG => true,
			];

			$matches = $query->query( $search_query_args );
		}

		return apply_filters( 'bigcommerce/query/search_post_ids', array_map( 'intval', $matches ), $search_phrase );
	}
}
