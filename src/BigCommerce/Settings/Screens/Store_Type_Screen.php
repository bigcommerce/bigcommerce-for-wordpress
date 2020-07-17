<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;
use BigCommerce\Settings\Sections\Cart;

/**
 * Class Store_Type_Screen
 *
 * @package BigCommerce\Settings\Screens
 */
class Store_Type_Screen extends Onboarding_Screen {
	const NAME              = 'bigcommerce_store_type';
	const COMPLETE_FLAG     = 'bigcommerce_store_type_option_complete';
	const ACTION_BLOG       = 'bigcommerce_set_store_type_blog';
	const ACTION_FULL_STORE = 'bigcommerce_set_store_type_full';

	/**
	 * @return string
	 */
	protected function get_page_title() {
		return __( 'How do you want to use BigCommerce for WordPress?', 'bigcommerce' );
	}

	/**
	 * @inheritdoc
	 * @return string
	 */
	protected function get_menu_title() {
		return __( 'Welcome', 'bigcommerce' );
	}

	/**
	 * @inheritdoc
	 * @return string
	 */
	protected function get_header() {
		return $this->before_title() . sprintf(
				'<header class="bc-connect__header"><h1 class="bc-settings-connect__title">%s</h1>%s</header>',
				__( 'How do you want to use BigCommerce for WordPress?', 'bigcommerce' ),
				$this->get_description()
			);
	}

	/**
	 * @return string
	 */
	private function get_description() {
		return sprintf(
			'<ul class="bc-settings-connect__channels-instructions"><li>%s</li><li>%s</li></ul>',
			__( 'Do you want to set up a full-featured store in WordPress or are you looking for a simple blogging approach, to embed products in blog posts and pages?', 'bigcommerce' ),
			__( 'Whichever you select, you can always scale up or scale down by adjusting your settings later.', 'bigcommerce' )
		);
	}

	/**
	 *  Build the buttons
	 */
	protected function submit_button() {
		$this->add_buttons_to_settings_screen();
	}

	/**
	 *  Add buttons to the settings screen
	 */
	public function add_buttons_to_settings_screen() {
		$url_blog = esc_url( $this->get_admin_url( self::ACTION_BLOG ) );
		$url_full = esc_url( $this->get_admin_url( self::ACTION_FULL_STORE ) );
		printf(
			'<div class="bc-welcome-choose-blog-full">
				<a href="%s" class="bc-admin-btn">%s</a>
				<a href="%s" class="bc-admin-btn">%s</a>
			</div>',
			$url_full,
			esc_html( __( 'Full Featured Store', 'bigcommerce' ) ),
			$url_blog,
			esc_html( __( 'Simple Blogging', 'bigcommerce' ) )
		);
	}

	/**
	 * @param $action
	 *
	 * @return string
	 */
	private function get_admin_url( $action ) {
		$url = admin_url( 'admin-post.php' );
		$url = add_query_arg( [
			'action' => $action,
		], $url );
		$url = wp_nonce_url( $url, $action );

		return $url;
	}

	/**
	 *  Handle form submission for when the blog option is chosen
	 */
	public function handle_submission_for_blog() {
		if ( ! check_admin_referer( self::ACTION_BLOG ) ) {
			return;
		}

		// Update the options
		update_option( self::COMPLETE_FLAG, 1 );
		update_option( Nav_Menu_Screen::COMPLETE_FLAG, 1 ); // skip nav menu setup
		update_option( Cart::OPTION_ENABLE_CART, 0 );
		update_option( Cart::OPTION_AJAX_CART, 0 );
		update_option( Cart::OPTION_EMBEDDED_CHECKOUT, 0 );

		// redirect to next step
		wp_safe_redirect( esc_url_raw( add_query_arg( [ 'settings-updated' => 1 ], $this->get_url() ) ), 303 );
		exit();
	}

	/**
	 *  Handle form submission for when the full store option is chosen
	 */
	public function handle_submission_for_full_store() {
		if ( ! check_admin_referer( self::ACTION_FULL_STORE ) ) {
			return;
		}

		update_option( self::COMPLETE_FLAG, 1 );

		// redirect to next step
		wp_safe_redirect( esc_url_raw( add_query_arg( [ 'settings-updated' => 1 ], $this->get_url() ) ), 303 );
		exit();
	}

	public function should_register() {
		if ( $this->configuration_status < Settings::STATUS_CHANNEL_CONNECTED ) {
			return false;
		}

		if ( $this->configuration_status < Settings::STATUS_STORE_TYPE_SELECTED ) {
			return true;
		}

		return false;
	}
}
