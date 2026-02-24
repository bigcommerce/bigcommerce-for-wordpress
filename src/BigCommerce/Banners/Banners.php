<?php


namespace BigCommerce\Banners;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Customizer\Sections\Banners as Banners_Settings;
use BigCommerce\Customizer\Sections\Colors;

/**
 * Handles the management and display of banners on various pages of the BigCommerce WordPress theme. It
 * provides methods to configure JavaScript settings, fetch banners from the API, and filter banners based
 * on the current page context, visibility, and date range.
 *
 * @package BigCommerce\Banners
 */
class Banners {

	/**
	 * Cache key for storing banners data.
	 *
	 * @var string
	 */
	const CACHE     = 'bigcommerce_banners';

	/**
	 * Time-to-live (TTL) for banner cache in seconds.
	 *
	 * @var int
	 */
	const CACHE_TTL = '3600';

	/**
	 * Page type for the home page.
	 *
	 * @var string
	 */
	const PAGE_HOME        = 'home_page';

	/**
	 * Page type for the product category page.
	 *
	 * @var string
	 */
	const PAGE_CATEGORY    = 'category_page';

	/**
	 * Page type for the brand page.
	 *
	 * @var string
	 */
	const PAGE_BRAND       = 'brand_page';

	/**
	 * Page type for the search page.
	 *
	 * @var string
	 */
	const PAGE_SEARCH      = 'search_page';

	/**
	 * Constant used to define custom date ranges for banners.
	 *
	 * @var string
	 */
	const DATE_TYPE_CUSTOM = 'custom';

	/**
	 * @var object The banners API instance used to fetch banners.
	 */
	private $banners_api;

	/**
	 * Banners constructor.
	 *
	 * Initializes the Banners class with a banners API instance.
	 *
	 * @param object $banners_api The banners API instance.
	 */
	public function __construct( $banners_api ) {
		$this->banners_api = $banners_api;
	}

	/**
	 * Adds banner configuration to the JS config.
	 *
	 * This method adds the banner settings, including background color, text color, and the context-based
	 * banners, to the JavaScript configuration. The configuration is later localized in the theme.
	 *
	 * @param array $config The current JavaScript configuration.
	 *
	 * @return array The modified JavaScript configuration.
	 * @filter bigcommerce/js_config
	 */
	public function js_config( $config ) {
		$config['banners'] = [
			'bg_color'   => get_theme_mod( Colors::BANNER_COLOR, Colors::COLOR_BANNER_GREY ),
			'text_color' => get_theme_mod( Colors::BANNER_TEXT, Colors::COLOR_WHITE ),
			'items'      => $this->get_context_banners(),
		];

		return $config;
	}

	/**
	 * Retrieves banners for the current page context.
	 *
	 * This method filters banners based on the current page type (home, category, brand, search), visibility,
	 * and any custom date ranges that are defined for each banner. Only banners that match the current context
	 * will be returned.
	 *
	 * @return array The filtered list of banners for the current context.
	 */
	public function get_context_banners() {
		$enable_banners = get_option( Banners_Settings::ENABLE_BANNERS, false ) === 'yes';

		if ( ! $enable_banners ) {
			return [];
		}

		$banners        = $this->get_banners();
		$page           = $this->get_current_page();
		$current_date   = time();
		$queried_object = get_queried_object();
		$bc_id          = 0;

		if ( is_a( $queried_object, 'WP_Term' ) && in_array( $page, [ self::PAGE_CATEGORY, self::PAGE_BRAND ] ) ) {
			$bc_id = (int) get_term_meta( $queried_object->term_id, 'bigcommerce_id', true );
		}

		$banners = array_filter( $banners, function( $banner ) use ( $page, $current_date, $bc_id ) {
			if ( ! $banner['visible'] || $banner['page'] !== $page ) {
				return false;
			}

			if ( $bc_id && $banner['item_id'] !== $bc_id ) {
				return false;
			}

			if ( $banner['date_type'] === self::DATE_TYPE_CUSTOM ) {
				$in_range = $current_date >= $banner['date_from'] && $current_date <= $banner['date_to'];
				if ( ! $in_range ) {
					return false;
				}
			}

			return true;
		} );

		return array_values( $banners ); // reset keys
	}

	/**
	 * Fetches banners from the banners API.
	 *
	 * This method checks for cached banners and fetches them from the API if they are not already cached.
	 * The fetched banners are stored in the WordPress transient cache for future use.
	 *
	 * @return array The list of banners retrieved from the API or cache.
	 */
	public function get_banners() {
		$cache = get_transient( self::CACHE );

		if ( ! empty( $cache ) && is_array( $cache ) ) {
			return $cache;
		}

		try {
			$banners = $this->banners_api->get_banners();
		} catch ( \Throwable $th ) {
			$banners = [];
		}

		set_transient( self::CACHE, $banners, self::CACHE_TTL );

		return $banners;
	}

	/**
	 * Determines the current page type.
	 *
	 * This method checks the WordPress environment to determine the type of page being viewed
	 * (home, category, brand, search) and returns the corresponding page type constant.
	 *
	 * @return string The current page type constant (e.g., 'home_page', 'category_page').
	 */
	private function get_current_page() {
		if ( is_front_page() ) {
			return self::PAGE_HOME;
		} elseif ( is_post_type_archive( Product::NAME ) ) {
			return self::PAGE_HOME;
		} elseif ( is_tax( Product_Category::NAME ) ) {
			return self::PAGE_CATEGORY;
		} elseif ( is_tax( Brand::NAME ) ) {
			return self::PAGE_BRAND;
		} elseif ( is_search() ) {
			return self::PAGE_SEARCH;
		}

		return '';
	}

}
