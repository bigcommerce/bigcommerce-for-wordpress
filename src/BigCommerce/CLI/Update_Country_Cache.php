<?php


namespace BigCommerce\CLI;


use Bigcommerce\Api\Client;

/**
 * Class Update_Country_Cache
 *
 * Updates the country/state cache in assets/data/countries.json
 * Usage:
 *        wp bigcommerce countries update
 */
class Update_Country_Cache extends Command {
	private $default_output_file = '';

	public function __construct( $default_output_file ) {
		$this->default_output_file = $default_output_file;
	}

	protected function command() {
		return 'countries update';
	}

	protected function description() {
		return __( 'Update the cache of countries and states in countries.json', 'bigcommerce' );
	}

	protected function arguments() {
		return [
			[
				'type'        => 'assoc',
				'name'        => 'output',
				'description' => __( 'The path to the output json file', 'bigcommerce' ),
				'optional'    => true,
			],
		];
	}

	public function run( $args, $assoc_args ) {
		$output_file = empty( $assoc_args[ 'output' ] ) ? $this->default_output_file : $assoc_args[ 'output' ];
		if ( ! is_writable( $output_file ) ) {
			\WP_CLI::error( sprintf( __( 'Cannot write to %s.', 'bigcommerce' ), $output_file ) );
		}
		$countries = $this->get_country_data();
		if ( empty( $countries ) ) {
			\WP_CLI::error( __( 'Unable to retrieve country data from the BigCommerce API', 'bigcommerce' ) );
		}
		$json = wp_json_encode( $countries );
		\WP_CLI::debug( sprintf( __( 'Writing country json to %s', 'bigcommerce' ), $output_file ) );
		file_put_contents( $output_file, $json );
		\WP_CLI::success( __( 'Update complete', 'bigcommerce' ) );
	}


	public function get_country_data() {
		try {
			$countries = Client::getCollection( '/countries?limit=250' );

			if ( ! is_array( $countries ) ) {
				return [];
			}

			$progress = \WP_CLI\Utils\make_progress_bar( __( 'Importing state lists', 'tribe' ), count( $countries ) );

			$countries = array_map( function ( $country ) use ( $progress ) {
				try {
					$states = Client::getCollection( sprintf( '/countries/%d/states?limit=250', $country->id ) );
				} catch ( \Exception $e ) {
					$states = null;
					\WP_CLI::warning( sprintf( __( 'Error fetching states for %s. Error message: %s', 'bigcommerce' ), $country->country, $e->getMessage() ) );
				}
				$country         = $country->getCreateFields();
				$country->states = $states ? array_map( function ( $state ) {
					return $state->getCreateFields();
				}, $states ) : null;
				$progress->tick();

				return $country;
			}, $countries );
		} catch ( \Exception $e ) {
			return [];
		}

		return $countries;
	}
}