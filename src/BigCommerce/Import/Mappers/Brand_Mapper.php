<?php


namespace BigCommerce\Import\Mappers;


use BigCommerce\Taxonomies\Brand\Brand;

/**
 * This class is responsible for mapping the BigCommerce brand data to a WordPress term.
 * It extends the Term_Mapper class and specifies the taxonomy used for the brand mapping.
 */
class Brand_Mapper extends Term_Mapper {

	/**
	 * @var string The taxonomy name used for mapping the brand data.
	 */
	protected $taxonomy = Brand::NAME;
}
