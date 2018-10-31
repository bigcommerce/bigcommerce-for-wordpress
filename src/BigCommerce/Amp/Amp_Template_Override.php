<?php


namespace BigCommerce\Amp;

/**
 * Class Amp_Template_Override
 *
 * Responsible for loading AMP templates when AMP mode is enabled
 */
class Amp_Template_Override {
	/**
	 * @var string The name of the directory containing AMP template overrides
	 */
	private $amp_directory;

	public function __construct( $amp_directory = 'amp' ) {
		$this->amp_directory = $amp_directory;
	}

	/**
	 * @param string $path          The absolute path to the template
	 * @param string $relative_path The relative path of the requested template
	 *
	 * @return string The filtered path to the template
	 * @filter bigcommerce/template/path
	 */
	public function override_template_path( $path, $relative_path ) {
		$amp_path          = '';
		$amp_relative_path = trailingslashit( $this->amp_directory ) . $relative_path;

		/**
		 * This filter is documented in src/BigCommerce/Templates/Controller.php
		 */
		$theme_dir = apply_filters( 'bigcommerce/template/directory/theme', '', $amp_relative_path );

		/**
		 * This filter is documented in src/BigCommerce/Templates/Controller.php
		 */
		$plugin_dir = apply_filters( 'bigcommerce/template/directory/plugin', '', $amp_relative_path );
		if ( ! empty( $theme_dir ) ) {
			$amp_path = locate_template( trailingslashit( $theme_dir ) . $amp_relative_path );
		}

		// no template in the theme, so fall back to the plugin default
		if ( empty( $amp_path ) && ! empty( $plugin_dir ) ) {
			$amp_path = trailingslashit( $plugin_dir ) . $amp_relative_path;
		}

		// check that we actually have an AMP override for this template
		if ( ! empty( $amp_path ) && file_exists( $amp_path ) ) {
			$path = $amp_path;
		}

		return $path;
	}
}