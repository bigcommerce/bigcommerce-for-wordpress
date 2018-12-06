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
		/*
		 * If we're not using AMP classic and are using paired/native instead,
		 * then we should only override `components/*` templates. All the other
		 * templates (i.e. single product, archive etc) will be rendered from
		 * the main WP theme template and thus we should not override that.
		 */
		if ( ! $this->is_classic() && ! strpos( $path, 'components' ) ) {
			return $path;
		}

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

	/**
	 * Override classic template paths for the custom AMP classic theme.
	 *
	 * @param string   $file The absolute path to the amp template.
	 * @param string   $template_type The type of template being served.
	 * @param \WP_Post $post The post object of the current post being viewed.
	 *
	 * @return string
	 * @filter amp_post_template_file
	 */
	public function override_classic_amp_template_path( $file, $template_type, $post ) {
		$template = $template_type . '-' . $post->post_type . '.php';
		$file     = $this->override_template_path( $file, $template );
		return $file;
	}

	/**
	 * Load header file from the plugin.
	 *
	 * @param string $file      Template file path.
	 * @param string $type      Template type.
	 * @param array  $container \BigCommerce\Container\Amp
	 *
	 * @return string
	 */
	public function override_classic_header_bar_template( $file, $type, $container ) {
		if ( 'header-bar' === $type ) {
			$file = $this->override_template_path( $file, 'components/header/header-bar.php' );
		}
		return $file;
	}

	/**
	 * Is classic mode enabled for AMP rendering?
	 *
	 * @return bool
	 */
	public function is_classic() {
		if ( is_callable( [ '\AMP_Options_Manager', 'get_option' ] ) ) {
			$theme_support = \AMP_Options_Manager::get_option( 'theme_support', false );

			if ( 'native' === $theme_support || 'paired' === $theme_support ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Adds header_nav_menu to the AMP data in classic mode.
	 *
	 * @param array $data AMP template data.
	 * @return array Filtered data.
	 */
	public function provide_header_nav_menu( $data ) {
		if ( ! isset( $data['header_nav_menu'] ) ) {
			$data['header_nav_menu'] = wp_nav_menu(
				array(
					'theme_location' => 'amp-menu',
					'container'      => 'false',
					'depth'          => 1,
					'echo'           => false,
				)
			);
		}

		return $data;
	}
}
