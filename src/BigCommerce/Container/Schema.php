<?php


namespace BigCommerce\Container;


use BigCommerce\Accounts\Roles\Customer;
use BigCommerce\Schema\Import_Queue_Table;
use BigCommerce\Schema\Products_Table;
use BigCommerce\Schema\Reviews_Table;
use BigCommerce\Schema\User_Roles;
use BigCommerce\Schema\Variants_Table;
use Pimple\Container;

class Schema extends Provider {
	const TABLE_PRODUCTS = 'schema.table.products';
	const TABLE_VARIANTS = 'schema.table.variants';
	const TABLE_QUEUE    = 'schema.table.import_queue';
	const TABLE_REVIEWS  = 'schema.table.reviews';
	const ROLE_SCHEMA    = 'schema.roles';
	const CUSTOMER_ROLE  = 'schema.roles.customer';

	public function register( Container $container ) {
		$this->tables( $container );
		$this->roles( $container );
	}

	private function tables( Container $container ) {
		$container[ self::TABLE_PRODUCTS ] = function ( Container $container ) {
			return new Products_Table();
		};

		$container[ self::TABLE_VARIANTS ] = function ( Container $container ) {
			return new Variants_Table();
		};

		$container[ self::TABLE_REVIEWS ] = function ( Container $container ) {
			return new Reviews_Table();
		};

		$container[ self::TABLE_QUEUE ] = function ( Container $container ) {
			return new Import_Queue_Table();
		};

		add_action( 'plugins_loaded', $this->create_callback( 'tables_plugins_loaded', function () use ( $container ) {
			$container[ self::TABLE_PRODUCTS ]->register_tables();
			$container[ self::TABLE_VARIANTS ]->register_tables();
			$container[ self::TABLE_REVIEWS ]->register_tables();
			$container[ self::TABLE_QUEUE ]->register_tables();
		} ), 10, 0 );
	}

	private function roles( Container $container ) {
		$container[ self::CUSTOMER_ROLE ] = function ( Container $container ) {
			return new Customer();
		};
		$container[ self::ROLE_SCHEMA ]   = function ( Container $container ) {
			return new User_Roles( [
				$container[ self::CUSTOMER_ROLE ],
			] );
		};
		add_action( 'admin_init', $this->create_callback( 'init_roles', function () use ( $container ) {
			$container[ self::ROLE_SCHEMA ]->register_roles();
		} ), 10, 0 );
	}
}