<?php


namespace BigCommerce\Accounts\Wishlists;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Templates\No_Results;
use BigCommerce\Templates\Product_Archive;

/**
 * This class handles the public-facing wishlist view, including customizing the query
 * to show only products in the wishlist, setting the page title, customizing the
 * `wp_title()` function, and managing the "No Results" message on the wishlist page.
 *
 * @package BigCommerce\Accounts\Wishlists
 */
class Public_Wishlist extends Wishlist_Public_View {
	/** @var Wishlist */
	private $wishlist;

    /**
     * Public_Wishlist constructor.
     *
     * Initializes the Public_Wishlist class with the provided Wishlist object, which represents
     * the user's wishlist and is used throughout the public wishlist view to display wishlist items
     * and handle related operations.
     *
     * @param Wishlist $wishlist The Wishlist object associated with the user's wishlist.
     */
	public function __construct( Wishlist $wishlist ) {
		$this->wishlist = $wishlist;
	}

    /**
     * Set the query to only show products in the wishlist.
     *
     * This method customizes the main WordPress query to only return products that are part of the
     * user's wishlist. If the wishlist is empty, it modifies the query to exclude any results.
     *
     * @param \WP_Query $query The WordPress query object.
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
     * Set the page title to the name of the wishlist.
     *
     * This method modifies the page title template data to include the name of the wishlist
     * in the title of the product archive page.
     *
     * @param array $template_data The template data for the product archive page.
     *
     * @return array The modified template data with the updated page title.
     * @filter bigcommerce/template=components/catalog/product-archive.php/data
     */
	public function set_page_title( $template_data ) {
		$name = $this->wishlist->name();
		$template_data[ Product_Archive::TITLE ] = sprintf( __( 'Wish List: %s', 'bigcommerce' ), $name );
		return $template_data;
	}

    /**
     * Filter the wp_title() for the Wish List page.
     *
     * This method filters the title of the wish list page to include the name of the wishlist.
     * It modifies the default `wp_title()` function output for product archive pages.
     *
     * @param string $title The current title.
     * @param string $post_type The post type for the page.
     *
     * @return string The modified title.
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
     * Set the No Results message to be wishlist-relevant.
     *
     * This method customizes the "No Results" message when the wishlist is empty. It updates the
     * template data to show a relevant message and a "Shop Around" button instead of the default
     * message when no products are found in the wishlist.
     *
     * @param array $template_data The template data for the "No Results" page.
     *
     * @return array The modified template data with the updated message and button label.
     * @filter bigcommerce/template=components/catalog/no-results.php/data
     */
	public function set_no_results_message( $template_data ) {
		$template_data[ No_Results::NO_RESULTS_MESSAGE ] = __( 'This Wish List does not have any products.', 'bigcommerce' );
		$template_data[ No_Results::RESET_BUTTON_LABEL ] = __( 'Shop Around', 'bigcommerce' );

		return $template_data;
	}
}
