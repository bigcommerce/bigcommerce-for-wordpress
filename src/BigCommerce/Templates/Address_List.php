<?php


namespace BigCommerce\Templates;


use BigCommerce\Accounts\Customer;

class Address_List extends Controller {
	const USER_ID = 'user_id';

	const ADDRESSES   = 'addresses';
	const NEW_ADDRESS = 'new_address';

	protected $template = 'components/accounts/address-list.php';
	protected $wrapper_tag = 'section';
	protected $wrapper_classes = [ 'bc-account-addresses' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-account-addresses' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::USER_ID => get_current_user_id(),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::ADDRESSES   => $this->get_addresses(),
			self::NEW_ADDRESS => $this->get_new_address(),
		];
	}

	private function get_addresses() {
		$addresses = [];
		$customer  = new Customer( $this->options[ self::USER_ID ] );
		foreach ( $customer->get_addresses() as $address ) {
			$formatted   = Address_Formatted::factory( $address );
			$actions     = Address_Actions::factory( [
				Address_Actions::ADDRESS => $address,
			] );
			$addresses[] = [
				'formatted' => $formatted->render(),
				'actions'   => $actions->render(),
			];
		}

		return $addresses;
	}

	private function get_new_address() {
		$new = Address_New::factory( [] );

		return $new->render();
	}

}
