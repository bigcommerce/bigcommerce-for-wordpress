<?php


namespace BigCommerce\Shortcodes;

use BigCommerce\Accounts\Customer;
use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Rest\Orders_Shortcode_Controller;
use BigCommerce\Settings\Sections\Currency;
use BigCommerce\Templates;
use BigCommerce\Templates\Order_Summary;

/**
 * Provides a shortcode to display the order history for logged-in users.
 * Handles rendering both the order history list and individual order details.
 */
class Order_History implements Shortcode {

	/**
	 * The name of the shortcode for order history.
	 * @var string
	 */
	const NAME = 'bigcommerce_order_history';

	/**
	 * Query argument key for specifying the order ID.
	 * @var string
	 */
	const ORDER_ID_QUERY_ARG = 'order_id';

	/** @var Orders_Shortcode_Controller */
	private $rest_controller;

	/**
	 * Constructor for the Order_History class.
	 *
	 * @param Orders_Shortcode_Controller $rest_controller The REST controller used for AJAX requests.
	 */
	public function __construct( $rest_controller ) {
		$this->rest_controller = $rest_controller;
	}

	/**
	 * Get the default attributes for the order history shortcode.
	 *
	 * @return array Default attributes including pagination, per-page limits, and AJAX settings.
	 */
	public static function default_attributes() {
		return [
			'paged'    => 1, // 1 to enable pagination
			'per_page' => 0, // number of orders to show at a time
			'ajax'     => 0, // internal use: set to 1 for ajax pagination requests
		];
	}

	/**
	 * Render the order history or order details based on the attributes and query parameters.
	 *
	 * @param array $attr     Shortcode attributes.
	 * @param mixed $instance The current instance (not used).
	 *
	 * @return string The rendered HTML output or an empty string for unauthorized users.
	 */
	public function render( $attr, $instance ) {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$attr             = shortcode_atts( self::default_attributes(), $attr, self::NAME );
		$attr['per_page'] = $attr['per_page'] ?: $this->per_page_default();

		if ( empty( $_GET[ self::ORDER_ID_QUERY_ARG ] ) ) {
			return $this->render_history_list( $attr );
		} else {
			return $this->render_order_details( $attr, (int) $_GET[ self::ORDER_ID_QUERY_ARG ] );
		}

	}

	private function render_history_list( $attr ) {
		$orders      = $this->get_orders( $attr );
		$total_pages = empty( $orders ) ? 0 : $this->get_total_pages( $attr['per_page'] );

		$controller = Templates\Order_History::factory( [
			Templates\Order_History::ORDERS        => $orders,
			Templates\Order_History::NEXT_PAGE_URL => $this->next_page_url( $attr, $total_pages ),
			Templates\Order_History::WRAP          => intval( $attr['ajax'] ) !== 1,
		] );

		return $controller->render();
	}

	private function render_order_details( $attr, $order_id ) {
		$customer = new Customer( get_current_user_id() );
		$order    = $customer->get_order_details( $order_id );
		if ( empty( $order ) ) {
			$controller = Templates\Order_Not_Found::factory( [] );

			return $controller->render();
		}

		$currency_filter = function ( $currency ) use ( $order ) {
			if ( empty( $order['currency_code'] ) ) {
				return $currency;
			}

			return $order['currency_code'];
		};

		add_filter( 'pre_option_' . Currency::CURRENCY_CODE, $currency_filter, 100, 1 );
		$controller = Templates\Order_Details::factory( [ Templates\Order_Details::ORDER => $order ] );
		$output = $controller->render();
		remove_filter( 'pre_option_' . Currency::CURRENCY_CODE, $currency_filter, 100 );

		return $output;
	}

	private function get_orders( $args ) {
		$orders   = [];
		$customer = new Customer( get_current_user_id() );
		foreach ( $customer->get_orders( $args['paged'], $args['per_page'] ) as $order ) {
			$currency_filter = function ( $currency ) use ( $order ) {
				if ( empty( $order['currency_code'] ) ) {
					return $currency;
				}

				return $order['currency_code'];
			};
			$component = Order_Summary::factory( [
				Order_Summary::ORDER => $order,
			] );
			
			add_filter( 'pre_option_' . Currency::CURRENCY_CODE, $currency_filter, 100, 1 );
			$orders[]  = $component->render();
			remove_filter( 'pre_option_' . Currency::CURRENCY_CODE, $currency_filter, 100 );
		}

		return $orders;
	}

	private function get_total_pages( $per_page ) {
		$customer = new Customer( get_current_user_id() );
		$count    = $customer->get_order_count();

		return ceil( $count / $per_page );
	}

	private function next_page_url( array $attr, $max_pages ) {
		$page = (int) $attr['paged'];
		if ( $page >= $max_pages ) {
			return '';
		}

		$base_url = trailingslashit( $this->rest_controller->get_base_url() ) . 'html';

		$attr['paged'] = $page + 1;
		$attr['ajax']  = 1;

		$url = add_query_arg( array_filter( $attr ), $base_url );
		$url = wp_nonce_url( $url, 'wp_rest' );

		return $url;
	}

	private function per_page_default() {
		$default = get_option( Product_Archive::PER_PAGE, Product_Archive::PER_PAGE_DEFAULT );

		return absint( $default ) ?: Product_Archive::PER_PAGE_DEFAULT;
	}

}