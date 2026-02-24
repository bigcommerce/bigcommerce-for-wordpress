<?php


namespace BigCommerce\Compatibility\Themes;

use BigCommerce\Compatibility\Themes\Flatsome\Flatsome;

/**
 * Factory class for creating theme instances based on template name and version.
 * It supports creating instances of themes such as Flatsome and provides a fallback to a Null_Theme
 * when the template is unsupported or the version is not compatible.
 *
 * @package BigCommerce
 * @subpackage Compatibility\Themes
 */
class Theme_Factory {

	/**
	 * Supported themes
	 *
	 * @var array
	 */
	protected $supported = [
		'flatsome' => Flatsome::class,
	];

	/**
	 * Creates an instance of the appropriate theme based on the template and version.
	 *
	 * @param string $template The theme template name.
	 * @param string $version  The theme version.
	 *
	 * @return BigCommerce\Compatibility\Themes\Theme The theme instance, or a Null_Theme if unsupported.
	 */
	public function make( $template, $version = '1.0.0' ) {
		if ( isset( $this->supported[ $template ] ) ) {
			$theme = new $this->supported[ $template ];
			if ( $theme->is_version_supported( $version ) ) {
				return $theme;
			}
		}
		
		return new Null_Theme();
	}

}