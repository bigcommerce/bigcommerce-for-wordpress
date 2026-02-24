<?php


namespace BigCommerce\CLI\Resources;


use BigCommerce\CLI\Command;
use League\Csv\Reader;
use WP_CLI;

/**
 * Class Build_Resources
 *
 * Builds plugin resources JSON from a CSV file containing resource data.
 *
 * @package BigCommerce\CLI\Resources
 */
class Build_Resources extends Command {
    /**
     * @var string Directory of the plugin being processed.
     */
    private $plugin_dir;

    /**
     * Build_Resources constructor.
     *
     * @param string $plugin_dir Directory of the plugin.
     */
    public function __construct( $plugin_dir ) {
        $this->plugin_dir = $plugin_dir;
    }

    /**
     * Declare the command name.
     *
     * @return string
     */
    protected function command() {
        return 'resources build';
    }

    /**
     * Provide a command description.
     *
     * @return string
     */
    protected function description() {
        return __( 'Builds plugin resources JSON from a CSV file', 'bigcommerce' );
    }

    /**
     * Declare command arguments.
     *
     * @return array[]
     */
    protected function arguments() {
        return [
            [
                'type'        => 'positional',
                'name'        => 'file',
                'optional'    => false,
                'description' => __( 'Path to the CSV file with resource data', 'bigcommerce' ),
            ],
            [
                'type'        => 'flag',
                'name'        => 'pretty',
                'optional'    => true,
                'description' => __( 'Apply JSON pretty formatting to the output', 'bigcommerce' ),
            ],
        ];
    }

    /**
     * Execute the resource build process from the provided CSV file.
     *
     * @param array $args       Positional arguments passed to the command.
     * @param array $assoc_args Associative arguments passed to the command.
     *
     * @throws WP_CLI\ExitException
     */
    public function run( $args, $assoc_args ) {
        $path = $args[ 0 ];
        if ( ! file_exists( $path ) || ! is_readable( $path ) ) {
            \WP_CLI::error( __( 'Unable to read input file.', 'bigcommerce' ) );
        }

        if ( ! class_exists( 'League\Csv\Reader' ) ) {
			\WP_CLI::error( __( 'Missing league/csv library. Unable to build resources json.', 'bigcommerce' ) );
		}

		$csv  = Reader::createFromPath( $path, 'r' );
		$keys = $csv->fetchOne();
		$csv->setOffset( 1 );
        $groups = [];
        foreach ( $csv->fetchAssoc( $keys ) as $record ) {
            if ( ! array_key_exists( $record[ 'Tab' ], $groups ) ) {
                $groups[ $record[ 'Tab' ] ] = new Resource_Group( $record[ 'Tab' ] );
            }

            $groups[ $record[ 'Tab' ] ]->add_resource( ( new Resource() )
                ->set_name( $record[ 'Name' ] )
                ->set_description( $record[ 'Description' ] )
                ->set_url( $record[ 'URL' ] )
                ->set_thumbnail( $record[ 'Thumbnail' ] )
                ->set_hires_thumbnail( $record[ 'HiRes Thumbnail' ] )
                ->set_categories( array_filter( explode( ',', $record[ 'Categories' ] ) ) )
            );
        }

        $output = [
            'version'  => 1,
            'sections' => array_values( $groups ),
        ];

        $flags = 0;
        if ( WP_CLI\Utils\get_flag_value( $assoc_args, 'pretty', false ) ) {
            $flags |= JSON_PRETTY_PRINT;
        }

        echo wp_json_encode( $output, $flags );

    }
}
