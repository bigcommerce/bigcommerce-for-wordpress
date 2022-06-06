<?php

namespace BigCommerce\GraphQL;

/**
 * @class Product_Query
 *
 * Handle product query data and fragments
 */
class Terms_Query {

	public function get_category_query() {
		return 'query LookUpUrl($urlPath: String!) {
		  site {
		    route(path: $urlPath) {
		      node {
		        __typename
		        id
		        ... on Category {
		          entityId
		          name
		          defaultImage {
		            url(width: 200)
		          }
		        }
		      }
		    }
		  }
		}';
	}

	public function get_brand_query() {
		return 'query LookUpUrl($urlPath: String!) {
		  site {
		    route(path: $urlPath) {
		      node {
		        __typename
		        id
		        ... on Category {
		          entityId
		          name
		          defaultImage {
		            url(width: 200)
		          }
		        }
		      }
		    }
		  }
		}';
	}

}
