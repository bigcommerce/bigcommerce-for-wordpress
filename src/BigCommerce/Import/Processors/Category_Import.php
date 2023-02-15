<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\GQL_Term_Model;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Category_Import extends Term_Import {
	use CategoriesTrees;

	protected function taxonomy() {
		return Product_Category::NAME;
	}

	protected function running_state() {
		return Status::UPDATING_CATEGORIES;
	}

	protected function completed_state() {
		return Status::UPDATED_CATEGORIES;
	}

	protected function get_fallback_terms() {
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Unable to fetch categories with GraphQL. Fallback to REST API', 'bigcommerce' ), [] );
		// If GraphQL failed we are trying to fallback with REST API
		try {
			if ( Store_Settings::is_msf_on() ) {
				$data = $this->get_msf_categories( $this->catalog_api );

				if ( ! empty( $data ) ) {
					return $data->getData();
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
	 * @param string $cursor
	 * @param int    $page
	 *
	 * @return \BigCommerce\Api\v3\Model\CategoryCollectionResponse|array
	 * @throws \BigCommerce\Api\v3\ApiException
	 */
	public function get_source_data( $cursor = ''): array {
		$result = $this->gql_processor->get_category_tree();

		return $this->handle_graph_ql_response( $result );
	}

	protected function parse_gql_term( $term = null ): array {
		$term->image_url  = ! empty( $term->image ) ? $term->image->url : '';
		$term->parent_id  = 0;
		$result[]        = new GQL_Term_Model( $term );;

		if ( ! empty( $term->children ) ) {
			$this->parse_term_children( $result, $term->children, $term->entityId );
		}

		return $result;
	}

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
	 * Get category data from API.
	 *
	 * @param $category_id
	 *
	 * @return \BigCommerce\Api\v3\Model\CategoryResponse
	 * @throws ApiException
	 */
	public function get_category_data( $category_id ) {
		$response = $this->catalog_api->getCategoryById( $category_id );

		return $response;
	}
}
