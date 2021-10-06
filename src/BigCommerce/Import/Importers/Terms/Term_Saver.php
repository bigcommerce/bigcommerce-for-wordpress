<?php


namespace BigCommerce\Import\Importers\Terms;

use BigCommerce\Import\Import_Strategy;
use BigCommerce\Import\Image_Importer;
use BigCommerce\Import\Processors\Category_Import;

abstract class Term_Saver implements Import_Strategy {

	const DATA_HASH_META_KEY        = 'bigcommerce_data_hash';
	const IMPORTER_VERSION_META_KEY = 'bigcommerce_importer_version';

	/** @var \ArrayAccess */
	protected $bc_term;

	/** @var string */
	protected $taxonomy;

	/** @var int */
	protected $term_id;

	public function __construct( \ArrayAccess $bc_term, $taxonomy, $term_id = 0 ) {
		$this->bc_term  = $bc_term;
		$this->taxonomy = $taxonomy;
		$this->term_id  = $term_id;
	}

	/**
	 * Import the Term into WordPress
	 *
	 * @return int The imported term ID
	 */
	public function do_import() {
		$this->term_id = $this->save_wp_term( $this->bc_term );
		$this->save_wp_termmeta( $this->bc_term );
		$this->import_image( $this->bc_term );

		update_term_meta( $this->term_id, self::DATA_HASH_META_KEY, self::hash( $this->bc_term ) );
		update_term_meta( $this->term_id, self::IMPORTER_VERSION_META_KEY, Import_Strategy::VERSION );

		return $this->term_id;
	}

	abstract protected function save_wp_term( \ArrayAccess $bc_term );

	abstract protected function save_wp_termmeta( \ArrayAccess $bc_term );

	/**
	 * Get the name of the term
	 *
	 * @param \ArrayAccess $bc_term
	 *
	 * @return string
	 */
	protected function term_name( \ArrayAccess $bc_term ) {
		return $this->sanitize_string( $bc_term['name'] );
	}

	/**
	 * Get the slug for the term. This is derived from the
	 * `custom_url` property of the term if present.
	 *
	 * @param \ArrayAccess $bc_term
	 *
	 * @return string
	 */
	protected function term_slug( \ArrayAccess $bc_term ) {
		$custom_url = $bc_term['custom_url'] ?: [ 'url' => '', 'is_customized' => false ];
		if ( empty( $custom_url ) || empty( $custom_url['url'] ) ) {
			$slug = sanitize_title( $this->term_name( $bc_term ) );
		} else {
			$slug = sanitize_title( pathinfo( $this->sanitize_string( $custom_url['url'] ), PATHINFO_FILENAME ) );
		}

		if ( empty( $slug ) ) {
			$slug = sanitize_title( $this->term_name( $bc_term ) );
		}

		$duplicate = get_term_by( 'slug', $slug, $this->taxonomy );
		if ( $duplicate && (int) $duplicate->term_id !== (int) $this->term_id ) {
			$term = get_term( $this->term_id );
			$current_slug = '';
			if ( ! empty( $term->slug ) ) {
				$current_slug = $term->slug;
			}
			if ( $current_slug === $duplicate->slug ) {
				$slug = ''; // let WP auto-assign the slug, otherwise the creation will fail
			} else {
				$slug = $current_slug; // keep the current slug else WP will alternate the slug on each import
			}
		}

		return $slug;
	}

	protected function sanitize_int( $value ) {
		if ( is_scalar( $value ) ) {
			return (int) $value;
		}

		return 0;
	}

	protected function sanitize_string( $value ) {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}

	/**
	 * @param \ArrayAccess $bc_term
	 *
	 * @return array
	 */
	protected function get_term_args( \ArrayAccess $bc_term ) {
		// Wp uses wp_filter_kses to sanitize html from the term description
		// we need to make sure we're getting the same result here
		return [
			'slug'        => $this->term_slug( $bc_term ),
			'description' => wp_kses_post( $this->sanitize_string( $bc_term['description'] ) ),
			'parent'      => $this->determine_parent_term_id( $bc_term ),
		];
	}

	/**
	 * Find a previously imported term that should be set as the parent term
	 *
	 * @param \ArrayAccess $bc_term
	 *
	 * @return int
	 */
	protected function determine_parent_term_id( \ArrayAccess $bc_term ) {
		$bc_id = isset( $bc_term['parent_id'] ) ? $this->sanitize_int( $bc_term['parent_id'] ) : 0;
		if ( empty( $bc_id ) ) {
			return 0;
		}

		$terms = get_terms( [
			'taxonomy'   => $this->taxonomy,
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'     => 'bigcommerce_id',
					'value'   => $bc_id,
					'compare' => '=',
				],
			],
		] );

		if ( ! empty( $terms ) ) {
			return (int) reset( $terms )->term_id;
		} else {
			$parent_term = apply_filters( 'bigcommerce/import/term/data', false, $bc_id );

			if ( ! empty( $parent_term ) ) {
				$strategy = new Term_Creator( $parent_term, $this->taxonomy );
				$parent_term_id = $strategy->do_import();

				if ( ! empty( $parent_term_id ) ) {
					return $parent_term_id;
				}
			}
		}

		return 0;
	}

	protected function import_image( \ArrayAccess $bc_term ) {
		$image_url = $bc_term[ 'image_url' ];

		// find an existing image
		$existing = get_posts( [
			'post_type'      => 'attachment',
			'meta_query'     => [
				[
					'key'     => Image_Importer::SOURCE_URL,
					'value'   => $image_url,
					'compare' => '=',
				],
			],
			'fields'         => 'ids',
			'posts_per_page' => 1,
		] );

		if ( ! empty( $existing ) ) {
			$post_id = reset( $existing );
		} else {
			$importer = new Image_Importer( $image_url );
			$post_id  = $importer->import();
		}
		if ( ! empty( $post_id ) ) {
			update_term_meta( $this->term_id, 'thumbnail_id', $post_id );
		} else {
			delete_term_meta( $this->term_id, 'thumbnail_id' );
		}
	}

	public static function hash( $bc_term ) {
		return md5( $bc_term );
	}
}
