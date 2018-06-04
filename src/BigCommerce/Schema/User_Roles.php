<?php


namespace BigCommerce\Schema;


use BigCommerce\Accounts\Roles\Role;

class User_Roles extends Schema {
	protected $schema_version = '0.1.0';

	/** @var array Role[] */
	private $roles = [];

	public function __construct( array $roles ) {
		$this->roles = $roles;
	}

	/**
	 * Register tables with WordPress, and create them if needed
	 *
	 * @return void
	 *
	 * @action init
	 */
	public function register_roles() {
		// create the roles
		if ( $this->schema_update_required() ) {
			array_walk( $this->roles, function ( Role $role ) {
				add_role( $role->get_id(), $role->get_label() );
			} );
			$this->mark_schema_update_complete();
		}
	}
}