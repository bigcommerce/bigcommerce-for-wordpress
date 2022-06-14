<?php

namespace BigCommerce\GraphQL;

/**
 * @class Product_Query
 *
 * Handle product query data and fragments
 */
class Customer_Query {

	public function get_wishlist_query() {
		return 'query {
		  customer {
		    wishlists {
		      pageInfo {
		        hasNextPage
		        hasPreviousPage
		        __typename
		        startCursor
		        endCursor
		      }
		      edges {
		        node {
		          __typename
		          entityId
		          name
		          isPublic
		          token
		          items {
		            pageInfo {
		              hasNextPage
		              hasPreviousPage
		              __typename
		              startCursor
		              endCursor
		            }
		            edges {
		              node {
		                entityId
		                __typename
		                productEntityId
		                variantEntityId
		              }
		            }
		          }
		        }
		      }
		    }
		  }
		}';
	}

}
