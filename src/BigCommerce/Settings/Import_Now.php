<?php


namespace BigCommerce\Settings;


use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Manager\Manager;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Import\Import_Type;

class Import_Now {
	const ACTION = 'bigcommerce_import_now';

	/** @var Settings_Screen */
	private $settings_screen;

	/**
	 * Import_Now constructor.
	 *
	 * @param Settings_Screen $settings_screen
	 */
	public function __construct( Settings_Screen $settings_screen ) {
		$this->settings_screen = $settings_screen;
	}

	/**
	 * @param string $label    The label for the button.
	 * @param string $redirect The redirect destination after starting the import. Defaults to the settings page.
	 *
	 * @return void
	 * @action bigcommerce/settings/render/import_status
	 */
	public function render_button( $label = '', $redirect = '' ) {
		if ( ! $this->current_user_can_start_import() ) {
			return;
		}
		$label  = $label ?: __( 'Sync Products', 'bigcommerce' );
		$button = sprintf( '<button class="button bc-admin-btn bc-admin-btn--outline">%s</button>', $label );

		/**
		 * Hidden form fields
		 */
		$hidden_fields = [
			[
				'name'  => 'redirect_to',
				'value' => $redirect,
			],
			[
				'name'  => 'action',
				'value' => self::ACTION,
			],
			[
				'name'  => '_wpnonce',
				'value' => wp_create_nonce( self::ACTION ),
			],
		];
		$hidden_fields = implode( '', array_map( function ( $field ) {
			return sprintf( '<input type="hidden" name="%s" value="%s">', $field['name'], $field['value'] );
		}, $hidden_fields ) );

		/**
		 * Import type dropdown
		 */
		$import_type_dropdown = [
			[
				'name'  => __( 'New/Updated since last sync', 'bigcommerce' ),
				'value' => Import_Type::IMPORT_TYPE_PARTIAL,
			],
			[
				'name'  => __( 'All Products', 'bigcommerce' ),
				'value' => Import_Type::IMPORT_TYPE_FULL,
			],
		];
		$import_type_dropdown = implode( '', array_map( function ( $option ) {
			return sprintf( '<option value="%s">%s</option>', $option['value'], $option['name'] );
		}, $import_type_dropdown ) );
		$import_type_dropdown = sprintf( '<select name="%s">%s</select>', Import_Type::IMPORT_TYPE, $import_type_dropdown );

		printf(
			'<form action="%s" class="bc-product-sync-form">%s %s %s</form>',
			admin_url( 'admin-post.php' ),
			$hidden_fields,
			$import_type_dropdown,
			$button
		);
	}


	/**
	 * Add the sync link to the views above the products
	 * list table. While not exactly a view, it's a reasonable place
	 * to inject the status into the UI.
	 *
	 * @param array $views
	 *
	 * @return array
	 * @filter views_edit-bigcommerce_product 5
	 */
	public function list_table_link( $views = [] ) {
		ob_start();
		$this->render_button(
			sprintf( '<i class="bc-icon icon-bc-sync"></i> %s', __( 'Sync Products', 'bigcommerce' ) ),
			add_query_arg( [ 'post_type' => Product::NAME ], admin_url( 'edit.php' ) )
		);
		$sync_button = ob_get_clean();
		if ( $sync_button ) {
			$views[ 'bc-sync-products' ] = $sync_button;
		}
		return $views;
	}

	/**
	 * @param string $redirect
	 *
	 * @return string
	 */
	public function get_import_url( $redirect = '' ) {
		$url = admin_url( 'admin-post.php' );
		$url = add_query_arg( [
			'action' => self::ACTION,
		], $url );
		if ( ! empty( $redirect ) ) {
			$url = add_query_arg( [ 'redirect_to' => urlencode( $redirect ) ], $url );
		}
		$url = wp_nonce_url( $url, self::ACTION );

		return $url;
	}

	/**
	 * @return void
	 * @action admin_post_ . self::ACTION
	 */
	public function handle_request() {
		check_admin_referer( self::ACTION );

		if ( $this->current_user_can_start_import() ) {
			$import_type = filter_input( INPUT_GET, Import_Type::IMPORT_TYPE, FILTER_SANITIZE_STRING );

			update_option( Import_Type::IMPORT_TYPE, $import_type );
			wp_unschedule_hook( Manager::CRON_PROCESSOR );
			do_action( Cron_Runner::START_CRON );
		}

		if ( ! empty( $_REQUEST[ 'redirect_to' ] ) ) {
			wp_safe_redirect( esc_url_raw( $_REQUEST[ 'redirect_to' ] ), 303 );
		} elseif ( current_user_can( $this->settings_screen->get_capability() ) ) {
			wp_safe_redirect( esc_url_raw( $this->settings_screen->get_url() ), 303 );
		} else {
			$edit_products_url = add_query_arg( [ 'post_type' => Product::NAME ], admin_url( 'edit.php' ) );
			wp_safe_redirect( esc_url_raw( $edit_products_url ), 303 );
		}
		exit();
	}

	private function current_user_can_start_import() {
		$post_type = get_post_type_object( Product::NAME );
		if ( $post_type && current_user_can( $post_type->cap->edit_posts ) ) {
			return true;
		}

		return false;
	}
	/**
	 * Print the import button into the notices section
	 * of the products admin list table.
	 *
	 * @return void
	 * @action admin_notices
	 */
	public function list_table_notice() {
		if ( ! $this->on_products_list_table() ) {
			return;
		}
		/**
		 * Displays the current import status notice in the product list table.
		 */
		do_action( 'bigcommerce/settings/import/product_list_table_notice' );
	}
	/**
	 * @return bool Whether the current screen is the products list table
	 */
	private function on_products_list_table() {
		if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
			return false;
		}
		$screen = get_current_screen();
		if ( ! $screen || $screen->base !== 'edit' || $screen->post_type !== Product::NAME ) {
			return false;
		}
		return true;
	}
}
