<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\CatalogApi;
use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Import\Runner\Status;

class Product_ID_Fetcher implements Import_Processor {
	const STATE_OPTION = 'bigcommerce_import_product_id_fetcher_state';

	/**
	 * @var CatalogApi
	 */
	private $api;

	/**
	 * @var int
	 */
	private $limit;

	/**
	 * Product_ID_Fetcher constructor.
	 *
	 * @param CatalogApi $api   The API connection to use for the import
	 * @param int        $limit Number of product IDs to fetch per request
	 */
	public function __construct( CatalogApi $api, $limit = 100 ) {
		$this->api   = $api;
		$this->limit = $limit;
	}

	public function run() {
		$status = new Status();
		$status->set_status( Status::FETCHING_PRODUCT_IDS );

		$page = $this->get_page();
		try {
			$response = $this->api->getProducts( [
				'include_fields' => [ 'id', 'date_modified' ],
				'limit'          => $this->limit,
				'page'           => $page,
			] );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage() );

			return;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;
		$inserts = array_map( function ( Product $product ) {
			$modified = $product[ 'date_modified' ];

			return sprintf( '( %d, "%s", "update", "%s" )', $product[ 'id' ], $modified->format( 'Y-m-d H:i:s' ), date( 'Y-m-d H:i:s' ) );
		}, $response->getData() );

		$count = 0;
		if ( ! empty( $inserts ) ) {
			$values = implode( ', ', $inserts );
			$count  = $wpdb->query( "INSERT IGNORE INTO {$wpdb->bc_import_queue} ( bc_id, date_modified, import_action, date_created ) VALUES $values" );
		}

		do_action( 'bigcommerce/import/fetched_ids', $count, $response );

		$total_pages = $response->getMeta()->getPagination()->getTotalPages();
		if ( $total_pages > $page ) {
			$this->set_page( $page + 1 );
		} else {
			$status->set_status( Status::FETCHED_PRODUCT_IDS );
			$this->clear_state();
		}
	}

	private function get_page() {
		$state = $this->get_state();
		if ( ! array_key_exists( 'page', $state ) ) {
			return 1;
		}

		return $state[ 'page' ];
	}

	private function set_page( $page ) {
		$state           = $this->get_state();
		$state[ 'page' ] = (int) $page;
		$this->set_state( $state );
	}

	private function get_state() {
		$state = get_option( self::STATE_OPTION, [] );
		if ( ! is_array( $state ) ) {
			return [];
		}

		return $state;
	}

	private function set_state( array $state ) {
		update_option( self::STATE_OPTION, $state, false );
	}

	private function clear_state() {
		delete_option( self::STATE_OPTION );
	}
}