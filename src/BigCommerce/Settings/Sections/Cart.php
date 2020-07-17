<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Checkout_Page;
use BigCommerce\Pages\Required_Page;
use BigCommerce\Settings\Screens\Settings_Screen;

class Cart extends Settings_Section {
	use WithPages;

	const NAME                     = 'cart';
	const OPTION_ENABLE_CART       = 'bigcommerce_enable_cart';
	const OPTION_AJAX_CART         = 'bigcommerce_ajax_cart';
	const OPTION_CART_PAGE_ID      = Cart_Page::NAME;
	const OPTION_EMBEDDED_CHECKOUT = 'bigcommerce_enable_embedded_checkout';

	private $cart_page;
	private $checkout_page;

	public function __construct( Cart_Page $cart_page, Checkout_Page $checkout_page ) {
		$this->cart_page     = $cart_page;
		$this->checkout_page = $checkout_page;
	}

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Cart & Checkout', 'bigcommerce' ),
			function ( $section ) {
				do_action( 'bigcommerce/settings/render/cart', $section );
			},
			Settings_Screen::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_ENABLE_CART
		);

		add_settings_field(
			self::OPTION_ENABLE_CART,
			esc_html( __( 'Enable Cart', 'bigcommerce' ) ),
			[ $this, 'render_enable_cart_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'label_for' => 'field-' . self::OPTION_ENABLE_CART,
			]
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_AJAX_CART
		);

		add_settings_field(
			self::OPTION_AJAX_CART,
			esc_html( __( 'Ajax Cart', 'bigcommerce' ) ),
			[ $this, 'render_ajax_cart_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'label_for' => 'field-' . self::OPTION_AJAX_CART,
			]
		);

		register_setting(
			Settings_Screen::NAME,
			$this->cart_page->get_option_name()
		);
		add_settings_field(
			$this->cart_page->get_option_name(),
			$this->cart_page->get_post_state_label(),
			[ $this, 'render_page_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'page'      => $this->cart_page,
				'label_for' => 'field-' . $this->cart_page->get_option_name(),
			]
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_EMBEDDED_CHECKOUT
		);

		add_settings_field(
			self::OPTION_EMBEDDED_CHECKOUT,
			esc_html( __( 'Enable Embedded Checkout', 'bigcommerce' ) ),
			[ $this, 'render_embedded_checkout_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'label_for' => 'field-' . self::OPTION_EMBEDDED_CHECKOUT,
			]
		);

		register_setting(
			Settings_Screen::NAME,
			$this->checkout_page->get_option_name()
		);
		add_settings_field(
			$this->checkout_page->get_option_name(),
			$this->checkout_page->get_post_state_label(),
			[ $this, 'render_page_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'page'      => $this->checkout_page,
				'label_for' => 'field-' . $this->checkout_page->get_option_name(),
			]
		);
	}

	public function render_enable_cart_field() {
		$value    = (bool) get_option( self::OPTION_ENABLE_CART, true );
		$checkbox = sprintf( '<input id="field-%s" type="checkbox" value="1" class="regular-text code" name="%s" %s />', esc_attr( self::OPTION_ENABLE_CART ), esc_attr( self::OPTION_ENABLE_CART ), checked( true, $value, false ) );
		printf( '<p class="description">%s %s</p>', $checkbox, esc_html( __( 'If enabled, customers will be able to add products to a cart before proceeding to checkout. If disabled, products will use a Buy Now button that takes them directly to checkout.', 'bigcommerce' ) ) );
	}

	public function render_ajax_cart_field() {
		$value    = (bool) get_option( self::OPTION_AJAX_CART, true );
		printf( '<p><label><input type="radio" value="0" name="%s" %s /> %s</label></p>', esc_attr( self::OPTION_AJAX_CART ), checked( false, $value, false ), esc_html( __( 'When a product is added to the cart, redirect the customer to the shopping cart page immediately', 'bigcommerce' ) ) );
		printf( '<p><label><input type="radio" value="1" name="%s" %s /> %s</label></p>', esc_attr( self::OPTION_AJAX_CART ), checked( true, $value, false ), esc_html( __( 'When a product is added to the cart, keep the customer on the present page and display a notification via Ajax', 'bigcommerce' ) ) );
	}

	public function render_embedded_checkout_field() {
		$value     = (bool) get_option( self::OPTION_EMBEDDED_CHECKOUT, true );
		$permitted = (bool) apply_filters( 'bigcommerce/checkout/can_embed', true );
		$checkbox  = sprintf( '<input id="field-%s" type="checkbox" value="1" class="regular-text code" name="%s" %s %s />', esc_attr( self::OPTION_EMBEDDED_CHECKOUT ), esc_attr( self::OPTION_EMBEDDED_CHECKOUT ), checked( true, $value, false ), disabled( $permitted, false, false ) );
		if ( $permitted ) {
			$description = __( 'If enabled, the checkout form will be embedded on your checkout page. If disabled, customers will be redirected to bigcommerce.com for checkout. Your WordPress domain must have a valid SSL certificate and %ssitewide HTTPS must be enabled%s in BigCommerce store to support embedded checkout.', 'bigcommerce' );
		} else {
			$description = __( 'Embedded checkout is disabled. An SSL certificate is required for your WordPress domain as well as %senabling sitewide HTTPS%s in BigCommerce store to support embedded checkout.', 'bigcommerce' );
		}
		printf( '<p class="description">%s %s</p>', $checkbox, sprintf(
			$description,
			sprintf( '<a target="__blank" href="%s">', esc_url( 'https://login.bigcommerce.com/deep-links/manage/settings/store' ) ),
			'</a>'
		) );
	}

	public function render_cart_page_field() {
		$value      = (int) get_option( self::OPTION_CART_PAGE_ID, 0 );
		$candidates = $this->cart_page->get_post_candidates();
		$options    = array_map( function ( $post_id ) use ( $value ) {
			return sprintf( '<option value="%d" %s>%s</option>', $post_id, selected( $post_id, $value, false ), esc_html( get_the_title( $post_id ) ) );
		}, $candidates );
		if ( empty( $options ) ) {
			$options     = [
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
