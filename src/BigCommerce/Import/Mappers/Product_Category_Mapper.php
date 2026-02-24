<?php


namespace BigCommerce\Import\Mappers;

use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Category;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * This class is responsible for mapping the BigCommerce product category data to a WordPress term.
 * It extends the Term_Mapper class and specifies the taxonomy used for the product category mapping.
 */
class Product_Category_Mapper extends Term_Mapper {

	/**
	 * @var string The taxonomy name used for mapping the product category data.
	 */
	protected $taxonomy = Product_Category::NAME;
}
