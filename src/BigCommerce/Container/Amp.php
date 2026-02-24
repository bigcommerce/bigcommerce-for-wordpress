<?php

namespace BigCommerce\Container;

use BigCommerce\Amp\Amp_Admin_Notices;
use BigCommerce\Amp\Amp_Controller_Factory;
use BigCommerce\Amp\Amp_Template_Override;
use BigCommerce\Amp\Amp_Cart;
use BigCommerce\Amp\Classic;
use Pimple\Container;
use BigCommerce\Amp\Amp_Assets;
use BigCommerce\Amp\Overrides;
use BigCommerce\Customizer\Styles;
use BigCommerce\Amp\Amp_Cart_Menu_Item;

/**
 * This class integrates AMP (Accelerated Mobile Pages) functionality into the BigCommerce platform.
 * It registers necessary services and handles template overrides, assets, and customization for AMP compatibility.
 * The class also provides hooks and filters for customizing the AMP-specific behavior of templates, assets, and menus.
 * 
 * The AMP class manages the AMP checkout redirect, template override initialization, and integration with 
 * BigCommerce's storefront rendering system to ensure AMP-compatible pages.
 * 
 * @package BigCommerce
 * @subpackage AMP
 */
class Amp extends Provider {
	/**
	 * The template override identifier for AMP templates.
	 * This constant is used to access the service responsible for overriding AMP templates within the application.
	 * @var string
	 */
	const TEMPLATE_OVERRIDE  = 'amp.template_override';

	/**
	 * The template directory identifier for AMP templates.
	 * This constant is used to access the service that specifies the AMP template directory.
	 * @var string
	 */
	const TEMPLATE_DIRECTORY = 'amp.template_directory';

	/**
	 * The factory override identifier for AMP controller factories.
	 * This constant is used to access the service that provides the custom controller factory for AMP controllers.
	 * @var string
	 */
	const FACTORY_OVERRIDE   = 'amp.controller_factory_override';

	/**
	 * The assets identifier for AMP assets.
	 * This constant is used to access the service responsible for managing AMP-specific assets.
	 * @var string
	 */
	const ASSETS             = 'amp.assets';

	/**
	 * The customizer styles identifier for AMP.
	 * This constant is used to access the service that handles custom styles for AMP customization.
	 * @var string
	 */
	const CUSTOMIZER_STYLES  = 'amp.customize_styles';

	/**
	 * The overrides identifier for AMP-related overrides.
	 * This constant is used to access the service responsible for applying AMP-specific overrides to content and templates.
	 * @var string
	 */
	const OVERRIDES          = 'amp.overrides';

	/**
	 * The classic AMP mode identifier.
	 * This constant is used to access the service that enables or handles the classic AMP mode for templates.
	 * @var string
	 */
	const CLASSIC            = 'amp.classic';

	/**
	 * The AMP cart identifier.
	 * This constant is used to access the service that handles AMP cart functionality, including interactions with the cart.
	 * @var string
	 */
	const AMP_CART           = 'amp.amp_cart';

	/**
	 * The menu item identifier for AMP cart menu item.
	 * This constant is used to access the service that adds AMP-specific functionality to the cart menu item.
	 * @var string
	 */
	const MENU_ITEM          = 'amp.cart_menu_item';

	/**
	 * The AMP admin notices identifier.
	 * This constant is used to access the service that handles AMP-specific admin notices within the application.
	 * @var string
	 */
	const AMP_ADMIN_NOTICES  = 'amp.notices';

	/**
	 * Registers AMP classes and callbacks.
	 *
	 * @param Container $container Plugin container.
	 */
	public function register( Container $container ) {

		$this->admin_notices( $container );

		$container[ self::TEMPLATE_DIRECTORY ] = function ( Container $container ) {
			/**
			 * Filter the name of the AMP template directory
			 *
			 * @param string $directory The base name of the template directory
			 */
			return apply_filters( 'bigcommerce/amp/templates/directory', 'amp' );
		};

		$container[ self::TEMPLATE_OVERRIDE ] = function ( Container $container ) {
			return new Amp_Template_Override( $container[ self::TEMPLATE_DIRECTORY ] );
		};

		$container[ self::FACTORY_OVERRIDE ] = function ( Container $container ) {
			return new Amp_Controller_Factory();
		};

		$container[ self::ASSETS ] = function ( Container $container ) {
			$customizer_template_file = dirname( $container['plugin_file'] ) . '/assets/customizer.template.css';
			return new Amp_Assets(
				trailingslashit( plugin_dir_path( $container['plugin_file'] ) ) . 'assets/',
				trailingslashit( plugin_dir_url( $container['plugin_file'] ) ) . 'assets/',
				$customizer_template_file
			);
		};

		$container[ self::CUSTOMIZER_STYLES ] = function ( Container $container ) {
			$path = dirname( $container['plugin_file'] ) . '/assets/customizer.template.css';

			return new Styles( $path );
		};

		$container[ self::OVERRIDES ] = function ( Container $container ) {
			return new Overrides();
		};

		$container[ self::CLASSIC ] = function ( Container $container ) {
			return new Classic();
		};

		$container[ self::AMP_CART ] = function( Container $container ) {
			return new Amp_Cart( $container[ Proxy::PROXY_BASE ] );
		};

		add_action(
			'bigcommerce/action_endpoint/' . Amp_Cart::CHECKOUT_REDIRECT_ACTION,
			$this->create_callback(
				'amp_checkout_handle_request',
				function ( $args ) use ( $container ) {
					$container[ self::AMP_CART ]->handle_redirect_request();
				}
			)
		);

		$container[ self::MENU_ITEM ] = function ( Container $container ) {
			return new Amp_Cart_Menu_Item();
		};

		add_action( 'wp', $this->create_callback( 'init_template_override', function ( $wp ) use ( $container ) {

			/**
			 * Toggles whether AMP template overrides will be used to render plugin templates
			 *
			 * @param bool $enable Whether AMP template overrides are enabled
			 */
			if ( apply_filters( 'bigcommerce/amp/templates/enable_override', function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ) {
				$template_override = $this->create_callback( 'template_override', function ( $path, $relative_path ) use ( $container ) {
					return $container[ self::TEMPLATE_OVERRIDE ]->override_template_path( $path, $relative_path );
				} );

				$controller_factory_override = $this->create_callback( 'controller_factory_override', function ( $factory, $classname ) use ( $container ) {
					return $container[ self::FACTORY_OVERRIDE ];
				} );

				$template_img_src_override = $this->create_callback( 'amp_template_data', function ( $data, $template, $options ) use ( $container ) {
					return $container[ self::OVERRIDES ]->add_amp_img_src( $data, $template );
				} );

				$template_button_override = $this->create_callback( 'amp_purchase_button', function ( $button, $post_id ) use ( $container ) {
					return $container[ self::OVERRIDES ]->amp_enable_button( $button, $post_id );
				} );

				// Remove extra attributes such as data-js from AMP to avoid validation errors.
				$amp_extra_attributes_removal = $this->create_callback( 'amp_remove_extra_attributes', function ( $attributes, $template ) use ( $container ) {
					$keys = array_filter( array_keys( $attributes ), function ( $string ) {
						return strpos( $string, 'data-' ) === 0;
					} );
					foreach ( $keys as $key ) {
						unset( $attributes[ $key ] );
					}

					return $attributes;
				} );

				$amp_filter_fallback_image = $this->create_callback( 'amp_filter_fallback_image', function () use ( $container ) {
					return $container[ self::OVERRIDES ]->filter_fallback_image();
				} );

				$amp_filter_stylesheet = $this->create_callback( 'amp_filter_stylesheet', function ( $stylesheet ) use ( $container ) {
					return $container[ self::ASSETS ]->filter_stylesheet( $stylesheet );
				} );

				$amp_kses_allowed_html = $this->create_callback( 'amp_kses_allowed_html', function ( $allowed_tags, $context ) use ( $container ) {
					return $container[ self::OVERRIDES ]->amp_kses_allowed_html( $allowed_tags, $context );
				} );

				add_filter( 'bigcommerce/template/path', $template_override, 5, 2 );
				add_filter( 'bigcommerce/template/controller_factory', $controller_factory_override, 10, 2 );
				add_filter( 'bigcommerce/template/data', $template_img_src_override, 10, 3 );
				add_filter( 'bigcommerce/button/purchase', $template_button_override, 10, 2 );
				add_filter( 'bigcommerce/template/wrapper/attributes', $amp_extra_attributes_removal, 10, 2 );
				add_filter( 'bigcommerce/template/image/fallback', $amp_filter_fallback_image, 10, 0 );
				add_filter( 'wp_kses_allowed_html', $amp_kses_allowed_html, 10, 2 );

				// Only applies to classic AMP mode.
				if ( $container[ self::TEMPLATE_OVERRIDE ]->is_classic() ) {
					$classic_template_override = $this->create_callback( 'classic_template_override', function ( $file, $template_type, $post ) use ( $container ) {
						return $container[ self::TEMPLATE_OVERRIDE ]->override_classic_amp_template_path( $file, $template_type, $post );
					} );

					$header_template_override = $this->create_callback( 'amp_filter_header_bar_template', function ( $file, $type ) use ( $container ) {
						return $container[ self::TEMPLATE_OVERRIDE ]->override_classic_header_bar_template( $file, $type, $container );
					} );

					$rendered_menu_filter = $this->create_callback( 'amp_provide_rendered_menu', function( $data ) use ( $container ) {
						return $container [ self::TEMPLATE_OVERRIDE ]->provide_header_nav_menu( $data );
					} );

					add_filter( 'amp_post_template_file', $classic_template_override, 10, 3 );
					add_filter( 'amp_post_template_file', $header_template_override, 10, 2 );
					add_filter( 'amp_post_template_data', $rendered_menu_filter );
				} else {
					add_filter( 'bigcommerce/assets/stylesheet', $amp_filter_stylesheet, 10, 1 );
				}

				add_filter( 'wp_setup_nav_menu_item', $this->create_callback( 'menu_item', function ( $menu_item ) use ( $container ) {
					return $container[ self::MENU_ITEM ]->add_classes_to_cart_page( $menu_item, $container[ Proxy::PROXY_BASE ] );
				} ), 11, 1 );
			}
		} ), 10, 1 );

		add_action( 'bigcommerce/form/before_redirect', $this->create_callback( 'amp_redirect_headers', function ( $url ) use ( $container ) {
			return $container[ self::OVERRIDES ]->add_amp_redirect_headers( $url );
		} ), 10, 1 );

		add_action( 'amp_post_template_css', $this->create_callback( 'amp_post_template_css', function () use ( $container ) {
			$container[ self::ASSETS ]->styles();
			$container[ self::CUSTOMIZER_STYLES ]->print_css();
		} ), 10, 0 );

		add_filter( 'amp_post_template_data', $this->create_callback( 'amp_post_template_data', function ( $data ) use ( $container ) {
			$data['amp_component_scripts'] = array_merge(
				$data['amp_component_scripts'],
				array_fill_keys( $container[ self::ASSETS ]->scripts(), true )
			);
			return $data;
		} ), 11, 1 );

		add_action( 'amp_post_template_head', $this->create_callback( 'amp_post_template_head', function () use ( $container ) {
			$container[ self::ASSETS ]->scripts();
		} ), 11, 0 );

		add_action( 'after_setup_theme', $this->create_callback( 'amp_register_menu', function () use ( $container ) {
			if ( $container[ self::TEMPLATE_OVERRIDE ]->is_classic() ) {
				$container[ self::CLASSIC ]->register_amp_menu();
			}
		} ) );
	}

	/**
	 * Sets up AMP admin notices class and callbacks.
	 *
	 * @param Container $container Plugin container instance.
	 */
	private function admin_notices( Container $container ) {
		$container[ self::AMP_ADMIN_NOTICES ] = function( Container $container ) {
			return new Amp_Admin_Notices(
				$container[ Settings::SETTINGS_SCREEN ]->get_hook_suffix(),
				defined( 'AMP__VERSION' ) && class_exists( 'AMP_Options_Manager' )
			);
		};

		add_action(
			'admin_notices',
			function() use ( $container ) {
				$container[ self::AMP_ADMIN_NOTICES ]->render_amp_admin_notices();
			}
		);
	}
}