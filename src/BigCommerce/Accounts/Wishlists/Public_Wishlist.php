<?php


namespace BigCommerce\Accounts\Wishlists;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Templates\No_Results;
use BigCommerce\Templates\Product_Archive;

class Public_Wishlist extends Wishlist_Public_View {
	/** @var Wishlist */
	private $wishlist;

	public function __construct( Wishlist $wishlist ) {
		$this->wishlist = $wishlist;
	}

	/**
	 * Set the query to only show products in the wishlist
	 *
	 * @param \WP_Query $query
	 *
	 * @return void
	 */
	public function filter_main_query( \WP_Query $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$product_ids = $this->wishlist->items();

		if ( empty( $product_ids ) ) {
			$query->set( 'post__in', [ -1 ] );
			return;
		}

		$query->set( 'bigcommerce_id__in', $product_ids );
	}

	/**
	 * Set the page title to the name of the wishlist
	 *
	 * @param array $template_data
	 *
	 * @return array
	 * @filter bigcommerce/template=components/catalog/product-archive.php/data
	 */
	public function set_page_title( $template_data ) {
		$name = $this->wishlist->name();
		$template_data[ Product_Archive::TITLE ] = sprintf( __( 'Wish List: %s', 'bigcommerce' ), $name );
		return $template_data;
	}

	/**
	 * Filter the wp_title() for the Wish List page
	 *
	 * @param string $title
	 * @param string $post_type
	 *
	 * @return string
	 * @filter post_type_archive_title
	 */
	public function set_wp_title( $title, $post_type ) {
		if ( $post_type === Product::NAME ) {
			$name = $this->wishlist->name();
			$title = sprintf( __( 'Wish List: %s', 'bigcommerce' ), $name );
		}
		return $title;
	}

	/**
	 * Set the No Results message to be wishlist-relevant
	 *
	 * @param array $template_data
	 *
	 * @return array
	 * @filter bigcommerce/template=components/catalog/no-results.php/data
	 */
	public function set_no_results_message( $template_data ) {
		$template_data[ No_Results::NO_RESULTS_MESSAGE ] = __( 'This Wish List does not have any products.', 'bigcommerce' );
		$template_data[ No_Results::RESET_BUTTON_LABEL ] = __( 'Shop Around', 'bigcommerce' );

		return $template_data;
	}
}
