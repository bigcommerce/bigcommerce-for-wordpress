<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Product_Archive extends Controller {
	const QUERY = 'query';

	const POSTS       = 'posts';
	const NO_RESULTS  = 'no_results';
	const TITLE       = 'title';
	const DESCRIPTION = 'description';
	const REFINERY    = 'refinery';
	const PAGINATION  = 'pagination';
	const COLUMNS     = 'columns';

	protected $template = 'components/catalog/product-archive.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::QUERY   => null, // \WP_Query
			self::COLUMNS => absint( get_option( Customizer\Sections\Product_Archive::GRID_COLUMNS, 4 ) ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var \WP_Query $query */
		$query = $this->options[ self::QUERY ];

		return [
			self::POSTS       => $this->get_posts( $query ),
			self::TITLE       => $this->get_title( $query ),
			self::DESCRIPTION => $this->get_description( $query ),
			self::REFINERY    => $this->get_refinery( $query ),
			self::PAGINATION  => $this->get_pagination( $query ),
			self::COLUMNS     => $this->options[ self::COLUMNS ],
			self::NO_RESULTS  => $this->get_no_results(),
		];
	}

	private function get_posts( \WP_Query $query ) {
		$cards = [];
		while ( $query->have_posts() ) {
			$query->the_post();
			$product = new Product( get_the_ID() );
			$card    = Product_Card::factory( [
				Product_Card::PRODUCT => $product,
			] );
			$cards[] = $card->render();
		}
		wp_reset_query();

		return $cards;
	}

	private function get_title( \WP_Query $query ) {
		// one of the taxonomies, but not both
		if ( $query->is_tax() && ! ( $query->get( Product_Category::NAME ) && $query->get( Brand::NAME ) ) ) {
			/** @var \WP_Term $term */
			$term     = $query->get_queried_object();
			$taxonomy = get_taxonomy( $term->taxonomy );

			return sprintf( _x( '%s: %s', 'term archive title', 'bigcommerce' ), $taxonomy->labels->singular_name, $term->name );
		}

		if ( $query->is_search() && ! empty( $query->query['s'] ) ) {
			return sprintf( __( 'All Products Matching "%s"', 'bigcommerce' ), stripslashes( $query->query['s'] ) );
		}

		$default = __( 'All Products', 'bigcommerce' );
		$title   = get_option( Customizer\Sections\Product_Archive::ARCHIVE_TITLE, $default );

		return $title;
	}

	private function get_description( \WP_Query $query ) {
		$description = get_the_archive_description();

		if ( empty( $description ) ) {
			$description = get_option( Customizer\Sections\Product_Archive::ARCHIVE_DESCRIPTION, '' );
		}

		return $description;
	}

	private function get_refinery( \WP_Query $query ) {
		$component = Refinery::factory( [
			Refinery::QUERY => $query,
		] );

		return $component->render();
	}

	private function get_pagination( \WP_Query $query ) {
		return get_the_posts_pagination( [
			'prev_text'          => __( 'Previous page', 'twentysixteen' ),
			'next_text'          => __( 'Next page', 'twentysixteen' ),
			'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'bigcommerce' ) . ' </span>',
		] );
	}

	private function get_no_results() {
		$component = No_Results::factory( [] );

		return $component->render();
	}

}