<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Api\v3\Model\GQL_Term_Model;

/**
 * This class handles the import process for BigCommerce brands. It extends the base Term_Import class
 * and provides the specific functionality for importing and updating brand terms in WordPress.
 */
class Brand_Import extends Term_Import {

	/**
	 * @var array $batch Holds the batch of brand terms to be processed.
	 */
	protected array $batch = [];

	/**
	 * Get the taxonomy name for the brand import.
	 *
	 * This method returns the taxonomy name specific to brands in BigCommerce.
	 *
	 * @return string The name of the taxonomy.
	 */
	protected function taxonomy() {
		return Brand::NAME;
	}

	/**
	 * Get the state for the running process.
	 *
	 * This method returns the status representing the running state of the brand import.
	 *
	 * @return string The status indicating the import is running.
	 */
	protected function running_state() {
		return Status::UPDATING_BRANDS;
	}

	/**
	 * Get the state for the completed process.
	 *
	 * This method returns the status representing the completion of the brand import.
	 *
	 * @return string The status indicating the import is completed.
	 */
	protected function completed_state() {
		return Status::UPDATED_BRANDS;
	}

	/**
	 * Parse the BigCommerce GraphQL term data and map it to a GQL_Term_Model.
	 *
	 * This method processes the raw GraphQL term data and transforms it into a standardized GQL_Term_Model object,
	 * adjusting certain properties such as the description and image URL.
	 *
	 * @param \stdClass|null $term The GraphQL term data to parse.
	 *
	 * @return array An array containing the GQL_Term_Model instance(s).
	 */
	protected function parse_gql_term( $term = null ): array {
		$term              = $term->node;
		$term->description = ! empty( $term->seo ) ? $term->seo->metaDescription : '';
		$term->parent_id   = 0;
		$term->image_url   = ! empty( $term->defaultImage ) ? $term->defaultImage->url : '';

		return [ new GQL_Term_Model( $term ) ];
	}

	/**
	 * Get the source data for the brand import from BigCommerce.
	 *
	 * This method fetches brand data from BigCommerce using GraphQL and handles the response.
	 * If the response includes a cursor, it recursively retrieves the next set of data.
	 *
	 * @param string $cursor The cursor to fetch the next set of data (optional).
	 *
	 * @return array The list of brand terms fetched from the BigCommerce API.
	 * @throws \Exception If there is an error retrieving the data.
	 */
	public function get_source_data( $cursor = '' ): array {
		$response = $this->gql_processor->get_brands( $cursor );

		$response = $this->handle_graph_ql_response( $response );

		if ( is_string( $response ) ) {
			// Get next portion of brands
			return $this->get_source_data( $response );
		}

		return array_merge( $response, $this->get_option( self::BRANDS_CHECKPOINT, [] ) );
	}

	/**
	 * Get the fallback terms if GraphQL data retrieval fails.
	 *
	 * This method retrieves brand data using the Catalog API if GraphQL data cannot be fetched.
	 * It handles pagination and ensures that all brand data is retrieved.
	 *
	 * @return array The list of fallback brand terms fetched from the Catalog API.
	 */
	protected function get_fallback_terms() {
		try {
			$page = $this->get_page();

			if ( empty( $page ) ) {
				$page = 1;
			}

			$response = $this->catalog_api->getBrands( [
				'page'  => $page,
				'limit' => $this->batch_size,
			] );

			$this->batch = array_merge( $this->batch, $response->getData() );

			$total_pages = $response->getMeta()->getPagination()->getTotalPages();

			if ( $total_pages > $page ) {
				$this->set_page( $page + 1 );

				return $this->get_fallback_terms();
			}

			return $this->batch;
		} catch ( \Throwable $e ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getMessage(), [
					'response' => method_exists( $e, 'getResponseBody' ) ? $e->getResponseBody() : $e->getTraceAsString(),
					'headers'  => method_exists( $e, 'getResponseHeaders' ) ? $e->getResponseHeaders() : '',
			] );

			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return [];
		}
		
	}
}
