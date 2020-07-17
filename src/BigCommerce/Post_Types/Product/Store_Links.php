<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Api_Factory;

class Store_Links {
	/**
	 * @var Api_Factory
	 */
	private $api_factory;

	public function __construct( Api_Factory $api_factory ) {
		$this->api_factory = $api_factory;
	}

	/**
	 * @param array    $actions
	 * @param \WP_Post $post
	 *
	 * @return array
	 * @filter post_row_actions
	 */
	public function add_row_action( $actions, $post ) {
		if ( get_post_type( $post ) !== Product::NAME ) {
			return $actions;
		}
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}
		$bc_url = $this->get_bigcommerce_post_url( $post );
		if ( $bc_url ) {
			$actions[ 'open-in-bigcommerce' ] = sprintf( '<a href="%s" class="%s" target="_blank">%s</a>', esc_url( $bc_url ), 'open-in-bigcommerce', __( 'Open in BigCommerce', 'bigcommerce' ) );
		}

		return $actions;
	}

	/**
	 * Add a link to the "Publish" meta box to
	 * open the product in BigCommerce
	 *
	 * @param \WP_Post $post
	 *
	 * @return void
	 * @action post_submitbox_misc_actions
	 */
	public function add_submitbox_link( $post ) {
		if ( get_post_type( $post ) !== Product::NAME ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}
		$bc_url = $this->get_bigcommerce_post_url( $post );
		if ( ! $bc_url ) {
			return;
		}
		echo '<div class="misc-pub-section misc-pub-bigcommerce">';
		printf( '<span class="dashicons dashicons-bigcommerce"></span> <a href="%s" target="_blank">%s</a>', esc_url( $bc_url ), esc_html( __( 'Open in BigCommerce', 'bigcommerce' ) ) );
		echo '</div>';
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 * @filter bigcommerce/gutenberg/js_config
	 */
	public function add_link_to_gutenberg_config( $data ) {
		$data[ 'store_link' ] = [
			'url'   => '',
			'label' => __( 'Open in BigCommerce', 'bigcommerce' ),
		];
		$screen               = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return $data;
		}
		if ( $screen->id !== Product::NAME ) {
			return $data;
		}
		$post = get_post();
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $data;
		}
		$data[ 'store_link' ][ 'url' ] = $this->get_bigcommerce_post_url( $post );

		return $data;
	}

	/**
	 * @param \WP_Admin_Bar $wp_admin_bar
	 *
	 * @action admin_bar_menu
	 */
	public function modify_edit_product_links_admin_bar( $wp_admin_bar ) {
		if ( ! ( is_singular( Product::NAME ) && current_user_can( 'edit_post', get_queried_object_id() ) ) ) {
			return;
		}

		$wp_admin_bar->add_menu( [
			'id' => 'edit',
			'href' => false,
		] );

		$wp_admin_bar->add_menu( [
			'id'     => 'edit-wp',
			'title'  => __( 'in WordPress', 'bigcommerce' ),
			'parent' => 'edit',
			'href'   => get_edit_post_link( get_queried_object_id() ),
		] );

		$wp_admin_bar->add_menu( [
			'id'     => 'edit-bc',
			'title'  => __( 'in BigCommerce', 'bigcommerce' ),
			'parent' => 'edit',
			'href' => $this->get_bigcommerce_post_url( get_queried_object() ),
		] );
	}

	/**
	 * Get the URL to edit a post in the BigCommerce admin
	 *
	 * @param \WP_Post $post
	 *
	 * @return string
	 */
	private function get_bigcommerce_post_url( $post ) {
		$product = new Product( $post->ID );
		$bc_id   = $product->bc_id();
		if ( empty( $bc_id ) ) {
			return '';
		}
		$store_url = $this->get_store_url();
		if ( empty( $store_url ) ) {
			return '';
		}
		$url = trailingslashit( $store_url ) . sprintf( 'manage/products/%d/edit', $bc_id );

		return $url;
	}

	/**
	 * Get the base URL for the BigCommerce store admin
	 *
	 * @return string
	 */
	private function get_store_url() {
		$url = get_transient( 'bigcommerce_store_url' );
		if ( ! empty( $url ) ) {
			return $url;
		}
		try {
			$api   = $this->api_factory->store();
			$store = $api->getStore();
			if ( empty( $store->secure_url ) ) {
				return '';
			}
			set_transient( 'bigcommerce_store_url', $store->secure_url, DAY_IN_SECONDS );

			return $store->secure_url;
		} catch ( \Exception $e ) {
			return '';
		}
	}
}