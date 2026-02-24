<?php


namespace BigCommerce\CLI;

use WP_CLI;

/**
 * This is an abstract base class for creating custom WP-CLI commands in the BigCommerce CLI namespace.
 * It registers the command with WP-CLI and provides methods for handling command-specific logic.
 *
 * @package BigCommerce\CLI
 */
abstract class Command extends \WP_CLI_Command {

    /**
     * Register the command with WP-CLI.
     *
     * This method checks if WP-CLI is defined and active. If so, it registers the command
     * with WP-CLI, using the specific command name defined in the child class.
     *
     * @return void
     */
    public function register() {
        if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
            return;
        }

        WP_CLI::add_command( 'bigcommerce ' . $this->command(), [ $this, 'run' ], [
            'shortdesc' => $this->description(),
            'synopsis'  => $this->arguments(),
        ] );
    }

    /**
     * Get the command name.
     *
     * This method must be implemented in the child class to return the specific command name.
     *
     * @return string The name of the WP-CLI command.
     */
    abstract protected function command();

    /**
     * Get a short description of the command.
     *
     * This method must be implemented in the child class to provide a brief description
     * of the command's functionality, which will be shown in the WP-CLI help output.
     *
     * @return string The short description of the command.
     */
    abstract protected function description();

    /**
     * Get the command arguments.
     *
     * This method must be implemented in the child class to return the arguments
     * required by the command. The arguments will be shown in the WP-CLI help output.
     *
     * @return string The arguments for the command.
     */
    abstract protected function arguments();

    /**
     * Run the command.
     *
     * This method must be implemented in the child class to define the logic
     * for executing the command. It accepts arguments and associative arguments
     * passed to the command via WP-CLI.
     *
     * @param array $args The positional arguments passed to the command.
     * @param array $assoc_args The associative arguments passed to the command.
     * @return void
     */
    abstract public function run( $args, $assoc_args );

}