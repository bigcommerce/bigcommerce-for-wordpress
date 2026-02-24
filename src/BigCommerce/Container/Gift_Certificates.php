<?php


namespace BigCommerce\Container;


use BigCommerce\Gift_Certificates\Sub_Nav;
use Pimple\Container;

/**
 * This class provides functionality for registering and handling the gift certificates sub-navigation
 * in the BigCommerce environment. It extends the Provider class and implements a registration method
 * to add a sub-navigation to the content.
 *
 * @package BigCommerce\Container
 */
class Gift_Certificates extends Provider {

    /**
     * The key used for accessing the gift certificates sub-navigation service in the container.
     *
     * This constant defines the service key for the gift certificates sub-navigation, allowing it to be
     * accessed through the container.
     *
     * @var string
     */
    const SUB_NAV = 'gift_certificates.sub_nav';

    /**
     * Registers the gift certificates sub-navigation service and hooks the sub-navigation to the content.
     *
     * This method registers the `SUB_NAV` service in the container and defines a callback that inserts
     * the gift certificates sub-navigation above the content. The callback is hooked into the `the_content`
     * filter to ensure the sub-navigation is added to the content when it is being rendered.
     *
     * @param Container $container The Pimple container instance used for managing dependencies.
     */
	public function register( Container $container ) {
		$container[ self::SUB_NAV ] = function ( Container $container ) {
			return new Sub_Nav();
		};

		/**
		 * Adds the gift certificates sub-navigation above the content.
		 *
		 * This callback function is hooked into the `the_content` filter to insert the gift
		 * certificates sub-navigation at the beginning of the content.
		 *
		 * @param string $content The post content.
		 *
		 * @return string Modified content with the gift certificates sub-navigation added above.
		 */
		add_filter( 'the_content', $this->create_callback( 'gift_certificates_subnav', function ( $content ) use ( $container ) {
			return $container[ self::SUB_NAV ]->add_subnav_above_content( $content );
		} ), 10, 1 );
	}

}