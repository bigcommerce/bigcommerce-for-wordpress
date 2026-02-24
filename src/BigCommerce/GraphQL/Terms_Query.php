<?php

namespace BigCommerce\GraphQL;

/**
 * This class is responsible for constructing GraphQL queries related to terms, 
 * including categories and brands. It provides methods to fetch term details 
 * such as name, ID, and default image.
 */
class Terms_Query {

    /**
     * Get the GraphQL query for fetching category details by URL path.
     *
     * @return string The GraphQL query string for fetching category details.
     */
    public function get_category_query(): string {
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

    /**
     * Get the GraphQL query for fetching brand details by URL path.
     *
     * @return string The GraphQL query string for fetching brand details.
     */
    public function get_brand_query(): string {
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
