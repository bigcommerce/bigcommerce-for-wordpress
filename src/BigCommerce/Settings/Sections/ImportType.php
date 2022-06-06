<?php

namespace BigCommerce\Settings\Sections;

trait ImportType {

	public function render_headless_flag_import() {
		$current = get_option( Import::HEADLESS_FLAG, 0 );

		printf( '<select name="%s" class="regular-text bc-field-choices" data-js="bc-import-switch">', esc_attr( Import::HEADLESS_FLAG ) );

		foreach ( $this->options as $key => $option ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $key ),
				selected( $current, $key, false ),
				$option
			);
		}

		printf( '</select>' );
	}

}
