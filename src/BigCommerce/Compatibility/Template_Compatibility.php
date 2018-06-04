<?php


namespace BigCommerce\Compatibility;


class Template_Compatibility {
	/**
	 * If a theme has a page template that assumes WooCommerce
	 * functions will be available (e.g., a page-cart.php),
	 * remove that template from the hierarchy.
	 *
	 * @param string $template
	 * @param string $type
	 * @param array  $templates
	 *
	 * @return string
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