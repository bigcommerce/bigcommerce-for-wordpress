<?php


namespace BigCommerce\Accounts;


use Bigcommerce\Api\Client;
use Bigcommerce\Api\Resource;
use Bigcommerce\Api\Resources\Address;
use Bigcommerce\Api\Resources\Order;
use Bigcommerce\Api\Resources\OrderProduct;
use BigCommerce\Import\Processors\Default_Customer_Group;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * Handles customer-specific data and resources such as customer addresses, profiles, orders, and related functionalities.
 *
 * This class interacts with the BigCommerce API to fetch and manage data for customers associated with WordPress users.
 */
class Customer {
	/**
	 * Constant for customer ID meta key.
	 *
	 * @var string
	 */
	const CUSTOMER_ID_META = 'bigcommerce_customer_id';

	/**
	 * The WordPress user ID associated with the customer.
	 *
	 * @var int
	 */
	private $wp_user_id = 0;

	/**
	 * Customer constructor.
	 *
	 * @param int $wp_user_id The WordPress user ID associated with the customer.
	 */
	public function __construct( $wp_user_id ) {
		$this->wp_user_id = $wp_user_id;
	}

	/**
	 * Get customer addresses.
	 *
	 * Fetches all the addresses associated with the customer.
	 *
	 * @return array An array of addresses associated with the customer.
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

	/**
	 * Delete a customer's address by address ID.
	 *
	 * Deletes the address associated with the provided address ID for the customer.
	 *
	 * @param int $address_id The ID of the address to be deleted.
	 *
	 * @return bool Returns true if the address was successfully deleted, false otherwise.
	 */
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

	/**
	 * Add a new address for the customer.
	 *
	 * Adds a new address to the customer's account.
	 *
	 * @param array $address The address data to be added.
	 *
	 * @return bool Returns true if the address was successfully added, false otherwise.
	 */
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

	/**
	 * Update a customer's address by address ID.
	 *
	 * Updates an existing address based on the provided address ID.
	 *
	 * @param int   $address_id The ID of the address to be updated.
	 * @param array $address    The updated address data.
	 *
	 * @return bool Returns true if the address was successfully updated, false otherwise.
	 */
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

	/**
	 * Get the number of orders for the customer.
	 *
	 * Returns the total count of orders placed by the customer.
	 *
	 * @return int The number of orders associated with the customer.
	 */
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
	 * Get the most recent orders of the customer.
	 *
	 * Returns a list of the most recent orders for the customer, each including at least one product.
	 * Note that this function makes multiple API calls and should be optimized for scalability in the future.
	 *
	 * @param int $page  The page number of results.
	 * @param int $limit The number of results per page.
	 *
	 * @return array A list of the most recent orders of the customer.
	 * @todo Optimize for scalability.
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

	/**
	 * Get detailed information for a specific customer order.
	 *
	 * Fetches detailed information about the specified order, including products and shipping addresses.
	 *
	 * @param int $order_id The ID of the order to retrieve.
	 *
	 * @return array|false An array of order details or false if the order is not found.
	 */
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

    /**
     * Retrieve order products list
     *
     * @param $order_id
     *
     * @return mixed|void
     */
	private function get_order_products( $order_id ) {
		$products = Client::getOrderProducts( $order_id ) ?: [];
		$products = array_filter( $products, function ( OrderProduct $product ) {
			$parent_product = $product->parent_order_product_id;

			return empty( $parent_product );
		} );
		$products = array_map( [ $this, 'flatten_resource' ], $products );

		/**
		 * Filters order products
		 *
		 * @param array    $products Products.
		 * @param int      $order_id Order ID.
		 * @param Customer $customer The Customer object.
		 */
		return apply_filters( 'bigcommerce/order/products', $products, $order_id, $this );
	}

    /**
     * Get order shipping address
     *
     * @param $order_id
     *
     * @return mixed|void
     */
	private function get_order_shipping_addresses( $order_id ) {
		$addresses = Client::getOrderShippingAddresses( $order_id ) ?: [];
		$addresses = array_map( [ $this, 'flatten_resource' ], $addresses );

		/**
		 * Filters order shipping addresses
		 *
		 * @param array    $addresses The shipping addresses.
		 * @param int      $order_id  Order ID.
		 * @param Customer $customer  The Customer object.
		 */
		return apply_filters( 'bigcommerce/order/shipping_addresses', $addresses, $order_id, $this );
	}

	private function flatten_resource( Resource $resource ) {
		$item       = get_object_vars( $resource->getCreateFields() );
		$item['id'] = $resource->id;

		return $item;
	}


	/**
	 * Get the profile data of the customer.
	 *
	 * Returns the customer's profile information, including fields like name, email, and customer group.
	 *
	 * @return array An array of customer profile data.
	 */
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

	/**
	 * Update the profile data of the customer.
	 *
	 * @param array $profile The profile data to update.
	 *
	 * @return bool Returns true if the profile was successfully updated, false otherwise.
	 */
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
	 * Get the customer ID linked to the current WordPress user.
	 *
	 * @return int The customer ID associated with the WordPress user.
	 */
	public function get_customer_id() {
		$customer_id = get_user_option( self::CUSTOMER_ID_META, $this->wp_user_id );

		return (int) $customer_id;
	}

	/**
	 * Set the customer ID linked to the current WordPress user.
	 *
	 * @param int $customer_id The customer ID to link to the WordPress user.
	 */
	public function set_customer_id( $customer_id ) {
		update_user_option( $this->wp_user_id, self::CUSTOMER_ID_META, $customer_id );
	}

	/**
	 * Get the customer group ID assigned to the user.
	 *
	 * @return int|null The group ID of the customer, or null if the user is a guest.
	 */
	public function get_group_id() {
		$customer_id = is_user_logged_in() || defined( 'DOING_CRON' ) ? get_user_option( self::CUSTOMER_ID_META, $this->wp_user_id ) : 0;
		if ( ! $customer_id ) {
			$default_guest_group = $this->get_guests_default_group();
			$default_guest_group = ! empty( $default_guest_group ) ? reset( $default_guest_group ) : null;
			/**
			 * This filter is documented in src/BigCommerce/Accounts/Customer.php
			 */
			return apply_filters( 'bigcommerce/customer/group_id', $default_guest_group ?: null, $this );
		}
		$transient_key = sprintf( 'bccustomergroup%d', $customer_id );
		$group_id      = get_transient( $transient_key );

		if ( empty( $group_id ) ) {
			// Couldn't find in cache, retrieve from the API
			$profile    = $this->get_profile();
			$group_id   = isset( $profile['customer_group_id'] ) ? absint( $profile['customer_group_id'] ) : 0;
			$expiration = get_option( Troubleshooting_Diagnostics::USERS_TRANSIENT, 12 * HOUR_IN_SECONDS ); // TODO: a future webhook to flush this cache when the customer's group changes
			if ( $group_id === 0 ) {
				$default_group = get_option( Default_Customer_Group::DEFAULT_GROUP, 0 );
				$value = $default_group ?: 'zero';

				set_transient( $transient_key, $value, $expiration ); // workaround for storing empty values in cache
			} else {
				set_transient( $transient_key, $group_id, $expiration );
			}
		}

		if ( $group_id === 'zero' ) {
			$group_id = get_option( Default_Customer_Group::DEFAULT_GROUP, 0 );
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
	 * Get the default group for guest users.
	 *
	 * @return array|null An array of default customer group IDs for guests, or null if none are found.
	 */
	public function get_guests_default_group() {
		$args  = [
			'is_group_for_guests' => true,
		];
		$query = '?' . http_build_query( $args );

		$customer_groups = Client::getCustomerGroups( $query );

		if ( empty( $customer_groups ) ) {
			/**
			 * Log information about empty customer groups.
			 *
			 * @param string $level The log level (INFO)
			 * @param string $message The log message
			 * @param array $context Additional context data
			 */
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Customer groups are empty', 'bigcommerce' ), [] );
			return null;
		}
		$connections = new Connections();
		$channel     = $connections->current();
		$channel_id  = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
		return array_filter( array_map( function ( $group ) use ( $channel_id ) {
			// Exit if group is not default
			if ( ( int ) $group->channel_id !== ( int ) $channel_id ) {
				return;
			}

			return $group->id;
		}, $customer_groups ) );
	}

	/**
	 * Get the customer group for the current customer.
	 *
	 * @return Customer_Group The customer group object associated with the customer.
	 */
	public function get_group() {
		return new Customer_Group( $this->get_group_id() );
	}
}
