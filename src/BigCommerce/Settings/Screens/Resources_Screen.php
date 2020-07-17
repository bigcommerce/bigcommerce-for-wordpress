<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;

class Resources_Screen extends Abstract_Screen {
	const NAME = 'bigcommerce_resources';

	/** @var string Path to the templates/admin directory */
	private $template_dir;

	public function __construct( $configuration_status, $assets_url, $template_dir ) {
		parent::__construct( $configuration_status, $assets_url );
		$this->template_dir = trailingslashit( $template_dir );
	}

	protected function get_page_title() {
		return __( 'Here are some helpful resources to get you started.', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Resources', 'bigcommerce' );
	}

	protected function get_page_header() {
		echo '<div class="bc-plugin-page-header">';
		printf( '<a href="%s" target="_blank" rel="noopener"><img class="bc-settings-save__logo" src="%s" alt="%s" /></a>', esc_url( $this->login_url() ), trailingslashit( $this->assets_url ) . 'img/admin/big-commerce-logo.svg', esc_html( __( 'BigCommerce', 'bigcommerce' ) ) );
		echo '</div>';
	}

	public function render_settings_page() {
		$resources   = $this->retrieve_resources();
		$page_header = $this->get_page_header();
		$page_title  = $this->get_page_title();
		include trailingslashit( $this->template_dir ) . 'resources-screen.php';
	}

	public function should_register() {
		return true; // always available
	}

	/**
	 * Retrieve the resources data from the API,
	 * or a local fallback.
	 *
	 * @return array
	 */
	private function retrieve_resources() {
		/**
		 * Filters the URL for fetching the resource data displayed on the
		 * admin Resources screen.
		 *
		 * Return an empty string to short-circuit the request and render
		 * the default resources.
		 *
		 * @param string $url The URL to the resources JSON data
		 */
		$resources_url = apply_filters( 'bigcommerce/settings/resources/url', 'https://storage.googleapis.com/bigcommerce-wp-connector.appspot.com/resources_v2.json' );

		if ( empty( $resources_url ) ) {
			return $this->default_resources();
		}

		$cache_key = 'bc_resources_' . md5( $resources_url );
		$cached    = get_transient( $cache_key );
		if ( ! empty( $cached ) ) {
			return $cached;
		}

		$response = wp_remote_get( $resources_url );

		$resources = [];

		if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
			$body      = wp_remote_retrieve_body( $response );
			$resources = json_decode( $body, true );
		}

		if ( empty( $resources ) ) {
			$resources = $this->default_resources();
		}

		set_transient( $cache_key, $resources, HOUR_IN_SECONDS );

		return $resources;
	}

	/**
	 * Get the default resources to display when the remote fetch
	 * cannot be used.
	 *
	 * @return array The resource data
	 */
	private function default_resources() {
		$default = [
			'version'  => 1,
			'sections' => [
				[
					'label'     => __( 'Support Links', 'bigcommerce' ),
					'resources' => [
						[
							'name'        => __( 'Help Center', 'bigcommerce' ),
							'description' => __( 'How can we help?', 'bigcommerce' ),
							'url'         => 'https://support.bigcommerce.com/',
							'thumbnail'   => [
								'small' => '',
								'large' => '',
							],
							'isExternal'  => true,
						],
					],
				],
			],
		];

		/**
		 * Filter the default resources to display on the Resources admin page.
		 *
		 * @param array $default The default data array.
		 */
		$default = apply_filters( 'bigcommerce/settings/resources/default', $default );

		return $default;
	}

	private function login_url() {
		return 'https://login.bigcommerce.com/';
	}

}
