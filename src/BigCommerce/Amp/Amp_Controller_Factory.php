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
 * Overrides the template controller factory to inject AMP
 * controllers when necessary.
 */
class Amp_Controller_Factory extends Controller_Factory {

	/**
	 * @var array A list of all template classes that should be
	 *            overridden with their AMP counterparts. Keys
	 *            in this array should be the "standard" fully-
	 *            qualified class names. Values should be the
	 *            replacement AMP class names.
	 */
	private $override_class_map = [
		Cart_Actions::class => Amp_Cart_Actions::class,
		Cart_Items::class   => Amp_Cart_Items::class,
		Cart_Summary::class => Amp_Cart_Summary::class,
	];

	public function get_controller( $classname, array $options = [], $template = '' ) {
		if ( array_key_exists( $classname, $this->override_class_map ) ) {
			$classname = $this->override_class_map[ $classname ];
		}
		return parent::get_controller( $classname, $options, $template );
	}

}
