<?php


namespace BigCommerce\Cart;

use BigCommerce\Api\v3\Model\BaseItem;
use BigCommerce\Api\v3\Model\Cart as BigCommerce_Cart;
use BigCommerce\Api\v3\Model\ItemGiftCertificate;
use BigCommerce\Api\v3\Model\ProductOption;
use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;
use BigCommerce\Util\Cart_Item_Iterator;


/**
 * Maps a cart from the API to a standard format usable by the
 * REST API and other templates.
 */
class Cart_Mapper {
    /**
     * @var BigCommerce_Cart
     */
    private $cart;

    /**
     * @var \WP_Term
     */
    private $channel;

    /**
     * Cart_Mapper constructor.
     *
     * @param BigCommerce_Cart $cart The cart to map.
     */
    public function __construct( BigCommerce_Cart $cart ) {
        $this->cart = $cart;
    }

    /**
     * Maps the cart data into a format for API and template usage.
     *
     * @return array Mapped cart data.
     */
	public function map() {
		$this->identify_channel();
		$cart = [
			'cart_id'         => $this->cart->getId(),
			'base_amount'     => [
				'raw'       => $this->cart->getBaseAmount(),
				'formatted' => $this->format_currency( $this->cart->getBaseAmount() ),
			],
			'discount_amount' => [
				'raw'       => $this->cart->getDiscountAmount(),
				'formatted' => $this->format_currency( $this->cart->getDiscountAmount() ),
			],
			'cart_amount'     => [
				'raw'       => $this->cart->getCartAmount(),
				'formatted' => $this->format_currency( $this->cart->getCartAmount() ),
			],
			'tax_included'    => (bool) $this->cart->getTaxIncluded(),
			'coupons'         => $this->map_coupon_data( $this->cart->getCoupons() ),
			'items'           => $this->cart_items(),
		];

		$coupons_discount_amount = $this->get_coupons_discount_amount( $this->cart->getCoupons() );

		$cart[ 'coupons_discount_amount' ] = [
			'raw'       => $coupons_discount_amount,
			'formatted' => $this->format_currency( $coupons_discount_amount ),
		];

		$tax_amount = $this->calculate_total_tax(
			$cart[ 'cart_amount' ][ 'raw' ],
			$cart[ 'discount_amount' ][ 'raw' ],
			$coupons_discount_amount,
			$cart[ 'items' ]
		);

		$cart[ 'tax_amount' ] = [
			'raw'       => $tax_amount,
			'formatted' => $this->format_currency( $tax_amount ),
		];

		/**
		 * If tax is not already included in item prices
		 * then we need to deduct the calculated tax from the subtotal
		 * as we are displaying the tax separately
		 */
		if ( $cart[ 'tax_included' ] || $tax_amount < 0 ) {
			$subtotal = $cart[ 'cart_amount' ][ 'raw' ];
		} else {
			$subtotal = $cart[ 'cart_amount' ][ 'raw' ] - $tax_amount;
		}

		$cart[ 'subtotal' ] = [
			'raw'       => $subtotal,
			'formatted' => $this->format_currency( $subtotal ),
		];

		/**
		 * Filter mapped cart
		 *
		 * @param array $cart Cart data.
		 */
		return apply_filters( 'bigcommerce/cart_mapper/map', $cart );
	}

	private function map_coupon_data( array $coupons ) {
		return array_map( function ( $coupon ) {
			$amount = $coupon->getDiscountedAmount();
			return [
				'code'        => $coupon->getCode(),
				'name'        => $coupon->getName(),
				'coupon_type' => $coupon->getCouponType(),
				'discounted_amount' => [
					'raw'       => $amount,
					'formatted' => $this->format_currency( $amount ),
				],
			];
		}, $coupons );
	}

	private function get_coupons_discount_amount( array $coupons ) {
		return array_reduce( $coupons, function( $carry, $coupon ) {
			return $carry + $coupon->getDiscountedAmount();
		}, 0 );
	}

	private function cart_items() {
		return array_filter( array_map( [
			$this,
			'prepare_line_item',
		], iterator_to_array( Cart_Item_Iterator::factory( $this->cart ) ) ) );
	}

	/**
	 * @param \BigCommerce\Api\v3\Model\BaseItem|\BigCommerce\Api\v3\Model\ItemGiftCertificate $item
	 *
	 * @return array
	 */
	private function prepare_line_item( $item ) {
		if ( $item instanceof BaseItem ) {
			return $this->prepare_base_item( $item );
		} elseif ( $item instanceof ItemGiftCertificate ) {
			return $this->prepare_gift_certificate_item( $item );
		} else {
			return [];
		}
	}

	/**
	 * @param \BigCommerce\Api\v3\Model\BaseItem $item
	 *
	 * @return Product
	 */
	private function get_product( $item ) {
		$product_id = $item->getProductId();
		return Product::by_product_id( $product_id, $this->channel );
	}

	private function get_terms( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		$terms = is_array( $terms ) ? $terms : [];
		$terms = array_map( [ $this, 'format_term' ], $terms );

		return $terms;
	}

	/**
	 * @param \WP_term $term
	 *
	 * @return array
	 */
	private function format_term( $term ) {
		return [
			'id'    => $term->term_id,
			'label' => $term->name,
			'slug'  => $term->slug,
		];
	}

	/**
	 * @param int     $variant_id
	 * @param Product $product
	 *
	 * @return string
	 */
	private function get_variant_sku( $variant_id, $product ) {
		$variant = $this->get_variant( $variant_id, $product );

		return $variant ? $variant->sku : '';
	}

	/**
	 * @param BaseItem $item
	 *
	 * @return array
	 */
	private function get_options( BaseItem $item ) {
		return array_map( function ( ProductOption $option ) {
			return [
				'label'    => $option->getName(),
				'key'      => $option->getNameId(),
				'value'    => $option->getValue(),
				'value_id' => $option->getValueId(),
			];
		}, array_filter( (array) $item->getOptions() ) );
	}

	/**
	 * @param int     $variant_id
	 * @param Product $product
	 *
	 * @return object|null
	 */
	private function get_variant( $variant_id, $product ) {
		$data = $product->get_source_data();

		if ( class_exists( 'BigCommerceReactTemplates\Plugin' ) ) {
			return null;
		}

		foreach ( $data->variants as $variant ) {
			if ( $variant->id == $variant_id ) {
				return $variant;
			}
		}

		return null;
	}

	private function get_max_quantity( $order_max, $inventory ) {
		$order_max = (int) $order_max;
		$inventory = (int) $inventory;
		if ( $inventory < 0 ) { // no inventory restriction, so fall back to order restriction
			return $order_max;
		}
		if ( $inventory == 0 ) { // no inventory remaining
			return - 1;
		}
		if ( $order_max == 0 ) {
			return $inventory; // no order restriction, so use inventory limit
		}

		return min( $order_max, $inventory );
	}

	private function format_currency( $value ) {
		if ( empty( $value ) ) {
			return __( 'Free', 'bigcommerce' );
		}

		/**
		 * This filter is documented in src/BigCommerce/Currency/With_Currency.php.
		 */
		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $value ), $value );
	}

	/**
	 * @param BaseItem $item
	 *
	 * @return array
	 */
	private function prepare_base_item( BaseItem $item ) {
		if ( $item->getParentId() ) {
			return []; // the item should not show in the cart, it's an add-on to another item
		}
		$data = [
			'id'                   => $item->getId(),
			'variant_id'           => (int) $item->getVariantId(),
			'product_id'           => $item->getProductId(),
			'name'                 => $item->getName(),
			'quantity'             => $item->getQuantity(),
			'list_price'           => [
				'raw'       => $item->getListPrice(),
				'formatted' => $this->format_currency( $item->getListPrice() ),
			],
			'sale_price'           => [
				'raw'       => $item->getSalePrice(),
				'formatted' => $this->format_currency( $item->getSalePrice() ),
			],
			'total_list_price'     => [
				'raw'       => $item->getExtendedListPrice(),
				'formatted' => $this->format_currency( $item->getExtendedListPrice() ),
			],
			'total_sale_price'     => [
				'raw'       => $item->getExtendedSalePrice(),
				'formatted' => $this->format_currency( $item->getExtendedSalePrice() ),
			],
			'post_id'              => 0,
			'thumbnail_id'         => 0,
			'is_featured'          => false,
			'on_sale'              => false,
			'show_condition'       => false,
			'sku'                  => [
				'product' => '',
				'variant' => '',
			],
			'options'              => [],
			'minimum_quantity'     => 0,
			'maximum_quantity'     => 0,
			'inventory_level'      => - 1,
			Availability::NAME     => [],
			Condition::NAME        => [],
			Product_Type::NAME     => [],
			Brand::NAME            => [],
			Product_Category::NAME => [],
		];
		try {
			$product                             = $this->get_product( $item );
			$data[ 'post_id' ]                   = $product->post_id();
			$data[ 'name' ]                      = get_the_title( $data[ 'post_id' ] );
			$data['thumbnail_id']                = $this->get_thumbnail_id( $product, $data['variant_id'] );
			$data[ 'is_featured' ]               = is_object_in_term( $data[ 'post_id' ], Flag::NAME, Flag::FEATURED );
			$data[ 'on_sale' ]                   = $product->on_sale();
			$data[ 'show_condition' ]            = $product->show_condition();
			$data[ 'sku' ]                       = [
				'product' => $product->sku(),
				'variant' => $this->get_variant_sku( $data[ 'variant_id' ], $product ),
			];
			$data[ 'options' ]                   = $this->get_options( $item );
			$data[ 'inventory_level' ]           = (int) $product->get_inventory_level( $data[ 'variant_id' ] );
			$data[ 'minimum_quantity' ]          = (int) $product->order_quantity_minimum;
			$data[ 'maximum_quantity' ]          = $this->get_max_quantity( (int) $product->order_quantity_maximum, $data[ 'inventory_level' ] );
			$data[ 'weight' ]                    = $product->weight;
			$data[ 'is_free_shipping' ]          = (bool) $product->is_free_shipping;
			$data[ 'fixed_cost_shipping_price' ] = [
				'raw'       => $product->fixed_cost_shipping_price,
				'formatted' => $this->format_currency( $product->fixed_cost_shipping_price ),
			];

			$taxonomies = [
				Availability::NAME,
				Condition::NAME,
				Product_Type::NAME,
				Brand::NAME,
				Product_Category::NAME,
			];
			foreach ( $taxonomies as $tax ) {
				$data[ $tax ] = $this->get_terms( $data[ 'post_id' ], $tax );
			}
		} catch ( Product_Not_Found_Exception $e ) {
			// leave empty
		}

		return $data;
	}

	private function prepare_gift_certificate_item( ItemGiftCertificate $item ) {
		$amount = $item->getAmount();
		// TODO: name always comes back empty from the API, even if we set it
		$name     = $item->getName() ?: sprintf(
			__( '%s Gift Certificate', 'bigcommerce' ),
			/**
			 * This filter is documented in src/BigCommerce/Currency/With_Currency.php.
			 */
			apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $amount ), $amount )
		);
		$quantity = $item->getQuantity() ?: 1;
		$data     = [
			'id'                   => $item->getId(),
			'variant_id'           => 0,
			'product_id'           => 0,
			'name'                 => $name,
			'quantity'             => $quantity,
			'list_price'           => [
				'raw'       => $amount,
				'formatted' => $this->format_currency( $amount ),
			],
			'sale_price'           => [
				'raw'       => $amount,
				'formatted' => $this->format_currency( $amount ),
			],
			'total_list_price'     => [
				'raw'       => $amount * $quantity,
				'formatted' => $this->format_currency( $amount * $quantity ),
			],
			'total_sale_price'     => [
				'raw'       => $amount * $quantity,
				'formatted' => $this->format_currency( $amount * $quantity ),
			],
			'post_id'              => 0,
			'thumbnail_id'         => 0,
			'is_featured'          => false,
			'on_sale'              => false,
			'show_condition'       => false,
			'sku'                  => [
				'product' => '',
				'variant' => '',
			],
			'options'              => [],
			'minimum_quantity'     => $quantity,
			'maximum_quantity'     => $quantity,
			'inventory_level'      => $quantity,
			Availability::NAME     => [],
			Condition::NAME        => [],
			Product_Type::NAME     => [],
			Brand::NAME            => [],
			Product_Category::NAME => [],
		];

		return $data;
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
			return isset( $item[ 'total_sale_price' ][ 'raw' ] ) ? $item[ 'total_sale_price' ][ 'raw' ] : 0;
		}, $items ) );

		return $cart_amount + $discount_amount + $coupons_discount_amount - $item_sum;
	}

	/**
	 * Identify the channel associated with the cart
	 *
	 * @return void
	 */
	private function identify_channel() {
		try {
			$channel_id  = $this->cart->getChannelId();
			$connections = new Connections();
			$active      = $connections->active();
			if ( count( $active ) === 1 ) {
				$this->channel = reset( $active );
				return;
			}
			/*
			 * Choosing to loop through connected channels rather than a query,
			 * based on the presumption that there will only ever be a small
			 * handful of channels connected, and the loop will be more efficient.
			 */
			foreach ( $active as $term ) {
				if ( get_term_meta( $term->term_id, Channel::CHANNEL_ID, true ) == $channel_id ) {
					$this->channel = $term;
					return;
				}
			}

			$this->channel = $connections->current();
		} catch ( Channel_Not_Found_Exception $e ) {
			$this->channel = null;
		}
	}

	private function get_thumbnail_id( Product $product, $variant_id ) {
		$variant_image_map = (array) get_post_meta( $product->post_id(), Product::VARIANT_IMAGES_META_KEY, true );
		if ( ! empty( $variant_image_map[ $variant_id ] ) ) {
			return $variant_image_map[ $variant_id ];
		}

		return get_post_thumbnail_id( $product->post_id() );
	}
}
