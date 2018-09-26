<?php


namespace BigCommerce\Settings;


use BigCommerce\Pages\Cart_Page;

class Cart extends Settings_Section {
	const NAME                = 'cart';
	const OPTION_ENABLE_CART  = 'bigcommerce_enable_cart';
	const OPTION_CART_PAGE_ID = Cart_Page::NAME;

	private $cart_page;

	public function __construct( Cart_Page $cart_page ) {
		$this->cart_page = $cart_page;
	}

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Cart Settings', 'bigcommerce' ),
			function ( $section ) {
				do_action( 'bigcommerce/settings/render/cart', $section );
			},
			Settings_Screen::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_ENABLE_CART
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_CART_PAGE_ID
		);


		add_settings_field(
			self::OPTION_ENABLE_CART,
			esc_html( __( 'Enable Cart', 'bigcommerce' ) ),
			[ $this, 'render_enable_cart_field', ],
			Settings_Screen::NAME,
			self::NAME
		);

		add_settings_field(
			self::OPTION_CART_PAGE_ID,
			esc_html( __( 'Cart Page', 'bigcommerce' ) ),
			[ $this, 'render_cart_page_field', ],
			Settings_Screen::NAME,
			self::NAME
		);
	}

	public function render_enable_cart_field() {
		$value    = (bool) get_option( self::OPTION_ENABLE_CART, true );
		$checkbox = sprintf( '<input type="checkbox" value="1" class="regular-text code" name="%s" %s />', esc_attr( self::OPTION_ENABLE_CART ), checked( true, $value, false ) );
		printf( '<p class="description">%s %s</p>', $checkbox, __( 'If enabled, customers will be able to add products to a cart before proceeding to checkout. If disabled, products will use a Buy Now button that takes them directly to checkout.', 'bigcommerce' ) );
	}

	public function render_cart_page_field() {
		$value      = (int) get_option( self::OPTION_CART_PAGE_ID, 0 );
		$candidates = $this->cart_page->get_post_candidates();
		$options = array_map( function( $post_id ) use ( $value ) {
			return sprintf( '<option value="%d" %s>%s</option>', $post_id, selected( $post_id, $value, false ), esc_html( get_the_title( $post_id ) ) );
		}, $candidates );
		if ( empty( $options ) ) {
			$options = [
				sprintf( '<option value="0">&mdash; %s &mdash;</option>', __( 'No pages available', 'bigcommerce' ) ),
			];
			$description = sprintf( __( 'Create a page with the [%s] shortcode, then select it here as the cart page.', 'bigcommerce' ), \BigCommerce\Shortcodes\Cart::NAME );
		} else {
			array_unshift( $options, sprintf( '<option value="0">&mdash; %s &mdash;</option>', __( 'Select Cart Page', 'bigcommerce' ) ) );
		}

		printf( '<select name="%s" class="regular-text bc-field-choices">%s</select>', esc_attr( self::OPTION_CART_PAGE_ID ), implode( "\n", $options ) );
		if ( ! empty( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}

	}

}
