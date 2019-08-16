<?php


namespace BigCommerce\Accounts;


use Bigcommerce\Api\Client;
use Bigcommerce\Api\Resource;
use Bigcommerce\Api\Resources\Address;
use Bigcommerce\Api\Resources\Order;
use Bigcommerce\Api\Resources\OrderProduct;

class Customer {
	const CUSTOMER_ID_META = 'bigcommerce_customer_id';

	private $wp_user_id = 0;

	public function __construct( $wp_user_id ) {
		$this->wp_user_id = $wp_user_id;
	}

	/**
	 * @return array
	 */
	public function get_addresses() {
		$customer_id = $this->get_customer_id();
		if ( empty( $customer_id ) ) {
			return [];
		}

		try {
			$addresses = Client::getCustomerAddresses( $customer_id ) ?: [];
			$addresses = array_map( function ( Address $address ) {
				return get_object_vars( $address->getCreateFields() );
			}, $addresses );

			return $addresses;
		} catch ( \Exception $e ) {
			return [];
		}
	}

	public function delete_address( $address_id ) {
		$customer_id = $this->get_customer_id();
		if ( ! $customer_id ) {
			return false;
		}
		try {
			Client::deleteResource( sprintf( '/customers/%d/addresses/%d', $customer_id, $address_id ) );

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	public function add_address( $address ) {
		$customer_id = $this->get_customer_id();
		if ( ! $customer_id ) {
			return false;
		}
		try {
			$result = Client::createCustomerAddress( $customer_id, $address );

			return ! empty( $result );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	public function update_address( $address_id, $address ) {
		$customer_id = $this->get_customer_id();
		if ( ! $customer_id ) {
			return false;
		}
		try {
			Client::updateResource( sprintf( '/customers/%d/addresses/%d', $customer_id, $address_id ), $address );

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	public function get_order_count() {
		$customer_id = $this->get_customer_id();
		if ( ! $customer_id ) {
			return 0;
		}
		try {
			$count = Client::getOrdersCount( [
				'customer_id' => $customer_id,
			] );

			return (int) $count;
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	/**
	 * Get the most recent orders on the account. Each will include
	 * at least one product (useful for finding featured images),
	 * but is not guaranteed to include all.
	 *
	 * WARNING: This function is heavy on API calls. One call for the
	 * order list, plus another for each order in the list.
	 *
	 * @param int $page
	 * @param int $limit
	 *
	 * @return array
	 * @todo Optimize for scalability
	 *
	 */
	public function get_orders( $page = 1, $limit = 12 ) {
		$customer_id = $this->get_customer_id();
		if ( ! $customer_id ) {
			return [];
		}

		try {
			$orders = Client::getOrders( [
				'customer_id' => $customer_id,
				'sort'        => 'date_created:desc',
				'limit'       => $limit,
				'page'        => $page,
			] ) ?: [];
			$orders = array_map( function ( Order $order ) {
				$products = $this->get_order_products( $order->id ) ?: [];

				$order = get_object_vars( $order->getCreateFields() );

				$order['products'] = $products;

				return $order;
			}, $orders );

			return $orders;
		} catch ( \Exception $e ) {
			return [];
		}
	}

	public function get_order_details( $order_id ) {
		$order = Client::getOrder( $order_id );
		if ( empty( $order ) || $order->customer_id != $this->get_customer_id() ) {
			return false;
		}
		$data                       = $this->flatten_resource( $order );
		$data['products']           = $this->get_order_products( $order_id );
		$data['shipping_addresses'] = $this->get_order_shipping_addresses( $order_id );
		$data['shipments']          = $order->shipments() ?: [];
		$data['coupons']            = $order->coupons() ?: [];

		return $data;
	}

	private function get_order_products( $order_id ) {
		$products = Client::getOrderProducts( $order_id ) ?: [];
		$products = array_filter( $products, function ( OrderProduct $product ) {
			$parent_product = $product->parent_order_product_id;

			return empty( $parent_product );
		} );
		$products = array_map( [ $this, 'flatten_resource' ], $products );

		return apply_filters( 'bigcommerce/order/products', $products, $order_id, $this );
	}

	private function get_order_shipping_addresses( $order_id ) {
		$addresses = Client::getOrderShippingAddresses( $order_id ) ?: [];
		$addresses = array_map( [ $this, 'flatten_resource' ], $addresses );

		return apply_filters( 'bigcommerce/order/shipping_addresses', $addresses, $order_id, $this );
	}

	private function flatten_resource( Resource $resource ) {
		$item       = get_object_vars( $resource->getCreateFields() );
		$item['id'] = $resource->id;

		return $item;
	}

	public function get_profile() {
		$customer_id = $this->get_customer_id();
		if ( ! $customer_id ) {
			return [];
		}
		/**
		 * Filter the base fields found in a customer profile
		 *
		 * @param array $fields
		 */
		$empty_profile = apply_filters( 'bigcommerce/customer/empty_profile', [
			'first_name'        => '',
			'last_name'         => '',
			'company'           => '',
			'email'             => '',
			'phone'             => '',
			'customer_group_id' => 0,
		] );
		try {
			$profile = Client::getCustomer( $customer_id );
			if ( ! $profile ) {
				return $empty_profile;
			}

			return array_filter( get_object_vars( $profile->getCreateFields() ), function ( $key ) use ( $empty_profile ) {
				return array_key_exists( $key, $empty_profile );
			}, ARRAY_FILTER_USE_KEY );
		} catch ( \Exception $e ) {
			return $empty_profile;
		}
	}

	public function update_profile( $profile ) {
		$customer_id = $this->get_customer_id();
		if ( ! $customer_id ) {
			return false;
		}
		try {
			Client::updateCustomer( $customer_id, $profile );

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * @return int The ID of the customer account linked to this user
	 */
	public function get_customer_id() {
		$customer_id = get_user_option( self::CUSTOMER_ID_META, $this->wp_user_id );

		return (int) $customer_id;
	}

	/**
	 * @param int $customer_id The customer ID to link to this user
	 *
	 * @return void
	 */
	public function set_customer_id( $customer_id ) {
		update_user_option( $this->wp_user_id, self::CUSTOMER_ID_META, $customer_id );
	}

	/**
	 * Get the customer group ID assigned to the user.
	 * Value will be fetched from cache if available,
	 * otherwise from the API.
	 *
	 * @return int|null
	 */
	public function get_group_id() {
		$customer_id = is_user_logged_in() ? get_user_option( self::CUSTOMER_ID_META, $this->wp_user_id ) : 0;
		if ( ! $customer_id ) {
			/**
			 * This filter is documented in src/BigCommerce/Accounts/Customer.php
			 */
			return apply_filters( 'bigcommerce/customer/group_id', null, $this );
		}
		$transient_key = sprintf( 'bccustomergroup%d', $customer_id );
		$group_id      = get_transient( $transient_key );

		if ( empty( $group_id ) ) {
			// Couldn't find in cache, retrieve from the API
			$profile    = $this->get_profile();
			$group_id   = isset( $profile['customer_group_id'] ) ? absint( $profile['customer_group_id'] ) : 0;
			$expiration = HOUR_IN_SECONDS; // TODO: a future webhook to flush this cache when the customer's group changes
			if ( $group_id === 0 ) {
				set_transient( $transient_key, 'zero', $expiration ); // workaround for storing empty values in cache
			} else {
				set_transient( $transient_key, $group_id, $expiration );
			}
		}

		if ( $group_id === 'zero' ) {
			$group_id = 0;
		}

		/**
		 * Filter the group ID associated with the customer
		 *
		 * @param int|null $group_id The customer's group ID. Null for guest users.
		 * @param Customer $customer The Customer object
		 */
		$group_id = apply_filters( 'bigcommerce/customer/group_id', $group_id, $this );

		return absint( $group_id );
	}

	/**
	 * Get the customer group associated with this customer
	 *
	 * @return Customer_Group
	 */
	public function get_group() {
		return new Customer_Group( $this->get_group_id() );
	}
}
