<?php


namespace BigCommerce\Container;


use BigCommerce\Analytics\Events;
use BigCommerce\Analytics\Facebook_Pixel;
use BigCommerce\Analytics\Google_Analytics;
use BigCommerce\Analytics\Segment;
use Pimple\Container;

/**
 * Handles the registration and configuration of analytics services and events for BigCommerce.
 * This class provides integration for popular analytics platforms like Facebook Pixel, Google Analytics, and Segment.
 * It also manages the registration of event tracking for actions such as adding to cart and viewing products.
 *
 * @package BigCommerce\Container
 */
class Analytics extends Provider {
    
    /**
     * Constant for Facebook Pixel analytics provider
     *
     * @var string
     */
    const FACEBOOK_PIXEL   = 'analytics.facebook';

    /**
     * Constant for Google Analytics provider
     *
     * @var string
     */
    const GOOGLE_ANALYTICS = 'analytics.google';

    /**
     * Constant for Segment analytics provider
     *
     * @var string
     */
    const SEGMENT          = 'analytics.segment';

    /**
     * Constant for 'Add to Cart' event tracking
     *
     * @var string
     */
    const ADD_TO_CART  = 'analytics.events.add_to_cart';

    /**
     * Constant for 'View Product' event tracking
     *
     * @var string
     */
    const VIEW_PRODUCT = 'analytics.events.view_product';

    /**
     * Registers analytics services and events into the container
     *
     * This function registers all necessary analytics providers (Facebook Pixel, Google Analytics, Segment)
     * and event listeners for tracking various interactions such as add-to-cart and product views.
     *
     * @param Container $container The dependency injection container.
     *
     * @return void
     */
	public function register( Container $container ) {
		$this->providers( $container );
		$this->events( $container );
	}

	/**
	 * Register analytics providers
	 *
	 * @param Container $container
	 *
	 * @return void
	 */
	private function providers( Container $container ) {
		$container[ self::FACEBOOK_PIXEL ] = function ( Container $container ) {
			return new Facebook_Pixel();
		};

		/*add_action( 'wp_head', $this->create_callback( 'facebook_pixel', function () use ( $container ) {
			$container[ self::FACEBOOK_PIXEL ]->render_tracking_code();
		} ), 10, 0 );*/

		$container[ self::GOOGLE_ANALYTICS ] = function ( Container $container ) {
			return new Google_Analytics();
		};

		/*add_action( 'wp_head', $this->create_callback( 'google_analytics', function () use ( $container ) {
			$container[ self::GOOGLE_ANALYTICS ]->render_tracking_code();
		} ), 10, 0 );*/

		$container[ self::SEGMENT ] = function ( Container $container ) {
			return new Segment();
		};

		add_action( 'wp_head', $this->create_callback( 'segment', function () use ( $container ) {
			$container[ self::SEGMENT ]->render_tracking_code();
		} ), 10, 0 );
	}

	/**
	 * Register analytics events
	 *
	 * @param Container $container
	 *
	 * @return void
	 */
	private function events( Container $container ) {
		$container[ self::ADD_TO_CART ] = function ( Container $container ) {
			return new Events\Add_To_Cart();
		};

		add_filter( 'bigcommerce/messages/success/arguments', $this->create_callback( 'add_to_cart_success_tracking_attributes', function ( $args, $data ) use ( $container ) {
			return $container[ self::ADD_TO_CART ]->set_tracking_attributes_on_success_message( $args, $data );
		} ), 10, 2 );

		add_filter( 'bigcommerce/button/purchase/attributes', $this->create_callback( 'add_to_cart_button_tracking_attributes', function ( $attributes, $product ) use ( $container ) {
			return $container[ self::ADD_TO_CART ]->add_tracking_attributes_to_purchase_button( $attributes, $product );
		} ), 10, 2 );

		$container[ self::VIEW_PRODUCT ] = function ( Container $container ) {
			return new Events\View_Product();
		};

		add_filter( 'bigcommerce/template=components/products/view-product-button.php/options', $this->create_callback( 'view_product_button', function ( $options, $template ) use ( $container ) {
			return $container[ self::VIEW_PRODUCT ]->add_tracking_attributes_to_button( $options, $template );
		} ), 10, 2 );

		add_filter( 'bigcommerce/template=components/products/product-card.php/options', $this->create_callback( 'quickview_product_button', function ( $options, $template ) use ( $container ) {
			return $container[ self::VIEW_PRODUCT ]->add_tracking_attributes_to_button( $options, $template );
		} ), 10, 2 );

		add_filter( 'bigcommerce/template=components/products/product-title.php/options', $this->create_callback( 'view_product_title', function( $options, $template ) use ( $container ) {
			return $container[ self::VIEW_PRODUCT ]->add_tracking_attributes_to_permalink( $options, $template );
		}), 10, 3 );
		
		add_filter( \BigCommerce\Settings\Sections\Analytics::TRACK_BY_HOOK, $this->create_callback( 'change_track_by_options', function( $track_data ) use ( $container ) {
			return $container[ self::VIEW_PRODUCT ]->change_track_data( $track_data );
		}), 10, 1 );
	}

}
