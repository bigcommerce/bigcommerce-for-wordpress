<?php


namespace BigCommerce\Cart;

use BigCommerce\Api\v3\Model\BaseItem;
use BigCommerce\Api\v3\Model\Cart;
use BigCommerce\Api\v3\Model\ItemGiftCertificate;
use BigCommerce\Customizer\Sections\Colors;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;
use BigCommerce\Util\Cart_Item_Iterator;


/**
 * Class Cart_Mapper
 *
 * Maps a cart from the API to a standard format usable by the
 * REST API and other templates
 */
class Cart_Mapper {
	/**
	 * @var Cart
	 */
	private $cart;

	public function __construct( Cart $cart ) {
		$this->cart = $cart;
	}

	/**
	 * @return array
	 */
	public function map() {
		return [
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
			'items'           => $this->cart_items(),
		];
	}

	private function cart_items() {
		return array_map( [
			$this,
			'prepare_line_item',
		], iterator_to_array( Cart_Item_Iterator::factory( $this->cart ) ) );
	}

	/**
	 * @param \BigCommerce\Api\v3\Model\BaseItem|\BigCommerce\Api\v3\Model\ItemGiftCertificate $item
	 *
	 * @return array
	 *
	 * @todo Details of selected options for the variant
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
		$posts      = get_posts( [
			'post_type'          => Product::NAME,
			'bigcommerce_id__in' => $product_id,
			'posts_per_page'     => 1,
			'fields'             => 'ids',
		] );
		if ( count( $posts ) < 1 ) {
			throw new \RuntimeException( 'Product not found' );
		}

		return new Product( reset( $posts ) );
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
	 * @param int     $variant_id
	 * @param Product $product
	 *
	 * @return array
	 */
	private function get_options( $variant_id, $product ) {
		$variant = $this->get_variant( $variant_id, $product );
		if ( ! $variant || empty( $variant->option_values ) ) {
			return [];
		}
		$options = [];
		foreach ( $variant->option_values as $value ) {
			$options[] = [
				'label' => $value->option_display_name,
				'key'   => $value->option_id,
				'value' => $value->label,
			];
		}

		return $options;
	}

	/**
	 * @param int     $variant_id
	 * @param Product $product
	 *
	 * @return object|null
	 */
	private function get_variant( $variant_id, $product ) {
		$data = $product->get_source_data();
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
		 * This filter is documented in src/BigCommerce/Templates/Controller.php
		 */
		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $value ), $value );
	}

	/**
	 * @param BaseItem $item
	 *
	 * @return array
	 */
	private function prepare_base_item( BaseItem $item ) {
		$data = [
			'id'                   => $item->getId(),
			'variant_id'           => $item->getVariantId(),
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
			$product                    = $this->get_product( $item );
			$data[ 'post_id' ]          = $product->post_id();
			$data[ 'thumbnail_id' ]     = get_post_thumbnail_id( $data[ 'post_id' ] );
			$data[ 'is_featured' ]      = is_object_in_term( $data[ 'post_id' ], Flag::NAME, Flag::FEATURED );
			$data[ 'on_sale' ]          = is_object_in_term( $data[ 'post_id' ], Flag::NAME, Flag::SALE );
			$data[ 'sku' ]              = [
				'product' => $product->sku(),
				'variant' => $this->get_variant_sku( $data[ 'variant_id' ], $product ),
			];
			$data[ 'options' ]          = $this->get_options( $data[ 'variant_id' ], $product );
			$data[ 'inventory_level' ]  = (int) $product->get_inventory_level( $data[ 'variant_id' ] );
			$data[ 'minimum_quantity' ] = (int) $product->order_quantity_minimum;
			$data[ 'maximum_quantity' ] = $this->get_max_quantity( (int) $product->order_quantity_maximum, $data[ 'inventory_level' ] );

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
		} catch ( \RuntimeException $e ) {
			// leave empty
		}

		return $data;
	}

	private function prepare_gift_certificate_item( ItemGiftCertificate $item ) {
		$amount = $item->getAmount();
		// TODO: name always comes back empty from the API, even if we set it
		$name = $item->getName() ?: sprintf(
			__( '%s Gift Certificate', 'bigcommerce' ),
			apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $amount ), $amount )
		);
		$quantity = $item->getQuantity() ?: 1;
		$data = [
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
}