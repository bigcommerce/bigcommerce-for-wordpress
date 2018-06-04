<?php


namespace BigCommerce;


use BigCommerce\Api\Customer_Api;
use BigCommerce\Api\Store_Api;
use BigCommerce\Api\v3\ApiClient;
use BigCommerce\Api\v3\CartApi;
use BigCommerce\Api\v3\CatalogApi;
use BigCommerce\Api\v3\CustomersApi;
use BigCommerce\Api\v3\OrdersApi;
use BigCommerce\Api\v3\PlacementApi;
use BigCommerce\Api\v3\ThemeRegionsApi;
use BigCommerce\Api\v3\ThemesApi;
use BigCommerce\Api\v3\WidgetApi;
use BigCommerce\Api\v3\WidgetTemplateApi;
use BigCommerce\Container\Api;

class Api_Factory {
	private $api_client;

	/**
	 * Api_Factory constructor.
	 *
	 * @param ApiClient $client
	 */
	public function __construct( ApiClient $client ) {
		$this->api_client = $client;
	}

	/**
	 * @return CartApi
	 */
	public function cart() {
		return new CartApi( $this->api_client );
	}

	/**
	 * @return CatalogApi
	 */
	public function catalog() {
		return new CatalogApi( $this->api_client );
	}

	/**
	 * @return CustomersApi
	 */
	public function customers() {
		return new CustomersApi( $this->api_client );
	}

	/**
	 * @return OrdersApi
	 */
	public function orders() {
		return new OrdersApi( $this->api_client );
	}

	/**
	 * @return PlacementApi
	 */
	public function placement() {
		return new PlacementApi( $this->api_client );
	}

	/**
	 * @return ThemeRegionsApi
	 */
	public function themeRegions() {
		return new ThemeRegionsApi( $this->api_client );
	}

	/**
	 * @return ThemesApi
	 */
	public function themes() {
		return new ThemesApi( $this->api_client );
	}

	/**
	 * @return WidgetApi
	 */
	public function widget() {
		return new WidgetApi( $this->api_client );
	}

	/**
	 * @return WidgetTemplateApi
	 */
	public function widgetTemplate() {
		return new WidgetTemplateApi( $this->api_client );
	}

	/**
	 * @return Customer_Api
	 */
	public function customer() {
		return new Customer_Api( $this->api_client );
	}

	/**
	 * @return Store_Api
	 */
	public function store() {
		return new Store_Api( $this->api_client );
	}

	/**
	 * @return self
	 */
	public static function instance() {
		return bigcommerce()->container()[ Api::FACTORY ];
	}
}