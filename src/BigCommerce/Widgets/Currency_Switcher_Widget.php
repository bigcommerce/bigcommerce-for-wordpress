<?php


namespace BigCommerce\Widgets;

use BigCommerce\Settings\Sections\Currency as Currency_Settings;
use BigCommerce\Templates\Currency_Switcher_Form;

/**
 * Class Currency_Switcher_Widget
 *
 * A widget to display a currency switcher
 */
class Currency_Switcher_Widget extends \WP_Widget {

	const NAME = 'bigcommerce_currency_switcher';

	/**
	 * Sets up a new Product Categories widget instance.
	 */
	public function __construct() {
		$widget_ops = [
			'classname'                   => self::NAME,
			'description'                 => __( 'Display a currency switcher', 'bigcommerce' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		];
		parent::__construct( self::NAME, __( 'Currency Switcher', 'bigcommerce' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Currency Swithcer widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! get_option( Currency_Settings::ENABLE_CURRENCY_SWITCHER, false ) ) {
			return;
		}

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );


		echo wp_kses_post( $args['before_widget'] );

		if ( $title ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		$currencies = get_option( Currency_Settings::ENABLED_CURRENCIES, [] );
		$component  = Currency_Switcher_Form::factory([
			/**
			 * Filters the list of enabled currencies for the current channel.
			 *
			 * This filter returns a list of currencies that are enabled for the current channel,
			 * allowing for dynamic adjustment based on context.
			 *
			 * @return array The list of enabled currencies.
			 */
			Currency_Switcher_Form::ENABLED_CURRENCIES => apply_filters( 'bigcommerce/currency/enabled', $currencies ),
			Currency_Switcher_Form::SELECTED_CURRENCY  => apply_filters( 'bigcommerce/currency/code', 'USD' ),
		]);
		echo $component->render();

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, [ 'title' => '' ] );
		$title    = sanitize_text_field( $instance['title'] );
		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bigcommerce' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>" /></p>
		<?php
	}

}
