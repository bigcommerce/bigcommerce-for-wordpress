<?php

namespace BigCommerce\Settings\Sections;

use BigCommerce\Import\Image_Importer;

trait Images {

	/**
	 * Display images import radio toggle. Adds ability to switch between images import types
	 *
	 * @param array $args
	 */
	public function enable_images_import_toggle( $args = [] ) {
		$current  = get_option( $args['option'], Image_Importer::FULL_IMAGE_IMPORT );
		$markup   = '<p><label><input type="%s" name="%s" value="%s" %s /> %s</label></p>';
		printf( '<p class="description">%s</p>', esc_html( $args['label'] ) );
		echo '<fieldset class="bc-settings-traditional">';
		printf(
			$markup,
			$args['type'],
			esc_attr( $args['option'] ),
			Image_Importer::FULL_IMAGE_IMPORT,
			checked( Image_Importer::FULL_IMAGE_IMPORT, $current, false ),
			esc_html( __( 'Full images import', 'bigcommerce' ) )
		);
		printf(
			$markup,
			$args['type'],
			esc_attr( $args['option'] ),
			Image_Importer::CDN_IMAGE_IMPORT,
			checked( Image_Importer::CDN_IMAGE_IMPORT, $current, false ),
			esc_html( __( "Import images URLs only", 'bigcommerce' ) )
		);
		printf(
			$markup,
			$args['type'],
			esc_attr( $args['option'] ),
			Image_Importer::DISABLE_IMAGE_IMPORT,
			checked( Image_Importer::DISABLE_IMAGE_IMPORT, $current, false ),
			esc_html( __( "Disable images import", 'bigcommerce' ) )
		);
		echo '</fieldset>';
	}

}
