<?php


namespace BigCommerce\Templates;

use BigCommerce\Accounts\Wishlists\Wishlist;
use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;

class Wishlist_Product extends Controller {
	const WISHLIST       = 'wishlist';
	const PRODUCT        = 'product';
	const THUMBNAIL_SIZE = 'thumbnail_size';

	const TITLE      = 'title';
	const IMAGE      = 'image';
	const PRICE      = 'price';
	const SKU        = 'sku';
	const PERMALINK  = 'permalink';
	const DELETE     = 'delete';
	const ATTRIBUTES = 'attributes';

	protected $template = 'components/wishlist/product.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-wish-list-product-row' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-product-loop-card' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::WISHLIST       => null,
			self::PRODUCT        => null,
			self::ATTRIBUTES     => [],
			/**
			 * Filter the image size for the wishlist user page
			 *
			 * @param string $size The image size to us
			 */
			self::THUMBNAIL_SIZE => apply_filters( 'bigcommerce/template/wishlist/user/image_size', Image_Sizes::BC_SMALL ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];
		/** @var Wishlist $wishlist */
		$wishlist = $this->options[ self::WISHLIST ];
		$post_id  = $product->post_id();

		$data = [
			self::PRODUCT    => $product,
			self::WISHLIST   => $wishlist,
			self::TITLE      => $post_id ? get_the_title( $post_id ) : $product['name'],
			self::IMAGE      => $this->get_featured_image( $product, $this->options[ self::ATTRIBUTES ] ),
			Brand::NAME      => $this->get_terms( $post_id, Brand::NAME ),
			Condition::NAME  => ( $post_id && has_term( Flag::SHOW_CONDITION, Flag::NAME, $post_id ) ) ? $this->get_terms( $post_id, Condition::NAME ) : [],
			self::PRICE      => $product->calculated_price_range(),
			self::SKU        => $product->sku(),
			self::PERMALINK  => $post_id ? get_the_permalink( $post_id ) : '',
			self::DELETE     => $wishlist->delete_item_url( $product->bc_id() ),
			self::ATTRIBUTES => '',
		];

		return $data;
	}

	protected function get_featured_image( Product $product, $attributes ) {
		$quick_view = get_option( \BigCommerce\Customizer\Sections\Product_Archive::QUICK_VIEW, 'yes' );
		if ( $quick_view === 'no' ) {
			$image_component = Linked_Product_Featured_Image::factory( [
				Product_Featured_Image::PRODUCT => $product,
			] );

			return $image_component->render();
		}

		$image_component = Product_Featured_Image::factory( [
			Product_Featured_Image::PRODUCT => $product,
		] );

		$quick_view_component = Quick_View_Image::factory( [
			Quick_View_Image::PRODUCT    => $product,
			Quick_View_Image::IMAGE      => $image_component->render(),
			Quick_View_Image::ATTRIBUTES => $attributes,
		] );

		return $quick_view_component->render();
	}

	private function get_terms( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return [];
		}

		return wp_list_pluck( $terms, 'name' );
	}

}
