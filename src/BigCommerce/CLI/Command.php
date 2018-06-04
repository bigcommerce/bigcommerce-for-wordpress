<?php


namespace BigCommerce\CLI;

use WP_CLI;

abstract class Command extends \WP_CLI_Command {

	public function register() {
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			return;
		}

		WP_CLI::add_command( 'bigcommerce ' . $this->command(), [ $this, 'run' ], [
			'shortdesc' => $this->description(),
			'synopsis'  => $this->arguments(),
		] );
	}

	abstract protected function command();

	abstract protected function description();

	abstract protected function arguments();

	abstract public function run( $args, $assoc_args );

}