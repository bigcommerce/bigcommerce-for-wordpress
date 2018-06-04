<?php


namespace BigCommerce\Settings;


use Bigcommerce\Api\Client;
use BigCommerce\Post_Types\Product\Product;

class Settings_Screen {
	const NAME = 'bigcommerce';

	private $hook_suffix = '';

	public function get_hook_suffix() {
		return $this->hook_suffix;
	}

	public function get_url() {
		return add_query_arg( [ 'page' => self::NAME, 'post_type' => Product::NAME ], admin_url( 'edit.php' ) );
	}

	/**
	 * @return void
	 * @action admin_menu
	 */
	public function register_settings_page() {
		$this->hook_suffix = add_submenu_page(
			sprintf( 'edit.php?post_type=%s', Product::NAME ),
			__( 'BigCommerce Settings', 'bigcommerce' ),
			__( 'Settings', 'bigcommerce' ),
			'manage_options',
			self::NAME,
			[ $this, 'render_settings_page' ]
		);

		do_action( 'bigcommerce/settings/register', $this->hook_suffix );
	}

	/**
	 * @return void
	 */
	public function render_settings_page() {
		$title = __( 'BigCommerce Plugin Settings', 'bigcommerce' );

		ob_start();
		printf( '<form action="%s" method="post">', esc_url( admin_url( 'options.php' ) ) );
		settings_fields( self::NAME );
		do_settings_sections( self::NAME );
		submit_button();
		echo '</form>';
		$content = ob_get_clean();

		printf( '<div class="wrap"><h1>%s</h1>%s</div>', $title, $content );
	}
}