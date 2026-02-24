<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;
use BigCommerce\Post_Types\Product\Product;

/**
 * Abstract base class for managing plugin settings screens.
 * 
 * This abstract class provides a foundation for settings screens in the WordPress admin.
 * It defines common properties such as the hook suffix, capability, and asset URL, 
 * along with the constant `NAME` which should be defined in concrete subclasses.
 * Concrete subclasses should implement their specific logic for rendering the screen 
 * and handling settings.
 * 
 * @package YourPlugin
 */
abstract class Abstract_Screen {
    
    /**
     * The name identifier for the screen.
     * 
     * This constant should be defined in concrete subclasses to uniquely identify the screen.
     * It may be used for routing, redirection, or referencing the screen in other parts of the plugin.
     * 
     * @var string
     */
    const NAME = '';
    
    /**
     * The hook suffix for the settings page.
     * 
     * This property stores the hook suffix that is used to identify the settings page.
     * It is typically populated during screen setup to enable specific functionality or rendering.
     * 
     * @var string
     */
    protected $hook_suffix = '';

    /**
     * The required capability to access the settings page.
     * 
     * This property defines the required capability to access the settings page.
     * By default, it's set to `manage_options`, which allows administrators to access the page.
     * 
     * @var string
     */
    protected $capability = 'manage_options';

    /**
     * The configuration status of the settings screen.
     * 
     * This property holds the status of the settings screen configuration.
     * By default, it is set to `STATUS_NEW` from the `Settings` class.
     * 
     * @var string
     */
    protected $configuration_status = Settings::STATUS_NEW;

    /**
     * URL to the plugin's assets directory.
     * 
     * This property stores the URL to the plugin's assets directory, such as images, stylesheets,
     * and scripts. It is used to reference assets from the plugin's folder within the admin interface.
     * 
     * @var string
     */
    protected $assets_url = '';

	/**
	 * Abstract_Screen constructor.
	 *
	 * @param int    $configuration_status A flag indicating if the current stage in the setup process.
	 * @param string $assets_url           Path to the plugin assets directory.
	 */
	public function __construct( $configuration_status, $assets_url ) {
		if ( empty( static::NAME ) ) {
			throw new \LogicException( 'Classes extending Abstract_Screen must have the NAME constant set to a non-empty value' );
		}
		$this->configuration_status = $configuration_status;
		$this->assets_url           = $assets_url;
	}

	/**
	 * Gets the title to render for the page.
	 *
	 * @return string The title to render for the page.
	 */
	abstract protected function get_page_title();

	/**
	 * Gets the title to show in the admin menu for the page.
	 *
	 * @return string The title to show in the admin menu for the page.
	 */
	abstract protected function get_menu_title();

	/**
	 * Retrieves the header HTML for the settings page.
	 *
	 * This method generates the header for the settings page, including the page title wrapped
	 * in an `<h1>` tag. If the title is empty, it returns an empty string.
	 * 
	 * The title is obtained by calling the `get_page_title()` method, and additional content
	 * may be prepended to the title using the `before_title()` method.
	 *
	 * @return string The HTML markup for the header, or an empty string if no title is provided.
	 */
	protected function get_header() {
		$title = $this->get_page_title();
		if ( empty( $title ) ) {
			return '';
		}

		return $this->before_title() . sprintf( '<h1>%s</h1>', $this->get_page_title() );
	}

	/**
	 * Generates the markup before the page title.
	 *
	 * This method outputs a placeholder to indicate where notices should be placed
	 * within the WordPress admin header. It also fires the `bigcommerce/settings/before_title/page={NAME}`
	 * action hook, allowing other components to hook into this point and potentially add additional content.
	 *
	 * @return string The HTML markup for the section before the title.
	 */
	protected function before_title() {
		$before = '<div class="wp-header-end"></div>'; // placeholder to tell WP where to put notices
		ob_start();
		do_action( 'bigcommerce/settings/before_title/page=' . static::NAME );
		return $before . ob_get_clean();
	}

	/**
	 * Gets the hook suffix for the settings page.
	 *
	 * @return string The hook suffix for the settings page.
	 */
	public function get_hook_suffix() {
		return $this->hook_suffix;
	}

	/**
	 * Gets the URL for the settings page.
	 *
	 * @return string The URL for the settings page.
	 */
	public function get_url() {
		return add_query_arg( [ 'page' => static::NAME, 'post_type' => Product::NAME ], admin_url( 'edit.php' ) );
	}

	/**
	 * Registers the settings page in the WordPress admin menu.
	 *
	 * @return void
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

	/**
	 * Gets the capability required to view the settings page.
	 *
	 * @return string The capability required to view the settings page.
	 */
	public function get_capability() {
		return $this->capability;
	}

	/**
	 * Retrieves the parent slug for the settings page.
	 *
	 * This method generates the parent URL slug for the settings page by formatting the URL
	 * with the post type constant from the `Product` class.
	 *
	 * @return string The formatted parent slug for the settings page.
	 */
	protected function parent_slug() {
		return sprintf( 'edit.php?post_type=%s', Product::NAME );
	}

	/**
	 * Renders the settings page.
	 *
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
	 * Renders the onboarding progress bar for the current screen.
	 *
	 * @return string The HTML markup for the progress bar.
	 */
	protected function progress_bar() {
		return '';
	}

	/**
	 * Starts the form for the settings page.
	 *
	 * @return void
	 */
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

	/**
	 * Ends the form for the settings page.
	 *
	 * @return void
	 */
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
	 * Gets the URL to which the form will submit.
	 *
	 * @return string The form action URL.
	 */
	protected function form_action_url() {
		return admin_url( 'options.php' );
	}

	/**
	 * Renders the hidden settings fields for the form.
	 *
	 * @return void
	 */
	protected function settings_fields() {
		settings_fields( static::NAME );
	}

	/**
	 * Renders the submit button for the settings form.
	 *
	 * @return void
	 */
	protected function submit_button() {
		echo '<div class="bc-plugin-page-header">';
		submit_button();
		echo '</div>';
	}

	/**
	 * Renders content before the settings form starts.
	 *
	 * @return void
	 */
	protected function before_form() {
		/**
		 * Triggered before the settings screen form starts to render.
		 * The dynamic portion of the hook is the identifier of the settings screen.
		 *
		 * @param string $hook_suffix The hook suffix generated for the screen
		 */
		do_action( 'bigcommerce/settings/before_form/page=' . static::NAME, $this->hook_suffix );
	}

	/**
	 * Renders content after the settings form finishes.
	 *
	 * @return void
	 */
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
	 * Renders the settings sections for the settings page.
	 *
	 * @param string $page The settings page.
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
	 * Renders the header for the settings section.
	 *
	 * @param array  $section The settings section.
	 * @param string $page The settings page.
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
	 * Renders the body for the settings section.
	 *
	 * @param array  $section The settings section.
	 * @param string $page The settings page.
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

	/**
	 * Sets up a redirect for unregistered screens in the admin menu.
	 * 
	 * This method ensures that if the current screen is not registered, 
	 * a redirection is set up to trigger the `bigcommerce/settings/unregistered_screen` action.
	 * It is only called if the global `plugin_page` matches the screen's NAME constant.
	 * 
	 * @return void
	 */
	protected function setup_unregistered_redirect() {
		if ( $GLOBALS[ 'plugin_page' ] !== static::NAME ) {
			return; // nothing to worry about
		}

		add_action( 'admin_menu', function () {
			/**
			 * Redirects to the appropriate screen when an unregistered screen is encountered.
			 *
			 * @param string $unregistered_screen The name of the unregistered screen.
			 */
			do_action( 'bigcommerce/settings/unregistered_screen', static::NAME );
		}, 10000, 0 );
	}

	/**
	 * Redirects to the current settings screen.
	 *
	 * @return void
	 */
	public function redirect_to_screen() {
		$url = $this->get_url();
		if ( ! empty( $_GET[ 'settings-updated' ] ) ) {
			$url = add_query_arg( [ 'settings-updated' => 1 ], $url );
		}
		wp_safe_redirect( esc_url_raw( $url ), 303 );
		exit();
	}

	/**
	 * Indicates if this screen should be registered, given the current state of the WordPress installation.
	 *
	 * @return bool True if the screen should be registered, false otherwise.
	 */
	public function should_register() {
		return true;
	}

	/**
	 * Sets the body class for the admin page.
	 *
	 * @param string $classes The existing body classes.
	 *
	 * @return string The modified body classes.
	 */
	public function set_admin_body_class( $classes = '' ) {
		$screen = get_current_screen();
		if ( $screen->id === $this->get_hook_suffix() ) {
			$classes .= ' ' . $this->get_admin_body_class();
		}

		return $classes;
	}

	/**
	 * Gets the class for the admin body.
	 *
	 * @return string The admin body class.
	 */
	protected function get_admin_body_class() {
		return 'bigcommerce-settings-page';
	}
}
