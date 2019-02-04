<?php


namespace BigCommerce\Templates;


use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Customizer\Sections;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Account_Settings;
use BigCommerce\Shortcodes;

class Order_Summary extends Controller {
	const ORDER          = 'order';
	const THUMBNAIL_SIZE = 'thumbnail_size';
	const DATE_FORMAT    = 'date_format';

	const ORDER_ID         = 'order_id';
	const DESCRIPTION      = 'description';
	const SHIPPING         = 'shipping';
	const TAX              = 'tax';
	const DISCOUNT         = 'discount_amount';
	const COUPON           = 'coupon_amount';
	const SUBTOTAL         = 'subtotal';
	const TOTAL_EX_TAX     = 'total_ex_tax';
	const TOTAL            = 'total';
	const COUNT            = 'item_count';
	const PAYMENT_METHOD   = 'payment_method';
	const STORE_CREDIT     = 'store_credit';
	const GIFT_CERTIFICATE = 'gift_certificate';
	const CREATED          = 'created_date';
	const UPDATED          = 'updated_date';
	const SHIPPED          = 'shipped_date';
	const IMAGE_ID         = 'image_id';
	const IMAGE            = 'image';
	const STATUS           = 'status';
	const DETAILS_URL      = 'details_url';
	const SUPPORT_EMAIL    = 'support_email';

	protected $template = 'components/orders/order-summary.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::ORDER          => null,
			/**
			 * This filter is documented in src/BigCommerce/Templates/Order_Product.php
			 */
			self::THUMBNAIL_SIZE => apply_filters( 'bigcommerce/template/order_history/image_size', Image_Sizes::BC_SMALL ),
			/**
			 * Filter the date format used on the order history template
			 *
			 * @param string $format The date format to use, defaults to the WordPress date format option
			 */
			self::DATE_FORMAT    => apply_filters( 'bigcommerce/template/order_history/date_format', get_option( 'date_format', 'F j, Y' ) ),
			self::DESCRIPTION    => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		$order    = $this->options[ 'order' ];
		$image_id = $this->get_image_id( $order[ 'products' ] );
		$image    = $image_id ? wp_get_attachment_image( $image_id, $this->options[ self::THUMBNAIL_SIZE ] ) : $this->get_fallback_image( $this->options[ self::THUMBNAIL_SIZE ] );

		return [
			self::ORDER_ID         => $order[ 'id' ],
			self::SHIPPING         => $this->format_currency( $order[ 'base_shipping_cost' ], false ),
			self::TAX              => $this->format_currency( $order[ 'total_tax' ], false ),
			self::DISCOUNT         => $this->format_currency( $order[ 'discount_amount' ], false ),
			self::COUPON           => $this->format_currency( $order[ 'coupon_discount' ], false ),
			self::SUBTOTAL         => $this->format_currency( $order[ 'subtotal_inc_tax' ] ),
			self::TOTAL_EX_TAX     => $this->format_currency( $order[ 'total_ex_tax' ] ),
			self::TOTAL            => $this->format_currency( $order[ 'total_inc_tax' ] ),
			self::COUNT            => $order[ 'items_total' ],
			self::PAYMENT_METHOD   => $this->get_payment_method( $order[ 'payment_method' ] ),
			self::CREATED          => $this->format_gmt_date( $order[ 'date_created' ] ),
			self::UPDATED          => $this->format_gmt_date( $order[ 'date_modified' ] ),
			self::SHIPPED          => $this->format_gmt_date( $order[ 'date_shipped' ] ),
			self::STATUS           => $order[ 'custom_status' ],
			self::IMAGE_ID         => $image_id,
			self::IMAGE            => $image,
			self::DESCRIPTION      => $this->options[ self::DESCRIPTION ] ?: $this->build_description( $order[ 'products' ] ),
			self::DETAILS_URL      => $this->order_details_url( $order[ 'id' ] ),
			self::STORE_CREDIT     => $this->format_currency( $order[ 'store_credit_amount' ], false ),
			self::GIFT_CERTIFICATE => $this->format_currency( $order[ 'gift_certificate_amount' ], false ),
			self::SUPPORT_EMAIL    => $this->get_support_email(),
		];
	}

	/**
	 * @param $date_string
	 *
	 * @return string
	 */
	private function format_gmt_date( $date_string ) {
		// if $date_string just return the same empty string
		if (empty($date_string)) {
			return $date_string;
		}

		$date_string = date( 'Y-m-d H:i:s', strtotime( $date_string ) );
		return get_date_from_gmt( $date_string, $this->options[ self::DATE_FORMAT ] );
	}

	/**
	 * Get the ID of a featured image on one of the products
	 *
	 * @param array[] $products
	 *
	 * @return int
	 */
	private function get_image_id( $products ) {
		if ( empty( $products ) ) {
			return 0;
		}
		// loop through until we find one with a featured image
		// filter the array, because some items (e.g., gift certificates) won't have a product ID
		$product_ids = array_filter( wp_list_pluck( $products, 'product_id' ) );
		foreach ( $product_ids as $product_id ) {
			/*
			 * Yes, this is inefficient if many products are lacking
			 * images. But it should work on the first try in most cases.
			 */
			$posts = get_posts( [
				'bigcommerce_id__in' => [ $product_id ],
				'post_type'          => Product::NAME,
				'post_status'        => 'publish',
				'fields'             => 'ids',
			] );
			if ( empty( $posts ) ) {
				continue;
			}
			$thumbnail_id = (int) get_post_thumbnail_id( reset( $posts ) );
			if ( $thumbnail_id ) {
				return $thumbnail_id;
			}
		}

		return 0;
	}

	private function build_description( $products ) {
		$count = count( $products );
		$names = array_filter( wp_list_pluck( $products, 'name' ) );
		if ( empty( $names ) ) {
			return sprintf( _n( '%d item', '%d items', $count, 'bigcommerce' ), $count );
		}
		if ( $count == 1 ) {
			return reset( $names );
		}

		$first = reset( $names );

		$description = sprintf( _n( '%s <span>and %d other item</span>', '%s <span>and %d other items</span>', $count - 1, 'bigcommerce' ), $first, $count - 1 );

		return $description;
	}

	private function order_details_url( $order_id ) {
		$page = get_option( Orders_Page::NAME, 0 );
		if ( empty( $page ) ) {
			return '';
		}
		$url = get_permalink( $page );
		$url = add_query_arg( [ Shortcodes\Order_History::ORDER_ID_QUERY_ARG => $order_id ], $url );

		return $url;
	}

	protected function get_payment_method( $method ) {
		switch ( $method ) {
			case 'storecredit':
				$label = __( 'Store Credit', 'bigcommerce' );
				break;
			case 'cash':
				$label = __( 'Cash', 'bigcommerce' );
				break;
			default:
				$label = $method;
				break;
		}

		/**
		 * Filter the label displayed for a payment method
		 *
		 * @param string $label  The label to display
		 * @param string $method The payment method name
		 */
		return apply_filters( 'bigcommerce/order/payment_method_label', $label, $method );
	}

	protected function get_support_email() {
		/**
		 * Filter the support email address displayed on order detail pages.
		 * If empty, no email will display.
		 *
		 * @param string $email_address The email address to display, defaults to the value set on the BigCommerce setting screen
		 */
		return apply_filters( 'bigcommerce/order/support_email', get_option( Account_Settings::SUPPORT_EMAIL, '' ) );
	}

	protected function get_fallback_image( $size ) {
		$default = get_option( Sections\Product_Single::DEFAULT_IMAGE, 0 );
		if ( empty( $default ) ) {
			$component = Fallback_Image::factory( [] );

			return $component->render();
		}

		return wp_get_attachment_image( $default, $size );
	}

}