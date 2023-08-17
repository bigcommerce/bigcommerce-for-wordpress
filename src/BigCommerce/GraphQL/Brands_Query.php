<?php

namespace BigCommerce\GraphQL;

/**
 * @class Brands_Query
 *
 * Handle brands query data and fragments
 */
class Brands_Query
{

	public function get_brands_query()
	{
		return 'query GetBrands(
			$pageSize: Int!
			$cursor: String
		  ) {
			site {
			  brands(first: $pageSize, after: $cursor) {
				pageInfo {
				  endCursor
				  hasNextPage
				}
				edges{
				  node{
					entityId
					id
					name
					seo {
					  metaDescription
					  pageTitle
					}
					path
					defaultImage {
					  url(width: 320)
					  urlOriginal
					}
				  }
				}
			  }
			}
		  }
		 ';
	}
}
