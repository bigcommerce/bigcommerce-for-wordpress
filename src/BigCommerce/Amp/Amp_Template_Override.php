<?php

namespace BigCommerce\Amp;

/**
 * Class Amp_Template_Override
 *
 * Responsible for overriding template paths to provide AMP-specific
 * versions when AMP mode is enabled.
 *
 * @package BigCommerce\Amp
 */
class Amp_Template_Override {
    /**
     * Directory name containing AMP-specific template overrides.
     *
     * @var string
     */
    private $amp_directory;

    /**
     * Constructor for the Amp_Template_Override class.
     *
     * @param string $amp_directory Name of the directory containing AMP template overrides. Default is 'amp'.
     */
    public function __construct( $amp_directory = 'amp' ) {
        $this->amp_directory = $amp_directory;
    }

    /**
     * Filters the path to a requested template, providing AMP-specific versions if available.
     *
     * @param string $path          The absolute path to the requested template.
     * @param string $relative_path The relative path of the template within the theme/plugin.
     *
     * @return string The filtered template path, modified for AMP if applicable.
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

        $amp_path = '';
        $amp_relative_path = trailingslashit( $this->amp_directory ) . $relative_path;

        /**
         * Filter the theme directory for AMP templates.
         *
         * @param string $theme_dir The directory for AMP templates within the theme.
         * @param string $amp_relative_path The relative path of the requested template.
         */
        $theme_dir = apply_filters( 'bigcommerce/template/directory/theme', '', $amp_relative_path );

        /**
         * Filter the plugin directory for AMP templates.
         *
         * @param string $plugin_dir The directory for AMP templates within the plugin.
         * @param string $amp_relative_path The relative path of the requested template.
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
     * Overrides template paths for classic AMP templates.
     *
     * @param string   $file          The absolute path to the AMP template.
     * @param string   $template_type The type of template being served (e.g., single, archive).
     * @param \WP_Post $post          The current post object.
     *
     * @return string The overridden template path.
     * @filter amp_post_template_file
     */
    public function override_classic_amp_template_path( $file, $template_type, $post ) {
        $template = $template_type . '-' . $post->post_type . '.php';
        $file = $this->override_template_path( $file, $template );
        return $file;
    }

    /**
     * Overrides the header bar template for AMP classic mode.
     *
     * @param string $file      Template file path.
     * @param string $type      Template type (e.g., 'header-bar').
     * @param array  $container \BigCommerce\Container\Amp
     *
     * @return string The overridden template file path.
     */
    public function override_classic_header_bar_template( $file, $type, $container ) {
        if ( 'header-bar' === $type ) {
            $file = $this->override_template_path( $file, 'components/header/header-bar.php' );
        }
        return $file;
    }

    /**
     * Determines whether AMP is in classic mode.
     *
     * Classic mode renders AMP templates for all pages, while paired/native mode only
     * renders AMP templates for specific components.
     *
     * @return bool True if classic mode is enabled; false otherwise.
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
     * Provides the header navigation menu data in AMP classic mode.
     *
     * @param array $data AMP template data.
     *
     * @return array Filtered data with `header_nav_menu` included.
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
