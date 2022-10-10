<?php
/**
 * Provides expanded cart data drawing from the proxy and integrating WP-stored data.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Proxy;

use WP_REST_Controller, WP_REST_Server, WP_REST_Request, WP_Query;
use BigCommerce\Cart\Cart;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Assets\Theme\Image_Sizes;

/**
 * AMP_Cart_Controller class
 */
class AMP_Cart_Controller extends Proxy_Controller {
	/**
	 * Init endpoint.
	 *
	 * @return void
	 */
	public function register_routes() {
		$public_args = [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_items' ],
			'permission_callback' => '__return_true',
		];

		register_rest_route( $this->proxy_base, '/amp-cart/', $public_args );
	}

	/**
	 * Adds additional data to a single item.
	 *
	 * @param array $item Cart item.
	 * @return array Updated data.
	 */
	private function build_item( $item ) {
		$query = new WP_Query(
			[
				'post_type'      => Product::NAME,
				'posts_per_page' => 1,
				'meta_key'       => 'bigcommerce_id',
				'meta_value'     => $item['product_id'],
			]
		);

		if ( is_wp_error( $query ) || ! $query->have_posts() ) {
			return $item;
		}

		$post = reset( $query->posts );

		$product           = new Product( $post->ID );
		$item['permalink'] = amp_get_permalink( $post->ID );
		$thumbnail_id      = get_post_thumbnail_id( $post->ID );

		$thumbnail = get_the_post_thumbnail( $post->ID, Image_Sizes::BC_SMALL );
		if ( $thumbnail_id ) {
			$image                    = wp_get_attachment_image_src( $thumbnail_id, Image_Sizes::BC_SMALL );
			$item['thumbnail_src']    = str_replace( home_url(), '', $image[0] );
			$item['thumbnail_height'] = $image[2];
			$item['thumbnail_width']  = $image[1];
			$item['thumbnail_srcset'] = wp_get_attachment_image_srcset( $thumbnail_id, Image_Sizes::BC_SMALL );
		}

		$brand = $product->brand();

		if ( $brand ) {
			$item['brand'] = $brand;
		}

		$item['price'] = apply_filters(
			'bigcommerce/currency/format',
			sprintf( '¤%0.2f', $item['sale_price'] ),
			$item['sale_price']
		);

		return $item;
	}

	/**
	 * Adds additional data to items.
	 *
	 * @param array $data Array of items.
	 * @return array Modified array.
	 */
	private function build_items( $data ) {
		// Extract items from physical_items, digital_items, etc.
		$items = array_reduce(
			array_keys( $data['line_items'] ),
			function( $list, $key ) use ( $data ) {
				return $list + (array) $data['line_items'][ $key ];
			},
			[]
		);

		$items = array_map( [ $this, 'build_item' ], $items );

		return $items;
	}

	/**
	 * Returns cart data.
	 *
	 * @param \WP_REST_Request $request Request instance.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_items( $request ) {
		$cart_id = $request->get_param( 'cart_id' );

		if ( empty( $cart_id ) ) {
			return rest_ensure_response( null );
		}

		$query_params = [
			'include' => [
				'line_items.physical_items.options',
				'line_items.digital_items.options',
				'redirect_urls',
			],
		];

		$request = new WP_REST_Request(
			'GET',
			sprintf( '/%scarts/%s?%s', trailingslashit( $this->proxy_base ), $cart_id, http_build_query( $query_params ) ),
		);

		$response = rest_do_request( $request );

		if ( 200 !== $response->status || is_wp_error( $response ) || ! isset( $response->data['data'] ) ) {
			return rest_ensure_response( null );
		}

		$data                = $response->data['data'];
		$data['items']       = $this->build_items( $data );
		$data['items_count'] = count( $data['items'] );
		$data['total']       = apply_filters(
			'bigcommerce/currency/format',
			sprintf( '¤%0.2f', $data['cart_amount'] ),
			$data['cart_amount']
		);

		$data = $this->get_cart_totals( $data );

		return rest_ensure_response( $data );
	}

	protected function get_cart_totals( $data ) {
		$cart = [
			'base_amount'     => [
				'raw'       => $data['base_amount'],
				'formatted' => $this->get_formatted_price( $data['base_amount'] ),
			],
			'discount_amount' => [
				'raw'       => $data['discount_amount'],
				'formatted' => $this->get_formatted_price( $data['discount_amount'] ),
			],
			'cart_amount'     => [
				'raw'       => $data['cart_amount'],
				'formatted' => $this->get_formatted_price( $data['cart_amount'] ),
			],
			'tax_included'    => (bool) $data['tax_included'],
			'coupons'         => $data['coupons'],
		];

		$coupons_discount_amount = $this->get_coupons_discount_amount( $data['coupons'] );

		$cart[ 'coupons_discount_amount' ] = [
			'raw'       => $coupons_discount_amount,
			'formatted' => $this->get_formatted_price( $coupons_discount_amount ),
		];

		$tax_amount = $this->calculate_total_tax(
			$cart[ 'cart_amount' ][ 'raw' ],
			$cart[ 'discount_amount' ][ 'raw' ],
			$coupons_discount_amount,
			$data[ 'items' ]
		);

		$cart[ 'tax_amount' ] = [
			'raw'       => $tax_amount,
			'formatted' => $this->get_formatted_price( $tax_amount ),
		];

		if ( $data[ 'tax_included' ] || $tax_amount < 0 ) {
			$subtotal = $cart[ 'cart_amount' ][ 'raw' ];
		} else {
			$subtotal = $cart[ 'cart_amount' ][ 'raw' ] - $tax_amount;
		}

		$cart[ 'subtotal' ] = [
			'raw'       => $subtotal,
			'formatted' => $this->get_formatted_price( $subtotal ),
		];

		return array_merge( $data, $cart );
	}

	private function get_formatted_price( $value ) {
		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $value ), $value );
	}

	/**
	 * @param float $cart_amount             The `cart_amount` value for the cart
	 * @param float $discount_amount         The `discount_amount` value for the cart
	 * @param float $coupons_discount_amount The `coupons_discount_amount` value for the cart
	 * @param array $items                   The items in the cart
	 *
	 * @return float
	 */
	private function calculate_total_tax( $cart_amount, $discount_amount, $coupons_discount_amount, $items ) {
		$item_sum = array_sum( array_map( function ( $item ) {
			return isset( $item[ 'sale_price' ] ) ? $item[ 'sale_price' ] * $item['quantity'] : 0;
		}, $items ) );

		return $cart_amount + $discount_amount + $coupons_discount_amount - $item_sum;
	}

	private function get_coupons_discount_amount( array $coupons ) {
		return array_reduce( $coupons, function( $carry, $coupon ) {
			return $carry + $coupon['discounted_amount'];
		}, 0 );
	}
}
