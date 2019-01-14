<?php


namespace BigCommerce\Import\Mappers;


use BigCommerce\Taxonomies\Brand\Brand;

class Brand_Mapper extends Term_Mapper {
	protected $taxonomy = Brand::NAME;
}