<?php

namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\GraphQL\GraphQL_Processor;
use BigCommerce\Import\Importers\Terms\Term_Strategy_Factory;
use BigCommerce\Import\No_Cache_Options;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * Abstract class for processing term imports.
 *
 * This class provides common functionality for importing terms into WordPress.
 * It interacts with BigCommerce's API and uses GraphQL data for creating or updating terms.
 */
abstract class Term_Import implements Import_Processor {
	use No_Cache_Options;

	/**
	 * The option name used to store the import state for terms.
	 *
	 * This constant defines the key under which the term import state
	 * is stored in the options table.
	 *
	 * @var string
	 */
	const STATE_OPTION      = 'bigcommerce_import_terms_state';

	/**
	 * The option name used to store the import checkpoint for brands.
	 *
	 * This constant defines the key under which the import progress for
	 * brands is stored, allowing resumption of the import process.
	 *
	 * @var string
	 */
	const BRANDS_CHECKPOINT = 'bigcommerce_import_brands_checkpoint';

	/**
	 * @var CatalogApi
	 */
	public $catalog_api;

	/**
	 * @var GraphQL_Processor
	 */
	public $gql_processor;

	/**
	 * @var int
	 */
	public $batch_size;

	/**
	 * Constructor for the Term_Import class.
	 *
	 * @param CatalogApi       $catalog_api   Instance of the Catalog API client.
	 * @param GraphQL_Processor $gql_processor Instance of the GraphQL processor.
	 * @param int              $batch_size    Number of terms to process in a single batch.
	 */
	public function __construct( CatalogApi $catalog_api, GraphQL_Processor $gql_processor, $batch_size ) {
		$this->catalog_api = $catalog_api;
		$this->gql_processor = $gql_processor;
		$this->batch_size  = $batch_size;
	}

	/**
	 * Get the name of the taxonomy being updated.
	 *
	 * @return string The taxonomy name.
	 */
	abstract protected function taxonomy();

	/**
	 * Get fallback terms in case the main source data is unavailable.
	 *
	 * @return array An array of fallback terms.
	 */
	abstract protected function get_fallback_terms();

	/**
	 * Get the state name to set while the import is running.
	 *
	 * @return string The running state name.
	 */
	abstract protected function running_state();

	/**
	 * Get the state name to set when the import is complete.
	 *
	 * @return string The completed state name.
	 */
	abstract protected function completed_state();

	/**
	 * Execute the term import process.
	 *
	 * This method fetches terms from the source, processes them, and updates
	 * the import status as it progresses.
	 *
	 * @return void
	 */
	public function run() {
		$status = new Status();
		$status->set_status( $this->running_state() );


		do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Importing terms for %s taxonomy', 'bigcommerce' ), $this->taxonomy() ), [
			'limit'    => $this->batch_size,
			'taxonomy' => $this->taxonomy(),
		] );

		$rest_fallback = false;

		try {
			$terms = $this->get_source_data();
			// Fallback to old categories pull
			if ( empty( $terms ) && $this->taxonomy() === Product_Category::NAME ) {
				$terms         = $this->get_fallback_terms();
				$rest_fallback = true;
			}
		} catch ( \Throwable $e ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getMessage(), [
				'response' => method_exists( $e, 'getResponseBody' ) ? $e->getResponseBody() : $e->getTraceAsString(),
				'headers'  => method_exists( $e, 'getResponseHeaders' ) ? $e->getResponseHeaders() : '',
			] );

			$terms         = $this->get_fallback_terms();
			$rest_fallback = true;
		}

		if ( empty( $terms ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, sprintf( __( 'Could not find terms for %s. Wrapping up step and go to the next one', 'bigcommerce' ), $this->taxonomy() ), [] );
			$status->set_status( $this->completed_state() );
			$this->clear_state();

			return;
		}

		// Allow more HTML in term descriptions than WP default
		$terms_descriptions_filtered = has_filter( 'pre_term_description', 'wp_filter_kses' );
		if ( $terms_descriptions_filtered ) {
			remove_filter( 'pre_term_description', 'wp_filter_kses' );
		}

		// Create/update each term
		foreach ( $terms as $term ) {
			$this->do_term_import( $term, $rest_fallback );
		}

		// Put the term description filter back where we found it
		if ( $terms_descriptions_filtered ) {
			add_filter( 'pre_term_description', 'wp_filter_kses' );
		}

		$status->set_status( $this->completed_state() );
		$this->clear_state();
	}

	/**
	 * Process and import a single term.
	 *
	 * @param \StdClass $term    The term object to import.
	 * @param bool      $fallback Whether to use fallback data for this import.
	 *
	 * @return void
	 */
	protected function do_term_import( $term, $fallback = false ) {
		if ( ! $fallback ) {
			$parsed = $this->parse_gql_term( $term );
			array_walk( $parsed, function ( $single ) {
				$strategy_factory = new Term_Strategy_Factory( $single, $this->taxonomy() );
				$strategy         = $strategy_factory->get_strategy();
				$strategy->do_import();
			} );

			return;
		}

		$strategy_factory = new Term_Strategy_Factory( $term, $this->taxonomy() );
		$strategy         = $strategy_factory->get_strategy();
		$strategy->do_import();
	}


	/**
	 * Fetch term data from the source.
	 *
	 * @param string $cursor Optional. A cursor for paginated results.
	 *
	 * @return array The API response object.
	 * @throws ApiException If the API request fails.
	 */
	abstract public function get_source_data( $cursor = '' );

	/**
	 * Parse a GraphQL term object into an array format.
	 *
	 * @param \StdClass|null $term The GraphQL term object.
	 *
	 * @return array The parsed term data.
	 */
	abstract protected function parse_gql_term( $term = null ): array;

	private function get_state() {
		$state = $this->get_option( self::STATE_OPTION, [] );
		if ( ! is_array( $state ) ) {
			return [];
		}

		return $state;
	}

	private function set_state( array $state ) {
		$this->update_option( self::STATE_OPTION, $state, false );
	}

	private function clear_state() {
		$this->delete_option( self::STATE_OPTION );
	}

	/**
	 * Parse a GraphQL response and handle pagination if necessary.
	 *
	 * @param string $raw_response The raw GraphQL response.
	 *
	 * @return array|mixed Parsed term data or a cursor for the next page of results.
	 */
	protected function handle_graph_ql_response( $raw_response = '' ) {
		if ( empty( $raw_response ) || empty( $raw_response->data->site ) ) {
			return [];
		}

		switch ( $this->taxonomy() ) {
			case Brand::NAME:
				if ( $raw_response->data->site->brands->pageInfo->hasNextPage ) {
					// Store brands data and return next cursor in order to retrieve all brands
					$checkpoint = $this->get_option( self::BRANDS_CHECKPOINT, [] );
					$this->update_option( self::BRANDS_CHECKPOINT, array_merge( $checkpoint, $raw_response->data->site->brands->edges) );

					return $raw_response->data->site->brands->pageInfo->endCursor;
				}

				return $raw_response->data->site->brands->edges;
			default:
				return $raw_response->data->site->categoryTree;
		}
	}

	/**
	 * Get the current page for the import process.
	 *
	 * @return int The current page number.
	 */
	protected function get_page() {
		$state = $this->get_state();
		if ( ! array_key_exists( $this->taxonomy(), $state ) ) {
			return 0;
		}

		return $state[ $this->taxonomy() ];
	}

	/**
	 * Set the current page for the import process.
	 *
	 * @param int $page The page number to set.
	 *
	 * @return void
	 */
	protected function set_page( $page ) {
		$state                      = $this->get_state();
		$state[ $this->taxonomy() ] = (int) $page;
		$this->set_state( $state );
	}

}
