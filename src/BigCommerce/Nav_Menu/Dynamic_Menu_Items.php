<?php


namespace BigCommerce\Nav_Menu;

use BigCommerce\Customizer\Sections\Product_Category as Customizer;
use BigCommerce\Post_Types\Product\Product;

class Dynamic_Menu_Items {
	const TYPE = 'bigcommerce_dynamic';

	/**
	 * @param object $item
	 *
	 * @return object
	 */
	public function setup_menu_item( $item ) {
		if ( $item->type !== self::TYPE ) {
			return $item;
		}
		$item->type_label = __( 'BigCommerce', 'bigcommerce' );
		$item->url        = get_post_type_archive_link( Product::NAME );

		return $item;
	}

	/**
	 * Add the child terms under the top-level dynamic menu items
	 *
	 * @param array  $items An array of menu item post objects.
	 * @param object $menu  The menu object.
	 * @param array  $args  An array of arguments used to retrieve menu item objects.
	 *
	 * @return array
	 * @filter wp_get_nav_menu_items 20
	 *         Should run after \WP_Customize_Nav_Menu_Item_Setting::filter_wp_get_nav_menu_items()
	 */
	public function insert_dynamic_menu_items( $items, $menu, $args ) {
		if ( is_admin() ) {
			return $items; // don't want to insert items while in the admin
		}

		$output = [];
		foreach ( $items as $item ) {
			$output[] = $item;
			if ( $item->type !== self::TYPE ) {
				continue;
			}
			$children = $this->get_menu_item_children( $item );
			$output   = array_merge( $output, $children );
		}

		// Re-set the menu_order properties to keep everything in order later
		$i = 1;
		foreach ( $output as $k => $item ) {
			$output[ $k ]->{$args[ 'output_key' ]} = $i ++;
		}

		return $output;
	}

	/**
	 * Get the top-level terms from the taxonomy as menu items
	 *
	 * @param object $item
	 *
	 * @return array
	 */
	private function get_menu_item_children( $item ) {
		$taxonomy = isset( $item->object ) ? get_taxonomy( $item->object ) : false;
		if ( ! $taxonomy ) {
			return [];
		}

		$terms                       = $this->get_terms_items( $taxonomy );
		$should_retrieve_child_items = get_option( Customizer::CHILD_ITEM_SHOW, 'no' ) === 'yes';
		$items                       = [];

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_taxonomy = $term->taxonomy;
				$term_id       = $term->term_id;

				$term                   = wp_setup_nav_menu_item( $term );
				$term->menu_item_parent = $item->ID;
				$term->post_status      = 'publish';

				$items[] = $term;

				if ( ! $should_retrieve_child_items ) {
					continue;
				}

				// Get term children to level 1.
				$term_children = $this->get_terms_items( $term_taxonomy, $term_id );

				if ( empty( $term_children ) ) {
					continue;
				}

				foreach ( $term_children as $child ) {
					$term_child        = wp_setup_nav_menu_item( $child );
					$term_child->title = ' - ' . $term_child->title;
					$term_child->menu_item_parent = $item->ID;
					$term_child->post_status      = 'publish';

					$items[] = $term_child;
				}
			}
		}

		return $items;
	}

	/**
	 * @param     $taxonomy
	 * @param int $parent
	 *
	 * @return array|int[]|string|string[]|\WP_Error|\WP_Term[]
	 */
	private function get_terms_items( $taxonomy, int $parent = 0 ) {
		$terms = get_terms( [
				'taxonomy'     => is_object( $taxonomy ) ? $taxonomy->name : $taxonomy,
				'hide_empty'   => true,
				'hierarchical' => true,
				'parent'       => $parent,
				'meta_query'   => [
						[
								'key'  => 'sort_order',
								'type' => 'NUMERIC',
						],
				],
				'orderby'      => 'sort_order',
				'order'        => 'ASC',
		] );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return [];
		}

		return $terms;
	}
}
