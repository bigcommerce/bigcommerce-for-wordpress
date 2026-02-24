<?php


namespace BigCommerce\CLI\Documentation;


use BigCommerce\CLI\Command;
use WP_CLI;

/**
 * Register build docs command and related functionality
 *
 * @package BigCommerce\CLI\Documentation
 */
class Build_Docs extends Command {
    /**
     * @var string The directory path of the plugin being documented.
     */
    private $plugin_dir;

    /**
     * Constructor for the Build_Docs class.
     *
     * @param string $plugin_dir Directory path of the plugin.
     */
    public function __construct( $plugin_dir ) {
        $this->plugin_dir = $plugin_dir;
    }

    /**
     * Declare command name.
     *
     * @return string The CLI command name.
     */
    protected function command() {
        return 'docs build';
    }

    /**
     * Provide a command description.
     *
     * @return string|void The CLI command description.
     */
    protected function description() {
        return __( 'Builds plugin documentation', 'bigcommerce' );
    }

    /**
     * Declare command arguments.
     *
     * @return array[] List of command arguments.
     */
    protected function arguments() {
        return [
            [
                'type'        => 'positional',
                'name'        => 'file',
                'optional'    => false,
                'description' => __( 'Path to the JSON file to export', 'bigcommerce' ),
            ],
        ];
    }

    /**
     * Get files data and write it to the provided file.
     *
     * @param array $args Positional arguments passed to the command.
     * @param array $assoc_args Associative arguments passed to the command.
     *
     * @throws WP_CLI\ExitException If there is an issue during execution.
     * @return void
     */
	public function run( $args, $assoc_args ) {
		if ( ! function_exists( '\WP_Parser\parse_files' ) ) {
			WP_CLI::error( __( 'Please install and activate WP Parser from https://github.com/WordPress/phpdoc-parser before building documentation.', 'bigcommerce' ) );
			exit;
		}
		$data        = $this->get_data();
		$json        = wp_json_encode( $data, JSON_PRETTY_PRINT );
		$output_file = reset( $args );
		$result      = file_put_contents( $output_file, $json );
		WP_CLI::line();

		if ( false === $result ) {
			WP_CLI::error( sprintf( 'Problem writing %1$s bytes of data to %2$s', strlen( $json ), $output_file ) );
			exit;
		}

		WP_CLI::success( sprintf( 'Data exported to %1$s', $output_file ) );
		WP_CLI::line();
	}

    /**
     * Get a list of files and parse files with wp-parser
     *
     * @return array|void
     *
     * @throws WP_CLI\ExitException
     */
	private function get_data() {

		WP_CLI::line( sprintf( 'Extracting PHPDoc from %1$s. This may take a few minutes...', $this->plugin_dir ) );
		$files = $this->collect_files();

		if ( $files instanceof \WP_Error ) {
			WP_CLI::error( sprintf( 'Problem with %1$s: %2$s', $this->plugin_dir, $files->get_error_message() ) );
			exit;
		}

		$output = \WP_Parser\parse_files( $files, $this->plugin_dir );

		return $output;
	}

    /**
     * Get a recursive list of files for plugin
     *
     * @return array|\WP_Error
     */
	private function collect_files() {
		$directory = new \RecursiveDirectoryIterator( $this->plugin_dir, \FilesystemIterator::FOLLOW_SYMLINKS );
		$filter    = new \RecursiveCallbackFilterIterator( $directory, function ( $current, $key, $iterator ) {
			// Skip hidden files and directories
			if ( $current->getFilename()[ 0 ] === '.' ) {
				return false;
			}
			if ( $current->isDir() ) {
				return ! in_array( $current->getFilename(), [
					'assets',
					'deploy',
					'dev_components',
					'grunt_options',
					'node_modules',
					'tests',
					'vendor',
				] );
			}

			return $current->getExtension() === 'php';
		} );
		$iterator  = new \RecursiveIteratorIterator( $filter );
		$files     = [];

		try {
			foreach ( $iterator as $file ) {
				$files[] = $file->getPathname();
			}
		} catch ( \UnexpectedValueException $exc ) {
			return new \WP_Error(
				'unexpected_value_exception',
				sprintf( 'Directory [%s] contained a directory we can not recurse into', $directory )
			);
		}

		return $files;
	}
}
