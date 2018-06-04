<?php


namespace BigCommerce\Container;


use BigCommerce\Meta_Boxes\Import_Settings;
use BigCommerce\Post_Types\Product\Product;
use Pimple\Container;

class Post_Meta extends Provider {
	const IMPORT = 'post_meta.import_settings';

	public function register( Container $container ) {
		$this->import( $container );

	}

	private function import( Container $container ) {
		$container[ self::IMPORT ] = function ( Container $container ) {
			return new Import_Settings();
		};
		add_action( 'add_meta_boxes', $this->create_callback( 'add_meta_boxes', function ( $post_type, $post ) use ( $container ) {
			$container[ self::IMPORT ]->register( $post_type, $post );
		} ), 10, 2 );

		add_action( 'save_post', $this->create_callback( 'save_post', function ( $post_id, $post ) use ( $container ) {
			$container[ self::IMPORT ]->save_post( $post_id, $post );
		} ), 10, 2 );

		$inline_save = $this->create_callback( 'inline_save', function () use ( $container ) {
			add_action( 'post_updated', $this->create_callback( 'save_post_inline', function ( $post_id, $post ) use ( $container ) {
				$container[ self::IMPORT ]->save_post_inline( $post_id, $post );
			} ), 10, 2 );
		} );
		add_action( 'wp_ajax_inline-save', $inline_save, 0, 0 );
		if ( isset( $_REQUEST[ 'bulk_edit' ] ) ) {
			add_action( 'load-edit.php', $inline_save, 0, 0 );
		}

		add_filter( 'manage_' . Product::NAME . '_posts_columns', $this->create_callback( 'add_detached_list_table_column', function ( $columns ) use ( $container ) {
			return $container[ self::IMPORT ]->add_list_table_column( $columns );
		} ), 10, 1 );
		add_action( 'manage_' . Product::NAME . '_posts_custom_column', $this->create_callback( 'render_detached_list_table_column', function ( $column, $post_id ) use ( $container ) {
			return $container[ self::IMPORT ]->render_list_table_column( $column, $post_id );
		} ), 10, 2 );

		add_filter( 'bigcommerce/import/product/post_array', $this->create_callback( 'import_disable_overwrite', function ( $post_array ) use ( $container ) {
			return $container[ self::IMPORT ]->filter_imported_post( $post_array );
		} ), 10, 1 );
	}
}