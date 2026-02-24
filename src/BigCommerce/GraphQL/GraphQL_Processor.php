<?php

namespace BigCommerce\GraphQL;

use BigCommerce\Api\v3\Configuration;
use BigCommerce\Container\GraphQL;
use BigCommerce\Import\Processors\Headless_Product_Processor;

/**
 * This class is responsible for handling GraphQL requests in the BigCommerce API context.
 * It includes methods to fetch various data types like terms, products, product reviews, and customer wishlists.
 * It communicates with the GraphQL API using pre-defined queries and returns the results in a structured format.
 * The class utilizes a configuration object for API settings and a query object for constructing GraphQL queries.
 *
 * @package BigCommerce\GraphQL
 */
class GraphQL_Processor extends BaseGQL {

	/**
	 * @var mixed $query Stores the GraphQL query object or string
	 */
	protected $query;

	/**
	 * Constructor to initialize the GraphQL processor with a configuration and query.
	 *
	 * @param Configuration $config The configuration object that holds API settings and other parameters.
	 * @param mixed $query The GraphQL query object or string to be used for making requests. This could be an array, string, or other types depending on the implementation.
	 *
	 * @return void
	 */
	public function __construct( Configuration $config, $query) {
		parent::__construct( $config );

		$this->query = $query;
	}


	/**
	 * Retrieve taxonomy terms for either categories or brands.
	 *
	 * @hook bigcommerce/gql/request_terms - modify or filter terms request
	 *
	 * @param string $slug Taxonomy term slug
	 * @param string $taxonomy The taxonomy type (category or brand)
	 *
	 * @return array
	 */
	public function request_terms( $slug, $taxonomy = 'category' ) {
		if ( $taxonomy === 'category' ) {
			$query = $this->query[ GraphQL::TERMS_QUERY ]->get_category_query();
		} else {
			$query = $this->query[ GraphQL::TERMS_QUERY ]->get_brand_query();
		}

		$body = [
			'query'     => $query,
			'variables' => [
				'urlPath' => '/' . $slug,
			],
		];

		$result = $this->make_request( $body );

		if ( ! isset( $result->data ) ) {
			return [];
		}

		// we need only specified taxonomy
		if ( strtolower( $result->data->site->route->node->__typename ) !== $taxonomy ) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieve a paginated list of products.
	 *
	 * @hook bigcommerce/gql/request_paginated_products - modify or filter paginated product request
	 *
	 * @param int $size Number of products per page (default: 50)
	 *
	 * @return array
	 */
	public function request_paginated_products( $size = 50 ) {
		$cursor = get_option( Headless_Product_Processor::HEADLESS_CURSOR, '' );
		$query  = $this->query[ GraphQL::PRODUCT_QUERY ]->get_paginated_products_query();
		$body   = [
			'query'     => $query,
			'variables' => [
				'pageSize' => $size,
				'cursor'   => $cursor,
			],
		];

		$result = $this->make_request( $body );

		if ( ! isset( $result->data ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieve a product by its slug.
	 *
	 * @hook bigcommerce/gql/request_product - modify or filter product request
	 *
	 * @param string $slug Product's URL slug
	 *
	 * @return array
	 */
	public function request_product( $slug ) {
		$query = $this->query[ GraphQL::PRODUCT_QUERY ]->get_product_query();
		$body  = [
			'query'     => $query,
			'variables' => [
				'path' => '/' . $slug,
			],
		];

		$result = $this->make_request( $body );

		if ( ! isset( $result->data ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieve product reviews for a given product.
	 *
	 * @hook bigcommerce/gql/request_product_reviews - modify or filter product reviews request
	 *
	 * @param int $product_id The product's ID
	 *
	 * @return array
	 */
	public function request_product_reviews( $product_id ) {
		if ( empty( $product_id ) ) {
			return [];
		}

		$query = $this->query[ GraphQL::REVIEWS_QUERY ]->get_product_reviews_query();
		$body  = [
			'query'     => $query,
			'variables' => [
				'productId' => $product_id,
			],
		];

		$result = $this->make_request( $body );

		if ( ! isset( $result->data ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieve paginated product listings.
	 *
	 * @hook bigcommerce/gql/products_loop_request - modify or filter products loop request
	 *
	 * @param int $limit Number of products to fetch per page
	 * @param string $cursor Pagination cursor (default: empty string)
	 *
	 * @return array
	 */
	public function products_loop_request( $limit = 12, $cursor = '') {
		$query = $this->query[ GraphQL::PRODUCT_QUERY ]->get_product_paginated_request_full();
		$body  = [
			'query'     => $query,
			'variables' => [
				'pageSize' => $limit,
				'cursor'   => $cursor,
			],
		];

		$result = $this->make_request( $body );

		if ( ! isset( $result->data ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieve the wishlist for a specific customer.
	 *
	 * @hook bigcommerce/gql/get_customer_wishlist - modify or filter customer wishlist request
	 *
	 * @param int $customer_id The customer's ID
	 * @param array $entityIds List of product entity IDs to fetch for wishlist
	 * @param bool $public Whether to retrieve public wishlist (default: false)
	 *
	 * @return array
	 */
	public function get_customer_wishlist( $customer_id, $entityIds, $public = false ) {
		if ( empty( $customer_id ) || empty( $entityIds ) ) {
			return [];
		}

		if ($public) {
			$query = $this->query[ GraphQL::CUSTOMER_QUERY ]->get_public_wishlist_query();
		} else {
			$query = $this->query[ GraphQL::CUSTOMER_QUERY ]->get_wishlist_query();
		}

		$body  = [
			'query'     => $query,
			'variables' => [
				'entityIds' => $entityIds
			]
		];

		$headers                     = $this->get_headers( true );
		$headers['X-Bc-Customer-Id'] = $customer_id;
		unset( $headers['Origin'] );

		$result = $this->make_request( $body, $headers );

		if ( ! isset( $result->data ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieve all wishlists for a specific customer.
	 *
	 * @hook bigcommerce/gql/get_customer_wishlists - modify or filter all customer wishlists request
	 *
	 * @param int $customer_id The customer's ID
	 *
	 * @return array
	 */
	public function get_customer_wishlists( $customer_id ) {
		if ( empty( $customer_id ) ) {
			return [];
		}

		$query = $this->query[ GraphQL::CUSTOMER_QUERY ]->get_wishlists_query();
		$body  = [
			'query'     => $query,
			'variables' => [],
		];

		$headers                     = $this->get_headers( true );
		$headers['X-Bc-Customer-Id'] = $customer_id;
		unset( $headers['Origin'] );

		$result = $this->make_request( $body, $headers );

		if ( ! isset( $result->data ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieve GraphQL query from file.
	 *
	 * @hook bigcommerce/gql/query_file_path - change file query location path
	 *
	 * @param string $file The query file name (without extension)
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function get_graph_ql_query_from_file( $file = '' ): string {
		$plugin_path = WP_PLUGIN_DIR . '/bigcommerce/src/BigCommerce/Import/Processors/GQL/%s.graphql';
		$path = apply_filters( 'bigcommerce/gql/query_file_path', sprintf( $plugin_path, $file ), $file );

		if ( ! file_exists( $path ) ) {
			throw new \Exception( __( 'Could not retrieve graph QL query: query file is missing. ' . $plugin_path, 422 ) );
		}

		return file_get_contents( $path );
	}

	/**
	 * Retrieve the category tree.
	 *
	 * @hook bigcommerce/gql/get_category_tree - modify or filter category tree request
	 *
	 * @return mixed
	 * @throws \BigCommerce\Api\v3\ApiException
	 */
	public function get_category_tree() {
		return $this->make_request( [
			'query'     => $this->get_graph_ql_query_from_file( 'category-tree' ),
			'variables' => [],
		] );
	}

	/**
	 * Retrieve a list of brands.
	 *
	 * @hook bigcommerce/gql/get_brands - modify or filter brands request
	 *
	 * @param string $cursor Pagination cursor
	 * @param int $page_size Number of brands to retrieve per page
	 *
	 * @return array
	 */
	public function get_brands( $cursor = '', $page_size = 50 ) {
		return $this->make_request( [
			'query'     => $this->get_graph_ql_query_from_file( 'brands' ),
			'variables' => [
				'pageSize' => $page_size,
				'cursor'   => $cursor
			],
		] );
	}

}
