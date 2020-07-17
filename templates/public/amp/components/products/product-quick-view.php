<?php
/**
 * Product Quick View Card.
 *
 * @package BigCommerce
 *
 * @var Product $product
 * @var string  $sku
 * @var string  $rating
 * @var string  $gallery
 * @var string  $title
 * @var string  $brand
 * @var string  $price
 * @var string  $description
 * @var string  $specs
 * @var string  $form      The form to purchase the product
 * @var string  $permalink A button linking to the product
 * @version 1.0.0
 */

use BigCommerce\Post_Types\Product\Product;

?>
<div class="amp-wp-article bc-product-card bc-product-card--single">

	<div class="bc-product__gallery">
		<?php echo $gallery; // WPCS: XSS ok. Already escaped data. ?>
	</div>

	<div class="bc-product__meta">
		<?php
		echo wp_kses( $title, 'bigcommerce/amp' );
		echo wp_kses( $brand, 'bigcommerce/amp' );
		echo wp_kses( $price, 'bigcommerce/amp' );
		echo wp_kses( $rating, 'bigcommerce/amp' );
		echo sprintf( esc_html( __( '<span class="bc-product-single__meta-label">SKU:</span> %s', 'bigcommerce' ) ), $sku );
		?>

	</div>

	<div class="bc-product__actions">
		<?php echo $form; // WPCS: XSS ok. Already escaped data. ?>
	</div>

	<?php echo wp_kses( $description, 'bigcommerce/amp' ); ?>
</div>
