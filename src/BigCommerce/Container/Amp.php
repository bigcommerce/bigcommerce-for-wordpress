<?php


namespace BigCommerce\Container;


use BigCommerce\Amp\Amp_Controller_Factory;
use BigCommerce\Amp\Amp_Template_Override;
use Pimple\Container;

class Amp extends Provider {
	const TEMPLATE_OVERRIDE  = 'amp.template_override';
	const TEMPLATE_DIRECTORY = 'amp.template_directory';
	const FACTORY_OVERRIDE   = 'amp.controller_factory_override';

	public function register( Container $container ) {

		$container[ self::TEMPLATE_DIRECTORY ] = function ( Container $container ) {
			/**
			 * Filter the name of the AMP template directory
			 *
			 * @param string $directory The base name of the template directory
			 */
			return apply_filters( 'bigcommerce/amp/templates/directory', 'amp' );
		};

		$container[ self::TEMPLATE_OVERRIDE ] = function ( Container $container ) {
			return new Amp_Template_Override( $container[ self::TEMPLATE_DIRECTORY ] );
		};

		$container[ self::FACTORY_OVERRIDE ] = function ( Container $container ) {
			return new Amp_Controller_Factory();
		};

		$template_override = $this->create_callback( 'template_override', function ( $path, $relative_path ) use ( $container ) {
			return $container[ self::TEMPLATE_OVERRIDE ]->override_template_path( $path, $relative_path );
		} );

		$controller_factory_override = $this->create_callback( 'controller_factory_override', function ( $factory, $classname ) use ( $container ) {
			return $container[ self::FACTORY_OVERRIDE ];
		} );

		add_action( 'wp', $this->create_callback( 'init_template_override', function ( $wp ) use ( $template_override, $controller_factory_override ) {
			/**
			 * Toggles whether AMP template overrides will be used to render plugin templates
			 *
			 * @param bool $enable Whether AMP template overrides are enabled
			 */
			if ( apply_filters( 'bigcommerce/amp/templates/enable_override', false ) ) {
				add_filter( 'bigcommerce/template/path', $template_override, 5, 2 );
				add_filter( 'bigcommerce/template/controller_factory', $controller_factory_override, 10, 2 );
			}
		} ), 10, 1 );
	}
}