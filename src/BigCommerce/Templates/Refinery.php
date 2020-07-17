<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer;
use BigCommerce\Post_Types\Product\Product;

class Refinery extends Controller {
	const QUERY = 'query';

	const SEARCH  = 'search';
	const SORT    = 'sort';
	const FILTERS = 'filters';
	const ACTION  = 'action';

	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-product-archive__refinery' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-product-archive-refinery' ];
	protected $template = 'components/catalog/refinery.php';

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	protected function parse_options( array $options ) {
		$defaults = [
			self::QUERY => null, // \WP_Query
		];

		return wp_parse_args( $options, $defaults );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		/** @var \WP_Query $query */
		$query = $this->options[ self::QUERY ];

		return [
			self::SEARCH  => $this->get_search( $query ),
			self::SORT    => $this->get_sort( $query ),
			self::FILTERS => $this->get_filters( $query ),
			self::ACTION  => $this->get_action( $query ),
		];
	}


	/**
	 * @param \WP_Query $query
	 *
	 * @return string
	 */
	private function get_search( \WP_Query $query ) {
		$search    = $query->is_search() && isset( $query->query['s'] ) ? stripslashes( $query->query['s'] ) : '';
		$component = Search_Box::factory( [
			Search_Box::NAME           => 's',
			Search_Box::VALUE          => $search,
			Search_Box::BUTTON_CLASSES => [ 'bc-btn', 'bc-btn--small', 'bc-icon', 'icon-bc-search' ],
		] );

		return $component->render();
	}

	/**
	 * @param \WP_Query $query
	 *
	 * @return string
	 */
	private function get_sort( \WP_Query $query ) {
		$choices         = Customizer\Sections\Product_Archive::sort_choices();
		$enabled_choices = get_option( Customizer\Sections\Product_Archive::SORT_OPTIONS, array_keys( $choices ) );
		if ( ! is_array( $enabled_choices ) ) {
			$enabled_choices = explode( ',', $enabled_choices );
		}
		$choices = array_filter( $choices, function ( $key ) use ( $enabled_choices ) {
			return in_array( $key, $enabled_choices );
		}, ARRAY_FILTER_USE_KEY );
		if ( empty( $choices ) ) {
			return '';
		}

		/**
		 * This filter is documents in src/BigCommerce/Post_Types/Product/Query.php
		 */
		$default_sort = apply_filters( 'bigcommerce/query/default_sort', Customizer\Sections\Product_Archive::SORT_FEATURED );
		$bc_sort      = filter_input( INPUT_GET, 'bc-sort', FILTER_SANITIZE_STRING );
		if ( $bc_sort && array_key_exists( $bc_sort, $choices ) ) {
			$sort = $bc_sort;
		} else {
			$sort = $default_sort;
		}

		$component = Refinement_Box::factory( [
			Refinement_Box::LABEL   => __( 'Sort by', 'bigcommerce' ),
			Refinement_Box::NAME    => 'bc-sort',
			Refinement_Box::VALUE   => $sort,
			Refinement_Box::CHOICES => $choices,
			Refinement_Box::ACTION  => remove_query_arg( 'bc-sort' ),
		] );

		return $component->render();
	}

	/**
	 * @param \WP_Query $query
	 *
	 * @return array
	 */
	private function get_filters( \WP_Query $query ) {
		$filters = [];

		$taxonomies         = Customizer\Sections\Product_Archive::filter_choices();
		$enabled_taxonomies = get_option( Customizer\Sections\Product_Archive::FILTER_OPTIONS, array_keys( $taxonomies ) );
		if ( ! is_array( $enabled_taxonomies ) ) {
			$enabled_taxonomies = explode( ',', $enabled_taxonomies );
		}
		$taxonomies = array_filter( $taxonomies, function ( $key ) use ( $enabled_taxonomies ) {
			return in_array( $key, $enabled_taxonomies );
		}, ARRAY_FILTER_USE_KEY );
		if ( empty( $taxonomies ) ) {
			return [];
		}

		foreach ( $taxonomies as $taxonomy => $label ) {
			if ( $query->is_tax( $taxonomy ) && ! $query->is_post_type_archive( Product::NAME ) ) {
				continue;
			}
			$tax_object = get_taxonomy( $taxonomy );
			$terms = get_terms( [
				'taxonomy'   => $taxonomy,
				'hide_empty' => true
			] );

			$terms_by_parent = [];
			$choices = [];

			foreach ( $terms as $term ) {
				$terms_by_parent[ $term->parent ][] = $term;
			}
			$choices = $this->get_choices( 0, $terms_by_parent, 0 );

			if ( empty( $choices ) ) {
				continue;
			}
			array_unshift( $choices, $tax_object->labels->all_items );
			$component = Refinement_Box::factory( [
				Refinement_Box::LABEL   => sprintf( __( 'Shop by %s', 'bigcommerce' ), $label ),
				Refinement_Box::NAME    => $taxonomy,
				Refinement_Box::VALUE   => empty( $_GET[ $taxonomy ] ) ? '' : sanitize_text_field( $_GET[ $taxonomy ] ),
				Refinement_Box::CHOICES => $choices,
				Refinement_Box::ACTION  => remove_query_arg( $taxonomy ),
				Refinement_Box::TYPE    => 'filter',
			] );
			$filters[] = $component->render();
		}

		return $filters;
	}

    /**
     * Sorts the categories array nesting child under parent with a preceding '-' for each level
     *
     * @param $parent_id
     * @param $terms_by_parent
     * @param $depth
     *
     * @return array
     */
	private function get_choices( $parent_id, $terms_by_parent, $depth ) {
		if ( empty( $terms_by_parent[ $parent_id ] ) ) {
			return [];
		}
		$prefix = implode( ' ', array_fill( 0, $depth, '- ' ) );
		foreach ( $terms_by_parent[ $parent_id ] as $term ) {
			$choices[ $term->slug ] = $prefix . $term->name;
			$choices = array_merge( $choices, $this->get_choices( $term->term_id, $terms_by_parent, $depth + 1 ));
		}
		return $choices;
	}


	/**
	 * @param \WP_Query $query
	 *
	 * @return string|void|\WP_Error
	 */
	private function get_action( \WP_Query $query ) {
		if ( $query->is_post_type_archive( Product::NAME ) ) {
			return get_post_type_archive_link( Product::NAME ) ?: home_url( '/' );
		}

		if ( $query->is_tax() ) {
			$term = get_queried_object();

			return get_term_link( $term );
		}

		return '';
	}
}
