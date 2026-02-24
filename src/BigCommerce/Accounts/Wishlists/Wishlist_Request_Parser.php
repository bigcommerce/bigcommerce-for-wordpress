<?php


namespace BigCommerce\Accounts\Wishlists;

use BigCommerce\Api\v3\Api\WishlistsApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Post_Types\Product\Product;

/**
 * Class Wishlist_Request_Parser
 *
 * This class parses and processes wishlist-related requests, such as filtering 
 * the product archive to display wishlist items. It integrates with the BigCommerce 
 * Wishlists API to fetch and handle wishlist data and applies necessary filters 
 * and actions to the WordPress environment.
 *
 * @package BigCommerce\Accounts\Wishlists
 */
class Wishlist_Request_Parser {

    /**
     * Query parameter for the wishlist identifier.
     *
     * @var string
     */
	const LIST_PARAM  = 'list';

    /**
     * Query parameter for the wishlist token.
     *
     * @var string
     */
	const TOKEN_PARAM = 'token';

	/** @var WishlistsApi */
	private $wishlists;

    /**
     * Constructor for Wishlist_Request_Parser.
     *
     * @param WishlistsApi $wishlists The Wishlists API instance for fetching wishlist data.
     */
	public function __construct( WishlistsApi $wishlists ) {
		$this->wishlists = $wishlists;
	}

    /**
     * Setup the wishlist request.
     *
     * Determines whether the current request is a wishlist request. If so, it fetches the 
     * wishlist data and applies appropriate actions and filters to render the wishlist.
     *
     * @param \WP $wp The WordPress request object.
     */
	public function setup_wishlist_request( \WP $wp ) {
		if ( ! $this->is_wishlist_request( $wp ) ) {
			return;
		}

		$list = $this->requested_list();
		$token = $this->requested_token();

		try {
			$wishlist = new Wishlist( $this->wishlists->getWishlist( $list )->getData() );
			if ( $wishlist->is_public() && $wishlist->token() === $token ) {
				$archive = new Public_Wishlist( $wishlist );
			} else {
				$archive = new Missing_Wishlist();
			}
		} catch ( ApiException $e ) {
			// treat an API error like a missing wishlist
			$archive = new Missing_Wishlist();
		}

		add_action( 'pre_get_posts', [ $archive, 'filter_main_query' ], 0, 1 ); // needs to run before Query::filter_queries() at 10
		add_filter( 'bigcommerce/template=components/catalog/product-archive.php/data', [ $archive, 'remove_refinery' ], 10, 1 );
		add_filter( 'bigcommerce/template=components/catalog/product-archive.php/data', [ $archive, 'set_page_title' ], 10, 1 );
		add_filter( 'post_type_archive_title', [ $archive, 'set_wp_title' ], 10, 2 );
		add_filter( 'bigcommerce/template=components/catalog/no-results.php/data', [ $archive, 'set_no_results_message' ], 10, 1 );
	}

	private function is_wishlist_request( \WP $wp ) {
		$post_type = array_key_exists( 'post_type', $wp->query_vars ) ? $wp->query_vars[ 'post_type' ] : '';
		if ( $post_type !== Product::NAME ) {
			return false;
		}

		if ( ! $this->requested_list() || ! $this->requested_token() ) {
			return false;
		}

		return true;
	}

	private function requested_list() {
		return filter_input( INPUT_GET, self::LIST_PARAM, FILTER_VALIDATE_INT );
	}

	private function requested_token() {
		return filter_input( INPUT_GET, self::TOKEN_PARAM, FILTER_SANITIZE_STRING );
	}
}
