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

	protected $template = 'components/refinery.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::QUERY => null, // \WP_Query
		];

		return wp_parse_args( $options, $defaults );
	}

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


	private function get_search( \WP_Query $query ) {
		$search    = $query->is_search() && isset( $query->query[ 's' ] ) ? stripslashes( $query->query[ 's' ] ) : '';
		$component = new Search_Box( [
			Search_Box::NAME           => 's',
			Search_Box::VALUE          => $search,
			Search_Box::BUTTON_CLASSES => [ 'bc-btn', 'bc-btn--small', 'bc-icon', 'icon-bc-search' ],
		] );

		return $component->render();
	}

	private function get_sort( \WP_Query $query ) {
		/**
		 * Filter the default sort order for products
		 *
		 * @param string $sort The sorting method to use
		 */
		$default_sort    = apply_filters( 'bigcommerce/template/product/archive/default_sort', Customizer\Sections\Product_Archive::SORT_TITLE_ASC );
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

		$component = new Refinement_Box( [
			Refinement_Box::LABEL   => __( 'Sort by', 'bigcommerce' ),
			Refinement_Box::NAME    => 'bc-sort',
			Refinement_Box::VALUE   => ! empty( $_GET[ 'bc-sort' ] ) ? $_GET[ 'bc-sort' ] : $default_sort,
			Refinement_Box::CHOICES => $choices,
			Refinement_Box::ACTION  => remove_query_arg( 'bc-sort' ),
		] );

		return $component->render();
	}

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
			$terms      = get_terms( [
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			] );
			$choices    = [];
			foreach ( $terms as $term ) {
				$choices[ $term->slug ] = $term->name;
			}
			if ( empty( $choices ) ) {
				continue;
			}
			array_unshift( $choices, $tax_object->labels->all_items );
			$component = new Refinement_Box( [
				Refinement_Box::LABEL   => sprintf( __( 'Shop by %s', 'bigcommerce' ), $label ),
				Refinement_Box::NAME    => $taxonomy,
				Refinement_Box::VALUE   => empty( $_GET[ $taxonomy ] ) ? '' : $_GET[ $taxonomy ],
				Refinement_Box::CHOICES => $choices,
				Refinement_Box::ACTION  => remove_query_arg( $taxonomy ),
				Refinement_Box::TYPE    => 'filter',
			] );
			$filters[] = $component->render();
		}

		return $filters;
	}

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