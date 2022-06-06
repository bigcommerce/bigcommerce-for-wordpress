<?php

namespace BigCommerce\GraphQL;

/**
 * @class Product_Query
 *
 * Handle product query data and fragments
 */
class Reviews_Query {

	public function get_product_reviews_query() {
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
