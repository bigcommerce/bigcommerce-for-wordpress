<?php
/**
 * This container provider adds a WordPress proxy layer on top of the BigCommerce API.
 *
 * @package BigCommere
 */

namespace BigCommerce\Container;

use BigCommerce\Proxy\Proxy_Cache;
use BigCommerce\Proxy\Proxy_Controller;
use BigCommerce\Proxy\AMP_Cart_Controller;
use Pimple\Container;

/**
 * Proxy provider class
 */
class Proxy extends Provider {

	const ACCESS              = 'proxy.access';
	const REST_CONTROLLER     = 'proxy.rest_controller';
	const CACHE               = 'proxy.cache';
	const PROXY_BASE          = 'proxy.base';
	const AMP_CART_CONTROLLER = 'proxy.amp_cart_controller';
	const CACHE_PRIORITY      = 10;

	/**
	 * The proxy base, set in the constructor.
	 *
	 * @var string
	 */
	private $proxy_base = '';

	/**
	 * Registers the container.
	 *
	 * @param Container $container A container instance.
	 */
	public function register( Container $container ) {
		$container[ self::PROXY_BASE ] = function( Container $container ) {
			/**
			 * Filters the REST base use for proxy API requests.
			 *
			 * @param string Default 'bc/v3'.
			 */
			return apply_filters( 'bigcommerce/rest/proxy_base', 'bc/v3' );
		};

		$this->rest_controller( $container );
		$this->cache( $container );
		$this->amp_cart_controller( $container );
	}

	/**
	 * Sets up the endpoint contianer.
	 *
	 * @param Container $container The Container instance.
	 */
	private function rest_controller( Container $container ) {
		$container[ self::REST_CONTROLLER ] = function ( Container $container ) {
			return new Proxy_Controller(
				[
					'host'         => $container[ Api::HOST ],
					'client_id'    => $container[ Api::CLIENT_ID ],
					'access_token' => $container[ Api::ACCESS_TOKEN ],
					'proxy_base'   => $container[ self::PROXY_BASE ],
				]
			);
		};

		// Initialise the proxy.
		add_action(
			'rest_api_init',
			$this->create_callback(
				'start_proxy_controller',
				function() use ( $container ) {
					$container[ self::REST_CONTROLLER ]->register_routes();
				}
			)
		);
	}

	/**
	 * Sets up the cache container.
	 *
	 * @param Container $container The Container instance.
	 */
	private function cache( Container $container ) {
		$container[ self::CACHE ] = function ( Container $container ) {
			return new Proxy_Cache(
				[
					'proxy_base' => $container[ self::PROXY_BASE ],
				]
			);
		};

		/**
		 * Filters whether to use the proxy cache.
		 *
		 * @param bool Defaul true.
		 */
		$use_cache = apply_filters( 'bigcommerce/proxy/use_cache', true );

		if ( ! $use_cache ) {
			return;
		}

		add_filter(
			'bigcommerce/proxy/result_pre',
			$this->create_callback(
				'before_fetch_result',
				function( $result, $args ) use ( $container ) {
					if ( $container[ Api::CONFIG_COMPLETE ] ) {
						return $container[ self::CACHE ]->get_result( $result, $args );
					}

					return $result;
				}
			),
			self::CACHE_PRIORITY,
			7,
			2
		);

		add_action(
			'bigcommerce/proxy/response_received',
			$this->create_callback(
				'on_response_received',
				function( $result, $args ) use ( $container ) {
					if ( $container[ Api::CONFIG_COMPLETE ] ) {
						$container[ self::CACHE ]->handle_result( $result, $args );
					}
				}
			),
			self::CACHE_PRIORITY,
			2
		);

		add_action(
			'bigcommerce/webhooks/product_updated',
			$this->create_callback(
				'on_product_updated',
				function( $product_id ) use ( $container ) {
					$container[ self::CACHE ]->bust_product_cache( $product_id );
				}
			)
		);
	}

	/**
	 * Sets up the AMP cart controller container.
	 *
	 * @param Container $container The Container instance.
	 */
	private function amp_cart_controller( Container $container ) {
		$container[ self::AMP_CART_CONTROLLER ] = function ( Container $container ) {
			return new AMP_Cart_Controller(
				[
					'host'         => $container[ Api::HOST ],
					'client_id'    => $container[ Api::CLIENT_ID ],
					'access_token' => $container[ Api::ACCESS_TOKEN ],
					'proxy_base'   => $container[ self::PROXY_BASE ],
				]
			);
		};

		// Initialise the proxy.
		add_action(
			'rest_api_init',
			$this->create_callback(
				'init_amp_cart_controller',
				function() use ( $container ) {
					$container[ self::AMP_CART_CONTROLLER ]->register_routes();
				}
			)
		);
	}
}
