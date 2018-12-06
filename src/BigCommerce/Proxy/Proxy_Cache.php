<?php
/**
 * This class adds a caching layer for the Proxy endpoint.
 *
 * Note: Hooks are added via the Proxy container.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Proxy;

/**
 * Proxy_Cache class
 */
class Proxy_Cache {

	const CACHE_GROUP_NAMESPACE = 'bigcommerce_proxy';
	const PRODUCTS_PATH         = '/catalog/products';
	const CATEGORIES_PATH       = '/catalog/categories';
	const BRANDS_PATH           = '/catalog/brands';
	const VARIANTS_PATH         = '/catalog/variants';
	const SUMMARY_PATH          = '/catalog/summary';
	const CHANNELS_PATH         = '/channels';

	const CACHE_TTL             = 10 * MINUTE_IN_SECONDS;
	const GENERATION_KEY_LENGTH = 5;

	/**
	 * Generation keys requested on the current pageload.
	 *
	 * @var array
	 */
	private $generations = [];

	/**
	 * Provides an array of the endpoint paths.
	 *
	 * @return array Endpoint paths.
	 */
	private function get_paths() {
		return [
			self::PRODUCTS_PATH,
			self::CATEGORIES_PATH,
			self::BRANDS_PATH,
			self::VARIANTS_PATH,
			self::SUMMARY_PATH,
			self::CHANNELS_PATH,
		];
	}

	/**
	 * Proxy_Cache constructor.
	 *
	 * @param array $config Configuration details.
	 */
	public function __construct( array $config ) {
		$this->config = $config;
	}

	/**
	 * Returns all parts of a REST route after the base.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return string The request path.
	 */
	public function get_request_route_path( \WP_REST_Request $request ) {
		return str_replace( '/' . $this->config['proxy_base'], '', $request->get_route() );
	}

	/**
	 * Provides a generation key for a cache group.
	 *
	 * @param string $cache_group Cache group name.
	 * @return string Cache generation key.
	 */
	public function get_generation( $cache_group ) {
		if ( isset( $this->generations[ $cache_group ] ) ) {
			return $this->generations[ $cache_group ];
		}

		if ( wp_using_ext_object_cache() ) {
			$this->generations[ $cache_group ] = wp_cache_get( 'generation', $cache_group );
		} else {
			$this->generations[ $cache_group ] = get_transient( 'generation' . $cache_group );
		}

		if ( empty( $this->generations[ $cache_group ] ) ) {
			$this->generations[ $cache_group ] = wp_generate_password( self::GENERATION_KEY_LENGTH );

			if ( wp_using_ext_object_cache() ) {
				wp_cache_set( 'generation', $this->generations[ $cache_group ], $cache_group, self::CACHE_TTL );
			} else {
				set_transient( 'generation' . $cache_group, $this->generations[ $cache_group ], self::CACHE_TTL );
			}
		}

		return $this->generations[ $cache_group ];
	}

	/**
	 * Build a cache key from request args.
	 *
	 * @param \WP_REST_Request $request Request instance.
	 * @param string           $cache_group Cache group name.
	 * @return string Cache key.
	 */
	public function get_cache_key( $request, $cache_group ) {
		$path = $this->get_request_route_path( $request );

		$key = $path . ':' . md5(
			wp_json_encode(
				[
					'params' => $request->get_params(),
					'header' => $request->get_headers(),
				]
			)
		) . ':' . $this->get_generation( $cache_group );

		return $key;
	}

	/**
	 * Returns a cache group name for a given REST request.
	 *
	 * @param string $route REST request route.
	 * @return string Cache group name or an empty string if no cache group applies.
	 */
	public function get_cache_group_name( $route = null ) {
		if ( $route ) {
			if ( preg_match(
				'@^' . preg_quote( self::PRODUCTS_PATH, '@' ) . '\/(?P<id>[0-9]+)(\/.*)?$@',
				$route,
				$matches
			) && isset( $matches['id'] ) ) {
				return sprintf( '%s_product_%s', self::CACHE_GROUP_NAMESPACE, strval( $matches['id'] ) );
			}

			foreach ( $this->get_paths() as $config_path ) {
				if ( 0 === strpos( $route, $config_path ) ) {
					return sprintf( '%s_%s', self::CACHE_GROUP_NAMESPACE, wp_unslash( $route ) );
				}
			}
		}

		return '';
	}

	/**
	 * Caches responses from the BigCommerce API.
	 *
	 * @param mixed            $result  Results returned by the BigCommerce API.
	 * @param \WP_REST_Request $request REST request.
	 *
	 * @action bigcommerce/proxy/response_received
	 *
	 * @return void
	 */
	public function handle_result( $result, $request ) {
		$cache_group = $this->get_cache_group_name( $this->get_request_route_path( $request ) );
		$cache_key   = $this->get_cache_key( $request, $cache_group );

		if ( ! empty( $cache_group ) ) {
			$this->cache_result( $result, $cache_key, $cache_group );

			/**
			 * Fires when a result has been cached.
			 *
			 * @param array|\WP_Error  $result  Result from API call.
			 * @param \WP_REST_Request $request API request.
			 */
			do_action( 'bigcommerce/proxy/cache_set', $result, $request );
		}
	}

	/**
	 * Caches data.
	 *
	 * @param mixed  $data Data to cache.
	 * @param string $cache_key Cache key.
	 * @param string $cache_group Cache group name.
	 */
	public function cache_result( $data, $cache_key, $cache_group ) {
		if ( wp_using_ext_object_cache() ) {
			wp_cache_set( $cache_key, $data, $cache_group, self::CACHE_TTL );

			/**
			 * The Object Cache API doesn't provide a way to delete an entire cache group, so
			 * we build an extra cached value containing all the cached hashes in this group
			 * for use when we need to bust the entire group. See the bust_cache_group method.
			 */
			$cache_keys = wp_cache_get( 'cache_keys', $cache_group );
			$cache_keys = is_array( $cache_keys ) ? $cache_keys : [];

			if ( ! in_array( $cache_key, $cache_keys, true ) ) {
				$cache_keys[] = $cache_key;
			}

			wp_cache_set( 'cache_keys', $cache_keys, $cache_group );

		} else {
			set_transient( $cache_key . $cache_group, $data, self::CACHE_TTL );
		}
	}

	/**
	 * Fetch results from cache if $results are empty.
	 *
	 * @param mixed            $result  Proxy results (This should be empty unless results are provided by extension).
	 * @param \WP_REST_Request $request Request instance.
	 *
	 * @filter bigcommerce/proxy/result_pre
	 *
	 * @return bool|mixed Result.
	 */
	public function get_result( $result, $request ) {
		$cache_group = $this->get_cache_group_name( $this->get_request_route_path( $request ) );
		$cache_key   = $this->get_cache_key( $request, $cache_group );
		$result      = $this->get_data_from_cache( $cache_key, $cache_group );

		/**
		 * Result retrieved from cache.
		 *
		 * @param array            $result  Result returned from cache.
		 * @param \WP_REST_Request $request API request.
		 */
		do_action( 'bigcommerce/proxy/cache_get', $result, $request );

		return $result;
	}

	/**
	 * Gets cached data.
	 *
	 * @param string $cache_key A hash built from the request URL.
	 * @param string $cache_group The request's cache group.
	 * @return mixed Data or false if unsuccessful.
	 */
	public function get_data_from_cache( $cache_key, $cache_group ) {
		if ( wp_using_ext_object_cache() ) {
			return wp_cache_get( $cache_key, $cache_group );
		} else {
			return get_transient( $cache_key . $cache_group );
		}
	}

	/**
	 * Deletes a transient cache group
	 *
	 * @param string $cache_group Cache group name.
	 */
	public function bust_cache_group( $cache_group ) {
		global $wpdb;

		$new_generation = wp_generate_password( self::GENERATION_KEY_LENGTH );
		$this->generations[ $cache_group ] = $new_generation;
		if ( wp_using_ext_object_cache() ) {
			$cache_keys = wp_cache_get( 'cache_keys', $cache_group );

			if ( is_array( $cache_keys ) ) {
				foreach ( $cache_keys as $cache_key ) {
					wp_cache_delete( $cache_key, $cache_group );
				}
			}
			wp_cache_set( 'cache_keys', [], $cache_group );
			wp_cache_set( 'generation', $new_generation, $cache_group, self::CACHE_TTL );
		} else {
			$cache_group = trim( strval( $cache_group ) );
			if ( 0 < strlen( $cache_group ) ) {
				$wpdb->query(
					$wpdb->prepare(
						"DELETE FROM $wpdb->options WHERE option_name LIKE %s AND option_name LIKE %s",
						$wpdb->esc_like( '_transient_' ) . '%',
						'%' . $wpdb->esc_like( $cache_group )
					)
				); // WPCS: db call ok; cache ok.
			}

			wp_cache_delete( 'alloptions', 'options' );
			set_transient( 'generation' . $cache_group, $new_generation, self::CACHE_TTL );
		}
	}

	/**
	 * Busts cache data related to a product that has been updated.
	 *
	 * @param int $product_id The BigCommerce product ID.
	 */
	public function bust_product_cache( $product_id ) {
		foreach ( $this->get_paths() as $path ) {
			$this->bust_cache_group( $this->get_cache_group_name( $path ) );
		}

		$this->bust_cache_group(
			$this->get_cache_group_name(
				sprintf( '%s%s', trailingslashit( self::PRODUCTS_PATH ), strval( $product_id ) )
			)
		);
	}
}
