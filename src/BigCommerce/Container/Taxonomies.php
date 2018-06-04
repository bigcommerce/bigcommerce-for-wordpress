<?php


namespace BigCommerce\Container;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Availability;
use BigCommerce\Taxonomies\Brand;
use BigCommerce\Taxonomies\Condition;
use BigCommerce\Taxonomies\Flag;
use BigCommerce\Taxonomies\Product_Category;
use BigCommerce\Taxonomies\Product_Type;
use Pimple\Container;

class Taxonomies extends Provider {
	const PRODUCT_CATEGORY        = 'taxonomy.product_category';
	const PRODUCT_CATEGORY_CONFIG = 'taxonomy.product_category.config';

	const BRAND        = 'taxonomy.brand';
	const BRAND_CONFIG = 'taxonomy.brand.config';

	const AVAILABILITY        = 'taxonomy.availability';
	const AVAILABILITY_CONFIG = 'taxonomy.availability.config';

	const CONDITION        = 'taxonomy.condition';
	const CONDITION_CONFIG = 'taxonomy.condition.config';

	const PRODUCT_TYPE        = 'taxonomy.product_type';
	const PRODUCT_TYPE_CONFIG = 'taxonomy.product_type.config';

	const FLAG        = 'taxonomy.flag';
	const FLAG_CONFIG = 'taxonomy.flag.config';

	public function register( Container $container ) {
		$this->product_category( $container );
		$this->brand( $container );
		$this->availability( $container );
		$this->condition( $container );
		$this->product_type( $container );
		$this->flag( $container );

		add_action( 'init', $this->create_callback( 'register', function () use ( $container ) {
			$container[ self::PRODUCT_CATEGORY_CONFIG ]->register();
			$container[ self::BRAND_CONFIG ]->register();
			$container[ self::AVAILABILITY_CONFIG ]->register();
			$container[ self::CONDITION_CONFIG ]->register();
			$container[ self::PRODUCT_TYPE_CONFIG ]->register();
			$container[ self::FLAG_CONFIG ]->register();
		} ), 0, 0 );
	}

	private function product_category( Container $container ) {
		$container[ self::PRODUCT_CATEGORY_CONFIG ] = function ( Container $container ) {
			return new Product_Category\Config( Product_Category\Product_Category::NAME, [ Product::NAME ] );
		};
	}

	private function brand( Container $container ) {
		$container[ self::BRAND_CONFIG ] = function ( Container $container ) {
			return new Brand\Config( Brand\Brand::NAME, [ Product::NAME ] );
		};
	}

	private function availability( Container $container ) {
		$container[ self::AVAILABILITY_CONFIG ] = function ( Container $container ) {
			return new Availability\Config( Availability\Availability::NAME, [ Product::NAME ] );
		};
	}

	private function condition( Container $container ) {
		$container[ self::CONDITION_CONFIG ] = function ( Container $container ) {
			return new Condition\Config( Condition\Condition::NAME, [ Product::NAME ] );
		};
	}

	private function product_type( Container $container ) {
		$container[ self::PRODUCT_TYPE_CONFIG ] = function ( Container $container ) {
			return new Product_Type\Config( Product_Type\Product_Type::NAME, [ Product::NAME ] );
		};
	}

	private function flag( Container $container ) {
		$container[ self::FLAG_CONFIG ] = function ( Container $container ) {
			return new Flag\Config( Flag\Flag::NAME, [ Product::NAME ] );
		};
	}
}