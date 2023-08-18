<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Api\v3\Model\GQL_Term_Model;

class Brand_Import extends Term_Import {

	protected array $batch = [];

	protected function taxonomy() {
		return Brand::NAME;
	}

	protected function running_state() {
		return Status::UPDATING_BRANDS;
	}

	protected function completed_state() {
		return Status::UPDATED_BRANDS;
	}

	protected function parse_gql_term( $term = null ): array {
		$term              = $term->node;
		$term->description = ! empty( $term->seo ) ? $term->seo->metaDescription : '';
		$term->parent_id   = 0;
		$term->image_url   = ! empty( $term->defaultImage ) ? $term->defaultImage->url : '';

		return [ new GQL_Term_Model( $term ) ];
	}

	/**
	 * @param string $cursor
	 *
	 * @return array
	 * @throws \Exception
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
