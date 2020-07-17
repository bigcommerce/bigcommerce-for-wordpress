<?php


namespace BigCommerce\Widgets;

use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Checkout_Page;

/**
 * Class Product_Category_Widget
 *
 * A widget to display a list or dropdown of product categories
 */
class Mini_Cart_Widget extends \WP_Widget {

	const NAME = 'bigcommerce_mini_cart';

	/**
	 * Sets up a new Product Categories widget instance.
	 */
	public function __construct() {
		$widget_ops = [
			'classname'                   => self::NAME,
			'description'                 => __( "A compact version of the visitor's cart", 'bigcommerce' ),
			'customize_selective_refresh' => true,
		];
		parent::__construct( self::NAME, __( 'Mini-Cart', 'bigcommerce' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Mini Cart widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( is_page( get_option( Cart_Page::NAME, 0 ) ) || is_page( get_option( Checkout_Page::NAME, 0 ) ) ) {
			return;
		}
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );


		echo wp_kses_post( $args['before_widget'] );

		if ( $title ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		printf( '<div data-js="bc-mini-cart"><span class="bc-loading">%s</span></div>', esc_html( __( 'Loading', 'bigcommerce' ) ) );

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Handles updating settings for the current Product Categories widget instance.
	 *
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
	 * Outputs the settings form for the Product Categories widget.
	 *
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
