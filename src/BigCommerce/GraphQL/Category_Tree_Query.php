<?php

namespace BigCommerce\GraphQL;

/**
 * @class Category_Tree_Query
 *
 * Handle category tree query data and fragments
 */
class Category_Tree_Query {

	public function get_category_tree_query() {
		return 'query CategoryTree3LevelsDeep {
			site {
			  categoryTree {
				...CategoryFields
				children {
				  ...CategoryFields
				  children {
					...CategoryFields
				  }
				}
			  }
			}
		  }
		 
		 fragment CategoryFields on CategoryTreeItem {
			 name
			 path
			 entityId
			 description
			 path
			 image {
				 altText
				 isDefault
				 urlOriginal
				 url(width:320)
			 }
		 }
		 ';
	}
}
