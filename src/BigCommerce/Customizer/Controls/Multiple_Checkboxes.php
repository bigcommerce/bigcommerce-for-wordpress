<?php


namespace BigCommerce\Customizer\Controls;

/**
 * Class Multiple_Checkboxes
 *
 * A customizer control that renders as a list of checkboxes
 */
class Multiple_Checkboxes extends \WP_Customize_Control {
	public $type = 'checkbox-multiple';

	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		if ( ! empty( $this->label ) ) {
			printf( '<span class="customize-control-title">%s</span>', esc_html( $this->label ) );
		}

		if ( ! empty( $this->description ) ) {
			printf( '<span class="description customize-control-description">', $this->description );
		}

		$selected = $this->value();
		if ( ! is_array( $selected ) ) {
			$selected = explode( ',', $selected );
		}

		echo '<ul>';
		foreach ( $this->choices as $key => $label ) {
			echo '<li>';
			echo '<label>';
			printf(
				'<input type="checkbox" value="%s" %s /> %s',
				esc_attr( $key ),
				checked( in_array( $key, $selected ), true, false ),
				esc_html( $label )
			);
			echo '</label>';
			echo '</li>';
		}
		echo '</ul>';

		// hidden field to store the canonical value for the customizer API
		printf(
			'<input type="hidden" %s value="%s" />',
			$this->get_link(),
			esc_attr( implode( ',', $selected ) )
		);
	}
}