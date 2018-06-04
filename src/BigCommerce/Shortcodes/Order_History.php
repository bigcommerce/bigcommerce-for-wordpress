<?php


namespace BigCommerce\Shortcodes;

use BigCommerce\Accounts\Customer;
use Bigcommerce\Api\Client;
use BigCommerce\Customizer\Sections\Catalog;
use BigCommerce\Rest\Orders_Shortcode_Controller;
use BigCommerce\Templates;
use BigCommerce\Templates\Order_Summary;

class Order_History implements Shortcode {

	const NAME = 'bigcommerce_order_history';

	const ORDER_ID_QUERY_ARG = 'order_id';

	/** @var Orders_Shortcode_Controller */
	private $rest_controller;

	public function __construct( $rest_controller ) {
		$this->rest_controller = $rest_controller;
	}

	public static function default_attributes() {
		return [
			'paged'    => 1, // 1 to enable pagination
			'per_page' => 0, // number of orders to show at a time
			'ajax'     => 0, // internal use: set to 1 for ajax pagination requests
		];
	}

	public function render( $attr, $instance ) {

		$attr               = shortcode_atts( self::default_attributes(), $attr, self::NAME );
		$attr[ 'per_page' ] = $attr[ 'per_page' ] ?: $this->per_page_default();

		if ( empty( $_GET[ self::ORDER_ID_QUERY_ARG ] ) ) {
			return $this->render_history_list( $attr );
		} else {
			return $this->render_order_details( $attr, (int) $_GET[ self::ORDER_ID_QUERY_ARG ] );
		}

	}

	private function render_history_list( $attr ) {
		$orders      = $this->get_orders( $attr );
		$total_pages = empty( $orders ) ? 0 : $this->get_total_pages( $attr[ 'per_page' ] );

		$controller = new Templates\Order_History( [
			Templates\Order_History::ORDERS        => $orders,
			Templates\Order_History::NEXT_PAGE_URL => $this->next_page_url( $attr, $total_pages ),
			Templates\Order_History::WRAP          => intval( $attr[ 'ajax' ] ) !== 1,
		] );

		return $controller->render();
	}

	private function render_order_details( $attr, $order_id ) {
		$customer = new Customer( get_current_user_id() );
		$order = $customer->get_order_details( $order_id );
		if ( empty( $order ) ) {
			$controller = new Templates\Order_Not_Found( [] );

			return $controller->render();
		}

		$controller = new Templates\Order_Details( [ Templates\Order_Details::ORDER => $order ] );

		return $controller->render();
	}

	private function get_orders( $args ) {
		$orders   = [];
		$customer = new Customer( get_current_user_id() );
		foreach ( $customer->get_orders( $args[ 'paged' ], $args[ 'per_page' ] ) as $order ) {
			$component = new Order_Summary( [
				Order_Summary::ORDER => $order,
			] );
			$orders[]  = $component->render();
		}

		return $orders;
	}

	private function get_total_pages( $per_page ) {
		$customer = new Customer( get_current_user_id() );
		$count    = $customer->get_order_count();

		return ceil( $count / $per_page );
	}

	private function next_page_url( array $attr, $max_pages ) {
		$page = (int) $attr[ 'paged' ];
		if ( $page >= $max_pages ) {
			return '';
		}

		$base_url = trailingslashit( $this->rest_controller->get_base_url() ) . 'html';

		$attr[ 'paged' ] = $page + 1;
		$attr[ 'ajax' ]  = 1;

		$url = add_query_arg( array_filter( $attr ), $base_url );
		$url = wp_nonce_url( $url, 'wp_rest' );

		return $url;
	}

	private function per_page_default() {
		$default = get_option( Catalog::PER_PAGE, Catalog::PER_PAGE_DEFAULT );

		return absint( $default ) ?: Catalog::PER_PAGE_DEFAULT;
	}

}