<?php

namespace BigCommerce\Container;

use BigCommerce\GraphQL\Customer_Query;
use BigCommerce\GraphQL\GraphQL_Processor;
use BigCommerce\GraphQL\Product_Query;
use BigCommerce\GraphQL\Reviews_Query;
use BigCommerce\GraphQL\Terms_Query;
use Pimple\Container;

/**
 * This class provides functionality for registering GraphQL-related services, such as various
 * query objects and a GraphQL requestor processor, in the BigCommerce container. The `register`
 * method configures these services to be accessed globally through the container.
 *
 * @package BigCommerce\Container
 */
class GraphQL extends Provider {

    /**
     * The service key for accessing the GraphQL requestor processor.
     *
     * This constant defines the key used to access the `GraphQL_Processor` service from the container.
     *
     * @var string
     */
    const GRAPHQL_REQUESTOR = 'bigcommerce.graphql_requestor';

    /**
     * The service key for accessing the main GraphQL query collection.
     *
     * This constant defines the key used to access the collection of GraphQL queries (product, reviews, etc.)
     * from the container.
     *
     * @var string
     */
    const QUERY = 'bigcommerce.graphql_query';

    /**
     * The service key for accessing the product-related GraphQL query.
     *
     * This constant is used to access the query specifically related to products in the GraphQL system.
     *
     * @var string
     */
    const PRODUCT_QUERY = 'bigcommerce.graphql_query_products';

    /**
     * The service key for accessing the reviews-related GraphQL query.
     *
     * This constant is used to access the query related to product reviews in the GraphQL system.
     *
     * @var string
     */
    const REVIEWS_QUERY = 'bigcommerce.graphql_query_reviews';

    /**
     * The service key for accessing the terms-related GraphQL query.
     *
     * This constant is used to access the query related to terms and conditions in the GraphQL system.
     *
     * @var string
     */
    const TERMS_QUERY = 'bigcommerce.graphql_query_terms';

    /**
     * The service key for accessing the customer-related GraphQL query.
     *
     * This constant is used to access the query related to customer information in the GraphQL system.
     *
     * @var string
     */
    const CUSTOMER_QUERY = 'bigcommerce.graphql_query_customer';

    /**
     * Registers the GraphQL-related services in the container.
     *
     * This method registers the GraphQL queries (products, reviews, terms, customer) and the `GraphQL_Processor`
     * service that will be used to execute GraphQL requests. These services are registered with specific keys
     * that can be accessed via the container.
     *
     * @param Container $container The Pimple container instance used for managing dependencies.
     */
    public function register(Container $container) {
        $container[ self::QUERY ] = function (Container $container) {
            return [
                self::PRODUCT_QUERY  => new Product_Query(),
                self::TERMS_QUERY    => new Terms_Query(),
                self::REVIEWS_QUERY  => new Reviews_Query(),
                self::CUSTOMER_QUERY => new Customer_Query(),
            ];
        };

        $container[ self::GRAPHQL_REQUESTOR ] = function (Container $container) {
            return new GraphQL_Processor( $container[ Api::CONFIG ], $container[ self::QUERY ] );
        };
    }
}
