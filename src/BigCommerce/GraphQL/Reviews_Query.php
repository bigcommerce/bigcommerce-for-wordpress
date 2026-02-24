<?php

namespace BigCommerce\GraphQL;

/**
 * This class is responsible for constructing GraphQL queries related to product reviews.
 * It provides methods to fetch reviews for a specific product, including review text, rating,
 * creation date, and author details.
 */
class Reviews_Query {

    /**
     * Get the GraphQL query for fetching product reviews by product ID.
     *
     * @return string The GraphQL query string for product reviews.
     */
    public function get_product_reviews_query(): string {
        return 'query ReviewsByProductId(
              $productId: Int!
            ) {
                site {
                    product(entityId: $productId) {
                        reviews {
                            edges {
                              node {
                                entityId
                                text
                                rating
                                createdAt {
                                    __typename
                                    utc
                                }
                                author {
                                    name
                                    __typename
                                }
                              }
                            }
                            pageInfo {
                              hasNextPage
                              hasPreviousPage
                              startCursor
                              endCursor
                            }
                        __typename
                    }
                    __typename
                }
            }
        }';
    }

}
