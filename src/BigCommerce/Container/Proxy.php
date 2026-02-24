<?php
/**
 * This container provider adds a WordPress proxy layer on top of the BigCommerce API.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Container;

use BigCommerce\Proxy\Proxy_Cache;
use BigCommerce\Proxy\Proxy_Controller;
use BigCommerce\Proxy\AMP_Cart_Controller;
use Pimple\Container;

/**
 * This class registers the proxy container that adds a Wordpress proxy layer on top of the BigCommerce API.
 * Sets up necessary services such as REST controllers, caching, and AMP cart controllers.
 *
 * @package BigCommerce
 */
class Proxy extends Provider {

    /**
     * Constant for the proxy access service.
     *
     * @var string
     */
    const ACCESS              = 'proxy.access';

    /**
     * Constant for the REST controller service.
     *
     * @var string
     */
    const REST_CONTROLLER     = 'proxy.rest_controller';

    /**
     * Constant for the cache service.
     *
     * @var string
     */
    const CACHE               = 'proxy.cache';

    /**
     * Constant for the proxy base service.
     *
     * @var string
     */
    const PROXY_BASE          = 'proxy.base';

    /**
     * Constant for the AMP cart controller service.
     *
     * @var string
     */
    const AMP_CART_CONTROLLER = 'proxy.amp_cart_controller';

    /**
     * Cache priority constant.
     *
     * @var int
     */
    const CACHE_PRIORITY      = 10;

    /**
     * The proxy base URL, set in the constructor.
     *
     * @var string
     */
    private $proxy_base = '';

    /**
     * Registers the container and initializes proxy-related services such as REST controller, cache, and AMP cart controller.
     *
     * @param Container $container A container instance.
     *
     * @return void
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
	 * Sets up the endpoint container.
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
     * Sets up the cache container for proxy requests.
     *
     * @param Container $container The Container instance.
     *
     * @return void
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
		 * @param bool Default true.
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
                    $container[ self::CACHE ]->bust_product_cache( $product_id['product_id'] );
                }
            )
        );
    }

    /**
     * Sets up the AMP cart controller container for AMP-specific requests.
     *
     * @param Container $container The Container instance.
     *
     * @return void
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
