<?php


namespace BigCommerce\Settings\Sections;

/**
 * Class Wishlists
 *
 * May eventually expose UI for these settings, but for now
 * they are imported from the BigCommerce API and not exposed
 * in the admin.
 */
class Wishlists extends Settings_Section {
	const ENABLED = 'bigcommerce_wishlists_enabled';
}
