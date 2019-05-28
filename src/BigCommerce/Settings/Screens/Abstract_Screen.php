<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;
use BigCommerce\Post_Types\Product\Product;

abstract class Abstract_Screen {
	const NAME = '';

	protected $hook_suffix = '';

	protected $capability = 'manage_options';

	protected $configuration_status = Settings::STATUS_NEW;

	/** @var string URL to the plugin's assets directory */
	protected $assets_url = '';

	/**
	 * Abstract_Screen constructor.
	 *
	 * @param int    $configuration_status A flag indicating if the current stage in the setup process
	 * @param string $assets_url           Path to the plugin assets directory
	 */
	public function __construct( $configuration_status, $assets_url ) {
		if ( empty( static::NAME ) ) {
			throw new \LogicException( 'Classes extending Abstract_Screen must have the NAME constant set to a non-empty value' );
		}
		$this->configuration_status = $configuration_status;
		$this->assets_url           = $assets_url;
	}

	/**
	 * @return string The title to render for the page
	 */
	abstract protected function get_page_title();

	/**
	 * @return string The title to show in the admin menu for the page
	 */
	abstract protected function get_menu_title();

	protected function get_header() {
		$title = $this->get_page_title();
		if ( empty( $title ) ) {
			return '';
		}

		return $this->before_title() . sprintf( '<h1>%s</h1>', $this->get_page_title() );
	}

	protected function before_title() {
		$before = '<div class="wp-header-end"></div>'; // placeholder to tell WP where to put notices
		ob_start();
		do_action( 'bigcommerce/settings/before_title/page=' . static::NAME );
		return $before . ob_get_clean();
	}

	public function get_hook_suffix() {
		return $this->hook_suffix;
	}

	public function get_url() {
		return add_query_arg( [ 'page' => static::NAME, 'post_type' => Product::NAME ], admin_url( 'edit.php' ) );
	}

	/**
	 * @return void
	 * @action admin_menu
	 */
	public function register_settings_page() {
		if ( ! $this->should_register() ) {
			$this->setup_unregistered_redirect();

			return;
		}
		$this->hook_suffix = add_submenu_page(
			$this->parent_slug(),
			$this->get_page_title(),
			$this->get_menu_title(),
			$this->get_capability(),
			static::NAME,
			[ $this, 'render_settings_page' ]
		);

		add_action( 'admin_body_class', [ $this, 'set_admin_body_class' ], 10, 1 );

		/**
		 * Triggered after registering a settings screen. The dynamic
		 * portion of the hook is the name of the screen.
		 *
		 * @param string $hook_suffix The hook suffix generated for the screen
		 * @param string $name        The name of the screen
		 */
		do_action( 'bigcommerce/settings/register/screen=' . static::NAME, $this->hook_suffix, static::NAME );
	}

	public function get_capability() {
		return $this->capability;
	}

	protected function parent_slug() {
		return sprintf( 'edit.php?post_type=%s', Product::NAME );
	}

	/**
	 * @return void
	 */
	public function render_settings_page() {
		ob_start();
		settings_errors();
		$this->before_form();
		$this->start_form();
		$this->settings_fields();
		$this->do_settings_sections( static::NAME );
		$this->submit_button();
		$this->end_form();
		$this->after_form();
		$content = ob_get_clean();

		printf( '<div class="wrap bc-settings bc-settings-%s">%s<div class="bc-settings-content-wrap">%s%s</div></div>', static::NAME, $this->progress_bar(), $this->get_header(), $content );
	}

	/**
	 * Renders the onboarding progress bar for the current screen
	 *
	 * @return string
	 */
	protected function progress_bar() {
		return '';
	}

	protected function start_form() {
		printf( '<form action="%1$s" method="post" class="bc-settings-form bc-settings-form--%2$s" data-js="%2$s">', esc_url( $this->form_action_url() ), static::NAME );
		/**
		 * Triggered after the opening <form> tag on the settings screen form finishes rendering.
		 * The dynamic portion of the hook is the identifier of the settings screen.
		 *
		 * @param string $hook_suffix The hook suffix generated for the screen
		 */
		do_action( 'bigcommerce/settings/after_start_form/page=' . static::NAME, $this->hook_suffix );
	}

	protected function end_form() {
		/**
		 * Triggered before the closing </form> tag on the settings screen form finishes rendering.
		 * The dynamic portion of the hook is the identifier of the settings screen.
		 *
		 * @param string $hook_suffix The hook suffix generated for the screen
		 */
		do_action( 'bigcommerce/settings/before_end_form/page=' . static::NAME, $this->hook_suffix );
		echo '</form>';
	}

	/**
	 * Get the URL to which the form will submit
	 *
	 * @return string
	 */
	protected function form_action_url() {
		return admin_url( 'options.php' );
	}

	/**
	 * Render the hidden settings fields for the form
	 *
	 * @return void
	 */
	protected function settings_fields() {
		settings_fields( static::NAME );
	}

	protected function submit_button() {
		echo '<div class="bc-plugin-page-header">';
		submit_button();
		echo '</div>';
	}

	protected function before_form() {
		/**
		 * Triggered before the settings screen form starts to render.
		 * The dynamic portion of the hook is the identifier of the settings screen.
		 *
		 * @param string $hook_suffix The hook suffix generated for the screen
		 */
		do_action( 'bigcommerce/settings/before_form/page=' . static::NAME, $this->hook_suffix );
	}

	protected function after_form() {
		/**
		 * Triggered after the settings screen form finishes rendering.
		 * The dynamic portion of the hook is the identifier of the settings screen.
		 *
		 * @param string $hook_suffix The hook suffix generated for the screen
		 */
		do_action( 'bigcommerce/settings/after_form/page=' . static::NAME, $this->hook_suffix );
	}

	/**
	 * Replacement for do_settings_sections() that provides greater
	 * control over formatting
	 *
	 * @param string $page
	 *
	 * @return void
	 */
	protected function do_settings_sections( $page ) {
		global $wp_settings_sections;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}


		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			/**
			 * Fires before rendering a settings section.
			 * The dynamic portion of the hook name is the section ID.
			 *
			 * @param array $section
			 */
			do_action( 'bigcommerce/settings/section/before/id=' . $section[ 'id' ], $section );

			printf( '<div id="%s" class="bc-settings-section bc-settings-section--%s" data-js="section-toggle">', sanitize_html_class( $section[ 'id' ] ), sanitize_html_class( $section[ 'id' ] ) );

			$this->section_header( $section, $page );

			$this->section_body( $section, $page );

			echo '</div>'; // bc-settings-section

			/**
			 * Fires after rendering a settings section.
			 * The dynamic portion of the hook name is the section ID.
			 *
			 * @param array $section
			 */
			do_action( 'bigcommerce/settings/section/after/id=' . $section[ 'id' ], $section );
		}
	}

	/**
	 * Render the header for the settings section
	 *
	 * @param array  $section
	 * @param string $page
	 *
	 * @return void
	 */
	protected function section_header( $section, $page ) {
		printf( '<button class="%s" data-js="%s" tabindex="0" aria-controls="%s" aria-expanded="false">', 'bc-settings-section__header', 'section-toggle-trigger', sanitize_html_class( $section[ 'id' ] ) . '-body' );

		/**
		 * Fires before rendering the title of a settings section.
		 * The dynamic portion of the hook name is the section ID.
		 *
		 * @param array $section
		 */
		do_action( 'bigcommerce/settings/section/before_title/id=' . $section[ 'id' ], $section );

		if ( $section[ 'title' ] ) {
			printf( "<i class='bc-icon bc-icon--settings icon-bc-%s'></i><h2 class='bc-settings-section__title'>%s</h2>\n", sanitize_html_class( $section[ 'id' ] ), $section[ 'title' ] );
		}

		/**
		 * Fires after rendering the title of a settings section.
		 * The dynamic portion of the hook name is the section ID.
		 *
		 * @param array $section
		 */
		do_action( 'bigcommerce/settings/section/after_title/id=' . $section[ 'id' ], $section );

		echo '<i class="bc-icon icon-bc-arrow-solid"></i></button>'; // bc-settings-section__header
	}

	/**
	 * Render the body for the settings section
	 *
	 * @param array  $section
	 * @param string $page
	 *
	 * @return void
	 */
	protected function section_body( $section, $page ) {
		global $wp_settings_fields;

		printf( "<div id='%s' class='%s' data-js='%s' hidden><div class='%s'>", sanitize_html_class( $section[ 'id' ] ) . '-body', 'bc-settings-section__target', 'section-toggle-target', 'bc-settings-section__body' );

		/**
		 * Fires before calling the callback of a settings section.
		 * The dynamic portion of the hook name is the section ID.
		 *
		 * @param array $section
		 */
		do_action( 'bigcommerce/settings/section/before_callback/id=' . $section[ 'id' ], $section );

		if ( $section[ 'callback' ] ) {
			call_user_func( $section[ 'callback' ], $section );
		}

		/**
		 * Fires after calling the callback of a settings section.
		 * The dynamic portion of the hook name is the section ID.
		 *
		 * @param array $section
		 */
		do_action( 'bigcommerce/settings/section/after_callback/id=' . $section[ 'id' ], $section );

		$has_fields = isset( $wp_settings_fields ) && isset( $wp_settings_fields[ $page ] ) && isset( $wp_settings_fields[ $page ][ $section[ 'id' ] ] );

		/**
		 * Fires before rendering the fields of a settings section.
		 * The dynamic portion of the hook name is the section ID.
		 *
		 * @param array $section
		 * @param bool  $has_fields Whether the settings section has any fields to render
		 */
		do_action( 'bigcommerce/settings/section/before_fields/id=' . $section[ 'id' ], $section, $has_fields );

		if ( $has_fields ) {
			echo '<table class="form-table">';
			do_settings_fields( $page, $section[ 'id' ] );
			echo '</table>';
		}

		/**
		 * Fires after rendering the fields of a settings section.
		 * The dynamic portion of the hook name is the section ID.
		 *
		 * @param array $section
		 * @param bool  $has_fields Whether the settings section has any fields to render
		 */
		do_action( 'bigcommerce/settings/section/after_fields/id=' . $section[ 'id' ], $section, $has_fields );

		echo '</div></div>'; // bc-settings-section__body, bc-settings-section__target
	}

	protected function setup_unregistered_redirect() {
		if ( $GLOBALS[ 'plugin_page' ] !== static::NAME ) {
			return; // nothing to worry about
		}
		add_action( 'admin_menu', function () {
			do_action( 'bigcommerce/settings/unregistered_screen', static::NAME );
		}, 10000, 0 );
	}

	public function redirect_to_screen() {
		$url = $this->get_url();
		if ( ! empty( $_GET[ 'settings-updated' ] ) ) {
			$url = add_query_arg( [ 'settings-updated' => 1 ], $url );
		}
		wp_safe_redirect( esc_url_raw( $url ), 303 );
		exit();
	}

	/**
	 * Indicates if this screen should be registered, given the
	 * current state of the WordPress installation.
	 *
	 * @return bool
	 */
	public function should_register() {
		return true;
	}

	public function set_admin_body_class( $classes = '' ) {
		$screen = get_current_screen();
		if ( $screen->id === $this->get_hook_suffix() ) {
			$classes .= ' ' . $this->get_admin_body_class();
		}

		return $classes;
	}

	protected function get_admin_body_class() {
		return 'bigcommerce-settings-page';
	}
}
