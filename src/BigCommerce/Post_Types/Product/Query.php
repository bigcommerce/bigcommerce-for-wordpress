<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Import\Import_Type;
use BigCommerce\Customizer\Sections\Product_Category as Customizer;
use BigCommerce\Import\Processors\Store_Settings;
use BigCommerce\Settings\Sections\Currency;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Category\Query_Filter;

/**
 * Handles custom BigCommerce product queries in WordPress.
 *
 * This class integrates with WordPress query modifications to support BigCommerce-specific
 * filters, sorting, and customization for product archives and search results.
 */
class Query {
	/**
	 * Flag to bypass custom filtering of queries.
	 *
	 * Queries with this flag set are not subject to BigCommerce-specific filtering.
	 */
	const UNFILTERED_QUERY_FLAG = '_bigcommerce_unfiltered';

	private $query_filter;

	/**
	 * @var \BigCommerce\Api\v3\Api\CatalogApi
	 */
	private $catalog_api;

	/**
	 * Query constructor.
	 *
	 * Initializes the query with the required Catalog API and filter instance.
	 *
	 * @param CatalogApi   $catalog_api Instance of the BigCommerce Catalog API.
	 * @param Query_Filter $filter      Query filter for handling visibility and customizations.
	 */
	public function __construct( CatalogApi $catalog_api, Query_Filter $filter) {
		$this->query_filter = $filter;
		$this->catalog_api  = $catalog_api;
	}

	/**
	 * Modifies WordPress queries to integrate BigCommerce-specific logic.
	 *
	 * This method applies custom sorting, filtering, and metadata adjustments to WordPress queries
	 * based on BigCommerce product data and settings. It respects WordPress's native query vars
	 * while introducing new ones like `bc-sort` for enhanced customization.
	 *
	 * @param \WP_Query $query The WordPress query instance to modify.
	 * @return void
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

				case Product_Archive::SORT_INVENTORY_COUNT:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query['bigcommerce_inventory_level'] = [
						'key'     => Product::INVENTORY_META_KEY,
						'compare' => 'EXISTS',
						'type'    => 'NUMERIC'
					];

					$meta_query['bigcommerce_id'] = [
						'key'  => Product::BIGCOMMERCE_ID,
						'type' => 'NUMERIC'
					];

					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_inventory_level' => 'DESC', 'bigcommerce_id' => 'DESC' ] );
					break;

				case Product_Archive::SORT_SKU:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query['bigcommerce_sku_normalized'] = [
						'key'     => Product::SKU_NORMALIZED,
						'compare' => 'EXISTS',
					];

					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_sku_normalized' => 'ASC'] );

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

		$product_behaviour            = get_option( Store_Settings::PRODUCT_OUT_OF_STOCK, 'do_nothing' );
		$should_respect_main_settings = get_option( Product_Archive::GENERAL_INVENTORY, 'no' ) !== 'no';
		if ( ! is_admin() && ! is_single() &&  $query->get( 'bc-sort' ) && $this->is_product_query( $query ) && $should_respect_main_settings && ( $product_behaviour === 'hide_product_and_accessible' || $product_behaviour === 'hide_product' ) ) {
			$meta_query = $query->get( 'meta_query' ) ?: [];
			$tax_query  = $query->get( 'tax_query' ) ?: [];

			$meta_query['bigcommerce_inventory_level_settings'] = [
				'key'     => Product::INVENTORY_META_KEY,
				'value'   => 0,
				'compare' => '>',
			];

			$tax_query['bigcommerce_out_stock_flag'] = [
				'taxonomy' => Flag::NAME,
				'field'    => 'name',
				'terms'    => Flag::OUT_OF_STOCK,
				'operator' => 'NOT IN',
			];

			$query->set( 'meta_query', $meta_query );
			$query->set( 'tax_query', $tax_query );
		}

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
			$is_headless = ! Import_Type::is_traditional_import();
			$search_in   = $this->search_to_post_ids( $query->get( 's' ), $is_headless );
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

	private function handle_non_visible_categories(): string {
		$result = $this->query_filter->get_non_visible_terms();

		if ( empty( $result) || is_wp_error( $result) ) {
			return '';
		}

		$categories_exclude = '';

		foreach ( $result as $item ) {
			$categories_exclude = sprintf( '-%d,', $item );
		}

		if ( empty( $categories_exclude ) ) {
			return '';
		}

		return trim( $categories_exclude, ',' );
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
	 * Removes empty query variables from the request vars to prevent unintended behavior. 
	 * For example, it prevents WordPress from interpreting empty s= parameters (left from product archive filters) as a search page request.
	 *
	 * @param array $vars Associative array of query variables.
	 * 
	 * @return array Filtered array of query variables.
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
	 * Add custom query vars to WordPress.
	 *
	 * This ensures custom query variables, such as 'bc-sort',
	 * are recognized and not removed during request parsing.
	 *
	 * @param array $vars The list of query vars recognized by WordPress.
	 *
	 * @return array Updated list of query vars.
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
	private function search_to_post_ids( $search_phrase, $is_headless = false ) {
		$query = new \WP_Query();

		if ( $is_headless ) {
			$matches = $this->perform_headless_search( $search_phrase );
		}

		if ( empty( $matches ) ) {
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

			if ( get_option( Customizer::CATEGORIES_IS_VISIBLE, 'no' ) === 'yes' ) {
			$categories = $this->handle_non_visible_categories();

			if ( ! empty( $categories ) ) {
				$search_query_args['cat'] = $categories;
			}
		}

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
		}

		/**
		 * Filters query search post ids.
		 *
		 * @param array  $matches       Post ids.
		 * @param string $search_phrase Search phrase.
		 */
		return apply_filters( 'bigcommerce/query/search_post_ids', array_map( 'intval', $matches ), $search_phrase );
	}

	/**
	 * Perform a search via Rest API. Retrieves an array of product ids from api
	 * and tries to find existing product in WP DB
	 *
	 * @param string $search_phrase
	 *
	 * @return array
	 */
	private function perform_headless_search( $search_phrase = '' ): array {
		$params = [
			'is_visible'     => true,
			'limit'          => 100,
			'keyword'        => $search_phrase,
			'include_fields' => ['id'],
		];
		try {
			$products_response = $this->catalog_api->getProducts( $params );
		} catch ( ApiException $exception ) {
			return [];
		}

		$ids = array_map( static function ( $entry ) {
			return $entry['id'];
		}, $products_response->getData() );

		if ( empty( $ids ) ) {
			return [];
		}

		global $wpdb;

		$prepared_ids   = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
		$prepare_values = array_merge( [ Product::BIGCOMMERCE_ID ], $ids );
		$sql            = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value IN ($prepared_ids)";
		$result         = $wpdb->get_col(
			$wpdb->prepare(
				$sql,
				$prepare_values
			)
		);

		if ( empty( $result ) || is_a( $result, 'WP_Error' ) ) {
			return [];
		}

		return $result;
	}
}
