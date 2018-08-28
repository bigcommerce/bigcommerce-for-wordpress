<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Query {

	/**
	 * @param \WP_Query $query
	 *
	 * @return void
	 * @action pre_get_posts
	 */
	public function filter_queries( \WP_Query $query ) {
		if ( ! $query->get( 'posts_per_page' ) && $this->is_product_query( $query ) ) {
			$per_page = get_option( Product_Archive::PER_PAGE, Product_Archive::PER_PAGE_DEFAULT );
			if ( $per_page ) {
				$query->set( 'posts_per_page', $per_page );
			}
		}

		if ( $query->get( 'bc-sort' ) && $this->is_product_query( $query ) ) {
			switch ( $query->get( 'bc-sort' ) ) {
				case Product_Archive::SORT_TITLE_ASC:
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'ASC' );
					break;
				case Product_Archive::SORT_TITLE_DESC:
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'DESC' );
					break;
				case Product_Archive::SORT_DATE:
					$query->set( 'orderby', [ 'date' => 'DESC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_FEATURED:
					$select_filter = function( $select, $wp_query ) use ( $query ) {
						if ( $wp_query !== $query ) {
							return $select;
						}
						$select .= ", COUNT(bcfeatured_terms.name) AS bcfeatured";
						return $select;
					};
					$join_filter    = function ( $join, $wp_query ) use ( $query ) {
						/** @var \wpdb $wpdb */
						global $wpdb;
						if ( $wp_query !== $query ) {
							return $join;
						}
						$join .= " LEFT JOIN {$wpdb->term_relationships} bcfeatured_tr ON bcfeatured_tr.object_id={$wpdb->posts}.ID";
						$join .= $wpdb->prepare( " LEFT JOIN {$wpdb->term_taxonomy} bcfeatured_tt ON bcfeatured_tr.term_taxonomy_id=bcfeatured_tt.term_taxonomy_id AND bcfeatured_tt.taxonomy=%s", Flag::NAME );
						$join .= $wpdb->prepare( " LEFT JOIN {$wpdb->terms} bcfeatured_terms ON bcfeatured_tt.term_id=bcfeatured_terms.term_id AND bcfeatured_terms.slug=%s", Flag::FEATURED );

						return $join;
					};
					$orderby_filter = function ( $orderby, $wp_query ) use ( $query ) {
						if ( $wp_query !== $query ) {
							return $orderby;
						}
						$feature_sort = " bcfeatured DESC ";
						if ( trim( $orderby ) == '' ) {
							return $feature_sort;
						} else {
							return $feature_sort . ', ' . $orderby;
						}
					};
					$groupby_filter = function( $groupby, $wp_query ) use ( $query ) {
						/** @var \wpdb $wpdb */
						global $wpdb;
						if ( $wp_query !== $query ) {
							return $groupby;
						}
						if ( empty( $groupby ) ) {
							$groupby = "{$wpdb->posts}.ID";
						}
						return $groupby;
					};

					add_filter( 'posts_fields', $select_filter, 10, 2 );
					add_filter( 'posts_join', $join_filter, 10, 2 );
					add_filter( 'posts_orderby', $orderby_filter, 10, 2 );
					add_filter( 'posts_groupby', $groupby_filter, 10, 2 );
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'ASC' );
					break;
				case Product_Archive::SORT_PRICE_ASC:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query[ 'bigcommerce_price' ] = [
						'key'     => Product::PRICE_META_KEY,
						'compare' => 'EXISTS',
						'type'    => 'DECIMAL',
					];
					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_price' => 'ASC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_PRICE_DESC:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query[ 'bigcommerce_price' ] = [
						'key'     => Product::PRICE_META_KEY,
						'compare' => 'EXISTS',
						'type'    => 'DECIMAL',
					];
					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_price' => 'DESC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_REVIEWS:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query[ 'bigcommerce_rating' ] = [
						'key'     => Product::RATING_META_KEY,
						'compare' => 'EXISTS',
					];
					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_rating' => 'DESC', 'title' => 'ASC' ] );
					break;
				case Product_Archive::SORT_SALES:
					$meta_query = $query->get( 'meta_query' ) ?: [];

					$meta_query[ 'bigcommerce_sales' ] = [
						'key'     => Product::SALES_META_KEY,
						'compare' => 'EXISTS',
					];
					$query->set( 'meta_query', $meta_query );
					$query->set( 'orderby', [ 'bigcommerce_sales' => 'DESC', 'title' => 'ASC' ] );
					break;
				default:
					do_action( 'bigcommerce/query/sort', $query );
					break;
			}
		}

		$bcid_in     = $this->get_query_var_as_array( $query, 'bigcommerce_id__in' );
		$bcid_not_in = $this->get_query_var_as_array( $query, 'bigcommerce_id__not_in' );
		$sku_in      = $this->get_query_var_as_array( $query, 'bigcommerce_sku__in' );
		$sku_not_in  = $this->get_query_var_as_array( $query, 'bigcommerce_sku__not_in' );

		$in = [];
		if ( ! empty( $bcid_in ) ) {
			$post_ids = $this->bcids_to_post_ids( $bcid_in ) ?: [ 0 ];
			$in       = $post_ids;
		}
		if ( ! empty( $sku_in ) ) {
			$post_ids = $this->skus_to_post_ids( $sku_in ) ?: [ 0 ];
			$in       = $in ? array_intersect( $in, $post_ids ) : $post_ids; // intersect with bcids if both present
			$in       = $in ?: [ 0 ];
		}
		if ( $this->is_product_search( $query ) ) {
			$search_in = $this->search_to_post_ids( $query->get( 's' ) );
			$query->set( 's', null );
			$in = $in ? array_intersect( $in, $search_in ) : $search_in;
			$in = $in ?: [ 0 ];
		}

		if ( $in ) {
			$post__in = $query->get( 'post__in', [] );
			$post__in = $post__in ? array_intersect( $post__in, $in ) : $in;
			$query->set( 'post__in', $post__in ?: [ 0 ] );

			return; // don't set not_in if we're setting in, as WP will ignore it
		}

		$out = [];
		if ( ! empty( $bcid_not_in ) ) {
			$post_ids = $this->bcids_to_post_ids( $bcid_not_in );
			$out      = array_merge( $out, $post_ids );
		}
		if ( ! empty( $sku_not_in ) ) {
			$post_ids = $this->skus_to_post_ids( $sku_not_in );
			$out      = array_merge( $out, $post_ids );
		}

		if ( $out ) {
			$post__not_in = $query->get( 'post__not_in', [] );
			$post__not_in = array_merge( $post__not_in, $out );
			$query->set( 'post__not_in', $post__not_in );
		}
	}

	/**
	 * Remove empty query vars from request vars
	 *
	 * The main purpose here is to avoid WP thinking
	 * we're on a search page because the product
	 * archive filters left a "s=" in the URL.
	 *
	 * @param array $vars
	 *
	 * @return array
	 * @filter request
	 */
	public function filter_empty_query_vars( $vars ) {
		foreach ( [ 's', 'bc-sort', Brand::NAME, Product_Category::NAME ] as $query_var ) {
			if ( isset( $vars[ $query_var ] ) && ( empty( $vars[ $query_var ] ) || $query_var === '0' ) ) {
				unset( $vars[ $query_var ] );
			}
		}
		return $vars;
	}

	/**
	 * @param array $vars
	 *
	 * @return array
	 * @filter query_vars
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'bc-sort';

		return $vars;
	}

	private function get_query_var_as_array( \WP_Query $query, $var ) {
		$value = $query->get( $var, [] );
		if ( empty( $value ) ) {
			return [];
		}
		if ( ! is_array( $value ) ) {
			$value = explode( ',', $value );
		}

		return array_filter( $value );
	}

	/**
	 * Convert an array of BigCommerce IDs to post IDs
	 *
	 * @param int[] $bcids
	 *
	 * @return int[]
	 */
	private function bcids_to_post_ids( $bcids ) {
		if ( empty( $bcids ) ) {
			return [];
		}

		$id_list = implode( ',', array_map( 'absint', $bcids ) );
		/** @var \wpdb $wpdb */
		global $wpdb;
		$results = $wpdb->get_col( "SELECT post_id FROM {$wpdb->bc_products} WHERE bc_id IN ( $id_list )" );

		return array_map( 'intval', $results );
	}

	/**
	 * Convert an array of BigCommerce SKUs to post IDs
	 *
	 * @param string[] $skus
	 *
	 * @return int[]
	 */
	private function skus_to_post_ids( $skus ) {
		if ( empty( $skus ) ) {
			return [];
		}
		/** @var \wpdb $wpdb */
		global $wpdb;
		$sku_list = implode( ',', array_map( function ( $sku ) {
			return sprintf( "'%s'", esc_sql( $sku ) );
		}, $skus ) );

		$results = $wpdb->get_col( "SELECT post_id FROM {$wpdb->bc_products} WHERE sku IN ( $sku_list )" );

		return array_map( 'intval', $results );
	}

	/**
	 * @param \WP_Query $query
	 *
	 * @return bool
	 */
	private function is_product_search( \WP_Query $query ) {
		$search_phrase = $query->get( 's' );
		if ( empty( $search_phrase ) ) {
			return false;
		}
		$search_terms = explode( ' ', $search_phrase );
		if ( count( $search_terms ) !== 1 ) {
			return false;
		}

		return $this->is_product_query( $query );
	}

	private function is_product_query( \WP_Query $query ) {
		$post_type = $query->get( 'post_type' );
		if ( ! empty( $post_type ) ) {
			if ( is_array( $post_type ) ) {
				if ( count( $post_type ) > 1 ) {
					return false;
				}
				$post_type = reset( $post_type );
			}

			return $post_type == Product::NAME;
		}

		if ( $query->is_tax( [ Brand::NAME, Product_Category::NAME ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Product search should match title, BigCommerce ID, or SKU
	 *
	 * @param string $search_phrase
	 *
	 * @return array
	 */
	private function search_to_post_ids( $search_phrase ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$escaped_like = '%' . $wpdb->esc_like( $search_phrase ) . '%';

		// title search
		$searches[] = $wpdb->prepare( "(posts.post_title LIKE %s)", $escaped_like );

		// ID search
		if ( is_numeric( $search_phrase ) ) {
			$searches[] = $wpdb->prepare( "(products.bc_id = %d)", $search_phrase );
		}

		// SKU search
		$searches[] = $wpdb->prepare( "(products.sku LIKE %s)", $escaped_like );

		$search = implode( ' OR ', $searches );

		$post_ids = $wpdb->get_col( "SELECT products.post_id FROM {$wpdb->bc_products} products INNER JOIN {$wpdb->posts} posts ON products.post_id=posts.ID WHERE ( $search )" );

		return apply_filters( 'bigcommerce/query/search_post_ids', array_map( 'intval', $post_ids ), $search_phrase );
	}
}