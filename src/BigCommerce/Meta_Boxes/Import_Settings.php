<?php


namespace BigCommerce\Meta_Boxes;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Import;

class Import_Settings extends Meta_Box {
	const NAME = 'bigcommerce_import_settings';

	const DISABLE_OVERWRITE = 'bigcommerce_disable_overwrite';

	protected function get_name() {
		return self::NAME;
	}

	protected function get_title() {
		return __( 'Product Import Settings', 'bigcommerce' );
	}

	protected function get_post_types() {
		return [
			Product::NAME,
		];
	}

	protected function get_context() {
		return 'side';
	}

	public function render( $post ) {
		$value = get_post_meta( $post->ID, self::DISABLE_OVERWRITE, true );
		if ( $value === '' ) {
			$value = $this->get_default_setting();
		}
		$value = (int) $value;

		printf(
			'<label><input type="checkbox" name="%s" value="1" %s /> %s</label>',
			self::DISABLE_OVERWRITE, checked( 1, $value, false ),
			__( 'Do not update product on import', 'bigcommerce' )
		);
		printf( '<p class="description">%s</p>', __( 'Any changes you make will be retained on the next scheduled product import.', 'bigcommerce' ) );
		echo wp_nonce_field( self::NAME, self::NAME . '_nonce', false, false );
	}

	private function get_default_setting() {
		$option = get_option( Import::OPTION_DISABLE_OVERWRITE, 1 );

		return (int) $option;
	}

	public function save_post( $post_id, $post ) {
		if ( ! isset( $_POST[ 'ID' ] ) || $_POST[ 'ID' ] != $post_id ) {
			return;
		}
		$nonce     = isset( $_POST[ self::NAME . '_nonce' ] ) ? $_POST[ self::NAME . '_nonce' ] : '';
		$submitted = isset( $_POST[ self::DISABLE_OVERWRITE ] ) ? (int) $_POST[ self::DISABLE_OVERWRITE ] : 0;
		if ( ! wp_verify_nonce( $nonce, self::NAME ) ) {
			return;
		}
		update_post_meta( $post_id, self::DISABLE_OVERWRITE, $submitted );
	}

	/**
	 * If doing an inline edit and the metabox has never been saved,
	 * set the default value.
	 *
	 * @param int      $post_id
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	public function save_post_inline( $post_id, $post ) {
		$value = get_post_meta( $post_id, self::DISABLE_OVERWRITE, true );
		if ( $value === '' ) {
			update_post_meta( $post_id, self::DISABLE_OVERWRITE, $this->get_default_setting() );
		}
	}

	/**
	 * @param string[] $columns
	 *
	 * @return string[]
	 * @filter manage_ . Product::NAME . _posts_columns
	 */
	public function add_list_table_column( $columns ) {
		$columns[ self::NAME ] = __( 'Detached', 'bigcommerce' );

		return $columns;
	}

	/**
	 * @param string $column
	 * @param int    $post_id
	 *
	 * @return void
	 * @action manage_ . Product::NAME . _posts_custom_column
	 */
	public function render_list_table_column( $column, $post_id ) {
		if ( $column !== self::NAME ) {
			return;
		}
		$value = get_post_meta( $post_id, self::DISABLE_OVERWRITE, true );
		if ( $value === '1' ) {
			printf( '<span title="%s">%s</span>', __( 'Not updated on import', 'bigcommerce' ), __( 'Yes', 'bigcommerce' ) );
		}
	}

	/**
	 * If a post is set to disable overwrite,
	 * prevent the import from updating the post
	 *
	 * @param array $post_array
	 *
	 * @return array
	 *
	 * @filter bigcommerce/import/product/post_array
	 */
	public function filter_imported_post( $post_array ) {
		if ( empty( $post_array[ 'ID' ] ) ) {
			return $post_array;
		}
		$disabled = get_post_meta( $post_array[ 'ID' ], self::DISABLE_OVERWRITE, true );
		if ( $disabled === '1' ) {
			$post = get_post( $post_array[ 'ID' ] );

			$post_array[ 'post_content' ] = $post->post_content;
			$post_array[ 'post_status' ]  = $post->post_status;
			$post_array[ 'post_name' ]    = $post->post_name;
			$post_array[ 'post_date' ]    = $post->post_date;
		}

		return $post_array;
	}
}