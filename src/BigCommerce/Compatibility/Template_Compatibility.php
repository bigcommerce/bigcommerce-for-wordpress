<?php


namespace BigCommerce\Compatibility;

/**
 * Provides compatibility functionality to override WooCommerce page templates.
 * This class handles the removal of WooCommerce-specific templates from the page template hierarchy.
 *
 * @package BigCommerce
 * @subpackage Compatibility
 */
class Template_Compatibility {
	/**
	 * Overrides the page template for WooCommerce pages that assume WooCommerce functions will be available.
	 *
	 * If a theme has a page template (e.g., `page-cart.php`) that relies on WooCommerce functions, 
	 * this method will remove that template from the hierarchy to prevent errors if WooCommerce is not present.
	 *
	 * @param string $template The current template being used for the page.
	 * @param string $type The type of template being overridden.
	 * @param array  $templates List of available templates in the hierarchy.
	 *
	 * @return string The modified template, or the original template if no changes were made.
	 * @filter page_template
	 */
	public function override_page_template( $template, $type, $templates ) {
		while ( ! empty( $templates ) && ! empty( $template ) ) {
			$template_contents = file_get_contents( $template );
			// covers wc_get_template(), wc_get_template_part(), and wc_get_template_html()
			if ( strpos( $template_contents, 'wc_get_template' ) === false ) {
				break;
			}
			array_shift( $templates );
			$template = locate_template( $templates );
		}

		return $template;
	}
}