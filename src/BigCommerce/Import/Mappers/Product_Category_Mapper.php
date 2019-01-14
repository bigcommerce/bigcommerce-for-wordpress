<?php


namespace BigCommerce\Import\Mappers;

use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\Category;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Product_Category_Mapper extends Term_Mapper {
	protected $taxonomy = Product_Category::NAME;
}
