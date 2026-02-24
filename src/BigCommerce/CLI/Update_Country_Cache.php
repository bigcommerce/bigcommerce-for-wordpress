<?php


namespace BigCommerce\CLI;


use Bigcommerce\Api\Client;

/**
 * Updates the country and state cache in a JSON file located at assets/data/countries.json.
 * This command fetches the latest country and state data from the BigCommerce API and stores it in a specified output file.
 * If no output file is provided, it defaults to a predefined file path.
 *
 * Usage: wp bigcommerce countries update --output=/path/to/file.json
 *
 * @package BigCommerce
 * @subpackage CLI
 */
class Update_Country_Cache extends Command {
    private $default_output_file = '';

    /**
     * Constructor to initialize the default output file.
     *
     * @param string $default_output_file The default file path for the JSON output.
     */
    public function __construct( $default_output_file ) {
        $this->default_output_file = $default_output_file;
    }

    /**
     * Declare the WP-CLI command for updating the country cache.
     *
     * @return string The WP-CLI command to execute.
     */
    protected function command() {
        return 'countries update';
    }

    /**
     * Add a description for the WP-CLI command.
     *
     * @return string The description of the command.
     */
    protected function description() {
        return __( 'Update the cache of countries and states in countries.json', 'bigcommerce' );
    }

    /**
     * Declare the command arguments for the update cache operation.
     *
     * @return array[] The command arguments, including an optional output file argument.
     */
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

    /**
     * Executes the update process for the country and state cache.
     * Fetches data from the BigCommerce API and writes it to the specified output file.
     *
     * @param array $args Arguments passed to the command.
     * @param array $assoc_args Associated arguments (e.g., output file path).
     *
     * @throws \WP_CLI\ExitException If there is an error in the process, an exit exception is thrown.
     */
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

    /**
     * Fetches the country and state data from the BigCommerce API.
     * Retrieves a list of countries and their respective states, if available.
     *
     * @return array An array of countries with associated state data.
     */
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
