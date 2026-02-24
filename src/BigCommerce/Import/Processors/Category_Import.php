<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\GQL_Term_Model;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Category_Import extends Term_Import {
	use CategoriesTrees;

	/**
	 * Get the taxonomy name for this import.
	 *
	 * This method returns the taxonomy name used for categorization, which is the product category.
	 *
	 * @return string The taxonomy name for product categories.
	 */
	protected function taxonomy() {
		return Product_Category::NAME;
	}

	/**
	 * Get the current running state for this import.
	 *
	 * This method returns the state representing the update process for categories.
	 *
	 * @return string The running state constant for category updating.
	 */
	protected function running_state() {
		return Status::UPDATING_CATEGORIES;
	}

	/**
	 * Get the completed state for this import.
	 *
	 * This method returns the state representing the successful completion of the category import.
	 *
	 * @return string The completed state constant for category update.
	 */
	protected function completed_state() {
		return Status::UPDATED_CATEGORIES;
	}

	/**
	 * Get fallback terms when GraphQL request fails.
	 *
	 * If GraphQL fails, this method fetches categories using the REST API as a fallback. It handles pagination
	 * and logging for both successful and failed attempts.
	 *
	 * @return array|null An array of categories or null if no categories are retrieved.
	 */
	protected function get_fallback_terms() {
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Unable to fetch categories with GraphQL. Fallback to REST API', 'bigcommerce' ), [] );
		// If GraphQL failed we are trying to fallback with REST API
		try {
			if ( Store_Settings::is_msf_on() ) {

				/* loop though all categories and return $allCategories */
				$allCategories = [];
				$currentPage = 1;
				$totalPages = 1;

				do {

					$params['page'] = $currentPage;

					$data = $this->get_msf_categories( $this->catalog_api, $params);

					if ( ! empty( $data ) ) {

						$currentData = $data->getData();

						if (is_array($currentData) && !empty($currentData)) {
							$allCategories = array_merge($allCategories, $currentData);
						} else {
							do_action('bigcommerce/log', Error_Log::WARNING, __('Current data is not an array or is empty.', 'bigcommerce'), []);
						}

						if ( method_exists( $data, 'getMeta' ) ){
							$meta = $data->getMeta();
							if ( isset( $meta['pagination']['current_page'] ) && isset( $meta['pagination']['total_pages'] ) ) {
								$currentPage = $meta['pagination']['current_page'];
								$totalPages  = $meta['pagination']['total_pages'];
							} else {
								do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Pagination information is missing in the response meta.', 'bigcommerce' ), [] );
								break; // Exit the loop
							}
						} else {
							do_action('bigcommerce/log', Error_Log::WARNING, __('getMeta method does not exist on the data object.', 'bigcommerce'), []);
							break; // Exit the loop
						}
					} else {
						do_action('bigcommerce/log', Error_Log::WARNING, __('No data returned from get_msf_categories.', 'bigcommerce'), []);
						break; // Exit the loop
					}

					do_action( 'bigcommerce/log', Error_Log::INFO, __( "Category import Page $currentPage of $totalPages", 'bigcommerce' ), [] );
					$currentPage ++;

				} while ( $currentPage <= $totalPages );

				do_action( 'bigcommerce/log', Error_Log::INFO, __( "Total categories found: ".count($allCategories) , 'bigcommerce' ), [] );

				if ( ! empty( $allCategories ) ) {
					return $allCategories;
				}

			}

			return $this->catalog_api->getCategoriesBatch()->getData();
		} catch ( \Throwable $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );

			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return null;
		}
	}

	/**
	 * Get category data using GraphQL.
	 *
	 * This method retrieves category tree data via GraphQL, handling the response and
	 * returning the result.
	 *
	 * @param string $cursor The cursor for pagination, default is an empty string.
	 *
	 * @return array The processed category data from GraphQL response.
	 * @throws \BigCommerce\Api\v3\ApiException Throws an exception if GraphQL request fails.
	 */
	public function get_source_data( $cursor = ''): array {
		$result = $this->gql_processor->get_category_tree();

		return $this->handle_graph_ql_response( $result );
	}

	/**
	 * Parse a term (category) for GraphQL.
	 *
	 * This method processes a single term, extracting the image URL and setting the
	 * parent ID before recursively parsing any children of the term.
	 *
	 * @param mixed $term The term (category) to parse.
	 *
	 * @return array An array of GQL_Term_Model objects representing the term and its children.
	 */
	protected function parse_gql_term( $term = null ): array {
		$term->image_url  = ! empty( $term->image ) ? $term->image->url : '';
		$term->parent_id  = 0;
		$result[]        = new GQL_Term_Model( $term );;

		if ( ! empty( $term->children ) ) {
			$this->parse_term_children( $result, $term->children, $term->entityId );
		}

		return $result;
	}

	/**
	 * Parse child terms (categories).
	 *
	 * This method recursively processes child categories for a given parent term.
	 * It sets the image URL and parent ID for each child before adding it to the result.
	 *
	 * @param array $result An array that will contain parsed terms.
	 * @param mixed $children The child terms to parse.
	 * @param int   $parent_id The ID of the parent term.
	 */
	protected function parse_term_children( &$result, $children, $parent_id ) {
		foreach ( $children as $child ) {
			$child->image_url = ! empty( $child->image ) ? $child->image->url : '';
			$child->parent_id = $parent_id;
			$result[]         = new GQL_Term_Model( $child );

			if ( ! empty( $child->children ) ) {
				$this->parse_term_children( $result, $child->children, $child->entityId );
			}
		}

		return $result;
	}

	/**
	 * Get category data by ID.
	 *
	 * This method retrieves category data by ID using the Catalog API.
	 *
	 * @param int $category_id The ID of the category to retrieve.
	 *
	 * @return \BigCommerce\Api\v3\Model\CategoryResponse The category data response.
	 * @throws ApiException Throws an exception if API request fails.
	 */
	public function get_category_data( $category_id ) {
		$response = $this->catalog_api->getCategoryById( $category_id );

		return $response;
	}
}
