<?php


namespace BigCommerce\Widgets;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * Class Product_Category_Widget
 *
 * A widget to display a list or dropdown of product categories
 */
class Product_Category_Widget extends \WP_Widget {

	const NAME = 'bigcommerce_product_categories';

	/**
	 * Sets up a new Product Categories widget instance.
	 */
	public function __construct() {
		$widget_ops = [
			'classname'                   => self::NAME,
			'description'                 => __( 'A list or dropdown of product categories.', 'bigcommerce' ),
			'customize_selective_refresh' => true,
		];
		parent::__construct( self::NAME, __( 'Product Categories', 'bigcommerce' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Product Categories widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Categories widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Product Categories', 'bigcommerce' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$show_count   = ! empty( $instance[ 'count' ] );
		$hierarchical = ! empty( $instance[ 'hierarchical' ] );
		$use_dropdown = ! empty( $instance[ 'dropdown' ] );

		$cat_args = [
			'orderby'      => 'name',
			'show_count'   => $show_count,
			'hierarchical' => $hierarchical,
			'taxonomy'     => Product_Category::NAME,
			'echo'         => false,
		];

		$markup = '';

		if ( $title ) {
			$markup .= $args[ 'before_title' ] . $title . $args[ 'after_title' ];
		}

		if ( $use_dropdown ) {
			$markup .= $this->category_dropdown( $title, $cat_args, $instance );
		} else {
			$markup .= $this->category_list( $cat_args, $instance );
		}

		$markup = $args[ 'before_widget' ] . $markup . $args[ 'after_widget' ];

		$allowed_html = array_merge( wp_kses_allowed_html( 'post' ), [
			'select' => [
				'id'    => [],
				'class' => [],
				'name'  => [],
			],
			'option' => [
				'value'    => [],
				'selected' => [],
				'class'    => [],
			],
			'form' => [
				'action' => [],
				'method' => [],
			],
		] );

		echo wp_kses( $markup, $allowed_html );
	}

	protected function category_dropdown( $title, $args, $instance ) {
		$dropdown_id = "{$this->id_base}-dropdown-{$this->number}";

		$args[ 'id' ]                = $dropdown_id;
		$args[ 'name' ]              = Product_Category::NAME;
		$args[ 'value_field' ]       = 'slug';
		$args[ 'show_option_none' ]  = __( 'Select Category', 'bigcommerce' );
		$args[ 'option_none_value' ] = '';

		$store_url = get_post_type_archive_link( Product::NAME ) ?: home_url();

		$dropdown = sprintf( '<form action="%s" method="get">', esc_url( $store_url ) );
		$dropdown .= '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $title . '</label>';


		/**
		 * Filters the arguments for the Product Categories Categories widget drop-down.
		 *
		 * @see   wp_dropdown_categories()
		 *
		 * @param array $args     An array of Product Categories widget drop-down arguments.
		 * @param array $instance Array of settings for the current widget.
		 */
		$dropdown .= wp_dropdown_categories( apply_filters( 'bigcommerce/widget/categories/dropdown_args', $args, $instance ) );

		$dropdown .= '</form>';
		$this->dropdown_js( $dropdown_id );

		return $dropdown;
	}

	protected function dropdown_js( $dropdown_id ) {
		$inline_js = "
			(function () {
				var dropdown = document.getElementById('{$dropdown_id}');

				function onCatChange() {
					if (dropdown.options[dropdown.selectedIndex].value !== '') {
						dropdown.parentNode.submit();
					}
				}

				dropdown.onchange = onCatChange;
			})();
		";

		wp_add_inline_script( 'bigcommerce-scripts', $inline_js );
	}

	protected function category_list( $args, $instance ) {
		$args[ 'title_li' ] = '';

		/**
		 * Filters the arguments for the Product Categories widget.
		 *
		 * @param array $args     An array of Product Categories widget options.
		 * @param array $instance Array of settings for the current widget.
		 */
		$list = wp_list_categories( apply_filters( 'bigcommerce/widget/categories/list_args', $args, $instance ) );

		return sprintf( '<ul>%s</ul>', $list );
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
		$instance                   = $old_instance;
		$instance[ 'title' ]        = sanitize_text_field( $new_instance[ 'title' ] );
		$instance[ 'count' ]        = ! empty( $new_instance[ 'count' ] ) ? 1 : 0;
		$instance[ 'hierarchical' ] = ! empty( $new_instance[ 'hierarchical' ] ) ? 1 : 0;
		$instance[ 'dropdown' ]     = ! empty( $new_instance[ 'dropdown' ] ) ? 1 : 0;

		return $instance;
	}

	/**
	 * Outputs the settings form for the Product Categories widget.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		//Defaults
		$instance     = wp_parse_args( (array) $instance, [ 'title' => '' ] );
		$title        = sanitize_text_field( $instance[ 'title' ] );
		$count        = isset( $instance[ 'count' ] ) ? (bool) $instance[ 'count' ] : false;
		$hierarchical = isset( $instance[ 'hierarchical' ] ) ? (bool) $instance[ 'hierarchical' ] : false;
		$dropdown     = isset( $instance[ 'dropdown' ] ) ? (bool) $instance[ 'dropdown' ] : false;
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bigcommerce' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
						 name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
						 value="<?php echo esc_attr( $title ); ?>"/></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>"
							name="<?php echo esc_attr( $this->get_field_name( 'dropdown' ) ); ?>"<?php checked( $dropdown ); ?> />
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>"><?php esc_html_e( 'Display as dropdown', 'bigcommerce' ); ?></label><br/>

			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"
						 name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Show post counts', 'bigcommerce' ); ?></label><br/>

			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'hierarchical' ) ); ?>"
						 name="<?php echo esc_attr( $this->get_field_name( 'hierarchical' ) ); ?>"<?php checked( $hierarchical ); ?> />
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'hierarchical' ) ); ?>"><?php esc_html_e( 'Show hierarchy', 'bigcommerce' ); ?></label>
		</p>
		<?php
	}

}