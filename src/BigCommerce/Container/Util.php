<?php

namespace BigCommerce\Container;

use BigCommerce\Util\Kses;
use Pimple\Container;

/**
 * Class Util
 *
 * Provides utility services for the application, such as KSES (WordPress content filtering).
 *
 * @package BigCommerce\Container
 */
class Util extends Provider {
    /**
     * Identifier for the KSES utility service.
     *
     * @var string
     */
    const KSES = 'util.kses';

    /**
     * Registers utility services in the container.
     *
     * @param Container $container The service container to register services into.
     */
    public function register( Container $container ) {
        $container[ self::KSES ] = function ( Container $container ) {
            return new Kses();
        };

        add_action( 
            'wp_kses_allowed_html', 
            $this->create_callback( 
                'kses_allowed_html', 
                function ( $allowed_tags, $context ) use ( $container ) {
                    return $container[ self::KSES ]->product_description_allowed_html( $allowed_tags, $context );
                } 
            ), 
            10, 
            2 
        );
    }
}
