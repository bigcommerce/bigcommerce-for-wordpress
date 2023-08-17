<?php

namespace BigCommerce\Container;

use BigCommerce\GraphQL\Brands_Query;
use BigCommerce\GraphQL\Category_Tree_Query;
use BigCommerce\GraphQL\Customer_Query;
use BigCommerce\GraphQL\GraphQL_Processor;
use BigCommerce\GraphQL\Product_Query;
use BigCommerce\GraphQL\Reviews_Query;
use BigCommerce\GraphQL\Terms_Query;
use Pimple\Container;

class GraphQL extends Provider {

	const GRAPHQL_REQUESTOR = 'bigcommerce.graphql_requestor';
	const QUERY             = 'bigcommerce.graphql_query';
	const PRODUCT_QUERY     = 'bigcommerce.graphql_query_products';
	const REVIEWS_QUERY     = 'bigcommerce.graphql_query_reviews';
	const TERMS_QUERY       = 'bigcommerce.graphql_query_terms';
	const CUSTOMER_QUERY    = 'bigcommerce.graphql_query_customer';
	const CATEGORY_TREE_QUERY    = 'bigcommerce.graphql_query_category_tree';
	const BRANDS_QUERY    = 'bigcommerce.graphql_query_brands';

	/**
	 * @inheritDoc
	 */
	public function register(Container $container) {
		$container[ self::QUERY ] = function ( Container $container ) {
			return [
				self::PRODUCT_QUERY  => new Product_Query(),
				self::TERMS_QUERY    => new Terms_Query(),
				self::REVIEWS_QUERY  => new Reviews_Query(),
				self::CUSTOMER_QUERY => new Customer_Query(),
				self::CATEGORY_TREE_QUERY => new Category_Tree_Query(),
				self::BRANDS_QUERY => new Brands_Query(),
			];
		};

		$container[ self::GRAPHQL_REQUESTOR ] = function ( Container $container ) {
			return new GraphQL_Processor( $container[ Api::CONFIG ], $container[ self::QUERY ] );
		};
	}
}
