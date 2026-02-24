<?php

namespace BigCommerce\Amp;

use BigCommerce\Templates\Controller_Factory;
use BigCommerce\Templates\Cart_Actions;
use BigCommerce\Templates\Amp_Cart_Actions;
use BigCommerce\Templates\Cart_Items;
use BigCommerce\Templates\Amp_Cart_Items;
use BigCommerce\Templates\Cart_Summary;
use BigCommerce\Templates\Amp_Cart_Summary;

/**
 * Class Amp_Controller_Factory
 *
 * Overrides the template controller factory to replace default
 * template controllers with AMP-specific counterparts when required.
 *
 * @package BigCommerce\Amp
 */
class Amp_Controller_Factory extends Controller_Factory {

    /**
     * Mapping of standard template classes to their AMP-specific counterparts.
     *
     * This array is used to override the default template controllers with
     * AMP-compatible versions. Keys are the fully-qualified names of the standard
     * classes, and values are the corresponding AMP class names.
     *
     * @var array<string, string>
     */
    private $override_class_map = [
        Cart_Actions::class => Amp_Cart_Actions::class,
        Cart_Items::class   => Amp_Cart_Items::class,
        Cart_Summary::class => Amp_Cart_Summary::class,
    ];

    /**
     * Retrieves the appropriate controller for a given class name.
     *
     * Overrides the default behavior by replacing the standard class name with
     * its AMP counterpart if defined in the `$override_class_map`.
     *
     * @param string $classname Fully-qualified class name of the controller.
     * @param array  $options   Optional parameters passed to the controller.
     * @param string $template  Optional template name to associate with the controller.
     *
     * @return object The controller instance.
     */
    public function get_controller( $classname, array $options = [], $template = '' ) {
        if ( array_key_exists( $classname, $this->override_class_map ) ) {
            $classname = $this->override_class_map[ $classname ];
        }
        return parent::get_controller( $classname, $options, $template );
    }
}
