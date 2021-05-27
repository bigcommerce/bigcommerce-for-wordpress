<?php


namespace BigCommerce\Banners;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Customizer\Sections\Banners as Banners_Settings;
use BigCommerce\Customizer\Sections\Colors;


class Banners {

	const CACHE     = 'bigcommerce_banners';
	const CACHE_TTL = '3600';

	const PAGE_HOME        = 'home_page';
	const PAGE_CATEGORY    = 'category_page';
	const PAGE_BRAND       = 'brand_page';
	const PAGE_SEARCH      = 'search_page';
	const DATE_TYPE_CUSTOM = 'custom';

	private $banners_api;

	public function __construct( $banners_api ) {
		$this->banners_api = $banners_api;
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 * @filter bigcommerce/js_config
	 */
	public function js_config( $config ) {
		$config[ 'banners' ] = [
			'bg_color'   => get_theme_mod( Colors::BANNER_COLOR, Colors::COLOR_BANNER_GREY ),
			'text_color' => get_theme_mod( Colors::BANNER_TEXT, Colors::COLOR_WHITE ),
			'items'      => $this->get_context_banners(),
		];

		return $config;
	}

	/**
	 * Get banners for the current contenxt
	 * 
	 * @return array
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

		if ( is_a( $queried_object,  'WP_Term' ) && in_array( $page, [ self::PAGE_CATEGORY, self::PAGE_BRAND ]) ) {
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
				if ( ! $in_range  ) {
					return false;
				}
			}

			return true;
		} );

		return array_values( $banners ); // reset keys
	}

	/**
	 * Get banners
	 * 
	 * @return array
	 */
	public function get_banners() {
		$cache = get_transient( self::CACHE );

		if ( ! empty( $cache ) && is_array( $cache ) ) {
			return $cache;
		}

		try {
			$banners = $this->banners_api->get_banners();
		} catch (\Throwable $th) {
			$banners = [];
		}

		set_transient( self::CACHE, $banners, self::CACHE_TTL );

		return $banners;
	}

	/**
	 * @return string
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
