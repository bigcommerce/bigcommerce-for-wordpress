<?php


namespace BigCommerce\Settings\Sections;

/**
 * Class Units
 *
 * May eventually expose UI for these settings, but for now
 * they are imported from the BigCommerce API and not exposed
 * in the admin.
 */
class Units extends Settings_Section {
	const MASS   = 'bigcommerce_mass_unit';
	const LENGTH = 'bigcommerce_length_unit';

	const POUND    = 'lb';
	const OUNCE    = 'oz';
	const KILOGRAM = 'kg';
	const GRAM     = 'g';
	const TONNE    = 'tonne';

	const INCH       = 'in';
	const CENTIMETER = 'cm';
}