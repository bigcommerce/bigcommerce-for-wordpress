<?php

namespace BigCommerce\Container;

use BigCommerce\Editor\Gutenberg;
use Pimple\Container;
use BigCommerce\Editor\Add_Products_Button;
use BigCommerce\Editor\Editor_Dialog_Template;
use BigCommerce\Customizer\Styles;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;
use BigCommerce\Settings\Sections\Gift_Certificates as Gift_Certificate_Settings;
use BigCommerce\Settings\Sections\Wishlists as Wishlist_Settings;

/**
 * Loads behavior relevant to the admin post editor, including rendering custom buttons,
 * templates, and Gutenberg blocks within the WordPress admin interface. It also handles 
 * the integration of the Gutenberg editor with BigCommerce blocks and assets.
 *
 * @package BigCommerce\Container
 */
class Editor extends Provider {
    /**
     * Constant for the shortcode button identifier.
     *
     * @var string
     */
    const SHORTCODE_BUTTON  = 'admin.shortcode_button';

    /**
     * Constant for the UI dialog identifier.
     *
     * @var string
     */
    const UI_DIALOG         = 'admin.ui_dialog';

    /**
     * Constant for the Gutenberg blocks identifier.
     *
     * @var string
     */
    const GUTENBERG_BLOCKS  = 'gutenberg.blocks';

    /**
     * Constant for the Gutenberg migration identifier.
     *
     * @var string
     */
    const GUTENBERG_MIGRATE = 'gutenberg.migrate';

    /**
     * Constant for the styles identifier.
     *
     * @var string
     */
    const STYLES            = 'gutenberg.styles';

    /**
     * Registers the necessary components for the editor, including buttons, dialog templates,
     * and Gutenberg blocks.
     *
     * This function initializes and sets up the custom behavior for the WordPress admin post 
     * editor, integrating custom buttons and templates for a more enhanced editing experience.
     *
     * @param Container $container The dependency injection container.
     */
	public function register( Container $container ) {
		$this->render_button( $container );
		$this->render_dialog_template( $container );
		$this->gutenberg( $container );
	}

	private function render_button( Container $container ) {
		$container[ self::SHORTCODE_BUTTON ] = function () {
			return new Add_Products_Button();
		};

		add_action( 'media_buttons', $this->create_callback( 'render_products_button', function ( $editor_id ) use ( $container ) {
			echo $container[ self::SHORTCODE_BUTTON ]->render_button();
		} ), 10, 1 );
	}

	private function render_dialog_template( Container $container ) {
		$container[ self::UI_DIALOG ] = function ( Container $container ) {
			$path = dirname( $container[ 'plugin_file' ] ) . '/templates/admin';

			return new Editor_Dialog_Template( $path );
		};

		$render_callback = $this->create_callback( 'render_editor_dialog_template', function () use ( $container ) {
			echo $container[ self::UI_DIALOG ]->render_dialog_once();
		} );

		add_action( 'enqueue_block_editor_assets', $this->create_callback( 'block_editor_enqueue_dialog_template', function() use ( $container, $render_callback ) {
			$current_screen = get_current_screen();
			
			if ( is_a( $current_screen, 'WP_Screen' ) && method_exists( $current_screen, 'is_block_editor' ) && ! $current_screen->is_block_editor() ) {
				return;
			}

			if ( did_action( 'admin_enqueue_scripts' ) ) { // if the Gutenberg plugin is enabled, the action will already be called
				$render_callback();
			} else {
				add_action( 'admin_enqueue_scripts', $render_callback, 10, 0 );
			}
		}), 10, 0 );

		add_action( 'admin_print_footer_scripts', $render_callback, 10, 0 ); // if the block editor is disabled, print scripts in the footer

		add_filter( 'bigcommerce/admin/js_config', $this->create_callback( 'editor_dialog_js_config', function ( $config ) use ( $container ) {
			return $container[ self::UI_DIALOG ]->js_config( $config, $container[ Rest::PRODUCTS ], $container[ Rest::SHORTCODE ] );
		} ), 10, 1 );
	}

	private function gutenberg( Container $container ) {
		$container[ self::GUTENBERG_BLOCKS ] = function ( Container $container ) {
			$blocks = [
				new Gutenberg\Blocks\Products( $container[ Assets::PATH ], $container[ Rest::SHORTCODE ] ),
				new Gutenberg\Blocks\Account_Profile( $container[ Assets::PATH ] ),
				new Gutenberg\Blocks\Address_List( $container[ Assets::PATH ] ),
				new Gutenberg\Blocks\Order_History( $container[ Assets::PATH ] ),
				new Gutenberg\Blocks\Login_Form( $container[ Assets::PATH ] ),
				new Gutenberg\Blocks\Product_Reviews( $container[ Assets::PATH ] ),
				new Gutenberg\Blocks\Product_Components( $container[ Assets::PATH ], $container[ Rest::COMPONENT_SHORTCODE ] ),
			];
			if ( ( (bool) get_option( Cart_Settings::OPTION_ENABLE_CART, true ) ) === true ) {
				$blocks[] = new Gutenberg\Blocks\Cart( $container[ Assets::PATH ] );
			}
			if ( ( (bool) get_option( Cart_Settings::OPTION_EMBEDDED_CHECKOUT, true ) ) === true ) {
				$blocks[] = new Gutenberg\Blocks\Checkout( $container[ Assets::PATH ] );
			}
			if ( ( (bool) get_option( Gift_Certificate_Settings::OPTION_ENABLE, true ) ) === true ) {
				$blocks[] = new Gutenberg\Blocks\Gift_Certificate_Form( $container[ Assets::PATH ] );
				$blocks[] = new Gutenberg\Blocks\Gift_Certificate_Balance( $container[ Assets::PATH ] );
			}
			if ( get_option( 'users_can_register' ) ) {
				$blocks[] = new Gutenberg\Blocks\Registration_Form( $container[ Assets::PATH ] );
			}
			if ( get_option( Wishlist_Settings::ENABLED ) ) {
				$blocks[] = new Gutenberg\Blocks\Wishlist( $container[ Assets::PATH ] );
			}
			return $blocks;
		};

		add_action( 'init', $this->create_callback( 'register_gutenberg_blocks', function () use ( $container ) {
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}
			foreach ( $container[ self::GUTENBERG_BLOCKS ] as $block ) {
				/** @var Gutenberg\Blocks\Gutenberg_Block $block */
				$block->register();
			}
		} ), 10, 0 );

		add_filter( 'bigcommerce/gutenberg/js_config', $this->create_callback( 'gutenberg_js_config', function ( $data ) use ( $container ) {
			if ( ! function_exists( 'register_block_type' ) ) {
				$data[ 'blocks' ] = new \stdClass();

				return $data;
			}
			foreach ( $container[ self::GUTENBERG_BLOCKS ] as $block ) {
				/** @var Gutenberg\Blocks\Gutenberg_Block $block */
				$data[ 'blocks' ][ $block->name() ] = $block->js_config();
			}

			return $data;
		} ), 10, 1 );

		$container[ self::STYLES ] = function ( Container $container ) {
			$path = dirname( $container[ 'plugin_file' ] ) . '/assets/customizer.template.css';

			return new Styles( $path );
		};

		add_action( 'admin_head', $this->create_callback( 'customizer_styles', function () use ( $container ) {
			$container[ self::STYLES ]->print_styles();
		} ), 10, 0 );

		$container[ self::GUTENBERG_MIGRATE ] = function ( Container $container ) {

			return new Gutenberg\Migrate_Blocks();
		};

		add_filter( 'replace_editor', $this->create_callback( 'check_for_gutenberg', function ( $passthrough, $post ) use ( $container ) {
			return $container[ self::GUTENBERG_MIGRATE ]->check_if_gutenberg_editor( $passthrough, $post );
		} ), 9, 2 );

		add_filter( 'replace_editor', $this->create_callback( 'check_for_classic', function ( $passthrough, $post ) use ( $container ) {
			return $container[ self::GUTENBERG_MIGRATE ]->check_if_classic_editor( $passthrough, $post );
		} ), 11, 2 );
	}
}
