<?php


namespace BigCommerce\Compatibility\Themes;

use BigCommerce\Compatibility\Themes\Flatsome\Flatsome;

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
	 * @return BigCommerce\Compatibility\Themes\Theme
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